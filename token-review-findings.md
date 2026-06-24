# Token Review Findings

## Short Summary

- JWT segment encoding/signing is basically correct for HS256 JWTs: header and payload are Base64URL-encoded JSON, and the signature is `HMAC-SHA256(header.payload)` with a Base64URL-encoded binary digest.
- The main security issue is **subject binding**: `verify/{id}/{jwt}` only checks whether the JWT is valid and unexpired, but does **not** verify that the JWT `sub` matches the `{id}` route parameter. A valid token for resident `A` can currently be replayed against resident `B`.
- `IdCardService` still duplicates HMAC token generation even though token logic was extracted to `TokenService`. That is not a direct vuln, but it risks drift.
- `TokenService` and `IdCardService` read `ENCRYPTION_KEY`, while the repo’s `.env` documents CodeIgniter’s standard `encryption.key`. This can silently leave HMAC signing on an empty key in some environments.
- The unit test command requested in the prompt currently fails before executing tests because the repo root is missing `phpunit.xml.dist`.
- The closest runnable PHPUnit command exposed two more issues:
  - `IdCardServiceTest` eagerly triggers DB setup because `IdCardService` constructs `ResidentModel` in `__construct()`.
  - The test environment expects `sqlite3`, which is not enabled on this machine.
- The resident listing UI has partial filter logic only: `purok` is server-backed, but name/household filters are client-side only in `public/js/residents/residents-index.js`, so exports, deep links, and controller-side filtering do not match the UI.

## Files Reviewed

- `app/Services/IdCardService.php`
- `app/Services/TokenService.php`
- `tests/unit/TokenServiceTest.php`
- `tests/unit/IdCardServiceTest.php`

## Test Commands Run

### Exact command from the prompt

```powershell
php vendor\bin\phpunit -c phpunit.xml.dist tests/unit
```

Result:

```text
PHPUnit 10.5.45 by Sebastian Bergmann and contributors.

Could not read XML from file "phpunit.xml.dist"
```

Exit code: `1`

### Closest runnable fallback

```powershell
php vendor\bin\phpunit -c vendor\codeigniter4\framework\phpunit.xml.dist --no-coverage tests/unit
```

Result summary:

- `8` tests executed
- `10` assertions
- `3` errors

Errors:

1. `HealthTest::testBaseUrlHasBeenSet`
   - `Typed property Config\App::$baseURL must not be accessed before initialization`
2. `IdCardServiceTest::testGetQrUrlContainsResidentAndJwt`
   - `sqlite3` PHP extension missing
3. `IdCardServiceTest::testValidateTokenDelegatesToTokenService`
   - `sqlite3` PHP extension missing

## Why The JWT Flow Needs A Patch

Current controller flow:

- `IdGenerator::verify($id, $jwt)` fetches resident by route ID.
- It calls `IdCardService::validateJwtToken($jwt)`.
- `TokenService::validateJwtToken()` only validates signature and expiry.

What is missing:

- It does **not** assert `sub === $id`.

Impact:

- A valid JWT minted for resident `99` can be reused against `/verify/5/{jwt-for-99}`.
- If resident `5` exists and is active, the page can incorrectly show a successful verification.

## Patches Ready To Apply

- `patch-01-test-bootstrap-idcard.patch`
  - Adds a root `phpunit.xml.dist`
  - Makes `IdCardService` testable by lazy-loading `ResidentModel`
  - Delegates HMAC generation back to `TokenService`
  - Removes reflection-heavy setup from `IdCardServiceTest`

- `patch-02-token-subject-binding-hardening.patch`
  - Binds JWT validation to the expected resident ID
  - Adds a unit test for subject mismatch
  - Adds a controller-level test for `verify/{id}/{jwt}`
  - Hardens Base64URL decoding and future timestamp handling
  - Adds fallback for CodeIgniter’s `encryption.key`

## Exact Local Commands To Run

### Current state

```powershell
php vendor\bin\phpunit -c phpunit.xml.dist tests/unit
```

### After applying `patch-01-test-bootstrap-idcard.patch`

Without coverage:

```powershell
php vendor\bin\phpunit -c phpunit.xml.dist --no-coverage tests/unit
```

With coverage driver installed:

```powershell
php vendor\bin\phpunit -c phpunit.xml.dist tests/unit
```

If you want only the token-related tests:

```powershell
php vendor\bin\phpunit -c phpunit.xml.dist --no-coverage tests/unit/TokenServiceTest.php tests/unit/IdCardServiceTest.php tests/app/Controllers/IdGeneratorTest.php
```

## Verification Steps

1. Apply `patch-01-test-bootstrap-idcard.patch`.
2. Apply `patch-02-token-subject-binding-hardening.patch`.
3. Run:

```powershell
php vendor\bin\phpunit -c phpunit.xml.dist --no-coverage tests/unit tests/app/Controllers/IdGeneratorTest.php
```

4. Confirm these cases pass:
   - valid JWT for the same resident verifies successfully
   - tampered JWT fails
   - JWT minted for another resident fails
   - HMAC token still validates for the correct resident and freshness window

## Recommended Hardening

- Replace the hand-rolled JWT implementation with `firebase/php-jwt`.
- Add `iss`, `aud`, and `jti`.
- If revocation matters, store active `jti` values in cache/DB and reject revoked ones.
- If token secrecy is required, do not rely on JWT/HMAC alone; use authenticated encryption or a server-side opaque token.
- Load secrets from environment/config only, never fall back to empty strings in production.

## Suggested `firebase/php-jwt` Migration

Install:

```powershell
composer require firebase/php-jwt
```

Example:

```php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use CodeIgniter\I18n\Time;

$now = Time::now()->getTimestamp();
$payload = [
    'iss' => base_url(),
    'aud' => 'resident-verification',
    'sub' => $residentId,
    'jti' => bin2hex(random_bytes(16)),
    'iat' => $now,
    'nbf' => $now,
    'exp' => $now + 900,
];

$jwt = JWT::encode($payload, $jwtSecret, 'HS256');
$decoded = (array) JWT::decode($jwt, new Key($jwtSecret, 'HS256'));

if ((int) $decoded['sub'] !== $residentId) {
    return false;
}
if (($decoded['aud'] ?? null) !== 'resident-verification') {
    return false;
}
```

Migration steps:

1. `composer require firebase/php-jwt`
2. Replace manual encode/decode in `TokenService`
3. Bind `sub` to the route ID during verification
4. Add `iss`, `aud`, and `jti`
5. Add a revocation store if you need early invalidation
6. Keep `--no-coverage` in CI until Xdebug or PCOV is installed

## Resident Filter Follow-Up

The resident listing already renders `searchName` and `filterHousehold`, but those are only client-side filters in `public/js/residents/residents-index.js`.

To finish the real filter flow:

1. Read `search` and `household` from `Resident::index()`
2. Extend `ResidentModel::getResidentsWithHousehold()` to apply them in SQL
3. Preserve them in the view and export links
4. Keep the JS only for UX polish, not as the source of truth

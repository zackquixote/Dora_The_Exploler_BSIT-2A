<?php
namespace App\Services;

use CodeIgniter\I18n\Time;

class TokenService
{
    protected ?string $encryptionKey = null;
    protected ?string $jwtSecret = null;

    public function __construct(?string $jwtSecret = null, ?string $encryptionKey = null)
    {
        $this->jwtSecret = $jwtSecret ?? env('JWT_SECRET') ?? '';
        $this->encryptionKey = $encryptionKey ?? env('ENCRYPTION_KEY') ?? env('encryption.key') ?? '';
    }

    /**
     * Generate a signed JWT for QR verification.
     */
    public function generateJwtToken(int $residentId, int $ttl = 900): string
    {
        $header = $this->base64UrlEncode(json_encode(['alg' => 'HS256', 'typ' => 'JWT']));
        $now = Time::now()->getTimestamp();
        $payload = $this->base64UrlEncode(json_encode([
            'sub' => $residentId,
            'iat' => $now,
            'exp' => $now + $ttl
        ]));
        $signature = hash_hmac('sha256', "$header.$payload", $this->jwtSecret, true);
        return "$header.$payload." . $this->base64UrlEncode($signature);
    }

    /**
     * Validate a JWT and return true if valid and not expired.
     */
    public function validateJwtToken(string $jwt, ?int $expectedResidentId = null): bool
    {
        $parts = explode('.', $jwt);
        if (count($parts) !== 3) {
            return false;
        }
        [$header, $payload, $signature] = $parts;

        $expectedSig = $this->base64UrlEncode(hash_hmac('sha256', "$header.$payload", $this->jwtSecret, true));
        if (!hash_equals($expectedSig, $signature)) {
            return false;
        }

        $headerData = json_decode($this->base64UrlDecode($header), true);
        if (!is_array($headerData) || ($headerData['alg'] ?? null) !== 'HS256' || ($headerData['typ'] ?? null) !== 'JWT') {
            return false;
        }

        $data = json_decode($this->base64UrlDecode($payload), true);
        if (!is_array($data) || !isset($data['sub'], $data['iat'], $data['exp'])) {
            return false;
        }

        if ($expectedResidentId !== null && (int) $data['sub'] !== $expectedResidentId) {
            return false;
        }

        $now = Time::now()->getTimestamp();
        if ((int) $data['iat'] > ($now + 60)) {
            return false;
        }

        if ((int) $data['exp'] < $now) {
            return false;
        }
        return true;
    }

    /**
     * Generate an HMAC-based token containing resident id and timestamp.
     * Format: base64url(json_payload).base64url(signature)
     */
    public function generateHmacToken(int $residentId): string
    {
        $payload = [
            'id' => $residentId,
            'ts' => Time::now()->getTimestamp(),
        ];
        $payloadB64 = $this->base64UrlEncode(json_encode($payload));
        $signature = $this->base64UrlEncode(hash_hmac('sha256', $payloadB64, $this->encryptionKey, true));
        return "$payloadB64.$signature";
    }

    /**
     * Validate HMAC token (payload.signature) and check freshness.
     */
    public function validateHmacToken(int $residentId, string $token, int $maxAge = 900): bool
    {
        $parts = explode('.', $token);
        if (count($parts) !== 2) {
            return false;
        }

        [$payloadB64, $sigB64] = $parts;
        $expectedSig = $this->base64UrlEncode(hash_hmac('sha256', $payloadB64, $this->encryptionKey, true));
        if (!hash_equals($expectedSig, $sigB64)) {
            return false;
        }

        $payloadJson = $this->base64UrlDecode($payloadB64);
        $data = json_decode($payloadJson, true);
        if (!is_array($data) || !isset($data['id']) || !isset($data['ts'])) {
            return false;
        }

        if ((int)$data['id'] !== $residentId) {
            return false;
        }

        $now = Time::now()->getTimestamp();
        $ts = (int)$data['ts'];
        if ($ts <= 0 || $ts > ($now + 60) || ($now - $ts) > $maxAge) {
            return false;
        }

        return true;
    }

    protected function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    protected function base64UrlDecode(string $data): string
    {
        if ($data === '' || preg_match('/[^A-Za-z0-9\-_]/', $data)) {
            return '';
        }

        $remainder = strlen($data) % 4;
        if ($remainder) {
            $data .= str_repeat('=', 4 - $remainder);
        }
        $decoded = base64_decode(strtr($data, '-_', '+/'), true);
        return $decoded === false ? '' : $decoded;
    }
}

?>

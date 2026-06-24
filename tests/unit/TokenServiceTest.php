<?php

use CodeIgniter\Test\CIUnitTestCase;
use App\Services\TokenService;

/**
 * @internal
 */
final class TokenServiceTest extends CIUnitTestCase
{
    public function testGenerateAndValidateHmacToken(): void
    {
        $enc = 'test_encryption_key_123';
        $jwt = 'test_jwt_secret_abc';
        $svc = new TokenService($jwt, $enc);

        $token = $svc->generateHmacToken(42);
        $this->assertIsString($token);
        $this->assertTrue($svc->validateHmacToken(42, $token));
    }

    public function testTamperedHmacFails(): void
    {
        $enc = 'another_key_456';
        $svc = new TokenService('s', $enc);
        $token = $svc->generateHmacToken(7);

        // Tamper payload (flip a char)
        $parts = explode('.', $token);
        $this->assertCount(2, $parts);
        $parts[0][0] = $parts[0][0] === 'A' ? 'B' : 'A';
        $tampered = implode('.', $parts);

        $this->assertFalse($svc->validateHmacToken(7, $tampered));
    }

    public function testGenerateAndValidateJwtToken(): void
    {
        $svc = new TokenService('jwt_secret_789', 'enc');
        $jwt = $svc->generateJwtToken(99, 60);
        $this->assertIsString($jwt);
        $this->assertTrue($svc->validateJwtToken($jwt));
    }

    public function testJwtSubjectMismatchFails(): void
    {
        $svc = new TokenService('jwt_secret_789', 'enc');
        $jwt = $svc->generateJwtToken(99, 60);

        $this->assertFalse($svc->validateJwtToken($jwt, 100));
    }

    public function testTamperedJwtFails(): void
    {
        $svc = new TokenService('jwt_secret_x', 'enc');
        $jwt = $svc->generateJwtToken(3, 60);

        $parts = explode('.', $jwt);
        $this->assertCount(3, $parts);
        // Tamper payload
        $parts[1][0] = $parts[1][0] === 'A' ? 'B' : 'A';
        $tampered = implode('.', $parts);

        $this->assertFalse($svc->validateJwtToken($tampered));
    }
}

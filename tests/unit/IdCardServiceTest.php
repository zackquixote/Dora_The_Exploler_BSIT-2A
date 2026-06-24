<?php

use CodeIgniter\Test\CIUnitTestCase;
use App\Services\IdCardService;
use App\Services\TokenService;

/**
 * @internal
 */
final class IdCardServiceTest extends CIUnitTestCase
{
    public function testGetQrUrlContainsResidentAndJwt(): void
    {
        $id = 5;
        $jwtSecret = 'test_jwt';
        $encKey = 'test_enc';
        $tokenSvc = new TokenService($jwtSecret, $encKey);
        $idService = new IdCardService(null, $tokenSvc);

        $url = $idService->getQrUrl($id);
        $this->assertStringContainsString("/verify/{$id}/", $url);

        $parts = explode('/', $url);
        $jwt = end($parts);
        $this->assertNotEmpty($jwt);
        $this->assertCount(3, explode('.', $jwt));
    }

    public function testValidateTokenDelegatesToTokenService(): void
    {
        $id = 10;
        $jwtSecret = 's_jwt';
        $encKey = 's_enc';

        $tokenSvc = new TokenService($jwtSecret, $encKey);
        $idService = new IdCardService(null, $tokenSvc);

        $token = $tokenSvc->generateHmacToken($id);
        $this->assertTrue($idService->validateToken($id, $token));
    }
}

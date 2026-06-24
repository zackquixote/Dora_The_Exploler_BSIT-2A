<?php
namespace App\Services;

use App\Models\ResidentModel;
use App\Exceptions\ResidentNotFoundException;
use App\Services\TokenService;

class IdCardService
{
    protected ?ResidentModel $residentModel = null;
    protected ?string $encryptionKey = null;
    protected ?string $jwtSecret = null;
    protected TokenService $tokenService;
    public function __construct(?ResidentModel $residentModel = null, ?TokenService $tokenService = null)
    {
        $this->residentModel = $residentModel;
        $this->encryptionKey = env('ENCRYPTION_KEY') ?? env('encryption.key') ?? '';
        $this->jwtSecret = env('JWT_SECRET') ?? '';
        $this->tokenService = $tokenService ?? new TokenService($this->jwtSecret, $this->encryptionKey);
    }

    /**
     * Retrieve resident with household and cache result.
     */
    public function getResident(int $id): array
    {
        $cacheKey = $this->makeCacheKey('resident_' . $id);
        $cached = cache()->get($cacheKey);
        if ($cached) {
            return $cached;
        }

        $resident = ($this->residentModel ??= new ResidentModel())->getDetailsWithHousehold($id);
        if (! $resident) {
            throw new ResidentNotFoundException("Resident with ID {$id} not found");
        }

        cache()->save($cacheKey, $resident, 300); // cache for 5 minutes
        return $resident;
    }

    /**
     * Build a cache key that strips reserved characters.
     */
    protected function makeCacheKey(string $key): string
    {
        return preg_replace('/[{}()\/\\@:]/', '_', $key);
    }

    /**
     * Generate signed token for verification URL.
     */
    
    /**
     * Generate a signed JWT for QR verification.
     */
    public function generateJwtToken(int $residentId, int $ttl = 900): string
    {
        return $this->tokenService->generateJwtToken($residentId, $ttl);
    }

    /**
     * Validate a JWT and return true if valid and not expired.
     */
    public function validateJwtToken(string $jwt, ?int $residentId = null): bool
    {
        return $this->tokenService->validateJwtToken($jwt, $residentId);
    }

    /**
     * Base64 URL-safe encode.
     */
    protected function base64UrlEncode(string $data): string
    {
        return $this->tokenService->base64UrlEncode($data);
    }

    /**
     * Base64 URL-safe decode.
     */
    protected function base64UrlDecode(string $data): string
    {
        return $this->tokenService->base64UrlDecode($data);
    }

    /**
     * Validate token and return bool.
     */
    public function validateToken(int $residentId, string $token, int $maxAge = 900): bool
    {
        return $this->tokenService->validateHmacToken($residentId, $token, $maxAge);
    }

    /**
     * Generate an HMAC-based token containing resident id and timestamp.
     * Format: base64url(json_payload).base64url(signature)
     */
    public function generateHmacToken(int $residentId): string
    {
        return $this->tokenService->generateHmacToken($residentId);
    }

    /**
     * Generate QR code URL for resident.
     */
    public function getQrUrl(int $residentId): string
    {
        $jwt = $this->generateJwtToken($residentId);
        return base_url("verify/{$residentId}/{$jwt}");
    }

    /**
     * Log audit action.
     */
    public function logAction(int $userId, string $action, string $entity, int $entityId, ?array $old = null, ?array $new = null): void
    {
        $db = \Config\Database::connect();
        $request = \Config\Services::request();
        $data = [
            'user_id'   => $userId,
            'action'    => $action,
            'entity'    => $entity,
            'entity_id' => $entityId,
            'old_data'  => $old ? json_encode($old) : null,
            'new_data'  => $new ? json_encode($new) : null,
            'ip_address'=> $request->getIPAddress(),
            'created_at'=> date('Y-m-d H:i:s'),
        ];
        $db->table('audit_logs')->insert($data);
    }
}
?>

<?php
namespace App\Services;

use App\Models\ResidentModel;
use App\Exceptions\ResidentNotFoundException;
use CodeIgniter\I18n\Time;

class IdCardService
{
    protected ResidentModel $residentModel;
    protected ?string $encryptionKey = null;
    protected ?string $jwtSecret = null;
    public function __construct()
    {
        $this->residentModel = new ResidentModel();
        $this->encryptionKey = env('ENCRYPTION_KEY') ?? '';
        $this->jwtSecret = env('JWT_SECRET') ?? '';
    }

    /**
     * Retrieve resident with household and cache result.
     */
    public function getResident(int $id): array
    {
        $cacheKey = 'resident_' . $id;
        $cached = cache()->get($cacheKey);
        if ($cached) {
            return $cached;
        }
        $resident = $this->residentModel->getDetailsWithHousehold($id);
        if (!$resident) {
            throw new ResidentNotFoundException("Resident with ID {$id} not found");
        }
        cache()->save($cacheKey, $resident, 300); // cache for 5 minutes
        return $resident;
    }

    /**
     * Generate signed token for verification URL.
     */
    /**
     * Generate a signed JWT for QR verification.
     */
    public function generateJwtToken(int $residentId, int $ttl = 900): string
    {
        $header = base64_encode(json_encode(['alg' => 'HS256', 'typ' => 'JWT']));
        $now = time();
        $payload = base64_encode(json_encode([
            'sub' => $residentId,
            'iat' => $now,
            'exp' => $now + $ttl
        ]));
        $signature = hash_hmac('sha256', "$header.$payload", $this->jwtSecret, true);
        $jwt = "$header.$payload." . rtrim(strtr(base64_encode($signature), '+/', '-_'), '=');
        return $jwt;
    }

    /**
     * Validate a JWT and return true if valid and not expired.
     */
    public function validateJwtToken(string $jwt): bool
    {
        $parts = explode('.', $jwt);
        if (count($parts) !== 3) {
            return false;
        }
        [$header, $payload, $signature] = $parts;
        $expectedSig = rtrim(strtr(base64_encode(hash_hmac('sha256', "$header.$payload", $this->jwtSecret, true)), '+/', '-_'), '=');
        if (!hash_equals($expectedSig, $signature)) {
            return false;
        }
        $data = json_decode(base64_decode($payload), true);
        if (!isset($data['exp']) || $data['exp'] < time()) {
            return false;
        }
        return true;
    }

    /**
     * Validate token and return bool.
     */
    public function validateToken(int $residentId, string $token, int $maxAge = 900): bool
    {
        $payload = json_encode([
            'id' => $residentId,
            'ts' => time()
        ]);
        $expected = hash_hmac('sha256', $payload, $this->encryptionKey);
        // Simple check – timestamp freshness is not enforced here for brevity
        return hash_equals($expected, $token);
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

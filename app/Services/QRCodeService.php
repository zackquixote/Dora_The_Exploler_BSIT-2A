<?php

namespace App\Services;

use App\Models\CertificateModel;
use App\Models\ResidentModel;

/**
 * QR Code Service
 * Generates and validates QR codes for certificates and resident verification
 */
class QRCodeService
{
    protected CertificateModel $certificateModel;
    protected ResidentModel $residentModel;
    protected string $secretKey;

    public function __construct()
    {
        $this->certificateModel = new CertificateModel();
        $this->residentModel = new ResidentModel();
        $this->secretKey = env('QR_SECRET_KEY', 'default_secret_key_change_this');
    }

    /**
     * Generate QR code for certificate verification
     */
    public function generateCertificateQR(int $certificateId): array
    {
        $certificate = $this->certificateModel->find($certificateId);
        if (!$certificate) {
            throw new \Exception('Certificate not found');
        }

        // Create verification payload
        $payload = [
            'type' => 'certificate',
            'id' => $certificateId,
            'number' => $certificate['certificate_number'],
            'issued_date' => $certificate['created_at'],
            'expires' => date('Y-m-d H:i:s', strtotime('+1 year')), // QR expires in 1 year
        ];

        $token = $this->generateSecureToken($payload);
        $verificationUrl = base_url("verify/certificate/{$certificateId}/{$token}");

        return [
            'qr_data' => $verificationUrl,
            'token' => $token,
            'expires_at' => $payload['expires'],
        ];
    }

    /**
     * Generate QR code for resident ID verification
     */
    public function generateResidentQR(int $residentId): array
    {
        $resident = $this->residentModel->find($residentId);
        if (!$resident) {
            throw new \Exception('Resident not found');
        }

        // Create verification payload
        $payload = [
            'type' => 'resident',
            'id' => $residentId,
            'name' => $resident['first_name'] . ' ' . $resident['last_name'],
            'issued_date' => date('Y-m-d H:i:s'),
            'expires' => date('Y-m-d H:i:s', strtotime('+6 months')), // QR expires in 6 months
        ];

        $token = $this->generateSecureToken($payload);
        $verificationUrl = base_url("verify/resident/{$residentId}/{$token}");

        return [
            'qr_data' => $verificationUrl,
            'token' => $token,
            'expires_at' => $payload['expires'],
        ];
    }

    /**
     * Generate QR code image as base64 PNG.
     * Uses the Google Charts API so no local library is required.
     * Swap this implementation for a Composer package (e.g. endroid/qr-code)
     * whenever you want fully offline generation.
     */
    public function generateQRImage(string $data, int $size = 300): string
    {
        $encodedData = urlencode($data);
        $url = "https://chart.googleapis.com/chart?chs={$size}x{$size}&cht=qr&chl={$encodedData}&choe=UTF-8";

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $imageData = curl_exec($ch);
        $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200 || empty($imageData)) {
            // Return a 1×1 transparent PNG as a safe fallback
            return 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=';
        }

        return 'data:image/png;base64,' . base64_encode($imageData);
    }

    /**
     * Verify QR code token
     */
    public function verifyToken(string $type, int $id, string $token): array
    {
        try {
            $payload = $this->decodeSecureToken($token);
            
            // Verify token type and ID match
            if ($payload['type'] !== $type || $payload['id'] !== $id) {
                return ['valid' => false, 'error' => 'Invalid token'];
            }

            // Check expiration
            if (strtotime($payload['expires']) < time()) {
                return ['valid' => false, 'error' => 'Token expired'];
            }

            // Verify entity still exists and is valid
            if ($type === 'certificate') {
                $certificate = $this->certificateModel->find($id);
                if (!$certificate) {
                    return ['valid' => false, 'error' => 'Certificate not found'];
                }
                
                return [
                    'valid' => true,
                    'data' => $certificate,
                    'verified_at' => date('Y-m-d H:i:s'),
                ];
            } elseif ($type === 'resident') {
                $resident = $this->residentModel->find($id);
                if (!$resident || $resident['status'] !== 'active') {
                    return ['valid' => false, 'error' => 'Resident not found or inactive'];
                }
                
                return [
                    'valid' => true,
                    'data' => $resident,
                    'verified_at' => date('Y-m-d H:i:s'),
                ];
            }

            return ['valid' => false, 'error' => 'Unknown verification type'];

        } catch (\Exception $e) {
            return ['valid' => false, 'error' => 'Invalid token format'];
        }
    }

    /**
     * Generate secure token with HMAC
     */
    protected function generateSecureToken(array $payload): string
    {
        $jsonPayload = json_encode($payload);
        $encodedPayload = base64_encode($jsonPayload);
        $signature = hash_hmac('sha256', $encodedPayload, $this->secretKey);
        
        return $encodedPayload . '.' . $signature;
    }

    /**
     * Decode and verify secure token
     */
    protected function decodeSecureToken(string $token): array
    {
        $parts = explode('.', $token);
        if (count($parts) !== 2) {
            throw new \Exception('Invalid token format');
        }

        [$encodedPayload, $signature] = $parts;
        
        // Verify signature
        $expectedSignature = hash_hmac('sha256', $encodedPayload, $this->secretKey);
        if (!hash_equals($expectedSignature, $signature)) {
            throw new \Exception('Invalid token signature');
        }

        // Decode payload
        $jsonPayload = base64_decode($encodedPayload);
        $payload = json_decode($jsonPayload, true);
        
        if (!$payload) {
            throw new \Exception('Invalid token payload');
        }

        return $payload;
    }

    /**
     * Log verification attempt
     */
    public function logVerification(string $type, int $id, string $token, bool $success, string $ipAddress = null): void
    {
        $db = \Config\Database::connect();
        
        $logData = [
            'verification_type' => $type,
            'entity_id' => $id,
            'token_hash' => hash('sha256', $token),
            'success' => $success ? 1 : 0,
            'ip_address' => $ipAddress ?? ($_SERVER['REMOTE_ADDR'] ?? 'unknown'),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'verified_at' => date('Y-m-d H:i:s'),
        ];

        // Create verification_logs table if it doesn't exist
        if (!$db->tableExists('verification_logs')) {
            $forge = \Config\Database::forge();
            $forge->addField([
                'id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'auto_increment' => true,
                ],
                'verification_type' => [
                    'type' => 'VARCHAR',
                    'constraint' => 50,
                ],
                'entity_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                ],
                'token_hash' => [
                    'type' => 'VARCHAR',
                    'constraint' => 64,
                ],
                'success' => [
                    'type' => 'TINYINT',
                    'constraint' => 1,
                ],
                'ip_address' => [
                    'type' => 'VARCHAR',
                    'constraint' => 45,
                ],
                'user_agent' => [
                    'type' => 'TEXT',
                ],
                'verified_at' => [
                    'type' => 'DATETIME',
                ],
            ]);
            $forge->addKey('id', true);
            $forge->addKey(['verification_type', 'entity_id']);
            $forge->createTable('verification_logs');
        }

        $db->table('verification_logs')->insert($logData);
    }

    /**
     * Get verification statistics
     */
    public function getVerificationStats(array $dateRange = []): array
    {
        $db = \Config\Database::connect();
        
        if (!$db->tableExists('verification_logs')) {
            return ['total' => 0, 'successful' => 0, 'failed' => 0];
        }

        $builder = $db->table('verification_logs');
        
        if (!empty($dateRange)) {
            $builder->where('verified_at >=', $dateRange['start'])
                   ->where('verified_at <=', $dateRange['end']);
        }

        $stats = $builder->select('
            COUNT(*) as total,
            SUM(success) as successful,
            COUNT(*) - SUM(success) as failed
        ')->get()->getRowArray();

        return $stats;
    }

    /**
     * Generate batch QR codes for multiple certificates
     */
    public function generateBatchCertificateQRs(array $certificateIds): array
    {
        $results = [];
        
        foreach ($certificateIds as $certificateId) {
            try {
                $qrData = $this->generateCertificateQR($certificateId);
                $qrImage = $this->generateQRImage($qrData['qr_data']);
                
                $results[$certificateId] = [
                    'success' => true,
                    'qr_data' => $qrData,
                    'qr_image' => $qrImage,
                ];
            } catch (\Exception $e) {
                $results[$certificateId] = [
                    'success' => false,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return $results;
    }
}
<?php

namespace App\Models;

use CodeIgniter\Model;

class ResidentVerificationModel extends Model
{
    protected $table            = 'resident_verifications';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'resident_account_id',
        'resident_id',
        'first_name',
        'middle_name',
        'last_name',
        'birthdate',
        'address_submitted',
        'contact_email_submitted',
        'contact_phone_submitted',
        'national_id_number',
        'status',
        'otp_required',
        'otp_channel',
        'otp_code_hash',
        'otp_expires_at',
        'otp_sent_at',
        'otp_verified_at',
        'otp_attempt_count',
        'review_notes',
        'rejection_reason',
        'resubmission_reason',
        'reviewed_by',
        'reviewed_at',
        'submitted_at',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getLatestForAccount(int $residentAccountId): ?array
    {
        return $this->where('resident_account_id', $residentAccountId)
            ->orderBy('id', 'DESC')
            ->first();
    }

    public function getQueue(array $statuses): array
    {
        return $this->select('resident_verifications.*, resident_accounts.email, resident_accounts.phone, resident_accounts.status AS account_status, residents.first_name AS resident_first_name, residents.last_name AS resident_last_name')
            ->join('resident_accounts', 'resident_accounts.id = resident_verifications.resident_account_id', 'left')
            ->join('residents', 'residents.id = resident_verifications.resident_id', 'left')
            ->whereIn('resident_verifications.status', $statuses)
            ->orderBy('resident_verifications.submitted_at', 'DESC')
            ->findAll();
    }

    public function getDetails(int $verificationId): ?array
    {
        return $this->select('resident_verifications.*, resident_accounts.email, resident_accounts.phone, resident_accounts.status AS account_status, resident_accounts.resident_id AS account_resident_id, residents.first_name AS resident_first_name, residents.last_name AS resident_last_name, residents.sitio AS resident_sitio')
            ->join('resident_accounts', 'resident_accounts.id = resident_verifications.resident_account_id', 'left')
            ->join('residents', 'residents.id = resident_verifications.resident_id', 'left')
            ->where('resident_verifications.id', $verificationId)
            ->first();
    }
}

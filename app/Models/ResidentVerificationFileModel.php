<?php

namespace App\Models;

use CodeIgniter\Model;

class ResidentVerificationFileModel extends Model
{
    protected $table            = 'resident_verification_files';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'resident_verification_id',
        'file_type',
        'storage_path',
        'original_name',
        'mime_type',
        'file_size',
        'is_primary',
        'uploaded_at',
        'created_at',
    ];

    protected $useTimestamps = false;

    public function getForVerification(int $verificationId): array
    {
        return $this->where('resident_verification_id', $verificationId)
            ->orderBy('id', 'ASC')
            ->findAll();
    }
}

<?php

namespace App\Models;

use CodeIgniter\Model;

class ResidentAccountModel extends Model
{
    protected $table            = 'resident_accounts';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'resident_id',
        'email',
        'phone',
        'password_hash',
        'status',
        'rejection_reason',
        'verification_code',
        'verified_at',
        'reset_token',
        'reset_token_expiry',
        'last_login_at',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}


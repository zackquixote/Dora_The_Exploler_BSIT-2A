<?php

namespace App\Models;

use CodeIgniter\Model;

class CertificateRequestModel extends Model
{
    protected $table            = 'certificate_requests';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    
    protected $allowedFields = [
        'resident_id',
        'certificate_type',
        'purpose',
        'status',
        'remarks',
        'rejection_note',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'resident_id'      => 'required|integer',
        'certificate_type' => 'required|max_length[100]',
        'purpose'          => 'required',
        'status'           => 'required|in_list[Pending,Processing,Ready for Pickup,Released,Rejected,Cancelled]',
    ];
}

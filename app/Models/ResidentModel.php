<?php

namespace App\Models;

use CodeIgniter\Model;

class ResidentModel extends Model
{
    protected $table = 'residents';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'household_id',
        'first_name',
        'middle_name',
        'last_name',
        'birthdate',
        'sex',
        'civil_status',
        'contact_number',
        'relationship_to_head',
        'is_voter',
        'is_pwd',
        'is_senior_citizen',
        'status',
        'registered_by',
    ];

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;

    protected $useTimestamps = false;
}
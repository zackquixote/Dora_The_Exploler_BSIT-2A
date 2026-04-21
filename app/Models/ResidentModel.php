<?php

namespace App\Models;

use CodeIgniter\Model;

class ResidentModel extends Model
{
    protected $table = 'residents';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array'; // Or 'object' if you prefer
    protected $useSoftDeletes = true;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'household_id',
        'first_name',
        'middle_name',
        'last_name',
        'birthdate',
        'sex',
        'civil_status',
        'sitio',
        'contact_number',
        'occupation',
        'citizenship',
        'profile_picture',
        'relationship_to_head',
        'is_voter',
        'is_senior_citizen',
        'is_pwd',
        'status',
        'registered_by',
        'joined_household_date',  // Add if you update this manually
        'left_household_date',    // Add if you update this manually
        'member_status'           // Add if you update this manually
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime'; // Matches created_at type in DB
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation rules (Optional but recommended)
    protected $validationRules = [
        'first_name' => 'required|min_length[2]|max_length[80]',
        'last_name'  => 'required|min_length[2]|max_length[80]',
        'birthdate'  => 'required|valid_date',
    ];

    protected $validationMessages = [];
    protected $skipValidation = false;
}
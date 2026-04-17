<?php

namespace App\Models;

use CodeIgniter\Model;

class ResidentModel extends Model
{
    protected $table            = 'residents';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;   // <-- ADD THIS LINE
    protected $protectFields    = true;
    protected $allowedFields = [
        'household_id', 'first_name', 'middle_name', 'last_name',
        'birthdate', 'sex', 'civil_status', 'sitio', 'street_address',
        'contact_number', 'occupation', 'citizenship', 'profile_picture',
        'relationship_to_head', 'is_voter', 'is_senior_citizen',
        'is_pwd', 'status', 'registered_by'
    ];
}
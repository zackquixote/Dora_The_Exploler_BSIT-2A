<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * ResidentModel
 * 
 * Core model for barangay residents.
 * 
 * RECENT ENHANCEMENTS:
 * - Added getWithAge() for automatic age display.
 * - Duplicate protection via DB unique index.
 */
class ResidentModel extends Model
{
    protected $table      = 'residents';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'household_id',
        'is_household_head',
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
        'joined_household_date',
        'left_household_date',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
    protected $useSoftDeletes = true;

    protected $validationRules = [
        'first_name'   => 'required|min_length[2]|max_length[80]',
        'last_name'    => 'required|min_length[2]|max_length[80]',
        'birthdate'    => 'required|valid_date',
        'sex'          => 'required|in_list[male,female]',
        'civil_status' => 'permit_empty|in_list[single,married,widowed,separated]',
    ];

    /**
     * Fetch residents with auto‑computed age.
     */
    public function getWithAge()
    {
        return $this->select('residents.*, TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) as age');
    }
}
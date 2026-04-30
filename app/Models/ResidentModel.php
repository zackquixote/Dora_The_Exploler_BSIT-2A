<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * ResidentModel
 * 
 * Core model for managing resident information and demographics.
 * 
 * TABLE: residents
 * - Stores detailed profile of each person residing in the barangay.
 * 
 * FIELDS:
 * - household_id: FK to households table
 * - first_name, middle_name, last_name
 * - birthdate, sex, civil_status, contacto (contact_number)
 * - occupation, citizenship
 * - profile_picture
 * - relationship_to_head (e.g., Head, Spouse, Child)
 * - is_voter, is_senior_citizen, is_pwd (boolean flags)
 * - status (e.g., Active, Inactive, Transferred)
 * - registered_by: FK to users.id who registered the resident
 * - joined_household_date, left_household_date, member_status (for household history)
 * 
 * SOFT DELETE:
 * - Uses soft deletes via `deleted_at` field.
 * 
 * TIMESTAMPS: created_at, updated_at, deleted_at
 * 
 * VALIDATION:
 * - first_name: required, length 2-80
 * - last_name: required, length 2-80
 * - birthdate: required, valid date
 * 
 * @package App\Models
 */
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
<?php
namespace App\Models;

use CodeIgniter\Model;

class ResidentModel extends Model
{
    protected $table      = 'residents';
    protected $primaryKey = 'id';

    // Automatically handle created_at and updated_at fields
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'registered_at'; // Matches your SQL column name
    protected $updatedField  = 'updated_at';   // You might need to add this column to your DB if not present

    protected $returnType = 'array';

    protected $allowedFields = [
        'household_id',
        'registered_by',
        
        // Names
        'first_name',
        'middle_name',
        'last_name',
        
        // Personal Details
        'birthdate',
        'sex',            // Changed from 'gender' to match SQL ENUM('male','female')
        'civil_status',
        
        // Contact & Work
        'contact_number',
        'occupation',     // Included based on your previous code
        'relationship_to_head', 
        
        // Flags (Booleans)
        'is_voter',
        'is_senior_citizen',
        'is_pwd',
        
        // Status
        'status'          // active/inactive
    ];

    protected $validationRules = [
        'first_name' => 'required|min_length[2]|max_length[100]',
        'last_name'  => 'required|min_length[2]|max_length[100]',
        'birthdate'  => 'required|valid_date',
        'sex'        => 'required|in_list[male,female]', // Must match SQL ENUM values exactly
        'civil_status' => 'required|in_list[single,married,widowed,separated]',
    ];
}
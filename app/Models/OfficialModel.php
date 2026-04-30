<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * OfficialModel
 * 
 * Manages elected/appointed barangay officials.
 * 
 * TABLE: officials
 * - Stores profile information for each official.
 * 
 * FIELDS:
 * - position: Title/role (e.g., Barangay Captain, Secretary, Treasurer)
 * - full_name: Complete name of the official
 * - contact_number: Phone/contact information
 * - photo: File path or filename for profile image
 * - is_active: Boolean (1/0) indicating if currently serving
 * 
 * TIMESTAMPS: created_at, updated_at
 * 
 * VALIDATION:
 * - full_name required, min length 3
 * - position required
 * 
 * @package App\Models
 */
class OfficialModel extends Model
{
    protected $table            = 'officials';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    // Matches your DB Structure exactly
    protected $allowedFields = [
        'position',
        'full_name',
        'contact_number',
        'photo',
        'is_active',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'full_name' => 'required|min_length[3]',
        'position'  => 'required',
    ];
}
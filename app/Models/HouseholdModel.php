<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * HouseholdModel
 * 
 * Manages household units and their primary address information.
 * 
 * TABLE: households
 * - Represents a group of residents living under one house number/address.
 * 
 * FIELDS:
 * - household_no: Unique household identifier
 * - address: Full address text
 * - street_address: Street part of the address
 * - head_resident_id: FK to residents.id (household head)
 * - sitio: Must be one of the 5 predefined puroks
 * - house_type: Concrete, Semi-Concrete, Wood, Light Materials
 * 
 * CONSTRAINTS:
 * - Allowed sitios: Purok Malipayon, Purok Masagana, Purok Cory, Purok Kawayan, Purok Pagla-um
 * 
 * METHODS:
 * - getBySitio($sitio): Retrieve all households in a specific sitio
 * - getAllWithHead(): Join residents to get head resident name
 * - getResidentCount($householdId): Count residents belonging to a household
 * - isUniqueHouseholdNo($no, $ignoreId): Check for duplicate household numbers
 * 
 * @package App\Models
 */
class HouseholdModel extends Model
{
    protected $table = 'households';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array'; 
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'household_no',
        'address',
        'street_address',
        'head_resident_id',
        'sitio',
        'house_type',
        'created_at'
    ];

    protected $useTimestamps = false;
    protected $dateFormat = 'datetime'; 
    protected $createdField = 'created_at';
    
    // --- UPDATED: Only these 5 Puroks are allowed ---
    protected $allowedSitios = [
        'Purok Malipayon', 
        'Purok Masagana', 
        'Purok Cory',       
        'Purok Kawayan',    
        'Purok Pagla-um',   
    ];

    // --- UPDATED: Validation rule updated to match the 5 above exactly ---
    protected $validationRules = [
        'household_no' => 'required|min_length[3]|max_length[50]',
        'sitio'        => 'required|in_list[Purok Malipayon,Purok Masagana,Purok Cory,Purok Kawayan,Purok Pagla-um]',
        'house_type'   => 'permit_empty|in_list[Concrete,Semi-Concrete,Wood,Light Materials]'
    ];

    protected $validationMessages = [
        'household_no' => [
            'required' => 'Household number is required',
        ],
        'sitio' => [
            'required' => 'Sitio/Purok is required',
            'in_list'  => 'Invalid Sitio selected' 
        ]
    ];

    /**
     * Get households by sitio/purok
     */
    public function getBySitio($sitio)
    {
        return $this->where('sitio', $sitio)
                    ->orderBy('household_no', 'ASC')
                    ->findAll();
    }

    /**
     * Get all households with head resident info
     */
    public function getAllWithHead()
    {
        return $this->select('households.*, CONCAT(residents.first_name, " ", residents.last_name) as head_name')
                    ->join('residents', 'residents.id = households.head_resident_id', 'left')
                    ->orderBy('household_no', 'ASC')
                    ->findAll();
    }
    
    /**
     * Get count of residents per household
     */
    public function getResidentCount($householdId)
    {
        $db = \Config\Database::connect();
        return $db->table('residents')
                  ->where('household_id', $householdId)
                  ->countAllResults();
    }

    /**
     * Helper function to check uniqueness manually
     */
    public function isUniqueHouseholdNo($no, $ignoreId = null)
    {
        $builder = $this->where('household_no', $no);
        
        if ($ignoreId) {
            $builder->where('id !=', $ignoreId);
        }
        
        return $builder->countAllResults() === 0;
    }
}
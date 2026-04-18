<?php

namespace App\Models;

use CodeIgniter\Model;

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
    
    protected $validationRules = [
        'household_no' => 'required|is_unique[households.household_no]',
        'sitio' => 'required'
    ];

    protected $validationMessages = [
        'household_no' => [
            'required' => 'Household number is required',
            'is_unique' => 'This household number already exists'
        ],
        'sitio' => [
            'required' => 'Sitio/Purok is required'
        ]
    ];

    // Get households by sitio/purok
    public function getBySitio($sitio)
    {
        return $this->where('sitio', $sitio)
                    ->orderBy('household_no', 'ASC')
                    ->findAll();
    }

    // Get all households with head resident info
    public function getAllWithHead()
    {
        return $this->select('households.*, CONCAT(residents.first_name, " ", residents.last_name) as head_name')
                    ->join('residents', 'residents.id = households.head_resident_id', 'left')
                    ->orderBy('household_no', 'ASC')
                    ->findAll();
    }
    
    // Get count of residents per household
    public function getResidentCount($householdId)
    {
        $db = \Config\Database::connect();
        return $db->table('residents')
                  ->where('household_id', $householdId)
                  ->countAllResults();
    }
}
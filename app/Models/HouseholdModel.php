<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * HouseholdModel – Household Units
 * 
 * TABLE: households
 * 
 * FIX: Removed `created_at` from allowedFields – the DB sets it automatically
 * (DEFAULT CURRENT_TIMESTAMP). Manual insertion would override the default.
 */
class HouseholdModel extends Model
{
    protected $table      = 'households';
    protected $primaryKey = 'id';

    // Allowed for mass assignment (do NOT include 'created_at')
    protected $allowedFields = [
        'household_no',
        'address',
        'street_address',
        'head_resident_id',
        'sitio',
        'house_type'
    ];

    // Timestamps are not used because we don't supply 'created_at'
    protected $useTimestamps = false;

    protected $allowedSitios = [
        'Purok Malipayon',
        'Purok Masagana',
        'Purok Cory',
        'Purok Kawayan',
        'Purok Pagla-um',
    ];

    protected $validationRules = [
        'household_no' => 'required|min_length[3]|max_length[50]',
        'sitio'        => 'required|in_list[Purok Malipayon,Purok Masagana,Purok Cory,Purok Kawayan,Purok Pagla-um]',
        'house_type'   => 'permit_empty|in_list[Concrete,Semi-Concrete,Wood,Light Materials]'
    ];

    public function getAllWithHead()
    {
        return $this->select('households.*, CONCAT(residents.first_name, " ", residents.last_name) as head_name')
                    ->join('residents', 'residents.id = households.head_resident_id', 'left')
                    ->orderBy('household_no', 'ASC')
                    ->findAll();
    }

    public function getResidentCount($householdId)
    {
        $db = \Config\Database::connect();
        return $db->table('residents')
                  ->where('household_id', $householdId)
                  ->countAllResults();
    }

    public function isUniqueHouseholdNo($no, $ignoreId = null)
    {
        $builder = $this->where('household_no', $no);
        if ($ignoreId) {
            $builder->where('id !=', $ignoreId);
        }
        return $builder->countAllResults() === 0;
    }
}
<?php

namespace App\Models;

use CodeIgniter\Model;

class HouseholdModel extends Model
{
    protected $table      = 'households';
    protected $primaryKey = 'id';
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = null;

    protected $allowedFields = [
        'household_no', 'sitio', 'street_address', 'house_type', 'head_resident_id'
    ];

    protected $validationRules = [
        'household_no' => 'required|is_unique[households.household_no,id,{id}]',
    ];

    // Get all households with head resident name
    public function getAllWithHead()
    {
        return $this->select('households.*, CONCAT(residents.first_name, " ", residents.last_name) as head_name')
                    ->join('residents', 'residents.id = households.head_resident_id', 'left')
                    ->orderBy('households.id', 'DESC')
                    ->findAll();
    }

    // Get households for dropdown (select2)
    public function getOptions()
    {
        return $this->select('id, household_no, street_address')
                    ->orderBy('household_no', 'ASC')
                    ->findAll();
    }
}
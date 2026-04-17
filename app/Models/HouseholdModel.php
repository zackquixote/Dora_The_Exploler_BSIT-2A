<?php

namespace App\Models;

use CodeIgniter\Model;

class HouseholdModel extends Model
{
    protected $table = 'households';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'household_no',
        'street_address',
        'resident_id'
    ];

    // Optional: used in your controller list()
    public function getAllWithHead()
    {
        return $this->select('households.*, residents.first_name, residents.last_name')
            ->join('residents', 'residents.id = households.resident_id', 'left')
            ->findAll();
    }
}
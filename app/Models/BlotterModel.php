<?php

namespace App\Models;

use CodeIgniter\Model;

class BlotterModel extends Model
{
    protected $table      = 'blotter_records';
    protected $primaryKey = 'id';
    
    protected $allowedFields = [
        'complainant', 
        'respondent', 
        'incident_type', 
        'incident_date', 
        'incident_location', 
        'purok', 
        'details', 
        'status', 
        'action_taken',
        'created_by'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get all blotters with creator name
     * FIX: Changed users.username to users.name to match your DB structure
     */
    public function getBlotters()
    {
        return $this->select('blotter_records.*, users.name as created_by_name')
                    ->join('users', 'users.id = blotter_records.created_by', 'left')
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }
}
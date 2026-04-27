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
     */
    public function getBlotters()
    {
        return $this->select('blotter_records.*, users.username as created_by_name')
                    ->join('users', 'users.id = blotter_records.created_by', 'left')
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }
}
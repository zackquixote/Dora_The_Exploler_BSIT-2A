<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * BlotterModel
 * 
 * Handles barangay blotter (incident/complaint) records.
 * 
 * TABLE: blotter_records
 * - Each record represents a reported incident with complainant,
 *   respondent, incident type, date, location, purok, and status.
 * 
 * FIELDS:
 * - complainant, respondent, incident_type, incident_date
 * - incident_location, purok, details, status, action_taken
 * - created_by (foreign key to users.id)
 * 
 * METHODS:
 * - getBlotters(): Joins with users to return creator name.
 * 
 * TIMESTAMPS: created_at, updated_at
 * 
 * @package App\Models
 */
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
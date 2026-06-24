<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * BlotterModel – Incident / Case Records
 * 
 * TABLE: blotter_records
 */
class BlotterModel extends Model
{
    protected $table      = 'blotter_records';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'case_number',
        'incident_type',
        'incident_date',
        'incident_location',
        'purok',
        'details',
        'status',
        'source',
        'action_taken',
        'created_by',
        'updated_by'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'incident_type' => 'required|max_length[50]',
        'incident_date' => 'required|valid_date',
        'details'       => 'required',
        'status'        => 'permit_empty|in_list[Pending,Investigating,Ongoing,For Hearing,Settled,Dismissed,Referred,Unsettled]',
    ];

    // Optional: helper to get all cases with creator name
    public function getBlotters()
    {
        return $this->select('blotter_records.*, users.name as created_by_name')
                    ->join('users', 'users.id = blotter_records.created_by', 'left')
                    ->orderBy('blotter_records.created_at', 'DESC')
                    ->findAll();
    }
}
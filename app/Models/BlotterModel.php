<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * BlotterModel – Incident / Case Records
 * 
 * TABLE: blotter_records
 * 
 * UPGRADE NOTES (Case Management System):
 * - `case_number` added (unique identifier, format BLT-YYYY-XXXX)
 * - `updated_by` tracks last editor (FK to users.id)
 * - `complainant` & `respondent` are KEPT TEMPORARILY during migration
 *   They will be removed once the UI fully uses `blotter_parties`.
 */
class BlotterModel extends Model
{
    protected $table      = 'blotter_records';
    protected $primaryKey = 'id';

    // Allowed for mass assignment – mirrors current table columns.
    // After full migration, remove 'complainant' and 'respondent'.
    protected $allowedFields = [
        'case_number',          // NEW – unique case reference
        'complainant',          // DEPRECATED – to be dropped
        'respondent',           // DEPRECATED – to be dropped
        'incident_type',
        'incident_date',
        'incident_location',
        'purok',
        'details',
        'status',
        'action_taken',
        'created_by',           // FK -> users.id (recording officer)
        'updated_by'            // NEW – FK -> users.id (last editor)
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get all blotter records with creator name.
     * NOTE: Complainant/respondent names now come from BlotterPartyModel.
     */
    public function getBlotters()
    {
        return $this->select([
                'blotter_records.id',
                'case_number',
                'incident_type',
                'incident_date',
                'incident_location',
                'purok',
                'details',
                'status',
                'action_taken',
                'blotter_records.created_at',
                'users.name as created_by_name'
            ])
            ->join('users', 'users.id = blotter_records.created_by', 'left')
            ->orderBy('blotter_records.created_at', 'DESC')
            ->findAll();
    }
}
<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * BlotterPartyModel – Involved Parties (Complainant, Respondent, Witness)
 * 
 * TABLE: blotter_parties
 * 
 * This model replaces the old VARCHAR columns in blotter_records.
 * Each case can have multiple parties; each party can be a registered
 * resident (linked via resident_id) or an outsider (name + address).
 */
class BlotterPartyModel extends Model
{
    protected $table      = 'blotter_parties';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'blotter_id',
        'resident_id',
        'outsider_name',
        'outsider_address',
        'role'                  // 'complainant', 'respondent', 'witness'
    ];

    // Only created_at exists in the table (no updated_at).
    protected $useTimestamps = false;

    protected $validationRules = [
        'blotter_id' => 'required|integer',
        'role'       => 'required|in_list[complainant,respondent,witness]',
    ];

    /**
     * Get parties for a specific blotter case, including resident full name.
     */
    public function getByBlotter(int $blotterId)
    {
        return $this->select([
                'blotter_parties.*',
                'CONCAT(residents.first_name, " ", residents.last_name) as resident_name',
                'residents.profile_picture'
            ])
            ->join('residents', 'residents.id = blotter_parties.resident_id', 'left')
            ->where('blotter_id', $blotterId)
            ->findAll();
    }

    /**
     * Clean insert for a party record.
     */
    public function addParty(array $data)
    {
        return $this->insert($data);
    }

    /**
     * Get blotter history for a specific resident.
     */
    public function getHistoryByResident(int $residentId)
    {
        return $this->select('blotter_records.id, blotter_records.case_number, blotter_records.incident_type, blotter_records.incident_date, blotter_records.status, blotter_parties.role')
            ->join('blotter_records', 'blotter_records.id = blotter_parties.blotter_id')
            ->where('blotter_parties.resident_id', $residentId)
            ->orderBy('blotter_records.incident_date', 'DESC')
            ->findAll();
    }
}
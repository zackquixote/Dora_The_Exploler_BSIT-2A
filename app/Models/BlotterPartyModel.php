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
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = '';

    /**
     * Get parties for a specific blotter case, including resident full name.
     */
    public function getByBlotter(int $blotterId)
    {
        return $this->select([
                'blotter_parties.*',
                'CONCAT(residents.first_name, " ", residents.last_name) as resident_name'
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
}
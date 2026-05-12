<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * OfficialModel – Barangay Officials
 * 
 * TABLE: officials (created via new migration)
 * 
 * Each official record can optionally link to a resident (resident_id).
 * If you later change barangay_settings to reference officials.id,
 * this model will be central.
 */
class OfficialModel extends Model
{
    protected $table      = 'officials';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'resident_id',
        'position',
        'full_name',
        'contact_number',
        'photo',
        'is_active',
        'term_start',
        'term_end',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'position'  => 'required|min_length[3]|max_length[100]',
        'full_name' => 'required|min_length[3]|max_length[150]',
        'is_active' => 'required|in_list[0,1]',
    ];

    protected $validationMessages = [
        'position' => [
            'required' => 'Official position (e.g. Barangay Captain) is required.'
        ],
        'full_name' => [
            'required' => 'The full name of the official is required.'
        ]
    ];

    /**
     * Get all officials with resident full name if linked.
     */
    public function getAllWithResident()
    {
        return $this->select([
                'officials.*',
                'CONCAT(residents.first_name, " ", residents.last_name) as resident_fullname'
            ])
            ->join('residents', 'residents.id = officials.resident_id', 'left')
            ->findAll();
    }
}
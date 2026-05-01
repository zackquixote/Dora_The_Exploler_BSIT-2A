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
        'resident_id',      // optional FK
        'position',         // e.g., Barangay Captain, Secretary, Treasurer
        'full_name',        // display name
        'contact_number',
        'photo',            // profile image path
        'is_active'         // 1 = currently serving
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

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
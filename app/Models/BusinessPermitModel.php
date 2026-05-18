<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * BusinessPermitModel
 * Manages business permits and registrations
 */
class BusinessPermitModel extends Model
{
    protected $table = 'business_permits';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'business_permit_number',
        'owner_resident_id',
        'business_name',
        'business_type',
        'business_address',
        'contact_number',
        'email',
        'capital_amount',
        'employees_count',
        'permit_fee',
        'status',
        'issue_date',
        'expiry_date',
        'renewal_reminder_sent',
        'created_by',
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'business_permit_number' => 'required|is_unique[business_permits.business_permit_number]|max_length[50]',
        'owner_resident_id' => 'required|integer',
        'business_name' => 'required|max_length[255]',
        'business_type' => 'required|max_length[100]',
        'business_address' => 'required',
        'contact_number' => 'required|max_length[20]',
        'permit_fee' => 'required|decimal|greater_than[0]',
        'issue_date' => 'required|valid_date',
        'expiry_date' => 'required|valid_date',
    ];

    /**
     * Get businesses by owner
     */
    public function getByOwner(int $residentId): array
    {
        return $this->where('owner_resident_id', $residentId)
                   ->orderBy('created_at', 'DESC')
                   ->findAll();
    }

    /**
     * Get active businesses
     */
    public function getActive(): array
    {
        return $this->where('status', 'active')
                   ->orderBy('business_name')
                   ->findAll();
    }

    /**
     * Get expiring permits
     */
    public function getExpiring(int $days = 30): array
    {
        return $this->where('expiry_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL ? DAY)', $days)
                   ->where('status', 'active')
                   ->findAll();
    }

    /**
     * Get businesses by type
     */
    public function getByType(string $businessType): array
    {
        return $this->where('business_type', $businessType)
                   ->where('status', 'active')
                   ->orderBy('business_name')
                   ->findAll();
    }

    /**
     * Search businesses
     */
    public function search(string $query): array
    {
        return $this->groupStart()
                   ->like('business_name', $query)
                   ->orLike('business_address', $query)
                   ->orLike('business_type', $query)
                   ->groupEnd()
                   ->where('status', 'active')
                   ->orderBy('business_name')
                   ->findAll();
    }

    /**
     * Get business statistics
     */
    public function getStats(): array
    {
        $db = \Config\Database::connect();
        
        return $db->query("
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active,
                SUM(CASE WHEN status = 'expired' THEN 1 ELSE 0 END) as expired,
                SUM(CASE WHEN status = 'suspended' THEN 1 ELSE 0 END) as suspended,
                AVG(permit_fee) as avg_fee,
                SUM(permit_fee) as total_fees
            FROM business_permits
        ")->getRowArray();
    }
}
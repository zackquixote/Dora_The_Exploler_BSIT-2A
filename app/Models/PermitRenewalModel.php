<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * PermitRenewalModel
 * Yearly renewal record for a business permit.
 */
class PermitRenewalModel extends Model
{
    protected $table      = 'permit_renewals';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'business_permit_id',
        'renewal_year',
        'status',
        'amount_due',
        'payment_id',
        'paid_at',
        'approved_by',
        'approved_at',
        'printed_by',
        'printed_at',
        'print_count',
        'remarks',
        'created_by',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'business_permit_id' => 'required|integer',
        'renewal_year'       => 'required|integer|greater_than_equal_to[2000]|less_than_equal_to[2100]',
        'status'             => 'required|in_list[pending,paid,approved,printed]',
        'amount_due'         => 'required|decimal',
        'created_by'         => 'required|integer',
    ];

    /**
     * Get renewals for a business permit.
     */
    public function listByBusiness(int $businessPermitId): array
    {
        return $this->where('business_permit_id', $businessPermitId)
            ->orderBy('renewal_year', 'DESC')
            ->findAll();
    }

    /**
     * Get the latest renewal for a business permit.
     */
    public function getLatest(int $businessPermitId): ?array
    {
        return $this->where('business_permit_id', $businessPermitId)
            ->orderBy('renewal_year', 'DESC')
            ->first();
    }
}

<?php

namespace App\Models;

use CodeIgniter\Model;

class ResidentTransferHistoryModel extends Model
{
    protected $table      = 'resident_transfer_history';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'resident_id',
        'from_household_id',
        'to_household_id',
        'from_household_no',
        'to_household_no',
        'reason',
        'transferred_by',
        'transferred_at',
    ];

    protected $useTimestamps = false;

    /**
     * Get full transfer history for a resident, newest first.
     */
    public function getByResident(int $residentId): array
    {
        return $this->db->table('resident_transfer_history rth')
            ->select('rth.*, u.name as transferred_by_name')
            ->join('users u', 'u.id = rth.transferred_by', 'left')
            ->where('rth.resident_id', $residentId)
            ->orderBy('rth.transferred_at', 'DESC')
            ->get()
            ->getResultArray();
    }

    /**
     * Record a household transfer for a resident.
     * Looks up household numbers automatically.
     */
    public function record(int $residentId, ?int $fromHouseholdId, ?int $toHouseholdId, string $reason = '', ?int $userId = null): void
    {
        $fromNo = null;
        $toNo   = null;

        if ($fromHouseholdId) {
            $row = $this->db->table('households')->select('household_no')->where('id', $fromHouseholdId)->get()->getRowArray();
            $fromNo = $row['household_no'] ?? null;
        }
        if ($toHouseholdId) {
            $row = $this->db->table('households')->select('household_no')->where('id', $toHouseholdId)->get()->getRowArray();
            $toNo = $row['household_no'] ?? null;
        }

        // Skip if nothing actually changed
        if ($fromHouseholdId === $toHouseholdId) {
            return;
        }

        $this->insert([
            'resident_id'        => $residentId,
            'from_household_id'  => $fromHouseholdId,
            'to_household_id'    => $toHouseholdId,
            'from_household_no'  => $fromNo,
            'to_household_no'    => $toNo,
            'reason'             => $reason ?: null,
            'transferred_by'     => $userId,
            'transferred_at'     => date('Y-m-d H:i:s'),
        ]);
    }
}

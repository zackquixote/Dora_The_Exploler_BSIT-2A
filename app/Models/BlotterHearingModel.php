<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * BlotterHearingModel
 * 
 * Manages scheduled hearings for a blotter case.
 * 
 * TABLE: blotter_hearings
 */
class BlotterHearingModel extends Model
{
    protected $table      = 'blotter_hearings';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'blotter_id',
        'hearing_date',
        'hearing_time',
        'venue',
        'presiding_officer',
        'notes',
        'outcome',
        'status',
        'created_by',
        'notification_sent'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'blotter_id'   => 'required|integer',
        'hearing_date' => 'required|valid_date',
        'status'       => 'permit_empty|in_list[Scheduled,In Progress,Completed,Cancelled]',
    ];

    /**
     * Get all hearings for a specific blotter case, ordered by date descending.
     */
    public function getByBlotter(int $blotterId)
    {
        return $this->where('blotter_id', $blotterId)
                    ->orderBy('hearing_date', 'DESC')
                    ->orderBy('hearing_time', 'DESC')
                    ->findAll();
    }


    /**
 * Get upcoming hearings within given days that have not been notified.
 *
 * @param int $days  Threshold in days (e.g., 3 = within next 3 days)
 * @return array
 */
public function getUpcomingHearings($days = 3)
{
    $today     = date('Y-m-d');
    $threshold = date('Y-m-d', strtotime("+{$days} days"));

    return $this->db->table('blotter_hearings')
                ->select('blotter_hearings.*, blotter_records.case_number, blotter_hearings.blotter_id')
                ->join('blotter_records', 'blotter_records.id = blotter_hearings.blotter_id')
                ->where('blotter_hearings.hearing_date >=', $today)
                ->where('blotter_hearings.hearing_date <=', $threshold)
                ->where('blotter_hearings.status', 'Scheduled')
                ->orderBy('blotter_hearings.hearing_date', 'ASC')
                ->get()
                ->getResultArray();
}

/**
 * Get hearings that are past their date but still marked as 'Scheduled'.
 */
public function getOverdueHearings()
{
    $today = date('Y-m-d');
    return $this->db->table('blotter_hearings')
                ->select('blotter_hearings.*, blotter_records.case_number, blotter_hearings.blotter_id')
                ->join('blotter_records', 'blotter_records.id = blotter_hearings.blotter_id')
                ->where('blotter_hearings.hearing_date <', $today)
                ->where('blotter_hearings.status', 'Scheduled')
                ->orderBy('blotter_hearings.hearing_date', 'ASC')
                ->get()
                ->getResultArray();
}

/**
 * Mark a hearing as notified.
 *
 * @param int $id
 * @return bool
 */
public function markNotified($id)
{
    return $this->update($id, ['notification_sent' => 1]);
}
}
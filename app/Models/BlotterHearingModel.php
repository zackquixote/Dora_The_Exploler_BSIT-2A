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
        'created_by'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

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
}
<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * BlotterTimelineModel
 * 
 * Tracks status changes for a blotter case.
 * 
 * TABLE: blotter_timeline
 */
class BlotterTimelineModel extends Model
{
    protected $table      = 'blotter_timeline';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'blotter_id',
        'old_status',
        'new_status',
        'remarks',
        'created_by'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = '';

    /**
     * Get timeline entries for a case, newest first.
     */
    public function getByBlotter(int $blotterId)
    {
        return $this->where('blotter_id', $blotterId)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }
}
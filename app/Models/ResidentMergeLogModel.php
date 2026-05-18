<?php

namespace App\Models;

use CodeIgniter\Model;

class ResidentMergeLogModel extends Model
{
    protected $table      = 'resident_merge_logs';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'primary_resident_id',
        'merged_resident_id',
        'merged_by',
        'merged_at',
        'impact_json',
        'before_primary_json',
        'before_merged_json',
        'after_primary_json',
        'notes',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';

    protected $validationRules = [
        'primary_resident_id' => 'required|integer',
        'merged_resident_id'  => 'required|integer',
        'merged_at'           => 'required|valid_date',
    ];
}


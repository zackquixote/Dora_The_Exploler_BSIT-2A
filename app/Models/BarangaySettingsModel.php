<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * BarangaySettingsModel
 * 
 * Manages barangay configuration.
 * 
 * TABLE: barangay_settings
 * - id, barangay_name, municipality, province, contact_number
 * - captain_id, secretary_id, treasurer_id, updated_at
 * 
 * NOTE: created_at column does NOT exist in the table.
 * Timestamps are disabled because the table lacks created_at.
 * updated_at is auto-managed by MySQL (ON UPDATE CURRENT_TIMESTAMP).
 */
class BarangaySettingsModel extends Model
{
    protected $table            = 'barangay_settings';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'barangay_name',
        'municipality',
        'province',
        'contact_number',
        'captain_id',
        'secretary_id',
        'treasurer_id',
    ];

    // Timestamps disabled because table lacks 'created_at'
    protected $useTimestamps = false;
}
<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * BarangaySettingsModel – Single Row Configuration
 * 
 * TABLE: barangay_settings
 * 
 * Stores barangay info and key official positions (currently by resident_id).
 * 
 * TODO: After creating 'officials' table, consider changing captain_id etc.
 * to reference officials.id for consistency.
 */
class BarangaySettingsModel extends Model
{
    protected $table            = 'barangay_settings';
    protected $primaryKey       = 'id';
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

    // Disabled because the table lacks 'created_at'; updated_at is auto by DB.
    protected $useTimestamps = false;
}
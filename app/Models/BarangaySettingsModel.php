<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * BarangaySettingsModel – Single Row Configuration
 *
 * TABLE: barangay_settings
 *
 * Stores barangay info only (name, municipality, province, contact).
 * Official position assignments are fully managed by the 'officials' table
 * (normalised, with is_active flag).
 *
 * NOTE: The legacy columns captain_id, secretary_id, treasurer_id have been
 * physically dropped from the barangay_settings table. They must NOT appear
 * in $allowedFields or be passed to any update/insert call on this model.
 */
class BarangaySettingsModel extends Model
{
    protected $table      = 'barangay_settings';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'barangay_name',
        'municipality',
        'province',
        'contact_number',
    ];

    // Disabled: the table has no created_at managed by CI; updated_at is
    // handled automatically by the DB engine via ON UPDATE CURRENT_TIMESTAMP.
    protected $useTimestamps = false;
}
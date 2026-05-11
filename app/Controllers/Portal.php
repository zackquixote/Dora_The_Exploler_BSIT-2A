<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\BarangaySettingsModel;

/**
 * --------------------------------------------------------------------
 * Portal
 * --------------------------------------------------------------------
 * Handles controller operations and data logic for the application.
 */
class Portal extends BaseController
{
    /**
     * Execute index functionality.
     *
     * @return mixed
     */
    public function index()
    {
        // Fetch barangay info if available
        $settingsModel = new BarangaySettingsModel();
        $settings = $settingsModel->first();
        
        return view('portal/index', [
            'settings' => $settings
        ]);
    }
}

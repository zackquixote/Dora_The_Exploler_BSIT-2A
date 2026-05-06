<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\BarangaySettingsModel;

class Portal extends BaseController
{
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

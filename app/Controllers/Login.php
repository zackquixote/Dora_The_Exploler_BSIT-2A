<?php

namespace App\Controllers;

/**
 * Login Controller (Legacy/Alternative)
 * 
 * Provides a simplified authentication flow (used as an alternative to Auth.php).
 * 
 * METHODS:
 * - index(): Displays login form.
 * - auth(): Redirects to dashboard without actual authentication (placeholder).
 * - dashboard(): Loads adminlte view.
 * 
 * NOTE: This controller appears to be a placeholder/legacy version.
 * The main authentication logic is in Auth.php.
 * 
 * @package App\Controllers
 */
class Login extends BaseController
{

    /**
     * Execute index functionality.
     *
     * @return mixed
     */
    public function index()
    {
        // Keep this controller compatible with the main login view which expects
        // `$settings` (barangay name/municipality/province/logo).
        $settingsModel = new \App\Models\BarangaySettingsModel();
        $settings = $settingsModel->first();

        if (!$settings) {
            $settings = [
                'barangay_name' => 'Barangay',
                'municipality'  => 'Municipality',
                'province'      => 'Province',
                'logo'          => '',
            ];
        }

        return view('login', [
            'settings' => $settings,
        ]);
    }

    public function auth()
    {   
        // For now, just redirect to AdminLTE view directly (no auth check yet)
        return redirect()->to('/dashboard');
    }


    /**
     * Execute dashboard functionality.
     *
     * @return mixed
     */
    public function dashboard()
    {
        return view('adminlte_view'); 
    }
}

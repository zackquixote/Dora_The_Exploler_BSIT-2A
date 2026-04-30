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

    public function index()
    {
        return view('login'); // your login view filename
    }

    public function auth()
    {   
        // For now, just redirect to AdminLTE view directly (no auth check yet)
        return redirect()->to('/dashboard');
    }


    public function dashboard()
    {
        return view('adminlte_view'); 
    }
}
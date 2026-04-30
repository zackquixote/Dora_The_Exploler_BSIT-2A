<?php

namespace App\Controllers;

/**
 * Dashboard Controller
 * 
 * Serves as the landing page after successful login.
 * 
 * METHODS:
 * - index(): Checks authentication and loads the main dashboard view.
 * 
 * @package App\Controllers
 */
class Dashboard extends BaseController
{
    public function index()
    {
        if (!session()->get('user_id')) {
            return redirect()->to('/login');
        }

        return view('dashboard'); // create this view later
    }
}
<?php
namespace App\Controllers\Staff;
use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function index()
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'staff') {
            return redirect()->to('/login');
        }

        return view('staff/index');
    }
}
<?php

namespace App\Controllers\Staff;

use App\Controllers\BaseController;
use App\Models\ResidentModel;

class Dashboard extends BaseController
{
    public function index()
    {
        // Auth guard
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $residentModel = new ResidentModel();

        // ✅ Changed view path from 'staff/dashboard' to 'residents/dashboard'
        return view('residents/dashboard', [
            'totalResidents'  => $residentModel->countAll(),
            'totalHouseholds' => 0,   // replace with HouseholdModel later
            'pendingCerts'    => 0,   // replace with CertModel later
            'blotterCount'    => 0,   // replace with BlotterModel later
            'latestBlotters'  => [],  // replace with BlotterModel later
            'latestCerts'     => [],  // replace with CertModel later
        ]);
    }
}
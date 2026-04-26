<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ResidentModel;
use App\Models\HouseholdModel;

class Dashboard extends BaseController
{
    public function index()
    {
        // 1. Authentication Check
        if (!session()->get('logged_in') || session()->get('role') !== 'admin') {
            return redirect()->to('/login');
        }

        // 2. Initialize Database & Models
        $db             = \Config\Database::connect();
        $residentModel  = new ResidentModel();
        $householdModel = new HouseholdModel();

        // ── Primary stats ────────────────────────────────────────────────
        $totalResidents  = $db->table('residents')->where('deleted_at', null)->countAllResults();
        $totalHouseholds = $db->table('households')->countAllResults();
        $pendingCerts    = 0;   // replace with CertModel when ready
        $blotterCount    = 0;   // replace with BlotterModel when ready

        // ── Secondary stats ──────────────────────────────────────────────
        $totalVoters  = $db->table('residents')->where('deleted_at', null)->where('is_voter', 1)->countAllResults();
        $totalPwd     = $db->table('residents')->where('deleted_at', null)->where('is_pwd', 1)->countAllResults();
        $totalSenior  = $db->table('residents')->where('deleted_at', null)->where('is_senior_citizen', 1)->countAllResults();
        $totalMale    = $db->table('residents')->where('deleted_at', null)->where('sex', 'male')->countAllResults();
        $totalFemale  = $db->table('residents')->where('deleted_at', null)->where('sex', 'female')->countAllResults();
        $avgPerHousehold = $totalHouseholds > 0 ? round($totalResidents / $totalHouseholds, 1) : 0;

        // ── Residents per Purok (bar chart) ──────────────────────────────
        $purokCounts = $db->table('residents')
            ->select("COALESCE(NULLIF(sitio,''), 'Unassigned') as sitio, COUNT(*) as count")
            ->where('deleted_at', null)
            ->groupBy('sitio')
            ->orderBy('count', 'DESC')
            ->get()->getResultArray();

        // ── Civil status breakdown (pie chart) ───────────────────────────
        $civilStatusData = $db->table('residents')
            ->select("COALESCE(NULLIF(civil_status,''), 'Unknown') as civil_status, COUNT(*) as count")
            ->where('deleted_at', null)
            ->groupBy('civil_status')
            ->orderBy('count', 'DESC')
            ->get()->getResultArray();

        // ── Staff account count (admin-only stat) ────────────────────────
        $totalStaff = $db->table('users')->where('role', 'staff')->countAllResults();
        $totalAdmins = $db->table('users')->where('role', 'admin')->countAllResults();

        // ── Recent activity ──────────────────────────────────────────────
        $latestBlotters = [];   // replace with BlotterModel when ready
        $latestCerts    = [];   // replace with CertModel when ready

        // 3. Load the View
        // NOTE: This loads 'Admin/dashboard' (app/Views/Admin/dashboard.php)
        return view('Admin/dashboard', [
            'title'           => 'Admin Dashboard',
            'totalResidents'  => $totalResidents,
            'totalHouseholds' => $totalHouseholds,
            'pendingCerts'    => $pendingCerts,
            'blotterCount'    => $blotterCount,
            'totalVoters'     => $totalVoters,
            'totalPwd'        => $totalPwd,
            'totalSenior'     => $totalSenior,
            'totalMale'       => $totalMale,
            'totalFemale'     => $totalFemale,
            'avgPerHousehold' => $avgPerHousehold,
            'purokCounts'     => $purokCounts,
            'civilStatusData' => $civilStatusData,
            'totalStaff'      => $totalStaff,
            'totalAdmins'     => $totalAdmins,
            'latestBlotters'  => $latestBlotters,
            'latestCerts'     => $latestCerts,
        ]);
    }
}
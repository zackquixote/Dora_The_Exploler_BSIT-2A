<?php

namespace App\Controllers\Staff;

use App\Controllers\BaseController;
use App\Models\ResidentModel;
use App\Models\HouseholdModel;

class Dashboard extends BaseController
{
    public function index()
    {
        // ── Guard: must be logged in AND be staff ──────────────────────
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        // If an admin somehow lands here, send them to their own dashboard
        if (session()->get('role') !== 'staff') {
            return redirect()->to('/admin/dashboard');
        }

        $db = \Config\Database::connect();

        $totalResidents  = $db->table('residents')->where('deleted_at', null)->countAllResults();
        $totalHouseholds = $db->table('households')->countAllResults();
        $pendingCerts    = 0;
        $blotterCount    = 0;

        $totalVoters     = $db->table('residents')->where('deleted_at', null)->where('is_voter', 1)->countAllResults();
        $totalPwd        = $db->table('residents')->where('deleted_at', null)->where('is_pwd', 1)->countAllResults();
        $totalSenior     = $db->table('residents')->where('deleted_at', null)->where('is_senior_citizen', 1)->countAllResults();
        $totalMale       = $db->table('residents')->where('deleted_at', null)->where('sex', 'male')->countAllResults();
        $totalFemale     = $db->table('residents')->where('deleted_at', null)->where('sex', 'female')->countAllResults();
        $avgPerHousehold = $totalHouseholds > 0 ? round($totalResidents / $totalHouseholds, 1) : 0;

        $purokCounts = $db->table('residents')
            ->select("COALESCE(NULLIF(sitio,''), 'Unassigned') as sitio, COUNT(*) as count")
            ->where('deleted_at', null)
            ->groupBy('sitio')
            ->orderBy('count', 'DESC')
            ->get()->getResultArray();

        $civilStatusData = $db->table('residents')
            ->select("COALESCE(NULLIF(civil_status,''), 'Unknown') as civil_status, COUNT(*) as count")
            ->where('deleted_at', null)
            ->groupBy('civil_status')
            ->orderBy('count', 'DESC')
            ->get()->getResultArray();

        // ── Loads app/Views/Staff/dashboard.php ───────────────────────
        return view('Staff/dashboard', [
            'title'           => 'Staff Dashboard',
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
            'latestBlotters'  => [],
            'latestCerts'     => [],
        ]);
    }
}
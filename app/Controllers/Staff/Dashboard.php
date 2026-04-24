<?php

namespace App\Controllers\Staff;

use App\Controllers\BaseController;
use App\Models\ResidentModel;
use App\Models\HouseholdModel;

class Dashboard extends BaseController
{
    public function index()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

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

        // ── Recent activity ──────────────────────────────────────────────
        $latestBlotters = [];   // replace with BlotterModel when ready
        $latestCerts    = [];   // replace with CertModel when ready

        return view('residents/dashboard', [
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
            'latestBlotters'  => $latestBlotters,
            'latestCerts'     => $latestCerts,
        ]);
    }
}
<?php

namespace App\Controllers\Staff;

use App\Controllers\BaseController;
use App\Models\ResidentModel;
use App\Models\HouseholdModel;
use App\Models\LogModel;
use App\Models\BlotterModel;
use App\Models\BlotterHearingModel;

class Dashboard extends BaseController
{

    public function index()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }
        if (session()->get('role') !== 'staff') {
            return redirect()->to('/admin/dashboard');
        }

        $db = \Config\Database::connect();
        $logModel     = new LogModel();
        $blotterModel = new BlotterModel();
        $hearingModel = new BlotterHearingModel();

        $totalResidents  = $db->table('residents')->where('deleted_at', null)->countAllResults();
        $totalHouseholds = $db->table('households')->countAllResults();
        $pendingCerts    = 0;
        $blotterCount    = $blotterModel->countAll();

        $totalVoters  = $db->table('residents')->where('deleted_at', null)->where('is_voter', 1)->countAllResults();
        $totalPwd     = $db->table('residents')->where('deleted_at', null)->where('is_pwd', 1)->countAllResults();
        $totalSenior  = $db->table('residents')->where('deleted_at', null)->where('is_senior_citizen', 1)->countAllResults();
        $totalMale    = $db->table('residents')->where('deleted_at', null)->where('sex', 'male')->countAllResults();
        $totalFemale  = $db->table('residents')->where('deleted_at', null)->where('sex', 'female')->countAllResults();
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

        // Blotter Stats
        $today = date('Y-m-d');
        $openCases = $db->table('blotter_records')
            ->whereIn('status', ['Pending','Investigating','Ongoing','For Hearing'])
            ->countAllResults();

        $hearingsToday = $hearingModel
            ->where('hearing_date', $today)
            ->countAllResults();

        $nextWeek = date('Y-m-d', strtotime('+7 days'));
        $upcomingHearings = $hearingModel
            ->where('hearing_date >=', $today)
            ->where('hearing_date <=', $nextWeek)
            ->orderBy('hearing_date', 'ASC')
            ->findAll(5);

        $recentCases = $blotterModel
            ->select('case_number, incident_type, status, incident_date')
            ->orderBy('id', 'DESC')
            ->limit(5)
            ->findAll();

        $recentLogs = $logModel->orderBy('DATELOG DESC, TIMELOG DESC')->limit(10)->findAll();

        return view('Staff/dashboard', [
            'title'            => 'Staff Dashboard',
            'totalResidents'   => $totalResidents,
            'totalHouseholds'  => $totalHouseholds,
            'pendingCerts'     => $pendingCerts,
            'blotterCount'     => $blotterCount,
            'totalVoters'      => $totalVoters,
            'totalPwd'         => $totalPwd,
            'totalSenior'      => $totalSenior,
            'totalMale'        => $totalMale,
            'totalFemale'      => $totalFemale,
            'avgPerHousehold'  => $avgPerHousehold,
            'purokCounts'      => $purokCounts,
            'civilStatusData'  => $civilStatusData,
            'latestBlotters'   => [],
            'latestCerts'      => [],
            'recentLogs'       => $recentLogs,
            // new blotter stats
            'openCases'        => $openCases,
            'hearingsToday'    => $hearingsToday,
            'upcomingHearings' => $upcomingHearings,
            'recentCases'      => $recentCases,
        ]);
    }
}
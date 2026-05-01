<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ResidentModel;
use App\Models\HouseholdModel;
use App\Models\LogModel;
use App\Models\BlotterModel;
use App\Models\CertificateModel;
use App\Models\BlotterHearingModel;

class Dashboard extends BaseController
{
    public function index()
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'admin') {
            return redirect()->to('/login');
        }

        $db             = \Config\Database::connect();
        $residentModel  = new ResidentModel();
        $householdModel = new HouseholdModel();
        $logModel       = new LogModel();
        $blotterModel   = new BlotterModel();
        $certModel      = new CertificateModel();
        $hearingModel   = new BlotterHearingModel();

        // --- Existing stats (unchanged) ---
        $totalResidents = $db->table('residents')->where('deleted_at', null)->countAllResults();
        $totalHouseholds = $db->table('households')->countAllResults();
        $totalCerts = $certModel->countAll();
        $today = date('Y-m-d');
        $dailyCerts = $certModel->where('DATE(created_at)', $today)->countAllResults();
        $blotterCount = $blotterModel->countAll();

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

        // --- Blotter Statistics ---
        // Open cases (Pending, Investigating, Ongoing, For Hearing)
        $openCases = $db->table('blotter_records')
            ->whereIn('status', ['Pending','Investigating','Ongoing','For Hearing'])
            ->countAllResults();

        // Cases settled this month
        $settledThisMonth = $db->table('blotter_records')
            ->where('status', 'Settled')
            ->where('MONTH(updated_at)', date('m'))
            ->where('YEAR(updated_at)', date('Y'))
            ->countAllResults();

        // Hearings today
        $hearingsToday = $hearingModel
            ->where('hearing_date', $today)
            ->countAllResults();

        // Upcoming hearings (next 7 days)
        $nextWeek = date('Y-m-d', strtotime('+7 days'));
        $upcomingHearings = $hearingModel
            ->where('hearing_date >=', $today)
            ->where('hearing_date <=', $nextWeek)
            ->orderBy('hearing_date', 'ASC')
            ->orderBy('hearing_time', 'ASC')
            ->findAll(5);

        // Cases by purok (for a quick table)
        $casesByPurok = $db->table('blotter_records')
            ->select("COALESCE(NULLIF(purok,''), 'Unknown') as purok, COUNT(*) as total")
            ->groupBy('purok')
            ->orderBy('total', 'DESC')
            ->get()->getResultArray();

        // Recent cases
        $recentCases = $blotterModel
            ->select('case_number, incident_type, status, incident_date')
            ->orderBy('id', 'DESC')
            ->limit(5)
            ->findAll();

        // Recent activity logs (unchanged but keep)
        $recentLogs = $logModel->orderBy('DATELOG DESC, TIMELOG DESC')->limit(10)->findAll();

        return view('Admin/dashboard', [
            'title'           => 'Admin Dashboard',
            // existing
            'totalResidents'  => $totalResidents,
            'totalHouseholds' => $totalHouseholds,
            'pendingCerts'    => $totalCerts,
            'dailyCerts'      => $dailyCerts,
            'blotterCount'    => $blotterCount,
            'totalVoters'     => $totalVoters,
            'totalPwd'        => $totalPwd,
            'totalSenior'     => $totalSenior,
            'totalMale'       => $totalMale,
            'totalFemale'     => $totalFemale,
            'avgPerHousehold' => $avgPerHousehold,
            'purokCounts'     => $purokCounts,
            'civilStatusData' => $civilStatusData,
            'totalStaff'      => $db->table('users')->where('role', 'staff')->countAllResults(),
            'totalAdmins'     => $db->table('users')->where('role', 'admin')->countAllResults(),
            'latestBlotters'  => [], // you can populate
            'latestCerts'     => [],
            'recentLogs'      => $recentLogs,
            // new blotter stats
            'openCases'        => $openCases,
            'settledThisMonth' => $settledThisMonth,
            'hearingsToday'    => $hearingsToday,
            'upcomingHearings' => $upcomingHearings,
            'casesByPurok'     => $casesByPurok,
            'recentCases'      => $recentCases,
        ]);
    }
}
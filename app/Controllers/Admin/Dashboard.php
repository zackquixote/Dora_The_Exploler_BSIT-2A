<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ResidentModel;
use App\Models\HouseholdModel;
use App\Models\LogModel;
use App\Models\BlotterModel;
use App\Models\CertificateModel;
use App\Models\BlotterHearingModel;

/**
 * --------------------------------------------------------------------
 * Dashboard
 * --------------------------------------------------------------------
 * Handles controller operations and data logic for the application.
 */
class Dashboard extends BaseController
{
    /**
     * Execute index functionality.
     *
     * @return mixed
     */
    public function index()
    {
        $role = strtolower((string) (session()->get('role') ?? ''));
        if (!session()->get('logged_in') || $role !== 'admin') {
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

        // Age distribution for demographics chart
        $ageDistribution = $db->query("
            SELECT 
                SUM(CASE WHEN TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) BETWEEN 0 AND 17 THEN 1 ELSE 0 END) as 'minors',
                SUM(CASE WHEN TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) BETWEEN 18 AND 30 THEN 1 ELSE 0 END) as 'young_adults',
                SUM(CASE WHEN TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) BETWEEN 31 AND 45 THEN 1 ELSE 0 END) as 'adults',
                SUM(CASE WHEN TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) BETWEEN 46 AND 59 THEN 1 ELSE 0 END) as 'middle_aged',
                SUM(CASE WHEN TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) >= 60 THEN 1 ELSE 0 END) as 'seniors'
            FROM residents WHERE deleted_at IS NULL AND birthdate IS NOT NULL
        ")->getRowArray();

        // --- Portal Usage Stats (NEW) ---
        $thisMonth = date('Y-m');
        try {
            $portalActiveAccounts  = $db->table('resident_accounts')->where('status', 'active')->countAllResults();
            $portalPendingAccounts = $db->table('resident_accounts')->where('status', 'pending')->countAllResults();
            $portalCertsThisMonth  = $db->table('certificate_requests')
                ->where('DATE_FORMAT(created_at, "%Y-%m")', $thisMonth)
                ->countAllResults();
            $portalBlottersThisMonth = $db->table('blotter_records')
                ->where('source', 'Online')
                ->where('DATE_FORMAT(created_at, "%Y-%m")', $thisMonth)
                ->countAllResults();
            $portalBookingsThisMonth = $db->table('facility_bookings')
                ->where('DATE_FORMAT(created_at, "%Y-%m")', $thisMonth)
                ->countAllResults();
        } catch (\Throwable $e) {
            $portalActiveAccounts = $portalPendingAccounts = $portalCertsThisMonth = $portalBlottersThisMonth = $portalBookingsThisMonth = 0;
        }

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
            'ageDistribution'  => $ageDistribution,
            // portal usage stats
            'portalActiveAccounts'   => $portalActiveAccounts,
            'portalPendingAccounts'  => $portalPendingAccounts,
            'portalCertsThisMonth'   => $portalCertsThisMonth,
            'portalBlottersThisMonth'=> $portalBlottersThisMonth,
            'portalBookingsThisMonth'=> $portalBookingsThisMonth,
        ]);
    }

    /**
     * Execute filterCases functionality.
     *
     * @return mixed
     */
    public function filterCases()
    {
        $range = $this->request->getGet('range') ?? 'month';
        $db = \Config\Database::connect();
        
        $openCasesQuery = $db->table('blotter_records')->whereIn('status', ['Pending','Investigating','Ongoing','For Hearing']);
        $settledQuery = $db->table('blotter_records')->where('status', 'Settled');
        $blotterQuery = $db->table('blotter_records');
        $hearingsQuery = $db->table('blotter_hearings');
        
        $today = date('Y-m-d');
        
        if ($range === 'month') {
            $month = date('m');
            $year = date('Y');
            $openCasesQuery->where('MONTH(created_at)', $month)->where('YEAR(created_at)', $year);
            $settledQuery->where('MONTH(updated_at)', $month)->where('YEAR(updated_at)', $year);
            $blotterQuery->where('MONTH(created_at)', $month)->where('YEAR(created_at)', $year);
            $hearingsQuery->where('MONTH(hearing_date)', $month)->where('YEAR(hearing_date)', $year);
        } elseif ($range === 'year') {
            $year = date('Y');
            $openCasesQuery->where('YEAR(created_at)', $year);
            $settledQuery->where('YEAR(updated_at)', $year);
            $blotterQuery->where('YEAR(created_at)', $year);
            $hearingsQuery->where('YEAR(hearing_date)', $year);
        }
        
        // Return JSON
        return $this->response->setJSON([
            'openCases' => $openCasesQuery->countAllResults(),
            'settledThisMonth' => $settledQuery->countAllResults(),
            'hearingsToday' => $hearingsQuery->where('hearing_date', $today)->countAllResults(),
            'blotterCount' => $blotterQuery->countAllResults()
        ]);
    }
}
<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ResidentModel;
use App\Models\HouseholdModel;
use App\Models\LogModel;
use App\Models\BlotterModel;
use App\Models\CertificateModel;

/**
 * Admin Dashboard Controller
 *
 * Displays aggregated statistics, charts, and recent activity
 * for the administrator's dashboard.
 *
 * @package App\Controllers\Admin
 */
class Dashboard extends BaseController
{
    /**
     * Dashboard main page
     *
     * Requires admin authentication.
     * Collects data from multiple models and database tables,
     * then renders the admin dashboard view.
     *
     * @return \CodeIgniter\HTTP\Response|string
     */
    public function index()
    {
        // ─────────────────────────────────────────────────────
        // 1. Authentication Check
        // ─────────────────────────────────────────────────────
        if (! session()->get('logged_in') || session()->get('role') !== 'admin') {
            return redirect()->to('/login');
        }

        // ─────────────────────────────────────────────────────
        // 2. Load database connection and models
        // ─────────────────────────────────────────────────────
        $db             = \Config\Database::connect();
        $residentModel  = new ResidentModel();
        $householdModel = new HouseholdModel();
        $logModel       = new LogModel();
        $blotterModel   = new BlotterModel();
        $certModel      = new CertificateModel();

        // ─────────────────────────────────────────────────────
        // 3. Primary Statistics
        // ─────────────────────────────────────────────────────
        
        // Total residents (excluding soft-deleted)
        $totalResidents = $db->table('residents')
            ->where('deleted_at', null)
            ->countAllResults();

        // Total households
        $totalHouseholds = $db->table('households')
            ->countAllResults();

        // Total certificates issued (all time)
        $totalCerts = $certModel->countAll();

        // Certificates issued today
        $today      = date('Y-m-d');
        $dailyCerts = $certModel->where('DATE(created_at)', $today)->countAllResults();

        // Total blotter/incident records (using the correct table name)
        $blotterCount = $blotterModel->countAll();

        // ─────────────────────────────────────────────────────
        // 4. Secondary Statistics (demographic breakdowns)
        // ─────────────────────────────────────────────────────
        
        // Registered voters
        $totalVoters = $db->table('residents')
            ->where('deleted_at', null)
            ->where('is_voter', 1)
            ->countAllResults();

        // Persons with disabilities
        $totalPwd = $db->table('residents')
            ->where('deleted_at', null)
            ->where('is_pwd', 1)
            ->countAllResults();

        // Senior citizens
        $totalSenior = $db->table('residents')
            ->where('deleted_at', null)
            ->where('is_senior_citizen', 1)
            ->countAllResults();

        // Gender counts
        $totalMale   = $db->table('residents')
            ->where('deleted_at', null)
            ->where('sex', 'male')
            ->countAllResults();
        
        $totalFemale = $db->table('residents')
            ->where('deleted_at', null)
            ->where('sex', 'female')
            ->countAllResults();

        // Average household size
        $avgPerHousehold = $totalHouseholds > 0
            ? round($totalResidents / $totalHouseholds, 1)
            : 0;

        // ─────────────────────────────────────────────────────
        // 5. Chart Data
        // ─────────────────────────────────────────────────────
        
        // Residents per Purok (sítio) for bar chart
        $purokCounts = $db->table('residents')
            ->select("COALESCE(NULLIF(sitio,''), 'Unassigned') as sitio, COUNT(*) as count")
            ->where('deleted_at', null)
            ->groupBy('sitio')
            ->orderBy('count', 'DESC')
            ->get()
            ->getResultArray();

        // Civil status distribution for pie chart
        $civilStatusData = $db->table('residents')
            ->select("COALESCE(NULLIF(civil_status,''), 'Unknown') as civil_status, COUNT(*) as count")
            ->where('deleted_at', null)
            ->groupBy('civil_status')
            ->orderBy('count', 'DESC')
            ->get()
            ->getResultArray();

        // ─────────────────────────────────────────────────────
        // 6. System Accounts (admin only stats)
        // ─────────────────────────────────────────────────────
        
        $totalStaff  = $db->table('users')
            ->where('role', 'staff')
            ->countAllResults();

        $totalAdmins = $db->table('users')
            ->where('role', 'admin')
            ->countAllResults();

        // ─────────────────────────────────────────────────────
        // 7. Recent Activity Logs
        // ─────────────────────────────────────────────────────
        
        // Fetch the 10 most recent system logs
        $recentLogs = $logModel
            ->orderBy('DATELOG DESC, TIMELOG DESC')
            ->limit(10)
            ->findAll();

        // ─────────────────────────────────────────────────────
        // 8. Latest Blotter Entries (for optional widget)
        // ─────────────────────────────────────────────────────
        
        $latestBlotters = $blotterModel
            ->orderBy('incident_date', 'DESC')
            ->limit(5)
            ->findAll();

        // ─────────────────────────────────────────────────────
        // 9. Load the dashboard view with all collected data
        // ─────────────────────────────────────────────────────
        
        return view('Admin/dashboard', [
            'title'           => 'Admin Dashboard',
            'totalResidents'  => $totalResidents,
            'totalHouseholds' => $totalHouseholds,
            'pendingCerts'    => $totalCerts,     // now total certificates
            'dailyCerts'      => $dailyCerts,     // issued today
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
            'latestCerts'     => [],
            'recentLogs'      => $recentLogs,
        ]);
    }
}
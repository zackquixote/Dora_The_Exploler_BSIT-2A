<?php

namespace App\Services;

use App\Models\ResidentModel;
use App\Models\BlotterModel;
use App\Models\CertificateModel;
use App\Models\PaymentModel;
use CodeIgniter\I18n\Time;

/**
 * Advanced Analytics Service
 * Provides comprehensive reporting and predictive analytics
 */
class AnalyticsService
{
    protected ResidentModel $residentModel;
    protected BlotterModel $blotterModel;
    protected CertificateModel $certificateModel;
    protected PaymentModel $paymentModel;

    public function __construct()
    {
        $this->residentModel = new ResidentModel();
        $this->blotterModel = new BlotterModel();
        $this->certificateModel = new CertificateModel();
        $this->paymentModel = new PaymentModel();
    }

    /**
     * Get comprehensive dashboard analytics
     */
    public function getDashboardAnalytics(): array
    {
        return [
            'population' => $this->getPopulationAnalytics(),
            'demographics' => $this->getDemographicAnalytics(),
            'certificates' => $this->getCertificateAnalytics(),
            'blotter' => $this->getBlotterAnalytics(),
            'revenue' => $this->getRevenueAnalytics(),
            'trends' => $this->getTrendAnalytics(),
            'modules' => $this->getModuleKpis(),
        ];
    }

    /**
     * Phase 3.1 - Dashboards/KPIs across modules (lightweight aggregates)
     */
    public function getModuleKpis(): array
    {
        $db = \Config\Database::connect();

        return [
            'business_permits' => $this->getBusinessPermitKpis($db),
            'events'           => $this->getEventKpis($db),
            'health'           => $this->getHealthKpis($db),
            'documents'        => $this->getDocumentKpis($db),
            'generated_at'     => date('Y-m-d H:i:s'),
        ];
    }

    private function getBusinessPermitKpis($db): array
    {
        $year = (int) date('Y');

        if (! $db->tableExists('permit_renewals')) {
            return [
                'available' => false,
                'message'   => 'permit_renewals table not found',
            ];
        }

        $byStatus = $db->query("
            SELECT status, COUNT(*) AS count
            FROM permit_renewals
            WHERE renewal_year = ?
            GROUP BY status
        ", [$year])->getResultArray();

        $statusMap = [
            'pending'  => 0,
            'paid'     => 0,
            'approved' => 0,
            'printed'  => 0,
        ];
        foreach ($byStatus as $row) {
            $statusMap[$row['status']] = (int) $row['count'];
        }

        $avgApprovalDays = $db->query("
            SELECT AVG(TIMESTAMPDIFF(HOUR, created_at, approved_at)) / 24 AS avg_days
            FROM permit_renewals
            WHERE renewal_year = ?
              AND approved_at IS NOT NULL
        ", [$year])->getRowArray();

        $avgPrintDays = $db->query("
            SELECT AVG(TIMESTAMPDIFF(HOUR, created_at, printed_at)) / 24 AS avg_days
            FROM permit_renewals
            WHERE renewal_year = ?
              AND printed_at IS NOT NULL
        ", [$year])->getRowArray();

        return [
            'available'            => true,
            'year'                 => $year,
            'counts'               => $statusMap,
            'total'                => array_sum($statusMap),
            'avg_days_to_approval' => isset($avgApprovalDays['avg_days']) ? round((float) $avgApprovalDays['avg_days'], 2) : null,
            'avg_days_to_print'    => isset($avgPrintDays['avg_days']) ? round((float) $avgPrintDays['avg_days'], 2) : null,
        ];
    }

    private function getEventKpis($db): array
    {
        if (! $db->tableExists('events') || ! $db->tableExists('event_participants')) {
            return [
                'available' => false,
                'message'   => 'events/event_participants tables not found',
            ];
        }

        $upcoming = $db->query("
            SELECT COUNT(*) AS count
            FROM events
            WHERE start_date >= NOW()
              AND status != 'cancelled'
        ")->getRowArray();

        $last30 = $db->query("
            SELECT
              COUNT(ep.id) AS total_registered,
              SUM(CASE WHEN ep.attendance_status = 'attended' THEN 1 ELSE 0 END) AS attended
            FROM event_participants ep
            JOIN events e ON e.id = ep.event_id
            WHERE e.start_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        ")->getRowArray();

        $registered = (int) ($last30['total_registered'] ?? 0);
        $attended   = (int) ($last30['attended'] ?? 0);

        return [
            'available'              => true,
            'upcoming_events'        => (int) ($upcoming['count'] ?? 0),
            'last_30_days_registered' => $registered,
            'last_30_days_attended'   => $attended,
            'attendance_rate'        => $registered > 0 ? round(($attended / $registered) * 100, 2) : 0,
        ];
    }

    private function getHealthKpis($db): array
    {
        if (! $db->tableExists('health_records')) {
            return [
                'available' => false,
                'message'   => 'health_records table not found',
            ];
        }

        $total = $db->query("SELECT COUNT(*) AS c FROM health_records")->getRowArray();
        $withVacc = $db->query("
            SELECT COUNT(*) AS c
            FROM health_records
            WHERE vaccination_records IS NOT NULL
              AND vaccination_records != ''
              AND vaccination_records != '[]'
        ")->getRowArray();

        $recentCheckups = $db->query("
            SELECT COUNT(*) AS c
            FROM health_records
            WHERE last_checkup_date IS NOT NULL
              AND last_checkup_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
        ")->getRowArray();

        $blood = $db->query("
            SELECT blood_type, COUNT(*) AS count
            FROM health_records
            WHERE blood_type IS NOT NULL AND blood_type != ''
            GROUP BY blood_type
            ORDER BY count DESC
        ")->getResultArray();

        return [
            'available'                => true,
            'total_records'            => (int) ($total['c'] ?? 0),
            'with_vaccination_records' => (int) ($withVacc['c'] ?? 0),
            'recent_checkups_30d'      => (int) ($recentCheckups['c'] ?? 0),
            'blood_type_distribution'  => $blood,
        ];
    }

    private function getDocumentKpis($db): array
    {
        if (! $db->tableExists('documents')) {
            return [
                'available' => false,
                'message'   => 'documents table not found',
            ];
        }

        $totals = $db->query("
            SELECT
              COUNT(*) AS total_rows,
              SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) AS active_rows,
              SUM(CASE WHEN is_active = 1 THEN file_size ELSE 0 END) AS active_bytes
            FROM documents
        ")->getRowArray();

        $byAccess = $db->query("
            SELECT access_level, COUNT(*) AS count
            FROM documents
            WHERE is_active = 1
            GROUP BY access_level
        ")->getResultArray();

        $last7 = $db->query("
            SELECT COUNT(*) AS c
            FROM documents
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
        ")->getRowArray();

        return [
            'available'        => true,
            'total_files'      => (int) ($totals['total_rows'] ?? 0),
            'active_files'     => (int) ($totals['active_rows'] ?? 0),
            'active_size_bytes'=> (int) ($totals['active_bytes'] ?? 0),
            'uploads_last_7d'  => (int) ($last7['c'] ?? 0),
            'by_access_level'  => $byAccess,
        ];
    }

    /**
     * Population analytics
     */
    public function getPopulationAnalytics(): array
    {
        $db = \Config\Database::connect();
        
        // Total population
        $totalPopulation = $this->residentModel->where('status', 'active')->countAllResults();
        
        // Population by sitio
        $populationBySitio = $db->query("
            SELECT sitio, COUNT(*) as count 
            FROM residents 
            WHERE status = 'active' AND sitio IS NOT NULL 
            GROUP BY sitio
        ")->getResultArray();

        // Age distribution
        $ageDistribution = $db->query("
            SELECT 
                CASE 
                    WHEN TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) < 18 THEN 'Minor (0-17)'
                    WHEN TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) BETWEEN 18 AND 35 THEN 'Young Adult (18-35)'
                    WHEN TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) BETWEEN 36 AND 59 THEN 'Adult (36-59)'
                    ELSE 'Senior (60+)'
                END as age_group,
                COUNT(*) as count
            FROM residents 
            WHERE status = 'active' AND birthdate IS NOT NULL
            GROUP BY age_group
        ")->getResultArray();

        // Gender distribution
        $genderDistribution = $db->query("
            SELECT sex, COUNT(*) as count 
            FROM residents 
            WHERE status = 'active' 
            GROUP BY sex
        ")->getResultArray();

        // Population growth (last 12 months)
        $populationGrowth = $db->query("
            SELECT 
                DATE_FORMAT(created_at, '%Y-%m') as month,
                COUNT(*) as new_residents
            FROM residents 
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
            AND status = 'active'
            GROUP BY DATE_FORMAT(created_at, '%Y-%m')
            ORDER BY month
        ")->getResultArray();

        return [
            'total_population' => $totalPopulation,
            'by_sitio' => $populationBySitio,
            'age_distribution' => $ageDistribution,
            'gender_distribution' => $genderDistribution,
            'growth_trend' => $populationGrowth,
        ];
    }

    /**
     * Demographic analytics
     */
    public function getDemographicAnalytics(): array
    {
        $db = \Config\Database::connect();

        // Civil status distribution
        $civilStatus = $db->query("
            SELECT civil_status, COUNT(*) as count 
            FROM residents 
            WHERE status = 'active' 
            GROUP BY civil_status
        ")->getResultArray();

        // Occupation distribution
        $occupations = $db->query("
            SELECT occupation, COUNT(*) as count 
            FROM residents 
            WHERE status = 'active' AND occupation IS NOT NULL AND occupation != ''
            GROUP BY occupation
            ORDER BY count DESC
            LIMIT 10
        ")->getResultArray();

        // Voter statistics
        $voterStats = $db->query("
            SELECT 
                SUM(CASE WHEN is_voter = 1 THEN 1 ELSE 0 END) as registered_voters,
                SUM(CASE WHEN is_voter = 0 THEN 1 ELSE 0 END) as non_voters,
                COUNT(*) as total_eligible
            FROM residents 
            WHERE status = 'active' 
            AND TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) >= 18
        ")->getRowArray();

        // PWD and Senior Citizen statistics
        $specialGroups = $db->query("
            SELECT 
                SUM(CASE WHEN is_pwd = 1 THEN 1 ELSE 0 END) as pwd_count,
                SUM(CASE WHEN is_senior_citizen = 1 THEN 1 ELSE 0 END) as senior_count,
                SUM(CASE WHEN TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) < 18 THEN 1 ELSE 0 END) as minor_count
            FROM residents 
            WHERE status = 'active'
        ")->getRowArray();

        return [
            'civil_status' => $civilStatus,
            'top_occupations' => $occupations,
            'voter_statistics' => $voterStats,
            'special_groups' => $specialGroups,
        ];
    }

    /**
     * Certificate analytics
     */
    public function getCertificateAnalytics(): array
    {
        $db = \Config\Database::connect();

        // Certificates issued this month vs last month
        $thisMonth = $db->query("
            SELECT COUNT(*) as count 
            FROM certificates 
            WHERE MONTH(created_at) = MONTH(NOW()) 
            AND YEAR(created_at) = YEAR(NOW())
        ")->getRowArray()['count'];

        $lastMonth = $db->query("
            SELECT COUNT(*) as count 
            FROM certificates 
            WHERE MONTH(created_at) = MONTH(DATE_SUB(NOW(), INTERVAL 1 MONTH))
            AND YEAR(created_at) = YEAR(DATE_SUB(NOW(), INTERVAL 1 MONTH))
        ")->getRowArray()['count'];

        // Certificate types distribution
        $certificateTypes = $db->query("
            SELECT ct.name as type_name, COUNT(c.id) as count
            FROM certificate_types ct
            LEFT JOIN certificates c ON ct.name = c.certificate_type
            WHERE c.created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
            GROUP BY ct.id, ct.name
            ORDER BY count DESC
        ")->getResultArray();

        // Monthly certificate trends
        $monthlyTrends = $db->query("
            SELECT 
                DATE_FORMAT(created_at, '%Y-%m') as month,
                COUNT(*) as count
            FROM certificates 
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
            GROUP BY DATE_FORMAT(created_at, '%Y-%m')
            ORDER BY month
        ")->getResultArray();

        // Peak hours for certificate requests
        $peakHours = $db->query("
            SELECT 
                HOUR(created_at) as hour,
                COUNT(*) as count
            FROM certificates 
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
            GROUP BY HOUR(created_at)
            ORDER BY count DESC
        ")->getResultArray();

        return [
            'this_month' => $thisMonth,
            'last_month' => $lastMonth,
            'growth_rate' => $lastMonth > 0 ? round((($thisMonth - $lastMonth) / $lastMonth) * 100, 2) : 0,
            'by_type' => $certificateTypes,
            'monthly_trends' => $monthlyTrends,
            'peak_hours' => $peakHours,
        ];
    }

    /**
     * Blotter/Crime analytics
     */
    public function getBlotterAnalytics(): array
    {
        $db = \Config\Database::connect();

        // Cases by status
        $casesByStatus = $db->query("
            SELECT status, COUNT(*) as count 
            FROM blotter_records 
            GROUP BY status
        ")->getResultArray();

        // Incident types
        $incidentTypes = $db->query("
            SELECT incident_type, COUNT(*) as count 
            FROM blotter_records 
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
            GROUP BY incident_type
            ORDER BY count DESC
        ")->getResultArray();

        // Monthly crime trends
        $monthlyTrends = $db->query("
            SELECT 
                DATE_FORMAT(created_at, '%Y-%m') as month,
                COUNT(*) as count
            FROM blotter_records 
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
            GROUP BY DATE_FORMAT(created_at, '%Y-%m')
            ORDER BY month
        ")->getResultArray();

        // Crime hotspots by purok
        $hotspots = $db->query("
            SELECT purok, COUNT(*) as count 
            FROM blotter_records 
            WHERE purok IS NOT NULL 
            AND created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
            GROUP BY purok
            ORDER BY count DESC
        ")->getResultArray();

        // Resolution time analysis
        $resolutionTimes = $db->query("
            SELECT 
                AVG(DATEDIFF(updated_at, created_at)) as avg_days,
                MIN(DATEDIFF(updated_at, created_at)) as min_days,
                MAX(DATEDIFF(updated_at, created_at)) as max_days
            FROM blotter_records 
            WHERE status IN ('Settled', 'Dismissed')
            AND updated_at > created_at
        ")->getRowArray();

        return [
            'by_status' => $casesByStatus,
            'incident_types' => $incidentTypes,
            'monthly_trends' => $monthlyTrends,
            'hotspots' => $hotspots,
            'resolution_times' => $resolutionTimes,
        ];
    }

    /**
     * Revenue analytics
     */
    public function getRevenueAnalytics(): array
    {
        $db = \Config\Database::connect();

        // Monthly revenue
        $monthlyRevenue = $db->query("
            SELECT 
                DATE_FORMAT(created_at, '%Y-%m') as month,
                SUM(amount) as total_amount,
                COUNT(*) as transaction_count
            FROM payments 
            WHERE status = 'completed'
            AND created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
            GROUP BY DATE_FORMAT(created_at, '%Y-%m')
            ORDER BY month
        ")->getResultArray();

        // Revenue by service type
        $revenueByService = $db->query("
            SELECT 
                service_type,
                SUM(amount) as total_amount,
                COUNT(*) as transaction_count,
                AVG(amount) as avg_amount
            FROM payments 
            WHERE status = 'completed'
            AND created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
            GROUP BY service_type
            ORDER BY total_amount DESC
        ")->getResultArray();

        // Payment methods distribution
        $paymentMethods = $db->query("
            SELECT 
                payment_method,
                COUNT(*) as count,
                SUM(amount) as total_amount
            FROM payments 
            WHERE status = 'completed'
            AND created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
            GROUP BY payment_method
        ")->getResultArray();

        return [
            'monthly_revenue' => $monthlyRevenue,
            'by_service' => $revenueByService,
            'payment_methods' => $paymentMethods,
        ];
    }

    /**
     * Trend analytics and predictions
     */
    public function getTrendAnalytics(): array
    {
        $db = \Config\Database::connect();

        // Population growth prediction (simple linear regression)
        $populationData = $db->query("
            SELECT 
                DATE_FORMAT(created_at, '%Y-%m') as month,
                COUNT(*) as new_residents
            FROM residents 
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 24 MONTH)
            GROUP BY DATE_FORMAT(created_at, '%Y-%m')
            ORDER BY month
        ")->getResultArray();

        $populationPrediction = $this->calculateLinearTrend($populationData, 'new_residents');

        // Certificate demand prediction
        $certificateData = $db->query("
            SELECT 
                DATE_FORMAT(created_at, '%Y-%m') as month,
                COUNT(*) as certificates_issued
            FROM certificates 
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 24 MONTH)
            GROUP BY DATE_FORMAT(created_at, '%Y-%m')
            ORDER BY month
        ")->getResultArray();

        $certificatePrediction = $this->calculateLinearTrend($certificateData, 'certificates_issued');

        // Seasonal patterns
        $seasonalPatterns = $db->query("
            SELECT 
                MONTH(created_at) as month,
                AVG(monthly_count) as avg_count
            FROM (
                SELECT 
                    created_at,
                    COUNT(*) as monthly_count
                FROM certificates 
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL 24 MONTH)
                GROUP BY DATE_FORMAT(created_at, '%Y-%m')
            ) as monthly_data
            GROUP BY MONTH(created_at)
            ORDER BY month
        ")->getResultArray();

        return [
            'population_prediction' => $populationPrediction,
            'certificate_prediction' => $certificatePrediction,
            'seasonal_patterns' => $seasonalPatterns,
        ];
    }

    /**
     * Calculate linear trend for predictions
     */
    protected function calculateLinearTrend(array $data, string $valueField): array
    {
        if (count($data) < 2) {
            return ['trend' => 'insufficient_data', 'predictions' => [0, 0, 0]];
        }

        $n = count($data);
        $sumX = 0;
        $sumY = 0;
        $sumXY = 0;
        $sumX2 = 0;

        foreach ($data as $i => $point) {
            $x = $i + 1; // Time index
            $y = (float) $point[$valueField];
            
            $sumX += $x;
            $sumY += $y;
            $sumXY += $x * $y;
            $sumX2 += $x * $x;
        }

        // Calculate slope and intercept
        $slope = ($n * $sumXY - $sumX * $sumY) / ($n * $sumX2 - $sumX * $sumX);
        $intercept = ($sumY - $slope * $sumX) / $n;

        // Predict next 3 months
        $predictions = [];
        for ($i = 1; $i <= 3; $i++) {
            $nextX = $n + $i;
            $prediction = $slope * $nextX + $intercept;
            $predictions[] = max(0, round($prediction)); // Ensure non-negative
        }

        return [
            'trend' => $slope > 0 ? 'increasing' : ($slope < 0 ? 'decreasing' : 'stable'),
            'slope' => round($slope, 4),
            'predictions' => $predictions,
        ];
    }

    /**
     * Generate comprehensive report
     */
    public function generateReport(string $reportType, array $dateRange = [], array $filters = []): array
    {
        switch ($reportType) {
            case 'population':
                return $this->generatePopulationReport($dateRange, $filters);
            case 'certificates':
                return $this->generateCertificateReport($dateRange, $filters);
            case 'blotter':
                return $this->generateBlotterReport($dateRange, $filters);
            case 'revenue':
                return $this->generateRevenueReport($dateRange, $filters);
            default:
                throw new \Exception('Unknown report type');
        }
    }

    /**
     * Generate population report
     */
    protected function generatePopulationReport(array $dateRange, array $filters): array
    {
        // Implementation for detailed population report
        return [
            'report_type' => 'population',
            'generated_at' => date('Y-m-d H:i:s'),
            'data' => $this->getPopulationAnalytics(),
        ];
    }

    /**
     * Export analytics data to various formats
     */
    public function exportData(string $format, array $data, string $filename): string
    {
        switch ($format) {
            case 'csv':
                return $this->exportToCSV($data, $filename);
            case 'excel':
                return $this->exportToExcel($data, $filename);
            case 'pdf':
                return $this->exportToPDF($data, $filename);
            default:
                throw new \Exception('Unsupported export format');
        }
    }

    /**
     * Export to CSV
     */
    protected function exportToCSV(array $data, string $filename): string
    {
        $filepath = WRITEPATH . 'uploads/exports/' . $filename . '.csv';
        
        if (!is_dir(dirname($filepath))) {
            mkdir(dirname($filepath), 0755, true);
        }

        $file = fopen($filepath, 'w');
        
        // Write headers
        if (!empty($data)) {
            fputcsv($file, array_keys($data[0]));
            
            // Write data
            foreach ($data as $row) {
                fputcsv($file, $row);
            }
        }
        
        fclose($file);
        
        return $filepath;
    }
}

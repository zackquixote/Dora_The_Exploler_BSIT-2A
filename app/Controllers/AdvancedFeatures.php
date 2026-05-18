<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Services\NotificationService;
use App\Services\QRCodeService;
use App\Services\AnalyticsService;
use App\Services\EmergencyService;
use App\Services\BusinessService;
use App\Services\EventService;
use App\Services\GmailService;
use App\Models\DocumentModel;
use App\Models\HealthRecordModel;

/**
 * Advanced Features Controller
 * Handles all the new advanced features and services
 */
class AdvancedFeatures extends BaseController
{
    protected NotificationService $notificationService;
    protected QRCodeService $qrService;
    protected AnalyticsService $analyticsService;
    protected EmergencyService $emergencyService;
    protected BusinessService $businessService;
    protected EventService $eventService;
    protected DocumentModel $documentModel;
    protected HealthRecordModel $healthModel;
    protected GmailService $gmailService;

    public function __construct()
    {
        $this->notificationService = new NotificationService();
        $this->qrService = new QRCodeService();
        $this->analyticsService = new AnalyticsService();
        $this->emergencyService = new EmergencyService();
        $this->businessService = new BusinessService();
        $this->eventService = new EventService();
        $this->documentModel = new DocumentModel();
        $this->healthModel = new HealthRecordModel();
        $this->gmailService = new GmailService();
    }

    /**
     * Advanced Analytics Dashboard
     */
    public function analytics()
    {
        $data = [
            'title' => 'Advanced Analytics',
            'analytics' => $this->analyticsService->getDashboardAnalytics(),
        ];

        return view('advanced/analytics', $data);
    }

    /**
     * Phase 3.1 KPI endpoint for the analytics page (AJAX).
     * GET /advanced/api/analytics/kpis
     */
    public function apiAnalyticsKpis()
    {
        try {
            $kpis = $this->analyticsService->getModuleKpis();
            return $this->jsonSuccess($kpis);
        } catch (\Throwable $e) {
            return $this->jsonError('Failed to load KPIs', 500);
        }
    }

    public function gmail()
    {
        $data = [
            'title'            => 'Gmail Integration',
            'gmail_configured' => $this->gmailService->isConfigured(),
            'gmail_authorized' => $this->gmailService->isAuthorized(),
        ];

        return view('advanced/gmail', $data);
    }

    public function gmailConnect()
    {
        if (! $this->gmailService->isConfigured()) {
            return redirect()->to('/advanced/gmail')->with('error', 'Gmail credentials file not found.');
        }

        try {
            return redirect()->to($this->gmailService->getAuthorizationUrl());
        } catch (\Throwable $e) {
            return redirect()->to('/advanced/gmail')->with('error', $e->getMessage());
        }
    }

    public function gmailCallback()
    {
        $code = (string) ($this->request->getGet('code') ?? '');
        if ($code === '') {
            return redirect()->to('/advanced/gmail')->with('error', 'Missing OAuth code.');
        }

        try {
            $this->gmailService->handleOAuthCallback($code);
            return redirect()->to('/advanced/gmail')->with('success', 'Gmail connected successfully.');
        } catch (\Throwable $e) {
            return redirect()->to('/advanced/gmail')->with('error', $e->getMessage());
        }
    }

    public function testNotifications()
    {
        $data = [
            'title' => 'Test Notifications',
        ];

        return view('advanced/test_notifications', $data);
    }

    public function sendTestNotifications()
    {
        if ($this->request->getMethod() !== 'post') {
            return redirect()->to('/advanced/test-notifications');
        }

        $title = (string) ($this->request->getPost('title') ?? 'Test Notification');
        $message = (string) ($this->request->getPost('message') ?? '');
        $phone = (string) ($this->request->getPost('phone') ?? '');
        $email = (string) ($this->request->getPost('email') ?? '');
        $channels = $this->request->getPost('channels') ?? [];
        if (! is_array($channels)) {
            $channels = [$channels];
        }

        if (trim($message) === '') {
            return redirect()->to('/advanced/test-notifications')->with('error', 'Message is required.');
        }

        $results = [];
        $ok = false;

        if (in_array('sms', $channels, true)) {
            $sms = $this->notificationService->sendDirectSmsDetailed($phone, $message);
            $sent = (bool) ($sms['success'] ?? false);
            $provider = (string) ($sms['provider'] ?? 'sms');
            $status = (string) ($sms['provider_status'] ?? ($sent ? 'sent' : 'failed'));
            $code = $sms['http_code'] ?? null;
            $num = (string) ($sms['number'] ?? '');
            $meta = $provider . ':' . $status . ($code ? ('(' . $code . ')') : '');
            $results[] = 'SMS [' . $meta . '] to ' . $num . ': ' . ($sent ? 'sent' : 'failed');
            $ok = $ok || $sent;
        }

        if (in_array('email', $channels, true)) {
            $sent = $this->notificationService->sendDirectEmail($email, $title, $message);
            $results[] = 'Email: ' . ($sent ? 'sent' : 'failed');
            $ok = $ok || $sent;
        }

        if ($results === []) {
            return redirect()->to('/advanced/test-notifications')->with('error', 'Select at least one channel.');
        }

        return redirect()
            ->to('/advanced/test-notifications')
            ->with($ok ? 'success' : 'error', implode(' | ', $results));
    }

    /**
     * Notification Center
     */
    public function notifications()
    {
        $data = [
            'title' => 'Notification Center',
            'notifications' => [], // Get user notifications
        ];

        return view('advanced/notifications', $data);
    }

    /**
     * Send bulk notification
     */
    public function sendBulkNotification()
    {
        if ($this->request->getMethod() === 'POST') {
            $recipients = $this->request->getPost('recipients');
            $type = $this->request->getPost('type');
            $title = $this->request->getPost('title');
            $message = $this->request->getPost('message');
            $channels = $this->request->getPost('channels') ?? ['sms'];

            try {
                if ($recipients === 'all') {
                    // Send to all active residents
                    $this->notificationService->sendToGroup(
                        ['status' => 'active'],
                        $type,
                        $title,
                        $message,
                        $channels
                    );
                } else {
                    // Send to specific residents
                    $residentIds = explode(',', $recipients);
                    $this->notificationService->sendBulk(
                        $residentIds,
                        $type,
                        $title,
                        $message,
                        $channels
                    );
                }

                return $this->response->setJSON(['success' => true, 'message' => 'Notifications sent successfully']);
            } catch (\Exception $e) {
                return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()]);
            }
        }

        return view('advanced/send_notification');
    }

    /**
     * QR Code Generator
     */
    public function qrGenerator()
    {
        if ($this->request->getMethod() === 'POST') {
            $type = $this->request->getPost('type');
            $id = $this->request->getPost('id');

            try {
                if ($type === 'certificate') {
                    $qrData = $this->qrService->generateCertificateQR($id);
                } elseif ($type === 'resident') {
                    $qrData = $this->qrService->generateResidentQR($id);
                } else {
                    throw new \Exception('Invalid QR type');
                }

                $qrImage = $this->qrService->generateQRImage($qrData['qr_data']);

                return $this->response->setJSON([
                    'success' => true,
                    'qr_image' => $qrImage,
                    'qr_data' => $qrData,
                ]);
            } catch (\Exception $e) {
                return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()]);
            }
        }

        return view('advanced/qr_generator');
    }

    /**
     * QR Code Verification
     */
    public function verifyQR($type, $id, $token)
    {
        try {
            $verification = $this->qrService->verifyToken($type, $id, $token);
            $this->qrService->logVerification($type, $id, $token, $verification['valid'], $this->request->getIPAddress());

            $data = [
                'title' => 'QR Code Verification',
                'verification' => $verification,
                'type' => $type,
            ];

            return view('advanced/qr_verification', $data);
        } catch (\Exception $e) {
            return view('errors/html/error_404');
        }
    }

    /**
     * Emergency Response Dashboard
     */
    public function emergency()
    {
        $data = [
            'title' => 'Emergency Response',
            'active_incidents' => $this->emergencyService->getEmergencyStats(),
            'preparedness_report' => $this->emergencyService->generatePreparednessReport(),
        ];

        return view('advanced/emergency', $data);
    }

    /**
     * Report Emergency Incident
     */
    public function reportEmergency()
    {
        if ($this->request->getMethod() === 'POST') {
            $incidentData = [
                'emergency_type' => $this->request->getPost('emergency_type'),
                'severity_level' => $this->request->getPost('severity_level'),
                'location' => $this->request->getPost('location'),
                'description' => $this->request->getPost('description'),
                'reporter_name' => $this->request->getPost('reporter_name'),
                'reporter_contact' => $this->request->getPost('reporter_contact'),
                'affected_residents' => json_encode($this->request->getPost('affected_residents') ?? []),
            ];

            try {
                $incident = $this->emergencyService->reportIncident($incidentData);
                return $this->response->setJSON(['success' => true, 'incident' => $incident]);
            } catch (\Exception $e) {
                return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()]);
            }
        }

        return view('advanced/report_emergency');
    }

    /**
     * Business Management Dashboard
     */
    public function business()
    {
        $data = [
            'title' => 'Business Management',
            'statistics' => $this->businessService->getBusinessStatistics(),
            'businesses' => $this->businessService->generateBusinessDirectory(),
        ];

        return view('advanced/business', $data);
    }

    /**
     * Register New Business
     */
    public function registerBusiness()
    {
        if ($this->request->getMethod() === 'POST') {
            $businessData = [
                'owner_resident_id' => $this->request->getPost('owner_resident_id'),
                'business_name' => $this->request->getPost('business_name'),
                'business_type' => $this->request->getPost('business_type'),
                'business_address' => $this->request->getPost('business_address'),
                'contact_number' => $this->request->getPost('contact_number'),
                'email' => $this->request->getPost('email'),
                'capital_amount' => $this->request->getPost('capital_amount'),
                'employees_count' => $this->request->getPost('employees_count'),
            ];

            try {
                $business = $this->businessService->registerBusiness($businessData);
                return $this->response->setJSON(['success' => true, 'business' => $business]);
            } catch (\Exception $e) {
                return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()]);
            }
        }

        return view('advanced/register_business');
    }

    /**
     * Event Management Dashboard
     */
    public function events()
    {
        $data = [
            'title' => 'Event Management',
            'upcoming_events' => $this->eventService->getUpcomingEvents(),
            'calendar' => $this->eventService->getEventsCalendar(),
        ];

        return view('advanced/events', $data);
    }

    /**
     * Create New Event
     */
    public function createEvent()
    {
        if ($this->request->getMethod() === 'POST') {
            $eventData = [
                'title' => $this->request->getPost('title'),
                'description' => $this->request->getPost('description'),
                'event_type' => $this->request->getPost('event_type'),
                'venue' => $this->request->getPost('venue'),
                'start_date' => $this->request->getPost('start_date'),
                'end_date' => $this->request->getPost('end_date'),
                'max_participants' => $this->request->getPost('max_participants'),
                'registration_required' => $this->request->getPost('registration_required') ? 1 : 0,
                'registration_deadline' => $this->request->getPost('registration_deadline'),
                'target_audience' => json_encode($this->request->getPost('target_audience') ?? []),
                'budget' => $this->request->getPost('budget'),
            ];

            try {
                $event = $this->eventService->createEvent($eventData);
                return $this->response->setJSON(['success' => true, 'event' => $event]);
            } catch (\Exception $e) {
                return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()]);
            }
        }

        return view('advanced/create_event');
    }

    /**
     * Health Records Management
     */
    public function healthRecords()
    {
        $data = [
            'title' => 'Health Records',
            'statistics' => $this->healthModel->getHealthStats(),
        ];

        return view('advanced/health_records', $data);
    }

    /**
     * Document Management
     */
    public function documents()
    {
        $data = [
            'title' => 'Document Management',
            'storage_stats' => $this->documentModel->getStorageStats(),
        ];

        return view('advanced/documents', $data);
    }

    /**
     * Upload Document
     */
    public function uploadDocument()
    {
        if ($this->request->getMethod() === 'POST') {
            $file = $this->request->getFile('document');
            
            if (!$file->isValid()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Invalid file']);
            }

            $documentData = [
                'entity_type' => $this->request->getPost('entity_type'),
                'entity_id' => $this->request->getPost('entity_id'),
                'document_type' => $this->request->getPost('document_type'),
                'access_level' => $this->request->getPost('access_level'),
                'uploaded_by' => session()->get('user_id'),
            ];

            try {
                $document = $this->documentModel->uploadDocument($documentData, $file);
                return $this->response->setJSON(['success' => true, 'document' => $document]);
            } catch (\Exception $e) {
                return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()]);
            }
        }

        return view('advanced/upload_document');
    }

    /**
     * Generate Reports
     */
    public function reports()
    {
        $reportType = $this->request->getGet('type') ?? 'population';
        $dateRange = [
            'start' => $this->request->getGet('start_date') ?? date('Y-m-01'),
            'end' => $this->request->getGet('end_date') ?? date('Y-m-t'),
        ];

        try {
            $report = $this->analyticsService->generateReport($reportType, $dateRange);
            
            $data = [
                'title' => 'Advanced Reports',
                'report' => $report,
                'report_type' => $reportType,
            ];

            return view('advanced/reports', $data);
        } catch (\Exception $e) {
            return view('errors/html/error_500');
        }
    }

    /**
     * Export Data
     */
    public function exportData()
    {
        $format = $this->request->getGet('format') ?? 'csv';
        $type = $this->request->getGet('type') ?? 'residents';
        
        try {
            // Get data based on type
            switch ($type) {
                case 'residents':
                    $data = $this->analyticsService->getPopulationAnalytics();
                    break;
                case 'certificates':
                    $data = $this->analyticsService->getCertificateAnalytics();
                    break;
                case 'business':
                    $data = $this->businessService->getBusinessStatistics();
                    break;
                default:
                    throw new \Exception('Invalid export type');
            }

            $filename = $type . '_export_' . date('Y-m-d_H-i-s');
            $filepath = $this->analyticsService->exportData($format, $data, $filename);

            return $this->response->download($filepath, null);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * System Health Check
     */
    public function systemHealth()
    {
        $health = [
            'database' => $this->checkDatabaseHealth(),
            'storage' => $this->checkStorageHealth(),
            'notifications' => $this->checkNotificationHealth(),
            'services' => $this->checkServicesHealth(),
        ];

        return $this->response->setJSON($health);
    }

    /**
     * Check database health
     */
    protected function checkDatabaseHealth(): array
    {
        try {
            $db = \Config\Database::connect();
            $db->query('SELECT 1');
            return ['status' => 'healthy', 'message' => 'Database connection OK'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Database connection failed'];
        }
    }

    /**
     * Check storage health
     */
    protected function checkStorageHealth(): array
    {
        $uploadPath = WRITEPATH . 'uploads/';
        $freeSpace = disk_free_space($uploadPath);
        $totalSpace = disk_total_space($uploadPath);
        $usedPercentage = (($totalSpace - $freeSpace) / $totalSpace) * 100;

        if ($usedPercentage > 90) {
            return ['status' => 'warning', 'message' => 'Storage usage above 90%'];
        } elseif ($usedPercentage > 95) {
            return ['status' => 'error', 'message' => 'Storage usage critical'];
        }

        return ['status' => 'healthy', 'message' => 'Storage OK'];
    }

    /**
     * Check notification health
     */
    protected function checkNotificationHealth(): array
    {
        // Check for failed notifications
        $failedCount = $this->notificationService->getStats()['failed'] ?? 0;
        
        if ($failedCount > 10) {
            return ['status' => 'warning', 'message' => 'High number of failed notifications'];
        }

        return ['status' => 'healthy', 'message' => 'Notifications OK'];
    }

    /**
     * Check services health
     */
    protected function checkServicesHealth(): array
    {
        $services = [
            'qr_service' => class_exists('QRcode'),
            'sms_service' => !empty(env('SMS_API_KEY')),
            'email_service' => !empty(env('SMTP_HOST')),
        ];

        $failedServices = array_filter($services, function($status) { return !$status; });

        if (count($failedServices) > 0) {
            return ['status' => 'warning', 'message' => 'Some services not configured'];
        }

        return ['status' => 'healthy', 'message' => 'All services OK'];
    }
    /**
     * API: Search Health Records & Blood Types
     */
    public function apiSearchHealthRecords()
    {
        $query = $this->request->getGet('q');
        $bloodType = $this->request->getGet('blood_type');

        $db = \Config\Database::connect();
        $builder = $db->table('health_records hr')
            ->select('hr.*, r.first_name, r.last_name, r.contact_no as resident_contact')
            ->join('residents r', 'r.id = hr.resident_id', 'left');

        if (!empty($query)) {
            $builder->groupStart()
                ->like('r.first_name', $query)
                ->orLike('r.last_name', $query)
                ->groupEnd();
        }

        if (!empty($bloodType)) {
            $builder->where('hr.blood_type', $bloodType);
        }

        $results = $builder->get()->getResultArray();
        return $this->jsonSuccess($results);
    }

    /**
     * API: Search Businesses
     */
    public function apiSearchBusiness()
    {
        $query = $this->request->getGet('q');
        $type = $this->request->getGet('type');

        $db = \Config\Database::connect();
        $builder = $db->table('business_permits bp')
            ->select('bp.*, r.first_name, r.last_name')
            ->join('residents r', 'r.id = bp.owner_resident_id', 'left');

        if (!empty($query)) {
            $builder->like('bp.business_name', $query);
        }

        if (!empty($type)) {
            $builder->where('bp.business_type', $type);
        }

        $results = $builder->get()->getResultArray();
        return $this->jsonSuccess($results);
    }

    /**
     * API: Active Emergencies
     */
    public function apiActiveEmergencies()
    {
        $db = \Config\Database::connect();
        $results = $db->table('emergency_incidents')
            ->whereIn('status', ['reported', 'dispatched', 'responding'])
            ->orderBy('created_at', 'DESC')
            ->get()->getResultArray();
            
        return $this->jsonSuccess($results);
    }

    /**
     * API: Events List
     */
    public function apiEventsList()
    {
        $db = \Config\Database::connect();
        $results = $db->table('events')
            ->orderBy('start_date', 'ASC')
            ->get()->getResultArray();
            
        return $this->jsonSuccess($results);
    }
}

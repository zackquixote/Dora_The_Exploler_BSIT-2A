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
use App\Models\ResidentModel;

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
    protected ResidentModel $residentModel;

    public function __construct()
    {
        // Lazy load services only when needed to reduce memory usage
        // Services will be instantiated on first access
    }

    /**
     * Lazy load notification service
     */
    private function getNotificationService(): NotificationService
    {
        if (!isset($this->notificationService)) {
            $this->notificationService = new NotificationService();
        }
        return $this->notificationService;
    }

    /**
     * Lazy load QR service
     */
    private function getQRService(): QRCodeService
    {
        if (!isset($this->qrService)) {
            $this->qrService = new QRCodeService();
        }
        return $this->qrService;
    }

    /**
     * Lazy load analytics service
     */
    private function getAnalyticsService(): AnalyticsService
    {
        if (!isset($this->analyticsService)) {
            $this->analyticsService = new AnalyticsService();
        }
        return $this->analyticsService;
    }

    /**
     * Lazy load emergency service
     */
    private function getEmergencyService(): EmergencyService
    {
        if (!isset($this->emergencyService)) {
            $this->emergencyService = new EmergencyService();
        }
        return $this->emergencyService;
    }

    /**
     * Lazy load business service
     */
    private function getBusinessService(): BusinessService
    {
        if (!isset($this->businessService)) {
            $this->businessService = new BusinessService();
        }
        return $this->businessService;
    }

    /**
     * Lazy load event service
     */
    private function getEventService(): EventService
    {
        if (!isset($this->eventService)) {
            $this->eventService = new EventService();
        }
        return $this->eventService;
    }

    /**
     * Lazy load document model
     */
    private function getDocumentModel(): DocumentModel
    {
        if (!isset($this->documentModel)) {
            $this->documentModel = new DocumentModel();
        }
        return $this->documentModel;
    }

    /**
     * Lazy load health model
     */
    private function getHealthModel(): HealthRecordModel
    {
        if (!isset($this->healthModel)) {
            $this->healthModel = new HealthRecordModel();
        }
        return $this->healthModel;
    }

    /**
     * Lazy load Gmail service
     */
    private function getGmailService(): GmailService
    {
        if (!isset($this->gmailService)) {
            $this->gmailService = new GmailService();
        }
        return $this->gmailService;
    }

    /**
     * Lazy load resident model
     */
    private function getResidentModel(): ResidentModel
    {
        if (!isset($this->residentModel)) {
            $this->residentModel = new ResidentModel();
        }
        return $this->residentModel;
    }

    /**
     * Advanced Analytics Dashboard
     */
    public function analytics()
    {
        $data = [
            'title' => 'Advanced Analytics',
            'analytics' => $this->getAnalyticsService()->getDashboardAnalytics(),
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
            $kpis = $this->getAnalyticsService()->getModuleKpis();
            return $this->jsonSuccess($kpis);
        } catch (\Throwable $e) {
            return $this->jsonError('Failed to load KPIs', 500);
        }
    }

    public function gmail()
    {
        $gmailService = $this->getGmailService();
        $data = [
            'title'            => 'Gmail Integration',
            'gmail_configured' => $gmailService->isConfigured(),
            'gmail_authorized' => $gmailService->isAuthorized(),
        ];

        return view('advanced/gmail', $data);
    }

    public function gmailConnect()
    {
        if (! $this->getGmailService()->isConfigured()) {
            return redirect()->to('/advanced/gmail')->with('error', 'Gmail credentials file not found.');
        }

        try {
            return redirect()->to($this->getGmailService()->getAuthorizationUrl());
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
            $this->getGmailService()->handleOAuthCallback($code);
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
            $sms = $this->getNotificationService()->sendDirectSmsDetailed($phone, $message);
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
            $sent = $this->getNotificationService()->sendDirectEmail($email, $title, $message);
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
            $scheduledAt = $this->request->getPost('scheduled_at');
            $templateId = $this->request->getPost('template_id');
            $saveAsDraft = $this->request->getPost('save_as_draft');

            try {
                // Handle draft saving
                if ($saveAsDraft) {
                    $draftId = $this->saveDraft([
                        'title' => $title,
                        'message' => $message,
                        'recipients' => $recipients,
                        'channels' => $channels,
                    ]);
                    return $this->response->setJSON([
                        'success' => true,
                        'message' => 'Draft saved successfully',
                        'draft_id' => $draftId
                    ]);
                }

                // Get recipient IDs based on criteria
                $residentIds = $this->getRecipientIds($recipients);

                if (empty($residentIds)) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'No recipients found matching the criteria'
                    ]);
                }

                // Personalize message for each recipient
                $results = [];
                $successCount = 0;
                $failedCount = 0;
                $failedRecipients = [];

                foreach ($residentIds as $residentId) {
                    $resident = $this->getResidentModel()->find($residentId);
                    if (!$resident) continue;

                    // Personalize message
                    $personalizedMessage = $this->personalizeMessage($message, $resident);
                    $personalizedTitle = $this->personalizeMessage($title, $resident);

                    // Send or schedule notification
                    if ($scheduledAt) {
                        $notificationId = $this->getNotificationService()->scheduleNotification(
                            $residentId,
                            $type,
                            $personalizedTitle,
                            $personalizedMessage,
                            $scheduledAt,
                            $channels
                        );
                    } else {
                        $notificationId = $this->getNotificationService()->sendToResident(
                            $residentId,
                            $type,
                            $personalizedTitle,
                            $personalizedMessage,
                            $channels
                        );
                    }

                    if ($notificationId) {
                        $successCount++;
                    } else {
                        $failedCount++;
                        $failedRecipients[] = [
                            'id' => $residentId,
                            'name' => $resident['first_name'] . ' ' . $resident['last_name'],
                        ];
                    }
                }

                // Get delivery statistics
                $stats = $this->getDeliveryStats($residentIds);

                return $this->response->setJSON([
                    'success' => true,
                    'message' => $scheduledAt ? 'Notifications scheduled successfully' : 'Notifications sent successfully',
                    'stats' => [
                        'total' => count($residentIds),
                        'success' => $successCount,
                        'failed' => $failedCount,
                        'sms_sent' => $stats['sms_sent'] ?? 0,
                        'email_sent' => $stats['email_sent'] ?? 0,
                        'estimated_cost' => $stats['estimated_cost'] ?? 0,
                    ],
                    'failed_recipients' => $failedRecipients,
                ]);
            } catch (\Exception $e) {
                log_message('error', 'Bulk notification error: ' . $e->getMessage());
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'An error occurred: ' . $e->getMessage()
                ]);
            }
        }

        // Load templates and drafts for the view
        $data = [
            'title' => 'Send Broadcast',
            'templates' => $this->getNotificationTemplates(),
            'drafts' => $this->getUserDrafts(),
            'sitios' => $this->getSitios(),
        ];

        return view('advanced/send_notification', $data);
    }

    /**
     * Get recipient IDs based on criteria
     */
    protected function getRecipientIds($criteria): array
    {
        try {
            if (is_array($criteria)) {
                // Specific resident IDs provided
                return $criteria;
            }

            // Parse JSON criteria
            $filters = json_decode($criteria, true);
            if (!$filters || !is_array($filters)) {
                $filters = ['type' => 'all'];
            }

            $builder = $this->getResidentModel()->builder();
            $builder->where('status', 'active');

            switch ($filters['type'] ?? 'all') {
                case 'all':
                    break;
                case 'sitio':
                    if (!empty($filters['sitio'])) {
                        $builder->where('sitio', $filters['sitio']);
                    }
                    break;
                case 'age_group':
                    if (!empty($filters['min_age'])) {
                        $builder->where('YEAR(CURDATE()) - YEAR(birthdate) - (DATE_FORMAT(CURDATE(), "%m%d") < DATE_FORMAT(birthdate, "%m%d")) >=', (int)$filters['min_age']);
                    }
                    if (!empty($filters['max_age'])) {
                        $builder->where('YEAR(CURDATE()) - YEAR(birthdate) - (DATE_FORMAT(CURDATE(), "%m%d") < DATE_FORMAT(birthdate, "%m%d")) <=', (int)$filters['max_age']);
                    }
                    break;
                case 'gender':
                    if (!empty($filters['gender'])) {
                        $builder->where('sex', $filters['gender']);
                    }
                    break;
                case 'voters':
                    $builder->where('is_voter', 1);
                    break;
                case 'seniors':
                    $builder->where('is_senior_citizen', 1);
                    break;
                case 'pwd':
                    $builder->where('is_pwd', 1);
                    break;
                case 'household_heads':
                    $builder->where('is_household_head', 1);
                    break;
                case 'specific':
                    if (!empty($filters['resident_ids']) && is_array($filters['resident_ids'])) {
                        $builder->whereIn('id', $filters['resident_ids']);
                    }
                    break;
            }

            $residents = $builder->select('id')->get()->getResultArray();
            return array_column($residents, 'id');
        } catch (\Exception $e) {
            log_message('error', 'getRecipientIds error: ' . $e->getMessage() . ' | Criteria: ' . (is_string($criteria) ? $criteria : json_encode($criteria)));
            throw $e;
        }
    }

    /**
     * Personalize message with resident data
     */
    protected function personalizeMessage(string $message, array $resident): string
    {
        $replacements = [
            '{first_name}' => $resident['first_name'] ?? '',
            '{last_name}' => $resident['last_name'] ?? '',
            '{full_name}' => trim(($resident['first_name'] ?? '') . ' ' . ($resident['last_name'] ?? '')),
            '{household_no}' => $resident['household_no'] ?? '',
            '{sitio}' => $resident['sitio'] ?? '',
            '{contact_number}' => $resident['contact_number'] ?? '',
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $message);
    }

    /**
     * Save notification draft
     */
    protected function saveDraft(array $data): int
    {
        $db = \Config\Database::connect();
        $db->table('notification_drafts')->insert([
            'user_id' => session()->get('user_id'),
            'title' => $data['title'] ?? '',
            'message' => $data['message'] ?? '',
            'recipients' => json_encode($data['recipients'] ?? []),
            'channels' => json_encode($data['channels'] ?? []),
            'metadata' => json_encode($data['metadata'] ?? []),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return $db->insertID();
    }

    /**
     * Get user's drafts
     */
    protected function getUserDrafts(): array
    {
        $db = \Config\Database::connect();
        return $db->table('notification_drafts')
            ->where('user_id', session()->get('user_id'))
            ->orderBy('updated_at', 'DESC')
            ->limit(10)
            ->get()
            ->getResultArray();
    }

    /**
     * Get notification templates
     */
    protected function getNotificationTemplates(): array
    {
        $db = \Config\Database::connect();
        return $db->table('notification_templates')
            ->where('is_active', 1)
            ->orderBy('category', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Get delivery statistics
     */
    protected function getDeliveryStats(array $residentIds): array
    {
        $db = \Config\Database::connect();
        
        $stats = $db->table('notification_delivery_logs ndl')
            ->select('
                COUNT(CASE WHEN ndl.channel = "sms" AND ndl.status = "sent" THEN 1 END) as sms_sent,
                COUNT(CASE WHEN ndl.channel = "email" AND ndl.status = "sent" THEN 1 END) as email_sent,
                SUM(ndl.cost) as estimated_cost
            ')
            ->join('notifications n', 'n.id = ndl.notification_id')
            ->whereIn('n.recipient_id', $residentIds)
            ->where('n.created_at >=', date('Y-m-d H:i:s', strtotime('-5 minutes')))
            ->get()
            ->getRowArray();

        return $stats ?? [];
    }

    /**
     * Get list of sitios
     */
    protected function getSitios(): array
    {
        return [
            'Purok Malipayon',
            'Purok Masagana',
            'Purok Cory',
            'Purok Kawayan',
            'Purok Pagla-um',
        ];
    }

    /**
     * Get recipient count (AJAX endpoint)
     */
    public function getRecipientCount()
    {
        try {
            $criteria = $this->request->getPost('criteria');
            if (!$criteria) {
                return $this->response->setJSON([
                    'success' => false,
                    'count' => 0,
                    'error' => 'No criteria provided'
                ])->setStatusCode(400);
            }

            $residentIds = $this->getRecipientIds($criteria);
            
            return $this->response->setJSON([
                'success' => true,
                'count' => count($residentIds),
            ]);
        } catch (\Exception $e) {
            log_message('error', 'getRecipientCount error: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());
            return $this->response->setJSON([
                'success' => false,
                'count' => 0,
                'error' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Load template (AJAX endpoint)
     */
    public function loadTemplate($templateId)
    {
        $db = \Config\Database::connect();
        $template = $db->table('notification_templates')
            ->where('id', $templateId)
            ->get()
            ->getRowArray();

        if ($template) {
            return $this->response->setJSON([
                'success' => true,
                'template' => $template,
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Template not found',
        ]);
    }

    /**
     * Load draft (AJAX endpoint)
     */
    public function loadDraft($draftId)
    {
        $db = \Config\Database::connect();
        $draft = $db->table('notification_drafts')
            ->where('id', $draftId)
            ->where('user_id', session()->get('user_id'))
            ->get()
            ->getRowArray();

        if ($draft) {
            return $this->response->setJSON([
                'success' => true,
                'draft' => $draft,
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Draft not found',
        ]);
    }

    /**
     * Delete draft (AJAX endpoint)
     */
    public function deleteDraft($draftId)
    {
        $db = \Config\Database::connect();
        $deleted = $db->table('notification_drafts')
            ->where('id', $draftId)
            ->where('user_id', session()->get('user_id'))
            ->delete();

        return $this->response->setJSON([
            'success' => $deleted,
            'message' => $deleted ? 'Draft deleted' : 'Failed to delete draft',
        ]);
    }

    /**
     * Search residents (AJAX endpoint)
     */
    public function searchResidents()
    {
        try {
            $query = $this->request->getGet('q');
            
            if (empty($query) || strlen($query) < 2) {
                return $this->response->setJSON([
                    'success' => true,
                    'residents' => [],
                ]);
            }
            
            $residents = $this->getResidentModel()
                ->select('id, first_name, last_name, sitio, contact_number')
                ->where('status', 'active')
                ->groupStart()
                    ->like('first_name', $query)
                    ->orLike('last_name', $query)
                ->groupEnd()
                ->limit(20)
                ->findAll();

            return $this->response->setJSON([
                'success' => true,
                'residents' => $residents,
            ]);
        } catch (\Exception $e) {
            log_message('error', 'searchResidents error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'residents' => [],
                'error' => $e->getMessage()
            ]);
        }
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
                    $qrData = $this->getQRService()->generateCertificateQR($id);
                } elseif ($type === 'resident') {
                    $qrData = $this->getQRService()->generateResidentQR($id);
                } else {
                    throw new \Exception('Invalid QR type');
                }

                $qrImage = $this->getQRService()->generateQRImage($qrData['qr_data']);

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
            $verification = $this->getQRService()->verifyToken($type, $id, $token);
            $this->getQRService()->logVerification($type, $id, $token, $verification['valid'], $this->request->getIPAddress());

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
            'active_incidents' => $this->getEmergencyService()->getEmergencyStats(),
            'preparedness_report' => $this->getEmergencyService()->generatePreparednessReport(),
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
                $incident = $this->getEmergencyService()->reportIncident($incidentData);
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
            'statistics' => $this->getBusinessService()->getBusinessStatistics(),
            'businesses' => $this->getBusinessService()->generateBusinessDirectory(),
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
                $business = $this->getBusinessService()->registerBusiness($businessData);
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
            'upcoming_events' => $this->getEventService()->getUpcomingEvents(),
            'calendar' => $this->getEventService()->getEventsCalendar(),
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
                $event = $this->getEventService()->createEvent($eventData);
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
            'statistics' => $this->getHealthModel()->getHealthStats(),
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
            'storage_stats' => $this->getDocumentModel()->getStorageStats(),
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
                $document = $this->getDocumentModel()->uploadDocument($documentData, $file);
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
            $report = $this->getAnalyticsService()->generateReport($reportType, $dateRange);
            
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
                    $data = $this->getAnalyticsService()->getPopulationAnalytics();
                    break;
                case 'certificates':
                    $data = $this->getAnalyticsService()->getCertificateAnalytics();
                    break;
                case 'business':
                    $data = $this->getBusinessService()->getBusinessStatistics();
                    break;
                default:
                    throw new \Exception('Invalid export type');
            }

            $filename = $type . '_export_' . date('Y-m-d_H-i-s');
            $filepath = $this->getAnalyticsService()->exportData($format, $data, $filename);

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
        $failedCount = $this->getNotificationService()->getStats()['failed'] ?? 0;
        
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

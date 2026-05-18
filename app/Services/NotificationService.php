<?php

namespace App\Services;

use App\Models\NotificationModel;
use App\Models\ResidentModel;
use CodeIgniter\I18n\Time;

/**
 * Advanced Notification Service
 * Handles SMS, Email, Push, and In-App notifications
 */
class NotificationService
{
    protected NotificationModel $notificationModel;
    protected ResidentModel $residentModel;
    protected array $smsConfig;
    protected array $emailConfig;
    protected GmailService $gmailService;
    protected string $emailDriver;
    protected bool $smsVerifySsl;

    public function __construct()
    {
        $this->notificationModel = new NotificationModel();
        $this->residentModel = new ResidentModel();
        
        // SMS Configuration (Semaphore, Twilio, etc.)
        $this->smsConfig = [
            'provider' => env('SMS_PROVIDER', 'semaphore'),
            'api_key' => env('SMS_API_KEY'),
            'sender_name' => env('SMS_SENDER_NAME', 'BARANGAY'),
        ];
        
        // Email Configuration
        $this->emailConfig = [
            'smtp_host' => env('SMTP_HOST'),
            'smtp_user' => env('SMTP_USER'),
            'smtp_pass' => env('SMTP_PASS'),
            'smtp_port' => env('SMTP_PORT', 587),
        ];

        $this->gmailService = new GmailService();
        $this->emailDriver = strtolower((string) env('EMAIL_DRIVER', 'gmail_api'));
        $this->smsVerifySsl = (bool) env('SMS_VERIFY_SSL', false);
    }

    /**
     * Send notification to single recipient
     */
    public function sendToResident(int $residentId, string $type, string $title, string $message, array $channels = ['sms'], array $metadata = []): int
    {
        $notificationId = $this->notificationModel->insert([
            'recipient_type' => 'resident',
            'recipient_id' => $residentId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'channels' => json_encode($channels),
            'status' => 'pending',
            'metadata' => json_encode($metadata),
            'created_at' => Time::now()->toDateTimeString(),
        ]);

        $this->processNotification($notificationId);
        return $notificationId;
    }

    /**
     * Send bulk notifications to multiple residents
     */
    public function sendBulk(array $residentIds, string $type, string $title, string $message, array $channels = ['sms'], array $metadata = []): array
    {
        $notificationIds = [];
        
        foreach ($residentIds as $residentId) {
            $notificationIds[] = $this->sendToResident($residentId, $type, $title, $message, $channels, $metadata);
        }
        
        return $notificationIds;
    }

    /**
     * Send to all residents matching criteria
     */
    public function sendToGroup(array $criteria, string $type, string $title, string $message, array $channels = ['sms'], array $metadata = []): array
    {
        $residents = $this->residentModel->where($criteria)->findAll();
        $residentIds = array_column($residents, 'id');
        
        return $this->sendBulk($residentIds, $type, $title, $message, $channels, $metadata);
    }

    /**
     * Send emergency broadcast to all residents
     */
    public function sendEmergencyBroadcast(string $title, string $message, array $channels = ['sms', 'push']): array
    {
        $residents = $this->residentModel->where('status', 'active')->findAll();
        $residentIds = array_column($residents, 'id');
        
        return $this->sendBulk($residentIds, 'emergency', $title, $message, $channels, [
            'priority' => 'high',
            'emergency' => true,
        ]);
    }

    /**
     * Schedule notification for future delivery
     */
    public function scheduleNotification(int $residentId, string $type, string $title, string $message, string $scheduledAt, array $channels = ['sms']): int
    {
        return $this->notificationModel->insert([
            'recipient_type' => 'resident',
            'recipient_id' => $residentId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'channels' => json_encode($channels),
            'status' => 'pending',
            'scheduled_at' => $scheduledAt,
            'created_at' => Time::now()->toDateTimeString(),
        ]);
    }

    /**
     * Process pending notifications
     */
    public function processPendingNotifications(): void
    {
        $pendingNotifications = $this->notificationModel
            ->where('status', 'pending')
            ->where('(scheduled_at IS NULL OR scheduled_at <= NOW())')
            ->findAll();

        foreach ($pendingNotifications as $notification) {
            $this->processNotification($notification['id']);
        }
    }

    /**
     * Process individual notification
     */
    protected function processNotification(int $notificationId): bool
    {
        $notification = $this->notificationModel->find($notificationId);
        if (!$notification) return false;

        $channels = json_decode($notification['channels'], true);
        $success = true;

        // Get recipient details
        $recipient = $this->residentModel->find($notification['recipient_id']);
        if (!$recipient) {
            $this->notificationModel->update($notificationId, ['status' => 'failed']);
            return false;
        }

        foreach ($channels as $channel) {
            switch ($channel) {
                case 'sms':
                    $success &= $this->sendSMS($recipient, $notification);
                    break;
                case 'email':
                    $success &= $this->sendEmail($recipient, $notification);
                    break;
                case 'push':
                    $success &= $this->sendPushNotification($recipient, $notification);
                    break;
                case 'in_app':
                    $success &= $this->createInAppNotification($recipient, $notification);
                    break;
            }
        }

        $this->notificationModel->update($notificationId, [
            'status' => $success ? 'sent' : 'failed',
            'sent_at' => Time::now()->toDateTimeString(),
        ]);

        return $success;
    }

    /**
     * Send SMS notification
     */
    protected function sendSMS(array $recipient, array $notification): bool
    {
        if (empty($recipient['contact_number'])) return false;

        $phoneNumber = $this->formatPhoneNumber($recipient['contact_number']);
        $message = $notification['message'];

        switch ($this->smsConfig['provider']) {
            case 'semaphore':
                return $this->sendSemaphoreSMS($phoneNumber, $message);
            case 'twilio':
                return $this->sendTwilioSMS($phoneNumber, $message);
            default:
                log_message('error', 'Unknown SMS provider: ' . $this->smsConfig['provider']);
                return false;
        }
    }

    /**
     * Send SMS via Semaphore
     */
    protected function sendSemaphoreSMS(string $phoneNumber, string $message): bool
    {
        $result = $this->sendSemaphoreSMSDetailed($phoneNumber, $message);
        if ($result['success']) {
            return true;
        }

        $summary = $result['provider_status'] ?? 'unknown';
        log_message('error', 'Semaphore SMS failed: ' . $summary);
        return false;
    }

    protected function sendSemaphoreSMSDetailed(string $phoneNumber, string $message): array
    {
        $apiKey = (string) ($this->smsConfig['api_key'] ?? '');
        if ($apiKey === '') {
            return [
                'success' => false,
                'http_code' => null,
                'provider_status' => 'missing_api_key',
                'response' => null,
            ];
        }

        $data = [
            'apikey'  => $apiKey,
            'number'  => $phoneNumber,
            'message' => $message,
        ];

        $sender = (string) ($this->smsConfig['sender_name'] ?? '');
        if ($sender !== '') {
            $data['sendername'] = $sender;
        }

        $http = \Config\Services::curlrequest([
            'timeout' => 20,
            'verify'  => $this->smsVerifySsl,
        ]);

        try {
            $res = $http->post('https://semaphore.co/api/v4/messages', [
                'headers' => [
                    'Accept' => 'application/json',
                ],
                'form_params' => $data,
            ]);

            $code = $res->getStatusCode();
            $body = (string) $res->getBody();
            $json = json_decode($body, true);

            $status = null;
            $messageId = null;
            if (is_array($json) && isset($json[0]) && is_array($json[0])) {
                $status = $json[0]['status'] ?? null;
                $messageId = $json[0]['message_id'] ?? null;
            }

            $okStatuses = ['Queued', 'Success', 'Sent'];

            return [
                'success' => ($code >= 200 && $code < 300) && ($status === null || in_array($status, $okStatuses, true)),
                'http_code' => $code,
                'provider_status' => $status,
                'message_id' => $messageId,
                'response' => is_array($json) ? $json : $body,
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'http_code' => null,
                'provider_status' => 'exception',
                'response' => $e->getMessage(),
            ];
        }
    }

    /**
     * Send SMS via Twilio
     */
    protected function sendTwilioSMS(string $phoneNumber, string $message): bool
    {
        $accountSid = env('TWILIO_ACCOUNT_SID');
        $authToken  = env('TWILIO_AUTH_TOKEN');
        $fromNumber = env('TWILIO_FROM_NUMBER');

        if (empty($accountSid) || empty($authToken) || empty($fromNumber)) {
            log_message('error', 'Twilio credentials not configured');
            return false;
        }

        $url = "https://api.twilio.com/2010-04-01/Accounts/{$accountSid}/Messages.json";

        $data = [
            'From' => $fromNumber,
            'To'   => '+' . ltrim($phoneNumber, '+'),
            'Body' => $message,
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, "{$accountSid}:{$authToken}");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 201) {
            $result = json_decode($response, true);
            return isset($result['sid']);
        }

        log_message('error', 'Twilio SMS failed: ' . $response);
        return false;
    }
    /**
     * Send email notification
     */
    protected function sendEmail(array $recipient, array $notification): bool
    {
        $to = (string) ($recipient['email'] ?? '');
        if ($to === '') {
            return false;
        }

        $subject = (string) ($notification['title'] ?? '');
        $message = (string) ($notification['message'] ?? '');

        if ($this->emailDriver === 'gmail_api') {
            try {
                return $this->gmailService->send(
                    $to,
                    $subject,
                    $message,
                    env('GMAIL_FROM_EMAIL') ?: null,
                    env('GMAIL_FROM_NAME') ?: null
                );
            } catch (\Throwable $e) {
                log_message('error', 'Gmail API send failed: ' . $e->getMessage());
            }
        }

        try {
            $email = \Config\Services::email();
            $email->initialize([
                'protocol'   => 'smtp',
                'SMTPHost'   => (string) ($this->emailConfig['smtp_host'] ?? ''),
                'SMTPUser'   => (string) ($this->emailConfig['smtp_user'] ?? ''),
                'SMTPPass'   => (string) ($this->emailConfig['smtp_pass'] ?? ''),
                'SMTPPort'   => (int) ($this->emailConfig['smtp_port'] ?? 587),
                'SMTPCrypto' => (string) env('SMTP_CRYPTO', 'tls'),
                'mailType'   => 'text',
                'charset'    => 'UTF-8',
                'CRLF'       => "\r\n",
                'newline'    => "\r\n",
            ]);

            $fromEmail = (string) env('EMAIL_FROM_EMAIL', 'noreply@barangay.gov.ph');
            $fromName = (string) env('EMAIL_FROM_NAME', 'Barangay Management System');

            $email->setFrom($fromEmail, $fromName);
            $email->setTo($to);
            $email->setSubject($subject);
            $email->setMessage($message);

            return $email->send();
        } catch (\Throwable $e) {
            log_message('error', 'SMTP email send failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send push notification
     */
    protected function sendPushNotification(array $recipient, array $notification): bool
    {
        // Implementation for push notifications (Firebase, OneSignal, etc.)
        // This would require mobile app integration
        return true; // Placeholder
    }

    /**
     * Create in-app notification
     */
    protected function createInAppNotification(array $recipient, array $notification): bool
    {
        // Store in database for in-app display
        return true; // Placeholder
    }

    /**
     * Format phone number for SMS
     */
    protected function formatPhoneNumber(string $phoneNumber): string
    {
        // Remove non-numeric characters
        $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);
        
        // Convert to international format for Philippines
        if (strlen($phoneNumber) === 10 && substr($phoneNumber, 0, 1) === '9') {
            return '63' . $phoneNumber;
        } elseif (strlen($phoneNumber) === 11 && substr($phoneNumber, 0, 2) === '09') {
            return '63' . substr($phoneNumber, 1);
        }
        
        return $phoneNumber;
    }

    /**
     * Get notification templates
     */
    public function getTemplate(string $type, array $variables = []): array
    {
        $templates = [
            'hearing_reminder' => [
                'title' => 'Hearing Reminder',
                'message' => 'You have a scheduled hearing on {date} at {time} for case {case_number}. Please attend at the Barangay Hall.',
            ],
            'certificate_ready' => [
                'title' => 'Certificate Ready',
                'message' => 'Your {certificate_type} is ready for pickup. Please visit the Barangay Hall during office hours.',
            ],
            'birthday_greeting' => [
                'title' => 'Happy Birthday!',
                'message' => 'Happy Birthday {name}! The Barangay wishes you good health and happiness.',
            ],
            'emergency_alert' => [
                'title' => 'Emergency Alert',
                'message' => 'EMERGENCY: {message}. Please follow safety protocols and stay updated.',
            ],
            'payment_reminder' => [
                'title' => 'Payment Reminder',
                'message' => 'Your {service} payment of ₱{amount} is due on {due_date}. Please settle at the Barangay Hall.',
            ],
            'event_invitation' => [
                'title' => 'Event Invitation',
                'message' => 'You are invited to {event_name} on {date} at {venue}. Registration required.',
            ],
        ];

        $template = $templates[$type] ?? ['title' => '', 'message' => ''];
        
        // Replace variables in template
        foreach ($variables as $key => $value) {
            $template['title'] = str_replace('{' . $key . '}', $value, $template['title']);
            $template['message'] = str_replace('{' . $key . '}', $value, $template['message']);
        }

        return $template;
    }

    /**
     * Send hearing reminders for upcoming hearings
     */
    public function sendHearingReminders(): void
    {
        $db = \Config\Database::connect();
        
        // Get hearings in the next 24 hours that haven't been notified
        $hearings = $db->query("
            SELECT h.*, b.case_number, bp.resident_id 
            FROM blotter_hearings h
            JOIN blotter_records b ON h.blotter_id = b.id
            JOIN blotter_parties bp ON b.id = bp.blotter_id
            WHERE h.hearing_date BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 24 HOUR)
            AND h.notification_sent = 0
            AND h.status = 'Scheduled'
        ")->getResultArray();

        foreach ($hearings as $hearing) {
            $template = $this->getTemplate('hearing_reminder', [
                'date' => date('F j, Y', strtotime($hearing['hearing_date'])),
                'time' => date('g:i A', strtotime($hearing['hearing_time'])),
                'case_number' => $hearing['case_number'],
            ]);

            $this->sendToResident(
                $hearing['resident_id'],
                'hearing_reminder',
                $template['title'],
                $template['message'],
                ['sms']
            );
        }

        // Mark hearings as notified
        $db->query("
            UPDATE blotter_hearings 
            SET notification_sent = 1 
            WHERE hearing_date BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 24 HOUR)
            AND notification_sent = 0
        ");
    }

    /**
     * Send birthday greetings
     */
    public function sendBirthdayGreetings(): void
    {
        $today = date('m-d');
        
        $residents = $this->residentModel
            ->where("DATE_FORMAT(birthdate, '%m-%d')", $today)
            ->where('status', 'active')
            ->findAll();

        foreach ($residents as $resident) {
            $template = $this->getTemplate('birthday_greeting', [
                'name' => $resident['first_name'] . ' ' . $resident['last_name'],
            ]);

            $this->sendToResident(
                $resident['id'],
                'birthday_greeting',
                $template['title'],
                $template['message'],
                ['sms']
            );
        }
    }

    /**
     * Get notification statistics (delegates to model)
     */
    public function getStats(array $dateRange = []): array
    {
        return $this->notificationModel->getStats($dateRange);
    }

    public function sendDirectSms(string $phoneNumber, string $message): bool
    {
        $phoneNumber = trim($phoneNumber);
        $message = trim($message);

        if ($phoneNumber === '' || $message === '') {
            return false;
        }

        $formatted = $this->formatPhoneNumber($phoneNumber);

        return match ($this->smsConfig['provider']) {
            'semaphore' => $this->sendSemaphoreSMS($formatted, $message),
            'twilio'    => $this->sendTwilioSMS($formatted, $message),
            default     => false,
        };
    }

    public function sendDirectSmsDetailed(string $phoneNumber, string $message): array
    {
        $phoneNumber = trim($phoneNumber);
        $message = trim($message);

        if ($phoneNumber === '' || $message === '') {
            return [
                'success' => false,
                'provider' => (string) ($this->smsConfig['provider'] ?? ''),
                'provider_status' => 'missing_input',
            ];
        }

        $formatted = $this->formatPhoneNumber($phoneNumber);

        return match ($this->smsConfig['provider']) {
            'semaphore' => array_merge(['provider' => 'semaphore', 'number' => $formatted], $this->sendSemaphoreSMSDetailed($formatted, $message)),
            'twilio'    => ['success' => $this->sendTwilioSMS($formatted, $message), 'provider' => 'twilio', 'number' => $formatted],
            default     => ['success' => false, 'provider' => (string) ($this->smsConfig['provider'] ?? ''), 'provider_status' => 'unknown_provider', 'number' => $formatted],
        };
    }

    public function sendDirectEmail(string $email, string $subject, string $message): bool
    {
        $email = trim($email);
        if ($email === '') {
            return false;
        }

        return $this->sendEmail(
            ['email' => $email],
            ['title' => $subject, 'message' => $message]
        );
    }
}

<?php

namespace App\Services;

use App\Models\EventModel;
use App\Models\EventParticipantModel;
use App\Models\ResidentModel;
use App\Services\NotificationService;
use CodeIgniter\I18n\Time;

/**
 * Event Management Service
 * Handles community events, registrations, and attendance tracking
 */
class EventService
{
    protected EventModel $eventModel;
    protected EventParticipantModel $participantModel;
    protected ResidentModel $residentModel;
    protected NotificationService $notificationService;

    public function __construct()
    {
        $this->eventModel = new EventModel();
        $this->participantModel = new EventParticipantModel();
        $this->residentModel = new ResidentModel();
        $this->notificationService = new NotificationService();
    }

    /**
     * Create new event
     */
    public function createEvent(array $eventData): array
    {
        // Generate event code
        $eventCode = $this->generateEventCode($eventData['title']);
        
        $event = array_merge($eventData, [
            'event_code' => $eventCode,
            'status' => 'planning',
            'organizer_id' => session()->get('user_id'),
        ]);

        $eventId = $this->eventModel->insert($event);
        
        // Log event creation
        $this->logEventActivity($eventId, 'created', 'Event created');
        
        return $this->eventModel->find($eventId);
    }

    /**
     * Generate unique event code
     */
    protected function generateEventCode(string $title): string
    {
        // Create code from title
        $baseCode = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $title), 0, 6));
        $year = date('Y');
        
        // Ensure uniqueness
        $counter = 1;
        do {
            $eventCode = $baseCode . $year . sprintf('%02d', $counter);
            $exists = $this->eventModel->where('event_code', $eventCode)->first();
            $counter++;
        } while ($exists && $counter <= 99);

        return $eventCode;
    }

    /**
     * Register participant for event
     */
    public function registerParticipant(int $eventId, int $residentId): array
    {
        $event = $this->eventModel->find($eventId);
        $resident = $this->residentModel->find($residentId);
        
        if (!$event) {
            throw new \Exception('Event not found');
        }
        
        if (!$resident) {
            throw new \Exception('Resident not found');
        }

        // Check if registration is still open
        if ($event['status'] !== 'open') {
            throw new \Exception('Registration is not open for this event');
        }

        // Check registration deadline
        if ($event['registration_deadline'] && strtotime($event['registration_deadline']) < time()) {
            throw new \Exception('Registration deadline has passed');
        }

        // Check if already registered
        $existingRegistration = $this->participantModel
            ->where('event_id', $eventId)
            ->where('resident_id', $residentId)
            ->first();

        if ($existingRegistration) {
            throw new \Exception('Resident is already registered for this event');
        }

        // Check capacity
        if ($event['max_participants']) {
            $currentParticipants = $this->participantModel
                ->where('event_id', $eventId)
                ->where('attendance_status !=', 'cancelled')
                ->countAllResults();

            if ($currentParticipants >= $event['max_participants']) {
                throw new \Exception('Event has reached maximum capacity');
            }
        }

        // Check target audience criteria
        if (!empty($event['target_audience'])) {
            $targetAudience = json_decode($event['target_audience'], true);
            if (!$this->checkTargetAudience($resident, $targetAudience)) {
                throw new \Exception('Resident does not meet event criteria');
            }
        }

        // Register participant
        $participantData = [
            'event_id' => $eventId,
            'resident_id' => $residentId,
            'registration_date' => Time::now()->toDateTimeString(),
            'attendance_status' => 'registered',
        ];

        $participantId = $this->participantModel->insert($participantData);
        
        // Send confirmation notification
        $this->sendRegistrationConfirmation($eventId, $residentId);
        
        // Log registration
        $this->logEventActivity($eventId, 'participant_registered', "Resident {$resident['first_name']} {$resident['last_name']} registered");
        
        return $this->participantModel->find($participantId);
    }

    /**
     * Check if resident meets target audience criteria
     */
    protected function checkTargetAudience(array $resident, array $targetAudience): bool
    {
        // Check age range
        if (isset($targetAudience['min_age']) || isset($targetAudience['max_age'])) {
            $age = date_diff(date_create($resident['birthdate']), date_create('today'))->y;
            
            if (isset($targetAudience['min_age']) && $age < $targetAudience['min_age']) {
                return false;
            }
            
            if (isset($targetAudience['max_age']) && $age > $targetAudience['max_age']) {
                return false;
            }
        }

        // Check gender
        if (isset($targetAudience['gender']) && $targetAudience['gender'] !== 'all') {
            if ($resident['sex'] !== $targetAudience['gender']) {
                return false;
            }
        }

        // Check special groups
        if (isset($targetAudience['senior_citizens_only']) && $targetAudience['senior_citizens_only']) {
            if (!$resident['is_senior_citizen']) {
                return false;
            }
        }

        if (isset($targetAudience['pwd_only']) && $targetAudience['pwd_only']) {
            if (!$resident['is_pwd']) {
                return false;
            }
        }

        return true;
    }

    /**
     * Send registration confirmation
     */
    protected function sendRegistrationConfirmation(int $eventId, int $residentId): void
    {
        $event = $this->eventModel->find($eventId);
        
        $template = $this->notificationService->getTemplate('event_invitation', [
            'event_name' => $event['title'],
            'date' => date('F j, Y g:i A', strtotime($event['start_date'])),
            'venue' => $event['venue'],
        ]);

        $this->notificationService->sendToResident(
            $residentId,
            'event_registration_confirmation',
            'Event Registration Confirmed',
            "You have successfully registered for {$event['title']} on " . date('F j, Y g:i A', strtotime($event['start_date'])) . " at {$event['venue']}.",
            ['sms']
        );
    }

    /**
     * Check-in participant
     */
    public function checkInParticipant(int $eventId, int $residentId): bool
    {
        $participant = $this->participantModel
            ->where('event_id', $eventId)
            ->where('resident_id', $residentId)
            ->first();

        if (!$participant) {
            throw new \Exception('Participant not found');
        }

        if ($participant['attendance_status'] === 'attended') {
            throw new \Exception('Participant already checked in');
        }

        return $this->participantModel->update($participant['id'], [
            'attendance_status' => 'attended',
            'check_in_time' => Time::now()->toDateTimeString(),
        ]);
    }

    /**
     * Check-out participant
     */
    public function checkOutParticipant(int $eventId, int $residentId): bool
    {
        $participant = $this->participantModel
            ->where('event_id', $eventId)
            ->where('resident_id', $residentId)
            ->where('attendance_status', 'attended')
            ->first();

        if (!$participant) {
            throw new \Exception('Participant not checked in');
        }

        return $this->participantModel->update($participant['id'], [
            'check_out_time' => Time::now()->toDateTimeString(),
        ]);
    }

    /**
     * Submit event feedback
     */
    public function submitFeedback(int $eventId, int $residentId, int $rating, string $comment = ''): bool
    {
        $participant = $this->participantModel
            ->where('event_id', $eventId)
            ->where('resident_id', $residentId)
            ->first();

        if (!$participant) {
            throw new \Exception('Participant not found');
        }

        return $this->participantModel->update($participant['id'], [
            'feedback_rating' => $rating,
            'feedback_comment' => $comment,
        ]);
    }

    /**
     * Get event statistics
     */
    public function getEventStatistics(int $eventId): array
    {
        $event = $this->eventModel->find($eventId);
        
        if (!$event) {
            throw new \Exception('Event not found');
        }

        $participants = $this->participantModel->where('event_id', $eventId)->findAll();
        
        $stats = [
            'total_registered' => count($participants),
            'attended' => 0,
            'absent' => 0,
            'cancelled' => 0,
            'attendance_rate' => 0,
            'average_rating' => 0,
            'feedback_count' => 0,
        ];

        $totalRating = 0;
        $feedbackCount = 0;

        foreach ($participants as $participant) {
            switch ($participant['attendance_status']) {
                case 'attended':
                    $stats['attended']++;
                    break;
                case 'absent':
                    $stats['absent']++;
                    break;
                case 'cancelled':
                    $stats['cancelled']++;
                    break;
            }

            if ($participant['feedback_rating']) {
                $totalRating += $participant['feedback_rating'];
                $feedbackCount++;
            }
        }

        if ($stats['total_registered'] > 0) {
            $stats['attendance_rate'] = round(($stats['attended'] / $stats['total_registered']) * 100, 2);
        }

        if ($feedbackCount > 0) {
            $stats['average_rating'] = round($totalRating / $feedbackCount, 2);
            $stats['feedback_count'] = $feedbackCount;
        }

        return $stats;
    }

    /**
     * Send event reminders
     */
    public function sendEventReminders(): void
    {
        // Get events starting in 24 hours
        $upcomingEvents = $this->eventModel
            ->where('start_date BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 24 HOUR)')
            ->where('status', 'open')
            ->findAll();

        foreach ($upcomingEvents as $event) {
            $participants = $this->participantModel
                ->where('event_id', $event['id'])
                ->where('attendance_status', 'registered')
                ->findAll();

            foreach ($participants as $participant) {
                $message = "Reminder: You are registered for {$event['title']} tomorrow at " . 
                          date('g:i A', strtotime($event['start_date'])) . " at {$event['venue']}. See you there!";

                $this->notificationService->sendToResident(
                    $participant['resident_id'],
                    'event_reminder',
                    'Event Reminder',
                    $message,
                    ['sms']
                );
            }
        }
    }

    /**
     * Generate event report
     */
    public function generateEventReport(int $eventId): array
    {
        $event = $this->eventModel->find($eventId);
        $statistics = $this->getEventStatistics($eventId);
        
        // Get participant details
        $participants = $this->participantModel
            ->select('event_participants.*, residents.first_name, residents.last_name, residents.contact_number')
            ->join('residents', 'residents.id = event_participants.resident_id')
            ->where('event_participants.event_id', $eventId)
            ->findAll();

        // Get feedback
        $feedback = array_filter($participants, function($p) {
            return !empty($p['feedback_comment']);
        });

        return [
            'event' => $event,
            'statistics' => $statistics,
            'participants' => $participants,
            'feedback' => $feedback,
            'generated_at' => date('Y-m-d H:i:s'),
        ];
    }

    /**
     * Log event activity
     */
    protected function logEventActivity(int $eventId, string $action, string $notes): void
    {
        $db = \Config\Database::connect();
        
        // Create event_activities table if it doesn't exist
        if (!$db->tableExists('event_activities')) {
            $forge = \Config\Database::forge();
            $forge->addField([
                'id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'auto_increment' => true,
                ],
                'event_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                ],
                'action' => [
                    'type' => 'VARCHAR',
                    'constraint' => 50,
                ],
                'notes' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'user_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'null' => true,
                ],
                'created_at' => [
                    'type' => 'DATETIME',
                ],
            ]);
            $forge->addKey('id', true);
            $forge->addKey('event_id');
            $forge->createTable('event_activities');
        }

        $db->table('event_activities')->insert([
            'event_id' => $eventId,
            'action' => $action,
            'notes' => $notes,
            'user_id' => session()->get('user_id'),
            'created_at' => Time::now()->toDateTimeString(),
        ]);
    }

    /**
     * Get upcoming events
     */
    public function getUpcomingEvents(int $limit = 10): array
    {
        return $this->eventModel
            ->where('start_date >', date('Y-m-d H:i:s'))
            ->where('status', 'open')
            ->orderBy('start_date')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Get events calendar
     */
    public function getEventsCalendar(string $month = null): array
    {
        $month = $month ?: date('Y-m');
        
        return $this->eventModel
            ->where("DATE_FORMAT(start_date, '%Y-%m')", $month)
            ->orderBy('start_date')
            ->findAll();
    }

    /**
     * Cancel event registration
     */
    public function cancelRegistration(int $eventId, int $residentId): bool
    {
        $participant = $this->participantModel
            ->where('event_id', $eventId)
            ->where('resident_id', $residentId)
            ->where('attendance_status', 'registered')
            ->first();

        if (!$participant) {
            throw new \Exception('Registration not found or already processed');
        }

        return $this->participantModel->update($participant['id'], [
            'attendance_status' => 'cancelled',
        ]);
    }
}
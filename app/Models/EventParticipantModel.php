<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * EventParticipantModel
 * Manages event registrations and attendance
 */
class EventParticipantModel extends Model
{
    protected $table = 'event_participants';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'event_id',
        'resident_id',
        'qr_token',
        'qr_expires_at',
        'registration_date',
        'attendance_status',
        'check_in_time',
        'checked_in_by',
        'check_out_time',
        'feedback_rating',
        'feedback_comment',
        'certificate_document_id',
        'certificate_generated_at',
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';

    protected $validationRules = [
        'event_id' => 'required|integer',
        'resident_id' => 'required|integer',
        'attendance_status' => 'required|in_list[registered,attended,absent,cancelled]',
        'feedback_rating' => 'permit_empty|integer|greater_than[0]|less_than[6]',
    ];

    /**
     * Find participant by QR token.
     */
    public function findByQrToken(string $token): ?array
    {
        return $this->where('qr_token', $token)->first();
    }

    /**
     * Get participants for event
     */
    public function getForEvent(int $eventId): array
    {
        return $this->select('event_participants.*, residents.first_name, residents.last_name, residents.contact_number')
                   ->join('residents', 'residents.id = event_participants.resident_id')
                   ->where('event_participants.event_id', $eventId)
                   ->orderBy('event_participants.registration_date')
                   ->findAll();
    }

    /**
     * Get events for resident
     */
    public function getForResident(int $residentId): array
    {
        return $this->select('event_participants.*, events.title, events.start_date, events.venue')
                   ->join('events', 'events.id = event_participants.event_id')
                   ->where('event_participants.resident_id', $residentId)
                   ->orderBy('events.start_date', 'DESC')
                   ->findAll();
    }

    /**
     * Get attendance statistics for event
     */
    public function getAttendanceStats(int $eventId): array
    {
        $stats = $this->select('
            COUNT(*) as total_registered,
            SUM(CASE WHEN attendance_status = "attended" THEN 1 ELSE 0 END) as attended,
            SUM(CASE WHEN attendance_status = "absent" THEN 1 ELSE 0 END) as absent,
            SUM(CASE WHEN attendance_status = "cancelled" THEN 1 ELSE 0 END) as cancelled
        ')->where('event_id', $eventId)
          ->first();

        if ($stats['total_registered'] > 0) {
            $stats['attendance_rate'] = round(($stats['attended'] / $stats['total_registered']) * 100, 2);
        } else {
            $stats['attendance_rate'] = 0;
        }

        return $stats;
    }

    /**
     * Get feedback for event
     */
    public function getFeedback(int $eventId): array
    {
        return $this->select('event_participants.feedback_rating, event_participants.feedback_comment, residents.first_name, residents.last_name')
                   ->join('residents', 'residents.id = event_participants.resident_id')
                   ->where('event_participants.event_id', $eventId)
                   ->where('event_participants.feedback_rating IS NOT NULL')
                   ->orderBy('event_participants.feedback_rating', 'DESC')
                   ->findAll();
    }

    /**
     * Check if resident is registered for event
     */
    public function isRegistered(int $eventId, int $residentId): bool
    {
        return $this->where('event_id', $eventId)
                   ->where('resident_id', $residentId)
                   ->where('attendance_status !=', 'cancelled')
                   ->countAllResults() > 0;
    }
}

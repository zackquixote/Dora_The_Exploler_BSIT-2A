<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * EventModel
 * Manages community events and activities
 */
class EventModel extends Model
{
    protected $table = 'events';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'event_code',
        'title',
        'description',
        'event_type',
        'venue',
        'start_date',
        'end_date',
        'max_participants',
        'registration_required',
        'registration_deadline',
        'target_audience',
        'organizer_id',
        'budget',
        'status',
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'event_code' => 'required|is_unique[events.event_code]|max_length[20]',
        'title' => 'required|max_length[255]',
        'description' => 'required',
        'event_type' => 'required|max_length[50]',
        'venue' => 'required|max_length[255]',
        'start_date' => 'required|valid_date',
        'end_date' => 'required|valid_date',
        'organizer_id' => 'required|integer',
    ];

    /**
     * Get upcoming events
     */
    public function getUpcoming(int $limit = 10): array
    {
        return $this->where('start_date >', date('Y-m-d H:i:s'))
                   ->where('status !=', 'cancelled')
                   ->orderBy('start_date')
                   ->limit($limit)
                   ->findAll();
    }

    /**
     * Get events by status
     */
    public function getByStatus(string $status): array
    {
        return $this->where('status', $status)
                   ->orderBy('start_date', 'DESC')
                   ->findAll();
    }

    /**
     * Get events by type
     */
    public function getByType(string $eventType): array
    {
        return $this->where('event_type', $eventType)
                   ->orderBy('start_date', 'DESC')
                   ->findAll();
    }

    /**
     * Get events in date range
     */
    public function getInDateRange(string $startDate, string $endDate): array
    {
        return $this->where('start_date >=', $startDate)
                   ->where('start_date <=', $endDate)
                   ->orderBy('start_date')
                   ->findAll();
    }

    /**
     * Search events
     */
    public function search(string $query): array
    {
        return $this->groupStart()
                   ->like('title', $query)
                   ->orLike('description', $query)
                   ->orLike('event_type', $query)
                   ->orLike('venue', $query)
                   ->groupEnd()
                   ->orderBy('start_date', 'DESC')
                   ->findAll();
    }

    /**
     * Get events with participant count
     */
    public function getWithParticipantCount(): array
    {
        return $this->select('events.*, COUNT(event_participants.id) as participant_count')
                   ->join('event_participants', 'event_participants.event_id = events.id', 'left')
                   ->where('event_participants.attendance_status !=', 'cancelled')
                   ->orWhere('event_participants.attendance_status IS NULL')
                   ->groupBy('events.id')
                   ->orderBy('events.start_date', 'DESC')
                   ->findAll();
    }
}
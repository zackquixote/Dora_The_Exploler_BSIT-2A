<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * EmergencyIncidentModel
 * Manages emergency incidents and response tracking
 */
class EmergencyIncidentModel extends Model
{
    protected $table = 'emergency_incidents';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'incident_number',
        'emergency_type',
        'severity_level',
        'location',
        'coordinates',
        'description',
        'reporter_name',
        'reporter_contact',
        'affected_residents',
        'response_team',
        'status',
        'response_time',
        'resolution_time',
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'emergency_type' => 'required|max_length[100]',
        'severity_level' => 'required|in_list[low,medium,high,critical]',
        'location' => 'required',
        'description' => 'required',
        'reporter_name' => 'required|max_length[150]',
        'reporter_contact' => 'required|max_length[20]',
    ];

    /**
     * Get active incidents
     */
    public function getActiveIncidents(): array
    {
        return $this->whereNotIn('status', ['resolved', 'closed'])
                   ->orderBy('severity_level', 'DESC')
                   ->orderBy('created_at', 'DESC')
                   ->findAll();
    }

    /**
     * Get incidents by type
     */
    public function getByType(string $emergencyType): array
    {
        return $this->where('emergency_type', $emergencyType)
                   ->orderBy('created_at', 'DESC')
                   ->findAll();
    }

    /**
     * Get incidents by severity
     */
    public function getBySeverity(string $severityLevel): array
    {
        return $this->where('severity_level', $severityLevel)
                   ->orderBy('created_at', 'DESC')
                   ->findAll();
    }

    /**
     * Get recent incidents
     */
    public function getRecent(int $limit = 10): array
    {
        return $this->orderBy('created_at', 'DESC')
                   ->limit($limit)
                   ->findAll();
    }
}
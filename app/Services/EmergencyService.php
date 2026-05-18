<?php

namespace App\Services;

use App\Models\EmergencyIncidentModel;
use App\Models\ResidentModel;
use App\Models\HealthRecordModel;
use App\Services\NotificationService;
use CodeIgniter\I18n\Time;

/**
 * Emergency Response Service
 * Handles emergency incidents, alerts, and response coordination
 */
class EmergencyService
{
    protected EmergencyIncidentModel $incidentModel;
    protected ResidentModel $residentModel;
    protected HealthRecordModel $healthModel;
    protected NotificationService $notificationService;

    public function __construct()
    {
        $this->incidentModel = new EmergencyIncidentModel();
        $this->residentModel = new ResidentModel();
        $this->healthModel = new HealthRecordModel();
        $this->notificationService = new NotificationService();
    }

    /**
     * Report new emergency incident
     */
    public function reportIncident(array $incidentData): array
    {
        // Generate incident number
        $incidentNumber = $this->generateIncidentNumber();
        
        $incident = array_merge($incidentData, [
            'incident_number' => $incidentNumber,
            'status' => 'reported',
            'created_at' => Time::now()->toDateTimeString(),
        ]);

        $incidentId = $this->incidentModel->insert($incident);
        
        // Auto-dispatch based on severity
        if ($incident['severity_level'] === 'critical') {
            $this->autoDispatchResponse($incidentId);
        }

        // Send notifications to response team
        $this->notifyResponseTeam($incidentId);

        // Log the incident
        $this->logIncidentActivity($incidentId, 'reported', 'Incident reported');

        return $this->incidentModel->find($incidentId);
    }

    /**
     * Generate unique incident number
     */
    protected function generateIncidentNumber(): string
    {
        $year = date('Y');
        $month = date('m');
        
        // Get last incident number for this month
        $lastIncident = $this->incidentModel
            ->where('incident_number LIKE', "EMR-{$year}{$month}-%")
            ->orderBy('id', 'DESC')
            ->first();

        if ($lastIncident) {
            $lastNumber = (int) substr($lastIncident['incident_number'], -4);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return sprintf('EMR-%s%s-%04d', $year, $month, $nextNumber);
    }

    /**
     * Auto-dispatch emergency response
     */
    protected function autoDispatchResponse(int $incidentId): void
    {
        $incident = $this->incidentModel->find($incidentId);
        
        // Update status to dispatched
        $this->incidentModel->update($incidentId, [
            'status' => 'dispatched',
            'response_time' => Time::now()->toDateTimeString(),
        ]);

        // Send emergency broadcast
        $this->sendEmergencyBroadcast($incident);

        // Notify emergency contacts
        $this->notifyEmergencyContacts($incident);

        $this->logIncidentActivity($incidentId, 'dispatched', 'Emergency response auto-dispatched');
    }

    /**
     * Send emergency broadcast to all residents
     */
    public function sendEmergencyBroadcast(array $incident): void
    {
        $message = "EMERGENCY ALERT: {$incident['emergency_type']} reported at {$incident['location']}. ";
        
        switch ($incident['emergency_type']) {
            case 'Fire':
                $message .= "Stay away from the area. Follow evacuation procedures if necessary.";
                break;
            case 'Flood':
                $message .= "Avoid flooded areas. Move to higher ground if in affected area.";
                break;
            case 'Earthquake':
                $message .= "Drop, Cover, and Hold On. Check for injuries and hazards.";
                break;
            case 'Medical Emergency':
                $message .= "Medical assistance is being provided. Keep area clear for responders.";
                break;
            default:
                $message .= "Follow safety protocols and stay updated for further instructions.";
        }

        $this->notificationService->sendEmergencyBroadcast(
            'Emergency Alert - ' . $incident['emergency_type'],
            $message,
            ['sms', 'push']
        );
    }

    /**
     * Notify emergency response team
     */
    protected function notifyResponseTeam(int $incidentId): void
    {
        $incident = $this->incidentModel->find($incidentId);
        
        // Get response team members (users with emergency_responder role)
        $db = \Config\Database::connect();
        $responders = $db->query("
            SELECT u.*, r.contact_number 
            FROM users u 
            LEFT JOIN residents r ON u.resident_id = r.id
            WHERE u.role IN ('admin', 'emergency_responder')
            AND u.status = 'active'
        ")->getResultArray();

        foreach ($responders as $responder) {
            if (!empty($responder['contact_number'])) {
                $message = "EMERGENCY DISPATCH: {$incident['emergency_type']} at {$incident['location']}. Incident #{$incident['incident_number']}. Severity: {$incident['severity_level']}.";
                
                // Send SMS notification (implement based on your SMS service)
                $this->notificationService->sendToResident(
                    $responder['resident_id'] ?? 0,
                    'emergency_dispatch',
                    'Emergency Dispatch',
                    $message,
                    ['sms']
                );
            }
        }
    }

    /**
     * Notify emergency contacts of affected residents
     */
    protected function notifyEmergencyContacts(array $incident): void
    {
        if (empty($incident['affected_residents'])) {
            return;
        }

        $affectedResidents = json_decode($incident['affected_residents'], true);
        
        foreach ($affectedResidents as $residentId) {
            $healthRecord = $this->healthModel->getByResidentId($residentId);
            
            if ($healthRecord && !empty($healthRecord['emergency_contact_phone'])) {
                $resident = $this->residentModel->find($residentId);
                $message = "EMERGENCY: {$resident['first_name']} {$resident['last_name']} may be affected by {$incident['emergency_type']} at {$incident['location']}. Please contact them or authorities for updates.";
                
                // Send to emergency contact (this would need SMS service integration)
                // For now, log the notification
                log_message('info', "Emergency contact notification: {$healthRecord['emergency_contact_phone']} - {$message}");
            }
        }
    }

    /**
     * Update incident status
     */
    public function updateIncidentStatus(int $incidentId, string $status, string $notes = ''): bool
    {
        $validStatuses = ['reported', 'dispatched', 'responding', 'resolved', 'closed'];
        
        if (!in_array($status, $validStatuses)) {
            throw new \Exception('Invalid incident status');
        }

        $updateData = ['status' => $status];
        
        if ($status === 'responding' && empty($this->incidentModel->find($incidentId)['response_time'])) {
            $updateData['response_time'] = Time::now()->toDateTimeString();
        }
        
        if ($status === 'resolved') {
            $updateData['resolution_time'] = Time::now()->toDateTimeString();
        }

        $result = $this->incidentModel->update($incidentId, $updateData);
        
        if ($result) {
            $this->logIncidentActivity($incidentId, $status, $notes);
        }

        return $result;
    }

    /**
     * Log incident activity
     */
    protected function logIncidentActivity(int $incidentId, string $action, string $notes): void
    {
        $db = \Config\Database::connect();
        
        // Create incident_activities table if it doesn't exist
        if (!$db->tableExists('incident_activities')) {
            $forge = \Config\Database::forge();
            $forge->addField([
                'id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'auto_increment' => true,
                ],
                'incident_id' => [
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
            $forge->addKey('incident_id');
            $forge->createTable('incident_activities');
        }

        $db->table('incident_activities')->insert([
            'incident_id' => $incidentId,
            'action' => $action,
            'notes' => $notes,
            'user_id' => session()->get('user_id'),
            'created_at' => Time::now()->toDateTimeString(),
        ]);
    }

    /**
     * Get evacuation plan for area
     */
    public function getEvacuationPlan(string $area): array
    {
        // Get residents in the affected area
        $residents = $this->residentModel
            ->where('sitio', $area)
            ->where('status', 'active')
            ->findAll();

        // Get vulnerable residents (seniors, PWD, medical conditions)
        $vulnerableResidents = [];
        foreach ($residents as $resident) {
            if ($resident['is_senior_citizen'] || $resident['is_pwd']) {
                $vulnerableResidents[] = $resident;
            }
        }

        // Get evacuation centers
        $evacuationCenters = $this->getEvacuationCenters();

        return [
            'area' => $area,
            'total_residents' => count($residents),
            'vulnerable_residents' => $vulnerableResidents,
            'evacuation_centers' => $evacuationCenters,
            'evacuation_routes' => $this->getEvacuationRoutes($area),
        ];
    }

    /**
     * Get evacuation centers
     */
    protected function getEvacuationCenters(): array
    {
        // This would typically come from a database table
        return [
            [
                'name' => 'Barangay Hall',
                'capacity' => 200,
                'facilities' => ['First Aid', 'Generator', 'Water'],
                'coordinates' => ['lat' => 10.1234, 'lng' => 123.5678],
            ],
            [
                'name' => 'Elementary School',
                'capacity' => 500,
                'facilities' => ['Classrooms', 'Kitchen', 'Restrooms'],
                'coordinates' => ['lat' => 10.1244, 'lng' => 123.5688],
            ],
            [
                'name' => 'Community Center',
                'capacity' => 150,
                'facilities' => ['Stage', 'Sound System', 'Parking'],
                'coordinates' => ['lat' => 10.1254, 'lng' => 123.5698],
            ],
        ];
    }

    /**
     * Get evacuation routes
     */
    protected function getEvacuationRoutes(string $area): array
    {
        // This would typically come from a GIS system or database
        $routes = [
            'Purok Malipayon' => [
                'primary' => 'Main Road → Barangay Hall',
                'secondary' => 'Back Road → Elementary School',
            ],
            'Purok Masagana' => [
                'primary' => 'Highway → Community Center',
                'secondary' => 'Side Street → Barangay Hall',
            ],
            // Add more routes as needed
        ];

        return $routes[$area] ?? [
            'primary' => 'Nearest main road → Barangay Hall',
            'secondary' => 'Alternative route → Elementary School',
        ];
    }

    /**
     * Get emergency supplies inventory
     */
    public function getEmergencySupplies(): array
    {
        $db = \Config\Database::connect();
        
        // Create emergency_supplies table if it doesn't exist
        if (!$db->tableExists('emergency_supplies')) {
            $this->createEmergencySuppliesTable();
        }

        return $db->table('emergency_supplies')
                 ->where('is_active', 1)
                 ->get()
                 ->getResultArray();
    }

    /**
     * Create emergency supplies table
     */
    protected function createEmergencySuppliesTable(): void
    {
        $forge = \Config\Database::forge();
        
        $forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'item_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'category' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'quantity' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'unit' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'location' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'expiry_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'minimum_stock' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'is_active' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        
        $forge->addKey('id', true);
        $forge->addKey('category');
        $forge->createTable('emergency_supplies');

        // Insert sample data
        $db = \Config\Database::connect();
        $sampleSupplies = [
            ['item_name' => 'First Aid Kit', 'category' => 'Medical', 'quantity' => 10, 'unit' => 'pieces', 'location' => 'Barangay Hall Storage'],
            ['item_name' => 'Flashlight', 'category' => 'Equipment', 'quantity' => 25, 'unit' => 'pieces', 'location' => 'Emergency Cabinet'],
            ['item_name' => 'Bottled Water', 'category' => 'Food & Water', 'quantity' => 100, 'unit' => 'bottles', 'location' => 'Storage Room'],
            ['item_name' => 'Emergency Radio', 'category' => 'Communication', 'quantity' => 5, 'unit' => 'pieces', 'location' => 'Command Center'],
            ['item_name' => 'Blankets', 'category' => 'Shelter', 'quantity' => 50, 'unit' => 'pieces', 'location' => 'Storage Room'],
        ];

        foreach ($sampleSupplies as $supply) {
            $supply['created_at'] = date('Y-m-d H:i:s');
            $db->table('emergency_supplies')->insert($supply);
        }
    }

    /**
     * Get emergency statistics
     */
    public function getEmergencyStats(array $dateRange = []): array
    {
        $builder = $this->incidentModel->builder();
        
        if (!empty($dateRange)) {
            $builder->where('created_at >=', $dateRange['start'])
                   ->where('created_at <=', $dateRange['end']);
        }

        // Incidents by type
        $incidentsByType = $builder->select('emergency_type, COUNT(*) as count')
                                 ->groupBy('emergency_type')
                                 ->get()
                                 ->getResultArray();

        // Incidents by severity
        $incidentsBySeverity = $builder->select('severity_level, COUNT(*) as count')
                                     ->groupBy('severity_level')
                                     ->get()
                                     ->getResultArray();

        // Response time analysis
        $responseTimeStats = $builder->select('
            AVG(TIMESTAMPDIFF(MINUTE, created_at, response_time)) as avg_response_minutes,
            MIN(TIMESTAMPDIFF(MINUTE, created_at, response_time)) as min_response_minutes,
            MAX(TIMESTAMPDIFF(MINUTE, created_at, response_time)) as max_response_minutes
        ')->where('response_time IS NOT NULL')
          ->get()
          ->getRowArray();

        return [
            'by_type' => $incidentsByType,
            'by_severity' => $incidentsBySeverity,
            'response_times' => $responseTimeStats,
        ];
    }

    /**
     * Check for low emergency supplies
     */
    public function checkLowSupplies(): array
    {
        $db = \Config\Database::connect();
        
        if (!$db->tableExists('emergency_supplies')) {
            return [];
        }

        return $db->table('emergency_supplies')
                 ->where('quantity <= minimum_stock')
                 ->where('is_active', 1)
                 ->get()
                 ->getResultArray();
    }

    /**
     * Generate emergency preparedness report
     */
    public function generatePreparednessReport(): array
    {
        $totalResidents = $this->residentModel->where('status', 'active')->countAllResults();
        
        // Residents with emergency contacts
        $withEmergencyContacts = $this->healthModel
            ->where('emergency_contact_name IS NOT NULL')
            ->where('emergency_contact_name !=', '')
            ->countAllResults();

        // Vulnerable population
        $vulnerableCount = $this->residentModel
            ->where('status', 'active')
            ->where('(is_senior_citizen = 1 OR is_pwd = 1)')
            ->countAllResults();

        // Emergency supplies status
        $lowSupplies = $this->checkLowSupplies();

        return [
            'total_residents' => $totalResidents,
            'emergency_contacts_coverage' => [
                'count' => $withEmergencyContacts,
                'percentage' => $totalResidents > 0 ? round(($withEmergencyContacts / $totalResidents) * 100, 2) : 0,
            ],
            'vulnerable_population' => [
                'count' => $vulnerableCount,
                'percentage' => $totalResidents > 0 ? round(($vulnerableCount / $totalResidents) * 100, 2) : 0,
            ],
            'supply_status' => [
                'low_supplies_count' => count($lowSupplies),
                'low_supplies' => $lowSupplies,
            ],
            'evacuation_capacity' => array_sum(array_column($this->getEvacuationCenters(), 'capacity')),
        ];
    }
}
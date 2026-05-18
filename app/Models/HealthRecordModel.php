<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * HealthRecordModel
 * Manages resident health information and medical records
 */
class HealthRecordModel extends Model
{
    protected $table = 'health_records';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'resident_id',
        'blood_type',
        'allergies',
        'medical_conditions',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relationship',
        'insurance_provider',
        'insurance_number',
        'vaccination_records',
        'last_checkup_date',
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'resident_id' => 'required|integer',
        'blood_type' => 'permit_empty|in_list[A+,A-,B+,B-,AB+,AB-,O+,O-,Unknown]',
        'emergency_contact_phone' => 'permit_empty|max_length[20]',
    ];

    /**
     * Get health record by resident ID
     */
    public function getByResidentId(int $residentId): ?array
    {
        return $this->where('resident_id', $residentId)->first();
    }

    /**
     * Get residents by blood type (for emergency situations)
     */
    public function getByBloodType(string $bloodType): array
    {
        return $this->select('health_records.*, residents.first_name, residents.last_name, residents.contact_number')
                   ->join('residents', 'residents.id = health_records.resident_id')
                   ->where('health_records.blood_type', $bloodType)
                   ->where('residents.status', 'active')
                   ->findAll();
    }

    /**
     * Get residents with specific medical conditions
     */
    public function getByMedicalCondition(string $condition): array
    {
        return $this->select('health_records.*, residents.first_name, residents.last_name, residents.contact_number')
                   ->join('residents', 'residents.id = health_records.resident_id')
                   ->where("JSON_SEARCH(health_records.medical_conditions, 'one', '{$condition}') IS NOT NULL")
                   ->where('residents.status', 'active')
                   ->findAll();
    }

    /**
     * Add vaccination record
     */
    public function addVaccination(int $residentId, array $vaccinationData): bool
    {
        $healthRecord = $this->getByResidentId($residentId);
        
        if (!$healthRecord) {
            // Create new health record
            $healthRecord = [
                'resident_id' => $residentId,
                'vaccination_records' => json_encode([]),
            ];
            $this->insert($healthRecord);
            $healthRecord = $this->getByResidentId($residentId);
        }

        $vaccinations = json_decode($healthRecord['vaccination_records'] ?? '[]', true);
        $vaccinations[] = array_merge($vaccinationData, [
            'date_administered' => date('Y-m-d H:i:s'),
            'recorded_by' => session()->get('user_id'),
        ]);

        return $this->update($healthRecord['id'], [
            'vaccination_records' => json_encode($vaccinations)
        ]);
    }

    /**
     * Get vaccination history for resident
     */
    public function getVaccinationHistory(int $residentId): array
    {
        $healthRecord = $this->getByResidentId($residentId);
        
        if (!$healthRecord || empty($healthRecord['vaccination_records'])) {
            return [];
        }

        return json_decode($healthRecord['vaccination_records'], true);
    }

    /**
     * Get residents due for vaccination
     */
    public function getDueForVaccination(string $vaccinationType, int $intervalMonths = 12): array
    {
        // This would require more complex logic based on vaccination schedules
        // For now, return residents who haven't had this vaccination in the specified interval
        
        $cutoffDate = date('Y-m-d', strtotime("-{$intervalMonths} months"));
        
        return $this->select('health_records.*, residents.first_name, residents.last_name, residents.contact_number')
                   ->join('residents', 'residents.id = health_records.resident_id')
                   ->where('residents.status', 'active')
                   ->where("(
                       health_records.vaccination_records IS NULL OR
                       JSON_EXTRACT(health_records.vaccination_records, '$[*].vaccine_type') NOT LIKE '%{$vaccinationType}%' OR
                       JSON_EXTRACT(health_records.vaccination_records, '$[*].date_administered') < '{$cutoffDate}'
                   )")
                   ->findAll();
    }

    /**
     * Get health statistics
     */
    public function getHealthStats(): array
    {
        $bloodTypeStats = $this->select('blood_type, COUNT(*) as count')
                              ->where('blood_type !=', 'Unknown')
                              ->groupBy('blood_type')
                              ->findAll();

        $totalRecords = $this->countAll();
        $withEmergencyContacts = $this->where('emergency_contact_name IS NOT NULL')
                                     ->where('emergency_contact_name !=', '')
                                     ->countAllResults();

        $withInsurance = $this->where('insurance_provider IS NOT NULL')
                             ->where('insurance_provider !=', '')
                             ->countAllResults();

        return [
            'total_records' => $totalRecords,
            'blood_type_distribution' => $bloodTypeStats,
            'emergency_contacts_percentage' => $totalRecords > 0 ? round(($withEmergencyContacts / $totalRecords) * 100, 2) : 0,
            'insurance_coverage_percentage' => $totalRecords > 0 ? round(($withInsurance / $totalRecords) * 100, 2) : 0,
        ];
    }

    /**
     * Search residents by health criteria (for emergency response)
     */
    public function searchForEmergency(array $criteria): array
    {
        $builder = $this->select('health_records.*, residents.first_name, residents.last_name, residents.contact_number, residents.sitio')
                       ->join('residents', 'residents.id = health_records.resident_id')
                       ->where('residents.status', 'active');

        if (!empty($criteria['blood_type'])) {
            $builder->where('health_records.blood_type', $criteria['blood_type']);
        }

        if (!empty($criteria['medical_condition'])) {
            $builder->where("JSON_SEARCH(health_records.medical_conditions, 'one', '{$criteria['medical_condition']}') IS NOT NULL");
        }

        if (!empty($criteria['sitio'])) {
            $builder->where('residents.sitio', $criteria['sitio']);
        }

        return $builder->findAll();
    }
}
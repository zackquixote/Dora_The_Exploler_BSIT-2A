<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\HealthRecordModel;
use App\Models\ResidentModel;
use App\Services\AuditService;

/**
 * Phase 1.4 - Health Records (CRUD + Vaccination Editor)
 */
class HealthRecords extends BaseController
{
    protected HealthRecordModel $health;
    protected ResidentModel $residents;
    protected AuditService $audit;

    public function __construct()
    {
        $this->health    = new HealthRecordModel();
        $this->residents = new ResidentModel();
        $this->audit     = new AuditService();
    }

    /**
     * GET /api/health-records?q=&blood_type=&resident_id=
     */
    public function index()
    {
        $q = trim((string) ($this->request->getGet('q') ?? ''));
        $blood = trim((string) ($this->request->getGet('blood_type') ?? ''));
        $residentId = (int) ($this->request->getGet('resident_id') ?? 0);

        $builder = $this->health->builder()
            ->select('health_records.*, r.first_name, r.last_name, r.contact_number as resident_contact')
            ->join('residents r', 'r.id = health_records.resident_id', 'left');

        if ($residentId > 0) {
            $builder->where('health_records.resident_id', $residentId);
        }

        if ($blood !== '') {
            $builder->where('health_records.blood_type', $blood);
        }

        if ($q !== '') {
            $builder->groupStart()
                ->like('r.first_name', $q)
                ->orLike('r.last_name', $q)
                ->groupEnd();
        }

        $rows = $builder->orderBy('health_records.updated_at', 'DESC')->get()->getResultArray();

        // Normalize JSON columns for API consumers
        foreach ($rows as &$r) {
            $r['medical_conditions'] = $this->decodeJsonArray($r['medical_conditions'] ?? null);
            $r['vaccination_records'] = $this->decodeJsonArray($r['vaccination_records'] ?? null);
        }

        return $this->jsonSuccess($rows);
    }

    /**
     * GET /api/health-records/{id}
     */
    public function show(int $id)
    {
        $row = $this->health->find($id);
        if (! $row) {
            return $this->jsonError('Health record not found', 404);
        }

        $resident = $this->residents->find((int) $row['resident_id']);

        $row['medical_conditions'] = $this->decodeJsonArray($row['medical_conditions'] ?? null);
        $row['vaccination_records'] = $this->decodeJsonArray($row['vaccination_records'] ?? null);

        return $this->jsonSuccess([
            'health_record' => $row,
            'resident'      => $resident,
        ]);
    }

    /**
     * POST /api/health-records
     * JSON/form fields:
     * - resident_id (required)
     * - blood_type, allergies, medical_conditions(array|json), emergency_*,
     *   insurance_provider, insurance_number, last_checkup_date, vaccination_records(array|json)
     */
    public function create()
    {
        $payload = $this->request->getJSON(true);
        if (! is_array($payload)) {
            $payload = $this->request->getPost() ?? [];
        }

        $residentId = (int) ($payload['resident_id'] ?? 0);
        if ($residentId <= 0) {
            return $this->jsonError('resident_id is required', 422);
        }

        $resident = $this->residents->find($residentId);
        if (! $resident) {
            return $this->jsonError('Resident not found', 404);
        }

        // One health record per resident (MVP default)
        $existing = $this->health->getByResidentId($residentId);
        if ($existing) {
            return $this->jsonError('Health record already exists for this resident', 409, ['health_record' => $existing]);
        }

        $data = $this->sanitizeRecordPayload($payload);
        $data['resident_id'] = $residentId;

        if (! $this->health->insert($data)) {
            return $this->jsonError('Failed to create health record', 422, $this->health->errors());
        }

        $id = (int) $this->health->getInsertID();
        $row = $this->health->find($id);
        $this->audit->log('create', 'health_record', $id, null, $row);

        return $this->jsonSuccess(['health_record' => $row], 'Health record created');
    }

    /**
     * POST /api/health-records/{id}
     * Update record fields (same payload as create, minus resident_id).
     */
    public function update(int $id)
    {
        $row = $this->health->find($id);
        if (! $row) {
            return $this->jsonError('Health record not found', 404);
        }

        $payload = $this->request->getJSON(true);
        if (! is_array($payload)) {
            $payload = $this->request->getPost() ?? [];
        }

        unset($payload['resident_id']); // prevent moving record across residents

        $data = $this->sanitizeRecordPayload($payload);
        if ($data === []) {
            return $this->jsonError('No updatable fields provided', 422);
        }

        if (! $this->health->update($id, $data)) {
            return $this->jsonError('Failed to update health record', 422, $this->health->errors());
        }

        $updated = $this->health->find($id);
        $this->audit->log('update', 'health_record', $id, $row, $updated);

        return $this->jsonSuccess(['health_record' => $updated], 'Health record updated');
    }

    /**
     * POST /api/health-records/{id}/delete
     * MVP: hard delete (since table has no deleted_at). If you want soft delete, add deleted_at later.
     */
    public function delete(int $id)
    {
        $row = $this->health->find($id);
        if (! $row) {
            return $this->jsonError('Health record not found', 404);
        }

        $this->health->delete($id);
        $this->audit->log('delete', 'health_record', $id, $row, null);

        return $this->jsonSuccess(['deleted' => true], 'Health record deleted');
    }

    /**
     * GET /api/health-records/{id}/vaccinations
     */
    public function listVaccinations(int $id)
    {
        $row = $this->health->find($id);
        if (! $row) {
            return $this->jsonError('Health record not found', 404);
        }

        return $this->jsonSuccess($this->decodeJsonArray($row['vaccination_records'] ?? null));
    }

    /**
     * POST /api/health-records/{id}/vaccinations
     * Adds one vaccination entry.
     *
     * JSON/form:
     * - vaccine_type (required)
     * - dose (optional)
     * - date_administered (optional; default now)
     * - lot_number (optional)
     * - provider (optional)
     * - notes (optional)
     */
    public function addVaccination(int $id)
    {
        $row = $this->health->find($id);
        if (! $row) {
            return $this->jsonError('Health record not found', 404);
        }

        $payload = $this->request->getJSON(true);
        if (! is_array($payload)) {
            $payload = $this->request->getPost() ?? [];
        }

        $vaccineType = trim((string) ($payload['vaccine_type'] ?? ''));
        if ($vaccineType === '') {
            return $this->jsonError('vaccine_type is required', 422);
        }

        $vaccinations = $this->decodeJsonArray($row['vaccination_records'] ?? null);
        $vaccinations[] = [
            'vaccine_type'      => $vaccineType,
            'dose'              => $payload['dose'] ?? null,
            'date_administered' => $payload['date_administered'] ?? date('Y-m-d H:i:s'),
            'lot_number'        => $payload['lot_number'] ?? null,
            'provider'          => $payload['provider'] ?? null,
            'notes'             => $payload['notes'] ?? null,
            'recorded_by'       => (int) (session()->get('user_id') ?? 0),
            'recorded_at'       => date('Y-m-d H:i:s'),
        ];

        $old = $row;
        $this->health->update($id, ['vaccination_records' => json_encode($vaccinations)]);
        $updated = $this->health->find($id);
        $this->audit->log('vaccination_add', 'health_record', $id, $old, $updated);

        return $this->jsonSuccess([
            'vaccinations' => $vaccinations,
        ], 'Vaccination added');
    }

    /**
     * POST /api/health-records/{id}/vaccinations/{index}/update
     */
    public function updateVaccination(int $id, int $index)
    {
        $row = $this->health->find($id);
        if (! $row) {
            return $this->jsonError('Health record not found', 404);
        }

        $vaccinations = $this->decodeJsonArray($row['vaccination_records'] ?? null);
        if (! isset($vaccinations[$index])) {
            return $this->jsonError('Vaccination index not found', 404);
        }

        $payload = $this->request->getJSON(true);
        if (! is_array($payload)) {
            $payload = $this->request->getPost() ?? [];
        }

        $allowed = array_intersect_key($payload, array_flip([
            'vaccine_type', 'dose', 'date_administered', 'lot_number', 'provider', 'notes',
        ]));

        if ($allowed === []) {
            return $this->jsonError('No updatable fields provided', 422);
        }

        $vaccinations[$index] = array_merge($vaccinations[$index], $allowed, [
            'updated_by' => (int) (session()->get('user_id') ?? 0),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $old = $row;
        $this->health->update($id, ['vaccination_records' => json_encode($vaccinations)]);
        $updated = $this->health->find($id);
        $this->audit->log('vaccination_update', 'health_record', $id, $old, $updated);

        return $this->jsonSuccess(['vaccinations' => $vaccinations], 'Vaccination updated');
    }

    /**
     * POST /api/health-records/{id}/vaccinations/{index}/delete
     */
    public function deleteVaccination(int $id, int $index)
    {
        $row = $this->health->find($id);
        if (! $row) {
            return $this->jsonError('Health record not found', 404);
        }

        $vaccinations = $this->decodeJsonArray($row['vaccination_records'] ?? null);
        if (! isset($vaccinations[$index])) {
            return $this->jsonError('Vaccination index not found', 404);
        }

        array_splice($vaccinations, $index, 1);

        $old = $row;
        $this->health->update($id, ['vaccination_records' => json_encode($vaccinations)]);
        $updated = $this->health->find($id);
        $this->audit->log('vaccination_delete', 'health_record', $id, $old, $updated);

        return $this->jsonSuccess(['vaccinations' => $vaccinations], 'Vaccination deleted');
    }

    /**
     * @param mixed $value
     * @return array<int, mixed>
     */
    private function decodeJsonArray($value): array
    {
        if (is_array($value)) {
            return $value;
        }
        if ($value === null || $value === '') {
            return [];
        }
        if (!is_string($value)) {
            return [];
        }
        $decoded = json_decode($value, true);
        return is_array($decoded) ? $decoded : [];
    }

    /**
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    private function sanitizeRecordPayload(array $payload): array
    {
        $data = [];

        foreach ([
            'blood_type',
            'allergies',
            'emergency_contact_name',
            'emergency_contact_phone',
            'emergency_contact_relationship',
            'insurance_provider',
            'insurance_number',
            'last_checkup_date',
        ] as $field) {
            if (array_key_exists($field, $payload)) {
                $data[$field] = $payload[$field];
            }
        }

        // JSON columns: accept array or JSON string
        if (array_key_exists('medical_conditions', $payload)) {
            $data['medical_conditions'] = is_array($payload['medical_conditions'])
                ? json_encode(array_values($payload['medical_conditions']))
                : $payload['medical_conditions'];
        }
        if (array_key_exists('vaccination_records', $payload)) {
            $data['vaccination_records'] = is_array($payload['vaccination_records'])
                ? json_encode(array_values($payload['vaccination_records']))
                : $payload['vaccination_records'];
        }

        return $data;
    }
}


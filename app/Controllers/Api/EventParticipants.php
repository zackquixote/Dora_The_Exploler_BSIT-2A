<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\DocumentModel;
use App\Models\EventModel;
use App\Models\EventParticipantModel;
use App\Models\ResidentModel;
use App\Services\PdfService;

/**
 * Phase 1.3 - Events QR Check-in + Attendance + Certificates (API)
 */
class EventParticipants extends BaseController
{
    protected EventModel $events;
    protected EventParticipantModel $participants;
    protected ResidentModel $residents;
    protected DocumentModel $documents;
    protected PdfService $pdf;
    protected string $secretKey;

    public function __construct()
    {
        $this->events       = new EventModel();
        $this->participants = new EventParticipantModel();
        $this->residents    = new ResidentModel();
        $this->documents    = new DocumentModel();
        $this->pdf          = new PdfService();
        $this->secretKey    = env('QR_SECRET_KEY', 'default_secret_key_change_this');
    }

    /**
     * GET /api/events/{eventId}/participants
     */
    public function list(int $eventId)
    {
        $event = $this->events->find($eventId);
        if (! $event) {
            return $this->jsonError('Event not found', 404);
        }

        $items = $this->participants->getForEvent($eventId);
        $stats = $this->participants->getAttendanceStats($eventId);

        return $this->jsonSuccess([
            'event'  => $event,
            'stats'  => $stats,
            'items'  => $items,
        ]);
    }

    /**
     * POST /api/events/{eventId}/participants
     * Register participant (resident) and generate QR token.
     *
     * JSON/form:
     * - resident_id (required)
     */
    public function register(int $eventId)
    {
        if (! $this->canWrite()) {
            return $this->jsonError('Forbidden', 403);
        }

        $event = $this->events->find($eventId);
        if (! $event) {
            return $this->jsonError('Event not found', 404);
        }

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

        if ($this->participants->isRegistered($eventId, $residentId)) {
            return $this->jsonError('Resident is already registered for this event', 409);
        }

        $data = [
            'event_id'           => $eventId,
            'resident_id'        => $residentId,
            'registration_date'  => date('Y-m-d H:i:s'),
            'attendance_status'  => 'registered',
        ];

        if (! $this->participants->insert($data)) {
            return $this->jsonError('Failed to register participant', 422, $this->participants->errors());
        }

        $participantId = (int) $this->participants->getInsertID();

        // Token expiry: end_date + 1 day (fallback: +7 days)
        $expiresAt = $event['end_date'] ?? null;
        $expiresTs = $expiresAt ? (strtotime($expiresAt) + 86400) : (time() + 86400 * 7);
        $expires   = date('Y-m-d H:i:s', $expiresTs);

        $token = $this->generateToken([
            'type'    => 'event_participant',
            'pid'     => $participantId,
            'eid'     => $eventId,
            'rid'     => $residentId,
            'expires' => $expires,
        ]);

        $this->participants->update($participantId, [
            'qr_token'      => $token,
            'qr_expires_at' => $expires,
        ]);

        $participant = $this->participants->find($participantId);
        $qrData = base_url("events/checkin/{$participantId}/{$token}");


        return $this->jsonSuccess([
            'event'       => $event,
            'participant' => $participant,
            'qr_data'     => $qrData,
        ], 'Participant registered');
    }

    /**
     * GET /api/event-participants/{participantId}/qr
     */
    public function qr(int $participantId)
    {
        $participant = $this->participants->find($participantId);
        if (! $participant) {
            return $this->jsonError('Participant not found', 404);
        }

        if (empty($participant['qr_token'])) {
            return $this->jsonError('QR token not generated yet', 409);
        }

        return $this->jsonSuccess([
            'participant' => $participant,
            'qr_data'     => base_url("events/checkin/{$participantId}/{$participant['qr_token']}"),
            'expires_at'  => $participant['qr_expires_at'] ?? null,
        ]);
    }

    /**
     * POST /api/event-participants/{participantId}/check-in
     * JSON/form: token (required)
     */
    public function checkIn(int $participantId)
    {
        if (! $this->canWrite()) {
            return $this->jsonError('Forbidden', 403);
        }

        $participant = $this->participants->find($participantId);
        if (! $participant) {
            return $this->jsonError('Participant not found', 404);
        }

        $payload = $this->request->getJSON(true);
        if (! is_array($payload)) {
            $payload = $this->request->getPost() ?? [];
        }

        $token = (string) ($payload['token'] ?? '');
        if ($token === '') {
            return $this->jsonError('token is required', 422);
        }

        $verification = $this->verifyToken($participant, $token);
        if (! $verification['valid']) {
            return $this->jsonError($verification['error'] ?? 'Invalid token', 403);
        }

        // Idempotent check-in
        if (!empty($participant['check_in_time'])) {
            return $this->jsonSuccess(['participant' => $participant], 'Already checked in');
        }

        $old = $participant;
        $this->participants->update($participantId, [
            'attendance_status' => 'attended',
            'check_in_time'     => date('Y-m-d H:i:s'),
            'checked_in_by'     => (int) (session()->get('user_id') ?? 0),
        ]);

        $updated = $this->participants->find($participantId);

        return $this->jsonSuccess(['participant' => $updated], 'Checked in');
    }

    /**
     * POST /api/event-participants/{participantId}/check-out
     */
    public function checkOut(int $participantId)
    {
        if (! $this->canWrite()) {
            return $this->jsonError('Forbidden', 403);
        }

        $participant = $this->participants->find($participantId);
        if (! $participant) {
            return $this->jsonError('Participant not found', 404);
        }

        if (empty($participant['check_in_time'])) {
            return $this->jsonError('Participant is not checked in yet', 409);
        }

        if (!empty($participant['check_out_time'])) {
            return $this->jsonSuccess(['participant' => $participant], 'Already checked out');
        }

        $old = $participant;
        $this->participants->update($participantId, [
            'check_out_time' => date('Y-m-d H:i:s'),
        ]);

        $updated = $this->participants->find($participantId);

        return $this->jsonSuccess(['participant' => $updated], 'Checked out');
    }

    /**
     * POST /api/event-participants/{participantId}/certificate
     * Generates a PDF certificate (requires dompdf) and stores it via Document Management.
     */
    public function generateCertificate(int $participantId)
    {
        if (! $this->canWrite()) {
            return $this->jsonError('Forbidden', 403);
        }

        $participant = $this->participants->find($participantId);
        if (! $participant) {
            return $this->jsonError('Participant not found', 404);
        }

        if (($participant['attendance_status'] ?? '') !== 'attended') {
            return $this->jsonError('Certificate can be generated only for attended participants', 409);
        }

        $event = $this->events->find((int) $participant['event_id']);
        $resident = $this->residents->find((int) $participant['resident_id']);
        if (! $event || ! $resident) {
            return $this->jsonError('Event or resident not found', 404);
        }

        $html = view('events/certificate_participation', [
            'event'       => $event,
            'participant' => $participant,
            'resident'    => $resident,
        ]);

        try {
            $pdfBinary = $this->pdf->generate($html, 'landscape');
        } catch (\Throwable $e) {
            return $this->jsonError($e->getMessage(), 500);
        }

        $dir = WRITEPATH . 'uploads/certificates/events/' . (int) $event['id'] . '/';
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $filename = 'certificate_participation_' . $participantId . '_' . date('Ymd_His') . '.pdf';
        $path = $dir . $filename;
        file_put_contents($path, $pdfBinary);

        $doc = $this->documents->createFromPath([
            'entity_type'   => 'event_participant',
            'entity_id'     => $participantId,
            'document_type' => 'certificate_participation',
            'access_level'  => 'internal',
            'uploaded_by'   => (int) (session()->get('user_id') ?? 0),
        ], $path, $filename, 'application/pdf');

        $this->participants->update($participantId, [
            'certificate_document_id'  => (int) ($doc['id'] ?? 0),
            'certificate_generated_at' => date('Y-m-d H:i:s'),
        ]);

        $updated = $this->participants->find($participantId);

        return $this->jsonSuccess([
            'participant' => $updated,
            'document'    => [
                'id'            => $doc['id'] ?? null,
                'version'       => $doc['version'] ?? null,
                'download_url'  => base_url('api/documents/' . ($doc['id'] ?? 0) . '/download'),
            ],
        ], 'Certificate generated');
    }

    /**
     * GET /api/event-participants/{participantId}/certificate/download
     */
    public function downloadCertificate(int $participantId)
    {
        $doc = $this->documents
            ->where('entity_type', 'event_participant')
            ->where('entity_id', $participantId)
            ->where('document_type', 'certificate_participation')
            ->where('is_active', 1)
            ->orderBy('version', 'DESC')
            ->first();

        if (! $doc) {
            return $this->response->setStatusCode(404)->setBody('Certificate not found');
        }

        $path = (string) ($doc['file_path'] ?? '');
        if ($path === '' || !is_file($path)) {
            return $this->response->setStatusCode(404)->setBody('File not found');
        }

        return $this->response->download($path, null);
    }

    private function canWrite(): bool
    {
        $role = strtolower((string) (session()->get('role') ?? ''));
        return in_array($role, ['admin', 'staff'], true);
    }

    private function generateToken(array $payload): string
    {
        $encoded = base64_encode(json_encode($payload));
        $sig = hash_hmac('sha256', $encoded, $this->secretKey);
        return $encoded . '.' . $sig;
    }

    /**
     * @param array<string, mixed> $participant
     */
    private function verifyToken(array $participant, string $token): array
    {
        try {
            $parts = explode('.', $token);
            if (count($parts) !== 2) {
                return ['valid' => false, 'error' => 'Invalid token format'];
            }
            [$encoded, $sig] = $parts;

            $expected = hash_hmac('sha256', $encoded, $this->secretKey);
            if (!hash_equals($expected, $sig)) {
                return ['valid' => false, 'error' => 'Invalid token signature'];
            }

            $payload = json_decode((string) base64_decode($encoded), true);
            if (!is_array($payload)) {
                return ['valid' => false, 'error' => 'Invalid token payload'];
            }

            if (($payload['type'] ?? '') !== 'event_participant') {
                return ['valid' => false, 'error' => 'Invalid token type'];
            }

            if ((int) ($payload['pid'] ?? 0) !== (int) ($participant['id'] ?? 0)) {
                return ['valid' => false, 'error' => 'Token does not match participant'];
            }

            if (!empty($payload['expires']) && strtotime((string) $payload['expires']) < time()) {
                return ['valid' => false, 'error' => 'Token expired'];
            }

            // Compare with stored token to prevent reuse of old/rotated tokens
            if (!hash_equals((string) ($participant['qr_token'] ?? ''), $token)) {
                return ['valid' => false, 'error' => 'Token mismatch'];
            }

            return ['valid' => true];
        } catch (\Throwable $e) {
            return ['valid' => false, 'error' => 'Invalid token'];
        }
    }
}


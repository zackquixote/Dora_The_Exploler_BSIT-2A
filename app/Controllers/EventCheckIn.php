<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\EventModel;
use App\Models\EventParticipantModel;
use App\Models\ResidentModel;
use App\Services\AuditService;

/**
 * Phase 1.3 - QR Scan endpoint (staff/admin)
 * QR contains URL: /events/checkin/{participantId}/{token}
 */
class EventCheckIn extends BaseController
{
    protected EventModel $events;
    protected EventParticipantModel $participants;
    protected ResidentModel $residents;
    protected AuditService $audit;
    protected string $secretKey;

    public function __construct()
    {
        $this->events       = new EventModel();
        $this->participants = new EventParticipantModel();
        $this->residents    = new ResidentModel();
        $this->audit        = new AuditService();
        $this->secretKey    = env('QR_SECRET_KEY', 'default_secret_key_change_this');
    }

    public function scan(int $participantId, string $token)
    {
        $participant = $this->participants->find($participantId);
        if (! $participant) {
            return view('events/checkin_result', [
                'success' => false,
                'message' => 'Participant not found',
            ]);
        }

        $verification = $this->verifyToken($participant, $token);
        if (! $verification['valid']) {
            return view('events/checkin_result', [
                'success' => false,
                'message' => $verification['error'] ?? 'Invalid token',
            ]);
        }

        $event = $this->events->find((int) $participant['event_id']);
        $resident = $this->residents->find((int) $participant['resident_id']);

        // Idempotent
        if (empty($participant['check_in_time'])) {
            $old = $participant;
            $this->participants->update($participantId, [
                'attendance_status' => 'attended',
                'check_in_time'     => date('Y-m-d H:i:s'),
                'checked_in_by'     => (int) (session()->get('user_id') ?? 0),
            ]);
            $updated = $this->participants->find($participantId);
            $this->audit->log('check_in', 'event_participant', $participantId, $old, $updated);
            $participant = $updated ?? $participant;
        }

        return view('events/checkin_result', [
            'success'     => true,
            'message'     => 'Check-in successful',
            'event'       => $event,
            'resident'    => $resident,
            'participant' => $participant,
        ]);
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

            if (!hash_equals((string) ($participant['qr_token'] ?? ''), $token)) {
                return ['valid' => false, 'error' => 'Token mismatch'];
            }

            return ['valid' => true];
        } catch (\Throwable $e) {
            return ['valid' => false, 'error' => 'Invalid token'];
        }
    }
}


<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\EventModel;
use App\Models\EventParticipantModel;
use App\Models\ResidentModel;

/**
 * Portal Events Controller
 * Handles resident-side event browsing, registration, and QR ticket display.
 */
class PortalEvents extends BaseController
{
    protected EventModel $eventModel;
    protected EventParticipantModel $participantModel;
    protected ResidentModel $residentModel;
    protected string $secretKey;

    public function __construct()
    {
        $this->eventModel       = new EventModel();
        $this->participantModel = new EventParticipantModel();
        $this->residentModel    = new ResidentModel();
        $this->secretKey        = env('QR_SECRET_KEY', 'default_secret_key_change_this');
    }

    public function index()
    {
        $residentId = session()->get('resident_id');

        $events = $this->eventModel
            ->where('status', 'open')
            ->where('start_date >', date('Y-m-d H:i:s'))
            ->orderBy('start_date', 'ASC')
            ->findAll();

        // Enrich with registration status for this resident
        foreach ($events as &$event) {
            $registration = $this->participantModel
                ->where('event_id', $event['id'])
                ->where('resident_id', $residentId)
                ->where('attendance_status !=', 'cancelled')
                ->first();

            $event['is_registered'] = !empty($registration);
            $event['registration']  = $registration;

            // Count remaining spots
            $registered = $this->participantModel
                ->where('event_id', $event['id'])
                ->where('attendance_status !=', 'cancelled')
                ->countAllResults();
            $event['slots_taken'] = $registered;
            $event['slots_full']  = $event['max_participants'] && $registered >= $event['max_participants'];
        }

        // Also get my registered events
        $myEvents = $this->participantModel->getForResident($residentId);

        return view('portal/events/index', [
            'events'   => $events,
            'myEvents' => $myEvents,
        ]);
    }

    public function register($eventId)
    {
        $residentId = session()->get('resident_id');
        $event      = $this->eventModel->find($eventId);

        if (!$event || $event['status'] !== 'open') {
            return redirect()->to('portal/events')->with('error', 'Event not found or registration is closed.');
        }

        // Check if already registered
        if ($this->participantModel->isRegistered($eventId, $residentId)) {
            return redirect()->to('portal/events')->with('error', 'You are already registered for this event.');
        }

        // Check capacity
        if ($event['max_participants']) {
            $taken = $this->participantModel
                ->where('event_id', $eventId)
                ->where('attendance_status !=', 'cancelled')
                ->countAllResults();
            if ($taken >= $event['max_participants']) {
                return redirect()->to('portal/events')->with('error', 'Sorry, this event is already full.');
            }
        }

        // Generate QR token
        $now      = date('Y-m-d H:i:s');
        $expires  = date('Y-m-d H:i:s', strtotime('+30 days'));
        $payload  = base64_encode(json_encode([
            'type'    => 'event_participant',
            'eid'     => $eventId,
            'rid'     => $residentId,
            'expires' => $expires,
        ]));
        $sig      = hash_hmac('sha256', $payload, $this->secretKey);
        $qrToken  = $payload . '.' . $sig;

        $participantId = $this->participantModel->insert([
            'event_id'          => $eventId,
            'resident_id'       => $residentId,
            'qr_token'          => $qrToken,
            'qr_expires_at'     => $expires,
            'registration_date' => $now,
            'attendance_status' => 'registered',
        ]);

        return redirect()->to("portal/events/ticket/{$participantId}")
            ->with('success', 'Registered successfully! Here is your QR ticket.');
    }

    public function ticket($participantId)
    {
        $residentId  = session()->get('resident_id');
        $participant = $this->participantModel->find($participantId);

        if (!$participant || (int)$participant['resident_id'] !== (int)$residentId) {
            return redirect()->to('portal/events')->with('error', 'Ticket not found.');
        }

        $event    = $this->eventModel->find($participant['event_id']);
        $resident = $this->residentModel->find($residentId);

        // Build the check-in URL that will be encoded in the QR code
        $checkInUrl = base_url("events/checkin/{$participantId}/{$participant['qr_token']}");

        return view('portal/events/ticket', [
            'participant' => $participant,
            'event'       => $event,
            'resident'    => $resident,
            'checkInUrl'  => $checkInUrl,
        ]);
    }

    public function cancel($participantId)
    {
        $residentId  = session()->get('resident_id');
        $participant = $this->participantModel->find($participantId);

        if (!$participant || (int)$participant['resident_id'] !== (int)$residentId) {
            return redirect()->to('portal/events')->with('error', 'Registration not found.');
        }

        $this->participantModel->update($participantId, ['attendance_status' => 'cancelled']);
        return redirect()->to('portal/events')->with('success', 'Registration cancelled.');
    }
}

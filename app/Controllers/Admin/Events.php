<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\EventModel;
use App\Models\EventParticipantModel;
use App\Models\ResidentModel;

class Events extends BaseController
{
    protected $eventModel;
    protected $participantModel;
    protected $residentModel;

    public function __construct()
    {
        $this->eventModel = new EventModel();
        $this->participantModel = new EventParticipantModel();
        $this->residentModel = new ResidentModel();
    }

    public function index()
    {
        $events = $this->eventModel->orderBy('start_date', 'DESC')->findAll();
        
        // Count participants for each event
        foreach ($events as &$e) {
            $e['participant_count'] = $this->participantModel->where('event_id', $e['id'])->countAllResults();
        }

        return view('Admin/events/index', ['events' => $events]);
    }

    public function create()
    {
        return view('Admin/events/create');
    }

    public function store()
    {
        $rules = [
            'title'                 => 'required',
            'event_type'            => 'required',
            'venue'                 => 'required',
            'start_date'            => 'required|valid_date',
            'end_date'              => 'required|valid_date',
            'max_participants'      => 'permit_empty|is_natural',
            'registration_deadline' => 'permit_empty|valid_date',
            'description'           => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Generate Event Code
        $baseCode = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $this->request->getPost('title')), 0, 6));
        $year = date('Y');
        $counter = 1;
        do {
            $eventCode = $baseCode . $year . sprintf('%02d', $counter);
            $exists = $this->eventModel->where('event_code', $eventCode)->first();
            $counter++;
        } while ($exists && $counter <= 99);

        $data = [
            'event_code'            => $eventCode,
            'title'                 => $this->request->getPost('title'),
            'description'           => $this->request->getPost('description'),
            'event_type'            => $this->request->getPost('event_type'),
            'venue'                 => $this->request->getPost('venue'),
            'start_date'            => $this->request->getPost('start_date'),
            'end_date'              => $this->request->getPost('end_date'),
            'max_participants'      => $this->request->getPost('max_participants') ?: null,
            'registration_required' => 1,
            'registration_deadline' => $this->request->getPost('registration_deadline') ?: null,
            'organizer_id'          => session()->get('user_id') ?? 1,
            'status'                => 'open',
        ];

        if ($this->eventModel->insert($data)) {
            return redirect()->to(base_url('admin/events'))->with('success', 'Event successfully created.');
        }

        return redirect()->back()->withInput()->with('error', 'Failed to create event.');
    }

    public function view($id)
    {
        $event = $this->eventModel->find($id);
        if (!$event) {
            return redirect()->to(base_url('admin/events'))->with('error', 'Event not found.');
        }

        // Get participants
        $participants = $this->participantModel
            ->select('event_participants.*, residents.first_name, residents.last_name, residents.sitio')
            ->join('residents', 'residents.id = event_participants.resident_id')
            ->where('event_participants.event_id', $id)
            ->orderBy('event_participants.registration_date', 'DESC')
            ->findAll();

        return view('Admin/events/view', [
            'event'        => $event,
            'participants' => $participants
        ]);
    }
    
    public function cancel($id)
    {
        $this->eventModel->update($id, ['status' => 'cancelled']);
        return redirect()->back()->with('success', 'Event cancelled.');
    }
}

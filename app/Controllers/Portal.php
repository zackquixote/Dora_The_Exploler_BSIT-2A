<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\BarangaySettingsModel;
use App\Models\ResidentAccountModel;
use App\Models\CertificateRequestModel;
use App\Models\CertificateModel;

/**
 * ------------------------------------------------ --------------------
 * Portal
 * --------------------------------------------------------------------
 * Resident-facing portal controller.
 * Auth is handled by the PortalAuthFilter — no need to check in every method.
 */
class Portal extends BaseController
{
    /**
     * Public landing page (no auth required).
     */
    public function index()
    {
        $settingsModel = new BarangaySettingsModel();
        $settings = $settingsModel->first();

        $db = \Config\Database::connect();
        
        $stats = [
            'residents' => 0,
            'accounts'  => 0
        ];
        $upcomingEvents = [];
        $announcements = [];

        try {
            $stats['residents'] = $db->table('residents')->where('deleted_at', null)->countAllResults();
            $stats['accounts']  = $db->table('resident_accounts')->where('status', 'active')->countAllResults();

            // Fetch latest 3 announcements
            $announcements = $db->table('announcements')
                ->orderBy('is_pinned', 'DESC')
                ->orderBy('created_at', 'DESC')
                ->limit(3)
                ->get()->getResultArray();

            // Fetch upcoming events
            $upcomingEvents = $db->table('events')
                ->where('event_date >=', date('Y-m-d'))
                ->orderBy('event_date', 'ASC')
                ->limit(3)
                ->get()->getResultArray();
        } catch (\Throwable $e) {}
        
        return view('portal/index', [
            'settings'       => $settings,
            'stats'          => $stats,
            'announcements'  => $announcements,
            'upcomingEvents' => $upcomingEvents,
        ]);
    }

    // ─────────────────────────────────────────────────────────
    //  Portal Home
    // ─────────────────────────────────────────────────────────

    public function home()
    {
        $residentId = session()->get('resident_id');

        $residentModel = new \App\Models\ResidentModel();
        $accountModel  = new \App\Models\ResidentAccountModel();

        $resident = $residentModel->find($residentId);
        $account  = $accountModel->where('resident_id', $residentId)->first();

        if (!$resident) {
            return redirect()->to(base_url('portal/login'))->with('error', 'Resident record not found.');
        }

        // ── Build real activity feed ──────────────────────────────
        $db = \Config\Database::connect();
        $activities = [];

        // 1. Blotter filings by this resident
        try {
            $blotters = $db->table('blotter_records br')
                ->select('br.id, br.case_number, br.incident_type, br.status, br.created_at')
                ->join('blotter_parties bp', 'bp.blotter_id = br.id')
                ->where('bp.resident_id', $residentId)
                ->where('bp.role', 'complainant')
                ->orderBy('br.created_at', 'DESC')
                ->limit(10)
                ->get()->getResultArray();

            foreach ($blotters as $b) {
                $activities[] = [
                    'type'   => 'blotter',
                    'icon'   => 'fas fa-balance-scale',
                    'color'  => '#f59e0b',
                    'title'  => 'Incident Report Filed',
                    'desc'   => $b['incident_type'] . ' — Case #' . $b['case_number'],
                    'status' => $b['status'],
                    'date'   => $b['created_at'],
                ];
            }
        } catch (\Throwable $e) {
            // Table may not exist yet
        }

        // 2. Facility bookings
        try {
            $bookings = $db->table('facility_bookings fb')
                ->select('fb.id, fb.purpose, fb.status, fb.created_at, fb.start_datetime, f.name as facility_name')
                ->join('facilities f', 'f.id = fb.facility_id', 'left')
                ->where('fb.resident_id', $residentId)
                ->orderBy('fb.created_at', 'DESC')
                ->limit(10)
                ->get()->getResultArray();

            foreach ($bookings as $bk) {
                $activities[] = [
                    'type'   => 'booking',
                    'icon'   => 'fas fa-building',
                    'color'  => '#0ea5e9',
                    'title'  => 'Facility Booking',
                    'desc'   => ($bk['facility_name'] ?? 'Facility') . ' — ' . $bk['purpose'],
                    'status' => $bk['status'],
                    'date'   => $bk['created_at'],
                ];
            }
        } catch (\Throwable $e) {
            // Table may not exist yet
        }

        // 3. Certificate requests
        try {
            $certRequests = $db->table('certificate_requests cr')
                ->select('cr.id, cr.certificate_type, cr.status, cr.created_at, cr.purpose')
                ->where('cr.resident_id', $residentId)
                ->orderBy('cr.created_at', 'DESC')
                ->limit(10)
                ->get()->getResultArray();

            foreach ($certRequests as $cr) {
                $activities[] = [
                    'type'   => 'certificate_request',
                    'icon'   => 'fas fa-file-signature',
                    'color'  => '#8b5cf6',
                    'title'  => 'Document Request',
                    'desc'   => $cr['certificate_type'] . ' — ' . $cr['purpose'],
                    'status' => $cr['status'],
                    'date'   => $cr['created_at'],
                ];
            }
        } catch (\Throwable $e) {
            // Table may not exist yet
        }

        usort($activities, fn($a, $b) => strtotime($b['date']) - strtotime($a['date']));
        $activities = array_slice($activities, 0, 8);

        // Fetch Announcements
        try {
            $announcements = $db->table('announcements')
                ->orderBy('is_pinned', 'DESC')
                ->orderBy('created_at', 'DESC')
                ->limit(5)
                ->get()->getResultArray();
        } catch (\Throwable $e) {
            $announcements = [];
        }

        return view('portal/home', [
            'resident'   => $resident,
            'account'    => $account,
            'activities' => $activities,
            'announcements' => $announcements,
        ]);
    }

    // ─────────────────────────────────────────────────────────
    //  Blotter / Incident Filing
    // ─────────────────────────────────────────────────────────

    public function fileBlotter()
    {
        return view('portal/file_blotter');
    }

    public function submitBlotter()
    {
        $rules = [
            'incident_type'     => 'required',
            'incident_date'     => 'required|valid_date',
            'incident_location' => 'required|max_length[255]',
            'details'           => 'required',
            'respondent_name'   => 'required|max_length[255]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Please fill all required fields correctly.');
        }

        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            $blotterModel = new \App\Models\BlotterModel();
            $partyModel   = new \App\Models\BlotterPartyModel();

            $year = date('Y');
            $last = $blotterModel
                ->like('case_number', "BLT-{$year}-%", 'after')
                ->orderBy('id', 'DESC')
                ->first();

            $seq = 1;
            if ($last && !empty($last['case_number'])) {
                $parts = explode('-', $last['case_number']);
                $seq = intval(end($parts)) + 1;
            }
            $caseNumber = "BLT-{$year}-" . str_pad($seq, 4, '0', STR_PAD_LEFT);

            $residentId = session()->get('resident_id');
            $resident   = (new \App\Models\ResidentModel())->find($residentId);

            $blotterId = $blotterModel->insert([
                'case_number'       => $caseNumber,
                'incident_type'     => $this->request->getPost('incident_type'),
                'incident_date'     => $this->request->getPost('incident_date'),
                'incident_location' => $this->request->getPost('incident_location'),
                'purok'             => $resident ? $resident['sitio'] : null,
                'details'           => $this->request->getPost('details'),
                'status'            => 'Pending',
                'source'            => 'Online',
                'created_by'        => session()->get('user_id') ?? 1,
            ]);

            $partyModel->insert([
                'blotter_id'  => $blotterId,
                'role'        => 'complainant',
                'resident_id' => $residentId,
            ]);

            $partyModel->insert([
                'blotter_id'       => $blotterId,
                'role'             => 'respondent',
                'outsider_name'    => $this->request->getPost('respondent_name'),
                'outsider_address' => $this->request->getPost('respondent_address'),
            ]);

            $db->transCommit();
            return redirect()->to('portal/home')->with('success', "Your report has been submitted successfully. Case Number: {$caseNumber}");

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Failed to submit report. Error: ' . $e->getMessage());
        }
    }

    // ─────────────────────────────────────────────────────────
    //  My Cases — Track filed blotters
    // ─────────────────────────────────────────────────────────

    public function myCases()
    {
        $residentId = session()->get('resident_id');
        $db = \Config\Database::connect();

        $cases = [];
        try {
            $cases = $db->table('blotter_records br')
                ->select('br.id, br.case_number, br.incident_type, br.incident_date, br.incident_location, br.details, br.status, br.created_at')
                ->join('blotter_parties bp', 'bp.blotter_id = br.id')
                ->where('bp.resident_id', $residentId)
                ->where('bp.role', 'complainant')
                ->orderBy('br.created_at', 'DESC')
                ->get()->getResultArray();

            // For each case, get the respondent info
            foreach ($cases as &$c) {
                $respondent = $db->table('blotter_parties')
                    ->where('blotter_id', $c['id'])
                    ->where('role', 'respondent')
                    ->get()->getRowArray();

                $c['respondent_name'] = $respondent['outsider_name'] ?? 'N/A';

                // Get hearings for timeline
                $c['hearings'] = $db->table('blotter_hearings')
                    ->where('blotter_id', $c['id'])
                    ->orderBy('scheduled_at', 'ASC')
                    ->get()->getResultArray();
            }
        } catch (\Throwable $e) {
            // Tables may not exist
        }

        return view('portal/my_cases', [
            'cases' => $cases,
        ]);
    }

    // ─────────────────────────────────────────────────────────
    //  Digital ID
    // ─────────────────────────────────────────────────────────

    public function myId()
    {
        $residentId = session()->get('resident_id');
        $service = new \App\Services\IdCardService();

        try {
            $resident = $service->getResident((int) $residentId);
        } catch (\App\Exceptions\ResidentNotFoundException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        $qrUrl = $service->getQrUrl((int) $residentId);
        return view('id_generator/print', [
            'title'    => 'My Digital ID',
            'resident' => $resident,
            'qr_url'   => $qrUrl,
        ]);
    }

    // ─────────────────────────────────────────────────────────
    //  Facilities Booking
    // ─────────────────────────────────────────────────────────

    public function facilities()
    {
        $facilityModel = new \App\Models\FacilityModel();
        $bookingModel  = new \App\Models\FacilityBookingModel();

        $data = [
            'facilities'  => $facilityModel->getAvailableFacilities(),
            'my_bookings' => $bookingModel->getBookingsWithDetails(session()->get('resident_id')),
        ];

        return view('portal/facilities', $data);
    }

    public function bookFacility()
    {
        $rules = [
            'facility_id'    => 'required|numeric',
            'start_datetime' => 'required|valid_date',
            'end_datetime'   => 'required|valid_date',
            'purpose'        => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Please fill all required fields correctly.');
        }

        $facilityId = $this->request->getPost('facility_id');
        $start      = $this->request->getPost('start_datetime');
        $end        = $this->request->getPost('end_datetime');

        if (strtotime($start) >= strtotime($end)) {
            return redirect()->back()->withInput()->with('error', 'End time must be after start time.');
        }

        $bookingModel = new \App\Models\FacilityBookingModel();

        if ($bookingModel->checkConflict($facilityId, $start, $end)) {
            return redirect()->back()->withInput()->with('error', 'The facility is already booked during this time. Please choose another schedule.');
        }

        $bookingModel->insert([
            'facility_id'    => $facilityId,
            'resident_id'    => session()->get('resident_id'),
            'start_datetime' => $start,
            'end_datetime'   => $end,
            'purpose'        => $this->request->getPost('purpose'),
            'status'         => 'Pending',
        ]);

        return redirect()->to('portal/facilities')->with('success', 'Booking request submitted successfully! Please wait for admin approval.');
    }

    public function facilityCalendarData()
    {
        $db = \Config\Database::connect();
        // Fetch only approved bookings
        $bookings = $db->table('facility_bookings')
            ->select('facility_bookings.start_datetime as start, facility_bookings.end_datetime as end, facilities.name as title')
            ->join('facilities', 'facilities.id = facility_bookings.facility_id', 'left')
            ->where('facility_bookings.status', 'Approved')
            ->get()->getResultArray();

        // Format for FullCalendar
        foreach ($bookings as &$b) {
            $b['backgroundColor'] = '#10b981'; // green for approved
            $b['borderColor'] = '#059669';
            $b['textColor'] = '#ffffff';
        }

        return $this->response->setJSON($bookings);
    }

    public function cancelBooking($bookingId)
    {
        $bookingModel = new \App\Models\FacilityBookingModel();
        $booking = $bookingModel->find($bookingId);

        if (!$booking || $booking['resident_id'] != session()->get('resident_id')) {
            return redirect()->to('portal/facilities')->with('error', 'Booking not found.');
        }

        if ($booking['status'] !== 'Pending') {
            return redirect()->to('portal/facilities')->with('error', 'Only pending bookings can be cancelled.');
        }

        $bookingModel->update($bookingId, ['status' => 'Cancelled']);

        return redirect()->to('portal/facilities')->with('success', 'Booking cancelled successfully.');
    }

    // ─────────────────────────────────────────────────────────
    //  Profile View & Edit
    // ─────────────────────────────────────────────────────────

    public function profile()
    {
        $residentId = session()->get('resident_id');

        $residentModel = new \App\Models\ResidentModel();
        $accountModel  = new \App\Models\ResidentAccountModel();

        $resident = $residentModel->find($residentId);
        $account  = $accountModel->where('resident_id', $residentId)->first();

        if (!$resident) {
            return redirect()->to(base_url('portal/login'))->with('error', 'Resident record not found.');
        }

        return view('portal/profile', [
            'resident' => $resident,
            'account'  => $account,
        ]);
    }

    public function updateProfile()
    {
        $residentId = session()->get('resident_id');

        $residentModel = new \App\Models\ResidentModel();
        $accountModel  = new \App\Models\ResidentAccountModel();
        $account       = $accountModel->where('resident_id', $residentId)->first();

        // Validate
        $rules = [
            'contact_number' => 'permit_empty|max_length[20]',
            'email'          => 'permit_empty|valid_email|max_length[255]',
            'phone'          => 'permit_empty|max_length[20]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Please check your input.');
        }

        // Update resident contact number
        $residentModel->update($residentId, [
            'contact_number' => $this->request->getPost('contact_number'),
        ]);

        // Update account email/phone
        if ($account) {
            $accountUpdate = [
                'email' => $this->request->getPost('email') ?: $account['email'],
                'phone' => $this->request->getPost('phone') ?: $account['phone'],
            ];
            $accountModel->update($account['id'], $accountUpdate);
        }

        // Change password (optional)
        $newPassword = $this->request->getPost('new_password');
        $confirmPassword = $this->request->getPost('confirm_password');

        if (!empty($newPassword)) {
            if (strlen($newPassword) < 6) {
                return redirect()->back()->with('error', 'Password must be at least 6 characters.');
            }
            if ($newPassword !== $confirmPassword) {
                return redirect()->back()->with('error', 'Passwords do not match.');
            }
            if ($account) {
                $accountModel->update($account['id'], [
                    'password_hash' => password_hash($newPassword, PASSWORD_DEFAULT),
                ]);
            }
        }

        return redirect()->to('portal/profile')->with('success', 'Profile updated successfully!');
    }

    // ─────────────────────────────────────────────────────────
    //  Online Certificate Requests
    // ─────────────────────────────────────────────────────────

    public function myCertificates()
    {
        $residentId = session()->get('resident_id');
        $requestModel = new CertificateRequestModel();
        
        $requests = $requestModel->where('resident_id', $residentId)
                                 ->orderBy('created_at', 'DESC')
                                 ->findAll();

        return view('portal/my_certificates', [
            'requests' => $requests,
        ]);
    }

    public function requestCertificate()
    {
        $types = CertificateModel::getTypes();
        return view('portal/request_certificate', [
            'types' => $types,
        ]);
    }

    public function submitCertificateRequest()
    {
        $rules = [
            'certificate_type' => 'required',
            'purpose'          => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Please fill all required fields correctly.');
        }

        $type = $this->request->getPost('certificate_type');
        if (!in_array($type, CertificateModel::getTypes())) {
            return redirect()->back()->withInput()->with('error', 'Invalid certificate type.');
        }

        $requestModel = new CertificateRequestModel();
        $requestModel->insert([
            'resident_id'      => session()->get('resident_id'),
            'certificate_type' => $type,
            'purpose'          => $this->request->getPost('purpose'),
            'status'           => 'Pending',
        ]);

        return redirect()->to('portal/certificates')->with('success', 'Certificate request submitted successfully! You can track its status here.');
    }

    public function cancelCertificateRequest($requestId)
    {
        $requestModel = new CertificateRequestModel();
        $request = $requestModel->find($requestId);

        if (!$request || $request['resident_id'] != session()->get('resident_id')) {
            return redirect()->to('portal/certificates')->with('error', 'Request not found.');
        }

        if ($request['status'] !== 'Pending') {
            return redirect()->to('portal/certificates')->with('error', 'Only pending requests can be cancelled.');
        }

        $requestModel->update($requestId, ['status' => 'Cancelled']);

        return redirect()->to('portal/certificates')->with('success', 'Certificate request cancelled.');
    }

    // ─────────────────────────────────────────────────────────
    //  Notifications
    // ─────────────────────────────────────────────────────────

    public function notifications()
    {
        $residentId = session()->get('resident_id');
        $notifModel = new \App\Models\NotificationModel();

        $notifications = [];
        try {
            $notifications = $notifModel->where('recipient_type', 'resident')
                ->where('recipient_id', $residentId)
                ->orderBy('created_at', 'DESC')
                ->findAll();
        } catch (\Throwable $e) {
            // Table may not exist yet
        }

        return view('portal/notifications', [
            'notifications' => $notifications,
        ]);
    }

    public function markNotificationsRead()
    {
        $residentId = session()->get('resident_id');
        $notifModel = new \App\Models\NotificationModel();

        try {
            $notifModel->where('recipient_type', 'resident')
                ->where('recipient_id', $residentId)
                ->where('status', 'sent')
                ->set(['status' => 'read'])
                ->update();
        } catch (\Throwable $e) {
            // Ignore if table doesn't exist
        }

        return redirect()->to('portal/notifications')->with('success', 'All notifications marked as read.');
    }
}

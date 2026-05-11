<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\BlotterModel;
use App\Models\BlotterPartyModel;
use App\Models\ResidentModel;
use App\Models\LogModel;

/**
 * Blotter Controller – Case Management (Upgraded)
 *
 * Now uses the normalised `blotter_records` + `blotter_parties` tables.
 * Supports resident selection, outsider entry, multiple roles,
 * automatic case number generation, and audit logging.
 */
class Blotter extends BaseController
{
    protected $blotterModel;
    protected $partyModel; 
    protected $residentModel;
    protected $logModel;
    protected $hearingModel;
    protected $timelineModel;
    protected $db;

    public function __construct()
    {
        $this->blotterModel  = new BlotterModel();
        $this->partyModel    = new BlotterPartyModel();
        $this->residentModel = new ResidentModel();
        $this->logModel      = new LogModel();
        $this->hearingModel  = new \App\Models\BlotterHearingModel();
        $this->timelineModel = new \App\Models\BlotterTimelineModel();
        $this->db            = \Config\Database::connect();
    }

    /**
     * List All Cases
     * 
     * Execute index functionality for blotter records.
     *
     * @return mixed
     */
    public function index()
    {
        // Because we now store complainants/respondents in blotter_parties,
        // we perform a custom query to aggregate the primary complainant
        // and respondent for each case (first one found of each role).
        $db = \Config\Database::connect();

        $blotterRows = $db->table('blotter_records')
            ->select([
                'blotter_records.*',
                'COALESCE(cpl.resident_name, cpl.outsider_name) as complainant_name',
                'COALESCE(resp.resident_name, resp.outsider_name) as respondent_name',
            ])
            // Subquery-like LEFT JOINs to pick the first complainant and first respondent
            ->join(
                "(SELECT blotter_id, 
                         resident_name, outsider_name
                  FROM (
                      SELECT bp.blotter_id,
                             CONCAT(r.first_name, ' ', r.last_name) AS resident_name,
                             bp.outsider_name,
                             ROW_NUMBER() OVER (PARTITION BY bp.blotter_id ORDER BY bp.id) AS rn
                      FROM blotter_parties bp
                      LEFT JOIN residents r ON r.id = bp.resident_id
                      WHERE bp.role = 'complainant'
                  ) sub WHERE rn = 1
                ) cpl",
                'cpl.blotter_id = blotter_records.id',
                'left'
            )
            ->join(
                "(SELECT blotter_id,
                         resident_name, outsider_name
                  FROM (
                      SELECT bp.blotter_id,
                             CONCAT(r.first_name, ' ', r.last_name) AS resident_name,
                             bp.outsider_name,
                             ROW_NUMBER() OVER (PARTITION BY bp.blotter_id ORDER BY bp.id) AS rn
                      FROM blotter_parties bp
                      LEFT JOIN residents r ON r.id = bp.resident_id
                      WHERE bp.role = 'respondent'
                  ) sub WHERE rn = 1
                ) resp",
                'resp.blotter_id = blotter_records.id',
                'left'
            )
            ->orderBy('blotter_records.id', 'DESC')
            ->get()
            ->getResultArray();

        return view('blotter/index', ['blotters' => $blotterRows]);
    }

    /**
     * Create Form
     * 
     * Execute create functionality for blotter records.
     *
     * @return mixed
     */
    public function create()
    {
        // Provide all active residents for the dropdown search
        $residents = $this->residentModel
            ->where('status', 'active')
            ->orderBy('last_name', 'ASC')
            ->findAll();

        return view('blotter/create', ['residents' => $residents]);
    }

    /**
     * Store New Case + Parties
     * 
     * Execute store functionality for blotter records.
     *
     * @return mixed
     */
    public function store()
    {
        // 1. Validate core incident fields
        $rules = [
            'incident_type'    => 'required',
            'incident_date'    => 'required|valid_date',
            'incident_location'=> 'permit_empty|max_length[255]',
            'purok'            => 'permit_empty|max_length[50]',
            'details'          => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // 2. Extract parties from POST – we expect arrays:
        //    parties[index][role] = 'complainant'|'respondent'|'witness'
        //    parties[index][type] = 'resident'|'outsider'
        //    parties[index][resident_id] = ...
        //    parties[index][outsider_name] = ...
        //    parties[index][outsider_address] = ...
        $partyData = $this->request->getPost('parties') ?? [];
        if (empty($partyData)) {
            return redirect()->back()->with('error', 'At least one involved party is required.');
        }

        // At least one complainant and one respondent
        $roles = array_column($partyData, 'role');
        if (!in_array('complainant', $roles) || !in_array('respondent', $roles)) {
            return redirect()->back()->withInput()->with('error', 'You must add at least one complainant and one respondent.');
        }

        // Validate individual party data
        foreach ($partyData as $p) {
            $type = $p['type'] ?? 'outsider';
            if ($type === 'resident' && empty($p['resident_id'])) {
                return redirect()->back()->withInput()->with('error', 'Resident party must have a resident selected.');
            }
            if ($type === 'outsider' && empty(trim($p['outsider_name'] ?? ''))) {
                return redirect()->back()->withInput()->with('error', 'Outsider party must have a name.');
            }
        }

        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            // 3. Generate case number
            $year = date('Y');
            $last = $this->blotterModel
                ->like('case_number', "BLT-{$year}-%", 'after')
                ->orderBy('id', 'DESC')
                ->first();

            if ($last && !empty($last['case_number'])) {
                // Extract sequence number
                $parts = explode('-', $last['case_number']);
                $seq   = intval(end($parts)) + 1;
            } else {
                $seq = 1;
            }
            $caseNumber = "BLT-{$year}-" . str_pad($seq, 4, '0', STR_PAD_LEFT);

            // 4. Insert blotter record (without old complainant/respondent)
            $blotterId = $this->blotterModel->insert([
                'case_number'      => $caseNumber,
                'incident_type'    => $this->request->getPost('incident_type'),
                'incident_date'    => $this->request->getPost('incident_date'),
                'incident_location'=> $this->request->getPost('incident_location'),
                'purok'            => $this->request->getPost('purok'),
                'details'          => $this->request->getPost('details'),
                'status'           => 'Pending',
                'created_by'       => session()->get('user_id') ?? session()->get('id'),
            ]);

            // 5. Insert each party
            foreach ($partyData as $p) {
                $role = $p['role'];
                $type = $p['type'] ?? 'outsider'; // default outsider if missing

                $insert = [
                    'blotter_id' => $blotterId,
                    'role'       => $role,
                ];

                if ($type === 'resident' && !empty($p['resident_id'])) {
                    $insert['resident_id'] = $p['resident_id'];
                } else {
                    $insert['outsider_name']    = $p['outsider_name'] ?? '';
                    $insert['outsider_address'] = $p['outsider_address'] ?? '';
                }

                $this->partyModel->insert($insert);
            }

            // 6. Commit and log
            $db->transCommit();
            $this->logModel->addLog("Created blotter case {$caseNumber}");

            return redirect()->to('blotter')->with('success', "Case {$caseNumber} recorded successfully.");
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Blotter store failed: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'An error occurred while saving the case.');
        }
    }

    /**
     * View Case Detail
     * 
     * Execute view functionality for blotter records.
     *
     * @return mixed
     */
    public function view($id)
{
    $case = $this->db->table('blotter_records')
        ->select('blotter_records.*, users.name as created_by_name')
        ->join('users', 'users.id = blotter_records.created_by', 'left')
        ->where('blotter_records.id', $id)
        ->get()->getRowArray();

    if (!$case) {
        return redirect()->to('blotter')->with('error', 'Case not found.');
    }

    $parties = $this->partyModel->getByBlotter($id);
    $grouped = [];
    foreach ($parties as $p) {
        $grouped[$p['role']][] = $p;
    }

    // Load hearings
    $hearings = $this->hearingModel->getByBlotter($id);
    $timeline = $this->timelineModel->getByBlotter($id);


    return view('blotter/view', [
        'case'      => $case,
        'parties'   => $grouped,
        'hearings'  => $hearings,
        'timeline'  => $timeline,       
    ]);
}

    /**
     * Edit Case (Basic Fields)
     * 
     * Execute edit functionality for blotter records.
     *
     * @return mixed
     */
    public function edit($id)
    {
        $case = $this->blotterModel->find($id);
        if (!$case) {
            return redirect()->to('blotter')->with('error', 'Case not found.');
        }

        $residents = $this->residentModel->where('status', 'active')->findAll();
        $parties   = $this->partyModel->where('blotter_id', $id)->findAll();

        return view('blotter/edit', [
            'case'      => $case,
            'residents' => $residents,
            'parties'   => $parties,
        ]);
    }

    /**
     * Update Case + Parties
     * 
     * Execute update functionality for blotter records.
     *
     * @return mixed
     */
    public function update($id)
{
    $case = $this->blotterModel->find($id);
    if (!$case) {
        return redirect()->to('blotter')->with('error', 'Case not found.');
    }

    // Validate input (matching store() rules)
    $rules = [
        'incident_type'     => 'required|max_length[50]',
        'incident_date'     => 'required|valid_date',
        'incident_location' => 'permit_empty|max_length[255]',
        'details'           => 'required',
        'status'            => 'required|in_list[Pending,Investigating,Ongoing,For Hearing,Settled,Dismissed,Referred,Unsettled]',
    ];

    if (!$this->validate($rules)) {
        return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
    }

    // Capture status before update for timeline
    $oldStatus = $case['status'];
    $newStatus = $this->request->getPost('status');

    $db = \Config\Database::connect();
    $db->transBegin();

    try {
        // Update main blotter fields
        $this->blotterModel->update($id, [
            'incident_type'     => $this->request->getPost('incident_type'),
            'incident_date'     => $this->request->getPost('incident_date'),
            'incident_location' => $this->request->getPost('incident_location'),
            'purok'             => $this->request->getPost('purok'),
            'details'           => $this->request->getPost('details'),
            'status'            => $newStatus,
            'action_taken'      => $this->request->getPost('action_taken'),
            'updated_by'        => session()->get('user_id') ?? session()->get('id'),
        ]);

        // Log timeline entry if status changed
        if ($oldStatus !== $newStatus) {
            $this->timelineModel->insert([
                'blotter_id' => $id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'remarks'    => $this->request->getPost('action_taken') ?? '',
                'created_by' => session()->get('user_id') ?? session()->get('id'),
            ]);
        }

        // Remove existing parties and re-insert — skip if this is a Quick Update from the view sidebar
        $isQuickUpdate = (bool) $this->request->getPost('_quick_update');
        if (!$isQuickUpdate) {
            $this->partyModel->where('blotter_id', $id)->delete();

            $partyData = $this->request->getPost('parties') ?? [];
            foreach ($partyData as $p) {
                $insert = [
                    'blotter_id' => $id,
                    'role'       => $p['role'],
                ];
                if (($p['type'] ?? '') === 'resident' && !empty($p['resident_id'])) {
                    $insert['resident_id'] = $p['resident_id'];
                } else {
                    $insert['outsider_name']    = $p['outsider_name'] ?? '';
                    $insert['outsider_address'] = $p['outsider_address'] ?? '';
                }
                $this->partyModel->insert($insert);
            }
        }

        $db->transCommit();
        $this->logModel->addLog("Updated blotter case {$case['case_number']}");

        return redirect()->to('blotter')->with('success', 'Case updated successfully.');
    } catch (\Exception $e) {
        $db->transRollback();
        log_message('error', 'Blotter update failed: ' . $e->getMessage());
        return redirect()->back()->withInput()->with('error', 'An error occurred while updating the case.');
    }
}
    /**
     * Delete Case (With Cascading Parties)
     * 
     * Execute delete functionality for blotter records.
     *
     * @return mixed
     */
    public function delete($id)
    {
        $case = $this->blotterModel->find($id);
        if (!$case) {
            return redirect()->to('blotter')->with('error', 'Case not found.');
        }

        $this->blotterModel->delete($id);  // FK CASCADE removes parties
        $this->logModel->addLog("Deleted blotter case {$case['case_number']}");

        return redirect()->to('blotter')->with('success', 'Case deleted.');
    }

    /**
     * Ajax: Search Residents By Name (For Autocomplete/Dropdown)
     * 
     * Execute searchResidents functionality for blotter records.
     *
     * @return mixed
     */
   public function searchResidents()
{
    $term = $this->request->getGet('q');

    // If a search term is provided, filter; otherwise return all active residents
    $builder = $this->residentModel
        ->select('id, CONCAT(first_name, " ", last_name) as text')
        ->where('status', 'active')
        ->orderBy('last_name', 'ASC')
        ->orderBy('first_name', 'ASC');

    if (!empty($term)) {
        $builder->groupStart()
                ->like('first_name', $term)
                ->orLike('last_name', $term)
                ->groupEnd();
    }

    $residents = $builder->limit(500)->findAll();  // limit for performance

    return $this->response->setJSON($residents);
}
/**
 * AJAX – store a new hearing
 */
public function addHearing($blotterId)
{
    $case = $this->blotterModel->find($blotterId);
    if (!$case) {
        return $this->response->setJSON(['status' => 'error', 'message' => 'Case not found.', 'csrf_hash' => csrf_hash()]);
    }

    // Validate hearing input
    $rules = [
        'hearing_date'      => 'required|valid_date',
        'hearing_time'      => 'permit_empty',
        'venue'             => 'permit_empty|max_length[255]',
        'presiding_officer' => 'permit_empty|max_length[150]',
    ];

    if (!$this->validate($rules)) {
        return $this->response->setJSON(['status' => 'error', 'errors' => $this->validator->getErrors(), 'csrf_hash' => csrf_hash()]);
    }

    $data = [
        'blotter_id'         => $blotterId,
        'hearing_date'       => $this->request->getPost('hearing_date'),
        'hearing_time'       => $this->request->getPost('hearing_time'),
        'venue'              => $this->request->getPost('venue'),
        'presiding_officer'  => $this->request->getPost('presiding_officer'),
        'notes'              => $this->request->getPost('notes'),
        'status'             => $this->request->getPost('status') ?? 'Scheduled',
        'created_by'         => session()->get('user_id') ?? session()->get('id'),
    ];

    if ($this->hearingModel->insert($data)) {
        $this->logModel->addLog("Added hearing for case {$case['case_number']}");
        return $this->response->setJSON(['status' => 'success', 'message' => 'Hearing added.', 'csrf_hash' => csrf_hash()]);
    }

    return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to save hearing.', 'csrf_hash' => csrf_hash()]);
}

/**
 * AJAX – update an existing hearing
 */
public function updateHearing($hearingId)
{
    $hearing = $this->hearingModel->find($hearingId);
    if (!$hearing) {
        return $this->response->setJSON(['status' => 'error', 'message' => 'Hearing not found.', 'csrf_hash' => csrf_hash()]);
    }

    $data = [
        'hearing_date'       => $this->request->getPost('hearing_date'),
        'hearing_time'       => $this->request->getPost('hearing_time'),
        'venue'              => $this->request->getPost('venue'),
        'presiding_officer'  => $this->request->getPost('presiding_officer'),
        'notes'              => $this->request->getPost('notes'),
        'outcome'            => $this->request->getPost('outcome'),
        'status'             => $this->request->getPost('status'),
    ];

    if ($this->hearingModel->update($hearingId, $data)) {
        $case = $this->blotterModel->find($hearing['blotter_id']);
        $this->logModel->addLog("Updated hearing for case {$case['case_number']}");
        return $this->response->setJSON(['status' => 'success', 'message' => 'Hearing updated.', 'csrf_hash' => csrf_hash()]);
    }

    return $this->response->setJSON(['status' => 'error', 'message' => 'Update failed.', 'csrf_hash' => csrf_hash()]);
}

/**
 * AJAX – delete a hearing
 */
public function deleteHearing($hearingId)
{
    $hearing = $this->hearingModel->find($hearingId);
    if (!$hearing) {
        return $this->response->setJSON(['status' => 'error', 'message' => 'Hearing not found.', 'csrf_hash' => csrf_hash()]);
    }

    $this->hearingModel->delete($hearingId);
    $case = $this->blotterModel->find($hearing['blotter_id']);
    $this->logModel->addLog("Deleted hearing for case {$case['case_number']}");
    return $this->response->setJSON(['status' => 'success', 'message' => 'Hearing deleted.', 'csrf_hash' => csrf_hash()]);
}

/**
 * Print-friendly case summary
 */
public function printCase($id)
{
    $case = $this->db->table('blotter_records')
        ->select('blotter_records.*, users.name as created_by_name')
        ->join('users', 'users.id = blotter_records.created_by', 'left')
        ->where('blotter_records.id', $id)
        ->get()->getRowArray();

    if (!$case) {
        return redirect()->to('blotter')->with('error', 'Case not found.');
    }

    // Load parties with resident names
    $parties = $this->partyModel->getByBlotter($id);
    $grouped = [];
    foreach ($parties as $p) {
        $grouped[$p['role']][] = $p;
    }

    // Hearings
    $hearings = $this->hearingModel->getByBlotter($id);

    // Status History
    $timeline = $this->timelineModel->getByBlotter($id);

    // Barangay info for the header
    $barangay = (new \App\Models\BarangaySettingsModel())->first();

    return view('blotter/print', [
        'case'      => $case,
        'parties'   => $grouped,
        'hearings'  => $hearings,
        'timeline'  => $timeline,
        'barangay'  => $barangay,
    ]);
}

/**
 * Print Amicable Settlement Contract
 */
public function printSettlement($id)
{
    $case = $this->blotterModel->find($id);
    if (!$case || strtolower($case['status']) !== 'settled') {
        return redirect()->to('blotter')->with('error', 'Case is not eligible for a settlement contract.');
    }

    $parties = $this->partyModel->getByBlotter($id);
    $grouped = [];
    foreach ($parties as $p) {
        $grouped[$p['role']][] = $p;
    }

    $barangay = (new \App\Models\BarangaySettingsModel())->first();

    return view('blotter/print_settlement', [
        'case'     => $case,
        'parties'  => $grouped,
        'barangay' => $barangay,
    ]);
}

/**
 * Print Summon for a specific hearing
 */
public function printSummon($caseId, $hearingId)
{
    $case = $this->blotterModel->find($caseId);
    if (!$case) {
        return redirect()->to('blotter')->with('error', 'Case not found.');
    }

    $hearing = $this->hearingModel->find($hearingId);
    if (!$hearing || $hearing['blotter_id'] != $caseId) {
        return redirect()->to("blotter/view/$caseId")->with('error', 'Hearing not found.');
    }

    $parties = $this->partyModel->getByBlotter($caseId);
    $grouped = [];
    foreach ($parties as $p) {
        $grouped[$p['role']][] = $p;
    }

    $barangay = (new \App\Models\BarangaySettingsModel())->first();

    return view('blotter/print_summon', [
        'case'     => $case,
        'hearing'  => $hearing,
        'parties'  => $grouped,
        'barangay' => $barangay,
    ]);
}
/**
 * AJAX: Get upcoming hearing notifications.
 * 
 * @return \CodeIgniter\HTTP\ResponseInterface
 */
public function getUpcomingNotifications()
{
    // Check authentication
    if (!session()->get('logged_in')) {
        return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
    }

    $days = $this->request->getGet('days') ?? 3;
    $upcomingHearings = $this->hearingModel->getUpcomingHearings($days);
    $overdueHearings  = $this->hearingModel->getOverdueHearings();

    $notifications = [];

    // Add Overdue hearings first (high priority)
    foreach ($overdueHearings as $h) {
        $notifications[] = [
            'id'          => $h['id'] . '_overdue',
            'title'       => 'Overdue Hearing',
            'case_number' => $h['case_number'],
            'message'     => 'Hearing was due on ' . date('M d, Y', strtotime($h['hearing_date'])) . ' but is still marked as Scheduled.',
            'date'        => $h['hearing_date'],
            'time'        => $h['hearing_time'] ? date('h:i A', strtotime($h['hearing_time'])) : '',
            'venue'       => $h['venue'],
            'url'         => site_url('blotter/view/' . $h['blotter_id']),
            'type'        => 'danger'
        ];
    }

    // Add Upcoming hearings
    foreach ($upcomingHearings as $h) {
        $notifications[] = [
            'id'          => $h['id'],
            'title'       => 'Upcoming Hearing',
            'case_number' => $h['case_number'],
            'message'     => 'Hearing scheduled for ' . date('M d, Y', strtotime($h['hearing_date'])),
            'date'        => $h['hearing_date'],
            'time'        => $h['hearing_time'] ? date('h:i A', strtotime($h['hearing_time'])) : '',
            'venue'       => $h['venue'],
            'url'         => site_url('blotter/view/' . $h['blotter_id']),
            'type'        => 'warning'
        ];
    }

    // Optionally, you could auto-mark as notified when fetched, but better to let user click "mark read".
    // For now, we only return the list.

    return $this->response->setJSON([
        'status'        => 'success',
        'notifications' => $notifications,
        'count'         => count($notifications),
        'csrf_hash'     => csrf_hash()
    ]);
}

/**
 * Export Blotter Cases to CSV
 */
public function exportCsv()
{
    if (!session()->get('logged_in')) {
        return redirect()->to('/login');
    }

    $db = \Config\Database::connect();
    
    // Fetch all cases with basic details
    $cases = $db->table('blotter_records')
        ->select('case_number, incident_type, incident_date, incident_location, purok, status, action_taken, created_at')
        ->orderBy('id', 'DESC')
        ->get()
        ->getResultArray();

    $filename = 'Blotter_Cases_' . date('Ymd_His') . '.csv';

    header("Content-Description: File Transfer");
    header("Content-Disposition: attachment; filename=$filename");
    header("Content-Type: text/csv; charset=UTF-8");

    $file = fopen('php://output', 'w');

    // Headers
    fputcsv($file, ['Case Number', 'Incident Type', 'Date of Incident', 'Location', 'Purok', 'Status', 'Action Taken', 'Date Filed']);

    foreach ($cases as $c) {
        fputcsv($file, [
            $c['case_number'],
            $c['incident_type'],
            $c['incident_date'],
            $c['incident_location'],
            $c['purok'],
            $c['status'],
            $c['action_taken'],
            $c['created_at']
        ]);
    }

    fclose($file);
    exit;
}
}
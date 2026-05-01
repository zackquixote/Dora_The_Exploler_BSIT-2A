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

    public function __construct()
    {
        $this->blotterModel  = new BlotterModel();
        $this->partyModel    = new BlotterPartyModel();
        $this->residentModel = new ResidentModel();
        $this->logModel      = new LogModel();
        $this->hearingModel  = new \App\Models\BlotterHearingModel();
        $this->timelineModel = new \App\Models\BlotterTimelineModel();
    }

    // ──────────────────────────────────────────────────────────
    //  LIST ALL CASES
    // ──────────────────────────────────────────────────────────
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

    // ──────────────────────────────────────────────────────────
    //  CREATE FORM
    // ──────────────────────────────────────────────────────────
    public function create()
    {
        // Provide all active residents for the dropdown search
        $residents = $this->residentModel
            ->where('status', 'active')
            ->orderBy('last_name', 'ASC')
            ->findAll();

        return view('blotter/create', ['residents' => $residents]);
    }

    // ──────────────────────────────────────────────────────────
    //  STORE NEW CASE + PARTIES
    // ──────────────────────────────────────────────────────────
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
            return redirect()->back()->with('error', 'You must add at least one complainant and one respondent.');
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

    // ──────────────────────────────────────────────────────────
    //  VIEW CASE DETAIL
    // ──────────────────────────────────────────────────────────
    public function view($id)
{
    $case = $this->blotterModel->find($id);
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

    // ──────────────────────────────────────────────────────────
    //  EDIT CASE (basic fields)
    // ──────────────────────────────────────────────────────────
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

    // ──────────────────────────────────────────────────────────
    //  UPDATE CASE + PARTIES
    // ──────────────────────────────────────────────────────────

    public function update($id)
{
    $case = $this->blotterModel->find($id);
    if (!$case) {
        return redirect()->to('blotter')->with('error', 'Case not found.');
    }

    // Capture status before update for timeline
    $oldStatus = $case['status'];
    $newStatus = $this->request->getPost('status');

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

    // Remove existing parties and re-insert
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

    $this->logModel->addLog("Updated blotter case {$case['case_number']}");

    return redirect()->to('blotter')->with('success', 'Case updated successfully.');
}
    // ──────────────────────────────────────────────────────────
    //  DELETE CASE (with cascading parties)
    // ──────────────────────────────────────────────────────────
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

    // ──────────────────────────────────────────────────────────
    //  AJAX: Search residents by name (for autocomplete/dropdown)
    // ──────────────────────────────────────────────────────────
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
        return $this->response->setJSON(['status' => 'error', 'message' => 'Case not found.']);
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
        return $this->response->setJSON(['status' => 'success', 'message' => 'Hearing added.']);
    }

    return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to save hearing.']);
}

/**
 * AJAX – update an existing hearing
 */
public function updateHearing($hearingId)
{
    $hearing = $this->hearingModel->find($hearingId);
    if (!$hearing) {
        return $this->response->setJSON(['status' => 'error', 'message' => 'Hearing not found.']);
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
        return $this->response->setJSON(['status' => 'success', 'message' => 'Hearing updated.']);
    }

    return $this->response->setJSON(['status' => 'error', 'message' => 'Update failed.']);
}

/**
 * AJAX – delete a hearing
 */
public function deleteHearing($hearingId)
{
    $hearing = $this->hearingModel->find($hearingId);
    if (!$hearing) {
        return $this->response->setJSON(['status' => 'error', 'message' => 'Hearing not found.']);
    }

    $this->hearingModel->delete($hearingId);
    $case = $this->blotterModel->find($hearing['blotter_id']);
    $this->logModel->addLog("Deleted hearing for case {$case['case_number']}");
    return $this->response->setJSON(['status' => 'success', 'message' => 'Hearing deleted.']);
}

/**
 * Print-friendly case summary
 */
public function printCase($id)
{
    $case = $this->blotterModel->find($id);
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
}
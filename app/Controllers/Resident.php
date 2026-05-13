<?php

namespace App\Controllers;

use App\Models\ResidentModel;
use App\Models\HouseholdModel;
use App\Models\LogModel;

class Resident extends BaseController
{
    protected $residentModel;
    protected $householdModel;
    protected $logModel;
    protected $db;

    public function __construct()
    {
        $this->residentModel  = new ResidentModel();
        $this->householdModel = new HouseholdModel();
        $this->logModel       = new LogModel();
        $this->db             = \Config\Database::connect();
    }

    private function requireLogin()
    {
        if (! session()->get('logged_in')) {
            return $this->respondLoginRequired();
        }

        return null;
    }

    // ── Index ─────────────────────────────────────────────────────────
    public function index()
    {
        if ($r = $this->requireLogin()) return $r;

        $selectedPurok = $this->request->getGet('purok') ?? 'all';

        $builder = $this->db->table('residents')
            ->select('residents.*, households.household_no, TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) as age')
            ->join('households', 'households.id = residents.household_id', 'left')
            ->where('residents.deleted_at IS NULL');

        if ($selectedPurok !== 'all') {
            if ($selectedPurok === 'Unassigned') {
                $builder->groupStart()
                    ->where('residents.sitio', null)
                    ->orWhere('residents.sitio', '')
                    ->groupEnd();
            } else {
                $builder->where('residents.sitio', $selectedPurok);
            }
        }

        $residents = $builder->orderBy('residents.id', 'DESC')->get()->getResultArray();

        $allResidents = $this->db->table('residents')
            ->select('sitio')
            ->where('deleted_at IS NULL')
            ->get()
            ->getResultArray();

        $purokCounts = [];
        foreach ($allResidents as $r) {
            $sitio = !empty($r['sitio']) ? $r['sitio'] : 'Unassigned';
            $purokCounts[$sitio] = ($purokCounts[$sitio] ?? 0) + 1;
        }

        return view('residents/index', [
            'title'         => 'Residents',
            'residents'     => $residents,
            'purokCounts'   => $purokCounts,
            'selectedPurok' => $selectedPurok,
        ]);
    }

    // ── Create ────────────────────────────────────────────────────────
    public function create()
    {
        if ($r = $this->requireLogin()) return $r;

        $households = $this->householdModel->orderBy('household_no', 'ASC')->findAll();

        return view('residents/create', [
            'title'                => 'Add Resident',
            'households'           => $households,
            'preselectedHousehold' => $this->request->getGet('household_id'),
        ]);
    }

    // ── Store (with duplicate protection & auto senior) ─────────────
    public function store()
    {
        if ($r = $this->requireLogin()) return $r;

        $rules = [
            'first_name'      => 'required|min_length[2]|max_length[100]|regex_match[/^[a-zA-ZÀ-ÿ\s\'\-\.]+$/]',
            'last_name'       => 'required|min_length[2]|max_length[100]|regex_match[/^[a-zA-ZÀ-ÿ\s\'\-\.]+$/]',
            'birthdate'       => 'required|valid_date|valid_birthdate',
            'sex'             => 'required|in_list[male,female]',
            'sitio'           => 'required|max_length[100]',
            'contact_number'  => 'permit_empty|regex_match[/^[\d\+\-\s\(\)]+$/]|min_length[7]|max_length[20]',
            'profile_picture' => 'permit_empty|is_image[profile_picture]|max_size[profile_picture,2048]|mime_in[profile_picture,image/jpg,image/jpeg,image/png,image/gif]',
        ];

        log_message('debug', 'Resident Store Called - POST Data: ' . json_encode($this->request->getPost()));

        if ($this->request->isAJAX()) {
            if (!$this->validate($rules)) {
                return $this->response->setJSON(['status' => 'error', 'errors' => $this->validator->getErrors()]);
            }
            $profilePic = $this->uploadProfilePicture($this->request->getFile('profile_picture'), $this->request->getPost('sitio'));
            $data       = $this->prepareResidentData($this->request->getPost(), $profilePic);
            $data['status'] = 'active';
            $data['registered_by'] = session()->get('user_id') ?? 1;

            try {
                $result = $this->residentModel->insert($data);
                if ($result === false) {
                    $modelErrors = $this->residentModel->errors();
                    log_message('error', 'Resident AJAX insert failed - Model errors: ' . json_encode($modelErrors));
                    return $this->response->setJSON(['status' => 'error', 'errors' => $modelErrors]);
                }

                $fullName = $data['first_name'] . ' ' . $data['last_name'];
                $this->logModel->addLog('Added Resident ' . $fullName);

                return $this->response->setJSON(['status' => 'success', 'redirect' => base_url('resident')]);
            } catch (\Exception $e) {
                log_message('error', 'Resident AJAX insert exception: ' . $e->getMessage());
                if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                    return $this->response->setJSON(['status' => 'error', 'message' => 'A resident with the same name and birthdate already exists.']);
                }
                return $this->response->setJSON(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
            }
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $profilePic = $this->uploadProfilePicture($this->request->getFile('profile_picture'), $this->request->getPost('sitio'));
        $data       = $this->prepareResidentData($this->request->getPost(), $profilePic);
        $data['status'] = 'active';
        $data['registered_by'] = session()->get('user_id') ?? 1;

        try {
            $result = $this->residentModel->insert($data);
            if ($result === false) {
                $modelErrors = $this->residentModel->errors();
                log_message('error', 'Resident insert failed - Model errors: ' . json_encode($modelErrors));
                return redirect()->back()->withInput()->with('errors', $modelErrors);
            }

            $fullName = $data['first_name'] . ' ' . $data['last_name'];
            $this->logModel->addLog('Added Resident ' . $fullName);

            return redirect()->to(base_url('resident'))->with('success', 'Resident added successfully.');
        } catch (\Exception $e) {
            log_message('error', 'Resident insert exception: ' . $e->getMessage());
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return redirect()->back()->with('error', 'A resident with the same name and birthdate already exists.')->withInput();
            }
            return redirect()->back()->with('error', 'Database error: ' . $e->getMessage())->withInput();
        }
    }

    // ── Edit ──────────────────────────────────────────────────────────
    public function edit($id)
    {
        if ($r = $this->requireLogin()) return $r;

        $resident = $this->residentModel->find($id);
        if (!$resident) {
            return redirect()->to('/resident')->with('error', 'Resident not found');
        }

        $households = $this->householdModel->orderBy('household_no', 'ASC')->findAll();

        return view('residents/edit', [
            'title'      => 'Edit Resident',
            'resident'   => $resident,
            'households' => $households,
        ]);
    }

    // ── Update (with duplicate protection & auto senior) ────────────
    public function update($id)
    {
        if ($r = $this->requireLogin()) return $r;

        $rules = [
            'first_name'      => 'required|min_length[2]|regex_match[/^[a-zA-ZÀ-ÿ\s\'\-\.]+$/]',
            'last_name'       => 'required|min_length[2]|regex_match[/^[a-zA-ZÀ-ÿ\s\'\-\.]+$/]',
            'birthdate'       => 'required|valid_date|valid_birthdate',
            'sex'             => 'required|in_list[male,female]',
            'sitio'           => 'required|max_length[100]',
            'contact_number'  => 'permit_empty|regex_match[/^[\d\+\-\s\(\)]+$/]|min_length[7]|max_length[20]',
            'profile_picture' => 'permit_empty|is_image[profile_picture]|max_size[profile_picture,2048]|mime_in[profile_picture,image/jpg,image/jpeg,image/png,image/gif]',
        ];

        if ($this->request->isAJAX()) {
            if (!$this->validate($rules)) {
                return $this->response->setJSON(['status' => 'error', 'errors' => $this->validator->getErrors()]);
            }

            $resident = $this->residentModel->find($id);
            if (!$resident) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Resident not found']);
            }

            $profilePic = $this->uploadProfilePicture(
                $this->request->getFile('profile_picture'),
                $this->request->getPost('sitio'),
                $resident['profile_picture'] ?? null
            );
            $data = $this->prepareResidentData($this->request->getPost(), $profilePic);

            try {
                $result = $this->residentModel->update($id, $data);
                if ($result === false) {
                    return $this->response->setJSON(['status' => 'error', 'errors' => $this->residentModel->errors()]);
                }
                return $this->response->setJSON(['status' => 'success', 'redirect' => base_url('resident')]);
            } catch (\Exception $e) {
                if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                    return $this->response->setJSON(['status' => 'error', 'message' => 'Another resident with the same name and birthdate already exists.']);
                }
                return $this->response->setJSON(['status' => 'error', 'message' => 'Update failed: ' . $e->getMessage()]);
            }
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $resident   = $this->residentModel->find($id);
        $profilePic = $this->uploadProfilePicture(
            $this->request->getFile('profile_picture'),
            $this->request->getPost('sitio'),
            $resident['profile_picture'] ?? null
        );
        $data = $this->prepareResidentData($this->request->getPost(), $profilePic);

        try {
            $result = $this->residentModel->update($id, $data);
            if ($result === false) {
                return redirect()->back()->withInput()->with('errors', $this->residentModel->errors());
            }
            return redirect()->to(base_url('resident'))->with('success', 'Resident updated successfully.');
        } catch (\Exception $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return redirect()->back()->with('error', 'Another resident with the same name and birthdate already exists.')->withInput();
            }
            return redirect()->back()->with('error', 'Update failed: ' . $e->getMessage())->withInput();
        }
    }

    // ── Delete ────────────────────────────────────────────────────────
    public function delete($id)
    {
        if ($r = $this->requireLogin()) return $r;

        $resident = $this->residentModel->find($id);
        if (!$resident) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Resident not found']);
            }
            return redirect()->back()->with('error', 'Resident not found');
        }

        // Delete stored profile photo (if any)
        if (!empty($resident['profile_picture']) && file_exists(FCPATH . 'uploads/' . $resident['profile_picture'])) {
            unlink(FCPATH . 'uploads/' . $resident['profile_picture']);
        }

        $deleted = $this->residentModel->delete($id);

        // Support both: AJAX (DataTables) and normal form POST
        if ($this->request->isAJAX()) {
            if ($deleted) {
                return $this->response->setJSON([
                    'status'    => 'success',
                    'message'   => 'Resident deleted successfully.',
                    'csrf_hash' => csrf_hash(),
                ]);
            }
            return $this->response->setJSON(['status' => 'error', 'message' => 'Delete failed']);
        }

        if ($deleted) {
            return redirect()->to(base_url('resident'))->with('success', 'Resident deleted successfully.');
        }
        return redirect()->back()->with('error', 'Delete failed');
    }

    // ── View (now includes age and history) ─────────────────────────────────────
    public function view($id = null)
    {
        if ($r = $this->requireLogin()) return $r;

        if (!$id) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Missing resident ID');
        }

        $resident = $this->db->table('residents r')
            ->select('r.*, h.household_no, h.street_address as household_address, TIMESTAMPDIFF(YEAR, r.birthdate, CURDATE()) as age')
            ->join('households h', 'h.id = r.household_id', 'left')
            ->where('r.id', $id)
            ->where('r.deleted_at IS NULL')
            ->get()
            ->getRowArray();

        if (!$resident) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Resident not found');
        }

        $certificates = $this->db->table('certificates')
            ->select('id, certificate_number, certificate_type, created_at, purpose')
            ->where('resident_id', $id)
            ->orderBy('created_at', 'DESC')
            ->get()->getResultArray();

        $blotterHistory = $this->db->table('blotter_parties bp')
            ->select('br.id, br.case_number, br.incident_type, br.incident_date, br.status, bp.role')
            ->join('blotter_records br', 'br.id = bp.blotter_id')
            ->where('bp.resident_id', $id)
            ->orderBy('br.incident_date', 'DESC')
            ->get()->getResultArray();

        return view('residents/view', [
            'resident'       => $resident,
            'certificates'   => $certificates,
            'blotterHistory' => $blotterHistory,
        ]);    }

    // ── Households by Sitio (AJAX) ────────────────────────────────────
    public function getHouseholdsBySitio()
    {
        $sitio = $this->request->getGet('sitio');
        if (empty($sitio)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Sitio parameter is required']);
        }

        $households = $this->householdModel->where('sitio', $sitio)->findAll();
        return $this->response->setJSON(['status' => 'success', 'data' => $households]);
    }

    // ── Activity Feed (AJAX) ──────────────────────────────────────────
    public function activity($id)
    {
        if ($r = $this->requireLogin()) return $r;

        // Get the resident's name to search logs
        $resident = $this->residentModel->find($id);
        if (!$resident) {
            return $this->response->setJSON(['status' => 'success', 'logs' => []]);
        }

        $fullName = $resident['first_name'] . ' ' . $resident['last_name'];

        $logs = $this->db->table('tbl_logs')
            ->select('ACTION, USER_NAME, DATELOG, TIMELOG')
            ->like('ACTION', $fullName)
            ->orderBy('DATELOG', 'DESC')
            ->orderBy('TIMELOG', 'DESC')
            ->limit(20)
            ->get()->getResultArray();

        return $this->response->setJSON([
            'status' => 'success',
            'logs'   => $logs,
        ]);
    }

    // ── Assign Search (search residents to assign to a household) ─────
    public function assignSearch()
    {
        if ($r = $this->requireLogin()) return $r;

        $householdId   = $this->request->getGet('household_id');
        $keyword       = $this->request->getGet('q') ?? '';
        $filterHouseId = $this->request->getGet('filter_household_id') ?? '';

        // Look up the household's sitio and current head so we can pre-filter and lock head relationship
        $household      = $this->householdModel->find($householdId);
        $householdSitio = $household['sitio'] ?? '';
        $headResidentId = $household['head_resident_id'] ?? null;

        // Use the URL param if explicitly set, otherwise default to the household's sitio
        $filterPurok = $this->request->getGet('filter_purok') ?? $householdSitio;

        $filterStatus  = $this->request->getGet('filter_status') ?? '';

        $builder = $this->db->table('residents')
            ->select('residents.id, residents.first_name, residents.last_name, residents.sitio, residents.household_id, residents.profile_picture, residents.relationship_to_head')
            ->where('residents.deleted_at IS NULL')
            ->where('residents.status', 'active');

        if (!empty($keyword)) {
            $builder->groupStart()
                ->like('residents.first_name', $keyword)
                ->orLike('residents.last_name', $keyword)
                ->groupEnd();
        }
        if (!empty($filterPurok)) {
            $builder->where('residents.sitio', $filterPurok);
        }
        if (!empty($filterHouseId)) {
            $builder->where('residents.household_id', $filterHouseId);
        }
        if ($filterStatus === 'no_household') {
            $builder->where('residents.household_id', null);
        }

        $residents = $builder->orderBy('residents.last_name', 'ASC')->get()->getResultArray();

        return view('households/resident_assign_search', [
            'residents'      => $residents,
            'household_id'   => $householdId,
            'householdSitio' => $householdSitio,
            'headResidentId' => $headResidentId,
            'keyword'        => $keyword,
            'filterPurok'    => $filterPurok,
            'filterHouseId'  => $filterHouseId,
            'filterStatus'   => $filterStatus,
        ]);
    }

    // ── Assign Bulk (assign selected residents to a household) ────────
    public function assignBulk()
    {
        if ($r = $this->requireLogin()) return $r;

        $targetHouseholdId = $this->request->getPost('target_household_id');
        $selectedResidents = $this->request->getPost('selected_residents') ?? [];
        $relationships     = $this->request->getPost('relationships') ?? [];

        if (empty($targetHouseholdId) || empty($selectedResidents)) {
            return redirect()->back()->with('error', 'No residents selected.');
        }
        
        // Determine the current head of the target household so we can preserve/auto-assign their relationship
        $household      = $this->householdModel->find($targetHouseholdId);
        $headResidentId = $household['head_resident_id'] ?? null;

        $count = 0;
        foreach ($selectedResidents as $residentId) {
            $relationship = ($headResidentId && (int)$residentId === (int)$headResidentId)
                ? 'Head'
                : ($relationships[$residentId] ?? null);

            $this->residentModel->update($residentId, [
                'household_id'          => $targetHouseholdId,
                'relationship_to_head'  => $relationship,
                'joined_household_date' => date('Y-m-d'),
            ]);
            $count++;
        }

        $this->logModel->addLog("Assigned {$count} resident(s) to household #{$targetHouseholdId}");

        return redirect()->to(base_url('households/view/' . $targetHouseholdId))
            ->with('success', "{$count} resident(s) assigned successfully.");
    }

    // ── Private Helpers ───────────────────────────────────────────────
    private function uploadProfilePicture($file, $sitio, $currentPicture = null)
    {   
        if ($file && $file->isValid() && !$file->hasMoved()) {
            if ($currentPicture && file_exists(FCPATH . 'uploads/' . $currentPicture)) {
                unlink(FCPATH . 'uploads/' . $currentPicture);
            }

            $folderName = $this->getSitioFolderName($sitio);
            $uploadPath = FCPATH . 'uploads/' . $folderName;

            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            $newName = $file->getRandomName();
            $file->move($uploadPath, $newName);
            return $folderName . '/' . $newName;
        }

        return $currentPicture;
    }

    private function prepareResidentData($postData, $profilePic)
    {
        // Auto‑compute senior citizen if not explicitly set
        $isSenior = isset($postData['is_senior_citizen']) ? 1 : (
            !empty($postData['birthdate']) && (date('Y') - date('Y', strtotime($postData['birthdate']))) >= 60 ? 1 : 0
        );

        return [
            'household_id'         => !empty($postData['household_id']) ? (int)$postData['household_id'] : null,
            'first_name'           => $postData['first_name'],
            'middle_name'          => !empty($postData['middle_name'])          ? $postData['middle_name']          : null,
            'last_name'            => $postData['last_name'],
            'birthdate'            => $postData['birthdate'],
            'sex'                  => $postData['sex'],
            'civil_status'         => !empty($postData['civil_status']) ? strtolower($postData['civil_status']) : 'single',  // ← Default to 'single' to prevent NULL
            'contact_number'       => !empty($postData['contact_number'])       ? $postData['contact_number']       : null,
            'relationship_to_head' => !empty($postData['relationship_to_head']) ? $postData['relationship_to_head'] : null,
            'occupation'           => !empty($postData['occupation'])           ? $postData['occupation']           : null,
            'citizenship'          => !empty($postData['citizenship'])          ? $postData['citizenship']          : null,
            // Remove 'street_address' — it does NOT exist in residents table
            'sitio'                => $postData['sitio'],
            'is_voter'             => isset($postData['is_voter'])        ? 1 : 0,
            'is_pwd'               => isset($postData['is_pwd'])          ? 1 : 0,
            'is_senior_citizen'    => $isSenior,
            'profile_picture'      => $profilePic,
        ];
    }

    private function getSitioFolderName($sitio)
    {
        $folderMap = [
            'Purok Malipayon' => 'purok_malipayon',
            'Purok Masagana'  => 'purok_masagana',
            'Purok Cory'      => 'purok_cory',
            'Purok Kawayan'   => 'purok_kawayan',
            'Purok Pagla-um'  => 'purok_paglaum',
        ];
        return $folderMap[$sitio] ?? 'others';
    }

    // ── Check Duplicate (AJAX) ────────────────────────────────────────
    public function checkDuplicate()
    {
        $firstName = trim($this->request->getGet('first_name') ?? '');
        $lastName  = trim($this->request->getGet('last_name')  ?? '');
        $birthdate = trim($this->request->getGet('birthdate')  ?? '');
        $excludeId = (int)($this->request->getGet('exclude_id') ?? 0);

        if (empty($firstName) || empty($lastName) || empty($birthdate)) {
            return $this->response->setJSON(['duplicate' => false]);
        }

        $builder = $this->db->table('residents')
            ->select('id, first_name, last_name, birthdate, sitio, status')
            ->where('LOWER(first_name)', strtolower($firstName))
            ->where('LOWER(last_name)',  strtolower($lastName))
            ->where('birthdate', $birthdate)
            ->where('deleted_at IS NULL');

        if ($excludeId > 0) {
            $builder->where('id !=', $excludeId);
        }

        $match = $builder->get()->getRowArray();

        if ($match) {
            return $this->response->setJSON([
                'duplicate' => true,
                'id'        => $match['id'],
                'name'      => $match['first_name'] . ' ' . $match['last_name'],
                'birthdate' => $match['birthdate'],
                'sitio'     => $match['sitio'] ?? '—',
                'status'    => $match['status'],
                'view_url'  => base_url('resident/view/' . $match['id']),
            ]);
        }

        return $this->response->setJSON(['duplicate' => false]);
    }

    // ── Quick AJAX status update (general) ─────────────────────────
    public function updateStatus($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $newStatus = $this->request->getPost('status');
        $allowed = ['active', 'inactive', 'deceased', 'transferred'];  // lowercase as per DB

        if (!in_array(strtolower($newStatus), $allowed)) {   // case‑insensitive check
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Invalid status value.',
                'csrf_hash' => csrf_hash(),
            ]);
        }

        $resident = $this->residentModel->find($id);
        if (!$resident) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Resident not found.',
                'csrf_hash' => csrf_hash(),
            ]);
        }

        $this->residentModel->update($id, [
            'status' => strtolower($newStatus)
        ]);

        $this->logModel->addLog(
            "Updated status of {$resident['first_name']} {$resident['last_name']} to {$newStatus}"
        );

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Status updated.',
            'new_status' => $newStatus,
            'csrf_hash' => csrf_hash(),
        ]);
    }

    public function updateMemberStatus($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid request.', 'csrf_hash' => csrf_hash()]);
        }

        $newStatus = $this->request->getPost('member_status');
        // DB enums are lowercase, so cast to lower for comparison
        $allowed = ['active', 'inactive', 'transferred', 'deceased'];

        if (!in_array(strtolower($newStatus), $allowed)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid status value.', 'csrf_hash' => csrf_hash()]);
        }

        $resident = $this->residentModel->find($id);
        if (!$resident) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Resident not found.', 'csrf_hash' => csrf_hash()]);
        }

        $this->residentModel->update($id, [
            'status' => strtolower($newStatus)
        ]);

        $this->logModel->addLog(
            "Updated membership status of {$resident['first_name']} {$resident['last_name']} to {$newStatus}"
        );

        return $this->response->setJSON([
            'status'     => 'success',
            'message'    => 'Membership status updated.',
            'new_status' => $newStatus,
            'csrf_hash'  => csrf_hash(),
        ]);
    }

    // ── Export to CSV ──────────────────────────────────────────────────
    public function exportCsv()
    {
        if ($r = $this->requireLogin()) return $r;

        $selectedPurok = $this->request->getGet('purok') ?? 'all';

        $builder = $this->db->table('residents')
            ->select('residents.first_name, residents.last_name, residents.middle_name, residents.birthdate, residents.sex, residents.civil_status, residents.contact_number, residents.occupation, residents.sitio, residents.is_voter, residents.is_pwd, residents.is_senior_citizen, households.household_no')
            ->join('households', 'households.id = residents.household_id', 'left')
            ->where('residents.deleted_at IS NULL');

        if ($selectedPurok !== 'all') {
            if ($selectedPurok === 'Unassigned') {
                $builder->groupStart()
                    ->where('residents.sitio', null)
                    ->orWhere('residents.sitio', '')
                    ->groupEnd();
            } else {
                $builder->where('residents.sitio', $selectedPurok);
            }
        }

        $residents = $builder->orderBy('residents.last_name', 'ASC')->get()->getResultArray();

        $filename = 'Residents_' . date('Ymd_His') . '.csv';

        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Type: text/csv; charset=UTF-8");

        $file = fopen('php://output', 'w');

        $header = ['First Name', 'Last Name', 'Middle Name', 'Birthdate', 'Sex', 'Civil Status', 'Contact', 'Occupation', 'Purok/Sitio', 'Household No', 'Voter', 'PWD', 'Senior'];
        fputcsv($file, $header);

        foreach ($residents as $r) {
            fputcsv($file, [
                $r['first_name'],
                $r['last_name'],
                $r['middle_name'],
                $r['birthdate'],
                ucfirst($r['sex']),
                $r['civil_status'],
                $r['contact_number'],
                $r['occupation'],
                $r['sitio'],
                $r['household_no'],
                $r['is_voter'] ? 'Yes' : 'No',
                $r['is_pwd'] ? 'Yes' : 'No',
                $r['is_senior_citizen'] ? 'Yes' : 'No'
            ]);
        }

        fclose($file);
        exit;
    }
}
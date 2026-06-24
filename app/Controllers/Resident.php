<?php

namespace App\Controllers;

use App\Models\ResidentModel;
use App\Models\HouseholdModel;
use App\Models\LogModel;
use App\Models\CertificateModel;
use App\Models\BlotterPartyModel;

class Resident extends BaseController
{
    protected $residentModel;
    protected $householdModel;
    protected $logModel;
    protected $certificateModel;
    protected $blotterPartyModel;
    protected $db;

    public function __construct()
    {
        $this->residentModel     = new ResidentModel();
        $this->householdModel    = new HouseholdModel();
        $this->logModel          = new LogModel();
        $this->certificateModel  = new CertificateModel();
        $this->blotterPartyModel = new BlotterPartyModel();
        $this->db                = \Config\Database::connect();
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

        $residents = $this->residentModel->getResidentsWithHousehold($selectedPurok);
        $purokCounts = $this->residentModel->getPurokCounts();

        return view('residents/index', [
            'title'         => 'Residents',
            'residents'     => $residents,
            'purokCounts'   => $purokCounts,
            'selectedPurok' => $selectedPurok,
            'purokList'     => ResidentModel::getPurokList(),
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
            'purokList'            => ResidentModel::getPurokList(),
        ]);
    }

    // ── Store (with duplicate protection & auto senior) ─────────────
    public function store()
    {
        if ($r = $this->requireLogin()) return $r;

        $rules = $this->getValidationRules();

        log_message('debug', 'Resident Store Called - POST Data: ' . json_encode($this->request->getPost()));

        if ($this->request->isAJAX()) {
            if (!$this->validate($rules)) {
                return $this->response->setJSON(['status' => 'error', 'errors' => $this->validator->getErrors()]);
            }
            $birthdate = $this->request->getPost('birthdate');
            if ($birthdate && strtotime($birthdate) > time()) {
                return $this->response->setJSON(['status' => 'error', 'errors' => ['birthdate' => 'Birthdate cannot be in the future.']]);
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
        $birthdate = $this->request->getPost('birthdate');
        if ($birthdate && strtotime($birthdate) > time()) {
            return redirect()->back()->withInput()->with('errors', ['birthdate' => 'Birthdate cannot be in the future.']);
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
            'purokList'  => ResidentModel::getPurokList(),
        ]);
    }

    // ── Update (with duplicate protection & auto senior) ────────────
    public function update($id)
    {
        if ($r = $this->requireLogin()) return $r;

        $rules = $this->getValidationRules();

        if ($this->request->isAJAX()) {
            if (!$this->validate($rules)) {
                return $this->response->setJSON(['status' => 'error', 'errors' => $this->validator->getErrors()]);
            }
            $birthdate = $this->request->getPost('birthdate');
            if ($birthdate && strtotime($birthdate) > time()) {
                return $this->response->setJSON(['status' => 'error', 'errors' => ['birthdate' => 'Birthdate cannot be in the future.']]);
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
                $changes = [];
                foreach ($data as $key => $newValue) {
                    $oldValue = $resident[$key] ?? null;
                    if ($oldValue != $newValue && $key !== 'updated_at') {
                        $changes[] = "$key (" . ($oldValue ?: 'empty') . " -> " . ($newValue ?: 'empty') . ")";
                    }
                }
                $changeStr = !empty($changes) ? ". Changes: " . implode(', ', $changes) : "";
                
                $fullName = $data['first_name'] . ' ' . $data['last_name'];
                $this->logModel->addLog('Updated Resident ' . $fullName . $changeStr);
                
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
        $birthdate = $this->request->getPost('birthdate');
        if ($birthdate && strtotime($birthdate) > time()) {
            return redirect()->back()->withInput()->with('errors', ['birthdate' => 'Birthdate cannot be in the future.']);
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
            $changes = [];
            foreach ($data as $key => $newValue) {
                $oldValue = $resident[$key] ?? null;
                if ($oldValue != $newValue && $key !== 'updated_at') {
                    $changes[] = "$key (" . ($oldValue ?: 'empty') . " -> " . ($newValue ?: 'empty') . ")";
                }
            }
            $changeStr = !empty($changes) ? ". Changes: " . implode(', ', $changes) : "";

            $fullName = $data['first_name'] . ' ' . $data['last_name'];
            $this->logModel->addLog('Updated Resident ' . $fullName . $changeStr);

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

        // Pre-deletion checks for foreign keys
        $blotterPartyModel = new \App\Models\BlotterPartyModel();
        $officialModel     = new \App\Models\OfficialModel();

        if ($blotterPartyModel->where('resident_id', $id)->first() || $officialModel->where('resident_id', $id)->first()) {
            $msg = 'Cannot delete resident. They are currently involved in a blotter case or assigned as an official.';
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['status' => 'error', 'message' => $msg]);
            }
            return redirect()->back()->with('error', $msg);
        }

        // Delete stored profile photo (if any)
        if (!empty($resident['profile_picture']) && file_exists(FCPATH . 'uploads/' . $resident['profile_picture'])) {
            unlink(FCPATH . 'uploads/' . $resident['profile_picture']);
        }

        $fullName = $resident['first_name'] . ' ' . $resident['last_name'];
        $deleted = $this->residentModel->delete($id);

        // Support both: AJAX (DataTables) and normal form POST
        if ($this->request->isAJAX()) {
            if ($deleted) {
                $this->logModel->addLog('Deleted Resident ' . $fullName);
                return $this->response->setJSON([
                    'status'    => 'success',
                    'message'   => 'Resident deleted successfully.',
                    'csrf_hash' => csrf_hash(),
                ]);
            }
            return $this->response->setJSON(['status' => 'error', 'message' => 'Delete failed']);
        }

        if ($deleted) {
            $this->logModel->addLog('Deleted Resident ' . $fullName);
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

        $resident = $this->residentModel->getDetailsWithHousehold($id);

        if (!$resident) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Resident not found');
        }

        $certificates = $this->certificateModel->getByResidentId($id);
        $blotterHistory = $this->blotterPartyModel->getHistoryByResident($id);
        
        $portalAccountModel = new \App\Models\ResidentAccountModel();
        $portalAccount = $portalAccountModel->where('resident_id', $id)->first();

        return view('residents/view', [
            'resident'       => $resident,
            'certificates'   => $certificates,
            'blotterHistory' => $blotterHistory,
            'portalAccount'  => $portalAccount,
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

        $filterStatus  = $this->request->getGet('filter_status') ?? 'no_household';

        $residents = $this->residentModel->searchForAssignment($keyword, $filterPurok, $filterHouseId, $filterStatus);

        return view('households/resident_assign_search', [
            'residents'      => $residents,
            'household_id'   => $householdId,
            'householdSitio' => $householdSitio,
            'headResidentId' => $headResidentId,
            'keyword'        => $keyword,
            'filterPurok'    => $filterPurok,
            'filterHouseId'  => $filterHouseId,
            'filterStatus'   => $filterStatus,
            'purokList'      => ResidentModel::getPurokList(),
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

        $this->db->transStart();

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

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return redirect()->back()->with('error', 'Failed to assign residents to household.');
        }

        $this->logModel->addLog("Assigned {$count} resident(s) to household #{$targetHouseholdId}");

        return redirect()->to(base_url('households/view/' . $targetHouseholdId))
            ->with('success', "{$count} resident(s) assigned successfully.");
    }

    // ── Private Helpers ───────────────────────────────────────────────
    private function getValidationRules()
    {
        return [
            'first_name'      => 'required|min_length[2]|max_length[100]|regex_match[/^[a-zA-ZÀ-ÿ\s\'\-\.]+$/]',
            'last_name'       => 'required|min_length[2]|max_length[100]|regex_match[/^[a-zA-ZÀ-ÿ\s\'\-\.]+$/]',
            'birthdate'       => 'required|valid_date|valid_birthdate',
            'sex'             => 'required|in_list[male,female]',
            'sitio'           => 'required|max_length[100]',
            'contact_number'  => 'permit_empty|regex_match[/^[\d\+\-\s\(\)]+$/]|min_length[7]|max_length[20]',
            'profile_picture' => 'permit_empty|is_image[profile_picture]|max_size[profile_picture,2048]|mime_in[profile_picture,image/jpg,image/jpeg,image/png,image/gif]',
        ];
    }
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

    // ── Bulk Upload ────────────────────────────────────────────────────
    public function bulkUpload()
    {
        if ($r = $this->requireLogin()) return $r;

        return view('residents/bulk_upload', [
            'title' => 'Bulk Upload Residents',
        ]);
    }

    public function processBulkUpload()
    {
        if ($r = $this->requireLogin()) return $r;

        $file = $this->request->getFile('csv_file');

        if (!$file || !$file->isValid()) {
            return redirect()->back()->with('error', 'Please select a valid CSV file to upload.');
        }

        if ($file->getExtension() !== 'csv' && $file->getClientMimeType() !== 'text/csv' && $file->getClientMimeType() !== 'application/vnd.ms-excel') {
            return redirect()->back()->with('error', 'Only CSV files are allowed.');
        }

        $filePath = $file->getTempName();
        $fileHandle = fopen($filePath, 'r');
        
        // Read header
        $header = fgetcsv($fileHandle);
        if (!$header) {
            return redirect()->back()->with('error', 'The uploaded file is empty or invalid.');
        }
        
        $insertedCount = 0;
        $skippedCount = 0;
        $errors = [];
        $row = 2; // starting from row 2 (after header)

        while (($data = fgetcsv($fileHandle, 1000, ",")) !== FALSE) {
            // Check if row matches header length, or at least has the required minimum fields (first, last, birthdate, sex)
            if (count($data) < 4) {
                $skippedCount++;
                $errors[] = "Row $row: Missing columns. Expected at least 4.";
                $row++;
                continue;
            }

            $firstName = trim($data[0] ?? '');
            $lastName = trim($data[1] ?? '');
            $middleName = trim($data[2] ?? '');
            $birthdate = trim($data[3] ?? '');
            $sex = strtolower(trim($data[4] ?? ''));
            $civilStatus = strtolower(trim($data[5] ?? 'single'));
            $contactNumber = trim($data[6] ?? '');
            $occupation = trim($data[7] ?? '');
            $sitio = trim($data[8] ?? '');

            // Basic validation for required fields
            if (empty($firstName) || empty($lastName) || empty($birthdate) || empty($sex)) {
                $skippedCount++;
                $errors[] = "Row $row: Missing required fields (First Name, Last Name, Birthdate, or Sex).";
                $row++;
                continue;
            }

            // Check duplicate
            $duplicateCheck = $this->db->table('residents')
                ->where('LOWER(first_name)', strtolower($firstName))
                ->where('LOWER(last_name)', strtolower($lastName))
                ->where('birthdate', $birthdate)
                ->where('deleted_at IS NULL')
                ->countAllResults();

            if ($duplicateCheck > 0) {
                $skippedCount++;
                $errors[] = "Row $row: Duplicate resident ($firstName $lastName) found. Skipped.";
                $row++;
                continue;
            }

            // Auto-compute senior
            $isSenior = (date('Y') - date('Y', strtotime($birthdate))) >= 60 ? 1 : 0;

            $insertData = [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'middle_name' => empty($middleName) ? null : $middleName,
                'birthdate' => $birthdate,
                'sex' => in_array($sex, ['male', 'female']) ? $sex : 'male',
                'civil_status' => in_array($civilStatus, ['single', 'married', 'widowed', 'separated']) ? $civilStatus : 'single',
                'contact_number' => empty($contactNumber) ? null : $contactNumber,
                'occupation' => empty($occupation) ? null : $occupation,
                'sitio' => $sitio,
                'status' => 'active',
                'is_senior_citizen' => $isSenior,
                'registered_by' => session()->get('user_id') ?? 1,
            ];

            try {
                if ($this->residentModel->insert($insertData)) {
                    $insertedCount++;
                } else {
                    $skippedCount++;
                    $errors[] = "Row $row: Database error on insert.";
                }
            } catch (\Exception $e) {
                $skippedCount++;
                $errors[] = "Row $row: " . $e->getMessage();
            }

            $row++;
        }

        fclose($fileHandle);

        $this->logModel->addLog("Bulk Uploaded Residents: $insertedCount inserted, $skippedCount skipped.");

        $message = "Upload complete! $insertedCount residents inserted. $skippedCount skipped.";
        if (count($errors) > 0) {
            return redirect()->to(base_url('resident/bulk-upload'))->with('success', $message)->with('bulk_errors', $errors);
        }

        return redirect()->to(base_url('resident/bulk-upload'))->with('success', $message);
    }

    public function downloadTemplate()
    {
        if ($r = $this->requireLogin()) return $r;

        $filename = 'Resident_Bulk_Upload_Template.csv';

        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Type: text/csv; charset=UTF-8");

        $file = fopen('php://output', 'w');

        // Note: Keep it simple: only required and standard fields.
        $header = ['First Name', 'Last Name', 'Middle Name', 'Birthdate (YYYY-MM-DD)', 'Sex (Male/Female)', 'Civil Status (Single/Married/Widowed/Separated)', 'Contact Number', 'Occupation', 'Purok/Sitio'];
        fputcsv($file, $header);
        
        // Add a dummy row for reference
        $sample = ['Juan', 'Dela Cruz', 'Perez', '1990-01-01', 'Male', 'Married', '09123456789', 'Farmer', 'Purok Malipayon'];
        fputcsv($file, $sample);

        fclose($file);
        exit;
    }
}
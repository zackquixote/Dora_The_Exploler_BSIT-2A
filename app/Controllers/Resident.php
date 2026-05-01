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
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
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
            ->where('residents.deleted_at', null);

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
            ->where('deleted_at', null)
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
            'first_name'      => 'required|min_length[2]|max_length[100]',
            'last_name'       => 'required|min_length[2]|max_length[100]',
            'birthdate'       => 'required|valid_date',
            'sex'             => 'required|in_list[male,female]',
            'sitio'           => 'required|max_length[100]',
            'profile_picture' => 'is_image[profile_picture]|max_size[profile_picture,2048]|mime_in[profile_picture,image/jpg,image/jpeg,image/png,image/gif]',
        ];

        if ($this->request->isAJAX()) {
            if (!$this->validate($rules)) {
                return $this->response->setJSON(['status' => 'error', 'errors' => $this->validator->getErrors()]);
            }
            $profilePic = $this->uploadProfilePicture($this->request->getFile('profile_picture'), $this->request->getPost('sitio'));
            $data       = $this->prepareResidentData($this->request->getPost(), $profilePic);

            try {
                $this->residentModel->insert($data);
                $fullName = $data['first_name'] . ' ' . $data['last_name'];
                $this->logModel->addLog('Added Resident ' . $fullName);
                return $this->response->setJSON(['status' => 'success', 'redirect' => base_url('resident')]);
            } catch (\Exception $e) {
                if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                    return $this->response->setJSON(['status' => 'error', 'message' => 'A resident with the same name and birthdate already exists.']);
                }
                return $this->response->setJSON(['status' => 'error', 'message' => 'Database error.']);
            }
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $profilePic = $this->uploadProfilePicture($this->request->getFile('profile_picture'), $this->request->getPost('sitio'));
        $data       = $this->prepareResidentData($this->request->getPost(), $profilePic);

        try {
            $this->residentModel->insert($data);
            $fullName = $data['first_name'] . ' ' . $data['last_name'];
            $this->logModel->addLog('Added Resident ' . $fullName);
            return redirect()->to(base_url('resident'))->with('success', 'Resident added successfully.');
        } catch (\Exception $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return redirect()->back()->with('error', 'A resident with the same name and birthdate already exists.')->withInput();
            }
            return redirect()->back()->with('error', 'Database error. Please try again.')->withInput();
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
            'first_name'      => 'required|min_length[2]',
            'last_name'       => 'required|min_length[2]',
            'birthdate'       => 'required|valid_date',
            'sex'             => 'required|in_list[male,female]',
            'sitio'           => 'required|max_length[100]',
            'profile_picture' => 'is_image[profile_picture]|max_size[profile_picture,2048]|mime_in[profile_picture,image/jpg,image/jpeg,image/png,image/gif]',
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
                $this->residentModel->update($id, $data);
                return $this->response->setJSON(['status' => 'success', 'redirect' => base_url('resident')]);
            } catch (\Exception $e) {
                if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                    return $this->response->setJSON(['status' => 'error', 'message' => 'Another resident with the same name and birthdate already exists.']);
                }
                return $this->response->setJSON(['status' => 'error', 'message' => 'Update failed.']);
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
            $this->residentModel->update($id, $data);
            return redirect()->to(base_url('resident'))->with('success', 'Resident updated successfully.');
        } catch (\Exception $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return redirect()->back()->with('error', 'Another resident with the same name and birthdate already exists.')->withInput();
            }
            return redirect()->back()->with('error', 'Update failed.')->withInput();
        }
    }

    // ── Delete ────────────────────────────────────────────────────────
    public function delete($id)
    {
        if ($r = $this->requireLogin()) return $r;

        if ($this->request->isAJAX()) {
            $resident = $this->residentModel->find($id);

            if (!$resident) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Resident not found']);
            }

            if (!empty($resident['profile_picture']) && file_exists(FCPATH . 'uploads/' . $resident['profile_picture'])) {
                unlink(FCPATH . 'uploads/' . $resident['profile_picture']);
            }

            if ($this->residentModel->delete($id)) {
                return $this->response->setJSON([
                    'status'    => 'success',
                    'message'   => 'Resident deleted successfully.',
                    'csrf_hash' => csrf_hash(),
                ]);
            }
        }

        return redirect()->back()->with('error', 'Invalid request');
    }

    // ── View (now includes age) ─────────────────────────────────────
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
            ->where('r.deleted_at', null)
            ->get()
            ->getRowArray();

        if (!$resident) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Resident not found');
        }

        return view('residents/view', ['resident' => $resident]);
    }

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

    // ── Assign Search ─────────────────────────────────────────────────
    public function assignSearch()
    {
        if ($r = $this->requireLogin()) return $r;

        $householdId   = $this->request->getGet('household_id');
        $keyword       = $this->request->getGet('q');
        $filterPurok   = $this->request->getGet('filter_purok');
        $filterHouseId = $this->request->getGet('filter_household_id');

        $builder = $this->residentModel
            ->groupStart()
                ->where('household_id !=', $householdId)
                ->orWhere('household_id IS NULL', null, false)
            ->groupEnd();

        if ($filterPurok)   $builder->where('sitio', $filterPurok);
        if ($filterHouseId) $builder->where('household_id', $filterHouseId);
        if ($keyword) {
            $builder->groupStart()
                ->like('first_name', $keyword)
                ->orLike('last_name', $keyword)
                ->groupEnd();
        }

        return view('households/resident_assign_search', [
            'residents'     => $builder->paginate(20),
            'pager'         => $this->residentModel->pager,
            'household_id'  => $householdId,
            'keyword'       => $keyword,
            'filterPurok'   => $filterPurok,
            'filterHouseId' => $filterHouseId,
        ]);
    }

    // ── Assign Bulk ───────────────────────────────────────────────────
    public function assignBulk()
    {
        if ($r = $this->requireLogin()) return $r;

        $targetHouseholdId = $this->request->getPost('target_household_id');
        $selectedResidents = $this->request->getPost('selected_residents');
        $relationships     = $this->request->getPost('relationships');

        if (empty($selectedResidents)) {
            return redirect()->back()->with('error', 'No residents selected.');
        }

        $successCount = 0;
        foreach ($selectedResidents as $residentId) {
            $relation = $relationships[$residentId] ?? null;
            if ($relation) {
                $this->residentModel->update($residentId, [
                    'household_id'         => $targetHouseholdId,
                    'relationship_to_head' => $relation,
                ]);
                $successCount++;
            }
        }

        return redirect()->back()->with('success', $successCount . ' resident(s) assigned successfully!');
    }

    // ── DataTable List ────────────────────────────────────────────────
    public function list()
    {
        if ($r = $this->requireLogin()) return $r;

        $start       = $this->request->getPost('start') ?? 0;
        $length      = $this->request->getPost('length') ?? 10;
        $searchValue = $this->request->getPost('search')['value'] ?? '';

        $builder = $this->db->table('residents')
            ->select('residents.*, households.household_no')
            ->join('households', 'households.id = residents.household_id', 'left')
            ->where('residents.deleted_at', null);

        if ($searchValue) {
            $builder->groupStart()
                ->like('residents.first_name', $searchValue)
                ->orLike('residents.last_name', $searchValue)
                ->orLike('residents.sitio', $searchValue)
                ->groupEnd();
        }

        $total    = $this->db->table('residents')->where('deleted_at', null)->countAllResults();
        $filtered = $builder->countAllResults(false);
        $data     = $builder->orderBy('residents.id', 'DESC')->limit($length, $start)->get()->getResultArray();

        return $this->response->setJSON([
            'draw'            => intval($this->request->getPost('draw')),
            'recordsTotal'    => $total,
            'recordsFiltered' => $filtered,
            'data'            => $data,
        ]);
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
            'civil_status'         => !empty($postData['civil_status'])         ? $postData['civil_status']         : null,
            'contact_number'       => !empty($postData['contact_number'])       ? $postData['contact_number']       : null,
            'relationship_to_head' => !empty($postData['relationship_to_head']) ? $postData['relationship_to_head'] : null,
            'occupation'           => !empty($postData['occupation'])           ? $postData['occupation']           : null,
            'citizenship'          => !empty($postData['citizenship'])          ? $postData['citizenship']          : null,
            'street_address'       => !empty($postData['street_address'])       ? $postData['street_address']       : null,
            'sitio'                => $postData['sitio'],
            'is_voter'             => isset($postData['is_voter'])        ? 1 : 0,
            'is_pwd'               => isset($postData['is_pwd'])          ? 1 : 0,
            'is_senior_citizen'    => $isSenior,
            'profile_picture'      => $profilePic,
            'status'               => 'active',
            'registered_by'        => session()->get('user_id') ?? 1,
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
    /**
 * Quick AJAX update of resident status.
 */
public function updateStatus($id)
{
    if (!$this->request->isAJAX()) {
        return $this->response->setStatusCode(403);
    }

    $newStatus = $this->request->getPost('status');
    $allowed = ['active', 'inactive', 'deceased', 'transferred'];

    if (!in_array($newStatus, $allowed)) {
        return $this->response->setJSON([
            'status'  => 'error',
            'message' => 'Invalid status value.'
        ]);
    }

    $resident = $this->residentModel->find($id);
    if (!$resident) {
        return $this->response->setJSON([
            'status'  => 'error',
            'message' => 'Resident not found.'
        ]);
    }

    $this->residentModel->update($id, ['status' => $newStatus]);

    $this->logModel->addLog(
        "Updated status of {$resident['first_name']} {$resident['last_name']} to {$newStatus}"
    );

    return $this->response->setJSON([
        'status'  => 'success',
        'message' => 'Status updated.',
        'new_status' => $newStatus
    ]);
}

public function updateMemberStatus($id)
{
    if (!$this->request->isAJAX()) {
        return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid request.']);
    }

    $newStatus = $this->request->getPost('member_status');
    $allowed = ['Active', 'Inactive', 'Transferred', 'Deceased'];

    if (!in_array($newStatus, $allowed)) {
        return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid status value.']);
    }

    $resident = $this->residentModel->find($id);
    if (!$resident) {
        return $this->response->setJSON(['status' => 'error', 'message' => 'Resident not found.']);
    }

    $this->residentModel->update($id, ['member_status' => $newStatus]);

    $this->logModel->addLog(
        "Updated membership status of {$resident['first_name']} {$resident['last_name']} to {$newStatus}"
    );

    return $this->response->setJSON([
        'status'     => 'success',
        'message'    => 'Membership status updated.',
        'new_status' => $newStatus
    ]);
}
}
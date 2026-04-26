<?php

namespace App\Controllers;

use App\Models\ResidentModel;
use App\Models\HouseholdModel;

class Resident extends BaseController
{
    protected $residentModel;
    protected $householdModel;
    protected $db;

    public function __construct()
    {
        $this->residentModel  = new ResidentModel();
        $this->householdModel = new HouseholdModel();
        $this->db             = \Config\Database::connect();
    }

    // ── Auth helper ───────────────────────────────────────────────────
    private function requireLogin()
    {
        if (!session()->get('logged_in')) { 
            return redirect()->to('/login'); // Added closing brace here
        }
        return null;
    }

    // ── Index ─────────────────────────────────────────────────────────
    public function index()
    {
        if ($r = $this->requireLogin()) return $r;

        $selectedPurok = $this->request->getGet('purok') ?? 'all';

        $builder = $this->db->table('residents')
            ->select('residents.*, households.household_no')
            ->join('households', 'households.id = residents.household_id', 'left')
            ->where('residents.deleted_at', null);

        if ($selectedPurok !== 'all') { // Fixed missing colon
            if ($selectedPurok === 'Unassigned') { // Fixed missing colon
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
        foreach ($allResidents as $r) { // Fixed syntax here
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

    // ── Store ─────────────────────────────────────────────────────────
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

            if ($this->residentModel->insert($data)) { // Fixed syntax here
                return $this->response->setJSON(['status' => 'success', 'redirect' => base_url('resident')]);
            }
            return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to save resident.']);
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $profilePic = $this->uploadProfilePicture($this->request->getFile('profile_picture'), $this->request->getPost('sitio'));
        $data       = $this->prepareResidentData($this->request->getPost(), $profilePic);

        if ($this->residentModel->insert($data)) { // Fixed syntax here
            return redirect()->to(base_url('resident'))->with('success', 'Resident added successfully.');
        }

        return redirect()->back()->with('error', 'Failed to save resident.')->withInput();
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

    // ── Update ────────────────────────────────────────────────────────
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

            if ($this->residentModel->update($id, $data)) { // Fixed syntax here
                return $this->response->setJSON(['status' => 'success', 'redirect' => base_url('resident')]);
            }
            return $this->response->setJSON(['status' => 'error', 'message' => 'Update failed.']);
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

        if ($this->residentModel->update($id, $data)) { // Fixed syntax here
            return redirect()->to(base_url('resident'))->with('success', 'Resident updated successfully.');
        }

        return redirect()->back()->with('error', 'Update failed.')->withInput();
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

            if ($this->residentModel->delete($id)) { // Fixed syntax here
                return $this->response->setJSON([
                    'status'    => 'success',
                    'message'   => 'Resident deleted successfully.',
                    'csrf_hash' => csrf_hash(),
                ]);
            }
        }

        return redirect()->back()->with('error', 'Invalid request');
    }

    // ── View ──────────────────────────────────────────────────────────
    public function view($id = null)
    {
        if ($r = $this->requireLogin()) return $r;

        if (!$id) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Missing resident ID');
        }

        $resident = $this->db->table('residents r')
            ->select('r.*, h.household_no, h.street_address as household_address')
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

    // ── List (AJAX/DataTable) ─────────────────────────────────────────
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
            'is_senior_citizen'    => isset($postData['is_senior_citizen']) ? 1 : 0,
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
}
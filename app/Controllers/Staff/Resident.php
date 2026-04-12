<?php

namespace App\Controllers\Staff;

use App\Controllers\BaseController;
use App\Models\ResidentModel;
use App\Models\HouseholdModel;

class Resident extends BaseController
{
    protected $residentModel;
    protected $householdModel;

    public function __construct()
    {
        $this->residentModel = new ResidentModel();
        $this->householdModel = new HouseholdModel();
    }

    // ─── VIEWS ───────────────────────────────────────────────────────────────

    public function index()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        return view('Staff/index', [
            'title' => 'Residents Management'
        ]);
    }

    public function create()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        return view('Staff/create', [
            'title' => 'Add New Resident'
        ]);
    }

    public function edit($id)
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $resident = $this->residentModel->find($id);
        if (!$resident) {
            return redirect()->to(base_url('residents'))->with('error', 'Resident not found.');
        }

        return view('Staff/edit', [
            'title'    => 'Edit Resident',
            'resident' => $resident
        ]);
    }

    // ─── AJAX: DATATABLES LIST ────────────────────────────────────────────────

    public function list()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        }

        $draw        = intval($this->request->getPost('draw') ?? 1);
        $start       = intval($this->request->getPost('start') ?? 0);
        $length      = intval($this->request->getPost('length') ?? 10);
        $searchValue = $this->request->getPost('search')['value'] ?? '';

        $db      = \Config\Database::connect();
        $builder = $db->table('residents r');
        $builder->select('r.*, h.household_no, h.address AS household_address');
        $builder->join('households h', 'h.id = r.household_id', 'left');
        $builder->where('r.status', 'active');

        if (!empty($searchValue)) {
            $builder->groupStart()
                ->like('r.first_name', $searchValue)
                ->orLike('r.last_name', $searchValue)
                ->orLike('r.middle_name', $searchValue)
                ->orLike('r.contact_number', $searchValue)
                ->orLike('h.household_no', $searchValue)
                ->groupEnd();
        }

        $totalRecords    = $db->table('residents')->where('status', 'active')->countAllResults();
        $filteredRecords = $builder->countAllResults(false);

        $builder->orderBy('r.id', 'DESC')->limit($length, $start);
        $data = $builder->get()->getResultArray();

        return $this->response->setJSON([
            'draw'            => $draw,
            'recordsTotal'    => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data'            => $data,
        ]);
    }

    // ─── AJAX: HOUSEHOLDS DROPDOWN ────────────────────────────────────────────

    public function households()
    {
        $households = $this->householdModel
            ->select('id, household_no, street_address AS address')
            ->orderBy('household_no', 'ASC')
            ->findAll();

        return $this->response->setJSON(['status' => 'success', 'data' => $households]);
    }

    // ─── AJAX: STORE (CREATE) ─────────────────────────────────────────────────

    public function store()
    {
        $rules = [
            'first_name'   => 'required|min_length[2]|max_length[100]',
            'last_name'    => 'required|min_length[2]|max_length[100]',
            'birthdate'    => 'required|valid_date',
            'sex'          => 'required|in_list[male,female]',
            'household_id' => 'required|integer',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Validation failed.',
                'errors'  => $this->validator->getErrors(),
            ]);
        }

        $data = [
            'household_id'         => $this->request->getPost('household_id'),
            'first_name'           => $this->request->getPost('first_name'),
            'middle_name'          => $this->request->getPost('middle_name'),
            'last_name'            => $this->request->getPost('last_name'),
            'birthdate'            => $this->request->getPost('birthdate'),
            'sex'                  => $this->request->getPost('sex'),
            'civil_status'         => $this->request->getPost('civil_status'),
            'contact_number'       => $this->request->getPost('contact_number'),
            'relationship_to_head' => $this->request->getPost('relationship_to_head'),
            'is_voter'             => $this->request->getPost('is_voter') ? 1 : 0,
            'is_pwd'               => $this->request->getPost('is_pwd') ? 1 : 0,
            'is_senior_citizen'    => $this->request->getPost('is_senior_citizen') ? 1 : 0,
            'status'               => 'active',
            'registered_by'        => session()->get('user_id') ?? 1,
        ];

        if ($this->residentModel->insert($data)) {
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Resident added successfully.',
            ]);
        }

        return $this->response->setJSON([
            'status'  => 'error',
            'message' => 'Failed to save resident.',
            'errors'  => $this->residentModel->errors(),
        ]);
    }

    // ─── AJAX: SHOW (GET ONE) ─────────────────────────────────────────────────

    public function show($id)
    {
        $db      = \Config\Database::connect();
        $builder = $db->table('residents r');
        $builder->select('r.*, h.household_no, h.address AS household_address');
        $builder->join('households h', 'h.id = r.household_id', 'left');
        $builder->where('r.id', $id);
        $resident = $builder->get()->getRowArray();

        if (!$resident) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Resident not found.']);
        }

        return $this->response->setJSON(['status' => 'success', 'data' => $resident]);
    }

    // ─── AJAX: UPDATE ─────────────────────────────────────────────────────────

    public function update($id)
    {
        $rules = [
            'first_name' => 'required|min_length[2]|max_length[100]',
            'last_name'  => 'required|min_length[2]|max_length[100]',
            'birthdate'  => 'required|valid_date',
            'sex'        => 'required|in_list[male,female]',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Validation failed.',
                'errors'  => $this->validator->getErrors(),
            ]);
        }

        $data = [
            'household_id'         => $this->request->getPost('household_id') ?: null,
            'first_name'           => $this->request->getPost('first_name'),
            'middle_name'          => $this->request->getPost('middle_name'),
            'last_name'            => $this->request->getPost('last_name'),
            'birthdate'            => $this->request->getPost('birthdate'),
            'sex'                  => $this->request->getPost('sex'),
            'civil_status'         => $this->request->getPost('civil_status'),
            'contact_number'       => $this->request->getPost('contact_number'),
            'relationship_to_head' => $this->request->getPost('relationship_to_head'),
            'is_voter'             => $this->request->getPost('is_voter') ? 1 : 0,
            'is_pwd'               => $this->request->getPost('is_pwd') ? 1 : 0,
            'is_senior_citizen'    => $this->request->getPost('is_senior_citizen') ? 1 : 0,
        ];

        if ($this->residentModel->update($id, $data)) {
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Resident updated successfully.',
            ]);
        }

        return $this->response->setJSON([
            'status'  => 'error',
            'message' => 'Update failed.',
            'errors'  => $this->residentModel->errors(),
        ]);
    }

    // ─── AJAX: DELETE ─────────────────────────────────────────────────────────

    public function delete($id)
    {
        if ($this->residentModel->delete($id)) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Resident deleted.']);
        }
        return $this->response->setJSON(['status' => 'error', 'message' => 'Delete failed.']);
    }
}
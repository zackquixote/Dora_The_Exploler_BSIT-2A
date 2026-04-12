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
        $this->residentModel  = new ResidentModel();
        $this->householdModel = new HouseholdModel();
    }

    // ───────────────────────── VIEW ─────────────────────────

    public function index()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        return view('Staff/index', [
            'title' => 'Residents Management'
        ]);
    }

    // ───────────────────────── DATATABLE LIST ─────────────────────────

    public function list()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setStatusCode(401)
                ->setJSON(['error' => 'Unauthorized']);
        }

        $request = $this->request;

        $draw   = (int) ($request->getPost('draw') ?? 1);
        $start  = (int) ($request->getPost('start') ?? 0);
        $length = (int) ($request->getPost('length') ?? 10);

        $searchValue = $request->getPost('search')['value'] ?? '';

        $db = \Config\Database::connect();

        // BASE QUERY
        $builder = $db->table('residents r');
        $builder->select('r.*, h.household_no, h.address AS household_address');
        $builder->join('households h', 'h.id = r.household_id', 'left');
        $builder->where('r.status', 'active');

        // TOTAL RECORDS (NO SEARCH)
        $totalRecords = $db->table('residents')
            ->where('status', 'active')
            ->countAllResults();

        // SEARCH FILTER (CLONE BUILDER SAFELY)
        $filteredBuilder = clone $builder;

        if (!empty($searchValue)) {
            $filteredBuilder->groupStart()
                ->like('r.first_name', $searchValue)
                ->orLike('r.last_name', $searchValue)
                ->orLike('r.middle_name', $searchValue)
                ->orLike('r.contact_number', $searchValue)
                ->orLike('h.household_no', $searchValue)
                ->groupEnd();
        }

        $filteredRecords = $filteredBuilder->countAllResults();

        // DATA QUERY (FRESH BUILDER)
        $dataBuilder = $db->table('residents r');
        $dataBuilder->select('r.*, h.household_no, h.address AS household_address');
        $dataBuilder->join('households h', 'h.id = r.household_id', 'left');
        $dataBuilder->where('r.status', 'active');

        if (!empty($searchValue)) {
            $dataBuilder->groupStart()
                ->like('r.first_name', $searchValue)
                ->orLike('r.last_name', $searchValue)
                ->orLike('r.middle_name', $searchValue)
                ->orLike('r.contact_number', $searchValue)
                ->orLike('h.household_no', $searchValue)
                ->groupEnd();
        }

        $data = $dataBuilder
            ->orderBy('r.id', 'DESC')
            ->limit($length, $start)
            ->get()
            ->getResultArray();

        return $this->response->setJSON([
            'draw'            => $draw,
            'recordsTotal'    => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data'            => $data,
        ]);
    }

    // ───────────────────────── HOUSEHOLDS ─────────────────────────

    public function households()
    {
        $households = $this->householdModel
            ->select('id, household_no, street_address AS address')
            ->orderBy('household_no', 'ASC')
            ->findAll();

        return $this->response->setJSON([
            'status' => 'success',
            'data'   => $households,
            'csrf_hash' => csrf_hash()
        ]);
    }

    // ───────────────────────── STORE ─────────────────────────

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
                'csrf_hash' => csrf_hash()
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

        return $this->residentModel->insert($data)
            ? $this->response->setJSON([
                'status' => 'success',
                'message' => 'Resident added successfully.',
                'csrf_hash' => csrf_hash()
            ])
            : $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to save resident.',
                'csrf_hash' => csrf_hash()
            ]);
    }

    // ───────────────────────── SHOW ─────────────────────────

    public function show($id)
    {
        $db = \Config\Database::connect();

        $resident = $db->table('residents r')
            ->select('r.*, h.household_no, h.address AS household_address')
            ->join('households h', 'h.id = r.household_id', 'left')
            ->where('r.id', $id)
            ->get()
            ->getRowArray();

        if (!$resident) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Resident not found',
                'csrf_hash' => csrf_hash()
            ]);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'data'   => $resident,
            'csrf_hash' => csrf_hash()
        ]);
    }

    // ───────────────────────── UPDATE ─────────────────────────

    public function update($id)
    {
        if (!$this->validate([
            'first_name' => 'required|min_length[2]',
            'last_name'  => 'required|min_length[2]',
            'birthdate'  => 'required|valid_date',
            'sex'        => 'required|in_list[male,female]',
        ])) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $this->validator->getErrors(),
                'csrf_hash' => csrf_hash()
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

        return $this->residentModel->update($id, $data)
            ? $this->response->setJSON([
                'status' => 'success',
                'message' => 'Resident updated successfully',
                'csrf_hash' => csrf_hash()
            ])
            : $this->response->setJSON([
                'status' => 'error',
                'message' => 'Update failed',
                'csrf_hash' => csrf_hash()
            ]);
    }

    // ───────────────────────── DELETE ─────────────────────────

    public function delete($id)
    {
        return $this->residentModel->delete($id)
            ? $this->response->setJSON([
                'status' => 'success',
                'message' => 'Resident deleted',
                'csrf_hash' => csrf_hash()
            ])
            : $this->response->setJSON([
                'status' => 'error',
                'message' => 'Delete failed',
                'csrf_hash' => csrf_hash()
            ]);
    }
}
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

        $draw   = (int) ($this->request->getPost('draw')   ?? 1);
        $start  = (int) ($this->request->getPost('start')  ?? 0);
        $length = (int) ($this->request->getPost('length') ?? 10);
        $searchValue = $this->request->getPost('search')['value'] ?? '';

        $db = \Config\Database::connect();

        // Total active residents (no filters)
        $totalRecords = $db->table('residents')
            ->where('status', 'active')
            ->countAllResults();

        // Filtered count
        $filteredBuilder = $db->table('residents r');
        $filteredBuilder->select('r.id');
        $filteredBuilder->join('households h', 'h.id = r.household_id', 'left');
        $filteredBuilder->where('r.status', 'active');

        if (!empty($searchValue)) {
            $filteredBuilder->groupStart()
                ->like('r.first_name',     $searchValue)
                ->orLike('r.last_name',    $searchValue)
                ->orLike('r.middle_name',  $searchValue)
                ->orLike('r.contact_number', $searchValue)
                ->orLike('h.household_no', $searchValue)
            ->groupEnd();
        }

        $filteredRecords = $filteredBuilder->countAllResults();

        // Data query
        $dataBuilder = $db->table('residents r');
        $dataBuilder->select('r.*, h.household_no, h.street_address AS household_address');
        $dataBuilder->join('households h', 'h.id = r.household_id', 'left');
        $dataBuilder->where('r.status', 'active');

        if (!empty($searchValue)) {
            $dataBuilder->groupStart()
                ->like('r.first_name',     $searchValue)
                ->orLike('r.last_name',    $searchValue)
                ->orLike('r.middle_name',  $searchValue)
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
            'csrf_hash'       => csrf_hash(),
        ]);
    }

    // ───────────────────────── HOUSEHOLDS DROPDOWN ─────────────────────────

    public function households()
    {
        $households = $this->householdModel
            ->select('id, household_no, street_address AS address')
            ->orderBy('household_no', 'ASC')
            ->findAll();

        return $this->response->setJSON([
            'status'    => 'success',
            'data'      => $households,
            'csrf_hash' => csrf_hash(),
        ]);
    }

    // ───────────────────────── STORE ─────────────────────────

    public function store()
    {
        // Validation — household_id is optional (permit_empty)
        $rules = [
            'first_name'   => 'required|min_length[2]|max_length[100]',
            'last_name'    => 'required|min_length[2]|max_length[100]',
            'birthdate'    => 'required|valid_date',
            'sex'          => 'required|in_list[male,female]',
            'household_id' => 'permit_empty|integer',   // ← was required|integer
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status'    => 'error',
                'message'   => 'Validation failed.',
                'errors'    => $this->validator->getErrors(),
                'csrf_hash' => csrf_hash(),
            ]);
        }

        $householdId = $this->request->getPost('household_id');

        $data = [
            'household_id'         => !empty($householdId) ? (int) $householdId : null,
            'first_name'           => $this->request->getPost('first_name'),
            'middle_name'          => $this->request->getPost('middle_name')       ?: null,
            'last_name'            => $this->request->getPost('last_name'),
            'birthdate'            => $this->request->getPost('birthdate'),
            'sex'                  => $this->request->getPost('sex'),
            'civil_status'         => $this->request->getPost('civil_status')      ?: null,
            'contact_number'       => $this->request->getPost('contact_number')    ?: null,
            'relationship_to_head' => $this->request->getPost('relationship_to_head') ?: null,
            'is_voter'             => $this->request->getPost('is_voter')          ? 1 : 0,
            'is_pwd'               => $this->request->getPost('is_pwd')            ? 1 : 0,
            'is_senior_citizen'    => $this->request->getPost('is_senior_citizen') ? 1 : 0,
            'status'               => 'active',
            'registered_by'        => session()->get('user_id') ?? 1,
        ];

        $inserted = $this->residentModel->insert($data, true); // true = return insert ID

        if ($inserted) {
            return $this->response->setJSON([
                'status'    => 'success',
                'message'   => 'Resident added successfully.',
                'id'        => $inserted,
                'csrf_hash' => csrf_hash(),
            ]);
        }

        // If insert returned false, grab model errors for debugging
        return $this->response->setJSON([
            'status'    => 'error',
            'message'   => 'Failed to save resident.',
            'errors'    => $this->residentModel->errors(),
            'csrf_hash' => csrf_hash(),
        ]);
    }

    // ───────────────────────── SHOW ─────────────────────────

    public function show($id)
    {
        $db = \Config\Database::connect();

        $resident = $db->table('residents r')
            ->select('r.*, h.household_no, h.street_address AS household_address')
            ->join('households h', 'h.id = r.household_id', 'left')
            ->where('r.id', $id)
            ->get()
            ->getRowArray();

        if (!$resident) {
            return $this->response->setJSON([
                'status'    => 'error',
                'message'   => 'Resident not found.',
                'csrf_hash' => csrf_hash(),
            ]);
        }

        return $this->response->setJSON([
            'status'    => 'success',
            'data'      => $resident,
            'csrf_hash' => csrf_hash(),
        ]);
    }

    // ───────────────────────── UPDATE ─────────────────────────

    public function update($id)
    {
        $rules = [
            'first_name'   => 'required|min_length[2]',
            'last_name'    => 'required|min_length[2]',
            'birthdate'    => 'required|valid_date',
            'sex'          => 'required|in_list[male,female]',
            'household_id' => 'permit_empty|integer',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status'    => 'error',
                'message'   => 'Validation failed.',
                'errors'    => $this->validator->getErrors(),
                'csrf_hash' => csrf_hash(),
            ]);
        }

        $householdId = $this->request->getPost('household_id');

        $data = [
            'household_id'         => !empty($householdId) ? (int) $householdId : null,
            'first_name'           => $this->request->getPost('first_name'),
            'middle_name'          => $this->request->getPost('middle_name')       ?: null,
            'last_name'            => $this->request->getPost('last_name'),
            'birthdate'            => $this->request->getPost('birthdate'),
            'sex'                  => $this->request->getPost('sex'),
            'civil_status'         => $this->request->getPost('civil_status')      ?: null,
            'contact_number'       => $this->request->getPost('contact_number')    ?: null,
            'relationship_to_head' => $this->request->getPost('relationship_to_head') ?: null,
            'is_voter'             => $this->request->getPost('is_voter')          ? 1 : 0,
            'is_pwd'               => $this->request->getPost('is_pwd')            ? 1 : 0,
            'is_senior_citizen'    => $this->request->getPost('is_senior_citizen') ? 1 : 0,
        ];

        if ($this->residentModel->update($id, $data)) {
            return $this->response->setJSON([
                'status'    => 'success',
                'message'   => 'Resident updated successfully.',
                'csrf_hash' => csrf_hash(),
            ]);
        }

        return $this->response->setJSON([
            'status'    => 'error',
            'message'   => 'Update failed.',
            'errors'    => $this->residentModel->errors(),
            'csrf_hash' => csrf_hash(),
        ]);
    }

    // ───────────────────────── DELETE ─────────────────────────

    public function delete($id)
    {
        if ($this->residentModel->delete($id)) {
            return $this->response->setJSON([
                'status'    => 'success',
                'message'   => 'Resident deleted successfully.',
                'csrf_hash' => csrf_hash(),
            ]);
        }

        return $this->response->setJSON([
            'status'    => 'error',
            'message'   => 'Delete failed.',
            'csrf_hash' => csrf_hash(),
        ]);
    }
}
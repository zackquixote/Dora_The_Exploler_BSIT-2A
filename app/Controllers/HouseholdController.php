<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\HouseholdModel;
use App\Models\ResidentModel;

class HouseholdController extends BaseController
{
    protected $householdModel;
    protected $residentModel;

    public function __construct()
    {
        $this->householdModel = new HouseholdModel();
        $this->residentModel  = new ResidentModel();
    }

    // ─────────────────────────────────────────────
    // INDEX
    // ─────────────────────────────────────────────
    public function index()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        return view('households/index', [
            'title' => 'Households Management'
        ]);
    }

    // ─────────────────────────────────────────────
    // DATA TABLE LIST (FIXED - WORKING 100%)
    // ─────────────────────────────────────────────
    public function list()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setStatusCode(401)
                ->setJSON(['error' => 'Unauthorized']);
        }

        $draw   = intval($this->request->getPost('draw') ?? 1);
        $start  = intval($this->request->getPost('start') ?? 0);
        $length = intval($this->request->getPost('length') ?? 10);

        $search = $this->request->getPost('search')['value'] ?? '';

        $db = \Config\Database::connect();

        // BASE QUERY
        $builder = $db->table('households h')
            ->select('
                h.*,
                CONCAT(
                    COALESCE(r.first_name,""),
                    " ",
                    COALESCE(r.last_name,"")
                ) AS head_name
            ')
            ->join('residents r', 'r.id = h.head_resident_id', 'left');

        // SEARCH FILTER
        if (!empty($search)) {
            $builder->groupStart()
                ->like('h.household_no', $search)
                ->orLike('h.sitio', $search)
                ->orLike('h.street_address', $search)
            ->groupEnd();
        }

        // TOTAL RECORDS (NO FILTER)
        $totalRecords = $db->table('households')->countAllResults();

        // FILTERED COUNT (SAFE CLONE)
        $countBuilder = clone $builder;
        $filteredRecords = $countBuilder->countAllResults();

        // DATA FETCH (SAFE CLONE)
        $dataBuilder = clone $builder;
        $data = $dataBuilder
            ->orderBy('h.id', 'DESC')
            ->limit($length, $start)
            ->get()
            ->getResultArray();

        return $this->response->setJSON([
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data
        ]);
    }

    // ─────────────────────────────────────────────
    // STORE
    // ─────────────────────────────────────────────
    public function store()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setStatusCode(401)
                ->setJSON(['error' => 'Unauthorized']);
        }

        $data = [
            'household_no'     => $this->request->getPost('household_no'),
            'sitio'            => $this->request->getPost('sitio'),
            'street_address'   => $this->request->getPost('street_address'),
            'house_type'       => $this->request->getPost('house_type'),
            'head_resident_id' => $this->request->getPost('head_resident_id') ?: null,
        ];

        if ($this->householdModel->insert($data)) {
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Household added successfully.'
            ]);
        }

        return $this->response->setJSON([
            'status'  => 'error',
            'message' => 'Validation failed.',
            'errors'  => $this->householdModel->errors()
        ]);
    }

    // ─────────────────────────────────────────────
    // SHOW
    // ─────────────────────────────────────────────
    public function show($id)
    {
        if (!session()->get('logged_in')) {
            return $this->response->setStatusCode(401)
                ->setJSON(['error' => 'Unauthorized']);
        }

        $db = \Config\Database::connect();

        $data = $db->table('households h')
            ->select('
                h.*,
                CONCAT(
                    COALESCE(r.first_name,""),
                    " ",
                    COALESCE(r.last_name,"")
                ) AS head_name
            ')
            ->join('residents r', 'r.id = h.head_resident_id', 'left')
            ->where('h.id', $id)
            ->get()
            ->getRowArray();

        if (!$data) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Household not found.'
            ]);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'data'   => $data
        ]);
    }

    // ─────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────
    public function update($id)
    {
        if (!session()->get('logged_in')) {
            return $this->response->setStatusCode(401)
                ->setJSON(['error' => 'Unauthorized']);
        }

        $data = [
            'household_no'     => $this->request->getPost('household_no'),
            'sitio'            => $this->request->getPost('sitio'),
            'street_address'   => $this->request->getPost('street_address'),
            'house_type'       => $this->request->getPost('house_type'),
            'head_resident_id' => $this->request->getPost('head_resident_id') ?: null,
        ];

        if ($this->householdModel->update($id, $data)) {
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Household updated successfully.'
            ]);
        }

        return $this->response->setJSON([
            'status'  => 'error',
            'message' => 'Update failed.',
            'errors'  => $this->householdModel->errors()
        ]);
    }

    // ─────────────────────────────────────────────
    // DELETE
    // ─────────────────────────────────────────────
    public function delete($id)
    {
        if (!session()->get('logged_in')) {
            return $this->response->setStatusCode(401)
                ->setJSON(['error' => 'Unauthorized']);
        }

        // CHECK IF HAS RESIDENTS
        $exists = $this->residentModel
            ->where('household_id', $id)
            ->first();

        if ($exists) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Cannot delete household with residents.'
            ]);
        }

        if ($this->householdModel->delete($id)) {
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Household deleted successfully.'
            ]);
        }

        return $this->response->setJSON([
            'status'  => 'error',
            'message' => 'Delete failed.'
        ]);
    }

    // ─────────────────────────────────────────────
    // OPTIONS
    // ─────────────────────────────────────────────
    public function options()
    {
        $data = $this->householdModel
            ->select('id, household_no, sitio')
            ->findAll();

        return $this->response->setJSON([
            'status' => 'success',
            'data'   => $data
        ]);
    }

    // ─────────────────────────────────────────────
    // RESIDENT OPTIONS
    // ─────────────────────────────────────────────
    public function residentsOptions($householdId = null)
    {
        if (!session()->get('logged_in')) {
            return $this->response->setStatusCode(401)
                ->setJSON(['error' => 'Unauthorized']);
        }

        $builder = $this->residentModel
            ->select('id, first_name, last_name');

        if (!empty($householdId)) {
            $builder->where('household_id', $householdId);
        }

        $residents = $builder
            ->orderBy('last_name', 'ASC')
            ->findAll();

        return $this->response->setJSON([
            'status'  => 'success',
            'data'    => $residents,
            'message' => empty($residents) ? 'No residents found.' : null
        ]);
    }
}
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
        $this->residentModel = new ResidentModel();
    }

    public function index()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        return view('households/index', [
            'title' => 'Households Management'
        ]);
    }

    // DataTables server-side
    public function list()
    {
    

        $request = $this->request->getPost();
        $draw        = intval($request['draw'] ?? 1);
        $start       = intval($request['start'] ?? 0);
        $length      = intval($request['length'] ?? 10);
        $searchValue = $request['search']['value'] ?? '';

        $builder = $this->householdModel->builder();
        $builder->select('households.*, CONCAT(residents.first_name, " ", residents.last_name) as head_name')
                ->join('residents', 'residents.id = households.head_resident_id', 'left');

        if (!empty($searchValue)) {
            $builder->groupStart()
                ->like('household_no', $searchValue)
                ->orLike('sitio', $searchValue)
                ->orLike('street_address', $searchValue)
                ->groupEnd();
        }

        $totalRecords = $this->householdModel->countAllResults(false);
        $filteredRecords = (clone $builder)->countAllResults(false);
        $builder->orderBy('id', 'DESC')->limit($length, $start);
        $data = $builder->get()->getResultArray();

        return $this->response->setJSON([
            'draw'            => $draw,
            'recordsTotal'    => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data'            => $data
        ]);
    }

    public function store()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        }

        $data = [
            'household_no'     => $this->request->getPost('household_no'),
            'sitio'            => $this->request->getPost('sitio'),
            'street_address'   => $this->request->getPost('street_address'),
            'house_type'       => $this->request->getPost('house_type'),
            'head_resident_id' => $this->request->getPost('head_resident_id') ?: null,
        ];

        if ($this->householdModel->insert($data)) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Household added successfully.']);
        }
        return $this->response->setJSON(['status' => 'error', 'message' => 'Validation failed', 'errors' => $this->householdModel->errors()]);
    }

    public function show($id)
    {
        if (!session()->get('logged_in')) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        }

        $household = $this->householdModel->find($id);
        if (!$household) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Not found']);
        }
        return $this->response->setJSON(['status' => 'success', 'data' => $household]);
    }

    public function update($id)
    {
        if (!session()->get('logged_in')) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        }

        $data = [
            'household_no'     => $this->request->getPost('household_no'),
            'sitio'            => $this->request->getPost('sitio'),
            'street_address'   => $this->request->getPost('street_address'),
            'house_type'       => $this->request->getPost('house_type'),
            'head_resident_id' => $this->request->getPost('head_resident_id') ?: null,
        ];

        if ($this->householdModel->update($id, $data)) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Household updated successfully.']);
        }
        return $this->response->setJSON(['status' => 'error', 'message' => 'Update failed', 'errors' => $this->householdModel->errors()]);
    }

    public function delete($id)
    {
        if (!session()->get('logged_in')) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        }

        // Check if there are residents in this household
        $residents = $this->residentModel->where('household_id', $id)->countAllResults();
        if ($residents > 0) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Cannot delete household with existing residents.']);
        }
        if ($this->householdModel->delete($id)) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Household deleted']);
        }
        return $this->response->setJSON(['status' => 'error', 'message' => 'Delete failed']);
    }

    // For dropdowns (Select2)
    public function options()
    {
        $households = $this->householdModel->getOptions();
        return $this->response->setJSON(['status' => 'success', 'data' => $households]);
    }

    // For selecting head resident dropdown in household form
    public function residentsOptions()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        }

        $residents = $this->residentModel->select('id, first_name, last_name')->findAll();
        return $this->response->setJSON(['status' => 'success', 'data' => $residents]);
    }
}
<?php

namespace App\Controllers;

use App\Models\HouseholdModel;
use App\Models\ResidentModel;

class HouseholdController extends BaseController
{
    protected $householdModel;
    protected $residentModel;
    protected $db;

    public function __construct()
    {
        $this->householdModel = new HouseholdModel();
        $this->residentModel  = new ResidentModel();
        $this->db             = \Config\Database::connect();
    }

    // ----------------------------------------------------------------
    // List all households
    // ----------------------------------------------------------------
    public function index()
    {
        $selectedPurok = $this->request->getGet('purok') ?? 'all';

        $builder = $this->db->table('households');
        $builder->select('*');

        if ($selectedPurok !== 'all') {
            $builder->where('sitio', $selectedPurok);
        }

        $builder->orderBy('household_no', 'ASC');
        $households = $builder->get()->getResultArray();

        $householdsData = [];
        foreach ($households as $h) {
            $residentCount = $this->db->table('residents')
                ->where('household_id', $h['id'])
                ->countAllResults();

            $headName = 'Not assigned';
            if (!empty($h['head_resident_id'])) {
                $head = $this->residentModel->find($h['head_resident_id']);
                if ($head) {
                    $headName = $head['first_name'] . ' ' . $head['last_name'];
                }
            }

            $householdsData[] = [
                'id'             => $h['id'],
                'household_no'   => $h['household_no'],
                'sitio'          => $h['sitio'] ?? 'Unassigned',
                'address'        => $h['address'] ?? '',
                'street_address' => $h['street_address'] ?? '',
                'head_name'      => $headName,
                'resident_count' => $residentCount,
                'house_type'     => $h['house_type'] ?? 'N/A',
            ];
        }

        $allHouseholds = $this->db->table('households')
            ->select('sitio')
            ->get()
            ->getResultArray();

        $purokCounts = [];
        foreach ($allHouseholds as $h) {
            $sitio = !empty($h['sitio']) ? $h['sitio'] : 'Unassigned';
            $purokCounts[$sitio] = ($purokCounts[$sitio] ?? 0) + 1;
        }

        return view('households/index', [
            'households'    => $householdsData,
            'purokCounts'   => $purokCounts,
            'selectedPurok' => $selectedPurok,
        ]);
    }

    // ----------------------------------------------------------------
    // Create household form
    // ----------------------------------------------------------------
    public function create()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        return view('households/create', [
            'title' => 'Add Household',
        ]);
    }

    // ----------------------------------------------------------------
    // Store household
    // ----------------------------------------------------------------
    public function store()
    {
        $rules = [
            'household_no'     => 'required|is_unique[households.household_no]',
            'sitio'            => 'required',
            'address'          => 'permit_empty|max_length[255]',
            'street_address'   => 'permit_empty|max_length[255]',
            'head_resident_id' => 'permit_empty|integer',
            'house_type'       => 'permit_empty|max_length[50]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'household_no'     => $this->request->getPost('household_no'),
            'sitio'            => $this->request->getPost('sitio'),
            'address'          => $this->request->getPost('address'),
            'street_address'   => $this->request->getPost('street_address'),
            'head_resident_id' => $this->request->getPost('head_resident_id') ?: null,
            'house_type'       => $this->request->getPost('house_type'),
        ];

        if ($this->householdModel->insert($data)) {
            return redirect()->to('/households')->with('success', 'Household added successfully');
        }

        return redirect()->back()->with('error', 'Failed to add household');
    }

    // ----------------------------------------------------------------
    // Edit household form
    // ----------------------------------------------------------------
    public function edit($id)
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $household = $this->householdModel->find($id);
        if (!$household) {
            return redirect()->to('/households')->with('error', 'Household not found');
        }

        $residentCount = $this->db->table('residents')
            ->where('household_id', $id)
            ->countAllResults();

        return view('households/edit', [
            'title'         => 'Edit Household',
            'household'     => $household,
            'residentCount' => $residentCount,
        ]);
    }

    // ----------------------------------------------------------------
    // Update household
    // ----------------------------------------------------------------
    public function update($id)
    {
        $rules = [
            'household_no'     => 'required',
            'sitio'            => 'required',
            'address'          => 'permit_empty|max_length[255]',
            'street_address'   => 'permit_empty|max_length[255]',
            'head_resident_id' => 'permit_empty|integer',
            'house_type'       => 'permit_empty|max_length[50]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'household_no'     => $this->request->getPost('household_no'),
            'sitio'            => $this->request->getPost('sitio'),
            'address'          => $this->request->getPost('address'),
            'street_address'   => $this->request->getPost('street_address'),
            'head_resident_id' => $this->request->getPost('head_resident_id') ?: null,
            'house_type'       => $this->request->getPost('house_type'),
        ];

        if ($this->householdModel->update($id, $data)) {
            return redirect()->to('/households')->with('success', 'Household updated successfully');
        }

        return redirect()->back()->with('error', 'Failed to update household');
    }

    // ----------------------------------------------------------------
    // View household details
    // ----------------------------------------------------------------
    public function view($id)
    {
        $household = $this->householdModel->find($id);
        if (!$household) {
            return redirect()->to('/households')->with('error', 'Household not found');
        }

        $headResident = null;
        if (!empty($household['head_resident_id'])) {
            $headResident = $this->residentModel->find($household['head_resident_id']);
        }

        $residents = $this->db->table('residents')
            ->where('household_id', $id)
            ->orderBy('id', 'ASC')
            ->get()
            ->getResultArray();

        return view('households/view', [
            'title'         => 'Household Details',
            'household'     => $household,
            'headResident'  => $headResident,
            'residents'     => $residents,
            'residentCount' => count($residents),
        ]);
    }

    // ----------------------------------------------------------------
    // Delete household (AJAX)
    // ----------------------------------------------------------------
    public function delete($id)
    {
        $household = $this->householdModel->find($id);

        if (!$household) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Household not found',
            ]);
        }

        $hasResidents = $this->db->table('residents')
            ->where('household_id', $id)
            ->countAllResults();

        if ($hasResidents > 0) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Cannot delete household with ' . $hasResidents . ' resident(s). Please transfer or delete residents first.',
            ]);
        }

        if ($this->householdModel->delete($id)) {
            return $this->response->setJSON([
                'status'    => 'success',
                'message'   => 'Household deleted successfully',
                'csrf_hash' => csrf_hash(),
            ]);
        }

        return $this->response->setJSON([
            'status'  => 'error',
            'message' => 'Delete failed',
        ]);
    }

    // ----------------------------------------------------------------
    // AJAX - get households by sitio for dropdowns/search
    // ----------------------------------------------------------------
    public function getHouseholdsBySitio()
    {
        $sitio = $this->request->getGet('sitio') ?? $this->request->getPost('sitio');

        if (empty($sitio)) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Sitio is required',
            ]);
        }

        try {
            $households = $this->householdModel
                ->where('sitio', $sitio)
                ->orderBy('household_no', 'ASC')
                ->findAll();

            return $this->response->setJSON([
                'status' => 'success',
                'data'   => $households,
                'count'  => count($households),
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Database error: ' . $e->getMessage(),
            ]);
        }
    }

    // ----------------------------------------------------------------
    // AJAX - get households by sitio (used elsewhere)
    // ----------------------------------------------------------------
    public function getBySitio()
    {
        $sitio = $this->request->getPost('sitio');

        if ($sitio) {
            $households = $this->householdModel
                ->where('sitio', $sitio)
                ->orderBy('household_no', 'ASC')
                ->findAll();

            return $this->response->setJSON([
                'status'     => 'success',
                'households' => $households,
            ]);
        }

        return $this->response->setJSON([
            'status'  => 'error',
            'message' => 'Invalid request',
        ]);
    }
}

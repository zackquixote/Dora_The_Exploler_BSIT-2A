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
        $this->residentModel = new ResidentModel();
        $this->db = \Config\Database::connect();
    }

    // List all households
    public function index()
    {
        $selectedPurok = $this->request->getGet('purok') ?? 'all';
        
        // Build the query - FIXED: Select all columns properly
        $builder = $this->db->table('households');
        $builder->select('*');
        
        if ($selectedPurok !== 'all') {
            $builder->where('sitio', $selectedPurok);
        }
        
        $builder->orderBy('household_no', 'ASC');
        $households = $builder->get()->getResultArray();
        
        // Debug: Check if households exist
        log_message('info', 'Households found: ' . count($households));
        
        // Get resident counts for each household and head name
        $householdsData = [];
        foreach ($households as $h) {
            // Get resident count
            $residentCount = $this->db->table('residents')
                ->where('household_id', $h['id'])
                ->countAllResults();
            
            // Get head resident name
            $headName = 'Not assigned';
            if (!empty($h['head_resident_id'])) {
                $head = $this->residentModel->find($h['head_resident_id']);
                if ($head) {
                    $headName = $head['first_name'] . ' ' . $head['last_name'];
                }
            }
            
            $householdsData[] = [
                'id' => $h['id'],
                'household_no' => $h['household_no'],
                'sitio' => $h['sitio'] ?? 'Unassigned',
                'address' => $h['address'] ?? '',
                'street_address' => $h['street_address'] ?? '',
                'head_name' => $headName,
                'resident_count' => $residentCount,
                'house_type' => $h['house_type'] ?? 'N/A'
            ];
        }
        
        // Get purok counts for statistics
        $allHouseholds = $this->db->table('households')->select('sitio')->get()->getResultArray();
        $purokCounts = [];
        if (!empty($allHouseholds)) {
            foreach ($allHouseholds as $h) {
                $sitio = !empty($h['sitio']) ? $h['sitio'] : 'Unassigned';
                if (isset($purokCounts[$sitio])) {
                    $purokCounts[$sitio]++;
                } else {
                    $purokCounts[$sitio] = 1;
                }
            }
        }
        
        return view('households/index', [
            'households' => $householdsData,
            'purokCounts' => $purokCounts,
            'selectedPurok' => $selectedPurok
        ]);
    }

    // Create household form
    public function create()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $residents = $this->residentModel->orderBy('last_name', 'ASC')->findAll();
        
        return view('households/create', [
            'title' => 'Add Household',
            'residents' => $residents
        ]);
    }

    // Store household
    public function store()
    {
        $rules = [
            'household_no' => 'required|is_unique[households.household_no]',
            'sitio' => 'required',
            'address' => 'permit_empty|max_length[255]',
            'street_address' => 'permit_empty|max_length[255]',
            'head_resident_id' => 'permit_empty|integer',
            'house_type' => 'permit_empty|max_length[50]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'household_no' => $this->request->getPost('household_no'),
            'sitio' => $this->request->getPost('sitio'),
            'address' => $this->request->getPost('address'),
            'street_address' => $this->request->getPost('street_address'),
            'head_resident_id' => $this->request->getPost('head_resident_id') ?: null,
            'house_type' => $this->request->getPost('house_type')
        ];

        if ($this->householdModel->insert($data)) {
            return redirect()->to('/households')->with('success', 'Household added successfully');
        }

        return redirect()->back()->with('error', 'Failed to add household');
    }

    // Edit household form
    public function edit($id)
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $household = $this->householdModel->find($id);
        if (!$household) {
            return redirect()->to('/households')->with('error', 'Household not found');
        }

        $residents = $this->residentModel->orderBy('last_name', 'ASC')->findAll();
        $residentCount = $this->db->table('residents')
            ->where('household_id', $id)
            ->countAllResults();
        
        return view('households/edit', [
            'title' => 'Edit Household',
            'household' => $household,
            'residents' => $residents,
            'residentCount' => $residentCount
        ]);
    }

    // Update household
    public function update($id)
    {
        $rules = [
            'household_no' => 'required',
            'sitio' => 'required',
            'address' => 'permit_empty|max_length[255]',
            'street_address' => 'permit_empty|max_length[255]',
            'head_resident_id' => 'permit_empty|integer',
            'house_type' => 'permit_empty|max_length[50]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'household_no' => $this->request->getPost('household_no'),
            'sitio' => $this->request->getPost('sitio'),
            'address' => $this->request->getPost('address'),
            'street_address' => $this->request->getPost('street_address'),
            'head_resident_id' => $this->request->getPost('head_resident_id') ?: null,
            'house_type' => $this->request->getPost('house_type')
        ];

        if ($this->householdModel->update($id, $data)) {
            return redirect()->to('/households')->with('success', 'Household updated successfully');
        }

        return redirect()->back()->with('error', 'Failed to update household');
    }

    // View household details
    public function view($id)
    {
        $household = $this->householdModel->find($id);
        if (!$household) {
            return redirect()->to('/households')->with('error', 'Household not found');
        }
        
        // Get head resident
        $headResident = null;
        if (!empty($household['head_resident_id'])) {
            $headResident = $this->residentModel->find($household['head_resident_id']);
        }
        
        // Get all residents in this household
        $residents = $this->db->table('residents')
            ->where('household_id', $id)
            ->orderBy('id', 'ASC')
            ->get()
            ->getResultArray();
        
        return view('households/view', [
            'title' => 'Household Details',
            'household' => $household,
            'headResident' => $headResident,
            'residents' => $residents,
            'residentCount' => count($residents)
        ]);
    }

    // Delete household
public function delete($id)
{
    // Check if it's an AJAX request
    if ($this->request->isAJAX()) {
        $household = $this->householdModel->find($id);
        
        if (!$household) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Household not found'
            ]);
        }
        
        // Check if household has residents
        $hasResidents = $this->db->table('residents')
            ->where('household_id', $id)
            ->countAllResults();
        
        if ($hasResidents > 0) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Cannot delete household with ' . $hasResidents . ' resident(s). Please transfer or delete residents first.'
            ]);
        }

        if ($this->householdModel->delete($id)) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Household deleted successfully',
                'csrf_hash' => csrf_hash()
            ]);
        }
        
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Delete failed'
        ]);
    }
    
    // If not AJAX, redirect back
    return redirect()->back()->with('error', 'Invalid request');
}

    // API endpoint for AJAX - get households by sitio
    public function getBySitio()
    {
        if ($this->request->isAJAX()) {
            $sitio = $this->request->getPost('sitio');
            
            if ($sitio) {
                $households = $this->householdModel
                    ->where('sitio', $sitio)
                    ->orderBy('household_no', 'ASC')
                    ->findAll();
                
                return $this->response->setJSON([
                    'status' => 'success',
                    'households' => $households
                ]);
            }
        }
        
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Invalid request'
        ]);
    }
}
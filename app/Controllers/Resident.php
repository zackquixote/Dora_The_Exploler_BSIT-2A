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
        $this->residentModel = new ResidentModel();
        $this->householdModel = new HouseholdModel();
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        $selectedPurok = $this->request->getGet('purok') ?? 'all';
        
        $builder = $this->db->table('residents')
            ->select('residents.*, households.household_no')
            ->join('households', 'households.id = residents.household_id', 'left');
        
        if ($selectedPurok !== 'all') {
            if ($selectedPurok === 'Unassigned') {
                $builder->where('residents.sitio IS NULL', null, false);
            } else {
                $builder->where('residents.sitio', $selectedPurok);
            }
        }
        
        $residents = $builder->orderBy('residents.id', 'DESC')->get()->getResultArray();
        
        // Get purok counts
        $allResidents = $this->db->table('residents')->select('sitio')->get()->getResultArray();
        $purokCounts = [];
        foreach ($allResidents as $r) {
            $sitio = !empty($r['sitio']) ? $r['sitio'] : 'Unassigned';
            $purokCounts[$sitio] = ($purokCounts[$sitio] ?? 0) + 1;
        }
        
        return view('residents/index', [  // Changed to 'residents/index'
            'residents' => $residents,
            'purokCounts' => $purokCounts,
            'selectedPurok' => $selectedPurok
        ]);
    }

    public function create()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        // Get household_id from URL if passed
        $householdId = $this->request->getGet('household_id');
        
        $households = $this->householdModel
            ->orderBy('household_no', 'ASC')
            ->findAll();

        return view('residents/create', [  // Changed to 'residents/create'
            'title'      => 'Add Resident',
            'households' => $households,
            'preselectedHousehold' => $householdId
        ]);
    }

    public function store()
    {
        $rules = [
            'first_name'   => 'required|min_length[2]|max_length[100]',
            'last_name'    => 'required|min_length[2]|max_length[100]',
            'birthdate'    => 'required|valid_date',
            'sex'          => 'required|in_list[male,female]',
            'household_id' => 'required|integer',
            'occupation'   => 'permit_empty|max_length[100]',
            'citizenship'  => 'permit_empty|max_length[100]',
            'street_address' => 'permit_empty|max_length[255]',
            'sitio'        => 'required|max_length[100]',
            'profile_picture' => 'is_image[profile_picture]|max_size[profile_picture,2048]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $profilePic = null;
        $file = $this->request->getFile('profile_picture');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move('uploads', $newName);
            $profilePic = $newName;
        }

        $householdId = $this->request->getPost('household_id');

        $data = [
            'household_id'         => !empty($householdId) ? (int)$householdId : null,
            'first_name'           => $this->request->getPost('first_name'),
            'middle_name'          => $this->request->getPost('middle_name') ?: null,
            'last_name'            => $this->request->getPost('last_name'),
            'birthdate'            => $this->request->getPost('birthdate'),
            'sex'                  => $this->request->getPost('sex'),
            'civil_status'         => $this->request->getPost('civil_status') ?: null,
            'contact_number'       => $this->request->getPost('contact_number') ?: null,
            'relationship_to_head' => $this->request->getPost('relationship_to_head') ?: null,
            'occupation'           => $this->request->getPost('occupation') ?: null,
            'citizenship'          => $this->request->getPost('citizenship') ?: null,
            'street_address'       => $this->request->getPost('street_address') ?: null,
            'sitio'                => $this->request->getPost('sitio') ?: null,
            'is_voter'             => $this->request->getPost('is_voter') ? 1 : 0,
            'is_pwd'               => $this->request->getPost('is_pwd') ? 1 : 0,
            'is_senior_citizen'    => $this->request->getPost('is_senior_citizen') ? 1 : 0,
            'profile_picture'      => $profilePic,
            'status'               => 'active',
            'registered_by'        => session()->get('user_id') ?? 1,
        ];

        if ($this->residentModel->insert($data)) {
            return redirect()->to(base_url('resident'))->with('success', 'Resident added successfully.');
        }

        return redirect()->back()->with('error', 'Failed to save resident.')->withInput();
    }

    public function edit($id)
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $resident = $this->residentModel->find($id);
        if (!$resident) {
            return redirect()->to('/resident')->with('error', 'Resident not found');
        }

        $households = $this->householdModel->orderBy('household_no', 'ASC')->findAll();

        return view('residents/edit', [  // Changed to 'residents/edit'
            'title'      => 'Edit Resident',
            'resident'   => $resident,
            'households' => $households
        ]);
    }

    public function update($id)
    {
        $rules = [
            'first_name'   => 'required|min_length[2]',
            'last_name'    => 'required|min_length[2]',
            'birthdate'    => 'required|valid_date',
            'sex'          => 'required|in_list[male,female]',
            'household_id' => 'required|integer',
            'occupation'   => 'permit_empty|max_length[100]',
            'citizenship'  => 'permit_empty|max_length[100]',
            'street_address' => 'permit_empty|max_length[255]',
            'sitio'        => 'required|max_length[100]',
            'profile_picture' => 'is_image[profile_picture]|max_size[profile_picture,2048]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $resident = $this->residentModel->find($id);
        if (!$resident) {
            return redirect()->back()->with('error', 'Resident not found.');
        }

        $profilePic = $resident['profile_picture'];
        $file = $this->request->getFile('profile_picture');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            if ($profilePic && file_exists('uploads/' . $profilePic)) {
                unlink('uploads/' . $profilePic);
            }
            $newName = $file->getRandomName();
            $file->move('uploads', $newName);
            $profilePic = $newName;
        }

        $householdId = $this->request->getPost('household_id');

        $data = [
            'household_id'         => !empty($householdId) ? (int)$householdId : null,
            'first_name'           => $this->request->getPost('first_name'),
            'middle_name'          => $this->request->getPost('middle_name') ?: null,
            'last_name'            => $this->request->getPost('last_name'),
            'birthdate'            => $this->request->getPost('birthdate'),
            'sex'                  => $this->request->getPost('sex'),
            'civil_status'         => $this->request->getPost('civil_status') ?: null,
            'contact_number'       => $this->request->getPost('contact_number') ?: null,
            'relationship_to_head' => $this->request->getPost('relationship_to_head') ?: null,
            'occupation'           => $this->request->getPost('occupation') ?: null,
            'citizenship'          => $this->request->getPost('citizenship') ?: null,
            'street_address'       => $this->request->getPost('street_address') ?: null,
            'sitio'                => $this->request->getPost('sitio') ?: null,
            'is_voter'             => $this->request->getPost('is_voter') ? 1 : 0,
            'is_pwd'               => $this->request->getPost('is_pwd') ? 1 : 0,
            'is_senior_citizen'    => $this->request->getPost('is_senior_citizen') ? 1 : 0,
            'profile_picture'      => $profilePic,
        ];

        if ($this->residentModel->update($id, $data)) {
            return redirect()->to(base_url('resident'))->with('success', 'Resident updated successfully.');
        }

        return redirect()->back()->with('error', 'Update failed.')->withInput();
    }

  public function delete($id)
{
    // Check if it's an AJAX request
    if ($this->request->isAJAX()) {
        $resident = $this->residentModel->find($id);
        
        if (!$resident) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Resident not found'
            ]);
        }
        
        // Delete profile picture if exists
        if (!empty($resident['profile_picture']) && file_exists('uploads/' . $resident['profile_picture'])) {
            unlink('uploads/' . $resident['profile_picture']);
        }
        
        // Delete the resident
        if ($this->residentModel->delete($id)) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Resident deleted successfully.',
                'csrf_hash' => csrf_hash()
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to delete resident.'
            ]);
        }
    }
    
    // If not AJAX, redirect back
    return redirect()->back()->with('error', 'Invalid request');
}
    public function view($id = null)
    {
        if (!$id) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Missing resident ID");
        }

        $resident = $this->db->table('residents r')
            ->select('r.*, h.household_no, h.street_address as household_address')
            ->join('households h', 'h.id = r.household_id', 'left')
            ->where('r.id', $id)
            ->get()
            ->getRowArray();

        if (!$resident) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Resident not found");
        }

        return view('residents/view', ['resident' => $resident]);  // Changed to 'residents/view'
    }

    public function getHouseholdsBySitio()
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
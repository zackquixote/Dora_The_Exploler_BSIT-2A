<?php

namespace App\Controllers;

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

 public function index()
{
    if (!session()->get('logged_in')) {
        return redirect()->to('/login');
    }

    // This loads the DataTable (residents list), NOT the dashboard
    return view('residents/index', [
        'title' => 'Residents Management'
    ]);
}
    // Server-side DataTable handler
  public function list()
{
    $db = \Config\Database::connect();
    $request = $this->request;

    $draw = (int) ($request->getPost('draw') ?? 1);
    $start = (int) ($request->getPost('start') ?? 0);
    $length = (int) ($request->getPost('length') ?? 10);
    $searchValue = $request->getPost('search')['value'] ?? '';

    // Total records
    $totalRecords = $db->table('residents')->countAll();

    // Build query
    $builder = $db->table('residents r');
    $builder->select('r.*, h.household_no');
    $builder->join('households h', 'h.id = r.household_id', 'left');
    
    // Apply search
    if (!empty($searchValue)) {
        $builder->groupStart()
            ->like('r.first_name', $searchValue)
            ->orLike('r.last_name', $searchValue)
            ->orLike('r.middle_name', $searchValue)
            ->orLike('r.occupation', $searchValue)
            ->groupEnd();
    }
    
    // Get filtered count
    $recordsFiltered = $builder->countAllResults(false);
    
    // Apply order and limit
    $builder->orderBy('r.id', 'DESC');
    $builder->limit($length, $start);
    
    // Execute query
    $residents = $builder->get()->getResultArray();

    // Format data for DataTable
    $data = [];
    foreach ($residents as $r) {
        // Full name
        $fullName = $r['first_name'];
        if (!empty($r['middle_name'])) {
            $fullName .= ' ' . $r['middle_name'];
        }
        $fullName .= ' ' . $r['last_name'];
        
        // Age calculation
        $age = '';
        if (!empty($r['birthdate'])) {
            $birthDate = new \DateTime($r['birthdate']);
            $today = new \DateTime('today');
            $age = $birthDate->diff($today)->y;
        }
        
        // Profile image
        $profileImage = !empty($r['profile_picture']) 
            ? base_url('uploads/' . $r['profile_picture']) 
            : base_url('assets/img/default.png');
        
        // Badges
        $voterBadge = $r['is_voter'] ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-secondary">No</span>';
        $seniorBadge = $r['is_senior_citizen'] ? '<span class="badge bg-info">Senior</span>' : '';
        $pwdBadge = $r['is_pwd'] ? '<span class="badge bg-warning">PWD</span>' : '';
        $flags = trim($seniorBadge . ' ' . $pwdBadge);
        
        $data[] = [
            'id' => $r['id'],
            'profile' => '<img src="' . $profileImage . '" width="40" height="40" class="rounded-circle">',
            'full_name' => $fullName,
            'sex' => ucfirst($r['sex']),
            'age' => $age,
            'civil_status' => ucfirst($r['civil_status'] ?? ''),
            'household_no' => $r['household_no'] ?? '-',
            'occupation' => $r['occupation'] ?? '-',
            'citizenship' => $r['citizenship'] ?? '-',
            'voter' => $voterBadge,
            'flags' => $flags,
            'actions' => '
                <a href="' . base_url('resident/view/' . $r['id']) . '" class="btn btn-sm btn-info">View</a>
                <a href="' . base_url('resident/edit/' . $r['id']) . '" class="btn btn-sm btn-warning">Edit</a>
                <button class="btn btn-sm btn-danger delete-resident" data-id="' . $r['id'] . '">Delete</button>
            '
        ];
    }
    
    // Return JSON response
    return $this->response->setJSON([
        'draw' => $draw,
        'recordsTotal' => $totalRecords,
        'recordsFiltered' => $recordsFiltered,
        'data' => $data
    ]);
}

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

    public function create()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $households = $this->householdModel
            ->orderBy('household_no', 'ASC')
            ->findAll();

        return view('residents/create', [
            'title'      => 'Add Resident',
            'households' => $households
        ]);
    }

    public function store()
    {
        $rules = [
            'first_name'   => 'required|min_length[2]|max_length[100]',
            'last_name'    => 'required|min_length[2]|max_length[100]',
            'birthdate'    => 'required|valid_date',
            'sex'          => 'required|in_list[male,female]',
            'household_id' => 'permit_empty|integer',
            'occupation'   => 'permit_empty|max_length[100]',
            'citizenship'  => 'permit_empty|max_length[100]',
            'street_address' => 'permit_empty|max_length[255]',
            'sitio'        => 'permit_empty|max_length[100]',
            'profile_picture' => 'uploaded[profile_picture]|is_image[profile_picture]|max_size[profile_picture,2048]'
        ];

        if (!$this->validate($rules)) {
            session()->setFlashdata('error', 'Validation failed: ' . json_encode($this->validator->getErrors()));
            return redirect()->back()->withInput();
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
            session()->setFlashdata('success', 'Resident added successfully.');
            return redirect()->to(base_url('resident'));
        }

        session()->setFlashdata('error', 'Failed to save resident.');
        return redirect()->back()->withInput();
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

        return view('residents/edit', [
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
            'household_id' => 'permit_empty|integer',
            'occupation'   => 'permit_empty|max_length[100]',
            'citizenship'  => 'permit_empty|max_length[100]',
            'street_address' => 'permit_empty|max_length[255]',
            'sitio'        => 'permit_empty|max_length[100]',
            'profile_picture' => 'is_image[profile_picture]|max_size[profile_picture,2048]'
        ];

        if (!$this->validate($rules)) {
            session()->setFlashdata('error', 'Validation failed.');
            return redirect()->back()->withInput();
        }

        $resident = $this->residentModel->find($id);
        if (!$resident) {
            session()->setFlashdata('error', 'Resident not found.');
            return redirect()->back();
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
            session()->setFlashdata('success', 'Resident updated successfully.');
            return redirect()->to(base_url('resident'));
        }

        session()->setFlashdata('error', 'Update failed.');
        return redirect()->back()->withInput();
    }

    public function delete($id)
    {
        $resident = $this->residentModel->find($id);
        if ($resident && !empty($resident['profile_picture']) && file_exists('uploads/' . $resident['profile_picture'])) {
            unlink('uploads/' . $resident['profile_picture']);
        }

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

    public function view($id = null)
    {
        if (!$id) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Missing resident ID");
        }

        $db = \Config\Database::connect();
        $resident = $db->table('residents r')
            ->select('r.*, h.household_no, h.street_address as household_address')
            ->join('households h', 'h.id = r.household_id', 'left')
            ->where('r.id', $id)
            ->get()
            ->getRowArray();

        if (!$resident) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Resident not found");
        }

        return view('residents/view', ['resident' => $resident]);
    }
}
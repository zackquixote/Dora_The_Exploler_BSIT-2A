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

    public function index()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        return view('Staff/index', [
            'title' => 'Residents Management'
        ]);
    }

    // ✅ CORRECTED server-side DataTable handler
    public function list()
    {
        $db = \Config\Database::connect();
        $request = $this->request;

        $draw        = $request->getPost('draw');
        $start       = $request->getPost('start');
        $length      = $request->getPost('length');
        $searchValue = $request->getPost('search')['value'] ?? '';
        $orderColumn = $request->getPost('order')[0]['column'] ?? 0;
        $orderDir    = $request->getPost('order')[0]['dir'] ?? 'desc';

        $columns = [
            'r.id', 'r.first_name', 'r.middle_name', 'r.last_name', 'r.sex',
            'r.birthdate', 'r.civil_status', 'h.household_no', 'r.occupation',
            'r.citizenship', 'r.is_voter', 'r.is_senior_citizen', 'r.is_pwd',
            'r.profile_picture'
        ];

        $builder = $db->table('residents r')
            ->select('r.*, h.household_no')
            ->join('households h', 'h.id = r.household_id', 'left');

        // Apply search
        if (!empty($searchValue)) {
            $builder->groupStart()
                ->like('r.first_name', $searchValue)
                ->orLike('r.middle_name', $searchValue)
                ->orLike('r.last_name', $searchValue)
                ->orLike('r.occupation', $searchValue)
                ->orLike('r.citizenship', $searchValue)
                ->groupEnd();
        }

        // Count total records (without filter)
        $totalRecords = $builder->countAllResults(false);

        // Apply ordering
        if (isset($columns[$orderColumn])) {
            $builder->orderBy($columns[$orderColumn], $orderDir);
        } else {
            $builder->orderBy('r.id', 'desc');
        }

        // Pagination
        $builder->limit($length, $start);
        $residents = $builder->get()->getResultArray();

        // Count filtered records (same as total for simplicity, but you can add a separate count with search)
        $recordsFiltered = $totalRecords;
        if (!empty($searchValue)) {
            // Re-run count with search conditions
            $filteredBuilder = $db->table('residents r')
                ->join('households h', 'h.id = r.household_id', 'left');
            $filteredBuilder->groupStart()
                ->like('r.first_name', $searchValue)
                ->orLike('r.middle_name', $searchValue)
                ->orLike('r.last_name', $searchValue)
                ->orLike('r.occupation', $searchValue)
                ->orLike('r.citizenship', $searchValue)
                ->groupEnd();
            $recordsFiltered = $filteredBuilder->countAllResults();
        }

        $data = [];
        foreach ($residents as $r) {
            $data[] = [
                'id'                 => $r['id'],
                'first_name'         => $r['first_name'],
                'middle_name'        => $r['middle_name'] ?? '',
                'last_name'          => $r['last_name'],
                'sex'                => $r['sex'],
                'birthdate'          => $r['birthdate'],
                'civil_status'       => $r['civil_status'],
                'household_no'       => $r['household_no'] ?? null,
                'occupation'         => $r['occupation'],
                'citizenship'        => $r['citizenship'],
                'is_voter'           => (int)($r['is_voter'] ?? 0),
                'is_senior_citizen'  => (int)($r['is_senior_citizen'] ?? 0),
                'is_pwd'             => (int)($r['is_pwd'] ?? 0),
                'profile_picture'    => $r['profile_picture'] ?? null
            ];
        }

        return $this->response->setJSON([
            'draw'            => intval($draw),
            'recordsTotal'    => $totalRecords,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $data,
            'csrf_hash'       => csrf_hash()
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

        return view('Staff/create', [
            'title'      => 'Add Resident',
            'households' => $households
        ]);
    }

    // ✅ STORE with address fields and profile picture
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

        // Handle profile picture upload
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
            return redirect()->to(base_url('staff/residents'));
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
            return redirect()->to('/staff/residents')->with('error', 'Resident not found');
        }

        $households = $this->householdModel->orderBy('household_no', 'ASC')->findAll();

        return view('Staff/edit', [
            'title'      => 'Edit Resident',
            'resident'   => $resident,
            'households' => $households
        ]);
    }

    // ✅ UPDATE with address fields and profile picture
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

        // Handle profile picture upload
        $profilePic = $resident['profile_picture']; // keep old by default
        $file = $this->request->getFile('profile_picture');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            // Delete old file if exists
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
            return redirect()->to(base_url('staff/residents'));
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

        return view('Staff/view', ['resident' => $resident]);
    }
}
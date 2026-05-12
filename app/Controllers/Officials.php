<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\BarangaySettingsModel;
use App\Models\OfficialModel;
use App\Models\ResidentModel;
use App\Models\LogModel;

/**
 * Officials Controller
 *
 * Displays the current set of barangay officials (public-facing index).
 * CRUD operations (create/edit/delete) are handled here and protected
 * by the adminOnly filter via Routes.php.
 */
class Officials extends BaseController
{
    protected $settingsModel;
    protected $officialModel;
    protected $residentModel;
    protected $logModel;
    protected $db;

    public function __construct()
    {
        $this->settingsModel = new BarangaySettingsModel();
        $this->officialModel = new OfficialModel();
        $this->residentModel = new ResidentModel();
        $this->logModel      = new LogModel();
        $this->db            = \Config\Database::connect();
    }

    /**
     * Public list of active barangay officials.
     */
    public function index()
    {
        $settings = $this->settingsModel->first();

        $officials = $this->db->table('officials')
            ->where('is_active', 1)
            ->orderBy('FIELD(position, "Punong Barangay","Secretary","Treasurer","SK Chairperson",
                          "Kagawad 1","Kagawad 2","Kagawad 3","Kagawad 4","Kagawad 5","Kagawad 6","Kagawad 7")')
            ->get()
            ->getResultArray();

        foreach ($officials as &$off) {
            if (!empty($off['resident_id'])) {
                $resident = $this->residentModel->find($off['resident_id']);
                if ($resident) {
                    $off['photo'] = $resident['profile_picture'] ?? $off['photo'];
                }
            }
            // Term status flag
            $off['term_expired'] = false;
            $off['term_expiring_soon'] = false;
            if (!empty($off['term_end'])) {
                $daysLeft = (int) ceil((strtotime($off['term_end']) - time()) / 86400);
                if ($daysLeft < 0)  $off['term_expired']       = true;
                elseif ($daysLeft <= 30) $off['term_expiring_soon'] = true;
                $off['term_days_left'] = $daysLeft;
            }
        }

        return view('officials/index', [
            'settings'  => $settings,
            'officials' => $officials,
        ]);
    }

    /**
     * Show create form for a new official.
     */
    public function create()
    {
        $residents = $this->residentModel
            ->where('status', 'active')
            ->orderBy('last_name', 'ASC')
            ->findAll();

        $positions = [
            'Punong Barangay', 'Secretary', 'Treasurer', 'SK Chairperson',
            'Kagawad 1', 'Kagawad 2', 'Kagawad 3',
            'Kagawad 4', 'Kagawad 5', 'Kagawad 6', 'Kagawad 7',
        ];

        return view('officials/create', [
            'residents' => $residents,
            'positions' => $positions,
        ]);
    }

    /**
     * Store a new official record.
     */
    public function store()
    {
        $rules = [
            'position'  => 'required|max_length[100]',
            'full_name' => 'required|max_length[200]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $residentId = $this->request->getPost('resident_id') ?: null;
        $fullName   = trim($this->request->getPost('full_name'));

        // If a resident is linked, use their name
        if ($residentId) {
            $resident = $this->residentModel->find($residentId);
            if ($resident) {
                $fullName = trim($resident['first_name'] . ' ' . $resident['last_name']);
            }
        }

        // Handle photo upload
        $photo = null;
        $file  = $this->request->getFile('photo');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move(FCPATH . 'uploads/officials', $newName);
            $photo = 'officials/' . $newName;
        }

        $this->officialModel->insert([
            'resident_id'    => $residentId,
            'position'       => $this->request->getPost('position'),
            'full_name'      => $fullName,
            'contact_number' => $this->request->getPost('contact_number') ?: null,
            'photo'          => $photo,
            'is_active'      => 1,
            'term_start'     => $this->request->getPost('term_start') ?: null,
            'term_end'       => $this->request->getPost('term_end') ?: null,
        ]);

        $this->logModel->addLog("Added official: {$fullName} as " . $this->request->getPost('position'));

        return redirect()->to('officials')->with('success', 'Official added successfully.');
    }

    /**
     * Show edit form for an existing official.
     */
    public function edit($id)
    {
        $official = $this->officialModel->find($id);
        if (!$official) {
            return redirect()->to('officials')->with('error', 'Official not found.');
        }

        $residents = $this->residentModel
            ->where('status', 'active')
            ->orderBy('last_name', 'ASC')
            ->findAll();

        $positions = [
            'Punong Barangay', 'Secretary', 'Treasurer', 'SK Chairperson',
            'Kagawad 1', 'Kagawad 2', 'Kagawad 3',
            'Kagawad 4', 'Kagawad 5', 'Kagawad 6', 'Kagawad 7',
        ];

        return view('officials/edit', [
            'official'  => $official,
            'residents' => $residents,
            'positions' => $positions,
        ]);
    }

    /**
     * Update an existing official record.
     */
    public function update($id)
    {
        $official = $this->officialModel->find($id);
        if (!$official) {
            return redirect()->to('officials')->with('error', 'Official not found.');
        }

        $rules = [
            'position'  => 'required|max_length[100]',
            'full_name' => 'required|max_length[200]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $residentId = $this->request->getPost('resident_id') ?: null;
        $fullName   = trim($this->request->getPost('full_name'));

        if ($residentId) {
            $resident = $this->residentModel->find($residentId);
            if ($resident) {
                $fullName = trim($resident['first_name'] . ' ' . $resident['last_name']);
            }
        }

        // Handle photo upload
        $photo = $official['photo'];
        $file  = $this->request->getFile('photo');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            // Delete old photo if it exists
            if (!empty($photo) && file_exists(FCPATH . 'uploads/' . $photo)) {
                unlink(FCPATH . 'uploads/' . $photo);
            }
            $newName = $file->getRandomName();
            $file->move(FCPATH . 'uploads/officials', $newName);
            $photo = 'officials/' . $newName;
        }

        $this->officialModel->update($id, [
            'resident_id'    => $residentId,
            'position'       => $this->request->getPost('position'),
            'full_name'      => $fullName,
            'contact_number' => $this->request->getPost('contact_number') ?: null,
            'photo'          => $photo,
            'is_active'      => (int) $this->request->getPost('is_active'),
            'term_start'     => $this->request->getPost('term_start') ?: null,
            'term_end'       => $this->request->getPost('term_end') ?: null,
        ]);

        $this->logModel->addLog("Updated official: {$fullName}");

        return redirect()->to('officials')->with('success', 'Official updated successfully.');
    }

    /**
     * Delete an official record.
     */
    public function delete($id)
    {
        $official = $this->officialModel->find($id);
        if (!$official) {
            return redirect()->to('officials')->with('error', 'Official not found.');
        }

        // Delete photo file if present
        if (!empty($official['photo']) && file_exists(FCPATH . 'uploads/' . $official['photo'])) {
            unlink(FCPATH . 'uploads/' . $official['photo']);
        }

        $this->officialModel->delete($id);
        $this->logModel->addLog("Deleted official: {$official['full_name']}");

        return redirect()->to('officials')->with('success', 'Official deleted.');
    }
}

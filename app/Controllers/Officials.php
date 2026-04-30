<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\BarangaySettingsModel;
use App\Models\ResidentModel;

class Officials extends BaseController
{
    protected $settingsModel;
    protected $residentModel;
    protected $db;

    public function __construct()
    {
        $this->settingsModel = new BarangaySettingsModel();
        $this->residentModel = new ResidentModel();
        $this->db = \Config\Database::connect();
    }

    /**
     * Public list of assigned barangay officials.
     *
     * Reads official_assignments, joins resident data, and builds a clean array
     * for the view.
     */
    public function index()
    {
        // Get barangay settings (name, etc.)
        $settings = $this->settingsModel->first();

        // 1. Fetch all assignments
        $assignmentRows = $this->db->table('official_assignments')->get()->getResultArray();

        $displayList = [];

        // 2. For each assignment, retrieve the resident’s name, photo and purok
        foreach ($assignmentRows as $row) {
            $resident = $this->residentModel->find($row['resident_id']);
            if ($resident) {
                $displayList[] = [
                    'full_name' => $resident['first_name'] . ' ' . $resident['last_name'],
                    'position'  => $row['position_name'],
                    'photo'     => $resident['profile_picture'] ?? 'default.png',
                    'purok'     => $resident['sitio'],
                ];
            }
        }

        // 3. Pass data to the public officials view
        return view('officials/index', [
            'settings'  => $settings,
            'officials' => $displayList
        ]);
    }
}
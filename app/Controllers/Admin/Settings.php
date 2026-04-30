<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\BarangaySettingsModel;
use App\Models\ResidentModel;

class Settings extends BaseController
{
    protected $settingsModel;
    protected $residentModel;
    protected $db;

    public function __construct()
    {
        $this->settingsModel = new BarangaySettingsModel();
        $this->residentModel = new ResidentModel();
        // Direct database connection for Query Builder and raw queries
        $this->db = \Config\Database::connect();
    }

    /**
     * Load the Settings page with all required data.
     *
     * Gathers:
     * - Barangay basic info
     * - All active residents (for dropdowns)
     * - Distinct puroks (for filtering)
     * - Current official assignments with ID and full name (for preselection)
     * - Full list of position names
     */
    public function index()
    {
        // 1. Barangay basic info – fallback to empty placeholders if no record
        $settings = $this->settingsModel->first();
        if (!$settings) {
            $settings = [
                'barangay_name'  => '',
                'municipality'   => '',
                'province'       => '',
                'contact_number' => ''
            ];
        }

        // 2. Active residents sorted by last name – populate all dropdowns
        $residents = $this->residentModel
                          ->where('status', 'active')
                          ->orderBy('last_name', 'ASC')
                          ->findAll();

        // 3. Unique purok list for filter dropdown
        $puroks = $this->residentModel
                       ->select('sitio')
                       ->distinct()
                       ->where('sitio !=', '')
                       ->orderBy('sitio', 'ASC')
                       ->findAll();

        // 4. Current assignments with resident ID and full name
        $assignments = [];
        $rows = $this->db->table('official_assignments')->get()->getResultArray();
        foreach ($rows as $row) {
            $resident = $this->residentModel->find($row['resident_id']);
            $fullName = $resident
                        ? $resident['first_name'] . ' ' . $resident['last_name']
                        : '';
            $assignments[$row['position_name']] = [
                'id'   => $row['resident_id'],
                'name' => $fullName
            ];
        }

        // 5. Full list of all official positions
        $positionsList = [
            'Punong Barangay', 'Secretary', 'Treasurer', 'SK Chairperson',
            'Kagawad 1', 'Kagawad 2', 'Kagawad 3', 'Kagawad 4',
            'Kagawad 5', 'Kagawad 6', 'Kagawad 7'
        ];

        // 6. Pass everything to the view
        return view('admin/settings/barangay', [
            'settings'      => $settings,
            'residents'     => $residents,
            'puroks'        => $puroks,
            'assignments'   => $assignments,
            'positionsList' => $positionsList,
        ]);
    }

    /**
     * Process form submission: update barangay info and official assignments.
     *
     * Steps:
     *  1. Update basic barangay settings (name, municipality, etc.)
     *  2. Collect all assigned resident IDs from POST data.
     *  3. Validate that no resident holds multiple positions.
     *  4. Delete existing assignments and insert new ones inside a transaction.
     */
    public function update()
    {
        // 1. Update basic barangay information (record with id = 1)
        $basicData = [
            'barangay_name'  => $this->request->getPost('barangay_name'),
            'municipality'   => $this->request->getPost('municipality'),
            'province'       => $this->request->getPost('province'),
            'contact_number' => $this->request->getPost('contact_number'),
        ];
        $this->settingsModel->update(1, $basicData);

        // 2. Gather all position → resident ID pairs from form
        $positionsToSave = [
            'Punong Barangay' => $this->request->getPost('captain_id'),
            'Secretary'       => $this->request->getPost('secretary_id'),
            'Treasurer'       => $this->request->getPost('treasurer_id'),
            'SK Chairperson'  => $this->request->getPost('sk_chair_id'),
        ];
        for ($i = 1; $i <= 7; $i++) {
            $positionsToSave["Kagawad $i"] = $this->request->getPost("kagawad_{$i}_id");
        }

        // 3. Duplicate check – no resident may hold multiple positions
        $filledIds = array_filter($positionsToSave);
        if (count($filledIds) !== count(array_unique($filledIds))) {
            return redirect()->back()->with('error', 'One person cannot hold multiple positions.');
        }

        // 4. Save assignments in a transaction for data integrity
        $this->db->transBegin();

        // Delete all existing assignments – raw DELETE is safe and privilege‑friendly
        $this->db->query('DELETE FROM official_assignments');

        // Insert the new set
        foreach ($positionsToSave as $position => $residentId) {
            if (!empty($residentId)) {
                $this->db->table('official_assignments')->insert([
                    'position_name' => $position,
                    'resident_id'   => $residentId
                ]);
            }
        }

        // Check transaction status
        if ($this->db->transStatus() === false) {
            $this->db->transRollback();
            return redirect()->back()->with('error', 'Database error. Please try again.');
        }

        $this->db->transCommit();

        return redirect()->to('admin/settings')
                         ->with('success', 'Official assignments updated successfully.');
    }
}
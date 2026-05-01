<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\BarangaySettingsModel;
use App\Models\ResidentModel;

/**
 * Settings Controller
 * 
 * Manages barangay information and official assignments.
 * Now uses only the 'officials' table (normalised, with is_active flag).
 */
class Settings extends BaseController
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
     * Load the Settings page with all required data.
     *
     * Gathers:
     * - Barangay basic info
     * - All active residents (for dropdowns)
     * - Distinct puroks (for filtering)
     * - Current official assignments (from officials table, is_active=1)
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

        // 4. Current assignments from officials table (is_active = 1)
        $activeOfficials = $this->db->table('officials')
            ->where('is_active', 1)
            ->get()
            ->getResultArray();

        $assignments = [];
        foreach ($activeOfficials as $off) {
            $assignments[$off['position']] = [
                'id'   => $off['resident_id'],
                'name' => $off['full_name']
            ];
        }

        // 5. Full list of all official positions
        $positionsList = [
            'Punong Barangay',
            'Secretary',
            'Treasurer',
            'SK Chairperson',
            'Kagawad 1', 'Kagawad 2', 'Kagawad 3',
            'Kagawad 4', 'Kagawad 5', 'Kagawad 6', 'Kagawad 7'
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
     *  1. Update basic barangay settings.
     *  2. Map form positions to resident IDs.
     *  3. Validate no duplicate assignments.
     *  4. Mark all current officials inactive, then reactivate/insert the new set.
     *  5. Optionally sync barangay_settings captain_id etc. (if still needed).
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

        // 2. Map positions to resident IDs from the form
        $positionsToSave = [
            'Punong Barangay' => $this->request->getPost('captain_id'),
            'Secretary'       => $this->request->getPost('secretary_id'),
            'Treasurer'       => $this->request->getPost('treasurer_id'),
            'SK Chairperson'  => $this->request->getPost('sk_chair_id'),
        ];
        for ($i = 1; $i <= 7; $i++) {
            $positionsToSave["Kagawad $i"] = $this->request->getPost("kagawad_{$i}_id");
        }

        // 3. Validate no resident holds multiple positions
        $filledIds = array_filter($positionsToSave);
        if (count($filledIds) !== count(array_unique($filledIds))) {
            return redirect()->back()->with('error', 'One person cannot hold multiple positions.');
        }

        // 4. Transaction: safely update the officials table
        $this->db->transBegin();

        // 4a. Deactivate all currently active officials
        $this->db->table('officials')->update(['is_active' => 0], ['is_active' => 1]);

        // 4b. For each position with an assigned resident, reactivate/insert
        foreach ($positionsToSave as $position => $residentId) {
            if (empty($residentId)) continue;

            // Get resident's full name (certificates need a ready-to-print name)
            $resident = $this->residentModel->find($residentId);
            $fullName = $resident
                        ? trim($resident['first_name'] . ' ' . $resident['last_name'])
                        : 'Unknown';

            // Check if this position already exists (inactive record)
            $existing = $this->db->table('officials')
                        ->where('position', $position)
                        ->get()
                        ->getRow();

            if ($existing) {
                // Reactivate and update
                $this->db->table('officials')
                    ->where('id', $existing->id)
                    ->update([
                        'resident_id' => $residentId,
                        'full_name'   => $fullName,
                        'is_active'   => 1,
                    ]);
            } else {
                // Insert new official
                $this->db->table('officials')->insert([
                    'position'    => $position,
                    'resident_id' => $residentId,
                    'full_name'   => $fullName,
                    'is_active'   => 1,
                ]);
            }
        }

        // 4c. Commit or rollback
        if ($this->db->transStatus() === false) {
            $this->db->transRollback();
            return redirect()->back()->with('error', 'Database error. Please try again.');
        }

        $this->db->transCommit();

        // (Optional) Sync barangay_settings captain_id etc. if you still rely on them.
        // If not needed, ignore.
        $this->settingsModel->update(1, [
             'captain_id'   => $positionsToSave['Punong Barangay'],
             'secretary_id' => $positionsToSave['Secretary'],
             'treasurer_id' => $positionsToSave['Treasurer'],
         ]);

        return redirect()->to('admin/settings')
                         ->with('success', 'Official assignments updated successfully.');
    }
}
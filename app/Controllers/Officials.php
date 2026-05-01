<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\BarangaySettingsModel;
use App\Models\ResidentModel;

/**
 * Officials Controller (Public‑facing)
 * 
 * Displays the current set of barangay officials.
 * Reads from the 'officials' table (only active ones).
 */
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
     * Public list of active barangay officials.
     */
    public function index()
    {
        $settings = $this->settingsModel->first();

        // Fetch only active officials, ordered by position for grouping
        $officials = $this->db->table('officials')
            ->where('is_active', 1)
            ->orderBy('FIELD(position, "Punong Barangay","Secretary","Treasurer","SK Chairperson",
                          "Kagawad 1","Kagawad 2","Kagawad 3","Kagawad 4","Kagawad 5","Kagawad 6","Kagawad 7")')
            ->get()
            ->getResultArray();

        // Optionally attach resident's profile picture from residents table
        // (if you prefer the photo stored in residents, merge it here)
        foreach ($officials as &$off) {
            if (!empty($off['resident_id'])) {
                $resident = $this->residentModel->find($off['resident_id']);
                if ($resident) {
                    // Use resident's profile_picture as photo fallback
                    $off['photo'] = $resident['profile_picture'] ?? null;
                }
            }
        }

        return view('officials/index', [
            'settings'  => $settings,
            'officials' => $officials
        ]);
    }
}   
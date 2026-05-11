<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\ResidentModel;
use App\Models\HouseholdModel;
use App\Models\BlotterModel;

/**
 * --------------------------------------------------------------------
 * SearchController
 * --------------------------------------------------------------------
 * Handles controller operations and data logic for the application.
 */
class SearchController extends Controller
{
    /**
     * Execute index functionality.
     *
     * @return mixed
     */
    public function index()
    {
        $query = $this->request->getGet('q') ?? '';
        $results = [];

        if (strlen($query) >= 2) {
            // 1. Search Residents
            $residentModel = new ResidentModel();
            $residents = $residentModel
                ->groupStart()
                    ->like('first_name', $query)
                    ->orLike('last_name', $query)
                ->groupEnd()
                ->findAll(5);

            foreach ($residents as $r) {
                $results[] = [
                    'type'  => 'Resident',
                    'title' => $r['first_name'] . ' ' . $r['last_name'],
                    'desc'  => $r['sitio'] . ' • ' . $r['sex'],
                    'icon'  => 'fa-user',
                    'color' => 'blue',
                    'url'   => base_url('resident/view/' . $r['id'])
                ];
            }

            // 2. Search Households
            $householdModel = new HouseholdModel();
            $households = $householdModel
                ->select('households.*, CONCAT(residents.first_name, " ", residents.last_name) as head_name')
                ->join('residents', 'residents.id = households.head_resident_id', 'left')
                ->groupStart()
                    ->like('households.household_no', $query)
                    ->orLike('CONCAT(residents.first_name, " ", residents.last_name)', $query)
                ->groupEnd()
                ->findAll(5);

            foreach ($households as $h) {
                $head = $h['head_name'] ? $h['head_name'] : 'Unassigned';
                $results[] = [
                    'type'  => 'Household',
                    'title' => $h['household_no'],
                    'desc'  => 'Head: ' . $head . ' • ' . ($h['sitio'] ?? 'N/A'),
                    'icon'  => 'fa-home',
                    'color' => 'teal',
                    'url'   => base_url('households/view/' . $h['id'])
                ];
            }

            // 3. Search Blotter
            $blotterModel = new BlotterModel();
            $blotters = $blotterModel
                ->groupStart()
                    ->like('case_number', $query)
                    ->orLike('incident_type', $query)
                ->groupEnd()
                ->findAll(5);

            foreach ($blotters as $b) {
                $results[] = [
                    'type'  => 'Blotter Case',
                    'title' => $b['case_number'],
                    'desc'  => $b['incident_type'] . ' • Status: ' . $b['status'],
                    'icon'  => 'fa-gavel',
                    'color' => 'rose',
                    'url'   => base_url('blotter/view/' . $b['id'])
                ];
            }
        }

        return $this->response->setJSON($results);
    }
}

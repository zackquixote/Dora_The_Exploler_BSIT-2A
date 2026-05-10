<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\ResidentModel;
use App\Models\HouseholdModel;
use App\Models\BlotterModel;

class SearchController extends Controller
{
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
                ->groupStart()
                    ->like('household_no', $query)
                    ->orLike('head_name', $query)
                ->groupEnd()
                ->findAll(5);

            foreach ($households as $h) {
                $results[] = [
                    'type'  => 'Household',
                    'title' => $h['household_no'],
                    'desc'  => 'Head: ' . $h['head_name'] . ' • ' . $h['sitio'],
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

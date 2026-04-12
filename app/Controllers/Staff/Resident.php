<?php

namespace App\Controllers\Staff;

use App\Controllers\BaseController;
use App\Models\ResidentModel;

class Resident extends BaseController
{
    protected $residentModel;

    public function __construct()
    {
        $this->residentModel = new ResidentModel();
    }

    public function index()
    {
        return view('Staff/index', [
            'title' => 'Residents Management',
        ]);
    }

    public function list()
    {
        // You can add pagination here later if needed
        $residents = $this->residentModel->findAll();
        
        // Calculate age dynamically for the view (since we removed it from DB)
        foreach ($residents as &$resident) {
            $resident['age'] = $this->calculateAge($resident['birthdate']);
        }

        return $this->response->setJSON([
            'data' => $residents
        ]);
    }

    public function store()
    {
        $data = [
            'household_id'        => $this->request->getPost('household_id'),
            'first_name'          => $this->request->getPost('first_name'),
            'middle_name'         => $this->request->getPost('middle_name'),
            'last_name'           => $this->request->getPost('last_name'),
            'birthdate'           => $this->request->getPost('birthdate'),
            'sex'                 => $this->request->getPost('sex'), // Changed from 'gender'
            'civil_status'        => $this->request->getPost('civil_status'),
            'contact_number'      => $this->request->getPost('contact_number'),
            'occupation'          => $this->request->getPost('occupation'),
            'relationship_to_head'=> $this->request->getPost('relationship_to_head'), // Added
            'is_voter'            => $this->request->getPost('is_voter') ? 1 : 0,
            'is_senior_citizen'   => $this->request->getPost('is_senior_citizen') ? 1 : 0,
            'is_pwd'              => $this->request->getPost('is_pwd') ? 1 : 0, // Added
            'status'              => $this->request->getPost('status') ?? 'active', // Added
            'registered_by'       => session()->get('user_id'),
        ];

        // Attempt to insert and handle validation errors
        if ($this->residentModel->insert($data)) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Resident added successfully.']);
        }

        return $this->response->setJSON([
            'status' => 'error', 
            'message' => 'Failed to save resident.', 
            'errors' => $this->residentModel->errors() // Return validation errors
        ]);
    }

    public function show($id)
    {
        $resident = $this->residentModel->find($id);

        if (!$resident) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Resident not found.']);
        }
        
        // Add calculated age to the single record view
        $resident['age'] = $this->calculateAge($resident['birthdate']);

        return $this->response->setJSON(['status' => 'success', 'data' => $resident]);
    }

    public function update($id)
    {
        $data = [
            'household_id'        => $this->request->getPost('household_id'),
            'first_name'          => $this->request->getPost('first_name'),
            'middle_name'         => $this->request->getPost('middle_name'),
            'last_name'           => $this->request->getPost('last_name'),
            'birthdate'           => $this->request->getPost('birthdate'),
            'sex'                 => $this->request->getPost('sex'), // Changed from 'gender'
            'civil_status'        => $this->request->getPost('civil_status'),
            'contact_number'      => $this->request->getPost('contact_number'),
            'occupation'          => $this->request->getPost('occupation'),
            'relationship_to_head'=> $this->request->getPost('relationship_to_head'), // Added
            'is_voter'            => $this->request->getPost('is_voter') ? 1 : 0,
            'is_senior_citizen'   => $this->request->getPost('is_senior_citizen') ? 1 : 0,
            'is_pwd'              => $this->request->getPost('is_pwd') ? 1 : 0, // Added
            'status'              => $this->request->getPost('status') ?? 'active', // Added
        ];

        // Note: We do not update 'registered_by' or 'registered_at'

        if ($this->residentModel->update($id, $data)) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Resident updated successfully.']);
        }

        return $this->response->setJSON([
            'status' => 'error', 
            'message' => 'Failed to update resident.',
            'errors' => $this->residentModel->errors()
        ]);
    }

    public function delete($id)
    {
        // Consider using Soft Deletes in the model instead of permanent deletion
        if ($this->residentModel->delete($id)) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Resident deleted.']);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to delete resident.']);
    }

    // Helper function to calculate age since we removed it from the DB
    private function calculateAge($birthdate)
    {
        if (empty($birthdate)) return 0;
        return date_diff(date_create($birthdate), date_create('today'))->y;
    }
}
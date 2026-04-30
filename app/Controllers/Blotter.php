<?php

namespace App\Controllers;

use App\Models\BlotterModel;
use App\Models\LogModel;
use App\Models\UserModel;      // NEW

/**
 * Blotter Controller
 * 
 * Manages barangay blotter (incident/complaint) records.
 * 
 * METHODS:
 * - index(): Lists all blotters with creator names.
 * - create(): Displays form to add a new blotter (with resident autocomplete).
 * - store(): Validates and saves a new blotter, logs the action.
 * - view($id): Shows details of a single blotter.
 * - update($id): Updates status and action taken.
 * 
 * DEPENDENCIES:
 * - BlotterModel for database operations
 * - LogModel for activity logging
 * - UserModel to fetch resident names
 * 
 * @package App\Controllers
 */
class Blotter extends BaseController
{
    protected $blotterModel;
    protected $logModel;
    protected $userModel;       // NEW

    public function __construct()
    {
        $this->blotterModel = new BlotterModel();
        $this->logModel      = new LogModel();
        $this->userModel     = new UserModel();   // NEW
    }

    /**
     * List Blotters
     */
    public function index()
    {
        $data = [
            'blotters' => $this->blotterModel->getBlotters()
        ];
        return view('blotter/index', $data);
    }

    /**
     * Create Blotter Form
     * Now passes resident names to the view for autocomplete.
     */
    public function create()
    {
        // Fetch all resident names for the autocomplete list
        // Optional: filter by role if needed (e.g. ->where('role', 'resident'))
        $residents = $this->userModel->select('name')->findAll();
        $names = array_column($residents, 'name');
        sort($names); // alphabetical order

        return view('blotter/create', ['resident_names' => $names]);
    }

    /**
     * Save Blotter
     */
    public function store()
    {
        $rules = [
            'complainant'      => 'required|min_length[3]',
            'respondent'       => 'required|min_length[3]',
            'incident_type'    => 'required',
            'incident_date'    => 'required',
            'details'          => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'complainant'      => $this->request->getPost('complainant'),
            'respondent'       => $this->request->getPost('respondent'),
            'incident_type'    => $this->request->getPost('incident_type'),
            'incident_date'    => $this->request->getPost('incident_date'),
            'incident_location'=> $this->request->getPost('incident_location'),
            'purok'            => $this->request->getPost('purok'),
            'details'          => $this->request->getPost('details'),
            'status'           => 'Pending',
            'created_by'       => session()->get('id')
        ];

        $this->blotterModel->insert($data);

        // ── LOG THE ACTIVITY HERE ────────────────────────────────────
        $this->logModel->addLog("Recorded Blotter: {$data['complainant']} vs {$data['respondent']}");
        // ─────────────────────────────────────────────────────────────────────

        return redirect()->to('blotter')->with('success', 'Blotter recorded successfully.');
    }

    /**
     * View/Update Blotter
     */
    public function view($id)
    {
        $data['blotter'] = $this->blotterModel->find($id);
        return view('blotter/view', $data);
    }

    /**
     * Update Status / Action
     */
    public function update($id)
    {
        $data = [
            'status'        => $this->request->getPost('status'),
            'action_taken'  => $this->request->getPost('action_taken')
        ];

        $this->blotterModel->update($id, $data);
        return redirect()->to('blotter')->with('success', 'Blotter updated successfully.');
    }
}
<?php

namespace App\Controllers;

use App\Models\BlotterModel;

class Blotter extends BaseController
{
    protected $blotterModel;

    public function __construct()
    {
        $this->blotterModel = new BlotterModel();
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
     */
    public function create()
    {
        return view('blotter/create');
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
            'purok'            => $this->request->getPost('purok'), // ADDED HERE
            'details'          => $this->request->getPost('details'),
            'status'           => 'Pending', // Default status
            'created_by'       => session()->get('id')
        ];

        $this->blotterModel->insert($data);
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
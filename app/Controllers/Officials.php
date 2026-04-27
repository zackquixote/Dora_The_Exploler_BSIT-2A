<?php

namespace App\Controllers;

use App\Models\OfficialModel;

class Officials extends BaseController
{
    protected $officialModel;

    public function __construct()
    {
        $this->officialModel = new OfficialModel();
    }

    /**
     * List Officials
     */
    public function index()
    {
        $data = [
            'officials' => $this->officialModel->orderBy('id', 'ASC')->findAll()
        ];
        return view('officials/index', $data);
    }

    /**
     * Create New Official
     */
    public function create()
    {
        return view('officials/create');
    }

    /**
     * Save Official to DB
     */
    public function store()
    {
        // 1. Validate
        $rules = [
            'full_name' => 'required|min_length[3]',
            'position'  => 'required',
            'photo'     => 'uploaded[photo]|max_size[photo,2048]|is_image[photo]|mime_in[photo,image/jpg,image/jpeg,image/png]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // 2. Handle File Upload
        $file = $this->request->getFile('photo');
        $photoName = null;

        if ($file->isValid() && !$file->hasMoved()) {
            $photoName = $file->getRandomName();
            $file->move('uploads/officials', $photoName);
        }

        // 3. Save Data
        $data = [
            'full_name'      => $this->request->getPost('full_name'),
            'position'       => $this->request->getPost('position'),
            'contact_number' => $this->request->getPost('contact_number'),
            'photo'          => $photoName,
            'is_active'      => 1
        ];

        $this->officialModel->insert($data);

        return redirect()->to('officials')->with('success', 'Official added successfully.');
    }

    /**
     * Edit Official
     */
    public function edit($id)
    {
        $data['official'] = $this->officialModel->find($id);
        return view('officials/edit', $data);
    }

    /**
     * Update Official
     */
    public function update($id)
    {
        $official = $this->officialModel->find($id);
        
        $rules = [
            'full_name' => 'required|min_length[3]',
            'position'  => 'required',
            'photo'     => 'max_size[photo,2048]|is_image[photo]|mime_in[photo,image/jpg,image/jpeg,image/png]' // Optional validation for update
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Check if new photo is uploaded
        $file = $this->request->getFile('photo');
        $photoName = $official['photo']; // Keep old photo by default

        if ($file->isValid() && !$file->hasMoved()) {
            // Delete old photo if exists
            if ($official['photo'] && file_exists('uploads/officials/' . $official['photo'])) {
                unlink('uploads/officials/' . $official['photo']);
            }
            
            $photoName = $file->getRandomName();
            $file->move('uploads/officials', $photoName);
        }

        $data = [
            'full_name'      => $this->request->getPost('full_name'),
            'position'       => $this->request->getPost('position'),
            'contact_number' => $this->request->getPost('contact_number'),
            'photo'          => $photoName,
        ];

        $this->officialModel->update($id, $data);
        return redirect()->to('officials')->with('success', 'Official updated successfully.');
    }

    /**
     * Delete / Deactivate Official
     */
    public function delete($id)
    {
        $this->officialModel->update($id, ['is_active' => 0]);
        return redirect()->to('officials')->with('success', 'Official removed.');
    }
}
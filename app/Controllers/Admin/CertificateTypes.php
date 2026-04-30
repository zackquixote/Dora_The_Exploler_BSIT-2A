<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CertificateTypeModel;
use App\Models\CertificateModel;
use App\Models\LogModel;

class CertificateTypes extends BaseController
{
    protected $typeModel;
    protected $logModel;

    public function __construct()
    {
        $this->typeModel = new CertificateTypeModel(); // ← certificate_types table
        $this->logModel  = new LogModel();
    }

    /**
     * List all certificate type templates
     */
    public function index()
    {
        // Cross-reference enum types vs saved templates
        $enumTypes     = CertificateModel::getTypes();
        $savedTemplates = $this->typeModel->orderBy('name', 'ASC')->findAll();

        // Index saved templates by name for quick lookup
        $templatesByName = array_column($savedTemplates, null, 'name');

        // Build a unified list showing which enum types have templates
        $types = array_map(function ($enumName) use ($templatesByName) {
            return $templatesByName[$enumName] ?? [
                'id'         => null,
                'name'       => $enumName,
                'content'    => null,
                'created_at' => null,
                'updated_at' => null,
            ];
        }, $enumTypes);

        return view('admin/certificate_types/index', ['types' => $types]);
    }

    /**
     * Show create form
     */
    public function create()
    {
        // Pass enum types so the name field can be a dropdown
        return view('admin/certificate_types/create', [
            'enumTypes' => CertificateModel::getTypes(),
        ]);
    }

    /**
     * Save new certificate type template
     */
    public function store()
    {
        $name = $this->request->getPost('name');

        $rules = [
            'name'    => 'required|in_list[' . implode(',', CertificateModel::getTypes()) . ']|is_unique[certificate_types.name]',
            'content' => 'permit_empty',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $this->typeModel->insert([
            'name'       => $name,
            'content'    => $this->request->getPost('content'),
            'created_by' => session()->get('id'),
        ]);

        $this->logModel->addLog('Created certificate template: ' . $name);

        return redirect()->to('admin/certificateTypes')
            ->with('success', "Template for '{$name}' created successfully.");
    }

    /**
     * Show edit form
     */
    public function edit($id)
    {
        $template = $this->typeModel->find($id);

        if (! $template) {
            return redirect()->to('admin/certificateTypes')
                ->with('error', 'Template not found.');
        }

        return view('admin/certificate_types/edit', ['template' => $template]);
    }

    /**
     * Update existing certificate type template
     */
    public function update()
    {
        $id = $this->request->getPost('id');

        if (! $this->typeModel->find($id)) {
            return redirect()->to('admin/certificateTypes')
                ->with('error', 'Template not found.');
        }

        $rules = [
            'name' => "required|in_list[" . implode(',', CertificateModel::getTypes()) . "]|is_unique[certificate_types.name,id,{$id}]",
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $this->typeModel->update($id, [
            'name'    => $this->request->getPost('name'),
            'content' => $this->request->getPost('content'),
        ]);

        $this->logModel->addLog('Updated certificate template: ' . $this->request->getPost('name'));

        return redirect()->to('admin/certificateTypes')
            ->with('success', 'Template updated successfully.');
    }

    /**
     * Delete a template (AJAX)
     */
    public function delete($id)
    {
        $template = $this->typeModel->find($id);

        if (! $template) {
            return $this->response->setJSON(['success' => false, 'message' => 'Not found.']);
        }

        $this->typeModel->delete($id);
        $this->logModel->addLog('Deleted certificate template: ' . $template['name']);

        return $this->response->setJSON(['success' => true, 'message' => 'Template deleted.']);
    }

    /**
     * Preview template content (AJAX)
     */
    public function preview($id)
    {
        $template = $this->typeModel->find($id);

        if (! $template) {
            return $this->response->setJSON(['success' => false, 'message' => 'Not found.']);
        }

        return $this->response->setJSON([
            'success' => true,
            'name'    => esc($template['name']),
            'content' => $template['content'],
        ]);
    }
}
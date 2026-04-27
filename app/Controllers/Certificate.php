<?php

namespace App\Controllers;

use App\Models\CertificateModel;

class Certificate extends BaseController
{
    protected $certModel;

    public function __construct()
    {
        $this->certModel = new CertificateModel();
    }

    /**
     * Handle Form Submission -> Save to DB -> Redirect to Print
     */
    public function store()
    {
        // 1. Validate Input
        $residentId = $this->request->getPost('resident_id');
        $type       = $this->request->getPost('certificate_type');
        $purpose    = $this->request->getPost('purpose');

        if (!$residentId || !$type) {
            return redirect()->back()->with('error', 'Missing required information.');
        }

        // 2. Save to Database (Recent Activity Log)
        $data = [
            'resident_id'      => $residentId,
            'certificate_type' => $type,
            'purpose'          => $purpose,
            'created_by'       => session()->get('id') // Track who did it
        ];

        $certId = $this->certModel->insert($data);

        if ($certId) {
            // 3. Redirect to Print View directly
            return redirect()->to('certificate/print/' . $certId);
        } else {
            return redirect()->back()->with('error', 'Failed to generate certificate.');
        }
    }

    /**
     * Display the Printable View
     * Route: certificate/print/{id}
     */
    public function print($id)
    {
        $data = [
            'cert'     => $this->certModel->getCertificateForPrint($id),
            'settings' => $this->certModel->getBarangaySettings()
        ];

        if (!$data['cert']) {
            return redirect()->to('resident')->with('error', 'Certificate not found.');
        }

        return view('certificate/print_view', $data);
    }

    /**
     * Optional: Dashboard showing recent activity
     */
    public function index()
    {
        $data = [
            'recent' => $this->certModel->getRecentActivity(20)
        ];
        return view('certificate/index', $data);
    }
}
<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CertificateModel;
use App\Models\LogModel;
use App\Models\ResidentModel;

/**
 * Certificate Controller
 * 
 * Manages the issuance, printing, editing, and deletion of resident certificates.
 * 
 * METHODS:
 * - index(): Lists all issued certificates.
 * - create(): Displays form to issue a new certificate.
 * - store(): Saves certificate, logs action, redirects to print.
 * - print_view($id): Displays printable certificate with populated placeholders.
 * - edit($id): Shows edit form for certificate purpose.
 * - update($id): Updates certificate purpose.
 * - delete($id): Deletes a certificate and logs the action.
 * 
 * DEPENDENCIES:
 * - CertificateModel, ResidentModel, LogModel
 * - Uses helper: auth, form
 * 
 * @package App\Controllers
 */
class Certificate extends BaseController
{
    protected $certModel;
    protected $logModel;
    protected $helpers = ['auth', 'form'];

    public function __construct()
    {
        $this->certModel = new CertificateModel();
        $this->logModel  = new LogModel();
    }

    /**
     * List all issued certificates
     */
    public function index()
    {
        // Get Certificates with Resident Names
        $certificatesRaw = $this->certModel
            ->select('certificates.*, residents.first_name, residents.last_name')
            ->join('residents', 'residents.id = certificates.resident_id')
            ->orderBy('certificates.id', 'DESC')
            ->findAll();

        // Format types for view (Array of Arrays)
        $rawTypes = CertificateModel::getTypes();
        $formattedTypes = [];
        
        foreach ($rawTypes as $t) {
            $formattedTypes[] = [
                'name'  => $t,
                'value' => $t
            ];
        }

        $data = [
            'certificates' => $certificatesRaw, // Pass raw results
            'types'        => $formattedTypes
        ];

        return view('certificate/index', $data);
    }

    /**
     * Show the "Create Certificate" Form
     */
    public function create()
    {
        $residentModel = new ResidentModel();
        
        // Fetch residents for the dropdown
        $data = [
            'residents' => $residentModel->orderBy('last_name', 'ASC')->findAll(),
            'types'     => CertificateModel::getTypes() 
        ];

        return view('certificate/create', $data);
    }

    /**
     * Handle Form Submission → Save to DB → Redirect to Print
     */
    public function store()
    {
        $residentId = $this->request->getPost('resident_id');
        $type       = $this->request->getPost('certificate_type');
        $purpose    = $this->request->getPost('purpose');

        if (! $residentId || ! $type) {
            return redirect()->back()->with('error', 'Missing required information.');
        }

        // Validate type
        if (! in_array($type, CertificateModel::getTypes())) {
            return redirect()->back()->with('error', 'Invalid certificate type.');
        }

        // Handle Session ID safely
        $createdBy = session()->get('id');
        if (!$createdBy) {
            $createdBy = session()->get('user_id');
        }
        if (!$createdBy) {
            $createdBy = 1; // Default fallback
        }

        $certId = $this->certModel->insert([
            'resident_id'      => $residentId,
            'certificate_type' => $type,
            'purpose'          => $purpose,
            'created_by'       => $createdBy,
        ]);

        if ($certId) {
            $residentModel = new ResidentModel();
            $resident      = $residentModel->find($residentId);

            if ($resident) {
                $name = $resident['first_name'] . ' ' . $resident['last_name'];
                $this->logModel->addLog("Generated {$type} for {$name}");
            }

            return redirect()->to('certificate/print/' . $certId);
        }

        return redirect()->back()->with('error', 'Failed to generate certificate.');
    }

    /**
     * Display the Printable View (Renamed from 'print' to 'print_view')
     */
    public function print_view($id)
    {
        // Fetch data using the Model (SQL logic is in the Model now)
        $cert = $this->certModel->getCertificateForPrint($id);

        if (! $cert) {
            return redirect()->to('certificate')->with('error', 'Certificate not found.');
        }

        // ── Replace template placeholders with actual data ──────────────
        $content = $cert['template_content'] ?? '';

        if (! empty($content)) {
            $fullName = trim(($cert['first_name'] ?? '') . ' ' . ($cert['last_name'] ?? ''));

            $replacements = [
                // Resident
                '{resident_name}'  => $fullName,
                '{first_name}'     => $cert['first_name']     ?? '',
                '{last_name}'      => $cert['last_name']      ?? '',
                '{middle_name}'    => $cert['middle_name']    ?? '',
                '{age}'            => $cert['age']            ?? '',
                '{civil_status}'   => $cert['civil_status']   ?? '',
                '{gender}'         => $cert['gender']         ?? '',
                '{address}'        => $cert['address']        ?? '',
                '{precinct_no}'    => '', // Column removed, keep empty
                // Certificate
                '{purpose}'        => $cert['purpose']        ?? '',
                '{ctrl_number}'    => str_pad($cert['id'], 5, '0', STR_PAD_LEFT),
                // Date
                '{date_today}'     => date('F d, Y'),
                '{date_issued}'    => date('F d, Y', strtotime($cert['created_at'])),
                '{valid_until}'    => date('F d, Y', strtotime($cert['created_at'] . ' +1 year')),
                '{year}'           => date('Y'),
                // Barangay
                '{barangay_name}'  => $cert['barangay_name']  ?? '',
                '{municipality}'   => $cert['municipality']   ?? '',
                '{province}'       => $cert['province']       ?? '',
                '{captain_name}'   => $cert['captain_name']   ?? '',
                '{secretary_name}' => $cert['secretary_name'] ?? '',
                '{treasurer_name}' => $cert['treasurer_name'] ?? '',
            ];

            $content = str_replace(
                array_keys($replacements),
                array_values($replacements),
                $content
            );
        }

        return view('certificate/print_view', [
            'cert'     => $cert,
            'content'  => $content,
            'settings' => $this->certModel->getBarangaySettings(),
        ]);
    }

    /**
     * Show the "Edit Certificate" Form
     */
    public function edit($id)
    {
        $cert = $this->certModel->find($id);

        if (! $cert) {
            return redirect()->to('certificate')->with('error', 'Certificate not found.');
        }

        $residentModel = new ResidentModel();
        $resident = $residentModel->find($cert['resident_id']);

        return view('certificate/edit', [
            'cert'      => $cert,
            'resident'  => $resident,
            'types'     => CertificateModel::getTypes()
        ]);
    }

    /**
     * Update certificate details
     */
    public function update($id)
    {
        $cert = $this->certModel->find($id);

        if (! $cert) {
            return redirect()->to('certificate')->with('error', 'Certificate not found.');
        }

        $purpose = $this->request->getPost('purpose');

        if (! $purpose) {
            return redirect()->back()->with('error', 'Purpose is required.');
        }

        $this->certModel->update($id, [
            'purpose' => $purpose,
        ]);

        $this->logModel->addLog('Updated certificate #' . $id);

        return redirect()->to('certificate/print/' . $id)
            ->with('success', 'Certificate updated successfully.');
    }

    /**
     * Delete a certificate
     */
    public function delete($id)
    {
        $cert = $this->certModel->find($id);

        if (! $cert) {
            return redirect()->to('certificate')->with('error', 'Certificate not found.');
        }

        $this->certModel->delete($id);
        $this->logModel->addLog('Deleted certificate #' . $id);

        return redirect()->to('certificate')->with('success', 'Certificate deleted successfully.');
    }
}
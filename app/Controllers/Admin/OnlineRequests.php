<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CertificateRequestModel;
use App\Models\CertificateModel;
use App\Models\LogModel;

class OnlineRequests extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();

        // 1. Fetch pending Certificate Requests
        $certRequests = $db->table('certificate_requests cr')
            ->select('cr.*, r.first_name, r.last_name, r.sitio')
            ->join('residents r', 'r.id = cr.resident_id', 'left')
            ->where('cr.status', 'Pending')
            ->orderBy('cr.created_at', 'DESC')
            ->get()->getResultArray();

        // 2. Fetch pending Blotter Records filed via Portal
        // Assuming portal-filed blotters start with 'Pending' status.
        $blotterRequests = $db->table('blotter_records br')
            ->select('br.*, r.first_name, r.last_name')
            ->join('blotter_parties bp', 'bp.blotter_id = br.id AND bp.role = "complainant"', 'left')
            ->join('residents r', 'r.id = bp.resident_id', 'left')
            ->where('br.status', 'Pending')
            ->where('br.source', 'Online')
            ->orderBy('br.created_at', 'DESC')
            ->get()->getResultArray();

        return view('Admin/online_requests', [
            'certRequests'    => $certRequests,
            'blotterRequests' => $blotterRequests
        ]);
    }

    public function approveCertificate($id)
    {
        $db = \Config\Database::connect();
        $requestModel = new CertificateRequestModel();
        $certModel = new CertificateModel();

        $req = $requestModel->find($id);

        if (!$req || $req['status'] !== 'Pending') {
            return redirect()->back()->with('error', 'Request not found or already processed.');
        }

        $db->transBegin();

        try {
            // Generate official certificate record
            $certNumber = $certModel->generateCertificateNumber($req['certificate_type']);
            
            $certId = $certModel->insert([
                'certificate_number' => $certNumber,
                'resident_id'        => $req['resident_id'],
                'certificate_type'   => $req['certificate_type'],
                'purpose'            => $req['purpose'],
                'created_by'         => session()->get('user_id') ?? 1,
            ]);

            // Update request status
            $requestModel->update($id, ['status' => 'Approved']);

            // Send notification to resident (if NotificationModel exists)
            try {
                $notifModel = new \App\Models\NotificationModel();
                $notifModel->insert([
                    'recipient_type' => 'resident',
                    'recipient_id'   => $req['resident_id'],
                    'title'          => 'Certificate Approved',
                    'message'        => 'Your request for ' . $req['certificate_type'] . ' has been approved and is ready.',
                    'status'         => 'sent'
                ]);
            } catch (\Throwable $th) {}

            $db->transCommit();

            return redirect()->to(base_url("admin/certificate/print/{$certId}"))
                ->with('success', 'Certificate generated successfully from portal request!');

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->with('error', 'Error approving request: ' . $e->getMessage());
        }
    }

    public function rejectCertificate($id)
    {
        $requestModel = new CertificateRequestModel();
        $req = $requestModel->find($id);

        if (!$req || $req['status'] !== 'Pending') {
            return redirect()->back()->with('error', 'Request not found or already processed.');
        }

        $rejectionNote = $this->request->getPost('rejection_note');

        if ($requestModel->update($id, [
            'status' => 'Rejected',
            'rejection_note' => $rejectionNote
        ])) {
            
            // Send notification
            try {
                $notifModel = new \App\Models\NotificationModel();
                $notifModel->insert([
                    'recipient_type' => 'resident',
                    'recipient_id'   => $req['resident_id'],
                    'title'          => 'Certificate Rejected',
                    'message'        => 'Your request for ' . $req['certificate_type'] . ' was rejected. Reason: ' . ($rejectionNote ?: 'None provided'),
                    'status'         => 'sent'
                ]);
            } catch (\Throwable $th) {}

            return redirect()->back()->with('success', 'Certificate request rejected.');
        }

        return redirect()->back()->with('error', 'Failed to reject request.');
    }

    public function acknowledgeBlotter($id)
    {
        $db = \Config\Database::connect();
        $blotterModel = new \App\Models\BlotterModel();
        
        $blotter = $blotterModel->find($id);

        if (!$blotter || $blotter['status'] !== 'Pending' || $blotter['source'] !== 'Online') {
            return redirect()->back()->with('error', 'Blotter not found or already acknowledged.');
        }

        $blotterModel->update($id, ['status' => 'Under Investigation']);

        // Notify complainant
        $complainant = $db->table('blotter_parties')->where('blotter_id', $id)->where('role', 'complainant')->first();
        if ($complainant && !empty($complainant->resident_id)) {
            try {
                $notifModel = new \App\Models\NotificationModel();
                $notifModel->insert([
                    'recipient_type' => 'resident',
                    'recipient_id'   => $complainant->resident_id,
                    'title'          => 'Blotter Report Acknowledged',
                    'message'        => 'Your incident report (Case #' . $blotter['case_number'] . ') has been acknowledged and is now Under Investigation.',
                    'status'         => 'sent'
                ]);
            } catch (\Throwable $th) {}
        }

        return redirect()->back()->with('success', 'Blotter acknowledged and set to Under Investigation.');
    }
}

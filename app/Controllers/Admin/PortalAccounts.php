<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ResidentModel;
use App\Models\ResidentAccountModel;
use App\Models\ResidentVerificationModel;
use App\Services\ResidentVerificationService;

class PortalAccounts extends BaseController
{
    public function index()
    {
        $verificationModel = new ResidentVerificationModel();
        $pendingVerifications = $verificationModel->getQueue(['pending_admin_review', 'needs_resubmission', 'pending_otp', 'rejected']);

        $activeAccounts = \Config\Database::connect()->table('resident_accounts')
            ->select('resident_accounts.*, residents.first_name, residents.last_name')
            ->join('residents', 'residents.id = resident_accounts.resident_id', 'left')
            ->where('resident_accounts.status', 'active')
            ->orderBy('resident_accounts.created_at', 'DESC')
            ->get()->getResultArray();

        return view('Admin/portal_accounts', [
            'pendingVerifications' => $pendingVerifications,
            'activeAccounts'       => $activeAccounts,
        ]);
    }

    public function viewVerification($id)
    {
        $verificationModel = new ResidentVerificationModel();
        $verification = $verificationModel->getDetails((int) $id);
        if (! $verification) {
            return redirect()->back()->with('error', 'Verification request not found.');
        }

        $service = new ResidentVerificationService();
        $files = $service->getVerificationFiles((int) $id);

        $residentCandidates = (new ResidentModel())
            ->groupStart()
            ->like('first_name', $verification['first_name'])
            ->orLike('last_name', $verification['last_name'])
            ->groupEnd()
            ->orderBy('last_name', 'ASC')
            ->findAll(30);

        return view('Admin/portal_verification_view', [
            'verification' => $verification,
            'files' => $files,
            'residentCandidates' => $residentCandidates,
        ]);
    }

    public function approve($id)
    {
        $reviewerId = (int) (session()->get('user_id') ?? 0);
        $residentId = (int) $this->request->getPost('resident_id');
        $otpRequired = $this->request->getPost('otp_required') === '1';
        $otpChannel = $this->request->getPost('otp_channel');
        $reviewNotes = trim((string) $this->request->getPost('review_notes'));

        try {
            (new ResidentVerificationService())->approve((int) $id, $reviewerId, $residentId, $otpRequired, $otpChannel, $reviewNotes);
            return redirect()->to(base_url('admin/portal-accounts'))->with('success', 'Resident verification approved successfully.');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function reject($id)
    {
        $reason = trim((string) $this->request->getPost('rejection_reason'));
        if ($reason === '') {
            return redirect()->back()->with('error', 'Rejection reason is required.');
        }

        try {
            (new ResidentVerificationService())->reject((int) $id, (int) (session()->get('user_id') ?? 0), $reason);
            return redirect()->to(base_url('admin/portal-accounts'))->with('success', 'Verification request rejected.');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function requestResubmission($id)
    {
        $reason = trim((string) $this->request->getPost('resubmission_reason'));
        if ($reason === '') {
            return redirect()->back()->with('error', 'Please provide the reason for requesting new ID images or documents.');
        }

        try {
            (new ResidentVerificationService())->requestResubmission((int) $id, (int) (session()->get('user_id') ?? 0), $reason);
            return redirect()->to(base_url('admin/portal-accounts'))->with('success', 'Resident asked to upload new verification documents.');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function viewFile($id)
    {
        $service = new ResidentVerificationService();
        $file = $service->getVerificationFile((int) $id);
        if (! $file) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $path = $service->getProtectedFilePath($file);
        $mimeType = (string) ($file['mime_type'] ?? 'application/octet-stream');
        $body = file_get_contents($path);

        return $this->response
            ->setHeader('Content-Type', $mimeType)
            ->setHeader('Content-Disposition', 'inline; filename="' . basename($path) . '"')
            ->setBody($body === false ? '' : $body);
    }

    public function suspend($id)
    {
        $model = new ResidentAccountModel();
        if ($model->update($id, ['status' => 'suspended'])) {
            return redirect()->back()->with('success', 'Account suspended.');
        }
        return redirect()->back()->with('error', 'Failed to suspend account.');
    }

    public function reactivate($id)
    {
        $model = new ResidentAccountModel();
        if ($model->update($id, ['status' => 'active', 'rejection_reason' => null])) {
            return redirect()->back()->with('success', 'Account reactivated.');
        }
        return redirect()->back()->with('error', 'Failed to reactivate account.');
    }

    public function resetPassword($id)
    {
        $model = new ResidentAccountModel();
        $newPassword = $this->request->getPost('new_password');

        if (empty($newPassword) || strlen($newPassword) < 6) {
            return redirect()->back()->with('error', 'Password must be at least 6 characters.');
        }

        if ($model->update($id, ['password_hash' => password_hash($newPassword, PASSWORD_DEFAULT)])) {
            return redirect()->back()->with('success', 'Password reset successfully. Resident can now log in with the new password.');
        }
        return redirect()->back()->with('error', 'Failed to reset password.');
    }
}

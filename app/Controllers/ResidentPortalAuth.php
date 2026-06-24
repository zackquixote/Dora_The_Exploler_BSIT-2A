<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ResidentAccountModel;
use App\Services\ResidentVerificationService;
use CodeIgniter\Exceptions\PageNotFoundException;

/**
 * ResidentPortalAuth
 *
 * Separate authentication for residents (public portal).
 * Phase 1A: scaffold (login/register) so Portal module can build on it.
 */
class ResidentPortalAuth extends BaseController
{
    public function login()
    {
        if (strtolower((string)$this->request->getMethod()) === 'post') {
            $identifier = trim((string) $this->request->getPost('identifier'));
            $password   = (string) $this->request->getPost('password');

            $model = new ResidentAccountModel();
            $user = $model->groupStart()
                ->where('email', $identifier)
                ->orWhere('phone', $identifier)
                ->groupEnd()
                ->first();

            if (!$user || !password_verify($password, (string)($user['password_hash'] ?? ''))) {
                return redirect()->back()->with('error', 'Invalid credentials.');
            }
            if (($user['status'] ?? 'pending_verification') !== 'active') {
                $status = $user['status'] ?? 'pending_verification';
                if ($status === 'rejected') {
                    $reason = !empty($user['rejection_reason']) ? ' Reason: ' . $user['rejection_reason'] : '';
                    return redirect()->back()->with('error', 'Your registration was not approved.' . $reason);
                }
                if ($status === 'suspended') {
                    return redirect()->back()->with('error', 'Your account has been suspended. Please contact the barangay office.');
                }
                if (in_array($status, ['pending_verification', 'needs_resubmission'], true)) {
                    $this->startPendingVerificationSession((int) $user['id']);
                    return redirect()->to(base_url('portal/verification-status'));
                }
                if ($status === 'pending_otp') {
                    $this->startPendingVerificationSession((int) $user['id']);
                    return redirect()->to(base_url('portal/verify-otp'));
                }
                return redirect()->back()->with('error', 'Your account is pending approval. Please wait for admin confirmation.');
            }

            $this->completeResidentLogin($user);

            $model->update((int)$user['id'], ['last_login_at' => date('Y-m-d H:i:s')]);

            return redirect()->to(base_url('portal/home'));
        }

        return view('portal/login');
    }

    public function register()
    {
        if (strtolower((string)$this->request->getMethod()) === 'post') {
            $payload = $this->collectVerificationInput();
            $validationError = $this->validateVerificationInput($payload, true);
            if ($validationError !== null) {
                return redirect()->back()->withInput()->with('error', $validationError);
            }

            $model = new ResidentAccountModel();
            if (!empty($payload['email']) && $model->where('email', $payload['email'])->first()) {
                return redirect()->back()->withInput()->with('error', 'That email address is already linked to a portal account.');
            }
            if (!empty($payload['phone']) && $model->where('phone', $payload['phone'])->first()) {
                return redirect()->back()->withInput()->with('error', 'That phone number is already linked to a portal account.');
            }

            try {
                $service = new ResidentVerificationService();
                $result = $service->register($payload, [
                    'national_id_front' => $this->request->getFile('national_id_front'),
                    'national_id_back' => $this->request->getFile('national_id_back'),
                    'supporting_document' => $this->request->getFile('supporting_document'),
                ]);
            } catch (\Throwable $e) {
                return redirect()->back()->withInput()->with('error', $e->getMessage());
            }

            $this->startPendingVerificationSession((int) $result['account_id']);

            return redirect()->to(base_url('portal/verification-status'))
                ->with('success', 'Registration submitted. Your ID upload is now pending admin review.');
        }

        return view('portal/register', [
            'mode' => 'register',
            'formAction' => base_url('portal/register'),
        ]);
    }

    public function verificationStatus()
    {
        $account = $this->getPendingAccount();
        if (!$account) {
            return redirect()->to(base_url('portal/login'))->with('error', 'Please log in to continue.');
        }

        $service = new ResidentVerificationService();
        $verification = $service->getLatestVerificationForAccount((int) $account['id']);

        return view('portal/verification_status', [
            'account' => $account,
            'verification' => $verification,
        ]);
    }

    public function resubmitVerification()
    {
        $account = $this->getPendingAccount();
        if (!$account) {
            return redirect()->to(base_url('portal/login'))->with('error', 'Please log in to continue.');
        }

        $service = new ResidentVerificationService();
        $verification = $service->getLatestVerificationForAccount((int) $account['id']);
        if (!$verification || $verification['status'] !== 'needs_resubmission') {
            return redirect()->to(base_url('portal/verification-status'));
        }

        if (strtolower((string) $this->request->getMethod()) === 'post') {
            $payload = $this->collectVerificationInput();
            $validationError = $this->validateVerificationInput($payload, false);
            if ($validationError !== null) {
                return redirect()->back()->withInput()->with('error', $validationError);
            }

            try {
                $service->resubmit((int) $account['id'], $payload, [
                    'national_id_front' => $this->request->getFile('national_id_front'),
                    'national_id_back' => $this->request->getFile('national_id_back'),
                    'supporting_document' => $this->request->getFile('supporting_document'),
                ]);
            } catch (\Throwable $e) {
                return redirect()->back()->withInput()->with('error', $e->getMessage());
            }

            return redirect()->to(base_url('portal/verification-status'))
                ->with('success', 'Your verification documents were resubmitted successfully.');
        }

        return view('portal/register', [
            'mode' => 'resubmit',
            'formAction' => base_url('portal/resubmit-verification'),
            'verification' => $verification,
            'account' => $account,
        ]);
    }

    public function verifyOtp()
    {
        $account = $this->getPendingAccount();
        if (!$account) {
            return redirect()->to(base_url('portal/login'))->with('error', 'Please log in to continue.');
        }

        $service = new ResidentVerificationService();
        $verification = $service->getLatestVerificationForAccount((int) $account['id']);
        if (!$verification || $verification['status'] !== 'pending_otp') {
            return redirect()->to(base_url('portal/verification-status'));
        }

        if (strtolower((string) $this->request->getMethod()) === 'post') {
            $otpCode = trim((string) $this->request->getPost('otp_code'));
            if ($otpCode === '' || !preg_match('/^\d{6}$/', $otpCode)) {
                return redirect()->back()->with('error', 'Please enter the 6-digit OTP.');
            }

            if (! $service->verifyOtp((int) $account['id'], $otpCode)) {
                return redirect()->back()->with('error', 'Invalid or expired OTP code.');
            }

            $this->clearPendingVerificationSession();
            $account = (new ResidentAccountModel())->find((int) $account['id']);
            if ($account) {
                $this->completeResidentLogin($account);
                return redirect()->to(base_url('portal/home'))->with('success', 'Your account has been verified and activated.');
            }

            return redirect()->to(base_url('portal/login'))->with('success', 'OTP verified. Please log in again.');
        }

        return view('portal/verify_otp', [
            'account' => $account,
            'verification' => $verification,
        ]);
    }

    public function resendOtp()
    {
        $account = $this->getPendingAccount();
        if (!$account) {
            return redirect()->to(base_url('portal/login'))->with('error', 'Please log in to continue.');
        }

        $service = new ResidentVerificationService();
        if (! $service->resendOtp((int) $account['id'])) {
            return redirect()->back()->with('error', 'OTP could not be resent yet. Please wait a moment and try again.');
        }

        return redirect()->back()->with('success', 'A new OTP was sent to your verified contact channel.');
    }
    public function forgotPassword()
    {
        if (strtolower((string)$this->request->getMethod()) === 'post') {
            $email = trim((string)$this->request->getPost('email'));
            if (!$email) {
                return redirect()->back()->with('error', 'Email is required.');
            }

            $model = new ResidentAccountModel();
            $account = $model->where('email', $email)->first();

            if ($account) {
                $token = bin2hex(random_bytes(32));
                $model->update($account['id'], [
                    'reset_token' => $token,
                    'reset_token_expiry' => date('Y-m-d H:i:s', strtotime('+1 hour'))
                ]);

                // In a real app, send email here. Since SMTP isn't guaranteed, 
                // we'll flash the reset link to the session for demonstration.
                $resetLink = base_url("portal/reset-password?token=" . $token);
                return redirect()->back()->with('success', "If your email is registered, you will receive a reset link shortly.<br><br><small><strong>DEMO MODE:</strong> <a href='{$resetLink}'>Click here to reset password</a></small>");
            }

            // Always show success to prevent email enumeration
            return redirect()->back()->with('success', 'If your email is registered, you will receive a reset link shortly.');
        }

        return view('portal/forgot_password');
    }

    public function resetPassword()
    {
        $token = $this->request->getGet('token');
        if (!$token) {
            return redirect()->to(base_url('portal/login'))->with('error', 'Invalid or missing reset token.');
        }

        $model = new ResidentAccountModel();
        $account = $model->where('reset_token', $token)
                         ->where('reset_token_expiry >=', date('Y-m-d H:i:s'))
                         ->first();

        if (!$account) {
            return redirect()->to(base_url('portal/login'))->with('error', 'Your reset link is invalid or has expired.');
        }

        if (strtolower((string)$this->request->getMethod()) === 'post') {
            $password = $this->request->getPost('password');
            $confirm = $this->request->getPost('confirm_password');

            if (strlen($password) < 6) {
                return redirect()->back()->with('error', 'Password must be at least 6 characters.');
            }
            if ($password !== $confirm) {
                return redirect()->back()->with('error', 'Passwords do not match.');
            }

            $model->update($account['id'], [
                'password_hash' => password_hash($password, PASSWORD_DEFAULT),
                'reset_token' => null,
                'reset_token_expiry' => null
            ]);

            return redirect()->to(base_url('portal/login'))->with('success', 'Your password has been successfully reset. You can now log in.');
        }

        return view('portal/reset_password', ['token' => $token]);
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to(base_url());
    }

    public function forceSetup()
    {
        // SECURITY: This endpoint is for local/dev setup only.
        // It must never be accessible in production.
        if (ENVIRONMENT === 'production' || !is_cli()) {
            throw PageNotFoundException::forPageNotFound();
        }

        $db = \Config\Database::connect();
        
        // Ensure resident 1 exists
        $db->table('residents')->ignore(true)->insert([
            'id' => 1,
            'first_name' => 'Juan',
            'last_name' => 'Dela Cruz',
            'status' => 'active',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        $model = new ResidentAccountModel();
        $existing = $model->where('email', 'juan@example.com')->first();
        
        $data = [
            'resident_id' => 1,
            'email' => 'juan@example.com',
            'phone' => '09123456789',
            'password_hash' => password_hash('password123', PASSWORD_DEFAULT),
            'status' => 'active'
        ];

        if ($existing) {
            $model->update($existing['id'], $data);
            return "Updated existing juan@example.com account. Password is now: password123";
        } else {
            $model->insert($data);
            return "Created new juan@example.com account. Password is: password123";
        }
    }

    public function checkAccount()
    {
        // SECURITY: Debug helper - CLI only.
        if (ENVIRONMENT === 'production' || !is_cli()) {
            throw PageNotFoundException::forPageNotFound();
        }

        $db = \Config\Database::connect();
        $account = $db->table('resident_accounts')->where('email', 'lourdianrentinoakol@gmail.com')->get()->getRowArray();
        if (!$account) {
            return "Account not found in resident_accounts table.";
        }
        
        $output = "Account found!\n";
        $output .= "Status: " . $account['status'] . "\n";
        $output .= "Resident ID: " . $account['resident_id'] . "\n";
        
        if (password_verify('lourdian', $account['password_hash'])) {
            $output .= "Password verification: SUCCESS (Password matches hash)\n";
        } else {
            $output .= "Password verification: FAILED (Password does not match hash)\n";
        }
        return $output;
    }

    public function migrateDB()
    {
        // SECURITY: Maintenance helper - CLI only.
        if (ENVIRONMENT === 'production' || !is_cli()) {
            throw PageNotFoundException::forPageNotFound();
        }

        $migrate = \Config\Services::migrations();
        try {
            $migrate->latest();
            return "Migrations ran successfully.";
        } catch (\Throwable $e) {
            return "Migration failed: " . $e->getMessage();
        }
    }

    private function collectVerificationInput(): array
    {
        return [
            'first_name' => trim((string) $this->request->getPost('first_name')),
            'middle_name' => trim((string) $this->request->getPost('middle_name')),
            'last_name' => trim((string) $this->request->getPost('last_name')),
            'birthdate' => trim((string) $this->request->getPost('birthdate')),
            'address' => trim((string) $this->request->getPost('address')),
            'email' => trim((string) $this->request->getPost('email')),
            'phone' => trim((string) $this->request->getPost('phone')),
            'national_id_number' => trim((string) $this->request->getPost('national_id_number')),
            'otp_channel' => trim((string) $this->request->getPost('otp_channel')),
            'password' => (string) $this->request->getPost('password'),
            'confirm_password' => (string) $this->request->getPost('confirm_password'),
        ];
    }

    private function validateVerificationInput(array $payload, bool $requirePassword): ?string
    {
        if ($payload['first_name'] === '' || $payload['last_name'] === '' || $payload['address'] === '' || $payload['national_id_number'] === '') {
            return 'Please complete all required identity fields.';
        }

        if ($payload['birthdate'] === '') {
            return 'Birthdate is required.';
        }

        if ($payload['email'] === '' && $payload['phone'] === '') {
            return 'Please provide at least one contact method (email or phone).';
        }

        if ($requirePassword) {
            if ($payload['password'] === '' || $payload['confirm_password'] === '') {
                return 'Password and confirm password are required.';
            }

            if (strlen($payload['password']) < 8) {
                return 'Password must be at least 8 characters.';
            }

            if ($payload['password'] !== $payload['confirm_password']) {
                return 'Passwords do not match.';
            }
        }

        $front = $this->request->getFile('national_id_front');
        if (!$front || !$front->isValid()) {
            return 'National ID front image is required.';
        }

        if (! $this->isAllowedUpload($front, ['jpg', 'jpeg', 'png', 'webp'])) {
            return 'National ID front image must be JPG, JPEG, PNG, or WEBP.';
        }

        $back = $this->request->getFile('national_id_back');
        if ($back && $back->isValid() && ! $this->isAllowedUpload($back, ['jpg', 'jpeg', 'png', 'webp'])) {
            return 'National ID back image must be JPG, JPEG, PNG, or WEBP.';
        }

        $support = $this->request->getFile('supporting_document');
        if ($support && $support->isValid() && ! $this->isAllowedUpload($support, ['jpg', 'jpeg', 'png', 'webp', 'pdf'])) {
            return 'Supporting document must be JPG, JPEG, PNG, WEBP, or PDF.';
        }

        return null;
    }

    private function isAllowedUpload($file, array $extensions): bool
    {
        return $file && $file->isValid() && in_array(strtolower((string) $file->getExtension()), $extensions, true);
    }

    private function startPendingVerificationSession(int $accountId): void
    {
        session()->set([
            'resident_pending_account_id' => $accountId,
        ]);
    }

    private function clearPendingVerificationSession(): void
    {
        session()->remove('resident_pending_account_id');
    }

    private function getPendingAccount(): ?array
    {
        $accountId = (int) session()->get('resident_pending_account_id');
        if ($accountId <= 0) {
            return null;
        }

        return (new ResidentAccountModel())->find($accountId);
    }

    private function completeResidentLogin(array $user): void
    {
        session()->regenerate(true);
        session()->remove('resident_pending_account_id');
        session()->set([
            'logged_in'   => true,
            'role'        => 'resident',
            'resident_id' => (int) ($user['resident_id'] ?? 0),
            'user_id'     => 0,
            'name'        => 'Resident',
            'last_activity' => time(),
        ]);
    }
}


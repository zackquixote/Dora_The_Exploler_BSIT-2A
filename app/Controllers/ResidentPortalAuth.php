<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ResidentAccountModel;

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
            if (($user['status'] ?? 'pending') !== 'active') {
                return redirect()->back()->with('error', 'Your account is not active yet.');
            }

            session()->regenerate(true);
            session()->set([
                'logged_in'   => true,
                'role'        => 'resident',
                'resident_id' => (int) ($user['resident_id'] ?? 0),
                'user_id'     => 0,
                'name'        => 'Resident',
                'last_activity' => time(),
            ]);

            $model->update((int)$user['id'], ['last_login_at' => date('Y-m-d H:i:s')]);

            return redirect()->to(base_url('portal/home'));
        }

        return view('portal/login');
    }

    public function register()
    {
        if (strtolower((string)$this->request->getMethod()) === 'post') {
            $residentId = (int) $this->request->getPost('resident_id');
            $email      = trim((string) $this->request->getPost('email'));
            $phone      = trim((string) $this->request->getPost('phone'));
            $password   = (string) $this->request->getPost('password');

            if ($residentId <= 0 || $password === '' || (empty($email) && empty($phone))) {
                return redirect()->back()->with('error', 'Please complete the required fields.');
            }

            $model = new ResidentAccountModel();

            // Phase 1A scaffold: accounts default to pending (verification flow in Phase 1C).
            $model->insert([
                'resident_id'    => $residentId,
                'email'          => $email ?: null,
                'phone'          => $phone ?: null,
                'password_hash'  => password_hash($password, PASSWORD_DEFAULT),
                'status'         => 'pending',
                'verification_code' => null,
            ]);

            return redirect()->to(base_url('portal/login'))
                ->with('success', 'Registration submitted. Please wait for activation.');
        }

        return view('portal/register');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to(base_url());
    }
    public function forceSetup()
    {
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

    public function migrateDB()
    {
        $migrate = \Config\Services::migrations();
        try {
            $migrate->latest();
            return "Migrations ran successfully.";
        } catch (\Throwable $e) {
            return "Migration failed: " . $e->getMessage();
        }
    }
}


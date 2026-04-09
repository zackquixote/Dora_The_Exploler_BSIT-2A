<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\LogModel;
use CodeIgniter\Controller;

class Auth extends BaseController
{
    public function index()
    {
        if (session()->get('logged_in')) {
            $role = session()->get('role');
            return $role === 'admin'
                ? redirect()->to('/admin/dashboard')
                : redirect()->to('/staff/dashboard');
        }

        $lockout = 0;
        $expiry = session()->get('lockout_expiry');

        if ($expiry && time() < $expiry) {
            $lockout = $expiry - time();
        } else {
            session()->remove('lockout_expiry');
            $this->clearFailedAttempts();
        }

        return view('login', ['lockout' => $lockout]);
    }

    public function auth()
    {
        $session = session();
        $model = new UserModel();
        $db = \Config\Database::connect();

        $email = filter_var($this->request->getPost('email'), FILTER_SANITIZE_EMAIL);
        $password = trim($this->request->getPost('password'));
        $ip = $this->request->getIPAddress();
        $userAgent = $this->request->getUserAgent();

        $maxAttempts = 5;
        $lockoutTime = 3 * 60;
        $timeWindow = date('Y-m-d H:i:s', strtotime('-15 minutes'));

        $builder = $db->table('login_attempts');
        $attempts = $builder
            ->where('ip_address', $ip)
            ->where('attempt_time >=', $timeWindow)
            ->countAllResults();

        if ($attempts >= $maxAttempts) {
            $lastAttempt = $builder
                ->selectMax('attempt_time')
                ->where('ip_address', $ip)
                ->get()
                ->getRow();

            $lastTime = strtotime($lastAttempt->attempt_time);
            $lockoutExpiry = $lastTime + $lockoutTime;
            $remaining = $lockoutExpiry - time();

            if ($remaining > 0) {
                session()->set('lockout_expiry', $lockoutExpiry);
                return redirect()->to('/login');
            }
        }

        $user = $model->where('email', $email)->first();

        if ($user && password_verify($password, $user['password'])) {
            $builder->where('ip_address', $ip)->delete();

            $session->regenerate();
            $session->set([
                'user_id'       => $user['id'],
                'email'         => $user['email'],
                'name'          => $user['name'],
                'role'          => $user['role'],  // ← ADDED THIS
                'logged_in'     => true,
                'last_activity' => time()
            ]);

            $logModel = new LogModel();
            $logModel->addLog('Login: ' . $user['name'], 'LOGIN');

            // ← FIXED REDIRECT
            if ($user['role'] === 'admin') {
                return redirect()->to('/admin/dashboard');
            } else {
                return redirect()->to('/staff/dashboard');
            }

        } else {
            $builder->insert([
                'email'        => $email,
                'ip_address'   => $ip,
                'user_agent'   => $userAgent,
                'attempt_time' => date('Y-m-d H:i:s')
            ]);

            return redirect()->to('/login')->with('error', 'Invalid email or password');
        }
    }

    public function logout()
    {
        $logModel = new LogModel();
        $logModel->addLog('Logout', 'LOGOUT');

        session()->destroy();
        return redirect()->to('/login');
    }

    private function clearFailedAttempts()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('login_attempts');

        $ip = $this->request->getIPAddress();
        $timeThreshold = date('Y-m-d H:i:s', strtotime('-1 minute'));

        $builder->where('ip_address', $ip)
                ->where('attempt_time <', $timeThreshold)
                ->delete();
    }
}
<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\LogModel;

/**
 * Auth Controller
 * 
 * Handles user authentication: login, logout, and session management.
 * 
 * METHODS:
 * - index(): Displays login form or redirects if already logged in.
 * - auth(): Processes login credentials, sets session, and logs the action.
 * - logout(): Destroys session, clears cookies, and logs the logout.
 * 
 * DEPENDENCIES:
 * - UserModel for database authentication
 * - LogModel for recording login/logout activities
 * 
 * @package App\Controllers
 */
class Auth extends BaseController
{
    protected $logModel;

    public function __construct()
    {
        $this->logModel = new LogModel();
    }

    public function index()
    {
        if (session()->get('logged_in')) {
            // Force lowercase to match Routes (admin or staff)
            $redirectRole = strtolower(session()->get('role'));
            return redirect()->to($redirectRole . '/dashboard');
        }
        
        $data['lockout'] = 0;
        return view('login', $data);
    }

    public function auth()
    {
        $model = new UserModel();
        
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        $remember = $this->request->getPost('remember');

        $user = $model->where('email', $email)->first();

        if ($user && password_verify($password, $user['password'])) {
            
            session()->set([
                'logged_in' => true,
                'user_id'   => $user['id'],
                'role'      => $user['role'],
                'email'     => $user['email'],
                'name'      => $user['name']
            ]);

            // ── LOG THE LOGIN HERE ────────────────────────────────
            $this->logModel->addLog("User Logged In");
            // ─────────────────────────────────────────────────────────────

            if ($remember) {
                $this->response->setCookie('user_email', $email, 60 * 60 * 24 * 30);
                $this->response->setCookie('user_id', $user['id'], 60 * 60 * 24 * 30);
            }

            // Force lowercase redirection
            $redirectRole = strtolower($user['role']);
            return redirect()->to($redirectRole . '/dashboard');
        }

        return redirect()->back()->with('error', 'Invalid email or password');
    }

    public function logout()
    {
        // ── LOG THE LOGOUT HERE (Before destroying session) ──
        $this->logModel->addLog("User Logged Out");
        // ─────────────────────────────────────────────────────────────

        session()->destroy();
        
        $this->response->deleteCookie('user_email');
        $this->response->deleteCookie('user_id');
        
        return redirect()->to('/login');
    }
}
<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;

class Auth extends BaseController
{
    public function index()
    {
        if (session()->get('logged_in')) {
            // Already logged in → go to appropriate dashboard
            $role = session()->get('role');
            return redirect()->to($role . '/dashboard');
        }
        
        // Pass lockout time if needed (for failed attempts feature)
        $data['lockout'] = 0;
        return view('login', $data);
    }

    public function auth()
    {
        $model = new UserModel();
        
        // Get email and password from your form
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        $remember = $this->request->getPost('remember');

        // Find user by email
        $user = $model->where('email', $email)->first();

        // Verify password
        if ($user && password_verify($password, $user['password'])) {
            
            // Set session data
            session()->set([
                'logged_in' => true,
                'user_id'   => $user['id'],
                'role'      => $user['role'],
                'email'     => $user['email'],
                'name'      => $user['name']
            ]);

            // Handle "Remember me" functionality
            if ($remember) {
                $this->response->setCookie('user_email', $email, 60 * 60 * 24 * 30);
                $this->response->setCookie('user_id', $user['id'], 60 * 60 * 24 * 30);
            }

            // REMOVE or COMMENT OUT this line:
            // session()->commit();

            // Redirect based on role
            return redirect()->to($user['role'] . '/dashboard');
        }

        // Failed login
        return redirect()->back()->with('error', 'Invalid email or password');
    }

    public function logout()
    {
        session()->destroy();
        
        $this->response->deleteCookie('user_email');
        $this->response->deleteCookie('user_id');
        
        return redirect()->to('/login');
    }
}
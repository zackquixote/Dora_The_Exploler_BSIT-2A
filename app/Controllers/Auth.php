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
        return view('login');
    }


    public function auth()
    {
        $model = new UserModel();
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $user = $model->where('username', $username)->first();

        if ($user && password_verify($password, $user['password'])) {
            // ✅ Set session data
            session()->set([
                'logged_in' => true,
                'user_id'   => $user['id'],
                'role'      => $user['role'],      // 'staff' or 'admin'
                'username'  => $user['username']
            ]);

            // Force session write
            session()->commit();

            // Redirect based on role
            return redirect()->to($user['role'] . '/dashboard');
        }

        return redirect()->back()->with('error', 'Invalid credentials');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }
}
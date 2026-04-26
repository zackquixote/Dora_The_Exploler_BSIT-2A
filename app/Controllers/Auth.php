<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;

class Auth extends BaseController
{
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
        session()->destroy();
        
        $this->response->deleteCookie('user_email');
        $this->response->deleteCookie('user_id');
        
        return redirect()->to('/login');
    }
}
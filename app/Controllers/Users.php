<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;
use App\Models\LogModel;

class Users extends Controller
{
    public function index()
    {
        $model = new UserModel();
        $data['users'] = $model->findAll();
        return view('users/index', $data);
    }

    public function save()
    {
        $name = $this->request->getPost('name');
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        $role = $this->request->getPost('role');
        $status = $this->request->getPost('status');
        $phone = $this->request->getPost('phone');

        if (!$email || !$password) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Email and password are required']);
        }

        $userModel = new \App\Models\UserModel();
        $logModel = new LogModel();

        // Check if email already exists
        $existingUser = $userModel->where('email', $email)->first();
        if ($existingUser) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Email is already in use']);
        }

        $data = [
            'uuid'       => uniqid(), // Generate unique ID
            'name'       => $name,
            'email'      => $email,
            'password'   => password_hash($password, PASSWORD_DEFAULT),
            'role'       => $role,
            'status'     => $status,
            'phone'      => $phone,
            'updated_at' => date('Y-m-d H:i:s'),
            'deleted_at' => null // New users shouldn't have deleted_at set
        ];

        if ($userModel->insert($data)) {
            $logModel->addLog('New User has been added: ' . $name, 'ADD');
            return $this->response->setJSON(['status' => 'success']);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to save user']);
        }
    }

    public function update()
    {
        $model = new UserModel();
        $logModel = new LogModel();
        $userId = $this->request->getPost('id');
        $name = $this->request->getPost('name');
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        $role = $this->request->getPost('role');
        $status = $this->request->getPost('status');
        $phone = $this->request->getPost('phone');

        // Validate the input
        if (empty($email)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Email is required']);
        }

        // Check if email already exists for another user
        $existingUser = $model->where('email', $email)
            ->where('id !=', $userId)
            ->first();

        if ($existingUser) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Email is already in use by another user.'
            ]);
        }

        $userData = [
            'name'       => $name,
            'email'      => $email,
            'role'       => $role,
            'status'     => $status,
            'phone'      => $phone,
            'updated_at' => date('Y-m-d H:i:s')
            // Do NOT set deleted_at here - that's only for soft deletes
        ];

        if (!empty($password)) {
            $userData['password'] = password_hash($password, PASSWORD_BCRYPT);
        }

        $updated = $model->update($userId, $userData);

        if ($updated) {
            $logModel->addLog('User has been updated: ' . $name, 'UPDATED');
            return $this->response->setJSON([
                'success' => true,
                'message' => 'User updated successfully.'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error updating user.'
            ]);
        }
    }

    public function edit($id)
    {
        $model = new UserModel();
        $user = $model->find($id); // Fetch user by ID

        if ($user) {
            return $this->response->setJSON(['data' => $user]); // Return user data as JSON
        } else {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'User not found']);
        }
    }

    public function delete($id)
    {
        $model = new UserModel();
        $logModel = new LogModel();
        $user = $model->find($id);
        
        if (!$user) {
            return $this->response->setJSON(['success' => false, 'message' => 'User not found.']);
        }

        $deleted = $model->delete($id);

        if ($deleted) {
            $logModel->addLog('User deleted: ' . $user['name'], 'DELETED');
            return $this->response->setJSON(['success' => true, 'message' => 'User deleted successfully.']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to delete user.']);
        }
    }

    public function fetchRecords()
    {
        try {
            $request = service('request');
            $model = new \App\Models\UserModel();

            $start = $request->getPost('start') ?? 0;
            $length = $request->getPost('length') ?? 10;
            $searchValue = $request->getPost('search')['value'] ?? '';

            $totalRecords = $model->countAll();
            $result = $model->getRecords($start, $length, $searchValue);

            $data = [];
            $counter = $start + 1;
            foreach ($result['data'] as $row) {
                $row['row_number'] = $counter++;
                
                // Format the data for DataTables
                $data[] = [
                    $row['row_number'],
                    $row['name'],
                    $row['email'],
                    $row['role'],
                    $row['status'],
                    '<button class="btn btn-sm btn-info edit-btn" data-id="' . $row['id'] . '">Edit</button> ' .
                    '<button class="btn btn-sm btn-danger delete-btn" data-id="' . $row['id'] . '">Delete</button>'
                ];
            }

            return $this->response->setJSON([
                'draw' => intval($request->getPost('draw')),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $result['filtered'],
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'error' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }
}
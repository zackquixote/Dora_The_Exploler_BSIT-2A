<?php

namespace App\Controllers;

use CodeIgniter\Controller;

/**
 * Users Controller
 * 
 * Manages system user accounts: listing, creating, editing, deleting.
 * Uses DataTables for AJAX listing and inline edits.
 * 
 * METHODS:
 * - index(): Displays the main user management view.
 * - fetchRecords(): DataTables server-side processing for user list.
 * - edit($id): Returns user data for editing (AJAX).
 * - update(): Updates user details (name, email, role, status, optional password).
 * - delete($id): Deletes a user account.
 * - create(): Displays the "add user" form.
 * - save(): Validates unique email and inserts new user.
 * 
 * DEPENDENCIES:
 * - Database connection for raw queries.
 * - No explicit model used; uses Query Builder directly.
 * 
 * @package App\Controllers
 */
class Users extends Controller
{
    public function index()
    {
        return view('users/index');
    }

    public function fetchRecords()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404)->setJSON(['message' => 'Invalid request']);
        }

        $db = \Config\Database::connect();
        $builder = $db->table('users');

        $draw   = $this->request->getPost('draw');
        $start  = $this->request->getPost('start');
        $length = $this->request->getPost('length');
        $search = $this->request->getPost('search')['value'];

        $builder->select('id, name, email, role, status, phone, created_at');

        if (!empty($search)) {
            $builder->groupStart();
            $builder->like('name', $search);
            $builder->orLike('email', $search);
            $groupEnd();
        }

        $totalRecords = $builder->countAllResults(false);
        $totalFiltered = (!empty($search)) ? $builder->countAllResults(false) : $totalRecords;

        $builder->orderBy('id', 'DESC');
        $builder->limit($length, $start);
        $query = $builder->get();
        $result = $query->getResultArray();

        $data = [];
        $num = $start + 1;

        foreach ($result as $row) {
            $actions = '
                <button class="btn btn-sm btn-warning edit-btn" data-id="' . $row['id'] . '">
                    <i class="far fa-edit"></i>
                </button>
                <button class="btn btn-sm btn-danger deleteUserBtn" data-id="' . $row['id'] . '">
                    <i class="fas fa-trash-alt"></i>
                </button>
            ';

            // We construct the array so DataTables can read it by name
            $data[] = [
                'no'         => $num++,
                'id'         => $row['id'],
                'name'       => $row['name'],
                'email'      => $row['email'],
                'role'       => $row['role'],
                'status'     => $row['status'],
                'phone'      => $row['phone'],
                'created_at' => $row['created_at'],
                'actions'    => $actions
            ];
        }

        $response = [
            "draw"            => intval($draw),
            "recordsTotal"    => $totalRecords,
            "recordsFiltered" => $totalFiltered,
            "data"            => $data
        ];

        return $this->response->setJSON($response);
    }

   
    public function edit($id = null)
    {
        if (!$this->request->isAJAX()) return $this->response->setStatusCode(404);
        
        $db = \Config\Database::connect();
        $user = $db->table('users')->where('id', $id)->get()->getRowArray();

        if ($user) {
            return $this->response->setJSON(['status' => 'success', 'data' => $user]);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'User not found']);
        }
    }

    public function update()
    {
        $id = $this->request->getPost('userId');
        $data = [
            'name'   => $this->request->getPost('name'),
            'email'  => $this->request->getPost('email'),
            'role'   => $this->request->getPost('role'),
            'status' => $this->request->getPost('status'),
        ];

        $password = $this->request->getPost('password');
        if (!empty($password)) {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $db = \Config\Database::connect();
        $builder = $db->table('users');

        if ($builder->where('id', $id)->update($data)) {
            return $this->response->setJSON(['success' => true, 'message' => 'User updated successfully']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to update user']);
        }
    }

    public function delete($id = null)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('users');

        if ($builder->where('id', $id)->delete()) {
            return $this->response->setJSON(['success' => true, 'message' => 'User deleted successfully']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to delete user']);
        }
    }

        // ... your existing code ...

    // ADD THIS NEW METHOD
    public function create()
    {
        return view('users/create');
    }

      public function save()
    {
        // 1. Get the email from the input
        $email = $this->request->getPost('email');

        // 2. Check if email already exists in database
        $db = \Config\Database::connect();
        $exists = $db->table('users')->where('email', $email)->countAllResults();

        if ($exists > 0) {
            // Email exists, redirect back with error
            return redirect()->back()->with('error', 'Email already exists. Please use a different email.');
        }

        // 3. Prepare data (Only runs if email is unique)
        $data = [
            'name'     => $this->request->getPost('name'),
            'email'    => $email,
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role'     => $this->request->getPost('role'),
            'status'   => $this->request->getPost('status'),
            'phone'    => $this->request->getPost('phone'),
        ];

        // 4. Insert data
        $builder = $db->table('users');

        if ($builder->insert($data)) {
            return redirect()->to('/admin/users')->with('success', 'User added successfully');
        } else {
            return redirect()->back()->with('error', 'Failed to add user');
        }
    }
    
}
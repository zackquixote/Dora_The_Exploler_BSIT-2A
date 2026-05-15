<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\LogModel;

class Users extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index()
    {
        return view('users/index');
    }

    public function fetchRecords()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404)->setJSON(['message' => 'Invalid request']);
        }

        // Use explicit builders: DataTables counts must not mutate the data query builder.
        $builder = $this->userModel->builder();
        $builder->select('id, name, email, role, status, phone, created_at');

        // Manually add soft‑delete condition because builder() does not auto‑filter
        $builder->where('deleted_at', null);

        $draw   = (int) ($this->request->getPost('draw') ?? 0);
        $start  = (int) ($this->request->getPost('start') ?? 0);
        $length = (int) ($this->request->getPost('length') ?? 10);

        $searchArr = $this->request->getPost('search');
        $search = (is_array($searchArr) && isset($searchArr['value'])) ? trim((string) $searchArr['value']) : '';

        if (!empty($search)) {
            $builder->groupStart();
            $builder->like('name', $search);
            $builder->orLike('email', $search);
            $builder->groupEnd();   // ← FIXED
        }

        // Total records (unfiltered)
        $totalRecords = $this->userModel->builder()
            ->where('deleted_at', null)
            ->countAllResults();

        // Total filtered records (apply same filters as $builder, but on an independent builder)
        $countBuilder = $this->userModel->builder()->where('deleted_at', null);
        if (!empty($search)) {
            $countBuilder->groupStart()
                ->like('name', $search)
                ->orLike('email', $search)
                ->groupEnd();
        }
        $totalFiltered = $countBuilder->countAllResults();

        $builder->orderBy('id', 'DESC');
        $builder->limit($length, $start);
        $query = $builder->get();
        $result = $query->getResultArray();

        $data = [];
        $num = $start + 1;

        foreach ($result as $row) {
            $actions = '
                <div style="white-space:nowrap;display:flex;gap:6px">
                    <a href="' . base_url('admin/users/view/' . $row['id']) . '" class="ds-action-btn ab-blue" title="View">
                        <i class="fas fa-eye"></i>
                    </a>
                    <button type="button" class="ds-action-btn ab-amber edit-btn" data-id="' . $row['id'] . '" title="Edit">
                        <i class="fas fa-pen"></i>
                    </button>
                    <button type="button" class="ds-action-btn ab-rose deleteUserBtn" data-id="' . $row['id'] . '" title="Delete">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            ';

            $data[] = [
                'no'         => $num++,
                'id'         => $row['id'],
                'name'       => '<strong class="font-serif" style="font-size:14px;letter-spacing:-0.01em;">' . esc($row['name']) . '</strong>',
                'email'      => $row['email'],
                'role'       => $row['role'],
                'status'     => $row['status'],
                'phone'      => $row['phone'],
                'created_at' => $row['created_at'],
                'actions'    => $actions
            ];
        }

        $response = [
            "draw"            => $draw,
            "recordsTotal"    => $totalRecords,
            "recordsFiltered" => $totalFiltered,
            "data"            => $data
        ];

        return $this->response->setJSON($response);
    }

    public function view($id = null)
    {
        $user = $this->userModel->find($id);   // respects soft deletes
        if (!$user) {
            return redirect()->to('/admin/users')->with('error', 'User not found.');
        }
        return view('users/view', ['user' => $user]);
    }

    public function edit($id = null)
    {
        // Relax isAJAX check to allow requests that accept JSON (some environments strip X-Requested-With)
        $isAjaxOrJson = $this->request->isAJAX() || (strpos($this->request->getHeaderLine('Accept'), 'application/json') !== false);
        if (!$isAjaxOrJson) return $this->response->setStatusCode(404);

        try {
            $user = $this->userModel->find($id);
            if ($user) {
                $user['role'] = strtolower((string) ($user['role'] ?? 'staff'));
                $user['status'] = strtolower((string) ($user['status'] ?? 'active'));
                return $this->response->setJSON(['status' => 'success', 'data' => $user, 'csrf_hash' => csrf_hash()]);
            } else {
                return $this->response->setJSON(['status' => 'error', 'message' => 'User not found', 'csrf_hash' => csrf_hash()]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Error fetching user data: ' . $e->getMessage());
            return $this->response->setJSON(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage(), 'csrf_hash' => csrf_hash()]);
        }
    }

    public function update()
    {
        $id = $this->request->getPost('userId');
        $user = $this->userModel->find($id);
        if (!$user) {
            return $this->response->setJSON(['success' => false, 'message' => 'User not found', 'csrf_hash' => csrf_hash()]);
        }

        $email = trim((string) $this->request->getPost('email'));
        $emailOwner = $this->userModel->where('email', $email)->first();
        if ($emailOwner && (int) $emailOwner['id'] !== (int) $id) {
            return $this->response->setJSON(['success' => false, 'message' => 'Email already exists.', 'csrf_hash' => csrf_hash()]);
        }

        $data = [
            'name'   => $this->request->getPost('name'),
            'email'  => $email,
            // Normalize to match DB conventions used across the app
            'role'   => strtolower(trim((string) $this->request->getPost('role'))),
            'status' => strtolower(trim((string) $this->request->getPost('status'))),
            'phone'  => trim((string) $this->request->getPost('phone')),
        ];

        $password = $this->request->getPost('password');
        if (!empty($password)) {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        if ($this->userModel->update($id, $data)) {
            return $this->response->setJSON(['success' => true, 'message' => 'User updated successfully', 'csrf_hash' => csrf_hash()]);
        }
        return $this->response->setJSON(['success' => false, 'message' => 'Failed to update user', 'csrf_hash' => csrf_hash()]);
    }

    public function delete($id = null)
    {
        $user = $this->userModel->find($id);
        if (!$user) {
            return $this->response->setJSON(['success' => false, 'message' => 'User not found', 'csrf_hash' => csrf_hash()]);
        }

        // Soft delete via the model – sets deleted_at, preserves record
        if ($this->userModel->delete($id)) {
            return $this->response->setJSON(['success' => true, 'message' => 'User deleted successfully', 'csrf_hash' => csrf_hash()]);
        }
        return $this->response->setJSON(['success' => false, 'message' => 'Failed to delete user', 'csrf_hash' => csrf_hash()]);
    }

    public function create()
    {
        return view('users/create');
    }

    public function save()
    {
        $email = trim((string) $this->request->getPost('email'));
        $wantsJson = $this->request->isAJAX() || $this->requestExpectsJsonResponse();

        $exists = $this->userModel->where('email', $email)->first();
        if ($exists) {
            if ($wantsJson) {
                return $this->response->setJSON([
                    'status'    => 'error',
                    'message'   => 'Email already exists. Please use a different email.',
                    'csrf_hash' => csrf_hash(),
                ]);
            }

            return redirect()->back()->with('error', 'Email already exists. Please use a different email.');
        }

        $data = [
            'name'     => $this->request->getPost('name'),
            'email'    => $email,
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role'     => strtolower(trim((string) $this->request->getPost('role'))),
            'status'   => strtolower(trim((string) $this->request->getPost('status'))),
            'phone'    => trim((string) $this->request->getPost('phone')),
        ];

        if ($this->userModel->insert($data)) {
            if ($wantsJson) {
                return $this->response->setJSON([
                    'status'    => 'success',
                    'message'   => 'User added successfully',
                    'csrf_hash' => csrf_hash(),
                ]);
            }

            return redirect()->to('/admin/users')->with('success', 'User added successfully');
        }

        if ($wantsJson) {
            return $this->response->setJSON([
                'status'    => 'error',
                'message'   => 'Failed to add user',
                'csrf_hash' => csrf_hash(),
            ]);
        }

        return redirect()->back()->with('error', 'Failed to add user');
    }
}
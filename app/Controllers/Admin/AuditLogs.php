<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AuditLogModel;
use App\Models\UserModel;

class AuditLogs extends BaseController
{
    public function index()
    {
        $model = new AuditLogModel();

        $date    = $this->request->getGet('date');
        $userId  = $this->request->getGet('user_id');
        $entity  = $this->request->getGet('entity');
        $action  = $this->request->getGet('action');
        $keyword = trim($this->request->getGet('keyword') ?? '');
        $page    = max(1, (int)($this->request->getGet('page') ?? 1));
        $perPage = 50;

        $builder = $model->builder();
        $builder->orderBy('created_at', 'DESC');

        if (!empty($date)) {
            $builder->where('DATE(created_at)', $date);
        }
        if (!empty($userId)) {
            $builder->where('user_id', (int) $userId);
        }
        if (!empty($entity)) {
            $builder->where('entity', $entity);
        }
        if (!empty($action)) {
            $builder->where('action', $action);
        }
        if (!empty($keyword)) {
            $builder->groupStart()
                ->like('entity', $keyword)
                ->orLike('action', $keyword)
                ->orLike('ip_address', $keyword)
                ->groupEnd();
        }

        $totalCount = $builder->countAllResults(false);
        $totalPages = max(1, (int)ceil($totalCount / $perPage));
        $offset     = ($page - 1) * $perPage;

        $rows = $builder->limit($perPage, $offset)->get()->getResultArray();

        // User list for filter dropdown
        $users = (new UserModel())
            ->select('id, name, role')
            ->orderBy('name', 'ASC')
            ->findAll();

        return view('Admin/audit_logs/index', [
            'title'         => 'Audit Logs',
            'logs'          => $rows,
            'users'         => $users,
            'selectedDate'  => $date,
            'selectedUser'  => $userId,
            'selectedEntity'=> $entity,
            'selectedAction'=> $action,
            'keyword'       => $keyword,
            'currentPage'   => $page,
            'totalPages'    => $totalPages,
            'totalCount'    => $totalCount,
            'perPage'       => $perPage,
        ]);
    }
}


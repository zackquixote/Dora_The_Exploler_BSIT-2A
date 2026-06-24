<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\LogModel;
use App\Models\UserModel;

class AuditLogs extends BaseController
{
    public function index()
    {
        // NOTE:
        // The legacy audit_logs/audit_logs table was removed in favor of tbl_logs.
        // This controller now reads from tbl_logs so the page does not break.
        $model = new LogModel();

        $date    = $this->request->getGet('date');
        $userId  = $this->request->getGet('user_id');
        $entity  = $this->request->getGet('entity');
        $action  = $this->request->getGet('action');
        $keyword = trim($this->request->getGet('keyword') ?? '');
        $page    = max(1, (int)($this->request->getGet('page') ?? 1));
        $perPage = 50;

        $builder = $model->builder();
        // TIMELOG stores full datetime string; use it for ordering
        $builder->orderBy('TIMELOG', 'DESC');

        if (!empty($date)) {
            $builder->where('DATELOG', $date);
        }
        if (!empty($userId)) {
            $builder->where('USERID', (string) ((int) $userId));
        }
        if (!empty($entity)) {
            $builder->where('identifier', $entity);
        }
        if (!empty($action)) {
            // match either exact or "contains" depending on how logs are written
            $builder->like('ACTION', $action);
        }
        if (!empty($keyword)) {
            $builder->groupStart()
                ->like('identifier', $keyword)
                ->orLike('ACTION', $keyword)
                ->orLike('user_ip_address', $keyword)
                ->groupEnd();
        }

        $totalCount = $builder->countAllResults(false);
        $totalPages = max(1, (int)ceil($totalCount / $perPage));
        $offset     = ($page - 1) * $perPage;

        $raw = $builder->limit($perPage, $offset)->get()->getResultArray();

        // Map tbl_logs columns into the view's expected keys
        $rows = array_map(static function (array $r): array {
            return [
                'created_at' => $r['TIMELOG'] ?? '',
                'user_id'    => $r['USERID'] ?? '',
                'action'     => $r['ACTION'] ?? '',
                'entity'     => $r['identifier'] ?? '',
                'entity_id'  => '',
                'ip_address' => $r['user_ip_address'] ?? '',
            ];
        }, $raw);

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


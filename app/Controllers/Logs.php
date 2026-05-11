<?php

namespace App\Controllers;

use App\Models\LogModel;
use App\Controllers\BaseController;

/**
 * Logs Controller
 * 
 * Displays system activity logs with optional date filtering.
 * 
 * METHODS:
 * - log(): Shows all logs or filters by a specific date.
 * 
 * DEPENDENCIES:
 * - LogModel for retrieving log entries.
 * 
 * @package App\Controllers
 */
class Logs extends BaseController
{

    /**
     * Execute log functionality.
     *
     * @return mixed
     */
    public function log()
    {
        $logModel = new \App\Models\LogModel();
        
        $date = $this->request->getGet('date');
        $user = $this->request->getGet('user');
        $action = $this->request->getGet('action');

        $builder = $logModel->builder();
        $builder->orderBy('DATELOG DESC, TIMELOG DESC');

        if (!empty($date)) {
            $builder->where('DATELOG', $date);
        }
        if (!empty($user)) {
            $builder->where('USER_NAME', $user);
        }
        if (!empty($action)) {
            $actionLower = strtolower($action);
            if ($actionLower === 'create') {
                $builder->groupStart()
                        ->like('ACTION', 'create')
                        ->orLike('ACTION', 'add')
                        ->orLike('ACTION', 'generate')
                        ->groupEnd();
            } elseif ($actionLower === 'update') {
                $builder->groupStart()
                        ->like('ACTION', 'update')
                        ->orLike('ACTION', 'edit')
                        ->groupEnd();
            } elseif ($actionLower === 'delete') {
                $builder->groupStart()
                        ->like('ACTION', 'delete')
                        ->orLike('ACTION', 'remove')
                        ->groupEnd();
            } elseif ($actionLower === 'login') {
                $builder->groupStart()
                        ->like('ACTION', 'log')
                        ->groupEnd();
            } else {
                $builder->like('ACTION', $action);
            }
        }

        $data['logs'] = $builder->get()->getResultArray();
        $data['selectedDate'] = $date;
        $data['selectedUser'] = $user;
        $data['selectedAction'] = $action;

        // Get unique users for the dropdown
        $data['users'] = $logModel->builder()->select('USER_NAME')->distinct()->orderBy('USER_NAME', 'ASC')->get()->getResultArray();

        return view('log/index', $data);
    }

}
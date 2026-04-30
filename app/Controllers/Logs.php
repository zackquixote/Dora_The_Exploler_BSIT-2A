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

    public function log()
    {
        $logModel = new \App\Models\LogModel();
        
        $date = $this->request->getGet('date');

        // IF NO DATE IS SELECTED, SHOW ALL LOGS
        if (empty($date)) {
            $data['logs'] = $logModel->orderBy('DATELOG DESC, TIMELOG DESC')->findAll();
            $data['selectedDate'] = ''; // Empty so the date input is blank
        } else {
            // IF DATE IS SELECTED, FILTER BY THAT DATE
            $data['logs'] = $logModel->getLogsByDate($date);
            $data['selectedDate'] = $date;
        }

        return view('log/index', $data);
    }

}
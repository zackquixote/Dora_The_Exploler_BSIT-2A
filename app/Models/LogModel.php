<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * LogModel
 * 
 * Handles activity logging for user actions within the system.
 * 
 * TABLE: tbl_logs
 * - Records each significant user action with timestamp, IP address, device info.
 * 
 * FIELDS:
 * - LOGID: Primary key
 * - USERID, USER_NAME: Reference to the user performing action
 * - ACTION: Description of action performed
 * - DATELOG, TIMELOG: Date and time of the action (Asia/Manila timezone)
 * - user_ip_address: Client IP address
 * - device_used: User agent string
 * - identifier: Optional categorisation/type of action
 * 
 * METHODS:
 * - addLog($action, $type): Inserts a new log entry using session data and current timestamp
 * - getLogs(): Returns all logs ordered by date/time descending
 * - getLogsByDate($date): Filters logs by a specific date
 * - getLogsByDateAndResid($date, $userId): Filters by date and user ID
 * - getLogsPerMonth(): Returns monthly log count for analytics
 * 
 * @package App\Models
 */
class LogModel extends Model
{
    protected $table = 'tbl_logs';
    protected $primaryKey = 'LOGID';
    protected $allowedFields = [
        'USERID', 'USER_NAME', 'ACTION', 'DATELOG', 'TIMELOG',
        'user_ip_address', 'device_used', 'identifier'
    ];


    /**
     * Execute addLog functionality.
     *
     * @return mixed
     */
    public function addLog(string $action, string $type = '')
    {
        date_default_timezone_set('Asia/Manila');

        $session = session();
        $request = service('request');
        
        $this->insert([
            'USERID'          => (string) $session->get('user_id'),
            'USER_NAME'       => $session->get('name'),
            'ACTION'          => $action,
            'DATELOG'         => date('Y-m-d'),      // Date only for date-based filtering
            'TIMELOG'         => date('Y-m-d H:i:s'), // Full datetime for time display
            'user_ip_address' => $request->getIPAddress(),
            'device_used'     => $request->getUserAgent()->getAgentString(),
            'identifier'      => $type
        ]);
    }

    public function getLogs()
    {
        return $this->orderBy('DATELOG', 'DESC')->orderBy('TIMELOG', 'DESC')->findAll();
    }

    public function getLogsByDate($date)
    {
        return $this->where('DATELOG', $date)
                    ->orderBy('TIMELOG', 'DESC')
                    ->findAll();
    }

    /**
     * Execute getLogsByDateAndResid functionality.
     *
     * @return mixed
     */
    public function getLogsByDateAndResid($date, $userId)
    {
        return $this->where('DATELOG', $date)
                    ->where('USERID', $userId)
                    ->orderBy('TIMELOG', 'DESC')
                    ->findAll();
    }

    /**
     * Execute getLogsPerMonth functionality.
     *
     * @return mixed
     */
    public function getLogsPerMonth()
    {
        return $this->db->table('tbl_logs')
            ->select("MONTH(STR_TO_DATE(DATELOG, '%Y-%m-%d')) AS month_num, COUNT(*) AS total_logs")
            ->where('DATELOG IS NOT NULL', null, false)
            ->groupBy('month_num')
            ->orderBy('month_num')
            ->get()
            ->getResultArray();
    }
}
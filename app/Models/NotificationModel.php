<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * NotificationModel
 * Handles all notification records and delivery tracking
 */
class NotificationModel extends Model
{
    protected $table = 'notifications';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'recipient_type',
        'recipient_id',
        'type',
        'title',
        'message',
        'channels',
        'status',
        'scheduled_at',
        'sent_at',
        'metadata',
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'recipient_type' => 'required|in_list[resident,user,all,group]',
        'type' => 'required|max_length[50]',
        'title' => 'required|max_length[255]',
        'message' => 'required',
        'channels' => 'required',
    ];

    /**
     * Get notifications for a specific recipient
     */
    public function getForRecipient(string $recipientType, int $recipientId, int $limit = 50): array
    {
        return $this->where('recipient_type', $recipientType)
                   ->where('recipient_id', $recipientId)
                   ->orderBy('created_at', 'DESC')
                   ->limit($limit)
                   ->findAll();
    }

    /**
     * Get pending notifications
     */
    public function getPending(): array
    {
        return $this->where('status', 'pending')
                   ->where('(scheduled_at IS NULL OR scheduled_at <= NOW())')
                   ->findAll();
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(int $notificationId): bool
    {
        return $this->update($notificationId, ['status' => 'read']);
    }

    /**
     * Get notification statistics
     */
    public function getStats(array $dateRange = []): array
    {
        $builder = $this->builder();
        
        if (!empty($dateRange)) {
            $builder->where('created_at >=', $dateRange['start'])
                   ->where('created_at <=', $dateRange['end']);
        }

        $stats = $builder->select('
            COUNT(*) as total,
            SUM(CASE WHEN status = "sent" THEN 1 ELSE 0 END) as sent,
            SUM(CASE WHEN status = "delivered" THEN 1 ELSE 0 END) as delivered,
            SUM(CASE WHEN status = "failed" THEN 1 ELSE 0 END) as failed,
            SUM(CASE WHEN status = "read" THEN 1 ELSE 0 END) as read_count
        ')->get()->getRowArray();

        return $stats;
    }

    /**
     * Clean old notifications
     */
    public function cleanOldNotifications(int $daysOld = 90): int
    {
        $cutoffDate = date('Y-m-d H:i:s', strtotime("-{$daysOld} days"));
        
        return $this->where('created_at <', $cutoffDate)
                   ->where('status !=', 'pending')
                   ->delete();
    }
}
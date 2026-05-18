<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * AuditLogModel
 *
 * Reads/writes to the new `audit_logs` table.
 * Phase 1A: viewer + basic write helper via AuditService.
 */
class AuditLogModel extends Model
{
    protected $table            = 'audit_logs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'user_id',
        'action',
        'entity',
        'entity_id',
        'old_data',
        'new_data',
        'ip_address',
        'user_agent',
        'session_id',
        'created_at',
    ];

    protected $useTimestamps = false;
}


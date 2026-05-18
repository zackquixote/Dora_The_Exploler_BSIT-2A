<?php

namespace App\Services;

use App\Models\AuditLogModel;

/**
 * AuditService
 *
 * Lightweight audit logger.
 * Phase 1A: log critical auth/system actions; expand to CRUD later.
 */
class AuditService
{
    protected AuditLogModel $model;

    public function __construct()
    {
        $this->model = new AuditLogModel();
    }

    /**
     * @param array<mixed>|null $oldData
     * @param array<mixed>|null $newData
     */
    public function log(string $action, string $entity, int $entityId = 0, ?array $oldData = null, ?array $newData = null): void
    {
        try {
            $request = service('request');

            $this->model->insert([
                'user_id'    => (int) (session()->get('user_id') ?? 0),
                'action'     => $action,
                'entity'     => $entity,
                'entity_id'  => $entityId,
                'old_data'   => $oldData ? json_encode($oldData) : null,
                'new_data'   => $newData ? json_encode($newData) : null,
                'ip_address' => (string) $request->getIPAddress(),
                'user_agent' => (string) $request->getUserAgent(),
                'session_id' => (string) session_id(),
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        } catch (\Throwable $e) {
            // Never block the main action if audit logging fails.
        }
    }
}


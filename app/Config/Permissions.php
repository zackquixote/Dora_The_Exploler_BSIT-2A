<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Permissions (RBAC groundwork)
 *
 * This is a lightweight, config-based permissions matrix.
 * Phase 1A goal: provide a single source of truth for what each role can do.
 *
 * Notes:
 * - Use "*" to allow all permissions for a role.
 * - Keep keys stable (e.g., "audit.view") so filters/controllers can reference them.
 */
class Permissions extends BaseConfig
{
    /**
     * @var array<string, array<int, string>>
     */
    public array $rolePermissions = [
        // Admin: full access
        'admin' => ['*'],

        // Staff: internal operations (no system settings)
        'staff' => [
            'residents.view',
            'residents.manage',
            'households.view',
            'households.manage',
            'certificates.view',
            'certificates.manage',
            'blotter.view',
            'blotter.manage',
            'officials.view',
            'officials.manage',
            'activity_logs.view',
        ],

        // Resident: used by Phase 1C Portal (scaffold)
        'resident' => [
            'portal.view',
            'requests.create',
            'requests.view_own',
        ],
    ];
}


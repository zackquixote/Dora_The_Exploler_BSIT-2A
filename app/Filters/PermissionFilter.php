<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Permissions;

/**
 * PermissionFilter
 *
 * Usage in Routes:
 *   ['filter' => 'perm:audit.view']
 *
 * Depends on session('role') being set (admin/staff/resident).
 */
class PermissionFilter implements FilterInterface
{
    use SendsJsonWhenUnauthorized;

    public function before(RequestInterface $request, $arguments = null)
    {
        if (! session()->get('logged_in')) {
            $json = $this->jsonUnauthorizedResponse($request);
            if ($json !== null) {
                return $json;
            }

            return redirect()->to('/login')->with('error', 'Please log in to continue.');
        }

        $permission = (string) (($arguments[0] ?? '') ?: '');
        if ($permission === '') {
            // Misconfigured route: no permission key.
            return;
        }

        $role = strtolower((string) (session()->get('role') ?? ''));

        $config = config(Permissions::class);
        $rolePerms = $config->rolePermissions[$role] ?? [];

        // Admin wildcard
        if (in_array('*', $rolePerms, true)) {
            return;
        }

        if (in_array($permission, $rolePerms, true)) {
            return;
        }

        $json = $this->jsonForbiddenResponse($request, 'Access denied.', 'login');
        if ($json !== null) {
            return $json;
        }

        return redirect()->to('/login')->with('error', 'Access denied.');
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}


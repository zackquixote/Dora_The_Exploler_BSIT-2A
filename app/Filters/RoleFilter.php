<?php
namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class RoleFilter implements FilterInterface
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

        $role    = strtolower((string) (session()->get('role') ?? ''));
        $allowed = array_map(static fn ($r) => strtolower((string) $r), $arguments ?? []);

        // No role arguments = any authenticated user (misconfigured empty list used to deny everyone)
        if ($allowed === []) {
            return;
        }

        if (in_array($role, $allowed, true)) {
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
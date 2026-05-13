<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

/**
 * AdminFilter
 * Applied to all /admin/* routes.
 * Redirects to login if not logged in.
 * Redirects to staff dashboard if logged in but not admin.
 */
class AdminFilter implements FilterInterface
{
    use SendsJsonWhenUnauthorized;

    public function before(RequestInterface $request, $arguments = null)
    {
        // Not logged in at all → login page
        if (! session()->get('logged_in')) {
            $json = $this->jsonUnauthorizedResponse($request);
            if ($json !== null) {
                return $json;
            }

            return redirect()->to('/login')->with('error', 'Please log in to continue.');
        }

        // Logged in but wrong role → their own dashboard
        if (strtolower((string) (session()->get('role') ?? '')) !== 'admin') {
            $json = $this->jsonForbiddenResponse(
                $request,
                'Access denied. Admins only.',
                'staff/dashboard'
            );
            if ($json !== null) {
                return $json;
            }

            return redirect()->to('/staff/dashboard')->with('error', 'Access denied. Admins only.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Nothing needed after
    }
}
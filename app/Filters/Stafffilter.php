<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

/**
 * StaffFilter
 * Applied to all /staff/* routes.
 * Redirects to login if not logged in.
 * Redirects to admin dashboard if logged in but not staff.
 */
class StaffFilter implements FilterInterface
{
    use SendsJsonWhenUnauthorized;

    public function before(RequestInterface $request, $arguments = null)
    {
        // Not logged in → login page
        if (! session()->get('logged_in')) {
            $json = $this->jsonUnauthorizedResponse($request);
            if ($json !== null) {
                return $json;
            }

            return redirect()->to('/login')->with('error', 'Please log in to continue.');
        }

        // Logged in but wrong role → their own dashboard
        if (strtolower((string) (session()->get('role') ?? '')) !== 'staff') {
            $json = $this->jsonForbiddenResponse(
                $request,
                'Access denied. Staff only.',
                'admin/dashboard'
            );
            if ($json !== null) {
                return $json;
            }

            return redirect()->to('/admin/dashboard')->with('error', 'Access denied. Staff only.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Nothing needed after
    }
}
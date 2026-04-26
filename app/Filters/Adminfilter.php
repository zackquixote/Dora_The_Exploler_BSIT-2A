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
    public function before(RequestInterface $request, $arguments = null)
    {
        // Not logged in at all → login page
        if (!session()->get('logged_in')) {
            return redirect()->to('/login')->with('error', 'Please log in to continue.');
        }

        // Logged in but wrong role → their own dashboard
        if (strtolower(session()->get('role')) !== 'admin') {
            return redirect()->to('/staff/dashboard')->with('error', 'Access denied. Admins only.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Nothing needed after
    }
}
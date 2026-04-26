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
    public function before(RequestInterface $request, $arguments = null)
    {
        // Not logged in → login page
        if (!session()->get('logged_in')) {
            return redirect()->to('/login')->with('error', 'Please log in to continue.');
        }

        // Logged in but wrong role → their own dashboard
        if (strtolower(session()->get('role')) !== 'staff') {
            return redirect()->to('/admin/dashboard')->with('error', 'Access denied. Staff only.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Nothing needed after
    }
}
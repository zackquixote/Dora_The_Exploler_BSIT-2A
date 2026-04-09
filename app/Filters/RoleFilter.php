<?php
namespace App\Filters;
use CodeIgniter\HTTP\RequestInterface; 
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class RoleFilter implements FilterInterface  // ← renamed to RoleFilter (no 's')
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $role = session()->get('role');

        if ($arguments && in_array($role, $arguments)) {
            return; // Role matches, allow access
        }

        // Role doesn't match
        return redirect()->to('/login')->with('error', 'Access denied.');
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No post-processing needed
    }
}
<?php
namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $role = strtolower((string) (session()->get('role') ?? ''));
        $allowed = array_map(static fn($r) => strtolower((string) $r), $arguments ?? []);

        if ($arguments && in_array($role, $allowed, true)) {
            return; 
        }

        return redirect()->to('/login')->with('error', 'Access denied.');
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}
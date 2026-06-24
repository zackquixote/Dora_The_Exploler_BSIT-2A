<?php
namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class SecurityHeaders implements FilterInterface
{
    /**
     * Add Content Security Policy header to all responses.
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // No action needed before controller execution.
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Remove any existing CSP headers to avoid conflicts
        $response->removeHeader('Content-Security-Policy');
        $response->removeHeader('Content-Security-Policy-Report-Only');
        
        $csp = "default-src 'self'; script-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com https://cdn.jsdelivr.net; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net; font-src 'self' https://fonts.gstatic.com; img-src 'self' data: https:; connect-src 'self' http://localhost:* http://127.0.0.1:* https://; frame-src 'none'; object-src 'none';";
        $response->setHeader('Content-Security-Policy', $csp);
    }
}
?>

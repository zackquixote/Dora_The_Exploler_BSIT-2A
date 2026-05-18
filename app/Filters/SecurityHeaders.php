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
        $csp = "default-src 'self'; script-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com; img-src 'self' data:; connect-src 'self'; frame-src 'none'; object-src 'none';";
        $response->setHeader('Content-Security-Policy', $csp);
    }
}
?>

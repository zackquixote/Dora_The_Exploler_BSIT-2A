<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class DisableCSP implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Nothing needed before
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Remove CSP headers
        $response->removeHeader('Content-Security-Policy');
        $response->removeHeader('Content-Security-Policy-Report-Only');
        $response->removeHeader('X-Content-Security-Policy');
        $response->removeHeader('X-WebKit-CSP');
        
        return $response;
    }
}
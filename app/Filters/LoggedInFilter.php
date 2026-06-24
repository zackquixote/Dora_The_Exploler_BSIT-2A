<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class LoggedInFilter implements FilterInterface
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
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Prevent caching of authenticated pages so browser back cannot show stale protected content.
        $response->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
        $response->setHeader('Pragma', 'no-cache');
        $response->setHeader('Expires', '0');
    }
}
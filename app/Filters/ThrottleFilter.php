<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * ThrottleFilter
 *
 * Supports route syntax: throttle:{max},{minutes}
 * Example: ['filter' => 'throttle:5,1'] => 5 requests per 1 minute.
 */
class ThrottleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $max     = (int) ($arguments[0] ?? 60);
        $minutes = (int) ($arguments[1] ?? 1);
        $seconds = max(1, $minutes) * 60;

        $ip   = $request->getIPAddress() ?: 'unknown';
        $path = $request->getUri()->getPath();
        $key  = $ip . '_' . md5($path);

        /** @var \CodeIgniter\Throttle\Throttler $throttler */
        $throttler = service('throttler');

        if ($throttler->check($key, max(1, $max), $seconds)) {
            return;
        }

        $retryAfter = $throttler->getTokenTime();

        $accept = strtolower($request->getHeaderLine('Accept'));
        $isJson = (strtolower($request->getHeaderLine('X-Requested-With')) === 'xmlhttprequest')
            || str_contains($accept, 'application/json');

        $response = service('response')->setStatusCode(429)->setHeader('Retry-After', (string) $retryAfter);

        if ($isJson) {
            return $response->setJSON([
                'status'      => 'error',
                'message'     => 'Too many requests. Please try again later.',
                'retry_after' => $retryAfter,
            ]);
        }

        return $response->setBody('Too many requests. Please try again later.');
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}


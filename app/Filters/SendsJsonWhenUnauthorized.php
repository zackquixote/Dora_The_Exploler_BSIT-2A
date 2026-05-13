<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Shared behavior for auth-related filters: return JSON + 401 for API-style
 * requests instead of an HTML redirect (avoids broken $.ajax dataType: "json").
 */
trait SendsJsonWhenUnauthorized
{
    protected function jsonUnauthorizedResponse(RequestInterface $request, string $message = 'Please log in to continue.'): ?ResponseInterface
    {
        if (! $this->clientExpectsJson($request)) {
            return null;
        }

        return service('response')
            ->setStatusCode(401)
            ->setJSON([
                'status'   => 'error',
                'message'  => $message,
                'redirect' => site_url('login'),
            ]);
    }

    protected function clientExpectsJson(RequestInterface $request): bool
    {
        if (strtolower($request->getHeaderLine('X-Requested-With')) === 'xmlhttprequest') {
            return true;
        }

        return str_contains(strtolower($request->getHeaderLine('Accept')), 'application/json');
    }

    /**
     * Wrong role or forbidden resource — JSON + 403 for XHR / JSON clients.
     */
    protected function jsonForbiddenResponse(
        RequestInterface $request,
        string $message,
        string $redirectPathOrUrl
    ): ?ResponseInterface {
        if (! $this->clientExpectsJson($request)) {
            return null;
        }

        $redirectPathOrUrl = trim($redirectPathOrUrl);
        $redirectUrl       = (str_starts_with($redirectPathOrUrl, 'http://')
            || str_starts_with($redirectPathOrUrl, 'https://'))
            ? $redirectPathOrUrl
            : site_url(ltrim($redirectPathOrUrl, '/'));

        return service('response')
            ->setStatusCode(403)
            ->setJSON([
                'status'   => 'error',
                'message'  => $message,
                'redirect' => $redirectUrl,
            ]);
    }
}

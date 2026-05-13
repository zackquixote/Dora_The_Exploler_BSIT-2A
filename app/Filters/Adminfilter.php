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
    use SendsJsonWhenUnauthorized;

    public function before(RequestInterface $request, $arguments = null)
    {
        // Not logged in at all → login page
        if (! session()->get('logged_in')) {
            // #region agent log
            @file_put_contents(
                ROOTPATH . 'debug-0646ff.log',
                json_encode([
                    'sessionId'    => '0646ff',
                    'runId'        => 'pre-fix',
                    'hypothesisId' => 'H28',
                    'location'     => 'app/Filters/AdminFilter.php:24',
                    'message'      => 'admin filter unauthenticated redirect',
                    'data'         => ['path' => $request->getUri()->getPath()],
                    'timestamp'    => (int) round(microtime(true) * 1000),
                ], JSON_UNESCAPED_SLASHES) . PHP_EOL,
                FILE_APPEND
            );
            // #endregion
            $json = $this->jsonUnauthorizedResponse($request);
            if ($json !== null) {
                return $json;
            }

            return redirect()->to('/login')->with('error', 'Please log in to continue.');
        }

        // Logged in but wrong role → their own dashboard
        if (strtolower((string) (session()->get('role') ?? '')) !== 'admin') {
            // #region agent log
            @file_put_contents(
                ROOTPATH . 'debug-0646ff.log',
                json_encode([
                    'sessionId'    => '0646ff',
                    'runId'        => 'pre-fix',
                    'hypothesisId' => 'H29',
                    'location'     => 'app/Filters/AdminFilter.php:47',
                    'message'      => 'admin filter role mismatch redirect',
                    'data'         => [
                        'path'         => $request->getUri()->getPath(),
                        'session_role' => strtolower((string) (session()->get('role') ?? '')),
                    ],
                    'timestamp'    => (int) round(microtime(true) * 1000),
                ], JSON_UNESCAPED_SLASHES) . PHP_EOL,
                FILE_APPEND
            );
            // #endregion
            $json = $this->jsonForbiddenResponse(
                $request,
                'Access denied. Admins only.',
                'staff/dashboard'
            );
            if ($json !== null) {
                return $json;
            }

            return redirect()->to('/staff/dashboard')->with('error', 'Access denied. Admins only.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Nothing needed after
    }
}
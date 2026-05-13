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
    use SendsJsonWhenUnauthorized;

    public function before(RequestInterface $request, $arguments = null)
    {
        // Not logged in → login page
        if (! session()->get('logged_in')) {
            // #region agent log
            @file_put_contents(
                ROOTPATH . 'debug-0646ff.log',
                json_encode([
                    'sessionId'    => '0646ff',
                    'runId'        => 'pre-fix',
                    'hypothesisId' => 'H30',
                    'location'     => 'app/Filters/StaffFilter.php:24',
                    'message'      => 'staff filter unauthenticated redirect',
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
        if (strtolower((string) (session()->get('role') ?? '')) !== 'staff') {
            // #region agent log
            @file_put_contents(
                ROOTPATH . 'debug-0646ff.log',
                json_encode([
                    'sessionId'    => '0646ff',
                    'runId'        => 'pre-fix',
                    'hypothesisId' => 'H31',
                    'location'     => 'app/Filters/StaffFilter.php:47',
                    'message'      => 'staff filter role mismatch redirect',
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
                'Access denied. Staff only.',
                'admin/dashboard'
            );
            if ($json !== null) {
                return $json;
            }

            return redirect()->to('/admin/dashboard')->with('error', 'Access denied. Staff only.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Nothing needed after
    }
}
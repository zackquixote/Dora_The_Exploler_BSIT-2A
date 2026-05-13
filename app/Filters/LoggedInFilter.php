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
        // #region agent log
        @file_put_contents(
            ROOTPATH . 'debug-0646ff.log',
            json_encode([
                'sessionId'    => '0646ff',
                'runId'        => 'pre-fix',
                'hypothesisId' => 'H11',
                'location'     => 'app/Filters/LoggedInFilter.php:16',
                'message'      => 'loggedIn filter before invoked',
                'data'         => [
                    'path'       => $request->getUri()->getPath(),
                    'method'     => $request->getMethod(),
                    'logged_in'  => (bool) session()->get('logged_in'),
                    'hasUserId'  => !empty(session()->get('user_id')),
                ],
                'timestamp'    => (int) round(microtime(true) * 1000),
            ], JSON_UNESCAPED_SLASHES) . PHP_EOL,
            FILE_APPEND
        );
        // #endregion
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

        // #region agent log
        @file_put_contents(
            ROOTPATH . 'debug-0646ff.log',
            json_encode([
                'sessionId'    => '0646ff',
                'runId'        => 'pre-fix',
                'hypothesisId' => 'H12',
                'location'     => 'app/Filters/LoggedInFilter.php:44',
                'message'      => 'loggedIn filter after headers',
                'data'         => [
                    'path'           => $request->getUri()->getPath(),
                    'status'         => $response->getStatusCode(),
                    'cacheControl'   => $response->getHeaderLine('Cache-Control'),
                    'pragma'         => $response->getHeaderLine('Pragma'),
                    'expires'        => $response->getHeaderLine('Expires'),
                ],
                'timestamp'    => (int) round(microtime(true) * 1000),
            ], JSON_UNESCAPED_SLASHES) . PHP_EOL,
            FILE_APPEND
        );
        // #endregion
    }
}
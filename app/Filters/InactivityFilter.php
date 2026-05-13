<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class InactivityFilter implements FilterInterface
{
    use SendsJsonWhenUnauthorized;

    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        $timeout = 3600; // 1 hour = 3600 seconds
        // $timeout = 180; // 3 minutes (60 * 3)


        if ($session->get('logged_in')) {
            $lastActivity = $session->get('last_activity');

            // #region agent log
            @file_put_contents(
                ROOTPATH . 'debug-0646ff.log',
                json_encode([
                    'sessionId'    => '0646ff',
                    'runId'        => 'pre-fix',
                    'hypothesisId' => 'H26',
                    'location'     => 'app/Filters/InactivityFilter.php:24',
                    'message'      => 'inactivity check',
                    'data'         => [
                        'path'            => $request->getUri()->getPath(),
                        'timeout'         => $timeout,
                        'last_activity'   => $lastActivity,
                        'seconds_inactive'=> $lastActivity ? (time() - $lastActivity) : null,
                        'will_expire'     => ($lastActivity !== null && (time() - $lastActivity) > $timeout),
                    ],
                    'timestamp'    => (int) round(microtime(true) * 1000),
                ], JSON_UNESCAPED_SLASHES) . PHP_EOL,
                FILE_APPEND
            );
            // #endregion

            if ($lastActivity !== null && (time() - $lastActivity) > $timeout) {
                // #region agent log
                @file_put_contents(
                    ROOTPATH . 'debug-0646ff.log',
                    json_encode([
                        'sessionId'    => '0646ff',
                        'runId'        => 'pre-fix',
                        'hypothesisId' => 'H27',
                        'location'     => 'app/Filters/InactivityFilter.php:44',
                        'message'      => 'session destroyed by inactivity filter',
                        'data'         => [
                            'path' => $request->getUri()->getPath(),
                        ],
                        'timestamp'    => (int) round(microtime(true) * 1000),
                    ], JSON_UNESCAPED_SLASHES) . PHP_EOL,
                    FILE_APPEND
                );
                // #endregion
                $session->destroy();
                $json = $this->jsonUnauthorizedResponse($request, 'Session expired due to inactivity.');
                if ($json !== null) {
                    return $json;
                }

                return redirect()->to('/login')->with('error', 'Session expired due to inactivity.');
            }

            $session->set('last_activity', time());
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Not needed
    }
}

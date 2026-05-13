<?php

namespace App\Controllers;

class DebugController extends BaseController
{
    public function probe()
    {
        $raw = $this->request->getBody();
        $payload = json_decode($raw, true);
        if (!is_array($payload)) {
            $payload = [
                'raw' => mb_substr((string) $raw, 0, 500),
            ];
        }

        @file_put_contents(
            ROOTPATH . 'debug-0646ff.log',
            json_encode([
                'sessionId'    => '0646ff',
                'runId'        => 'pre-fix',
                'hypothesisId' => $payload['hypothesisId'] ?? 'H16',
                'location'     => $payload['location'] ?? 'app/Controllers/DebugController.php:18',
                'message'      => $payload['message'] ?? 'debug probe',
                'data'         => $payload['data'] ?? $payload,
                'timestamp'    => (int) round(microtime(true) * 1000),
            ], JSON_UNESCAPED_SLASHES) . PHP_EOL,
            FILE_APPEND
        );

        return $this->response->setJSON(['ok' => true]);
    }
}


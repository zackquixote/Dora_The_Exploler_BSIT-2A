<?php

namespace App\Services;

class GmailService
{
    public function isConfigured(): bool
    {
        return is_file($this->getCredentialsPath());
    }

    public function isAuthorized(): bool
    {
        return is_file($this->getTokenPath());
    }

    public function getAuthorizationUrl(): string
    {
        $cfg = $this->readCredentials();
        $redirectUri = $this->resolveRedirectUri($cfg);

        $query = http_build_query([
            'client_id'     => $cfg['client_id'],
            'redirect_uri'  => $redirectUri,
            'response_type' => 'code',
            'scope'         => 'https://www.googleapis.com/auth/gmail.send',
            'access_type'   => 'offline',
            'prompt'        => 'consent',
        ]);

        return 'https://accounts.google.com/o/oauth2/v2/auth?' . $query;
    }

    public function handleOAuthCallback(string $code): void
    {
        $cfg = $this->readCredentials();
        $redirectUri = $this->resolveRedirectUri($cfg);

        $http = \Config\Services::curlrequest();
        $res = $http->post('https://oauth2.googleapis.com/token', [
            'form_params' => [
                'code'          => $code,
                'client_id'     => $cfg['client_id'],
                'client_secret' => $cfg['client_secret'],
                'redirect_uri'  => $redirectUri,
                'grant_type'    => 'authorization_code',
            ],
            'headers' => [
                'Accept' => 'application/json',
            ],
        ]);

        $token = json_decode((string) $res->getBody(), true);
        if (! is_array($token) || isset($token['error'])) {
            throw new \RuntimeException((string) (($token['error_description'] ?? $token['error'] ?? 'OAuth token exchange failed')));
        }

        $token['expires_at'] = time() + ((int) ($token['expires_in'] ?? 0)) - 60;

        $this->ensureCredentialsDirectory();
        file_put_contents($this->getTokenPath(), json_encode($token));
    }

    public function send(string $to, string $subject, string $body, ?string $fromEmail = null, ?string $fromName = null): bool
    {
        $accessToken = $this->getValidAccessToken();
        $raw = $this->buildRawMessage($to, $subject, $body, $fromEmail, $fromName);

        $http = \Config\Services::curlrequest();
        $res = $http->post('https://gmail.googleapis.com/gmail/v1/users/me/messages/send', [
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken,
                'Accept'        => 'application/json',
                'Content-Type'  => 'application/json',
            ],
            'body' => json_encode(['raw' => $raw]),
        ]);

        if ($res->getStatusCode() < 200 || $res->getStatusCode() >= 300) {
            throw new \RuntimeException('Gmail send failed.');
        }

        return true;
    }

    private function getValidAccessToken(): string
    {
        $tokenPath = $this->getTokenPath();
        if (! is_file($tokenPath)) {
            throw new \RuntimeException('Gmail OAuth token missing. Connect Gmail first.');
        }

        $token = json_decode((string) file_get_contents($tokenPath), true);
        if (! is_array($token) || empty($token['access_token'])) {
            throw new \RuntimeException('Gmail OAuth token invalid. Connect Gmail first.');
        }

        $expiresAt = (int) ($token['expires_at'] ?? 0);
        if ($expiresAt > 0 && time() < $expiresAt) {
            return (string) $token['access_token'];
        }

        $refresh = (string) ($token['refresh_token'] ?? '');
        if ($refresh === '') {
            throw new \RuntimeException('Gmail OAuth refresh token missing. Reconnect Gmail.');
        }

        $cfg = $this->readCredentials();

        $http = \Config\Services::curlrequest();
        $res = $http->post('https://oauth2.googleapis.com/token', [
            'form_params' => [
                'client_id'     => $cfg['client_id'],
                'client_secret' => $cfg['client_secret'],
                'refresh_token' => $refresh,
                'grant_type'    => 'refresh_token',
            ],
            'headers' => [
                'Accept' => 'application/json',
            ],
        ]);

        $newToken = json_decode((string) $res->getBody(), true);
        if (! is_array($newToken) || isset($newToken['error']) || empty($newToken['access_token'])) {
            throw new \RuntimeException('Gmail OAuth refresh failed. Reconnect Gmail.');
        }

        $token['access_token'] = $newToken['access_token'];
        $token['expires_in'] = $newToken['expires_in'] ?? null;
        $token['expires_at'] = time() + ((int) ($newToken['expires_in'] ?? 0)) - 60;

        $this->ensureCredentialsDirectory();
        file_put_contents($tokenPath, json_encode($token));

        return (string) $token['access_token'];
    }

    private function buildRawMessage(string $to, string $subject, string $body, ?string $fromEmail, ?string $fromName): string
    {
        $fromEmail = $fromEmail ?: (string) env('GMAIL_FROM_EMAIL');
        $fromName = $fromName ?: (string) env('GMAIL_FROM_NAME');

        $headers = [];
        if ($fromEmail !== '') {
            $headers[] = $fromName !== '' ? ('From: ' . $fromName . ' <' . $fromEmail . '>') : ('From: ' . $fromEmail);
        }
        $headers[] = 'To: ' . $to;
        $headers[] = 'Subject: ' . $this->encodeHeader($subject);
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'Content-Type: text/plain; charset=UTF-8';
        $headers[] = 'Content-Transfer-Encoding: base64';

        $payload = implode("\r\n", $headers) . "\r\n\r\n" . base64_encode($body);

        return rtrim(strtr(base64_encode($payload), '+/', '-_'), '=');
    }

    private function encodeHeader(string $value): string
    {
        return '=?UTF-8?B?' . base64_encode($value) . '?=';
    }

    private function readCredentials(): array
    {
        $path = $this->getCredentialsPath();
        if (! is_file($path)) {
            throw new \RuntimeException('Gmail OAuth credentials file not found.');
        }

        $json = json_decode((string) file_get_contents($path), true);
        if (! is_array($json)) {
            throw new \RuntimeException('Gmail OAuth credentials JSON is invalid.');
        }

        $root = $json['web'] ?? $json['installed'] ?? null;
        if (! is_array($root)) {
            throw new \RuntimeException('Gmail OAuth credentials JSON missing web/installed section.');
        }

        $clientId = (string) ($root['client_id'] ?? '');
        $clientSecret = (string) ($root['client_secret'] ?? '');
        $redirectUris = $root['redirect_uris'] ?? [];

        if ($clientId === '' || $clientSecret === '') {
            throw new \RuntimeException('Gmail OAuth credentials JSON missing client_id/client_secret.');
        }

        return [
            'client_id'     => $clientId,
            'client_secret' => $clientSecret,
            'redirect_uris' => is_array($redirectUris) ? $redirectUris : [],
        ];
    }

    private function resolveRedirectUri(array $cfg): string
    {
        $envUri = (string) env('GMAIL_OAUTH_REDIRECT_URI');
        if ($envUri !== '') {
            return $envUri;
        }

        $uris = $cfg['redirect_uris'] ?? [];
        if (is_array($uris) && isset($uris[0]) && is_string($uris[0]) && $uris[0] !== '') {
            return $uris[0];
        }

        helper('url');
        return site_url('advanced/gmail/callback');
    }

    private function getCredentialsPath(): string
    {
        $path = (string) env('GMAIL_OAUTH_CREDENTIALS_PATH');
        if ($path !== '') {
            return $this->normalizePath($path);
        }

        return rtrim(WRITEPATH, "\\/") . DIRECTORY_SEPARATOR . 'credentials' . DIRECTORY_SEPARATOR . 'gmail.json';
    }

    private function getTokenPath(): string
    {
        $path = (string) env('GMAIL_OAUTH_TOKEN_PATH');
        if ($path !== '') {
            return $this->normalizePath($path);
        }

        return rtrim(WRITEPATH, "\\/") . DIRECTORY_SEPARATOR . 'credentials' . DIRECTORY_SEPARATOR . 'gmail_token.json';
    }

    private function normalizePath(string $path): string
    {
        $p = trim($path);
        if ($p === '') {
            return $p;
        }

        $isAbsolute = (bool) preg_match('/^(?:[A-Za-z]:\\\\|\\\\\\\\|\\/)/', $p);
        if ($isAbsolute) {
            return $p;
        }

        return rtrim(ROOTPATH, "\\/") . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $p);
    }

    private function ensureCredentialsDirectory(): void
    {
        $dir = dirname($this->getTokenPath());
        if (is_dir($dir)) {
            return;
        }
        mkdir($dir, 0755, true);
    }
}

<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\LogModel;
use App\Services\AuditService;

/**
 * Auth Controller
 * 
 * Handles user authentication: login, logout, and session management.
 * 
 * METHODS:
 * - index(): Displays login form or redirects if already logged in.
 * - auth(): Processes login credentials, sets session, and logs the action.
 * - logout(): Destroys session, clears cookies, and logs the logout.
 * 
 * 
 * DEPENDENCIES:
 * - UserModel for database authentication
 * - LogModel for recording login/logout activities
 * 
 * @package App\Controllers
 */
class Auth extends BaseController
{
    protected $logModel;
    protected AuditService $auditService;

    public function __construct()
    {
        $this->logModel = new LogModel();
        $this->auditService = new AuditService();
    }

  /**
   * Execute index functionality.
   *
   * @return mixed
   */
  public function index()
{
    // #region agent log
    @file_put_contents(
        ROOTPATH . 'debug-0646ff.log',
        json_encode([
            'sessionId'    => '0646ff',
            'runId'        => 'pre-fix',
            'hypothesisId' => 'H22',
            'location'     => 'app/Controllers/Auth.php:42',
            'message'      => 'auth index session gate',
            'data'         => [
                'logged_in' => (bool) session()->get('logged_in'),
                'user_id'   => session()->get('user_id') ?: null,
                'role'      => session()->get('role') ?: null,
            ],
            'timestamp'    => (int) round(microtime(true) * 1000),
        ], JSON_UNESCAPED_SLASHES) . PHP_EOL,
        FILE_APPEND
    );
    // #endregion
    // Already logged in – redirect to role dashboard
    if (session()->get('logged_in')) {
        $redirectRole = strtolower(session()->get('role'));
        if ($redirectRole === 'resident') {
            return redirect()->to(base_url('portal/home'));
        }
        return redirect()->to(base_url($redirectRole . '/dashboard'));
    }

    // Fetch barangay settings (only one row, id = 1)
    $settingsModel = new \App\Models\BarangaySettingsModel();
    $settings = $settingsModel->first();

    // Fallback if no record exists
    if (!$settings) {
        $settings = [
            'barangay_name' => 'Barangay',
            'municipality'  => 'Municipality',
            'province'      => 'Province',
        ];
    }

    return view('login', [
        'lockout'  => 0,
        'settings' => $settings,    // <-- pass to view
    ]);
}
    public function auth()
    {
        $model = new UserModel();
        
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        $remember = $this->request->getPost('remember');

        $user = $model->where('email', $email)->first();

        // #region agent log
        @file_put_contents(
            ROOTPATH . 'debug-0646ff.log',
            json_encode([
                'sessionId'    => '0646ff',
                'runId'        => 'pre-fix',
                'hypothesisId' => 'H23',
                'location'     => 'app/Controllers/Auth.php:84',
                'message'      => 'auth credential evaluation',
                'data'         => [
                    'email'           => (string) $email,
                    'user_found'      => (bool) $user,
                    'has_password_row'=> is_array($user) && array_key_exists('password', $user),
                    'verify_result'   => (bool) ($user && password_verify((string) $password, (string) ($user['password'] ?? ''))),
                    'already_logged_in'=> (bool) session()->get('logged_in'),
                ],
                'timestamp'    => (int) round(microtime(true) * 1000),
            ], JSON_UNESCAPED_SLASHES) . PHP_EOL,
            FILE_APPEND
        );
        // #endregion

        if ($user && password_verify($password, $user['password'])) {
            $normalizedRole = strtolower((string) ($user['role'] ?? 'staff'));

            // New session ID after authentication (mitigates session fixation)
            session()->regenerate(true);

            session()->set([
                'logged_in' => true,
                'user_id'   => $user['id'],
                'role'      => $normalizedRole,
                'email'     => $user['email'],
                'name'      => $user['name'],
                'last_activity' => time(),
            ]);

            // ── LOG THE LOGIN HERE ────────────────────────────────
            $this->logModel->addLog("User Logged In");
            $this->auditService->log('login', 'user', (int) ($user['id'] ?? 0));
            // ─────────────────────────────────────────────────────────────

            if ($remember) {
                // Store only the email in a secure, httpOnly cookie (no user_id exposed)
                $this->response->setCookie(
                    'remember_email',
                    $email,
                    60 * 60 * 24 * 30, // 30 days
                    '',                // domain
                    '/',               // path
                    false,             // secure (set true in production with HTTPS)
                    true               // httpOnly — not accessible via JavaScript
                );
            }

            // Force lowercase redirection
            $redirectRole = $normalizedRole;
            // #region agent log
            @file_put_contents(
                ROOTPATH . 'debug-0646ff.log',
                json_encode([
                    'sessionId'    => '0646ff',
                    'runId'        => 'pre-fix',
                    'hypothesisId' => 'H24',
                    'location'     => 'app/Controllers/Auth.php:132',
                    'message'      => 'auth success redirect',
                    'data'         => [
                        'redirect'  => base_url($redirectRole . '/dashboard'),
                        'user_id'   => $user['id'] ?? null,
                        'role'      => $normalizedRole,
                    ],
                    'timestamp'    => (int) round(microtime(true) * 1000),
                ], JSON_UNESCAPED_SLASHES) . PHP_EOL,
                FILE_APPEND
            );
            // #endregion
            return redirect()->to(base_url($redirectRole . '/dashboard'));
        }

        // #region agent log
        @file_put_contents(
            ROOTPATH . 'debug-0646ff.log',
            json_encode([
                'sessionId'    => '0646ff',
                'runId'        => 'pre-fix',
                'hypothesisId' => 'H25',
                'location'     => 'app/Controllers/Auth.php:150',
                'message'      => 'auth failure redirect',
                'data'         => [
                    'redirect' => 'back',
                    'error'    => 'Invalid email or password',
                ],
                'timestamp'    => (int) round(microtime(true) * 1000),
            ], JSON_UNESCAPED_SLASHES) . PHP_EOL,
            FILE_APPEND
        );
        // #endregion
        return redirect()->back()->with('error', 'Invalid email or password');
    }

    /**
     * Execute logout functionality.
     *
     * @return mixed
     */
    public function logout()
    {
        // #region agent log
        @file_put_contents(
            ROOTPATH . 'debug-0646ff.log',
            json_encode([
                'sessionId'    => '0646ff',
                'runId'        => 'pre-fix',
                'hypothesisId' => 'H13',
                'location'     => 'app/Controllers/Auth.php:123',
                'message'      => 'logout start session state',
                'data'         => [
                    'logged_in_before' => (bool) session()->get('logged_in'),
                    'user_id_before'   => session()->get('user_id') ?: null,
                    'role_before'      => session()->get('role') ?: null,
                ],
                'timestamp'    => (int) round(microtime(true) * 1000),
            ], JSON_UNESCAPED_SLASHES) . PHP_EOL,
            FILE_APPEND
        );
        // #endregion
        // ── LOG THE LOGOUT HERE (Before destroying session) ──
        $this->logModel->addLog("User Logged Out");
        $this->auditService->log('logout', 'user', (int) (session()->get('user_id') ?? 0));
        // ─────────────────────────────────────────────────────────────

        session()->destroy();
        // #region agent log
        @file_put_contents(
            ROOTPATH . 'debug-0646ff.log',
            json_encode([
                'sessionId'    => '0646ff',
                'runId'        => 'pre-fix',
                'hypothesisId' => 'H14',
                'location'     => 'app/Controllers/Auth.php:147',
                'message'      => 'logout complete redirecting',
                'data'         => [
                    'redirect'        => '/login',
                    'logged_in_after' => (bool) session()->get('logged_in'),
                ],
                'timestamp'    => (int) round(microtime(true) * 1000),
            ], JSON_UNESCAPED_SLASHES) . PHP_EOL,
            FILE_APPEND
        );
        // #endregion
        
        $this->response->deleteCookie('remember_email');

        return redirect()->to('/login')
            ->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->setHeader('Pragma', 'no-cache')
            ->setHeader('Expires', '0');
    }
}

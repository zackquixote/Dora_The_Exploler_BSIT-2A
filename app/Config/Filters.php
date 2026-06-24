<?php

namespace Config;

use CodeIgniter\Config\Filters as BaseFilters;
use CodeIgniter\Filters\Cors;
use CodeIgniter\Filters\CSRF;
use CodeIgniter\Filters\DebugToolbar;
use CodeIgniter\Filters\ForceHTTPS;
use CodeIgniter\Filters\Honeypot;
use CodeIgniter\Filters\InvalidChars;
use CodeIgniter\Filters\PageCache;
use CodeIgniter\Filters\PerformanceMetrics;
use CodeIgniter\Filters\SecureHeaders;

class Filters extends BaseFilters
{
    /**
     * @var array<string, class-string|list<class-string>>
     */
    public array $aliases = [
        'csrf'            => CSRF::class,
        'toolbar'         => DebugToolbar::class,
        'honeypot'        => Honeypot::class,
        'invalidchars'    => InvalidChars::class,
        'secureheaders'   => SecureHeaders::class,
        'role'            => \App\Filters\RoleFilter::class,
        'throttle'        => \App\Filters\ThrottleFilter::class,
        'cors'            => Cors::class,
        'forcehttps'      => ForceHTTPS::class,
        'pagecache'       => PageCache::class,
        'performance'     => PerformanceMetrics::class,
        'inactivity'      => \App\Filters\InactivityFilter::class,
        'rolefilter'      => \App\Filters\RoleFilter::class,
        'csp'             => \App\Filters\CspFilter::class,
        'securityHeaders' => \App\Filters\SecurityHeaders::class,

        // Role-based route guards
        'adminOnly'       => \App\Filters\AdminFilter::class,
        'staffOnly'       => \App\Filters\StaffFilter::class,
        'loggedIn'        => \App\Filters\LoggedInFilter::class,
        'portalAuth'      => \App\Filters\PortalAuthFilter::class,

        // Permission-based route guard (Phase 1A groundwork)
        'perm'            => \App\Filters\PermissionFilter::class,
    ];

    /**
     * @var array{before: list<string>, after: list<string>}
     */
    public array $required = [
        'before' => [
            'forcehttps',
        ],
        'after' => [
            'performance',
            'toolbar',
        ],
    ];

    /**
     * @var array<string, array<string, array<string, string>>>|array<string, list<string>>
     */
    public array $globals = [
        'before' => [
            'csrf' => ['except' => ['login', 'login/*', 'auth', 'auth/*', 'debug/probe']],
            // Inactivity filter only for authenticated routes to reduce overhead
            'inactivity' => ['except' => ['login', 'login/*', 'auth/*', '/', 'portal', 'portal/login', 'portal/register']],
            // Honeypot only on forms, not all requests
            // 'honeypot',
            'invalidchars',
        ],
        'after' => [
            // Reduced global filters for better performance
            'secureheaders' => ['except' => ['debug/*', 'system-monitor/*']],
            'securityHeaders' => ['except' => ['debug/*', 'system-monitor/*']],
        ],
    ];

    /**
     * @var array<string, list<string>>
     */
    public array $methods = [];

    /**
     * @var array<string, array<string, list<string>>>
     */
    public array $filters = [];
}

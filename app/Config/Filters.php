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
        'csrf'          => CSRF::class,
        'toolbar'       => DebugToolbar::class,
        'honeypot'      => Honeypot::class,
        'invalidchars'  => InvalidChars::class,
        'secureheaders' => SecureHeaders::class,
        'role'          => \App\Filters\RoleFilter::class,
        'cors'          => Cors::class,
        'forcehttps'    => ForceHTTPS::class,
        'pagecache'     => PageCache::class,
        'performance'   => PerformanceMetrics::class,
        'inactivity'    => \App\Filters\InactivityFilter::class,
        'rolefilter'    => \App\Filters\RoleFilter::class,
        'csp'           => \App\Filters\CspFilter::class,

        // Role-based route guards
        'adminOnly'     => \App\Filters\AdminFilter::class,
        'staffOnly'     => \App\Filters\StaffFilter::class,
        'loggedIn'      => \App\Filters\LoggedInFilter::class,
    ];

    /**
     * @var array{before: list<string>, after: list<string>}
     */
    public array $required = [
        'before' => [
            'forcehttps',
            'pagecache',
        ],
        'after' => [
            'pagecache',
            'performance',
            'toolbar',
        ],
    ];

    /**
     * @var array<string, array<string, array<string, string>>>|array<string, list<string>>
     */
    public array $globals = [
        'before' => [
            'inactivity' => ['except' => ['login', 'login/*', 'auth/*']],
            'honeypot',
            'invalidchars',
        ],
        'after' => [
            'csp',
            'honeypot',
            'secureheaders',
        ],
    ];

    /**
     * @var array<string, list<string>>
     */
    public array $methods = [];

    /**
     * @var array<string, array<string, list<string>>>
     */
    public array $filters = [
        'csrf' => [
            'except' => [
                'staff/resident/list',
                'staff/resident/store',
                'staff/resident/update/*',
                'staff/resident/delete/*',
                'households/list',
                'households/store',
                'households/update/*',
                'households/delete/*',
            ],
        ],
    ];
}
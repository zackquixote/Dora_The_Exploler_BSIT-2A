<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Performance Configuration
 * Optimizations for better system performance
 */
class Performance extends BaseConfig
{
    /**
     * Enable output compression
     */
    public bool $enableCompression = true;

    /**
     * Cache configuration
     */
    public array $cache = [
        'enabled' => true,
        'ttl' => 3600, // 1 hour default TTL
        'driver' => 'file', // file, redis, memcached
        'prefix' => 'brgy_',
    ];

    /**
     * Database query optimization
     */
    public array $database = [
        'query_cache' => true,
        'persistent_connections' => true,
        'connection_pooling' => true,
        'slow_query_log' => true,
        'slow_query_threshold' => 2.0, // seconds
    ];

    /**
     * Memory optimization
     */
    public array $memory = [
        'limit' => '256M',
        'gc_enabled' => true,
        'gc_probability' => 1,
        'gc_divisor' => 100,
    ];

    /**
     * Session optimization
     */
    public array $session = [
        'gc_maxlifetime' => 1440, // 24 minutes
        'cookie_lifetime' => 0,
        'regenerate_id' => true,
    ];

    /**
     * Asset optimization
     */
    public array $assets = [
        'minify_css' => true,
        'minify_js' => true,
        'combine_files' => true,
        'use_cdn' => false,
    ];

    /**
     * Notification system optimization
     */
    public array $notifications = [
        'batch_size' => 100, // Process notifications in batches
        'queue_enabled' => true,
        'retry_attempts' => 3,
        'timeout' => 30, // seconds
    ];

    /**
     * Logging optimization
     */
    public array $logging = [
        'level' => 'error', // Only log errors in production
        'rotate_logs' => true,
        'max_log_size' => '10MB',
        'max_log_files' => 5,
    ];
}
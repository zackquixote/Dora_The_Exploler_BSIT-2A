<?php

/**
* Description:  This Codeigniter Framework is created to build smart web applications
* Author:       Glenn Azuelo
* Date Created: April 14, 2025
* Revised By:       
*/

namespace Config;

use CodeIgniter\Database\Config;

/**
 * Database Configuration
 */
class Database extends Config
{
    /**
     * Resolve env value reliably across Apache/CLI contexts.
     */
    private function resolveEnv(string $key, $default = null)
    {
        $value = env($key);
        if ($value === null || $value === '') {
            $value = getenv($key);
        }
        if ($value === false || $value === null || $value === '') {
            $value = $_ENV[$key] ?? $_SERVER[$key] ?? $default;
        }
        return $value;
    }

    /**
     * The directory that holds the Migrations and Seeds directories.
     */
    public string $filesPath = APPPATH . 'Database' . DIRECTORY_SEPARATOR;

    /**
     * Lets you choose which connection group to use if no other is specified.
     */
    public string $defaultGroup = 'default';

    /**
     * The default database connection.
     *
     * @var array<string, mixed>
     */

    public function __construct()
    {
        parent::__construct();

        $resolvedHostname = (string) $this->resolveEnv('database.default.hostname', 'localhost');
        $resolvedUsername = (string) $this->resolveEnv('database.default.username', 'root');
        $resolvedPassword = (string) $this->resolveEnv('database.default.password', '');
        $resolvedDatabase = (string) $this->resolveEnv('database.default.database', '');
        $resolvedDriver   = (string) $this->resolveEnv('database.default.DBDriver', 'MySQLi');
        $resolvedPort     = (int) $this->resolveEnv('database.default.port', 3306);

        // #region agent log
        @file_put_contents(
            ROOTPATH . 'debug-0646ff.log',
            json_encode([
                'sessionId'    => '0646ff',
                'runId'        => 'pre-fix',
                'hypothesisId' => 'H20',
                'location'     => 'app/Config/Database.php:63',
                'message'      => 'database env resolution',
                'data'         => [
                    'sapi'             => PHP_SAPI,
                    'env_hostname'     => $resolvedHostname,
                    'env_username'     => $resolvedUsername,
                    'env_database'     => $resolvedDatabase,
                    'env_driver'       => $resolvedDriver,
                    'env_port'         => $resolvedPort,
                    'database_is_empty'=> ($resolvedDatabase === ''),
                ],
                'timestamp'    => (int) round(microtime(true) * 1000),
            ], JSON_UNESCAPED_SLASHES) . PHP_EOL,
            FILE_APPEND
        );
        // #endregion

        // Initialize the default configuration with dynamic values.
        $this->default = [
            'DSN'      => '',
            'hostname' => $resolvedHostname,
            'username' => $resolvedUsername,
            'password' => $resolvedPassword,
            'database' => $resolvedDatabase,
            'DBDriver' => $resolvedDriver,
            'DBPrefix' => '',
            'pConnect' => false,
            'DBDebug'  => (ENVIRONMENT !== 'production'),
            'cacheOn'  => false,
            'cacheDir' => '',
            'charset'  => 'utf8mb4',
            'DBCollat' => 'utf8mb4_general_ci',
            'swapPre'  => '',
            'encrypt'  => false,
            'compress' => false,
            'strictOn' => false,
            'failover' => [],
            'port'     => $resolvedPort,
        ];

        // If running in a testing environment, use the 'tests' connection group
        if (ENVIRONMENT === 'testing') {
            $this->defaultGroup = 'tests';
        }
    }


    // public array $default = [
    //     'DSN'          => '',
    //     'hostname'     => 'localhost',
    //     'username'     => 'phpmyadmin',
    //     'password'     => '1234',
    //     'database'     => 'newdb_brgy',
    //     'DBDriver'     => 'MySQLi',
    //     'DBPrefix'     => '',
    //     'pConnect'     => false,
    //     'DBDebug'      => true,
    //     'charset'      => 'utf8mb4',
    //     'DBCollat'     => 'utf8mb4_general_ci',
    //     'swapPre'      => '',
    //     'encrypt'      => false,
    //     'compress'     => false,
    //     'strictOn'     => false,
    //     'failover'     => [],
    //     'port'         => 3306,
    //     'numberNative' => false,
    //     'foundRows'    => false,
    //     'dateFormat'   => [
    //         'date'     => 'Y-m-d',
    //         'datetime' => 'Y-m-d H:i:s',
    //         'time'     => 'H:i:s',
    //     ],
    // ];

    //    /**
    //     * Sample database connection for SQLite3.
    //     *
    //     * @var array<string, mixed>
    //     */
    //    public array $default = [
    //        'database'    => 'database.db',
    //        'DBDriver'    => 'SQLite3',
    //        'DBPrefix'    => '',
    //        'DBDebug'     => true,
    //        'swapPre'     => '',
    //        'failover'    => [],
    //        'foreignKeys' => true,
    //        'busyTimeout' => 1000,
    //        'synchronous' => null,
    //        'dateFormat'  => [
    //            'date'     => 'Y-m-d',
    //            'datetime' => 'Y-m-d H:i:s',
    //            'time'     => 'H:i:s',
    //        ],
    //    ];

    //    /**
    //     * Sample database connection for Postgre.
    //     *
    //     * @var array<string, mixed>
    //     */
    //    public array $default = [
    //        'DSN'        => '',
    //        'hostname'   => 'localhost',
    //        'username'   => 'root',
    //        'password'   => 'root',
    //        'database'   => 'ci4',
    //        'schema'     => 'public',
    //        'DBDriver'   => 'Postgre',
    //        'DBPrefix'   => '',
    //        'pConnect'   => false,
    //        'DBDebug'    => true,
    //        'charset'    => 'utf8',
    //        'swapPre'    => '',
    //        'failover'   => [],
    //        'port'       => 5432,
    //        'dateFormat' => [
    //            'date'     => 'Y-m-d',
    //            'datetime' => 'Y-m-d H:i:s',
    //            'time'     => 'H:i:s',
    //        ],
    //    ];

    //    /**
    //     * Sample database connection for SQLSRV.
    //     *
    //     * @var array<string, mixed>
    //     */
    //    public array $default = [
    //        'DSN'        => '',
    //        'hostname'   => 'localhost',
    //        'username'   => 'root',
    //        'password'   => 'root',
    //        'database'   => 'ci4',
    //        'schema'     => 'dbo',
    //        'DBDriver'   => 'SQLSRV',
    //        'DBPrefix'   => '',
    //        'pConnect'   => false,
    //        'DBDebug'    => true,
    //        'charset'    => 'utf8',
    //        'swapPre'    => '',
    //        'encrypt'    => false,
    //        'failover'   => [],
    //        'port'       => 1433,
    //        'dateFormat' => [
    //            'date'     => 'Y-m-d',
    //            'datetime' => 'Y-m-d H:i:s',
    //            'time'     => 'H:i:s',
    //        ],
    //    ];

    //    /**
    //     * Sample database connection for OCI8.
    //     *
    //     * You may need the following environment variables:
    //     *   NLS_LANG                = 'AMERICAN_AMERICA.UTF8'
    //     *   NLS_DATE_FORMAT         = 'YYYY-MM-DD HH24:MI:SS'
    //     *   NLS_TIMESTAMP_FORMAT    = 'YYYY-MM-DD HH24:MI:SS'
    //     *   NLS_TIMESTAMP_TZ_FORMAT = 'YYYY-MM-DD HH24:MI:SS'
    //     *
    //     * @var array<string, mixed>
    //     */
    //    public array $default = [
    //        'DSN'        => 'localhost:1521/XEPDB1',
    //        'username'   => 'root',
    //        'password'   => 'root',
    //        'DBDriver'   => 'OCI8',
    //        'DBPrefix'   => '',
    //        'pConnect'   => false,
    //        'DBDebug'    => true,
    //        'charset'    => 'AL32UTF8',
    //        'swapPre'    => '',
    //        'failover'   => [],
    //        'dateFormat' => [
    //            'date'     => 'Y-m-d',
    //            'datetime' => 'Y-m-d H:i:s',
    //            'time'     => 'H:i:s',
    //        ],
    //    ];

    /**
     * This database connection is used when running PHPUnit database tests.
     *
     * @var array<string, mixed>
     */
    public array $tests = [
        'DSN'         => '',
        'hostname'    => '127.0.0.1',
        'username'    => '',
        'password'    => '',
        'database'    => ':memory:',
        'DBDriver'    => 'SQLite3',
        'DBPrefix'    => 'db_',  // Needed to ensure we're working correctly with prefixes live. DO NOT REMOVE FOR CI DEVS
        'pConnect'    => false,
        'DBDebug'     => true,
        'charset'     => 'utf8',
        'DBCollat'    => '',
        'swapPre'     => '',
        'encrypt'     => false,
        'compress'    => false,
        'strictOn'    => false,
        'failover'    => [],
        'port'        => 3306,
        'foreignKeys' => true,
        'busyTimeout' => 1000,
        'dateFormat'  => [
            'date'     => 'Y-m-d',
            'datetime' => 'Y-m-d H:i:s',
            'time'     => 'H:i:s',
        ],
    ];

    // public function __construct()
    // {
    //     parent::__construct();

    //     // Ensure that we always set the database group to 'tests' if
    //     // we are currently running an automated test suite, so that
    //     // we don't overwrite live data on accident.
    //     if (ENVIRONMENT === 'testing') {
    //         $this->defaultGroup = 'tests';
    //     }
    // }
}
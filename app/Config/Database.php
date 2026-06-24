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



        // Initialize the default configuration with dynamic values.
        $this->default = [
            'DSN'      => '',
            'hostname' => $resolvedHostname,
            'username' => $resolvedUsername,
            'password' => $resolvedPassword,
            'database' => $resolvedDatabase,
            'DBDriver' => $resolvedDriver,
            'DBPrefix' => '',
            'pConnect' => true, // Enable persistent connections for better performance
            'DBDebug'  => (ENVIRONMENT !== 'production'),
            'cacheOn'  => true, // Enable query caching
            'cacheDir' => WRITEPATH . 'cache/db/',
            'charset'  => 'utf8mb4',
            'DBCollat' => 'utf8mb4_general_ci',
            'swapPre'  => '',
            'encrypt'  => false,
            'compress' => false,
            'strictOn' => false,
            'failover' => [],
            'port'     => $resolvedPort,
            // Performance optimizations
            'numberNative' => false,
            'foundRows'    => false,
            'dateFormat'   => [
                'date'     => 'Y-m-d',
                'datetime' => 'Y-m-d H:i:s',
                'time'     => 'H:i:s',
            ],
        ];

        // If running in a testing environment, use the 'tests' connection group
        if (ENVIRONMENT === 'testing') {
            $this->defaultGroup = 'tests';
        }
    }


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
}
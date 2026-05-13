<?php

/*
 *---------------------------------------------------------------
 * CHECK PHP VERSION
 *---------------------------------------------------------------
 */

$minPhpVersion = '8.1'; // If you update this, don't forget to update `spark`.
if (version_compare(PHP_VERSION, $minPhpVersion, '<')) {
    $message = sprintf(
        'Your PHP version must be %s or higher to run CodeIgniter. Current version: %s',
        $minPhpVersion,
        PHP_VERSION,
    );

    header('HTTP/1.1 503 Service Unavailable.', true, 503);
    echo $message;

    exit(1);
}

/*
 *---------------------------------------------------------------
 * SET THE CURRENT DIRECTORY
 *---------------------------------------------------------------
 */

// Path to the front controller (this file)
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);

// Ensure the current directory is pointing to the front controller's directory
if (getcwd() . DIRECTORY_SEPARATOR !== FCPATH) {
    chdir(FCPATH);
}

/*
 *---------------------------------------------------------------
 * BOOTSTRAP THE APPLICATION
 *---------------------------------------------------------------
 * This process sets up the path constants, loads and registers
 * our autoloader, along with Composer's, loads our constants
 * and fires up an environment-specific bootstrapping.
 */

// LOAD OUR PATHS CONFIG FILE
// This is the line that might need to be changed, depending on your folder structure.
require FCPATH . '../app/Config/Paths.php';
// ^^^ Change this line if you move your application folder

$paths = new Config\Paths();

// LOAD THE FRAMEWORK BOOTSTRAP FILE
require $paths->systemDirectory . '/Boot.php';

// #region agent log
$__dbgIncomingEnv = $_ENV['CI_ENVIRONMENT'] ?? $_SERVER['CI_ENVIRONMENT'] ?? getenv('CI_ENVIRONMENT') ?: null;
// #endregion
if (true) {
    $_ENV['CI_ENVIRONMENT'] = 'development';
    $_SERVER['CI_ENVIRONMENT'] = 'development';
    putenv('CI_ENVIRONMENT=development');
}

// #region agent log
$__dbgEnv = $_ENV['CI_ENVIRONMENT'] ?? $_SERVER['CI_ENVIRONMENT'] ?? getenv('CI_ENVIRONMENT') ?: 'production';
$__dbgBootPath = $paths->appDirectory . '/Config/Boot/' . $__dbgEnv . '.php';
@file_put_contents(
    dirname(__DIR__) . '/debug-0646ff.log',
    json_encode([
        'sessionId'    => '0646ff',
        'runId'        => 'pre-fix',
        'hypothesisId' => 'H6',
        'location'     => 'public/index.php:57',
        'message'      => 'web bootstrap environment resolved',
        'data'         => [
            'sapi'          => PHP_SAPI,
            'incomingEnv'   => $__dbgIncomingEnv,
            'env'           => $__dbgEnv,
            'bootFile'      => $__dbgBootPath,
            'bootFileExists'=> is_file($__dbgBootPath),
        ],
        'timestamp'    => (int) round(microtime(true) * 1000),
    ], JSON_UNESCAPED_SLASHES) . PHP_EOL,
    FILE_APPEND
);
// #endregion

exit(CodeIgniter\Boot::bootWeb($paths));

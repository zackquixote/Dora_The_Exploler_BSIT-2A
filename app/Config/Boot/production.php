<?php

/*
 |--------------------------------------------------------------------------
 | ERROR REPORTING
 |--------------------------------------------------------------------------
 | Different environments will require different levels of error reporting.
 | By default development will show errors but testing and live will hide them.
 */

ini_set('display_errors', '0');
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_USER_NOTICE & ~E_USER_DEPRECATED);

/*
 |--------------------------------------------------------------------------
 | DEBUG MODE
 |--------------------------------------------------------------------------
 | Debug mode is an experimental flag that can allow for additional
 | debugging features. It is not used in the core framework yet.
 */
defined('CI_DEBUG') || define('CI_DEBUG', false);
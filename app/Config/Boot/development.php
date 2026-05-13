<?php

/*
 |--------------------------------------------------------------------------
 | ERROR REPORTING
 |--------------------------------------------------------------------------
 | Different environments will require different levels of error reporting.
 | By default development will show errors but testing and live will hide them.
 */

error_reporting(E_ALL);
ini_set('display_errors', '1');

/*
 |--------------------------------------------------------------------------
 | DEBUG MODE
 |--------------------------------------------------------------------------
 | Debug mode is an experimental flag that can allow for additional
 | debugging features. It is not used in the core framework yet.
 */
defined('CI_DEBUG') || define('CI_DEBUG', true);
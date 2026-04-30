<?php

use CodeIgniter\Router\RouteCollection;

/**
 * --------------------------------------------------------------------
 * ROUTE CONFIGURATION
 * --------------------------------------------------------------------
 */

$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Auth');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(false);

// Placeholders
$routes->addPlaceholder('num', '[0-9]+');
$routes->addPlaceholder('segment', '[^/]+');


// ------------------------------------------------
// AUTH (PUBLIC)
// ------------------------------------------------
$routes->get('/', 'Auth::index');
$routes->match(['get', 'post'], 'login', 'Auth::index');
$routes->post('auth', 'Auth::auth');
$routes->get('logout', 'Auth::logout');


// ------------------------------------------------
// LOGGED IN AREA (MAIN SYSTEM)
// ------------------------------------------------
$routes->group('', [
    'namespace' => 'App\Controllers',
    'filter'    => 'loggedIn',
], function ($routes) {

    // =========================
    // RESIDENTS
    // =========================
    $routes->get('resident', 'Resident::index');
    $routes->get('residents', 'Resident::index');
    $routes->match(['get', 'post'], 'resident/create', 'Resident::create');
    $routes->post('resident/store', 'Resident::store');
    $routes->match(['get', 'post'], 'resident/edit/(:num)', 'Resident::edit/$1');
    $routes->post('resident/update/(:num)', 'Resident::update/$1');
    $routes->get('resident/view/(:num)', 'Resident::view/$1');
    $routes->post('resident/delete/(:num)', 'Resident::delete/$1');

    $routes->get('resident/getHouseholdsBySitio', 'Resident::getHouseholdsBySitio');
    $routes->get('resident/assign-search', 'Resident::assignSearch');
    $routes->get('resident/assign/(:num)', 'Resident::assign/$1');


    // =========================
    // HOUSEHOLDS
    // =========================
    $routes->group('households', function ($routes) {
        $routes->get('/', 'HouseholdController::index');
        $routes->match(['get', 'post'], 'create', 'HouseholdController::create');
        $routes->post('store', 'HouseholdController::store');
        $routes->match(['get', 'post'], 'edit/(:num)', 'HouseholdController::edit/$1');
        $routes->post('update/(:num)', 'HouseholdController::update/$1');
        $routes->get('view/(:num)', 'HouseholdController::view/$1');
        $routes->post('delete/(:num)', 'HouseholdController::delete/$1');

        $routes->post('getResidentsBySitio', 'HouseholdController::getResidentsBySitio');
        $routes->post('get-by-sitio', 'HouseholdController::getBySitio');
        $routes->get('getNextHouseholdNo', 'HouseholdController::getNextHouseholdNo');
        $routes->get('checkHouseholdNo', 'HouseholdController::checkHouseholdNo');
    });


    // =========================
    // CERTIFICATES
    // =========================
    $routes->get('certificate', 'Certificate::index');
    $routes->get('certificate/create', 'Certificate::create');
    $routes->post('certificate/store', 'Certificate::store');
    $routes->get('certificate/print/(:num)', 'Certificate::print_view/$1');
    $routes->get('certificate/edit/(:num)', 'Certificate::edit/$1');
    $routes->post('certificate/update/(:num)', 'Certificate::update/$1');
    $routes->delete('certificate/delete/(:num)', 'Certificate::delete/$1');


    // =========================
    // OFFICIALS (FULL CRUD FIXED)
    // =========================
    $routes->get('officials', 'Officials::index');
    $routes->get('officials/create', 'Officials::create');
    $routes->post('officials/store', 'Officials::store');
    $routes->get('officials/edit/(:num)', 'Officials::edit/$1');
    $routes->post('officials/update/(:num)', 'Officials::update/$1');
    $routes->get('officials/delete/(:num)', 'Officials::delete/$1');
     $routes->get('officials', 'Officials::index');


    // =========================
    // BLOTTER
    // =========================
    $routes->get('blotter', 'Blotter::index');
    $routes->get('blotter/create', 'Blotter::create');
    $routes->post('blotter/store', 'Blotter::store');
    $routes->get('blotter/view/(:num)', 'Blotter::view/$1');
    $routes->post('blotter/update/(:num)', 'Blotter::update/$1');
});


// ------------------------------------------------
// ADMIN AREA
// ------------------------------------------------
$routes->group('admin', [
    'namespace' => 'App\Controllers\Admin',
    'filter'    => 'adminOnly',
], function ($routes) {

    $routes->get('dashboard', 'Dashboard::index');

    // =========================
    // SETTINGS
    // =========================
    $routes->get('settings', 'Settings::index');
    $routes->post('settings', 'Settings::update');
    $routes->post('settings/update', 'Settings::update');


    // =========================
    // CERTIFICATE TYPES
    // =========================
    $routes->get('certificateTypes', 'CertificateTypes::index');
    $routes->get('certificateTypes/create', 'CertificateTypes::create');
    $routes->post('certificateTypes/store', 'CertificateTypes::store');
    $routes->get('certificateTypes/edit/(:num)', 'CertificateTypes::edit/$1');
    $routes->post('certificateTypes/update/(:num)', 'CertificateTypes::update/$1');
    $routes->delete('certificateTypes/delete/(:num)', 'CertificateTypes::delete/$1');


    // =========================
    // USERS
    // =========================
    $routes->group('users', ['namespace' => 'App\Controllers'], function ($routes) {
        $routes->get('/', 'Users::index');
        $routes->get('create', 'Users::create');
        $routes->post('save', 'Users::save');
        $routes->get('edit/(:segment)', 'Users::edit/$1');
        $routes->post('update', 'Users::update');
        $routes->delete('delete/(:num)', 'Users::delete/$1');
        $routes->post('fetchRecords', 'Users::fetchRecords');
    });
});


// ------------------------------------------------
// STAFF AREA
// ------------------------------------------------
$routes->group('staff', [
    'namespace' => 'App\Controllers\Staff',
    'filter'    => 'staffOnly',
], function ($routes) {

    $routes->get('dashboard', 'Dashboard::index');

    $routes->get('residents', function () {
        return redirect()->to('/resident');
    });

    $routes->group('users', ['namespace' => 'App\Controllers'], function ($routes) {
        $routes->get('/', 'Users::index');
        $routes->post('fetchRecords', 'Users::fetchRecords');
    });
});


// ------------------------------------------------
// SYSTEM LOGS
// ------------------------------------------------
$routes->get('logs', 'Logs::log');
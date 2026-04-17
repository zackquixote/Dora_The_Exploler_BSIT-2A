<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// ─── Auth ─────────────────────────────────────────────────────────────────────
$routes->get('/',      'Auth::index');
$routes->get('login',  'Auth::index');
$routes->post('auth',  'Auth::auth');
$routes->get('logout', 'Auth::logout');

// ─── Staff & Admin Dashboards ─────────────────────────────────────────────────
$routes->group('staff', ['namespace' => 'App\Controllers\Staff'], function ($routes) {
    $routes->get('dashboard', 'Dashboard::index');
});

$routes->group('admin', ['namespace' => 'App\Controllers\Admin'], function ($routes) {
    $routes->get('dashboard', 'Dashboard::index');
});

$routes->group('staff/residents', ['namespace' => 'App\Controllers\Staff'], function ($routes) {

    // Pages
    $routes->get('/', 'Resident::index');
    $routes->get('create', 'Resident::create');
    $routes->get('edit/(:num)', 'Resident::edit/$1');
    $routes->get('view/(:num)', 'Resident::view/$1');

    // AJAX
    $routes->post('list', 'Resident::list');

    // Actions
    $routes->post('store', 'Resident::store');
    $routes->post('update/(:num)', 'Resident::update/$1');
    $routes->post('delete/(:num)', 'Resident::delete/$1');

    // Optional
    $routes->get('households', 'Resident::households');
});

$routes->group('households', function($routes) {

    // Pages
    $routes->get('/', 'HouseholdController::index');
    $routes->get('create', 'HouseholdController::create');
    $routes->get('edit/(:num)', 'HouseholdController::edit/$1');

    // Data
    $routes->get('list', 'HouseholdController::list');
    $routes->get('residentsOptions', 'HouseholdController::residentsOptions');

    // Actions
    $routes->post('store', 'HouseholdController::store');
    $routes->post('update/(:num)', 'HouseholdController::update/$1');
    $routes->post('delete/(:num)', 'HouseholdController::delete/$1');
});

// ─── Users ────────────────────────────────────────────────────────────────────
$routes->group('staff/users', ['namespace' => 'App\Controllers\Staff'], function ($routes) {
    $routes->get('/',                'Users::index');
    $routes->post('save',            'Users::save');
    $routes->get('edit/(:segment)',  'Users::edit/$1');
    $routes->post('update',          'Users::update');
    $routes->delete('delete/(:num)', 'Users::delete/$1');
    $routes->post('fetchRecords',    'Users::fetchRecords');
});

// ─── Misc ─────────────────────────────────────────────────────────────────────
$routes->get('residents', 'Staff\Resident::index');
$routes->get('log',       'Logs::log');
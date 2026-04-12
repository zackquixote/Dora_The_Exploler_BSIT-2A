<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// ─── Auth ─────────────────────────────────────────────────────────────────────
$routes->get('/',      'Auth::index');
$routes->get('login',  'Auth::index');
$routes->post('auth',  'Auth::auth');
$routes->get('logout', 'Auth::logout');

// ─── Staff Dashboard ──────────────────────────────────────────────────────────
$routes->group('staff', ['namespace' => 'App\Controllers\Staff'], function ($routes) {
    $routes->get('dashboard', 'Dashboard::index');
});

// ─── Admin Dashboard ──────────────────────────────────────────────────────────
$routes->group('admin', ['namespace' => 'App\Controllers\Admin'], function ($routes) {
    $routes->get('dashboard', 'Dashboard::index');
});

// ─── Staff Residents ──────────────────────────────────────────────────────────
$routes->group('staff/resident', ['namespace' => 'App\Controllers\Staff'], function ($routes) {
    $routes->get('/',              'Resident::index');
    $routes->post('list',          'Resident::list');        // FIX: was GET, must be POST
    $routes->get('households',     'Resident::households');
    $routes->post('store',         'Resident::store');
    $routes->get('show/(:num)',    'Resident::show/$1');
    $routes->post('update/(:num)', 'Resident::update/$1');
    $routes->post('delete/(:num)', 'Resident::delete/$1');
});

// Alias so base_url('residents') works as a shortcut to the index page
$routes->get('residents', 'Staff\Resident::index');

// ─── Households ───────────────────────────────────────────────────────────────
$routes->group('households', function ($routes) {
    $routes->get('/',              'HouseholdController::index');
    $routes->post('list',          'HouseholdController::list');
    $routes->post('store',         'HouseholdController::store');
    $routes->get('show/(:num)',    'HouseholdController::show/$1');
    $routes->post('update/(:num)', 'HouseholdController::update/$1');
    $routes->post('delete/(:num)', 'HouseholdController::delete/$1');
    $routes->get('residentsOptions', 'HouseholdController::residentsOptions');
});

// ─── Person ───────────────────────────────────────────────────────────────────
$routes->group('person', function ($routes) {
    $routes->get('/',                  'Person::index');
    $routes->post('save',              'Person::save');
    $routes->get('edit/(:segment)',    'Person::edit/$1');
    $routes->post('update',            'Person::update');
    $routes->delete('delete/(:num)',   'Person::delete/$1');
    $routes->post('fetchRecords',      'Person::fetchRecords');
});

// ─── Staff Users ──────────────────────────────────────────────────────────────
$routes->group('staff/users', function ($routes) {
    $routes->get('/',                'Users::index');
    $routes->post('save',            'Users::save');
    $routes->get('edit/(:segment)',  'Users::edit/$1');
    $routes->post('update',          'Users::update');
    $routes->delete('delete/(:num)', 'Users::delete/$1');
    $routes->post('fetchRecords',    'Users::fetchRecords');
});

// ─── Logs ─────────────────────────────────────────────────────────────────────
$routes->get('log', 'Logs::log');
<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// ─── Auth ────────────────────────────────────────────
$routes->get('/',         'Auth::index');
$routes->get('/login',    'Auth::index');
$routes->post('/auth',    'Auth::auth');
$routes->get('/logout',   'Auth::logout');

// ─── Staff ───────────────────────────────────────────
$routes->group('staff', ['namespace' => 'App\Controllers\Staff'], function($routes) {
    $routes->get('dashboard', 'Dashboard::index');
});

// ─── Admin ───────────────────────────────────────────
$routes->group('admin', ['namespace' => 'App\Controllers\Admin'], function($routes) {
    $routes->get('dashboard', 'Dashboard::index');
});

// ─── Residents (Staff\Resident) ───────────────────────
$routes->group('residents', ['namespace' => 'App\Controllers\Staff'], function($routes) {
    $routes->get('',                'Resident::index');
    $routes->get('list',            'Resident::list');
    $routes->post('store',          'Resident::store');
    $routes->get('show/(:num)',     'Resident::show/$1');
    $routes->post('update/(:num)',  'Resident::update/$1');
    $routes->get('delete/(:num)',   'Resident::delete/$1');
});
$routes->get('test-resident', function() {
    return new \App\Controllers\Staff\Resident();
});
// ─── Users ───────────────────────────────────────────
$routes->get('/staff/users',                        'Users::index');
$routes->post('/staff/save',                        'Users::save');
$routes->get('/staff/users/edit/(:segment)',        'Users::edit/$1');
$routes->post('/staff/users/update',                'Users::update');
$routes->delete('/staff/users/delete/(:num)',       'Users::delete/$1');
$routes->post('/staff/users/fetchRecords',          'Users::fetchRecords');

// ─── Person ──────────────────────────────────────────
$routes->get('/person',                     'Person::index');
$routes->post('/person/save',               'Person::save');
$routes->get('/person/edit/(:segment)',     'Person::edit/$1');
$routes->post('/person/update',             'Person::update');
$routes->delete('/person/delete/(:num)',    'Person::delete/$1');
$routes->post('/person/fetchRecords',       'Person::fetchRecords');

// ─── Logs ────────────────────────────────────────────
$routes->get('/log', 'Logs::log');
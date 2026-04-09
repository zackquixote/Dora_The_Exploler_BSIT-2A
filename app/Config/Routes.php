<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// ... existing routes ...

// Admin Routes
$routes->group('admin', ['namespace' => 'App\Controllers\Admin'], function($routes) {
    $routes->get('dashboard', 'Dashboard::index');
});

// Staff Routes
$routes->group('staff', ['namespace' => 'App\Controllers\Staff'], function($routes) {
    $routes->get('dashboard', 'Dashboard::index');
});

// Authentication Routes
$routes->get('/', 'Auth::index');
$routes->post('/auth', 'Auth::auth');
$routes->get('/login', 'Auth::index');
$routes->get('/logout', 'Auth::logout');
$routes->get('/dashboard', 'Dashboard::index');

// User Accounts Routes
$routes->get('/users', 'Users::index');
$routes->post('users/save', 'Users::save');
$routes->get('users/edit/(:segment)', 'Users::edit/$1');
$routes->post('users/update', 'Users::update');
$routes->delete('users/delete/(:num)', 'Users::delete/$1');
$routes->post('users/fetchRecords', 'Users::fetchRecords');

// Person Routes
$routes->get('/person', 'Person::index');
$routes->post('person/save', 'Person::save');
$routes->get('person/edit/(:segment)', 'Person::edit/$1');
$routes->post('person/update', 'Person::update');
$routes->delete('person/delete/(:num)', 'Person::delete/$1');
$routes->post('person/fetchRecords', 'Person::fetchRecords');


// Admin Routes (only admin role allowed)
$routes->group('admin', [
    'namespace' => 'App\Controllers\Admin',
    'filter'    => 'role:admin'          // ← add this
], function($routes) {
    $routes->get('dashboard', 'Dashboard::index');
});

// Staff Routes (only staff role allowed)
$routes->group('staff', [
    'namespace' => 'App\Controllers\Staff',
    'filter'    => 'role:staff'          // ← add this
], function($routes) {
    $routes->get('dashboard', 'Dashboard::index');
});

// Logs Routes for Admin
$routes->get('/log', 'Logs::log');

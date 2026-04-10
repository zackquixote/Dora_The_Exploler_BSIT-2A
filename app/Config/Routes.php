<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'Auth::index');
$routes->get('/login', 'Auth::index');
$routes->post('/auth', 'Auth::auth');
$routes->get('/logout', 'Auth::logout');
$routes->get('/dashboard', 'Auth::index');

$routes->group('staff', ['namespace' => 'App\Controllers\Staff'], function($routes) {
    $routes->get('dashboard', 'Dashboard::index'); // ← ::index
});

$routes->group('admin', ['namespace' => 'App\Controllers\Admin'], function($routes) {
    $routes->get('dashboard', 'Dashboard::index'); // ← ::index
}); 

// User Accounts Routes
$routes->get('/staff/users', 'Users::index');
$routes->post('/staff/save', 'Users::save');
$routes->get('/staff/users/edit/(:segment)', 'Users::edit/$1');
$routes->post('/staff/users/update', 'Users::update');
$routes->delete('/staff/users/delete/(:num)', 'Users::delete/$1');
$routes->post('/staff/users/fetchRecords', 'Users::fetchRecords');

// User Accounts Routes
$routes->get('/users', 'Users::index');
$routes->post('/users/save', 'Users::save');
$routes->get('/users/edit/(:segment)', 'Users::edit/$1');
$routes->post('/users/update', 'Users::update');
$routes->delete('/users/delete/(:num)', 'Users::delete/$1');
$routes->post('/users/fetchRecords', 'Users::fetchRecords');

// Person Routes
$routes->get('/person', 'Person::index');
$routes->post('/person/save', 'Person::save');
$routes->get('/person/edit/(:segment)', 'Person::edit/$1');
$routes->post('/person/update', 'Person::update');
$routes->delete('/person/delete/(:num)', 'Person::delete/$1');
$routes->post('/person/fetchRecords', 'Person::fetchRecords');

// Logs Routes
$routes->get('/log', 'Logs::log');
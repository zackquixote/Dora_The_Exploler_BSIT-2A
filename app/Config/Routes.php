<?php

use CodeIgniter\Router\RouteCollection;

/**
 * --------------------------------------------------------------------
 * Route Setup
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

/** @var RouteCollection $routes */

// ------------------------------------------------
// Authentication (Public)
// ------------------------------------------------
 $routes->get('/', 'Auth::index');
 $routes->match(['get', 'post'], 'login', 'Auth::index');
 $routes->post('auth', 'Auth::auth');
 $routes->get('logout', 'Auth::logout');

// ------------------------------------------------
// Admin Area (Role: Admin Only)
// ------------------------------------------------
 $routes->group('admin', [
    'namespace' => 'App\Controllers\Admin',
    'filter'    => 'adminOnly',
], function ($routes) {

    $routes->get('dashboard', 'Dashboard::index');

    // User Management (Points to App\Controllers\Users)
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
// Staff Area (Role: Staff Only)
// ------------------------------------------------
 $routes->group('staff', [
    'namespace' => 'App\Controllers\Staff',
    'filter'    => 'staffOnly',
], function ($routes) {

    $routes->get('dashboard', 'Dashboard::index');

    // Residents
    $routes->group('residents', function ($routes) {
        $routes->get('/', 'Resident::index');
        $routes->match(['get', 'post'], 'create', 'Resident::create');
        $routes->post('store', 'Resident::store');
        $routes->match(['get', 'post'], 'edit/(:num)', 'Resident::edit/$1');
        $routes->post('update/(:num)', 'Resident::update/$1');
        $routes->get('view/(:num)', 'Resident::view/$1');
    });

    // User Management (Points to App\Controllers\Users)
    // Note: Currently only index/fetch enabled for Staff. 
    // Add save/update/delete routes here if Staff needs full CRUD.
    $routes->group('users', ['namespace' => 'App\Controllers'], function ($routes) {
        $routes->get('/', 'Users::index');
        $routes->post('fetchRecords', 'Users::fetchRecords');
    });
});

// ------------------------------------------------
// Shared Area (Role: Logged In)
// ------------------------------------------------
 $routes->group('', [
    'namespace' => 'App\Controllers',
    'filter'    => 'loggedIn',
], function ($routes) {

    // Residents
    $routes->get('residents', 'Resident::index');
    $routes->match(['get', 'post'], 'resident/create', 'Resident::create');
    $routes->post('resident/store', 'Resident::store');
    $routes->match(['get', 'post'], 'resident/edit/(:num)', 'Resident::edit/$1');
    $routes->post('resident/update/(:num)', 'Resident::update/$1');
    $routes->get('resident/view/(:num)', 'Resident::view/$1');
    $routes->post('resident/delete/(:num)', 'Resident::delete/$1');
    
    $routes->post('resident/getHouseholdsBySitio', 'Resident::getHouseholdsBySitio');
    $routes->get('resident/assign-search', 'Resident::assignSearch');
    $routes->get('resident/assign/(:num)', 'Resident::assign/$1');

    // Households
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
});

// ------------------------------------------------
// System (Admin Only)
// ------------------------------------------------
 $routes->get('log', 'Logs::log', ['filter' => 'adminOnly']);
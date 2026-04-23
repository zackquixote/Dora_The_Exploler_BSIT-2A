<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// ─── AUTH ─────────────────────────────────────────────────────────────────────
 $routes->get('/', 'Auth::index');
 $routes->get('login', 'Auth::index');
 $routes->post('auth', 'Auth::auth');
 $routes->get('logout', 'Auth::logout');

// ─── STAFF DASHBOARD ─────────────────────────────────────────────────────────
 $routes->group('staff', ['namespace' => 'App\Controllers\Staff'], function ($routes) {
    $routes->get('dashboard', 'Dashboard::index');
});

// ─── ADMIN DASHBOARD ─────────────────────────────────────────────────────────
 $routes->group('admin', ['namespace' => 'App\Controllers\Admin'], function ($routes) {
    $routes->get('dashboard', 'Dashboard::index');
});

// ─── RESIDENT MANAGEMENT (Controller: App\Controllers\Resident) ──────────────
 $routes->group('', ['namespace' => 'App\Controllers'], function ($routes) {
    
    // --- Dashboard & List ---
    $routes->get('resident', 'Resident::index');
    $routes->get('residents', 'Resident::index'); // Alias
    
    // --- CRUD Operations ---
    $routes->match(['get', 'post'], 'resident/create', 'Resident::create');
    $routes->post('resident/store', 'Resident::store');
    
    $routes->match(['get', 'post'], 'resident/edit/(:num)', 'Resident::edit/$1');
    $routes->post('resident/update/(:num)', 'Resident::update/$1');
    $routes->put('resident/update/(:num)', 'Resident::update/$1'); // REST support
    
    $routes->get('resident/view/(:num)', 'Resident::view/$1');
    $routes->post('resident/delete/(:num)', 'Resident::delete/$1');
    
    // --- AJAX & Data Endpoints ---
    $routes->post('resident/list', 'Resident::list');
    
    // Household Logic (Sitio/Zone dependent dropdowns)
    $routes->match(['get', 'post'], 'resident/getHouseholdsBySitio', 'Resident::getHouseholdsBySitio');
    $routes->match(['get', 'post'], 'resident/get-households-by-sitio', 'Resident::getHouseholdsBySitio');

    // --- NEW: ASSIGNING EXISTING RESIDENTS ---
    $routes->get('resident/assign-search', 'Resident::assignSearch');
    $routes->get('resident/assign/(:num)', 'Resident::assign/$1');
});

// ─── STAFF RESIDENT BACKWARD COMPATIBILITY ─────────────────────────────────────
 $routes->get('staff/residents', 'Resident::index');
 $routes->match(['get', 'post'], 'staff/residents/create', 'Resident::create');
 $routes->post('staff/residents/store', 'Resident::store');
 $routes->match(['get', 'post'], 'staff/residents/edit/(:num)', 'Resident::edit/$1');
 $routes->post('staff/residents/update/(:num)', 'Resident::update/$1');
 $routes->get('staff/residents/view/(:num)', 'Resident::view/$1');

// ─── HOUSEHOLD MANAGEMENT (Controller: App\Controllers\HouseholdController) ──
 $routes->group('households', function ($routes) {
    $routes->get('/', 'HouseholdController::index');
    $routes->get('create', 'HouseholdController::create');
    $routes->post('store', 'HouseholdController::store');
    $routes->get('edit/(:num)', 'HouseholdController::edit/$1');
    $routes->post('update/(:num)', 'HouseholdController::update/$1');
    $routes->get('view/(:num)', 'HouseholdController::view/$1');
    $routes->post('delete/(:num)', 'HouseholdController::delete/$1');
    
    // AJAX for Households
    $routes->post('getResidentsBySitio', 'HouseholdController::getResidentsBySitio');
    $routes->post('get-by-sitio', 'HouseholdController::getBySitio');
    $routes->get('getNextHouseholdNo', 'HouseholdController::getNextHouseholdNo');
    $routes->get('checkHouseholdNo', 'HouseholdController::checkHouseholdNo');
});

// ─── USERS MANAGEMENT ────────────────────────────────────────────────────────
 $routes->group('staff/users', function ($routes) {
    $routes->get('/', 'Users::index');
    $routes->post('save', 'Users::save');
    $routes->get('edit/(:segment)', 'Users::edit/$1');
    $routes->post('update', 'Users::update');
    $routes->delete('delete/(:num)', 'Users::delete/$1');
    $routes->post('fetchRecords', 'Users::fetchRecords');
});

// ─── MISCELLANEOUS ─────────────────────────────────────────────────────────────
 $routes->get('log', 'Logs::log');
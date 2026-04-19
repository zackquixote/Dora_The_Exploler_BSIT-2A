<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// ─── Auth ─────────────────────────────────────────────────────────────────────
$routes->get('/',          'Auth::index');
$routes->get('login',      'Auth::index');
$routes->post('auth',      'Auth::auth');
$routes->get('logout',     'Auth::logout');

// ─── Staff Dashboard (uses residents/dashboard.php view) ─────────────────────
$routes->group('staff', ['namespace' => 'App\Controllers\Staff'], function ($routes) {
    $routes->get('dashboard', 'Dashboard::index');
});

// ─── Admin Dashboard ─────────────────────────────────────────────────────────
$routes->group('admin', ['namespace' => 'App\Controllers\Admin'], function ($routes) {
    $routes->get('dashboard', 'Dashboard::index');
});

// ─── Resident Management (Controller: App\Controllers\Resident) ──────────────
// Resident routes - Both with and without 'staff/' prefix
$routes->get('resident', 'Resident::index');
$routes->get('residents', 'Resident::index');  // For sidebar link
$routes->get('staff/residents', 'Resident::index');  // For backward compatibility

$routes->get('resident/create', 'Resident::create');
$routes->get('staff/residents/create', 'Resident::create');  // Backward compatibility

$routes->post('resident/store', 'Resident::store');
$routes->post('staff/residents/store', 'Resident::store');  // Backward compatibility

$routes->get('resident/edit/(:num)', 'Resident::edit/$1');
$routes->get('staff/residents/edit/(:num)', 'Resident::edit/$1');  // Backward compatibility

$routes->post('resident/update/(:num)', 'Resident::update/$1');
$routes->post('staff/residents/update/(:num)', 'Resident::update/$1');  // Backward compatibility

$routes->get('resident/view/(:num)', 'Resident::view/$1');
$routes->get('staff/residents/view/(:num)', 'Resident::view/$1');  // Backward compatibility

$routes->post('resident/delete/(:num)', 'Resident::delete/$1');
$routes->post('resident/list', 'Resident::list');

// ✅ FIXED: Add proper route for household dropdown AJAX
$routes->get('resident/getHouseholdsBySitio', 'Resident::getHouseholdsBySitio');
$routes->post('resident/getHouseholdsBySitio', 'Resident::getHouseholdsBySitio');

// Also keep hyphenated version for compatibility
$routes->get('resident/get-households-by-sitio', 'Resident::getHouseholdsBySitio');
$routes->post('resident/get-households-by-sitio', 'Resident::getHouseholdsBySitio');

// Alias for /residents (optional, points to same Resident controller)
$routes->get('residents', 'Resident::index');

// Household routes
$routes->get('households', 'HouseholdController::index');
$routes->get('households/create', 'HouseholdController::create');
$routes->post('households/store', 'HouseholdController::store');
$routes->get('households/edit/(:num)', 'HouseholdController::edit/$1');
$routes->post('households/update/(:num)', 'HouseholdController::update/$1');
$routes->get('households/view/(:num)', 'HouseholdController::view/$1');
$routes->post('households/delete/(:num)', 'HouseholdController::delete/$1');
$routes->post('households/getResidentsBySitio', 'HouseholdController::getResidentsBySitio');
$routes->post('households/get-by-sitio', 'HouseholdController::getBySitio');

// ─── Users Management (Staff) ─────────────────────────
$routes->group('staff/users', function ($routes) {
    $routes->get('/',                'Users::index');
    $routes->post('save',            'Users::save');
    $routes->get('edit/(:segment)',  'Users::edit/$1');
    $routes->post('update',          'Users::update');
    $routes->delete('delete/(:num)', 'Users::delete/$1');
    $routes->post('fetchRecords',    'Users::fetchRecords');
});

// ─── Miscellaneous ───────────────────────────────────────────────────────────
$routes->get('log', 'Logs::log');
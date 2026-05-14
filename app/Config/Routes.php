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


/**
 * --------------------------------------------------------------------
 * Public Authentication Routes
 * --------------------------------------------------------------------
 * Routes accessible without user authentication.
 * Handles the main portal access, login processing, and logout.
 */
$routes->get('/', 'Portal::index');
$routes->match(['get', 'post'], 'login', 'Auth::index');
$routes->post('auth', 'Auth::auth');
$routes->get('logout', 'Auth::logout');
$routes->post('debug/probe', 'DebugController::probe');


/**
 * --------------------------------------------------------------------
 * Authenticated Area (Main System Routes)
 * --------------------------------------------------------------------
 * All routes within this group require an active user session.
 * Protected by the 'loggedIn' route filter.
 */
$routes->group('', [
    'namespace' => 'App\Controllers',
    'filter'    => 'loggedIn',
], function ($routes) {

    /**
     * --------------------------------------------------------------------
     * Resident Management Routes
     * --------------------------------------------------------------------
     * CRUD operations and specialized endpoints for Barangay residents.
     */
    $routes->get('resident', 'Resident::index');
    $routes->get('residents', 'Resident::index');
    $routes->get('resident/exportCsv', 'Resident::exportCsv');
    $routes->get('resident/bulk-upload', 'Resident::bulkUpload');
    $routes->post('resident/process-bulk-upload', 'Resident::processBulkUpload');
    $routes->get('resident/download-template', 'Resident::downloadTemplate');
    $routes->match(['get', 'post'], 'resident/create', 'Resident::create');
    $routes->post('resident/store', 'Resident::store');
    $routes->match(['get', 'post'], 'resident/edit/(:num)', 'Resident::edit/$1');
    $routes->match(['post', 'put'], 'resident/update/(:num)', 'Resident::update/$1');
    $routes->get('resident/view/(:num)', 'Resident::view/$1');
    $routes->post('resident/delete/(:num)', 'Resident::delete/$1');

    $routes->get('resident/getHouseholdsBySitio', 'Resident::getHouseholdsBySitio');
    $routes->get('resident/activity/(:num)', 'Resident::activity/$1');
    $routes->get('resident/assign-search', 'Resident::assignSearch');
    $routes->post('resident/assignBulk', 'Resident::assignBulk');
    $routes->match(['post', 'put'], 'resident/updateStatus/(:num)', 'Resident::updateStatus/$1');
    $routes->match(['post', 'put'], 'resident/updateMemberStatus/(:num)', 'Resident::updateMemberStatus/$1');
    $routes->get('resident/checkDuplicate', 'Resident::checkDuplicate');

    /**
     * --------------------------------------------------------------------
     * Household Management Routes
     * --------------------------------------------------------------------
     * Grouped routes for handling household records and resident groupings.
     */
    $routes->group('households', function ($routes) {
        $routes->get('/', 'HouseholdController::index');
        $routes->match(['get', 'post'], 'create', 'HouseholdController::create');
        $routes->post('store', 'HouseholdController::store');
        $routes->match(['get', 'post'], 'edit/(:num)', 'HouseholdController::edit/$1');
        $routes->match(['post', 'put'], 'update/(:num)', 'HouseholdController::update/$1');
        $routes->get('view/(:num)', 'HouseholdController::view/$1');
        $routes->post('delete/(:num)', 'HouseholdController::delete/$1');
        $routes->post('set-head/(:num)', 'HouseholdController::setHead/$1');

        $routes->post('getResidentsBySitio', 'HouseholdController::getResidentsBySitio');
        $routes->post('get-by-sitio', 'HouseholdController::getBySitio');
        $routes->get('getNextHouseholdNo', 'HouseholdController::getNextHouseholdNo');
        $routes->get('checkHouseholdNo', 'HouseholdController::checkHouseholdNo');
    });


    /**
     * --------------------------------------------------------------------
     * Certificate Management Routes
     * --------------------------------------------------------------------
     * Handling the creation, generation, printing, and management of various certificates.
     */
    $routes->get('certificate', 'Certificate::index');
    $routes->get('certificate/create', 'Certificate::create');
    $routes->post('certificate/store', 'Certificate::store');
    $routes->get('certificate/bulk-create', 'Certificate::bulkCreate');
    $routes->post('certificate/bulk-store', 'Certificate::bulkStore');
    $routes->get('certificate/bulk-print', 'Certificate::bulkPrint');
    $routes->get('certificate/print/(:num)', 'Certificate::print_view/$1');
    $routes->get('certificate/edit/(:num)', 'Certificate::edit/$1');
    $routes->match(['post', 'put'], 'certificate/update/(:num)', 'Certificate::update/$1');
    $routes->post('certificate/delete/(:num)', 'Certificate::delete/$1');


    /**
     * --------------------------------------------------------------------
     * Barangay Officials Management Routes
     * --------------------------------------------------------------------
     * Full CRUD operations for managing current and past barangay officials.
     */
    $routes->get('officials', 'Officials::index');
    $routes->get('officials/create', 'Officials::create');
    $routes->post('officials/store', 'Officials::store');
    $routes->get('officials/edit/(:num)', 'Officials::edit/$1');
    $routes->match(['post', 'put'], 'officials/update/(:num)', 'Officials::update/$1');
    $routes->post('officials/delete/(:num)', 'Officials::delete/$1');


   
    /**
     * --------------------------------------------------------------------
     * Blotter / Incident Management Routes
     * --------------------------------------------------------------------
     * Endpoints for recording, updating, and managing blotter cases, hearings, and summons.
     */
    $routes->get('blotter', 'Blotter::index');
$routes->get('blotter/exportCsv', 'Blotter::exportCsv');
$routes->get('blotter/create', 'Blotter::create');
$routes->post('blotter/store', 'Blotter::store');
$routes->get('blotter/view/(:num)', 'Blotter::view/$1');
$routes->get('blotter/edit/(:num)', 'Blotter::edit/$1');
$routes->get('blotter/print-settlement/(:num)', 'Blotter::printSettlement/$1');
$routes->get('blotter/print-summon/(:num)/(:num)', 'Blotter::printSummon/$1/$2');
$routes->match(['post', 'put'], 'blotter/update/(:num)', 'Blotter::update/$1');
$routes->post('blotter/delete/(:num)', 'Blotter::delete/$1');
// Additional AJAX endpoint
$routes->get('blotter/searchResidents', 'Blotter::searchResidents');
$routes->get('blotter/getUpcomingNotifications', 'Blotter::getUpcomingNotifications');
$routes->post('blotter/hearing/add/(:num)', 'Blotter::addHearing/$1');
$routes->post('blotter/hearing/update/(:num)', 'Blotter::updateHearing/$1');
$routes->post('blotter/hearing/delete/(:num)', 'Blotter::deleteHearing/$1');
$routes->get('blotter/print/(:num)', 'Blotter::printCase/$1');

    /**
     * --------------------------------------------------------------------
     * Archive Routes
     * --------------------------------------------------------------------
     */
    $routes->group('archive', function ($routes) {
        $routes->get('/', 'Archive::index');
        $routes->get('restoreResident/(:num)', 'Archive::restoreResident/$1');
        $routes->get('forceDeleteResident/(:num)', 'Archive::forceDeleteResident/$1');
        $routes->get('restoreHousehold/(:num)', 'Archive::restoreHousehold/$1');
        $routes->get('forceDeleteHousehold/(:num)', 'Archive::forceDeleteHousehold/$1');
    });

    /**
     * --------------------------------------------------------------------
     * Global API Endpoints
     * --------------------------------------------------------------------
     * Generic endpoints used across the system (e.g., Global Search).
     */
    $routes->get('api/search', 'SearchController::index');

    /**
     * --------------------------------------------------------------------
     * System Logs
     * --------------------------------------------------------------------
     * View audit trails and user activity logs across the application.
     */
$routes->get('logs', 'Logs::log');
});


/**
 * --------------------------------------------------------------------
 * Administrative Routes
 * --------------------------------------------------------------------
 * Grouped routes restricted to Administrator role.
 * Protected by 'adminOnly' filter.
 */
$routes->group('admin', [
    'namespace' => 'App\Controllers\Admin',
    'filter'    => 'adminOnly',
], function ($routes) {

    $routes->get('dashboard', 'Dashboard::index');
    $routes->get('dashboard/filterCases', 'Dashboard::filterCases');

    /**
     * --------------------------------------------------------------------
     * System Settings
     * --------------------------------------------------------------------
     * Endpoints for configuring global system behaviors and information.
     */
    $routes->get('settings', 'Settings::index');
    $routes->post('settings', 'Settings::update');
    $routes->post('settings/update', 'Settings::update');

    /**
     * --------------------------------------------------------------------
     * Certificate Types Configuration
     * --------------------------------------------------------------------
     * Manage the dynamic types of certificates that can be issued.
     */
    $routes->get('certificateTypes', 'CertificateTypes::index');
    $routes->get('certificateTypes/create', 'CertificateTypes::create');
    $routes->post('certificateTypes/store', 'CertificateTypes::store');
    $routes->get('certificateTypes/edit/(:num)', 'CertificateTypes::edit/$1');
    $routes->post('certificateTypes/update/(:num)', 'CertificateTypes::update/$1');
    $routes->post('certificateTypes/delete/(:num)', 'CertificateTypes::delete/$1');


    /**
     * --------------------------------------------------------------------
     * User Identity and Access Management
     * --------------------------------------------------------------------
     * Administrative routes for managing system user accounts and roles.
     */
    $routes->group('users', ['namespace' => 'App\Controllers'], function ($routes) {
        $routes->get('/', 'Users::index');
        $routes->get('create', 'Users::create');
        $routes->post('save', 'Users::save');
        $routes->get('edit/(:segment)', 'Users::edit/$1');
        $routes->post('update', 'Users::update');
        $routes->post('delete/(:num)', 'Users::delete/$1');
        $routes->post('fetchRecords', 'Users::fetchRecords');
        $routes->get('view/(:num)', 'Users::view/$1');
    });
});


/**
 * --------------------------------------------------------------------
 * Staff / Standard User Routes
 * --------------------------------------------------------------------
 * Grouped routes specific to Staff role.
 * Protected by 'staffOnly' filter.
 */
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



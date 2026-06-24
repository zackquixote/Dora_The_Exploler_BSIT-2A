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
$routes->match(['get', 'post'], 'login', 'Auth::index', ['filter' => 'throttle:3,1']);
$routes->post('auth', 'Auth::auth', ['filter' => 'throttle:3,1']);
$routes->get('logout', 'Auth::logout');
$routes->match(['get', 'post'], 'portal/login', 'ResidentPortalAuth::login', ['filter' => 'throttle:5,1']);
$routes->match(['get', 'post'], 'portal/register', 'ResidentPortalAuth::register');
$routes->get('portal/verification-status', 'ResidentPortalAuth::verificationStatus');
$routes->match(['get', 'post'], 'portal/resubmit-verification', 'ResidentPortalAuth::resubmitVerification');
$routes->match(['get', 'post'], 'portal/verify-otp', 'ResidentPortalAuth::verifyOtp', ['filter' => 'throttle:5,1']);
$routes->post('portal/resend-otp', 'ResidentPortalAuth::resendOtp', ['filter' => 'throttle:3,1']);
$routes->match(['get', 'post'], 'portal/forgot-password', 'ResidentPortalAuth::forgotPassword');
$routes->match(['get', 'post'], 'portal/reset-password', 'ResidentPortalAuth::resetPassword');
$routes->get('portal/logout', 'ResidentPortalAuth::logout');
$routes->get('portal/setup', 'ResidentPortalAuth::forceSetup');
$routes->get('portal/migrate', 'ResidentPortalAuth::migrateDB');
$routes->post('debug/probe', 'Debug::probe');

// System Performance Monitor (Admin only)
$routes->group('system-monitor', ['filter' => 'role:admin'], function ($routes) {
    $routes->get('/', 'SystemMonitor::index');
    $routes->get('metrics', 'SystemMonitor::getMetrics');
    $routes->post('clear-cache', 'SystemMonitor::clearCache');
    $routes->post('optimize-database', 'SystemMonitor::optimizeDatabase');
});

// QR Check-in endpoint — accessible by logged-in admins/staff
$routes->get('events/checkin/(:num)/(:segment)', 'EventCheckIn::scan/$1/$2', ['filter' => 'loggedIn']);

// Authenticated Resident Portal routes — protected by portalAuth filter
$routes->group('portal', ['filter' => 'portalAuth'], function ($routes) {
    $routes->get('home', 'Portal::home');
    $routes->get('file-blotter', 'Portal::fileBlotter');
    $routes->post('blotter/submit', 'Portal::submitBlotter');
    $routes->get('facilities', 'Portal::facilities');
    $routes->get('facilities/calendar-data', 'Portal::facilityCalendarData');
    $routes->post('facilities/book', 'Portal::bookFacility');
    $routes->post('facilities/cancel/(:num)', 'Portal::cancelBooking/$1');
    $routes->get('my-id', 'Portal::myId');
    $routes->get('my-cases', 'Portal::myCases');
    $routes->get('certificates', 'Portal::myCertificates');
    $routes->get('certificates/request', 'Portal::requestCertificate');
    $routes->post('certificates/request', 'Portal::submitCertificateRequest');
    $routes->post('certificates/cancel/(:num)', 'Portal::cancelCertificateRequest/$1');
    $routes->get('notifications', 'Portal::notifications');
    $routes->post('notifications/read', 'Portal::markNotificationsRead');
    $routes->get('profile', 'Portal::profile');
    $routes->post('profile/update', 'Portal::updateProfile');
    // Events
    $routes->get('events', 'PortalEvents::index');
    $routes->post('events/register/(:num)', 'PortalEvents::register/$1');
    $routes->get('events/ticket/(:num)', 'PortalEvents::ticket/$1');
    $routes->post('events/cancel/(:num)', 'PortalEvents::cancel/$1');
});

$routes->get('verify/(:num)/(:segment)', 'IdGenerator::verify/$1/$2', ['filter' => 'throttle:5,1']);


/**
 * --------------------------------------------------------------------
 * Authenticated Area (Main System Routes)
 * --------------------------------------------------------------------
 * All routes within this group require an active user session.
 * Protected by the 'loggedIn' route filter.
 */
$routes->group('', [
    'namespace' => 'App\Controllers',
    'filter'    => ['loggedIn', 'role:admin,staff'],
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
    $routes->get('id/print/(:num)', 'IdGenerator::print/$1');

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
     * Document Management API (Phase 1.1)
     * --------------------------------------------------------------------
     * JSON endpoints for uploads/versioning/downloads.
     */
    $routes->group('api', function ($routes) {
        // Per-entity attachments
        $routes->get('entities/(:segment)/(:num)/documents', 'Api\EntityDocuments::index/$1/$2');
        $routes->post('entities/(:segment)/(:num)/documents', 'Api\EntityDocuments::upload/$1/$2');
        $routes->post('entities/(:segment)/(:num)/documents/(:num)/detach', 'Api\EntityDocuments::detach/$1/$2/$3');

        // Document actions (by document row id)
        $routes->get('documents/(:num)', 'Api\Documents::show/$1');
        $routes->get('documents/(:num)/versions', 'Api\Documents::versions/$1');
        $routes->post('documents/(:num)/versions', 'Api\Documents::uploadVersion/$1');
        $routes->get('documents/(:num)/download', 'Api\Documents::download/$1');
        $routes->post('documents/(:num)', 'Api\Documents::update/$1');
        $routes->post('documents/(:num)/delete', 'Api\Documents::delete/$1');

        // Phase 1.2 - Business Permit Renewals pipeline
        $routes->get('business-permits/(:num)/renewals', 'Api\PermitRenewals::listByBusiness/$1');
        $routes->post('business-permits/(:num)/renewals', 'Api\PermitRenewals::create/$1');
        $routes->get('permit-renewals/(:num)', 'Api\PermitRenewals::show/$1');
        $routes->post('permit-renewals/(:num)/pay', 'Api\PermitRenewals::pay/$1');
        $routes->post('permit-renewals/(:num)/approve', 'Api\PermitRenewals::approve/$1');
        $routes->post('permit-renewals/(:num)/mark-printed', 'Api\PermitRenewals::markPrinted/$1');

        // Phase 1.3 - Events QR check-in + attendance + certificates
        $routes->get('events/(:num)/participants', 'Api\EventParticipants::list/$1');
        $routes->post('events/(:num)/participants', 'Api\EventParticipants::register/$1');
        $routes->get('event-participants/(:num)/qr', 'Api\EventParticipants::qr/$1');
        $routes->post('event-participants/(:num)/check-in', 'Api\EventParticipants::checkIn/$1');
        $routes->post('event-participants/(:num)/check-out', 'Api\EventParticipants::checkOut/$1');
        $routes->post('event-participants/(:num)/certificate', 'Api\EventParticipants::generateCertificate/$1');
        $routes->get('event-participants/(:num)/certificate/download', 'Api\EventParticipants::downloadCertificate/$1');

        // Phase 1.4 - Health Records (CRUD + vaccination editor)
        $routes->get('health-records', 'Api\HealthRecords::index');
        $routes->post('health-records', 'Api\HealthRecords::create');
        $routes->get('health-records/(:num)', 'Api\HealthRecords::show/$1');
        $routes->post('health-records/(:num)', 'Api\HealthRecords::update/$1');
        $routes->post('health-records/(:num)/delete', 'Api\HealthRecords::delete/$1');
        $routes->get('health-records/(:num)/vaccinations', 'Api\HealthRecords::listVaccinations/$1');
        $routes->post('health-records/(:num)/vaccinations', 'Api\HealthRecords::addVaccination/$1');
        $routes->post('health-records/(:num)/vaccinations/(:num)/update', 'Api\HealthRecords::updateVaccination/$1/$2');
        $routes->post('health-records/(:num)/vaccinations/(:num)/delete', 'Api\HealthRecords::deleteVaccination/$1/$2');
    });

    // Printable view (admin/staff area)
    $routes->get('permit-renewals/print/(:num)', 'PermitRenewals::print/$1');

    // QR scan endpoint (admin/staff area)
    $routes->get('events/checkin/(:num)/(:segment)', 'EventCheckIn::scan/$1/$2');



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
 * QR Code Verification (Public Access)
 * --------------------------------------------------------------------
 */
$routes->get('verify/certificate/(:num)/(:segment)', 'AdvancedFeatures::verifyQR/certificate/$1/$2');
$routes->get('verify/resident/(:num)/(:segment)', 'AdvancedFeatures::verifyQR/resident/$1/$2');

/**
 * --------------------------------------------------------------------
 * Advanced Features Routes
 * --------------------------------------------------------------------
 * Accessible by authenticated users with specific roles
 */
$routes->group('advanced', ['filter' => 'loggedIn'], function ($routes) {
    
    // Shared Advanced Features (Admin, Staff, Resident)
    $routes->group('', ['filter' => 'role:admin,staff,resident'], function ($routes) {
        $routes->match(['get', 'post'], 'qr-generator', 'AdvancedFeatures::qrGenerator');
        $routes->match(['get', 'post'], 'report-emergency', 'AdvancedFeatures::reportEmergency');
        $routes->match(['get', 'post'], 'register-business', 'AdvancedFeatures::registerBusiness');
        $routes->get('events', 'AdvancedFeatures::events');
        $routes->match(['get', 'post'], 'create-event', 'AdvancedFeatures::createEvent');
    });

    // Admin & Staff Advanced Features
    $routes->group('', ['filter' => 'role:admin,staff'], function ($routes) {
        $routes->get('emergency', 'AdvancedFeatures::emergency');
        $routes->get('business', 'AdvancedFeatures::business');
        $routes->get('health-records', 'AdvancedFeatures::healthRecords');
        $routes->get('api/health-records/search', 'AdvancedFeatures::apiSearchHealthRecords');
        $routes->get('api/business/search', 'AdvancedFeatures::apiSearchBusiness');
        $routes->get('api/emergency/active', 'AdvancedFeatures::apiActiveEmergencies');
        $routes->get('api/events/list', 'AdvancedFeatures::apiEventsList');
    });

    // Strictly Admin Only
    $routes->group('', ['filter' => 'role:admin'], function ($routes) {
        $routes->get('analytics', 'AdvancedFeatures::analytics');
        $routes->get('notifications', 'AdvancedFeatures::notifications');
        $routes->match(['get', 'post'], 'send-notification', 'AdvancedFeatures::sendBulkNotification');
        $routes->post('get-recipient-count', 'AdvancedFeatures::getRecipientCount');
        $routes->get('load-template/(:num)', 'AdvancedFeatures::loadTemplate/$1');
        $routes->get('load-draft/(:num)', 'AdvancedFeatures::loadDraft/$1');
        $routes->post('delete-draft/(:num)', 'AdvancedFeatures::deleteDraft/$1');
        $routes->get('search-residents', 'AdvancedFeatures::searchResidents');
        $routes->get('gmail', 'AdvancedFeatures::gmail');
        $routes->get('gmail/connect', 'AdvancedFeatures::gmailConnect');
        $routes->get('gmail/callback', 'AdvancedFeatures::gmailCallback');
        $routes->get('test-notifications', 'AdvancedFeatures::testNotifications');
        $routes->post('test-notifications/send', 'AdvancedFeatures::sendTestNotifications');
        $routes->get('documents', 'AdvancedFeatures::documents');
        $routes->match(['get', 'post'], 'upload-document', 'AdvancedFeatures::uploadDocument');
        $routes->get('reports', 'AdvancedFeatures::reports');
        $routes->get('export', 'AdvancedFeatures::exportData');
        $routes->get('system-health', 'AdvancedFeatures::systemHealth');
        $routes->get('api/analytics/kpis', 'AdvancedFeatures::apiAnalyticsKpis');
    });
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
    // Announcements
    $routes->get('announcements', 'Announcements::index');
    $routes->match(['get', 'post'], 'announcements/create', 'Announcements::create');
    $routes->post('announcements/delete/(:num)', 'Announcements::delete/$1');

    // Portal Accounts Approval
    $routes->get('portal-accounts', 'PortalAccounts::index');
    $routes->get('portal-accounts/verification/(:num)', 'PortalAccounts::viewVerification/$1');
    $routes->post('portal-accounts/request-resubmission/(:num)', 'PortalAccounts::requestResubmission/$1');
    $routes->get('portal-accounts/file/(:num)', 'PortalAccounts::viewFile/$1');
    $routes->post('portal-accounts/approve/(:num)', 'PortalAccounts::approve/$1');
    $routes->post('portal-accounts/reject/(:num)', 'PortalAccounts::reject/$1');
    $routes->post('portal-accounts/suspend/(:num)', 'PortalAccounts::suspend/$1');
    $routes->post('portal-accounts/reactivate/(:num)', 'PortalAccounts::reactivate/$1');
    $routes->post('portal-accounts/reset-password/(:num)', 'PortalAccounts::resetPassword/$1');

    // Online Requests (Portal Certificates & Blotters)
    $routes->get('online-requests', 'OnlineRequests::index');
    $routes->post('online-requests/approve-certificate/(:num)', 'OnlineRequests::approveCertificate/$1');
    $routes->post('online-requests/reject-certificate/(:num)', 'OnlineRequests::rejectCertificate/$1');
    $routes->post('online-requests/acknowledge-blotter/(:num)', 'OnlineRequests::acknowledgeBlotter/$1');

    // Facility Bookings
    $routes->get('facility-bookings', 'FacilityBookings::index');
    $routes->post('facility-bookings/approve/(:num)', 'FacilityBookings::approve/$1');
    $routes->post('facility-bookings/reject/(:num)', 'FacilityBookings::reject/$1');

    // Events Management
    $routes->get('events', 'Events::index');
    $routes->get('events/create', 'Events::create');
    $routes->post('events/store', 'Events::store');
    $routes->get('events/view/(:num)', 'Events::view/$1');
    $routes->post('events/cancel/(:num)', 'Events::cancel/$1');


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



<?php
/**
 * Unified Sidebar — role-aware navigation for both Admin and Staff.
 *
 * Admin extras: User Accounts, Activity Logs, Settings link.
 * Staff: simplified nav without system management.
 */
 $uri  = service('uri');
 $seg1 = $uri->getSegment(1);
 $seg2 = $uri->getSegment(2);
 $role = strtolower(session()->get('role') ?? 'staff');
 $dashboardUrl = ($role === 'admin') ? 'admin/dashboard' : 'staff/dashboard';
 $isActive = function($val) use ($seg1, $seg2) {
    return ($seg1 === $val || $seg2 === $val) ? 'active' : '';
 };

 helper('barangay_settings');
 $bs = barangay_settings();
 $brandImg = $bs['photo'] ?: ($bs['logo'] ?: 'tabu.jpg');
?>

<nav class="bmis-sidebar" id="mainSidebar">
    <!-- Close button for mobile -->
    <button class="sb-close-btn" id="sidebarClose" title="Close Menu">
        <i class="fas fa-times"></i>
    </button>

    <!-- Logo -->
    <div class="sb-logo">
        <img
            src="<?= base_url('assets/img/' . esc($brandImg)) ?>"
            alt="<?= esc($bs['barangay_name']) ?> Logo"
            class="sb-logo-img"
            style="width:<?= (int)$bs['logo_size'] ?>px;height:<?= (int)$bs['logo_size'] ?>px;object-fit:cover;border-radius:50%;"
        >
        <div class="sb-logo-text">
            <h4><?= esc($bs['barangay_name']) ?></h4>
            <span><?= esc($bs['municipality']) ?></span>
        </div>
    </div>

    <!-- User -->
    <div class="sb-user">
        <div class="sb-avatar"><i class="fas <?= ($role === 'admin') ? 'fa-user-shield' : 'fa-user-tie' ?>"></i></div>
        <div class="sb-user-info">
            <div class="sb-user-name"><?= esc(session()->get('name') ?? 'User') ?></div>
            <div class="sb-user-role"><?= ucfirst($role) ?></div>
        </div>
        <span class="sb-user-badge"><?= ucfirst($role) ?></span>
    </div>

    <!-- Navigation -->
    <div class="sb-nav">
        <div class="sb-header">Main</div>
        <a href="<?= base_url($dashboardUrl) ?>" class="sb-link <?= $isActive('dashboard') ?>">
            <i class="fas fa-th-large"></i> Dashboard
        </a>
        <a href="<?= base_url('admin/announcements') ?>" class="sb-link <?= $isActive('announcements') ?>">
            <i class="fas fa-bullhorn"></i> Announcements
        </a>

        <div class="sb-header">Management</div>
        <a href="<?= base_url('resident') ?>" class="sb-link <?= $isActive('resident') ?>">
            <i class="fas fa-users"></i> Residents
        </a>
        <a href="<?= base_url('households') ?>" class="sb-link <?= $isActive('households') ?>">
            <i class="fas fa-house-user"></i> Households
        </a>
        <a href="<?= base_url('officials') ?>" class="sb-link <?= $isActive('officials') ?>">
            <i class="fas fa-user-tie"></i> Officials
        </a>
        <a href="<?= base_url('certificate') ?>" class="sb-link <?= $isActive('certificate') ?>">
            <i class="fas fa-file-contract"></i> Certificates
        </a>
        <a href="<?= base_url('blotter') ?>" class="sb-link <?= $isActive('blotter') ?>">
            <i class="fas fa-balance-scale"></i> Blotter Records
        </a>
        <a href="<?= base_url('admin/online-requests') ?>" class="sb-link <?= ($seg1 === 'admin' && $seg2 === 'online-requests') ? 'active' : '' ?>">
            <i class="fas fa-inbox"></i> Online Requests
            <?php
                // Quickly get pending count
                $db = \Config\Database::connect();
                $pendingCertCount = 0;
                $pendingBlotterCount = 0;
                $pendingFacilityCount = 0;
                try {
                    $pendingCertCount = $db->table('certificate_requests')->where('status', 'Pending')->countAllResults();
                    $pendingBlotterCount = $db->table('blotter_records')->where('status', 'Pending')->where('source', 'Online')->countAllResults();
                    $pendingFacilityCount = $db->table('facility_bookings')->where('status', 'Pending')->countAllResults();
                } catch (\Throwable $th) {}
                $totalPending = $pendingCertCount + $pendingBlotterCount;
            ?>
            <?php if ($totalPending > 0): ?>
                <span class="ds-badge ds-badge-danger" style="margin-left: auto; padding: 2px 6px; font-size: 10px;"><?= $totalPending ?></span>
            <?php endif; ?>
        </a>
        <a href="<?= base_url('admin/facility-bookings') ?>" class="sb-link <?= ($seg1 === 'admin' && $seg2 === 'facility-bookings') ? 'active' : '' ?>">
            <i class="fas fa-building"></i> Facility Bookings
            <?php if ($pendingFacilityCount > 0): ?>
                <span class="ds-badge ds-badge-danger" style="margin-left: auto; padding: 2px 6px; font-size: 10px;"><?= $pendingFacilityCount ?></span>
            <?php endif; ?>
        </a>
     
        <?php if ($role === 'admin'): ?>
        <div class="sb-header">Advanced</div>
        <a href="<?= base_url('advanced/analytics') ?>" class="sb-link <?= $isActive('analytics') ?>">
            <i class="fas fa-chart-line"></i> Analytics
        </a>
        <a href="<?= base_url('advanced/business') ?>" class="sb-link <?= ($seg1 === 'advanced' && $seg2 === 'business') ? 'active' : '' ?>">
            <i class="fas fa-store"></i> Businesses
        </a>
        <a href="<?= base_url('admin/events') ?>" class="sb-link <?= ($seg1 === 'admin' && $seg2 === 'events') ? 'active' : '' ?>">
            <i class="fas fa-calendar-alt"></i> Events
        </a>
        <a href="<?= base_url('advanced/health-records') ?>" class="sb-link <?= ($seg1 === 'advanced' && $seg2 === 'health-records') ? 'active' : '' ?>">
            <i class="fas fa-heartbeat"></i> Health Records
        </a>
        <a href="<?= base_url('advanced/emergency') ?>" class="sb-link <?= ($seg1 === 'advanced' && $seg2 === 'emergency') ? 'active' : '' ?>">
            <i class="fas fa-ambulance"></i> Emergency
        </a>
        <a href="<?= base_url('advanced/notifications') ?>" class="sb-link <?= ($seg1 === 'advanced' && $seg2 === 'notifications') ? 'active' : '' ?>">
            <i class="fas fa-bell"></i> Notifications
        </a>
        <a href="<?= base_url('advanced/gmail') ?>" class="sb-link <?= ($seg1 === 'advanced' && $seg2 === 'gmail') ? 'active' : '' ?>">
            <i class="fas fa-envelope"></i> Gmail
        </a>
        <a href="<?= base_url('advanced/test-notifications') ?>" class="sb-link <?= ($seg1 === 'advanced' && $seg2 === 'test-notifications') ? 'active' : '' ?>">
            <i class="fas fa-paper-plane"></i> Test Notifications
        </a>
        <a href="<?= base_url('advanced/documents') ?>" class="sb-link <?= ($seg1 === 'advanced' && $seg2 === 'documents') ? 'active' : '' ?>">
            <i class="fas fa-folder-open"></i> Documents
        </a>
        <a href="<?= base_url('advanced/reports') ?>" class="sb-link <?= ($seg1 === 'advanced' && $seg2 === 'reports') ? 'active' : '' ?>">
            <i class="fas fa-file-alt"></i> Reports
        </a>
        <?php endif; ?>

        <?php if ($role === 'admin'): ?>
        <div class="sb-header">System</div>
        <a href="<?= base_url('admin/portal-accounts') ?>" class="sb-link <?= ($seg1 === 'admin' && $seg2 === 'portal-accounts') ? 'active' : '' ?>">
            <i class="fas fa-users-cog"></i> Portal Accounts
        </a>
        <a href="<?= base_url('admin/users') ?>" class="sb-link <?= $isActive('users') ?>">
            <i class="fas fa-user-lock"></i> User Accounts
        </a>
        <a href="<?= base_url('logs') ?>" class="sb-link <?= $isActive('log') ?>">
            <i class="fas fa-history"></i> Activity Logs
        </a>
        <a href="<?= base_url('archive') ?>" class="sb-link <?= $isActive('archive') ?>">
            <i class="fas fa-trash-restore"></i> Recycle Bin
        </a>
        <hr class="sb-divider">
        <a href="<?= base_url('admin/settings') ?>" class="sb-link">
            <i class="fas fa-cog"></i> Settings
        </a>
        <?php endif; ?>
        <a href="<?= base_url('logout') ?>" class="sb-link sb-link-logout">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>
</nav>

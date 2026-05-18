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
?>

<nav class="bmis-sidebar" id="mainSidebar">
    <!-- Logo -->
    <div class="sb-logo">
        <img src="<?= base_url('assets/img/tabu.jpg') ?>" alt="BMIS" class="sb-logo-img">
        <div class="sb-logo-text">
            <h4>BMIS</h4>
            <span>Tabu, Ilog City</span>
        </div>
    </div>

    <!-- User -->
    <div class="sb-user">
        <div class="sb-avatar"><i class="fas <?= ($role === 'admin') ? 'fa-user-shield' : 'fa-user-tie' ?>"></i></div>
        <div class="sb-user-name"><?= esc(session()->get('name') ?? 'User') ?></div>
        <span class="sb-user-badge"><?= ucfirst($role) ?></span>
    </div>

    <!-- Navigation -->
    <div class="sb-nav">
        <div class="sb-header">Main</div>
        <a href="<?= base_url($dashboardUrl) ?>" class="sb-link <?= $isActive('dashboard') ?>">
            <i class="fas fa-th-large"></i> Dashboard
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
            <i class="fas fa-gavel"></i> Blotter
        </a>
     
        <?php if ($role === 'admin'): ?>
        <div class="sb-header">Advanced</div>
        <a href="<?= base_url('advanced/analytics') ?>" class="sb-link <?= $isActive('analytics') ?>">
            <i class="fas fa-chart-line"></i> Analytics
        </a>
        <a href="<?= base_url('advanced/business') ?>" class="sb-link <?= ($seg1 === 'advanced' && $seg2 === 'business') ? 'active' : '' ?>">
            <i class="fas fa-store"></i> Businesses
        </a>
        <a href="<?= base_url('advanced/events') ?>" class="sb-link <?= ($seg1 === 'advanced' && $seg2 === 'events') ? 'active' : '' ?>">
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
        <a href="<?= base_url('admin/users') ?>" class="sb-link <?= $isActive('users') ?>">
            <i class="fas fa-user-lock"></i> User Accounts
        </a>
        <a href="<?= base_url('admin/audit-logs') ?>" class="sb-link <?= ($seg1 === 'admin' && $seg2 === 'audit-logs') ? 'active' : '' ?>">
            <i class="fas fa-clipboard-list"></i> Audit Logs
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

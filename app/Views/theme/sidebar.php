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
        <div class="sb-header">System</div>
        <a href="<?= base_url('admin/users') ?>" class="sb-link <?= $isActive('users') ?>">
            <i class="fas fa-user-lock"></i> User Accounts
        </a>
        <a href="<?= base_url('logs') ?>" class="sb-link <?= $isActive('log') ?>">
            <i class="fas fa-history"></i> Activity Logs
        </a>
        <?php endif; ?>

        <hr class="sb-divider">
        <?php if ($role === 'admin'): ?>
        <a href="<?= base_url('admin/settings') ?>" class="sb-link">
            <i class="fas fa-cog"></i> Settings
        </a>
        <?php endif; ?>
        <a href="<?= base_url('logout') ?>" class="sb-link sb-link-logout">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>
</nav>
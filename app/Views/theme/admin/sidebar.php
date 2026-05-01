<?php //admin dashboard sidebar ?>
<?php
 $uri     = service('uri');
 $seg1    = $uri->getSegment(1);
 $seg2    = $uri->getSegment(2);

 $role = session()->get('role');

 $dashboardUrl = ($role === 'admin') ? 'admin/dashboard' : 'staff/dashboard';

 $isActive = function($val) use ($seg1, $seg2) {
    return ($seg1 === $val || $seg2 === $val) ? 'active' : '';
 };
?>

<link rel="stylesheet" href="<?= base_url('assets/css/theme/sidebar.css') ?>">

<aside class="main-sidebar elevation-4 sidebar-dark-danger" id="mainSidebar">

    <!-- Dynamic Brand Link -->
    <a href="<?= base_url($dashboardUrl) ?>" class="brand-link" id="brandLink">
        <img src="<?= base_url('assets/adminlte/dist/img/AdminLTELogo.png') ?>"
             alt="BMIS" class="brand-image img-circle elevation-3" style="opacity:.9">
        <span class="brand-text"> Tabu, Ilog City</span>
    </a>

    <div class="sidebar os-host-flexbox">
        <!-- User Card -->
        <div class="admin-user-card">
            <div class="admin-avatar" style="background: <?= ($role === 'admin') ? 'linear-gradient(135deg, #e94560, #c0392b)' : 'linear-gradient(135deg, #3498db, #2980b9)' ?>;">
                <i class="fas <?= ($role === 'admin') ? 'fa-user-shield' : 'fa-user-tie' ?>"></i>
            </div>
            <div class="admin-name"><?= esc(session()->get('name') ?? 'User') ?></div>
            <span class="admin-badge" style="background: <?= ($role === 'admin') ? 'linear-gradient(135deg, #e94560, #c0392b)' : 'linear-gradient(135deg, #3498db, #2980b9)' ?>;">
                <?= ucfirst($role) ?>
            </span>
        </div>

        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column"
                data-widget="treeview" role="menu" data-accordion="false">

                <!-- MAIN -->
                <li class="nav-header">Main</li>
                <li class="nav-item">
                    <a href="<?= base_url($dashboardUrl) ?>" class="nav-link <?= $isActive('dashboard') ?>">
                        <i class="nav-icon fas fa-th-large"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <!-- MANAGEMENT -->
                <li class="nav-header">Management</li>
                <li class="nav-item">
                    <a href="<?= base_url('resident') ?>" class="nav-link <?= $isActive('resident') ?>">
                        <i class="nav-icon fas fa-users"></i>
                        <p>Residents</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= base_url('households') ?>" class="nav-link <?= $isActive('households') ?>">
                        <i class="nav-icon fas fa-house-user"></i>
                        <p>Households</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= base_url('officials') ?>" class="nav-link <?= $isActive('officials') ?>">
                        <i class="nav-icon fas fa-user-tie"></i>
                        <p>Officials</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= base_url('certificate') ?>" class="nav-link <?= $isActive('certificate') ?>">
                        <i class="nav-icon fas fa-file-contract"></i>
                        <p>Certificates</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= base_url('blotter') ?>" class="nav-link <?= $isActive('blotter') ?>">
                        <i class="nav-icon fas fa-gavel"></i>
                        <p>Blotter</p>
                    </a>
                </li>

                <!-- SYSTEM: ADMIN ONLY -->
                <?php if ($role === 'admin'): ?>
                <li class="nav-header">System</li>
                <li class="nav-item">
                    <a href="<?= base_url('admin/users') ?>" class="nav-link <?= $isActive('users') ?>">
                        <i class="nav-icon fas fa-user-lock"></i>
                        <p>User Accounts</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= base_url('logs') ?>" class="nav-link <?= $isActive('log') ?>">
                        <i class="nav-icon fas fa-history"></i>
                        <p>Activity Logs</p>
                    </a>
                </li>
                <?php endif; ?>

                <li><div class="sidebar-divider"></div></li>
                <li class="nav-item">
                    <a href="<?= base_url('admin/settings') ?>" class="nav-link">
                        <i class="nav-icon fas fa-cog"></i>
                        <p>Settings</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= base_url('logout') ?>" class="nav-link nav-link-logout">
                        <i class="nav-icon fas fa-sign-out-alt"></i>
                        <p>Logout</p>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>
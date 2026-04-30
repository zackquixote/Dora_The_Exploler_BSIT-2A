<?php //admin dashboard sidebar ?>
<style>
/* ── Admin Sidebar ─────────────────────────────────────── */
.main-sidebar {
    background: #0d0d1a !important;
    border-right: 1px solid rgba(233,69,96,0.15) !important;
    font-family: 'Source Sans Pro', sans-serif;
}
.main-sidebar::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    background:
        radial-gradient(ellipse at 20% 20%, rgba(233,69,96,0.08) 0%, transparent 60%),
        radial-gradient(ellipse at 80% 80%, rgba(192,57,43,0.06) 0%, transparent 60%);
    pointer-events: none;
    z-index: 0;
}
.main-sidebar .sidebar,
.main-sidebar .brand-link { position: relative; z-index: 1; }

/* Brand */
.brand-link {
    background: transparent !important;
    border-bottom: 1px solid rgba(233,69,96,0.2) !important;
    padding: 14px 16px !important;
    display: flex !important;
    align-items: center !important;
    gap: 10px !important;
}
.brand-link:hover { background: rgba(233,69,96,0.05) !important; }
.brand-image {
    background: linear-gradient(135deg, #e94560, #c0392b) !important;
    box-shadow: 0 0 12px rgba(233,69,96,0.5) !important;
    border: 2px solid rgba(233,69,96,0.3) !important;
}
.brand-text {
    color: #fff !important;
    font-weight: 800 !important;
    font-size: 15px !important;
    letter-spacing: 2px !important;
    text-transform: uppercase;
}

/* User Card */
.admin-user-card {
    margin: 16px 12px 8px;
    padding: 14px 12px;
    background: rgba(255,255,255,0.04);
    border: 1px solid rgba(233,69,96,0.15);
    border-radius: 12px;
    text-align: center;
}
.admin-avatar {
    width: 52px; height: 52px;
    border-radius: 50%;
    background: linear-gradient(135deg, #e94560, #c0392b);
    margin: 0 auto 8px;
    display: flex; align-items: center; justify-content: center;
    box-shadow: 0 0 20px rgba(233,69,96,0.4), 0 0 0 3px rgba(233,69,96,0.15);
    font-size: 20px; color: #fff;
}
.admin-name {
    color: #fff;
    font-size: 13px;
    font-weight: 700;
    margin-bottom: 4px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.admin-badge {
    display: inline-block;
    background: linear-gradient(135deg, #e94560, #c0392b);
    color: #fff;
    font-size: 9px;
    font-weight: 800;
    letter-spacing: 2px;
    padding: 3px 10px;
    border-radius: 20px;
    text-transform: uppercase;
}

/* Nav */
.nav-header {
    color: rgba(233,69,96,0.5) !important;
    font-size: 9px !important;
    font-weight: 800 !important;
    letter-spacing: 2.5px !important;
    padding: 14px 20px 4px !important;
    text-transform: uppercase !important;
}
.nav-sidebar .nav-link {
    color: rgba(255,255,255,0.5) !important;
    border-radius: 8px !important;
    margin: 1px 10px !important;
    padding: 9px 12px !important;
    transition: all 0.25s ease !important;
    border: 1px solid transparent !important;
    font-size: 13px !important;
    font-weight: 500 !important;
    position: relative;
    overflow: hidden;
}
.nav-sidebar .nav-link::before {
    content: '';
    position: absolute;
    left: 0; top: 0; bottom: 0;
    width: 0;
    background: linear-gradient(90deg, rgba(233,69,96,0.3), transparent);
    border-radius: 8px 0 0 8px;
    transition: width 0.25s ease;
}
.nav-sidebar .nav-link:hover::before { width: 3px; }
.nav-sidebar .nav-link:hover {
    color: #fff !important;
    background: rgba(233,69,96,0.1) !important;
    border-color: rgba(233,69,96,0.2) !important;
    transform: translateX(3px) !important;
}
.nav-sidebar .nav-link.active {
    background: linear-gradient(135deg, rgba(233,69,96,0.25), rgba(192,57,43,0.15)) !important;
    color: #fff !important;
    border-color: rgba(233,69,96,0.35) !important;
    box-shadow: 0 2px 12px rgba(233,69,96,0.2) !important;
}
.nav-sidebar .nav-link.active::before { width: 3px; background: #e94560; }
.nav-icon {
    color: rgba(255,255,255,0.35) !important;
    width: 1.5rem !important;
    font-size: 14px !important;
    transition: color 0.25s ease !important;
}
.nav-sidebar .nav-link:hover .nav-icon,
.nav-sidebar .nav-link.active .nav-icon { color: #e94560 !important; }

/* Logout link */
.nav-link-logout { color: rgba(231,76,60,0.7) !important; }
.nav-link-logout:hover {
    color: #e74c3c !important;
    background: rgba(231,76,60,0.1) !important;
    border-color: rgba(231,76,60,0.2) !important;
}
.nav-link-logout .nav-icon { color: rgba(231,76,60,0.5) !important; }
.nav-link-logout:hover .nav-icon { color: #e74c3c !important; }

.sidebar-divider {
    border-top: 1px solid rgba(255,255,255,0.06) !important;
    margin: 10px 16px !important;
}

/* Scrollbar */
.sidebar::-webkit-scrollbar { width: 4px; }
.sidebar::-webkit-scrollbar-track { background: transparent; }
.sidebar::-webkit-scrollbar-thumb { background: rgba(238, 232, 233, 0.3); border-radius: 4px; }
</style>

<?php
 $uri     = service('uri');
 $seg1    = $uri->getSegment(1);
 $seg2    = $uri->getSegment(2);

 $role = session()->get('role'); // Get role from session (admin or staff)

// Determine Dashboard URL based on role
 $dashboardUrl = ($role === 'admin') ? 'admin/dashboard' : 'staff/dashboard';

// Helper for active class
 $isActive = function($val) use ($seg1, $seg2) {
    return ($seg1 === $val || $seg2 === $val) ? 'active' : '';
 };
?>

<aside class="main-sidebar elevation-4 sidebar-dark-danger" id="mainSidebar">

    <!-- Dynamic Brand Link (ADDED BACK) -->
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
                
                <!-- OFFICIALS LINK -->
                <li class="nav-item">
                    <a href="<?= base_url('officials') ?>" class="nav-link <?= $isActive('officials') ?>">
                        <i class="nav-icon fas fa-user-tie"></i>
                        <p>Officials</p>
                    </a>
                </li>

                <!-- CERTIFICATES LINK (FIXED) -->
                <li class="nav-item">
                    <a href="<?= base_url('certificate') ?>" class="nav-link <?= $isActive('certificate') ?>">
                        <i class="nav-icon fas fa-file-contract"></i>
                        <p>Certificates</p>
                    </a>
                </li>
                
                <!-- BLOTTER LINK -->
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
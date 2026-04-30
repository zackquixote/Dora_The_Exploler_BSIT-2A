<?php //admin dashboard sidebar ?>
<style>
/* ============================================================
   Admin Sidebar — Enhanced
   Theme: Deep Night / Crimson
   Font: Plus Jakarta Sans
   ============================================================ */

@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

/* ── Base ─────────────────────────────────────────────────── */
.main-sidebar {
    background: #0a0a16 !important;
    border-right: 1px solid rgba(233, 69, 96, 0.12) !important;
    font-family: 'Plus Jakarta Sans', sans-serif !important;
    width: 260px !important;
}

.main-sidebar::before {
    content: '';
    position: absolute;
    inset: 0;
    background:
        radial-gradient(ellipse at 15% 15%, rgba(233, 69, 96, 0.07) 0%, transparent 55%),
        radial-gradient(ellipse at 85% 85%, rgba(192, 57, 43, 0.05) 0%, transparent 55%),
        repeating-linear-gradient(
            0deg,
            transparent,
            transparent 40px,
            rgba(255,255,255,0.008) 40px,
            rgba(255,255,255,0.008) 41px
        );
    pointer-events: none;
    z-index: 0;
}

.main-sidebar .sidebar,
.main-sidebar .brand-link {
    position: relative;
    z-index: 1;
}

/* ── Brand / Logo ─────────────────────────────────────────── */
.brand-link {
    background: transparent !important;
    border-bottom: 1px solid rgba(233, 69, 96, 0.15) !important;
    padding: 16px 18px !important;
    display: flex !important;
    align-items: center !important;
    gap: 12px !important;
    text-decoration: none !important;
    position: relative;
    overflow: hidden;
}

.brand-link::after {
    content: '';
    position: absolute;
    bottom: 0; left: 18px; right: 18px;
    height: 1px;
    background: linear-gradient(90deg, transparent, rgba(233,69,96,0.5), transparent);
}

.brand-link:hover {
    background: rgba(233, 69, 96, 0.04) !important;
}

/* CSS Logo Mark — replaces image */
.brand-logo-mark {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    background: linear-gradient(135deg, #e94560 0%, #c0392b 100%);
    box-shadow:
        0 0 0 1px rgba(233,69,96,0.4),
        0 0 16px rgba(233,69,96,0.35),
        inset 0 1px 0 rgba(255,255,255,0.15);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 16px;
    color: #fff;
    font-weight: 800;
    font-family: 'Plus Jakarta Sans', sans-serif;
    letter-spacing: -1px;
    position: relative;
    overflow: hidden;
}

.brand-logo-mark::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 50%;
    background: rgba(255,255,255,0.08);
    border-radius: 10px 10px 0 0;
}

/* Hide the original AdminLTE image, use CSS mark instead */
.brand-link .brand-image {
    display: none !important;
}

/* Inject logo via pseudo on brand-link */
.brand-link::before {
    content: 'B';
    width: 38px;
    height: 38px;
    border-radius: 10px;
    background: linear-gradient(135deg, #e94560 0%, #c0392b 100%);
    box-shadow:
        0 0 0 1px rgba(233,69,96,0.4),
        0 0 16px rgba(233,69,96,0.35),
        inset 0 1px 0 rgba(255,255,255,0.15);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 18px;
    color: #fff;
    font-weight: 800;
    font-family: 'Plus Jakarta Sans', sans-serif;
    text-align: center;
    line-height: 38px;
}

.brand-text {
    color: #fff !important;
    font-weight: 700 !important;
    font-size: 13px !important;
    letter-spacing: 0.08em !important;
    text-transform: uppercase !important;
    font-family: 'Plus Jakarta Sans', sans-serif !important;
    line-height: 1.3 !important;
}

/* ── User Card ────────────────────────────────────────────── */
.admin-user-card {
    margin: 14px 12px 8px;
    padding: 14px 12px;
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(233, 69, 96, 0.12);
    border-radius: 12px;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.admin-user-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 2px;
    background: linear-gradient(90deg, transparent, rgba(233,69,96,0.6), transparent);
}

.admin-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: linear-gradient(135deg, #e94560, #c0392b);
    margin: 0 auto 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow:
        0 0 0 3px rgba(233,69,96,0.15),
        0 0 20px rgba(233,69,96,0.3);
    font-size: 18px;
    color: #fff;
}

.admin-name {
    color: #fff;
    font-size: 12.5px;
    font-weight: 700;
    margin-bottom: 6px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    font-family: 'Plus Jakarta Sans', sans-serif;
    letter-spacing: 0.01em;
}

.admin-badge {
    display: inline-block;
    background: rgba(233, 69, 96, 0.15);
    color: #e94560;
    font-size: 9px;
    font-weight: 700;
    letter-spacing: 0.12em;
    padding: 3px 10px;
    border-radius: 20px;
    text-transform: uppercase;
    border: 1px solid rgba(233,69,96,0.25);
    font-family: 'Plus Jakarta Sans', sans-serif;
}

/* ── Nav Headers ──────────────────────────────────────────── */
.nav-header {
    color: rgba(233, 69, 96, 0.45) !important;
    font-size: 9px !important;
    font-weight: 700 !important;
    letter-spacing: 0.18em !important;
    padding: 16px 20px 5px !important;
    text-transform: uppercase !important;
    font-family: 'Plus Jakarta Sans', sans-serif !important;
}

/* ── Nav Links ────────────────────────────────────────────── */
.nav-sidebar .nav-link {
    color: rgba(255, 255, 255, 0.45) !important;
    border-radius: 8px !important;
    margin: 1px 10px !important;
    padding: 9px 12px !important;
    transition: all 0.2s ease !important;
    border: 1px solid transparent !important;
    font-size: 13px !important;
    font-weight: 500 !important;
    font-family: 'Plus Jakarta Sans', sans-serif !important;
    position: relative;
    overflow: hidden;
    letter-spacing: 0.01em !important;
}

.nav-sidebar .nav-link::after {
    content: '';
    position: absolute;
    left: 0; top: 20%; bottom: 20%;
    width: 0;
    background: #e94560;
    border-radius: 0 2px 2px 0;
    transition: width 0.2s ease;
}

.nav-sidebar .nav-link:hover {
    color: rgba(255, 255, 255, 0.9) !important;
    background: rgba(233, 69, 96, 0.08) !important;
    border-color: rgba(233, 69, 96, 0.15) !important;
    transform: translateX(2px) !important;
}

.nav-sidebar .nav-link:hover::after { width: 2px; }

.nav-sidebar .nav-link.active {
    background: rgba(233, 69, 96, 0.12) !important;
    color: #fff !important;
    border-color: rgba(233, 69, 96, 0.25) !important;
    box-shadow: 0 2px 10px rgba(233, 69, 96, 0.15) !important;
}

.nav-sidebar .nav-link.active::after {
    width: 2px;
}

/* ── Icons ────────────────────────────────────────────────── */
.nav-icon {
    color: rgba(255, 255, 255, 0.25) !important;
    width: 1.5rem !important;
    font-size: 13px !important;
    transition: color 0.2s ease !important;
}

.nav-sidebar .nav-link:hover .nav-icon,
.nav-sidebar .nav-link.active .nav-icon {
    color: #e94560 !important;
}

/* ── Logout ───────────────────────────────────────────────── */
.nav-link-logout {
    color: rgba(231, 76, 60, 0.6) !important;
}

.nav-link-logout:hover {
    color: #e74c3c !important;
    background: rgba(231, 76, 60, 0.08) !important;
    border-color: rgba(231, 76, 60, 0.2) !important;
}

.nav-link-logout .nav-icon { color: rgba(231, 76, 60, 0.4) !important; }
.nav-link-logout:hover .nav-icon { color: #e74c3c !important; }

/* ── Divider ──────────────────────────────────────────────── */
.sidebar-divider {
    border: none !important;
    border-top: 1px solid rgba(255, 255, 255, 0.05) !important;
    margin: 8px 16px !important;
}

/* ── Scrollbar ────────────────────────────────────────────── */
.sidebar::-webkit-scrollbar { width: 3px; }
.sidebar::-webkit-scrollbar-track { background: transparent; }
.sidebar::-webkit-scrollbar-thumb {
    background: rgba(233, 69, 96, 0.2);
    border-radius: 4px;
}
.sidebar::-webkit-scrollbar-thumb:hover {
    background: rgba(233, 69, 96, 0.4);
}

    /* Custom styles for the sidebar */
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
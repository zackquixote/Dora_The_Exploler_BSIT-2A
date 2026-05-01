<?php //staff dashboard sidebar ?>
<style>
/* ── Staff Sidebar (Enhanced) ────────────────────────────────── */
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

.main-sidebar {
    background: #061a10 !important;
    border-right: 1px solid rgba(39, 174, 96, 0.15) !important;
    font-family: 'Plus Jakarta Sans', sans-serif !important;
    box-shadow: 4px 0 15px rgba(0,0,0,0.2);
}

.main-sidebar::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    /* Enhanced gradient overlay */
    background:
        radial-gradient(circle at 15% 15%, rgba(39, 174, 96, 0.08) 0%, transparent 50%),
        radial-gradient(circle at 85% 85%, rgba(46, 204, 113, 0.05) 0%, transparent 50%);
    pointer-events: none;
    z-index: 0;
}

.main-sidebar .sidebar,
.main-sidebar .brand-link { position: relative; z-index: 1; }

/* Brand / Logo */
.brand-link {
    background: rgba(0,0,0,0.1) !important;
    border-bottom: 1px solid rgba(39, 174, 96, 0.15) !important;
    padding: 14px 16px !important;
    display: flex !important;
    align-items: center !important;
    gap: 12px !important;
    transition: all 0.3s ease;
}

.brand-link:hover { 
    background: rgba(39, 174, 96, 0.08) !important; 
}

.brand-image {
    /* Enhanced logo container */
    background: #fff !important; 
    padding: 2px;
    box-shadow: 0 4px 12px rgba(39, 174, 96, 0.4) !important;
    border: 2px solid rgba(39, 174, 96, 0.3) !important;
    transition: transform 0.3s ease;
}

.brand-link:hover .brand-image {
    transform: scale(1.05) rotate(2deg);
}

.brand-text {
    color: #fff !important;
    font-weight: 800 !important;
    font-size: 14px !important;
    letter-spacing: 0.5px !important;
    text-transform: uppercase;
    text-shadow: 0 2px 4px rgba(0,0,0,0.4);
}

/* User Card */
.staff-user-card {
    margin: 18px 12px 12px;
    padding: 18px 12px;
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(39, 174, 96, 0.2);
    border-radius: 14px;
    text-align: center;
    backdrop-filter: blur(5px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.staff-avatar {
    width: 56px; 
    height: 56px;
    border-radius: 50%;
    /* Richer gradient for avatar */
    background: linear-gradient(135deg, #27ae60, #145a32);
    margin: 0 auto 10px;
    display: flex; 
    align-items: center; 
    justify-content: center;
    box-shadow: 
        0 0 0 3px rgba(39, 174, 96, 0.2), 
        0 4px 10px rgba(0,0,0,0.3);
    font-size: 22px; 
    color: #fff;
    transition: transform 0.3s ease;
}

.staff-user-card:hover .staff-avatar {
    transform: scale(1.1);
}

.staff-name {
    color: #fff;
    font-size: 13px;
    font-weight: 700;
    margin-bottom: 6px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    text-shadow: 0 1px 2px rgba(0,0,0,0.5);
}

.staff-badge {
    display: inline-block;
    /* Better badge contrast */
    background: rgba(39, 174, 96, 0.2);
    border: 1px solid rgba(39, 174, 96, 0.4);
    color: #2ecc71; 
    font-size: 9px;
    font-weight: 800;
    letter-spacing: 1px;
    padding: 3px 12px;
    border-radius: 20px;
    text-transform: uppercase;
}

/* Navigation Headers */
.nav-header {
    color: rgba(39, 174, 96, 0.5) !important;
    font-size: 10px !important;
    font-weight: 800 !important;
    letter-spacing: 1.5px !important;
    padding: 18px 22px 6px !important;
    text-transform: uppercase !important;
}

/* Navigation Links */
.nav-sidebar .nav-link {
    color: rgba(255, 255, 255, 0.6) !important;
    border-radius: 10px !important;
    margin: 2px 12px !important;
    padding: 10px 14px !important;
    transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1) !important;
    border: 1px solid transparent !important;
    font-size: 13px !important;
    font-weight: 500 !important;
    position: relative;
    overflow: hidden;
}

/* Accent Bar Animation */
.nav-sidebar .nav-link::before {
    content: '';
    position: absolute;
    left: 0; top: 15%; bottom: 15%;
    width: 0;
    background: #2ecc71;
    border-radius: 0 4px 4px 0;
    box-shadow: 0 0 10px rgba(46, 204, 113, 0.6);
    transition: width 0.3s ease;
    z-index: 1;
}

.nav-sidebar .nav-link:hover {
    color: #fff !important;
    background: rgba(39, 174, 96, 0.1) !important;
    border-color: rgba(39, 174, 96, 0.15) !important;
    transform: translateX(4px) !important;
}

.nav-sidebar .nav-link:hover::before { width: 4px; }

.nav-sidebar .nav-link.active {
    /* Glassy active state */
    background: linear-gradient(90deg, rgba(39, 174, 96, 0.2), rgba(39, 174, 96, 0.05)) !important;
    color: #fff !important;
    border-color: rgba(39, 174, 96, 0.3) !important;
    box-shadow: 0 2px 10px rgba(0,0,0,0.15), inset 0 0 10px rgba(39, 174, 96, 0.05) !important;
}

.nav-sidebar .nav-link.active::before { width: 4px; background: #2ecc71; }

/* Icons */
.nav-icon {
    color: rgba(255, 255, 255, 0.4) !important;
    width: 1.5rem !important;
    font-size: 14px !important;
    transition: all 0.3s ease !important;
    margin-right: 8px !important;
}

.nav-sidebar .nav-link:hover .nav-icon,
.nav-sidebar .nav-link.active .nav-icon { 
    color: #2ecc71 !important;
    text-shadow: 0 0 8px rgba(46, 204, 113, 0.4);
}

/* Logout */
.nav-link-logout { color: rgba(231, 76, 60, 0.7) !important; }
.nav-link-logout:hover {
    color: #e74c3c !important;
    background: rgba(231, 76, 60, 0.12) !important;
    border-color: rgba(231, 76, 60, 0.3) !important;
    transform: translateX(4px) !important;
}
.nav-link-logout .nav-icon { color: rgba(231, 76, 60, 0.5) !important; }
.nav-link-logout:hover .nav-icon { color: #e74c3c !important; text-shadow: 0 0 8px rgba(231, 76, 60, 0.4); }

/* Divider */
.sidebar-divider {
    border: none !important;
    border-top: 1px solid rgba(255, 255, 255, 0.06) !important;
    margin: 12px 20px !important;
}

/* Enhanced Scrollbar */
.sidebar::-webkit-scrollbar { width: 6px; }
.sidebar::-webkit-scrollbar-track { background: transparent; }
.sidebar::-webkit-scrollbar-thumb { 
    background: rgba(39, 174, 96, 0.3); 
    border-radius: 4px;
}
.sidebar::-webkit-scrollbar-thumb:hover { 
    background: rgba(39, 174, 96, 0.6); 
}
</style>

<?php
 $uri      = service('uri');
 $seg1     = $uri->getSegment(1);
 $seg2     = $uri->getSegment(2);
 $isActive = function($val) use ($seg1, $seg2) {
    return ($seg1 === $val || $seg2 === $val) ? 'active' : '';
};
?>

<aside class="main-sidebar elevation-4 sidebar-dark-success" id="mainSidebar">

    <!-- Brand Logo -->
    <a href="<?= base_url('staff/dashboard') ?>" class="brand-link" id="brandLink">
        <img src="<?= base_url('assets/adminlte/dist/img/AdminLTELogo.png') ?>"
             alt="BMIS" class="brand-image img-circle elevation-3" style="opacity:.9">
        <span class="brand-text">Tabu, Ilog City</span>
    </a>

    <div class="sidebar os-host-flexbox">
        <!-- User Card -->
        <div class="staff-user-card">
            <div class="staff-avatar">
                <i class="fas fa-user-tie"></i>
            </div>
            <div class="staff-name"><?= esc(session()->get('name') ?? 'Staff User') ?></div>
            <span class="staff-badge">Staff</span>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column"
                data-widget="treeview" role="menu" data-accordion="false">

                <!-- MAIN -->
                <li class="nav-header">Main</li>
                <li class="nav-item">
                    <a href="<?= base_url('staff/dashboard') ?>" class="nav-link <?= $isActive('dashboard') ?>">
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

                <!-- BLOTTER LINK -->
                <li class="nav-item">
                    <a href="<?= base_url('blotter') ?>" class="nav-link <?= $isActive('blotter') ?>">
                        <i class="nav-icon fas fa-gavel"></i>
                        <p>Blotter</p>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?= base_url('certificate') ?>" class="nav-link <?= $isActive('certificate') ?>">
                        <i class="nav-icon fas fa-file-contract"></i>
                        <p>Certificates</p>
                    </a>
                </li>
             

                <li><div class="sidebar-divider"></div></li>

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
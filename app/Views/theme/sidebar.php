<style type="text/css">
.nav-sidebar .nav-link {
    position: relative;
    transition: background 0.2s ease;
}
.nav-sidebar .nav-link::before {
    content: "";
    position: absolute;
    left: 0; top: 0;
    height: 100%; width: 4px;
    background: orange;
    border-radius: 0 3px 3px 0;
    transform: scaleY(0);
    transform-origin: top;
    transition: transform 0.25s ease;
}
.nav-sidebar .nav-link.active::before,
.nav-sidebar .nav-link:hover::before { transform: scaleY(1); }
.nav-sidebar .nav-link:hover,
.nav-sidebar .nav-link.active {
    background: linear-gradient(to right, rgba(255,165,0,0.05), rgba(255,165,0,0.01)) !important;
    box-shadow: none !important;
}
.nav-treeview .nav-link:hover,
.nav-treeview .nav-link.active {
    background: linear-gradient(to right, rgba(255,165,0,0.05), rgba(255,165,0,0.01)) !important;
    box-shadow: none !important;
}
body.dark-mode .main-sidebar .nav-link { color: #fff !important; }
body.dark-mode .main-sidebar .nav-link p { color: #fff !important; }
body.dark-mode .main-sidebar .nav-icon { color: #fff !important; }
body.dark-mode .main-sidebar .nav-link.active,
body.dark-mode .main-sidebar .nav-link:hover { background-color: rgba(255,255,255,0.1) !important; }
</style>

<?php
// ✅ Updated Logic: Checks both Segment 1 and Segment 2
 $uri = service('uri');
 $seg1 = $uri->getSegment(1);
 $seg2 = $uri->getSegment(2);

// Helper function to set active class
 $isActive = function($val) use ($seg1, $seg2) {
    return ($seg1 === $val || $seg2 === $val) ? 'active' : '';
};
?>

<aside class="main-sidebar sidebar-light-light sidebar-light elevation-5" id="mainSidebar">
  <div class="brand-link bg-warning" id="brandLink" style="cursor:default; border-bottom: 1px rgba(255,255,255);">
    <img src="<?= base_url('assets/adminlte/dist/img/AdminLTELogo.png') ?>"
         alt="AdminLTE Logo"
         class="brand-image img-circle elevation-3"
         style="opacity:.8">
    <span class="brand-text font-weight-light" style="color:white;">SMART BMIS</span>
  </div>

  <div class="sidebar">
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

        <!-- Dashboard -->
        <li class="nav-item">
          <a href="<?= base_url((session()->get('role') ?? 'staff') . '/dashboard') ?>"
             class="nav-link <?= $isActive('dashboard') ?>">
            <i class="nav-icon fas fa-tachometer-alt"></i>
            <p>Dashboard</p>
          </a>
        </li>

        <!-- Residents -->
        <li class="nav-item">
          <a href="<?= base_url('residents') ?>"
             class="nav-link <?= $isActive('residents') ?>">
            <i class="nav-icon fas fa-users"></i>
            <p>Residents</p>
          </a>
        </li>

        <!-- Households -->
        <li class="nav-item">
          <a href="<?= base_url('households') ?>"
             class="nav-link <?= $isActive('households') ?>">
            <i class="nav-icon fas fa-home"></i>
            <p>Households</p>
          </a>
        </li>

        <!-- Person -->
        <li class="nav-item">
          <a href="<?= base_url('person') ?>"
             class="nav-link <?= $isActive('person') ?>">
            <i class="nav-icon fas fa-user-friends"></i>
            <p>Person</p>
          </a>
        </li>

        <!-- User Accounts -->
        <li class="nav-item">
          <a href="<?= base_url('staff/users') ?>"
             class="nav-link <?= $isActive('users') ?>">
            <i class="nav-icon fas fa-user-lock"></i>
            <p>User Accounts</p>
          </a>
        </li>

        <!-- Activity Logs -->
        <li class="nav-item">
          <a href="<?= base_url('log') ?>"
             class="nav-link <?= $isActive('log') ?>">
            <i class="nav-icon fas fa-history"></i>
            <p>Activity Logs</p>
          </a>
        </li>

        <!-- Logout -->
        <li class="nav-item mt-3">
          <a href="<?= base_url('logout') ?>" class="nav-link text-danger">
            <i class="nav-icon fas fa-sign-out-alt"></i>
            <p>Logout</p>
          </a>
        </li>

      </ul>
    </nav>
  </div>
</aside>
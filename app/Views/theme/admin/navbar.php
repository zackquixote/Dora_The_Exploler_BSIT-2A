<nav class="main-header navbar navbar-expand navbar-dark" id="mainNavbar">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                <i class="fas fa-bars"></i>
            </a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="<?= base_url('admin/dashboard') ?>" class="nav-link">
                <i class="fas fa-home mr-1"></i> Home
            </a>
        </li>
    </ul>

    <ul class="navbar-nav ml-auto align-items-center">
        <!-- Theme Toggle -->
        <li class="nav-item mr-2">
            <a class="nav-link" href="#" id="themeToggle">
                <i class="fas fa-sun"></i>
            </a>
        </li>
        
        <!-- User Email Display -->
        <li class="nav-item">
            <a class="nav-link d-flex align-items-center" href="#">
                <div class="nav-avatar-icon">
                    <i class="fas fa-user-shield"></i>
                </div>
                <span class="nav-user-email ml-2"><?= esc(session()->get('email')) ?></span>
            </a>
        </li>
<li class="nav-item dropdown">
    <a class="nav-link" data-toggle="dropdown" href="#" id="notifications-bell">
        <i class="fas fa-bell"></i>
        <span class="badge badge-warning navbar-badge notifications-badge" style="display: none;"></span>
    </a>
    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" id="notifications-dropdown-menu">
        <div class="dropdown-item text-muted text-center">Loading...</div>
    </div>
</li>
        <!-- Tinanggal na dito yung Logout button para hindi redundant sa sidebar -->
    </ul>
</nav>

<!-- Include external navbar CSS -->
<link rel="stylesheet" href="<?= base_url('assets/css/theme/navbar.css') ?>">
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
        <li class="nav-item mr-2">
            <a class="nav-link" href="#" id="themeToggle">
                <i class="fas fa-sun"></i>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link d-flex align-items-center" href="#">
                <div class="nav-avatar-icon">
                    <i class="fas fa-user-shield"></i>
                </div>
                <span class="nav-user-email"><?= esc(session()->get('email')) ?></span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link btn-logout" href="<?= base_url('logout') ?>">
                <i class="fas fa-sign-out-alt mr-1"></i> Logout
            </a>
        </li>
    </ul>
</nav>

<!-- Include external navbar CSS -->
<link rel="stylesheet" href="<?= base_url('assets/css/theme/navbar.css') ?>">
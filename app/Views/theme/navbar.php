<nav class="main-header navbar navbar-expand navbar-warning" id="mainNavbar">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                <i class="fas fa-bars" style="color: #fff;"></i>
            </a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <!-- ✅ Fixed: was index3.html -->
            <a href="<?= base_url(session()->get('role') . '/dashboard') ?>" class="nav-link" style="color: #fff;">
                <i class="fas fa-home mr-1"></i> Home
            </a>
        </li>
    </ul>

    <ul class="navbar-nav ml-auto">
        <li class="nav-item">
            <a class="nav-link" href="#" id="themeToggle" style="color: #fff;">
                <i class="fas fa-sun"></i>
            </a>
        </li>
        <li class="nav-item">
            <a style="color: #fff;" class="nav-link" href="#">
                <?= esc(session()->get('email')) ?>
                <i class="far fa-user-circle" style="color: #fff; margin-left: 5px;"></i>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="<?= base_url('logout') ?>" style="color: #fff;">
                Logout <i class="fa fa-sign-out-alt fa-fw"></i>
            </a>
        </li>
    </ul>
</nav>
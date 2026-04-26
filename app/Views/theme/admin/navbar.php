<nav class="main-header navbar navbar-expand navbar-dark" id="mainNavbar" style="background: linear-gradient(135deg, #1a1a2e, #16213e); border-bottom: 2px solid #e94560;">
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
                <div style="width:32px;height:32px;border-radius:50%;background:#e94560;display:flex;align-items:center;justify-content:center;margin-right:8px;">
                    <i class="fas fa-user-shield" style="font-size:14px;"></i>
                </div>
                <!-- UPDATED SPAN BELOW -->
                <span style="font-size:13px; font-weight:bold; color:#ffffff;"><?= esc(session()->get('email')) ?></span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link btn btn-sm ml-2" href="<?= base_url('logout') ?>" style="background:#e94560;border-radius:20px;padding:4px 14px;">
                <i class="fas fa-sign-out-alt mr-1"></i> Logout
            </a>
        </li>
    </ul>
</nav>
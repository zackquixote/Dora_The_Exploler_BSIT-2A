<nav class="main-header navbar navbar-expand navbar-dark" id="mainNavbar" style="background: linear-gradient(135deg, #1a3c2e, #145a32); border-bottom: 2px solid #27ae60;">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                <i class="fas fa-bars"></i>
            </a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="<?= base_url('staff/dashboard') ?>" class="nav-link">
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
                <div style="width:32px;height:32px;border-radius:50%;background:#27ae60;display:flex;align-items:center;justify-content:center;margin-right:8px;">
                    <i class="fas fa-user" style="font-size:14px;"></i>
                </div>
                <span style="font-size:13px;"><?= esc(session()->get('email')) ?></span>
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
    </ul>
</nav>
<?php //staff sidebar ?>
<?php
 $uri  = service('uri');
 $seg1 = $uri->getSegment(1);
 $seg2 = $uri->getSegment(2);
 $isActive = function($val) use ($seg1, $seg2) {
    return ($seg1 === $val || $seg2 === $val) ? 'active' : '';
 };
?>

<nav class="bmis-sidebar" id="mainSidebar">
    <!-- Logo -->
    <div class="sb-logo">
        <img src="<?= base_url('assets/img/tabu.jpg') ?>" alt="BMIS" class="sb-logo-img">
        <div class="sb-logo-text">
            <h4>BMIS</h4>
            <span>Tabu, Ilog City</span>
        </div>
    </div>

    <!-- User -->
    <div class="sb-user">
        <div class="sb-avatar"><i class="fas fa-user-tie"></i></div>
        <div class="sb-user-name"><?= esc(session()->get('name') ?? 'Staff User') ?></div>
        <span class="sb-user-badge">Staff</span>
    </div>

    <!-- Navigation -->
    <div class="sb-nav">
        <div class="sb-header">Main</div>
        <a href="<?= base_url('staff/dashboard') ?>" class="sb-link <?= $isActive('dashboard') ?>">
            <i class="fas fa-th-large"></i> Dashboard
        </a>

        <div class="sb-header">Management</div>
        <a href="<?= base_url('resident') ?>" class="sb-link <?= $isActive('resident') ?>">
            <i class="fas fa-users"></i> Residents
        </a>
        <a href="<?= base_url('households') ?>" class="sb-link <?= $isActive('households') ?>">
            <i class="fas fa-house-user"></i> Households
        </a>
        <a href="<?= base_url('officials') ?>" class="sb-link <?= $isActive('officials') ?>">
            <i class="fas fa-user-tie"></i> Officials
        </a>
        <a href="<?= base_url('blotter') ?>" class="sb-link <?= $isActive('blotter') ?>">
            <i class="fas fa-gavel"></i> Blotter
        </a>
        <a href="<?= base_url('certificate') ?>" class="sb-link <?= $isActive('certificate') ?>">
            <i class="fas fa-file-contract"></i> Certificates
        </a>

        <hr class="sb-divider">
        <a href="<?= base_url('logout') ?>" class="sb-link sb-link-logout">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>
</nav>
<!DOCTYPE html>
<html lang="en" style="font-size: 14px;">
<head>
    <meta name="csrf-token" content="<?= csrf_hash() ?>">
    <meta name="csrf-name"  content="<?= csrf_token() ?>">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BMIS | <?= isset($title) ? esc($title) : 'Dashboard' ?></title>

    <!-- CRITICAL: AdminLTE Core & Custom Styles -->
    <link rel="stylesheet" href="<?= base_url('assets/adminlte/dist/css/adminlte.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/user/user.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/dashboard/style.css') ?>">
    
    <!-- NON-CRITICAL CSS - Deferred (loads asynchronously) -->
    <link rel="preload" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback"></noscript>
    
    <link rel="preload" href="<?= base_url('assets/adminlte/plugins/fontawesome-free/css/all.min.css') ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="<?= base_url('assets/adminlte/plugins/fontawesome-free/css/all.min.css') ?>"></noscript>
    
    <link rel="preload" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css"></noscript>
    
    <!-- Plugin CSS - Deferred -->
    <link rel="preload" href="<?= base_url('assets/adminlte/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <link rel="preload" href="<?= base_url('assets/adminlte/plugins/icheck-bootstrap/icheck-bootstrap.min.css') ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <link rel="preload" href="<?= base_url('assets/adminlte/plugins/jqvmap/jqvmap.min.css') ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <link rel="preload" href="<?= base_url('assets/adminlte/plugins/overlayScrollbars/css/OverlayScrollbars.min.css') ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <link rel="preload" href="<?= base_url('assets/adminlte/plugins/daterangepicker/daterangepicker.css') ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <link rel="preload" href="<?= base_url('assets/adminlte/plugins/summernote/summernote-bs4.min.css') ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
    
    <!-- DataTables CSS - Deferred -->
    <link rel="preload" href="<?= base_url('assets/adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <link rel="preload" href="<?= base_url('assets/adminlte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <link rel="preload" href="<?= base_url('assets/adminlte/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
    
    <!-- External Libraries - Deferred -->
    <link rel="preload" href="<?= base_url('assets/adminlte/plugins/toastr/toastr.min.css') ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <link rel="preload" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <link rel="preload" href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript>
        <link rel="stylesheet" href="<?= base_url('assets/adminlte/plugins/toastr/toastr.min.css') ?>">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css">
    </noscript>

    <style>
        /* Only dynamic inline CSS that depends on PHP role remains */
        :root {
            --role-primary: <?= session()->get('role') === 'admin' ? '#e94560' : '#27ae60' ?>;
            --role-dark:    <?= session()->get('role') === 'admin' ? '#c0392b' : '#1e8449' ?>;
        }
    </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

    <?= $this->include('theme/admin/navbar') ?>

    <?php
    $role = strtolower(session()->get('role') ?? 'staff');
    if ($role === 'admin') {
        echo $this->include('theme/admin/sidebar');
    } else {
        echo $this->include('theme/staff/sidebar');
    }
    ?>

    <?= $this->renderSection('content') ?>

    <footer class="main-footer no-print">
        <strong>Copyright &copy; <?= date('Y') ?> <a href="#">Glenn IT Solutions</a></strong>
        All rights reserved.
        <div class="float-right d-none d-sm-inline-block">
            <b>Version</b> CI4.v1 &nbsp;|&nbsp;
            <span style="color: var(--role-primary); font-weight: 700; text-transform: uppercase; font-size: 11px;">
                <?= esc(strtoupper($role)) ?>
            </span>
        </div>
    </footer>

    <aside class="control-sidebar control-sidebar-dark"></aside>
</div>

<!-- CRITICAL: jQuery & Bootstrap (required for page interactivity) -->
<script src="<?= base_url('assets/adminlte/plugins/jquery/jquery.min.js') ?>"></script>
<script src="<?= base_url('assets/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>

<!-- CRITICAL: jQuery-UI & Dependencies (dashboard.js needs these) -->
<script src="<?= base_url('assets/adminlte/plugins/jquery-ui/jquery-ui.min.js') ?>"></script>
<script>$.widget.bridge('uibutton', $.ui.button)</script>
<script src="<?= base_url('assets/adminlte/plugins/chart.js/Chart.min.js') ?>"></script>
<script src="<?= base_url('assets/adminlte/plugins/sparklines/sparkline.js') ?>"></script>
<script src="<?= base_url('assets/adminlte/plugins/moment/moment.min.js') ?>"></script>

<!-- IMPORTANT: JQVMap must load BEFORE dashboard.js (which uses it to render charts) -->
<script src="<?= base_url('assets/adminlte/plugins/jqvmap/jquery.vmap.min.js') ?>"></script>
<script src="<?= base_url('assets/adminlte/plugins/jqvmap/maps/jquery.vmap.usa.js') ?>"></script>

<!-- DEFERRED: Non-Critical Libraries (can load after page paint) -->
<script defer src="<?= base_url('assets/adminlte/plugins/jquery-knob/jquery.knob.min.js') ?>"></script>
<script defer src="<?= base_url('assets/adminlte/plugins/daterangepicker/daterangepicker.js') ?>"></script>
<script defer src="<?= base_url('assets/adminlte/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') ?>"></script>
<script defer src="<?= base_url('assets/adminlte/plugins/summernote/summernote-bs4.min.js') ?>"></script>
<script defer src="<?= base_url('assets/adminlte/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') ?>"></script>
<script defer src="<?= base_url('assets/adminlte/dist/js/adminlte.js') ?>"></script>
<script defer src="<?= base_url('assets/adminlte/dist/js/pages/dashboard.js') ?>"></script>

<!-- DataTables Core (synchronous for table pages) -->
<script src="<?= base_url('assets/adminlte/plugins/datatables/jquery.dataTables.min.js') ?>"></script>
<script src="<?= base_url('assets/adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') ?>"></script>

<!-- DataTables Extensions (deferred - load after core) -->
<script defer src="<?= base_url('assets/adminlte/plugins/datatables-responsive/js/dataTables.responsive.min.js') ?>"></script>
<script defer src="<?= base_url('assets/adminlte/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') ?>"></script>
<script defer src="<?= base_url('assets/adminlte/plugins/datatables-buttons/js/dataTables.buttons.min.js') ?>"></script>
<script defer src="<?= base_url('assets/adminlte/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') ?>"></script>
<script defer src="<?= base_url('assets/adminlte/plugins/jszip/jszip.min.js') ?>"></script>
<script defer src="<?= base_url('assets/adminlte/plugins/pdfmake/pdfmake.min.js') ?>"></script>
<script defer src="<?= base_url('assets/adminlte/plugins/pdfmake/vfs_fonts.js') ?>"></script>
<script defer src="<?= base_url('assets/adminlte/plugins/datatables-buttons/js/buttons.html5.min.js') ?>"></script>
<script defer src="<?= base_url('assets/adminlte/plugins/datatables-buttons/js/buttons.print.min.js') ?>"></script>
<script defer src="<?= base_url('assets/adminlte/plugins/datatables-buttons/js/buttons.colVis.min.js') ?>"></script>

<!-- External Libraries (deferred) -->
<script defer src="<?= base_url('assets/adminlte/plugins/toastr/toastr.min.js') ?>"></script>
<script defer src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script defer src="<?= base_url('js/blotter/notifications.js') ?>"></script>

<?= $this->renderSection('scripts') ?>

<script>
const themeToggle = document.getElementById('themeToggle');

function applyTheme(mode) {
    if (mode === 'dark') {
        document.body.classList.add('dark-mode');
        if (themeToggle) themeToggle.innerHTML = '<i class="fas fa-moon"></i>';
    } else {
        document.body.classList.remove('dark-mode');
        if (themeToggle) themeToggle.innerHTML = '<i class="fas fa-sun"></i>';
    }
}

applyTheme(localStorage.getItem('adminlteTheme') || 'light');

if (themeToggle) {
    themeToggle.addEventListener('click', function(e) {
        e.preventDefault();
        const newTheme = document.body.classList.contains('dark-mode') ? 'light' : 'dark';
        applyTheme(newTheme);
        localStorage.setItem('adminlteTheme', newTheme);
    });
}

$(document).ajaxSend(function(e, xhr, options) {
    if (options.type && options.type.toUpperCase() === 'POST') {
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    }
});

$(document).on('submit', 'form', function() {
    const $form = $(this);
    if ($form.data('submitted') === true) return false;
    $form.data('submitted', true);
});
</script>
</body>
</html>
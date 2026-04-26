<!DOCTYPE html>
<html lang="en" style="font-size: 14px;">
<head>
    <meta name="csrf-token" content="<?= csrf_hash() ?>">
    <meta name="csrf-name"  content="<?= csrf_token() ?>">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BMIS | <?= isset($title) ? esc($title) : 'Dashboard' ?></title>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="<?= base_url('assets/adminlte/plugins/fontawesome-free/css/all.min.css') ?>">
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/adminlte/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/adminlte/plugins/icheck-bootstrap/icheck-bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/adminlte/plugins/jqvmap/jqvmap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/adminlte/dist/css/adminlte.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/adminlte/plugins/overlayScrollbars/css/OverlayScrollbars.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/adminlte/plugins/daterangepicker/daterangepicker.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/adminlte/plugins/summernote/summernote-bs4.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/adminlte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/adminlte/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/adminlte/plugins/toastr/toastr.min.css') ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/css/custom.css') ?>">

    <style>
        :root {
            --role-primary: <?= session()->get('role') === 'admin' ? '#e94560' : '#27ae60' ?>;
            --role-dark:    <?= session()->get('role') === 'admin' ? '#c0392b' : '#1e8449' ?>;
        }

        /* ── Layout ── */
        .content-wrapper {
            background: #f0f2f8 !important;
            transition: background 0.3s ease;
        }
        .main-footer {
            background: #0d0d1a;
            color: #a8b2c8;
            border-top: 2px solid var(--role-primary);
            font-size: 12px;
        }
        .main-footer a { color: var(--role-primary); }

        /* ── Cards ── */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.07);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .card:hover { transform: translateY(-2px); box-shadow: 0 6px 25px rgba(0,0,0,0.11); }
        .card-header {
            border-radius: 12px 12px 0 0 !important;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            font-weight: 600;
            background: #fff;
        }

        /* ── Small boxes ── */
        .small-box { border-radius: 12px; overflow: hidden; }

        /* ── Breadcrumb ── */
        .content-header h1 { font-weight: 700; color: #1a1a2e; }
        .breadcrumb { background: transparent; }

        /* ── Navbar accent ── */
        .main-header.navbar { border-bottom: 2px solid var(--role-primary) !important; }

        /* ── Dark Mode ── */
        body.dark-mode .content-wrapper  { background: #0f0f1a !important; color: #e0e0e0; }
        body.dark-mode .card             { background: #1a1a2e; border: 1px solid #2a2a4e; color: #e0e0e0; }
        body.dark-mode .card-header      { background: #16213e; color: #e0e0e0; }
        body.dark-mode .table            { color: #e0e0e0; }
        body.dark-mode .table thead th   { background: #16213e; border-color: #2a2a4e; color: #a8b2c8; }
        body.dark-mode .table td,
        body.dark-mode .table th         { border-color: #2a2a4e; }
        body.dark-mode .form-control     { background: #16213e; border-color: #2a2a4e; color: #e0e0e0; }
        body.dark-mode .form-control:focus { background: #1a1a2e; color: #fff; }
        body.dark-mode .modal-content    { background: #1a1a2e; color: #e0e0e0; }
        body.dark-mode .modal-header,
        body.dark-mode .modal-footer     { border-color: #2a2a4e; }
        body.dark-mode .content-header h1 { color: #e0e0e0; }
        body.dark-mode .main-footer      { background: #0a0a14; }
        body.dark-mode .small-box        { filter: brightness(0.85); }
        body.dark-mode .text-muted       { color: #6c7a9c !important; }
        body.dark-mode .card-header      { background: #16213e !important; }
    </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

    <?= $this->include('theme/admin/navbar') ?>

    <?php
    /*
     * ── SIDEBAR ROLE ROUTER ───────────────────────────────────────────────────
     * This is the fix: load the correct sidebar based on the session role.
     * Admin  → theme/admin/sidebar   (red theme, User Accounts link)
     * Staff  → theme/staff/sidebar   (green theme, no User Accounts link)
     * ─────────────────────────────────────────────────────────────────────────
     */
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

<!-- Scripts -->
<script src="<?= base_url('assets/adminlte/plugins/jquery/jquery.min.js') ?>"></script>
<script src="<?= base_url('assets/adminlte/plugins/jquery-ui/jquery-ui.min.js') ?>"></script>
<script>$.widget.bridge('uibutton', $.ui.button)</script>
<script src="<?= base_url('assets/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
<script src="<?= base_url('assets/adminlte/plugins/chart.js/Chart.min.js') ?>"></script>
<script src="<?= base_url('assets/adminlte/plugins/sparklines/sparkline.js') ?>"></script>
<script src="<?= base_url('assets/adminlte/plugins/jqvmap/jquery.vmap.min.js') ?>"></script>
<script src="<?= base_url('assets/adminlte/plugins/jquery-knob/jquery.knob.min.js') ?>"></script>
<script src="<?= base_url('assets/adminlte/plugins/moment/moment.min.js') ?>"></script>
<script src="<?= base_url('assets/adminlte/plugins/daterangepicker/daterangepicker.js') ?>"></script>
<script src="<?= base_url('assets/adminlte/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') ?>"></script>
<script src="<?= base_url('assets/adminlte/plugins/summernote/summernote-bs4.min.js') ?>"></script>
<script src="<?= base_url('assets/adminlte/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') ?>"></script>
<script src="<?= base_url('assets/adminlte/dist/js/adminlte.js') ?>"></script>
<script src="<?= base_url('assets/adminlte/dist/js/pages/dashboard.js') ?>"></script>
<script src="<?= base_url('assets/adminlte/plugins/datatables/jquery.dataTables.min.js') ?>"></script>
<script src="<?= base_url('assets/adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') ?>"></script>
<script src="<?= base_url('assets/adminlte/plugins/datatables-responsive/js/dataTables.responsive.min.js') ?>"></script>
<script src="<?= base_url('assets/adminlte/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') ?>"></script>
<script src="<?= base_url('assets/adminlte/plugins/datatables-buttons/js/dataTables.buttons.min.js') ?>"></script>
<script src="<?= base_url('assets/adminlte/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') ?>"></script>
<script src="<?= base_url('assets/adminlte/plugins/jszip/jszip.min.js') ?>"></script>
<script src="<?= base_url('assets/adminlte/plugins/pdfmake/pdfmake.min.js') ?>"></script>
<script src="<?= base_url('assets/adminlte/plugins/pdfmake/vfs_fonts.js') ?>"></script>
<script src="<?= base_url('assets/adminlte/plugins/datatables-buttons/js/buttons.html5.min.js') ?>"></script>
<script src="<?= base_url('assets/adminlte/plugins/datatables-buttons/js/buttons.print.min.js') ?>"></script>
<script src="<?= base_url('assets/adminlte/plugins/datatables-buttons/js/buttons.colVis.min.js') ?>"></script>
<script src="<?= base_url('assets/adminlte/plugins/toastr/toastr.min.js') ?>"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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

// Auto-attach CSRF to all AJAX POST requests
$(document).ajaxSend(function(e, xhr, options) {
    if (options.type && options.type.toUpperCase() === 'POST') {
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    }
});

// Prevent double-submit
$(document).on('submit', 'form', function() {
    const $form = $(this);
    if ($form.data('submitted') === true) return false;
    $form.data('submitted', true);
});
</script>
</body>
</html>
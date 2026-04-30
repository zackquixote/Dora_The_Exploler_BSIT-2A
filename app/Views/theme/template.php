<!DOCTYPE html>
<html lang="en" style="font-size: 14px;">
<head>
    <meta name="csrf-token" content="<?= csrf_hash() ?>">
    <meta name="csrf-name" content="<?= csrf_token() ?>">
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

<link rel="stylesheet" href="<?= base_url('assets/css/user/user.css') ?>">
<link rel="stylesheet" href="<?= base_url('asset/css/blotter/blotter.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/css/resident/residents-create.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/css/resident/residents-index.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/css/resident/residents-view.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/css/dashboard/style.css') ?>">


    <style>
        .content-wrapper { background: #f0f7f2 !important; transition: all 0.3s ease; }
        .main-footer { background: #1a3c2e; color: #a8d5b5; border-top: 2px solid #27ae60; }
        .main-footer a { color: #27ae60; }

        .card { border: none; border-radius: 12px; box-shadow: 0 2px 15px rgba(0,0,0,0.08); transition: transform 0.2s ease, box-shadow 0.2s ease; }
        .card:hover { transform: translateY(-2px); box-shadow: 0 6px 25px rgba(0,0,0,0.12); }
        .card-header { border-radius: 12px 12px 0 0 !important; border-bottom: 1px solid rgba(0,0,0,0.06); font-weight: 600; }
        .small-box { border-radius: 12px; overflow: hidden; }
        .content-header h1 { font-weight: 700; color: #1a3c2e; }
        .breadcrumb { background: transparent; }

        /* Dark Mode */
        body.dark-mode .content-wrapper { background: #0a1f14 !important; color: #e0e0e0; }
        body.dark-mode .card { background: #1a3c2e; border: 1px solid #2a5a3e; color: #e0e0e0; }
        body.dark-mode .card-header { background: #145a32; color: #e0e0e0; }
        body.dark-mode .table { color: #e0e0e0; }
        body.dark-mode .table thead th { background: #145a32; border-color: #2a5a3e; color: #a8d5b5; }
        body.dark-mode .table td, body.dark-mode .table th { border-color: #2a5a3e; }
        body.dark-mode .form-control { background: #145a32; border-color: #2a5a3e; color: #e0e0e0; }
        body.dark-mode .form-control:focus { background: #1a3c2e; color: #fff; }
        body.dark-mode .modal-content { background: #1a3c2e; color: #e0e0e0; }
        body.dark-mode .modal-header, body.dark-mode .modal-footer { border-color: #2a5a3e; }
        body.dark-mode .content-header h1 { color: #e0e0e0; }
        body.dark-mode .main-footer { background: #0a1f14; }
        body.dark-mode .text-muted { color: #5a9a6a !important; }
    </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

    <?= $this->include('theme/navbar') ?>
    <?= $this->include('theme/sidebar') ?>

    <?= $this->renderSection('content') ?>

    <footer class="main-footer no-print">
        <strong>Copyright &copy; 2025 <a href="#">Glenn IT Solutions</a></strong>
        All rights reserved.
        <div class="float-right d-none d-sm-inline-block">
            <b>Version</b> CI4.v1
        </div>
    </footer>

    <aside class="control-sidebar control-sidebar-dark"></aside>
</div>

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
        let newTheme = document.body.classList.contains('dark-mode') ? 'light' : 'dark';
        applyTheme(newTheme);
        localStorage.setItem('adminlteTheme', newTheme);
    });
}

$(document).ajaxSend(function(e, xhr, options) {
    if (options.type.toUpperCase() === 'POST') {
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    }
});

$(document).on('submit', 'form', function() {
    var $form = $(this);
    if ($form.data('submitted') === true) return false;
    $form.data('submitted', true);
});
</script>
</body>
</html>
<!DOCTYPE html>
<html lang="en" style="font-size: 14px;">
<head>
    <meta name="csrf-token" content="<?= csrf_hash() ?>">
    <meta name="csrf-name" content="<?= csrf_token() ?>">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BMIS | <?= isset($title) ? esc($title) : 'Dashboard' ?></title>

    <!-- Fonts & Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="<?= base_url('assets/adminlte/plugins/fontawesome-free/css/all.min.css') ?>">
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    
    <!-- AdminLTE Plugins -->
    <link rel="stylesheet" href="<?= base_url('assets/adminlte/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/adminlte/plugins/icheck-bootstrap/icheck-bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/adminlte/plugins/jqvmap/jqvmap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/adminlte/dist/css/adminlte.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/adminlte/plugins/overlayScrollbars/css/OverlayScrollbars.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/adminlte/plugins/daterangepicker/daterangepicker.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/adminlte/plugins/summernote/summernote-bs4.min.css') ?>">
    
    <!-- DataTables -->
    <link rel="stylesheet" href="<?= base_url('assets/adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/adminlte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/adminlte/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') ?>">
    
    <!-- Notifications -->
    <link rel="stylesheet" href="<?= base_url('assets/adminlte/plugins/toastr/toastr.min.css') ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- Custom Theme CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/css/user/user.css') ?>">
    <link rel="stylesheet" href="<?= base_url('asset/css/blotter/blotter.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/resident/residents-create.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/resident/residents-index.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/resident/residents-view.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/dashboard/style.css') ?>">
    
    <!-- TomSelect -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">

    <style>
        /* ============================================================
           Base Layout (Green Theme)
           ============================================================ */
        
        body {
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .content-wrapper {
            background: #f0f7f2 !important; 
            transition: all 0.3s ease;
        }

        /* Footer */
        .main-footer { 
            background: #1a3c2e; 
            color: #a8d5b5; 
            border-top: 2px solid #27ae60; 
            padding: 20px;
            font-size: 0.9rem;
        }

        .main-footer a { 
            color: #27ae60; 
            font-weight: 600;
            transition: color 0.2s;
        }

        .main-footer a:hover {
            color: #2ecc71;
            text-decoration: underline;
        }

        /* Cards */
        .card { 
            border: none; 
            border-radius: 12px; 
            box-shadow: 0 4px 20px rgba(0,0,0,0.05); 
            transition: transform 0.2s cubic-bezier(0.165, 0.84, 0.44, 1), box-shadow 0.2s ease; 
        }

        .card:hover { 
            transform: translateY(-3px); 
            box-shadow: 0 8px 25px rgba(0,0,0,0.1); 
        }

        .card-header { 
            border-radius: 12px 12px 0 0 !important; 
            border-bottom: 1px solid rgba(0,0,0,0.05); 
            font-weight: 700; 
            background: #fff;
            padding: 1rem 1.25rem;
        }

        /* Small Boxes */
        .small-box { 
            border-radius: 12px; 
            overflow: hidden; 
            transition: transform 0.2s ease;
        }
        
        .small-box:hover {
            transform: scale(1.01);
        }

        /* Content Header */
        .content-header h1 { 
            font-weight: 800; 
            color: #1a3c2e; 
            letter-spacing: -0.5px;
        }

        .breadcrumb { 
            background: transparent; 
            font-size: 0.9rem;
        }

        .breadcrumb-item a {
            color: #1a3c2e;
        }

        /* Form Controls */
        .form-control {
            border-radius: 8px;
            border-color: #e0e0e0;
            transition: border-color 0.2s;
        }

        .form-control:focus {
            border-color: #27ae60;
            box-shadow: 0 0 0 3px rgba(39, 174, 96, 0.1);
        }

        /* ============================================================
           Dark Mode (Green Theme)
           ============================================================ */
        
        body.dark-mode {
            background-color: #0a1f14;
        }

        body.dark-mode .content-wrapper { 
            background: #0a1f14 !important; 
            color: #e0e0e0; 
        }

        body.dark-mode .card { 
            background: #145a32; 
            border: 1px solid #1e8449; 
            color: #e0e0e0; 
        }

        body.dark-mode .card-header { 
            background: #117a65; 
            color: #ffffff; 
            border-bottom: 1px solid #1e8449;
        }

        body.dark-mode .table { 
            color: #e0e0e0; 
        }

        body.dark-mode .table thead th { 
            background: #0e3d26; 
            border-color: #1e8449; 
            color: #a9dfbf; 
        }

        body.dark-mode .table td, 
        body.dark-mode .table th { 
            border-color: #1e8449; 
        }

        body.dark-mode .table-hover tbody tr:hover {
            background: rgba(39, 174, 96, 0.1);
        }

        body.dark-mode .form-control { 
            background: #0b3d25; 
            border-color: #1e8449; 
            color: #e0e0e0; 
        }

        body.dark-mode .form-control:focus { 
            background: #0f462b; 
            color: #fff; 
            border-color: #2ecc71;
            box-shadow: 0 0 0 3px rgba(46, 204, 113, 0.25);
        }

        body.dark-mode .form-control::placeholder {
            color: #7ca882;
        }

        body.dark-mode .modal-content { 
            background: #145a32; 
            color: #e0e0e0; 
            border: 1px solid #1e8449;
        }

        body.dark-mode .modal-header, 
        body.dark-mode .modal-footer { 
            border-color: #1e8449; 
        }

        body.dark-mode .content-header h1 { 
            color: #ffffff; 
        }

        body.dark-mode .main-footer { 
            background: #05160d; 
            color: #7ca882;
        }

        body.dark-mode .text-muted { 
            color: #7ca882 !important; 
        }

        body.dark-mode a:not(.btn) {
            color: #2ecc71;
        }
    </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

    <!-- Navbar & Sidebar -->
    <?= $this->include('theme/navbar') ?>
    <?= $this->include('theme/sidebar') ?>

    <!-- Main Content -->
    <?= $this->renderSection('content') ?>

    <!-- Footer -->
    <footer class="main-footer no-print">
        <strong>Copyright &copy; 2025 <a href="#">Glenn IT Solutions</a></strong>
        All rights reserved.
        <div class="float-right d-none d-sm-inline-block">
            <b>Version</b> CI4.v1
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
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

<?= $this->renderSection('scripts') ?>

<script>
// Theme Management
const themeToggle = document.getElementById('themeToggle');

function applyTheme(mode) {
    if (mode === 'dark') {
        document.body.classList.add('dark-mode');
        // Re-init TomSelect for dark mode styling if needed
        updateTomSelectTheme(true);
        if (themeToggle) themeToggle.innerHTML = '<i class="fas fa-moon"></i>';
    } else {
        document.body.classList.remove('dark-mode');
        updateTomSelectTheme(false);
        if (themeToggle) themeToggle.innerHTML = '<i class="fas fa-sun"></i>';
    }
}

// Apply saved theme immediately
applyTheme(localStorage.getItem('adminlteTheme') || 'light');

if (themeToggle) {
    themeToggle.addEventListener('click', function(e) {
        e.preventDefault();
        let newTheme = document.body.classList.contains('dark-mode') ? 'light' : 'dark';
        applyTheme(newTheme);
        localStorage.setItem('adminlteTheme', newTheme);
    });
}

// Global AJAX Setup
 $(document).ajaxSend(function(e, xhr, options) {
    if (options.type.toUpperCase() === 'POST') {
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    }
});

// Prevent Double Form Submission
 $(document).on('submit', 'form', function() {
    var $form = $(this);
    if ($form.data('submitted') === true) return false;
    $form.data('submitted', true);
});

// Tom-Select Helper (Ensure dropdown text is readable in dark mode)
function updateTomSelectTheme(isDark) {
    // This logic assumes TomSelect is initialized via a class like .tom-select
    // You may need to adjust selector based on your implementation
    const tomSelects = document.querySelectorAll('.ts-wrapper');
    tomSelects.forEach(el => {
        if(isDark) {
            el.style.backgroundColor = '#0b3d25';
            el.style.borderColor = '#1e8449';
        } else {
            el.style.backgroundColor = '#ffffff';
            el.style.borderColor = '#e0e0e0';
        }
    });
}
</script>
</body>
</html>
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
    
    <!-- Plugins & AdminLTE CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/adminlte/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/adminlte/plugins/icheck-bootstrap/icheck-bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/adminlte/plugins/jqvmap/jqvmap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/adminlte/dist/css/adminlte.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/adminlte/plugins/overlayScrollbars/css/OverlayScrollbars.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/adminlte/plugins/daterangepicker/daterangepicker.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/adminlte/plugins/summernote/summernote-bs4.min.css') ?>">
    
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/adminlte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/adminlte/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') ?>">
    
    <!-- Toastr & SweetAlert2 -->
    <link rel="stylesheet" href="<?= base_url('assets/adminlte/plugins/toastr/toastr.min.css') ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- CUSTOM CSS (Dark Mode & Transitions) -->
    <style>
        /* Global Transitions */
        .content-wrapper, .main-footer, .main-header {
            transition: all 0.3s ease-in-out;
        }

        /* Dark Mode Overrides */
        body.dark-mode .content-wrapper {
            background-color: #343a40 !important; /* Dark Gray */
            color: #f8f9fa;
        }
        
        body.dark-mode .card {
            background-color: #4b545c;
            border-color: #6c757d;
            color: #fff;
        }
        
        body.dark-mode .table {
            color: #fff;
        }
        body.dark-mode .table thead th {
            background-color: #3b4249;
            border-color: #5c636a;
        }
        body.dark-mode .table td, body.dark-mode .table th {
            border-color: #5c636a;
        }
        body.dark-mode .text-muted {
            color: #ced4da !important;
        }
        body.dark-mode .small {
            color: #ced4da;
        }

        /* Sidebar Specific Fixes for Dark Mode */
        body.dark-mode .sidebar {
            background-color: #343a40 !important;
        }
        
        /* Sidebar Dark Mode (Primary) */
        body.dark-mode .sidebar-dark-primary .nav-sidebar > .nav-item > .nav-link.active {
            background-color: #007bff !important; /* Blue active state */
            color: #fff !important;
        }
        body.dark-mode .sidebar-dark-primary .nav-sidebar > .nav-item > .nav-link {
            color: #c2c7d0 !important; /* Light gray text */
        }
        body.dark-mode .sidebar-dark-primary .nav-sidebar > .nav-item > .nav-link:hover {
            background-color: rgba(255,255,255,0.1);
        }
        body.dark-mode .sidebar-dark-primary .brand-text {
            color: #fff !important;
        }

        /* Sidebar Light Mode */
        body:not(.dark-mode) .sidebar-light .nav-sidebar > .nav-item > .nav-link.active {
            background-color: #007bff !important;
            color: #fff !important;
        }
        body:not(.dark-mode) .sidebar-light .nav-sidebar > .nav-item > .nav-link {
            color: #495057 !important;
        }
        body:not(.dark-mode) .sidebar-light .brand-link {
            color: #1f2d3d !important;
        }

        /* Form Elements in Dark Mode */
        body.dark-mode .form-control {
            background-color: #3b4249;
            border-color: #5c636a;
            color: #fff;
        }
        body.dark-mode .form-control:focus {
            background-color: #4b545c;
            color: #fff;
        }
        body.dark-mode .dropdown-menu {
            background-color: #343a40;
            border-color: #4b545c;
            color: #fff;
        }
        body.dark-mode .dropdown-item {
            color: #e9ecef;
        }
        body.dark-mode .dropdown-item:hover {
            background-color: #007bff;
            color: #fff;
        }
        body.dark-mode .modal-content {
            background-color: #4b545c;
            color: #fff;
        }
        body.dark-mode .modal-header, body.dark-mode .modal-footer {
            border-color: #5c636a;
        }
    </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
  <!-- Preloader (Optional) -->
  <!-- <div class="preloader flex-column justify-content-center align-items-center">
    <img class="animation__shake" src="<?= base_url('assets/adminlte/dist/img/AdminLTELogo.png') ?>" alt="AdminLTELogo" height="60" width="60">
  </div> -->

  <!-- Navbar -->
  <?= $this->include('theme/navbar') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/custom.css') ?>">
  <!-- Sidebar -->
  <?= $this->include('theme/sidebar') ?>

  <!-- Main Content -->
  <?= $this->renderSection('content') ?>

  <!-- Footer -->
  <footer class="main-footer no-print">
    <strong>Copyright &copy; 2025 <a href="#">Glenn IT Solutions</a> </strong>
    All rights reserved.
    <div class="float-right d-none d-sm-inline-block">
      <b>Version</b> CI4.v1
    </div>
  </footer>

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <div class="p-3">
      <h5>Settings</h5>
      <hr>
      <div class="form-group">
        <label>Option 1</label>
        <input type="checkbox" class="form-control">
      </div>
      <div class="form-group">
        <label>Option 2</label>
        <input type="checkbox" class="form-control">
      </div>
    </div>
  </aside>
</div>

<!-- ================= SCRIPTS ================= -->

<!-- jQuery & Dependencies -->
<script src="<?= base_url('assets/adminlte/plugins/jquery/jquery.min.js') ?>"></script>
<script src="<?= base_url('assets/adminlte/plugins/jquery-ui/jquery-ui.min.js') ?>"></script>
<script>
  $.widget.bridge('uibutton', $.ui.button)
</script>
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

<!-- DataTables & Plugins -->
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

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Page Specific Scripts -->
<?= $this->renderSection('scripts') ?>

<!-- Global Script (Theme Toggle & AJAX Setup) -->
<script>
// 1. Theme Toggle Logic
const themeToggle = document.getElementById('themeToggle');
const navbar = document.getElementById('mainNavbar');
const sidebar = document.getElementById('mainSidebar');
const brandLink = document.getElementById('brandLink');

function applyTheme(mode) {
    if(mode === 'dark'){
        document.body.classList.add('dark-mode');
        // Navbar
        navbar.classList.remove('navbar-warning', 'navbar-light');
        navbar.classList.add('navbar-dark', 'bg-dark');
        
        // Sidebar
        sidebar.classList.remove('sidebar-light');
        sidebar.classList.add('sidebar-dark-primary');
        
        // Brand link
        brandLink.classList.remove('bg-warning');
        brandLink.classList.add('bg-dark');
        
        if(themeToggle) themeToggle.innerHTML = '<i class="fas fa-moon"></i>';
    } else {
        // Light mode
        document.body.classList.remove('dark-mode');
        // Navbar
        navbar.classList.remove('navbar-dark', 'bg-dark');
        navbar.classList.add('navbar-warning', 'navbar-light');
        
        // Sidebar
        sidebar.classList.remove('sidebar-dark-primary');
        sidebar.classList.add('sidebar-light');
        
        // Brand link
        brandLink.classList.remove('bg-dark');
        brandLink.classList.add('bg-warning');
        
        if(themeToggle) themeToggle.innerHTML = '<i class="fas fa-sun"></i>';
    }
}

// Apply saved theme on load
let savedTheme = localStorage.getItem('adminlteTheme');
if(savedTheme) {
    applyTheme(savedTheme);
} else {
    // Default to light if nothing saved
    applyTheme('light');
}

// Toggle event listener
if(themeToggle) {
    themeToggle.addEventListener('click', function(e){
        e.preventDefault();
        let isDark = document.body.classList.contains('dark-mode');
        let newTheme = isDark ? 'light' : 'dark';
        
        applyTheme(newTheme);
        localStorage.setItem('adminlteTheme', newTheme);
    });
}

// 2. Global AJAX Setup (CSRF)
 $(document).ajaxSend(function(e, xhr, options) {
    if (options.type.toUpperCase() === 'POST') {
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    }
});

// 3. Global Form Submission Intercept (prevent double submit)
 $(document).on('submit', 'form', function() {
    var $form = $(this);
    if($form.data('submitted') === true) {
        // Previously submitted - don't submit again
        return false;
    }
    // Mark it so that the next submit can be ignored
    $form.data('submitted', true);
    
    // Reset if form validation fails (optional implementation depends on validation library)
});
</script>

</body>
</html>
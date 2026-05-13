<head>
    <meta name="csrf-token" content="<?= csrf_hash() ?>">
    <meta name="csrf-name"  content="<?= csrf_token() ?>">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BMIS | <?= isset($title) ? esc($title) : 'Dashboard' ?></title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?= base_url('favicon.ico') ?>">
    <link rel="shortcut icon" type="image/x-icon" href="<?= base_url('favicon.ico') ?>">

    <!-- Design System -->
    <link rel="stylesheet" href="<?= base_url('assets/css/bmis-design-system.css') ?>">

    <!-- AdminLTE (kept for Bootstrap grid/utilities + DataTables compat) -->
    <link rel="stylesheet" href="<?= base_url('assets/adminlte/dist/css/adminlte.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/adminlte/plugins/fontawesome-free/css/all.min.css') ?>">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/adminlte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') ?>">

    <!-- External CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/adminlte/plugins/toastr/toastr.min.css') ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- Override AdminLTE defaults with design system -->
    <style>
        .main-sidebar, .main-header, .content-wrapper, .main-footer { display:none !important; }
        body { overflow-x: hidden; }
    </style>
</head>

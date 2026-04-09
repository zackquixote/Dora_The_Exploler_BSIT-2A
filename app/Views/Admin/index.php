<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>BMIS | Admin Dashboard</title>
  <link rel="stylesheet" href="<?= base_url('assets/adminlte/plugins/fontawesome-free/css/all.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/adminlte/dist/css/adminlte.min.css') ?>">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-dark navbar-dark" style="background:#343a40;">
    <ul class="navbar-nav ml-auto">
      <li class="nav-item">
        <span class="nav-link">Welcome, <?= session()->get('name') ?> (Admin)</span>
      </li>
      <li class="nav-item">
        <a href="<?= base_url('/logout') ?>" class="nav-link">
          <i class="fas fa-sign-out-alt"></i> Logout
        </a>
      </li>
    </ul>
  </nav>

  <!-- Sidebar -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="#" class="brand-link text-center">
      <span class="brand-text font-weight-light">BMIS Admin</span>
    </a>
    <div class="sidebar">
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column">
          <li class="nav-item">
            <a href="<?= base_url('/admin/dashboard') ?>" class="nav-link active">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>Dashboard</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="<?= base_url('/users') ?>" class="nav-link">
              <i class="nav-icon fas fa-users"></i>
              <p>Users</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="<?= base_url('/person') ?>" class="nav-link">
              <i class="nav-icon fas fa-id-card"></i>
              <p>Person Records</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="<?= base_url('/log') ?>" class="nav-link">
              <i class="nav-icon fas fa-list"></i>
              <p>Logs</p>
            </a>
          </li>
        </ul>
      </nav>
    </div>
  </aside>

  <!-- Content -->
  <div class="content-wrapper" style="background:#f4f6f9;">
    <div class="content-header">
      <div class="container-fluid">
        <h1 class="m-0">Admin Dashboard</h1>
      </div>
    </div>

    <div class="content">
      <div class="container-fluid">

        <!-- Stats Cards -->
        <div class="row">
          <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
              <div class="inner"><h3>--</h3><p>Total Users</p></div>
              <div class="icon"><i class="fas fa-users"></i></div>
            </div>
          </div>
          <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
              <div class="inner"><h3>--</h3><p>Person Records</p></div>
              <div class="icon"><i class="fas fa-id-card"></i></div>
            </div>
          </div>
          <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
              <div class="inner"><h3>--</h3><p>Login Attempts</p></div>
              <div class="icon"><i class="fas fa-exclamation-triangle"></i></div>
            </div>
          </div>
          <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
              <div class="inner"><h3>--</h3><p>Logs Today</p></div>
              <div class="icon"><i class="fas fa-list"></i></div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>

  <footer class="main-footer text-center">
    <strong>BMIS &copy; <?= date('Y') ?></strong>
  </footer>
</div>

<script src="<?= base_url('assets/adminlte/plugins/jquery/jquery.min.js') ?>"></script>
<script src="<?= base_url('assets/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
<script src="<?= base_url('assets/adminlte/dist/js/adminlte.min.js') ?>"></script>
</body>
</html>
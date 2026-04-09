<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>BMIS | Staff Dashboard</title>
  <link rel="stylesheet" href="<?= base_url('assets/adminlte/plugins/fontawesome-free/css/all.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/adminlte/dist/css/adminlte.min.css') ?>">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">

  <?= view('theme/navbar') ?>
  <?= view('theme/sidebar') ?>

  <div class="content-wrapper" style="background:#f4f6f9;">
    <div class="content-header">
      <div class="container-fluid">
        <h1 class="m-0">Staff Dashboard</h1>
      </div>
    </div>
    <div class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-lg-4 col-6">
            <div class="small-box bg-success">
              <div class="inner"><h3>--</h3><p>Person Records</p></div>
              <div class="icon"><i class="fas fa-id-card"></i></div>
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
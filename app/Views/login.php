<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title> Gov Portal</title>

  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="<?= base_url('assets/adminlte/plugins/fontawesome-free/css/all.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/adminlte/plugins/icheck-bootstrap/icheck-bootstrap.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/adminlte/dist/css/adminlte.min.css') ?>">

  <style>
    :root {
      --pasig-blue: #003893;
      --glass-white: rgba(255, 255, 255, 0.85); /* Increased opacity for black text contrast */
      --glass-border: rgba(255, 255, 255, 0.5);
    }

    body.login-page {
      height: 100vh;
      margin: 0;
      overflow: hidden;
      display: flex;
      align-items: center;
      justify-content: center;
      background: #000;
      font-family: 'Source Sans Pro', sans-serif;
    }

    .bg-image {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-image: url("<?= base_url('assets/img/wowow.jpg') ?>");
      background-size: cover;
      background-position: center;
      z-index: 1;
      filter: brightness(0.6);
      animation: zoomBg 20s infinite alternate;
    }

    @keyframes zoomBg {
      from { transform: scale(1); }
      to { transform: scale(1.1); }
    }

    .login-box {
      width: 450px;
      z-index: 2;
      animation: fadeIn 0.8s ease-out forwards;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .card {
      background: var(--glass-white) !important;
      backdrop-filter: blur(15px);
      -webkit-backdrop-filter: blur(15px);
      border: 2px solid var(--glass-border);
      border-radius: 24px;
      box-shadow: 0 25px 50px rgba(0, 0, 0, 0.4);
    }

    .card-header {
      padding: 40px 20px 20px;
      border-bottom: 1px solid rgba(0, 0, 0, 0.1);
      text-align: center;
    }

    .brand-logo {
      height: 130px;
      width: auto;
      margin-bottom: 20px;
      animation: float 4s ease-in-out infinite;
    }

    @keyframes float {
      0%, 100% { transform: translateY(0); }
      50% { transform: translateY(-10px); }
    }

    /* Primary Bold Black Headers */
    .card-header h5 {
      color: #000000 !important;
      font-weight: 900 !important; /* Extra Bold */
      letter-spacing: 1px;
      margin-bottom: 5px;
    }

    .card-header small {
      color: #000000 !important;
      font-weight: 800 !important;
      text-transform: uppercase;
      letter-spacing: 1px;
      display: block;
    }

    .login-box-msg {
      color: #000000 !important;
      font-weight: 700 !important;
    }

    /* Bold Labels */
    .form-group label {
      color: #000000 !important;
      font-weight: 800 !important;
      margin-left: 5px;
      text-transform: uppercase;
      font-size: 0.75rem;
    }

    .input-group {
      background: #fff;
      border-radius: 12px;
      border: 2px solid #000;
      overflow: hidden;
    }

    .form-control {
      background: transparent !important;
      border: none !important;
      height: 50px;
      color: #000 !important;
      font-weight: 600;
    }

    .input-group-text {
      background: transparent !important;
      border: none !important;
      color: #000 !important;
    }

    .btn-primary {
      background: #000000 !important;
      border: none;
      height: 55px;
      border-radius: 12px;
      font-weight: 800;
      font-size: 1.1rem;
      letter-spacing: 1px;
      transition: all 0.3s;
    }

    .btn-primary:hover:not(:disabled) {
      background: #333 !important;
      transform: translateY(-2px);
    }

    /* Checkbox Bold Black */
    .icheck-primary label {
      color: #000000 !important;
      font-weight: 700 !important;
    }

    /* Footer Bold Black */
    .gov-footer {
      position: fixed;
      bottom: 30px;
      z-index: 2;
      text-align: center;
      width: 100%;
      color: #000000 !important;
      font-weight: 800 !important;
      font-size: 0.95rem;
      text-shadow: 0 0 10px rgba(255,255,255,0.5); /* Helps visibility against dark BG */
    }

    .gov-footer a {
      color: #000000 !important;
      font-weight: 900 !important;
      text-decoration: underline;
    }
  </style>
</head>

<body class="hold-transition login-page">

  <div class="bg-image"></div>

  <div class="login-box">
    <div class="card">
      <div class="card-header">
        <img src="<?= base_url('assets/img/tabu.jpg') ?>" alt="Pasig Logo" class="brand-logo">
        <h5>BARANGGAY TABU,ILOG CITY</h5>
        <small>Smart Governance Portal</small>
      </div>

      <div class="card-body p-4">
        <p class="login-box-msg text-center">Authorized Personnel Only</p>

        <?php if (session()->getFlashdata('error')): ?>
          <div class="alert alert-danger font-weight-bold">
            <?= session()->getFlashdata('error') ?>
          </div>
        <?php endif; ?>

        <form action="<?= base_url('/auth') ?>" method="post">
          <?= csrf_field() ?>

          <div class="form-group">
            <label>Administrator Email</label>
            <div class="input-group mb-3">
              <input type="email" name="email" class="form-control" placeholder="admin@Ilogcity.gov.ph" required>
              <div class="input-group-append">
                <div class="input-group-text">
                  <span class="fas fa-user-shield"></span>
                </div>
              </div>
            </div>
          </div>

          <div class="form-group">
            <label>Access Key</label>
            <div class="input-group mb-4">
              <input type="password" name="password" id="passwordField" class="form-control" placeholder="••••••••" required>
              <div class="input-group-append">
                <div class="input-group-text">
                  <span class="fas fa-eye-slash" id="togglePassword" style="cursor:pointer;"></span>
                </div>
              </div>
            </div>
          </div>

          <button type="submit" class="btn btn-primary btn-block mb-4">
            AUTHENTICATE <i class="fas fa-fingerprint ml-2"></i>
          </button>

          <div class="text-center">
            <div class="icheck-primary">
              <input type="checkbox" id="remember" name="remember">
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="gov-footer">
    <div>&copy; 2026 <strong>BARANGGAY TABU,ILOG CITY</strong> Management Information Systems Office.</div>
    <div class="mt-2">
      <a href="#">Privacy & Security</a> &bull; <a href="#">Terms of Use</a>
    </div>
  </div>

  <script src="<?= base_url('assets/adminlte/plugins/jquery/jquery.min.js') ?>"></script>
  <script src="<?= base_url('assets/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
  
  <script>
    $('#togglePassword').click(function () {
      const input = $('#passwordField');
      const isPass = input.attr('type') === 'password';
      input.attr('type', isPass ? 'text' : 'password');
      $(this).toggleClass('fa-eye-slash fa-eye');
    });
  </script>

</body>
</html>
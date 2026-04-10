<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>BMIS Pasig City | Official Portal</title>

  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="<?= base_url('assets/adminlte/plugins/fontawesome-free/css/all.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/adminlte/plugins/icheck-bootstrap/icheck-bootstrap.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/adminlte/dist/css/adminlte.min.css') ?>">

  <style>
    body.login-page {
      background-image: url("<?= base_url('assets/img/wowow.jpg') ?>");
      background-repeat: no-repeat;
      background-position: center center;
      background-attachment: fixed;
      background-size: cover;
      height: 100vh;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      position: relative;
      margin: 0;
    }

    body.login-page::before {
      content: "";
      position: absolute;
      inset: 0;
      background: rgba(0, 0, 0, 0.4);
      z-index: 1;
    }

    .login-box {
      width: 420px;
      position: relative;
      z-index: 2;
    }

    .card {
      background: rgba(255, 255, 255, 0.15) !important;
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
      border: 1px solid rgba(255, 255, 255, 0.2);
      border-radius: 15px;
      box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);
    }

    .card-header {
      background: rgba(255, 255, 255, 0.2) !important;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
      text-align: center;
      padding: 20px;
    }

    .card-header h5,
    .card-header small,
    .login-box-msg,
    label,
    .text-muted {
      color: #ffffff !important;
    }

    .login-card-body {
      background: transparent !important;
      padding: 2rem;
    }

    .form-control {
      background: rgba(255, 255, 255, 0.9);
      border: none;
    }

    .btn-primary {
      background-color: #003893;
      border: none;
      height: 45px;
      font-weight: 600;
      transition: all 0.3s ease;
    }

    .gov-footer {
      position: relative;
      z-index: 2;
      margin-top: 20px;
      color: white;
      font-size: 0.85rem;
      text-align: center;
      width: 100%;
    }
  </style>
</head>

<body class="hold-transition login-page">

  <div class="login-box">
    <div class="card card-outline card-primary">
      <div class="card-header">
        <img src="<?= base_url('assets/img/pasig.jpeg') ?>" alt="Logo" style="height: 70px; margin-bottom: 10px;">
        <h5 class="m-0 font-weight-bold text-dark">Barangay Management System</h5>
        <small class="text-muted">Pasig City E-Government Portal</small>
      </div>

      <div class="card-body login-card-body">
        <p class="login-box-msg pb-0">Secure Administrator Access</p>
        <hr>

        <?php $lockoutTime = $lockout ?? 0; ?>

        <?php if ($lockoutTime > 0): ?>
          <div class="alert alert-warning text-center shadow-sm" id="lockout-alert">
            <i class="fas fa-user-lock mb-2 d-block" style="font-size: 1.5rem;"></i>
            <strong>Security Lockout</strong><br>
            <small>Try again in:</small><br>
            <span id="lockout-timer" class="badge badge-dark p-2 mt-1" style="font-size: 1rem;"></span>
          </div>
        <?php elseif (session()->getFlashdata('error')): ?>
          <div class="alert alert-danger shadow-sm">
            <i class="fas fa-exclamation-circle mr-2"></i>
            <?= session()->getFlashdata('error') ?>
          </div>
        <?php endif; ?>

        <form action="<?= base_url('/auth') ?>" method="post">
          <?= csrf_field() ?>

          <div class="form-group">
            <label class="text-muted small">Email Address</label>
            <div class="input-group mb-3">
              <input type="email" name="email" class="form-control" placeholder="Email" required autofocus>
              <div class="input-group-append">
                <div class="input-group-text">
                  <span class="fas fa-envelope"></span>
                </div>
              </div>
            </div>
          </div>

          <div class="form-group">
            <label class="text-muted small">Password</label>
            <div class="input-group mb-3">
              <input type="password" name="password" id="passwordField" class="form-control" placeholder="Password" required>
              <div class="input-group-append">
                <div class="input-group-text">
                  <span class="fas fa-eye pass-toggle" id="togglePassword" style="cursor:pointer;"></span>
                </div>
              </div>
            </div>
          </div>

          <div class="row align-items-center">
            <div class="col-7">
              <div class="icheck-primary">
                <input type="checkbox" id="remember" name="remember">
                <label for="remember" class="text-muted small">Keep me signed in</label>
              </div>
            </div>
            <div class="col-5">
              <button type="submit" class="btn btn-primary btn-block shadow-sm" id="signInBtn"
                <?= ($lockoutTime > 0) ? 'disabled' : '' ?>>
                Sign In <i class="fas fa-arrow-right ml-1"></i>
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="gov-footer">
    <div>&copy; 2026 Pasig City ICT Office. All Rights Reserved.</div>
    <div>
      <a href="#" class="text-white-50 mx-2">Privacy Policy</a> |
      <a href="#" class="text-white-50 mx-2">Terms of Service</a>
    </div>
  </div>

  <script src="<?= base_url('assets/adminlte/plugins/jquery/jquery.min.js') ?>"></script>
  <script src="<?= base_url('assets/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
  <script src="<?= base_url('assets/adminlte/dist/js/adminlte.min.js') ?>"></script>

  <script>
    $('#togglePassword').click(function () {
      const input = $('#passwordField');
      const type = input.attr('type') === 'password' ? 'text' : 'password';
      input.attr('type', type);
      $(this).toggleClass('fa-eye fa-eye-slash');
    });

    <?php if ($lockoutTime > 0): ?>
    let secondsLeft = <?= (int) $lockoutTime ?>;
    const timerDisplay = document.getElementById('lockout-timer');
    const signInBtn = document.getElementById('signInBtn');
    const alertBox = document.getElementById('lockout-alert');

    function updateTimer() {
      if (secondsLeft > 0) {
        const minutes = Math.floor(secondsLeft / 60);
        const seconds = secondsLeft % 60;
        timerDisplay.textContent = `${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;
        secondsLeft--;
        setTimeout(updateTimer, 1000);
      } else {
        signInBtn.disabled = false;
        alertBox.className = 'alert alert-success text-center shadow-sm';
        alertBox.innerHTML = `<i class="fas fa-check-circle d-block mb-1"></i> Ready to try again.`;
      }
    }

    updateTimer();
    <?php endif; ?>
  </script>
</body>
</html> 
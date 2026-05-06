<?php
// Dynamic values from the database (with fallbacks)
$barangay     = $settings['barangay_name'] ?? 'Barangay';
$municipality = $settings['municipality']  ?? 'Municipality';
$province     = $settings['province']      ?? 'Province';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= esc($barangay) ?> · Smart Governance Portal</title>
  
  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600;9..40,700&display=swap" rel="stylesheet">
  
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?= base_url('assets/adminlte/plugins/fontawesome-free/css/all.min.css') ?>">
  
  <!-- Design System -->
  <link rel="stylesheet" href="<?= base_url('assets/css/bmis-design-system.css') ?>">

  <style>
    body {
        margin: 0;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: var(--bg);
        background-image: radial-gradient(circle at 50% 0%, var(--c-blue-bg) 0%, transparent 70%);
        font-family: 'DM Sans', sans-serif;
        color: var(--ink);
    }
    .login-wrapper {
        width: 100%;
        max-width: 400px;
        padding: 20px;
    }
    .logo-container {
        text-align: center;
        margin-bottom: 24px;
    }
    .logo-container img {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        border: 3px solid var(--white);
        box-shadow: var(--shadow);
        margin-bottom: 16px;
        object-fit: cover;
    }
    .portal-title {
        font-size: 20px;
        font-weight: 700;
        color: var(--c-navy);
        margin: 0;
    }
    .portal-subtitle {
        font-size: 13px;
        color: var(--ink-muted);
        margin-top: 4px;
    }
    .flash-error {
        background: var(--c-rose-bg);
        color: var(--c-rose);
        padding: 12px 16px;
        border-radius: var(--r-sm);
        margin-bottom: 20px;
        font-size: 12px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .login-footer {
        text-align: center;
        margin-top: 24px;
        font-size: 11px;
        color: var(--ink-soft);
    }
    .login-footer a {
        color: var(--c-blue);
        text-decoration: none;
    }
  </style>
</head>
<body>

  <div class="login-wrapper">
    <div class="logo-container">
      <img src="<?= base_url('assets/img/tabu.jpg') ?>" alt="Barangay Logo">
      <h1 class="portal-title"><?= esc($barangay) ?>, <?= esc($municipality) ?></h1>
      <div class="portal-subtitle">Smart Governance Portal</div>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
      <div class="flash-error">
        <i class="fas fa-exclamation-triangle"></i>
        <?= session()->getFlashdata('error') ?>
      </div>
    <?php endif; ?>

    <div class="ds-card">
      <form id="loginForm" action="<?= base_url('/auth') ?>" method="post" autocomplete="off">
        <?= csrf_field() ?>
        
        <div class="ds-card-body">
            <div style="margin-bottom:16px">
                <label class="ds-input-label">Administrator Email</label>
                <div style="position:relative">
                    <i class="fas fa-envelope" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--ink-soft);font-size:12px"></i>
                    <input type="email" name="email" class="ds-input" placeholder="admin@example.gov.ph" required autocomplete="username" style="padding-left:36px">
                </div>
            </div>
            
            <div style="margin-bottom:20px">
                <label class="ds-input-label">Access Key</label>
                <div style="position:relative">
                    <i class="fas fa-lock" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--ink-soft);font-size:12px"></i>
                    <input type="password" name="password" id="passwordField" class="ds-input" placeholder="••••••••••••" required autocomplete="current-password" style="padding-left:36px;padding-right:36px">
                    <button type="button" id="togglePwd" style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--ink-soft);cursor:pointer;padding:4px"><i class="fas fa-eye-slash" id="eyeIcon"></i></button>
                </div>
            </div>

            <div style="display:flex;align-items:center;gap:8px;margin-bottom:24px">
                <input type="checkbox" id="remember" name="remember" style="accent-color:var(--c-teal);width:14px;height:14px;cursor:pointer">
                <label for="remember" style="font-size:12px;color:var(--ink-muted);cursor:pointer;user-select:none;margin:0">Keep me signed in</label>
            </div>

            <button type="submit" class="ds-btn" style="width:100%;background:var(--c-teal);color:#fff;justify-content:center;height:40px;font-size:13px">
                Authenticate <i class="fas fa-arrow-right" style="margin-left:6px"></i>
            </button>
        </div>
      </form>
    </div>

    <div class="login-footer">
      &copy; <?= date('Y') ?> &mdash; <?= esc($barangay) ?><br>
      <a href="#">Privacy Policy</a> &nbsp;&middot;&nbsp; <a href="#">Terms of Use</a>
    </div>
  </div>

  <script>
    document.getElementById('togglePwd').addEventListener('click', function() {
        var pwdField = document.getElementById('passwordField');
        var eyeIcon = document.getElementById('eyeIcon');
        if (pwdField.type === 'password') {
            pwdField.type = 'text';
            eyeIcon.classList.remove('fa-eye-slash');
            eyeIcon.classList.add('fa-eye');
        } else {
            pwdField.type = 'password';
            eyeIcon.classList.remove('fa-eye');
            eyeIcon.classList.add('fa-eye-slash');
        }
    });
  </script>
</body>
</html>
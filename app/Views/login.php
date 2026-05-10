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
        background-color: var(--c-navy);
        background-image: linear-gradient(rgba(15, 23, 42, 0.65), rgba(15, 23, 42, 0.85)), url('<?= base_url("assets/img/wowow.jpg") ?>');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        font-family: 'Inter', sans-serif;
        color: #f8fafc;
    }
    .login-wrapper {
        width: 100%;
        max-width: 420px;
        padding: 20px;
        animation: fadeUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        opacity: 0;
        transform: translateY(30px);
    }
    @keyframes fadeUp {
        to { opacity: 1; transform: translateY(0); }
    }
    .ds-card.glass-card {
        background: rgba(15, 23, 42, 0.65) !important;
        backdrop-filter: blur(24px) !important;
        -webkit-backdrop-filter: blur(24px) !important;
        border: 1px solid rgba(255, 255, 255, 0.15) !important;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5) !important;
    }
    .glass-card .ds-input-label {
        color: rgba(255, 255, 255, 0.85) !important;
    }
    .glass-card .ds-input {
        background: rgba(255, 255, 255, 0.08) !important;
        border: 1px solid rgba(255, 255, 255, 0.15) !important;
        color: #fff !important;
    }
    .glass-card .ds-input:focus {
        background: rgba(255, 255, 255, 0.12) !important;
        border-color: var(--c-teal) !important;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2) !important;
    }
    .glass-card .ds-input::placeholder {
        color: rgba(255, 255, 255, 0.3) !important;
    }
    .glass-card i.fas {
        color: rgba(255, 255, 255, 0.5) !important;
    }
    .glass-card label[for="remember"] {
        color: rgba(255, 255, 255, 0.75) !important;
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
        font-size: 22px;
        font-weight: 800;
        color: #ffffff;
        margin: 0;
    }
    .portal-subtitle {
        font-size: 13.5px;
        color: rgba(255, 255, 255, 0.75);
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
        font-size: 11.5px;
        color: rgba(255, 255, 255, 0.6);
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
      <div style="display: flex; justify-content: center; gap: 16px; margin-bottom: 16px;">
        <img src="<?= base_url('assets/img/ilog.png') ?>" alt="Logo 1" style="margin-bottom: 0; border: 3px solid rgba(255, 255, 255, 0.8);">
        <img src="<?= base_url('assets/img/tabu.jpg') ?>" alt="Logo 2" style="margin-bottom: 0; border: 3px solid rgba(255, 255, 255, 0.8);">
      </div>
      <h1 class="portal-title"><?= esc($barangay) ?>, <?= esc($municipality) ?></h1>
      <div class="portal-subtitle">Smart Governance Portal</div>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
      <div class="flash-error">
        <i class="fas fa-exclamation-triangle"></i>
        <?= esc(session()->getFlashdata('error')) ?>
      </div>
    <?php endif; ?>

    <div class="ds-card glass-card">
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
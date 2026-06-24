<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Register · Smart Governance Portal</title>
  
  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  
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
        font-family: var(--font);
        color: #f8fafc;
    }
    .register-wrapper {
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
        font-weight: 600;
        font-size: 13px;
        margin-bottom: 6px;
        display: block;
    }
    .glass-card .ds-input {
        background: rgba(255, 255, 255, 0.08) !important;
        border: 1px solid rgba(255, 255, 255, 0.15) !important;
        color: #fff !important;
        width: 100%;
        box-sizing: border-box;
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
    .flash-success {
        background: var(--c-teal-bg);
        color: var(--c-teal);
        padding: 12px 16px;
        border-radius: var(--r-sm);
        margin-bottom: 20px;
        font-size: 12px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .register-footer {
        text-align: center;
        margin-top: 24px;
        font-size: 11.5px;
        color: rgba(255, 255, 255, 0.6);
    }
    .register-footer a {
        color: var(--c-blue);
        text-decoration: none;
        font-weight: 600;
    }
    .register-footer a:hover {
        text-decoration: underline;
    }
  </style>
</head>
<body>

  <div class="register-wrapper">
    <div class="logo-container">
      <div style="display: flex; justify-content: center; gap: 16px; margin-bottom: 16px;">
        <img src="<?= base_url('assets/img/ilog.png') ?>" alt="Logo 1" style="margin-bottom: 0; border: 3px solid rgba(255, 255, 255, 0.8);">
        <img src="<?= base_url('assets/img/tabu.jpg') ?>" alt="Logo 2" style="margin-bottom: 0; border: 3px solid rgba(255, 255, 255, 0.8);">
      </div>
      <h1 class="portal-title">Create Account</h1>
      <div class="portal-subtitle">Smart Governance Portal Registration</div>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
      <div class="flash-error">
        <i class="fas fa-exclamation-triangle"></i>
        <?= esc(session()->getFlashdata('error')) ?>
      </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('success')): ?>
      <div class="flash-success">
        <i class="fas fa-check-circle"></i>
        <?= esc(session()->getFlashdata('success')) ?>
      </div>
    <?php endif; ?>

    <div class="ds-card glass-card">
      <form id="registerForm" action="<?= base_url('/register-post') ?>" method="post" autocomplete="off" aria-label="Registration Form">
        <?= csrf_field() ?>
        
        <div class="ds-card-body" style="padding: 24px;">
            <div style="margin-bottom:16px">
                <label for="emailField" class="ds-input-label">Email Address</label>
                <div style="position:relative">
                    <i class="fas fa-envelope" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--ink-soft);font-size:12px"></i>
                    <input type="email" id="emailField" name="email" class="ds-input" placeholder="you@example.gov.ph" required autocomplete="email" style="padding-left:36px">
                </div>
            </div>
            
            <div style="margin-bottom:24px">
                <label for="passwordField" class="ds-input-label">Password</label>
                <div style="position:relative">
                    <i class="fas fa-lock" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--ink-soft);font-size:12px"></i>
                    <input type="password" name="password" id="passwordField" class="ds-input" placeholder="••••••••••••" required autocomplete="new-password" style="padding-left:36px;padding-right:36px">
                    <button type="button" id="togglePwd" style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--ink-soft);cursor:pointer;padding:4px" aria-label="Toggle Password Visibility"><i class="fas fa-eye-slash" id="eyeIcon"></i></button>
                </div>
            </div>

            <button type="submit" class="ds-btn" style="width:100%;background:var(--c-teal);color:#fff;justify-content:center;height:40px;font-size:13px;border:none;cursor:pointer">
                Register <i class="fas fa-user-plus" style="margin-left:6px"></i>
            </button>
        </div>
      </form>
    </div>

    <div class="register-footer">
      Already have an account? <a href="<?= base_url('/login') ?>">Login here</a><br><br>
      &copy; <?= date('Y') ?> &mdash; Smart Governance Portal
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

    // Provide submission feedback
    document.getElementById('registerForm').addEventListener('submit', function() {
        var btn = this.querySelector('button[type="submit"]');
        btn.disabled = true;
        btn.innerHTML = 'Registering... <i class="fas fa-spinner fa-spin" style="margin-left:6px"></i>';
    });
  </script>
</body>
</html>

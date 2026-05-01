<?php
// Dynamic values from the controller
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
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@600;700&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?= base_url('assets/adminlte/plugins/fontawesome-free/css/all.min.css') ?>">

  <!-- External CSS -->
  <link rel="stylesheet" href="<?= base_url('assets/css/login/style.css') ?>">
</head>
<body>

  <canvas id="canvas"></canvas>
  <!-- Background image (inline because of PHP URL) -->
  <div class="bg-photo" style="background-image: url('<?= base_url('assets/img/wowow.jpg') ?>');"></div>
  <div class="grain"></div>
  <div class="vignette"></div>

  <div class="page">
    <div class="layout">

      <!-- LEFT PANEL -->
      <div class="left">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>

        <div class="left-top">

          <h1 class="left-headline">
            Smart<br>
            <span class="gold italic">Governance</span><br>
            Portal
          </h1>

          <p class="tagline">
            A unified digital platform for <?= esc($barangay) ?>, <?= esc($municipality) ?> — delivering transparent, efficient, and citizen-centered public service to every household.
          </p>

          <div class="gold-rule">
            <div class="gold-rule-line"></div>
            <div class="gold-rule-diamond"></div>
          </div>

          <div class="left-stats">
            <div class="stat"><div class="stat-n">24/7</div><div class="stat-l">Uptime</div></div>
            <div class="stat"><div class="stat-n">AES</div><div class="stat-l">Encrypted</div></div>
            <div class="stat"><div class="stat-n">ISO</div><div class="stat-l">Certified</div></div>
          </div>
        </div>

        <div class="left-bottom">
          <div class="seal-row">
            <div class="seal-icon"><i class="fas fa-shield-alt"></i></div>
            <div class="seal-text">
              <strong>Authorized Personnel Only</strong><br>
              All access attempts are logged and monitored.<br>
              <small style="opacity:0.6;"><?= esc($province) ?> · <?= esc($municipality) ?></small>
            </div>
          </div>
        </div>
      </div>

      <!-- RIGHT PANEL (login form) -->
      <div class="right">
        <div class="beam"></div>
        <div class="right-inner">

          <div class="logo-wrap">
            <div class="logo-ring-wrap">
              <div class="logo-ring"></div>
              <div class="logo-ring-2"></div>
              <img src="<?= base_url('assets/img/tabu.jpg') ?>" alt="Barangay Logo" class="logo-img">
            </div>
            <div class="portal-name"><?= esc($barangay) ?>, <?= esc($municipality) ?></div>
            <span class="portal-sub">Smart Governance Portal</span>
            <div class="live-clock">
              <i class="fas fa-circle" style="font-size:0.4rem;color:#4adb86;"></i>
              <span id="clock">--:-- --</span>
            </div>
          </div>

          <?php if (session()->getFlashdata('error')): ?>
            <div class="flash-error">
              <i class="fas fa-exclamation-triangle"></i>
              <?= session()->getFlashdata('error') ?>
            </div>
          <?php endif; ?>

          <form id="loginForm" action="<?= base_url('/auth') ?>" method="post" autocomplete="off">
            <?= csrf_field() ?>

            <div class="form-divider">
              <div class="form-divider-line"></div>
              <span>Secure Sign-In</span>
              <div class="form-divider-line"></div>
            </div>

            <div class="fields">
              <div class="field">
                <div class="field-label"><i class="fas fa-circle"></i> Administrator Email</div>
                <div class="field-wrap">
                  <input type="email" name="email" placeholder="admin@<?= esc(strtolower(str_replace(' ', '', $municipality))) ?>.gov.ph" required autocomplete="username">
                  <button type="button" class="field-icon-btn" tabindex="-1"><i class="fas fa-user-shield"></i></button>
                </div>
              </div>

              <div class="field">
                <div class="field-label"><i class="fas fa-circle"></i> Access Key</div>
                <div class="field-wrap">
                  <input type="password" name="password" id="passwordField" placeholder="••••••••••••" required autocomplete="current-password">
                  <button type="button" class="field-icon-btn" id="togglePwd" tabindex="-1"><i class="fas fa-eye-slash" id="eyeIcon"></i></button>
                </div>
              </div>
            </div>

            <div class="remember">
              <input type="checkbox" id="remember" name="remember">
              <label for="remember">Keep me signed in on this device</label>
            </div>

            <button type="submit" class="btn-auth" id="submitBtn">
              <span class="btn-label">Authenticate &nbsp;<i class="fas fa-fingerprint"></i></span>
              <span class="btn-spinner"><span class="spinner-ring"></span></span>
            </button>
          </form>

          <div class="security-badges">
            <div class="badge"><i class="fas fa-lock"></i><span>SSL</span></div>
            <div class="badge"><i class="fas fa-shield-alt"></i><span>Secured</span></div>
            <div class="badge"><i class="fas fa-eye-slash"></i><span>Private</span></div>
            <div class="badge"><i class="fas fa-fingerprint"></i><span>Auth</span></div>
          </div>

          <div class="copy">
            &copy; 2026 MISO &mdash; <?= esc($barangay) ?>, <?= esc($municipality) ?>, <?= esc($province) ?><br>
            <a href="#">Privacy Policy</a> &nbsp;&middot;&nbsp; <a href="#">Terms of Use</a>
          </div>

        </div>
      </div>

    </div>
  </div>

  <script src="<?= base_url('js/login/login.js') ?>"></script>
</body>
</html>
<?php
helper('barangay_settings');
$bs = barangay_settings();
$barangay = ($bs['barangay_name'] ?? 'Barangay') . ' Portal';
$logoFile = $bs['logo'] ?? '';
$photoFile = $bs['photo'] ?? '';
$logoSize = (int)($bs['logo_size'] ?? 56);
if ($logoSize < 32 || $logoSize > 160) $logoSize = 56;

$hasLogo  = (!empty($logoFile)  && is_file(FCPATH . 'assets/img/' . $logoFile));
$hasPhoto = (!empty($photoFile) && is_file(FCPATH . 'assets/img/' . $photoFile));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($barangay) ?> - Resident Login</title>
    <link rel="stylesheet" href="<?= base_url('assets/adminlte/plugins/fontawesome-free/css/all.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/bmis-design-system.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/login-ui.css') ?>">
    <style>
        /* Keep logo sizing dynamic (driven by DB) */
        .login-logo {
            width: <?= $logoSize ?>px;
            height: <?= $logoSize ?>px;
            border-radius: 50%;
            border: 2px solid rgba(15, 23, 42, 0.10);
            object-fit: cover;
            background: var(--white);
        }
        .login-logo-placeholder {
            width: <?= $logoSize ?>px;
            height: <?= $logoSize ?>px;
            border-radius: 50%;
            background: var(--c-blue-bg);
            color: var(--c-blue);
            display:flex;
            align-items:center;
            justify-content:center;
            border: 2px solid rgba(15, 23, 42, 0.10);
        }
    </style>
</head>
<body class="login-page">
<div class="login-wrap">
    <div class="login-card">
        <?php if ($hasPhoto): ?>
            <div class="login-banner" style="background-image:url('<?= base_url('assets/img/' . esc($photoFile)) ?>')"></div>
        <?php endif; ?>
        <div class="login-head">
            <?php if ($hasLogo): ?>
                <img src="<?= base_url('assets/img/' . esc($logoFile)) ?>" alt="<?= esc($bs['barangay_name'] ?? 'Barangay') ?> Logo" class="login-logo">
            <?php else: ?>
                <div class="login-logo-placeholder"><i class="fas fa-user"></i></div>
            <?php endif; ?>
            <div>
                <div class="login-title">Resident Login</div>
                <div class="login-subtitle">Access your barangay requests</div>
            </div>
        </div>
        <div class="login-body">
            <?php if (session()->getFlashdata('success')): ?>
                <div class="login-alert login-alert-success">
                    <?= esc(session()->getFlashdata('success')) ?>
                </div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('error')): ?>
                <div class="login-alert login-alert-error">
                    <?= esc(session()->getFlashdata('error')) ?>
                </div>
            <?php endif; ?>

            <form method="post" action="<?= base_url('portal/login') ?>">
                <?= csrf_field() ?>
                <label class="ds-input-label">Email or Phone</label>
                <input class="ds-input" name="identifier" placeholder="you@email.com / 09xxxxxxxxx" required>

                <div style="height:10px"></div>
                <div style="display:flex;justify-content:space-between;align-items:center;">
                    <label class="ds-input-label" style="margin-bottom:0;">Password</label>
                    <a href="<?= base_url('portal/forgot-password') ?>" style="font-size:12px;color:var(--c-blue);text-decoration:none;font-weight:600;">Forgot Password?</a>
                </div>
                <input class="ds-input" type="password" name="password" required style="margin-top:6px;">

                <div style="display:flex;gap:10px;margin-top:14px">
                    <button class="ds-btn ds-btn-blue" style="flex:1;height:40px"><i class="fas fa-sign-in-alt"></i> Login</button>
                    <a class="ds-btn ds-btn-ghost" style="height:40px;background:var(--white)" href="<?= base_url() ?>"><i class="fas fa-home"></i></a>
                </div>
            </form>

            <div style="margin-top:14px;font-size:12px;color:var(--ink-muted);text-align:center">
                No account yet? <a href="<?= base_url('portal/register') ?>" style="color:var(--c-blue);font-weight:700;text-decoration:none">Register</a>
            </div>
        </div>
    </div>
</div>
</body>
</html>

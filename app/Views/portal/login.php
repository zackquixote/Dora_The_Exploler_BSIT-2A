<?php
$barangay = 'Barangay Portal';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($barangay) ?> - Resident Login</title>
    <link rel="stylesheet" href="<?= base_url('assets/adminlte/plugins/fontawesome-free/css/all.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/bmis-design-system.css') ?>">
    <style>
        body { background: var(--bg); margin:0; font-family: var(--font); color: var(--ink); }
        .wrap { min-height: 100vh; display:flex; align-items:center; justify-content:center; padding: 24px; }
        .card { width: 100%; max-width: 420px; background: var(--white); border: .5px solid var(--border); border-radius: var(--r-lg); box-shadow: var(--shadow); overflow:hidden; }
        .head { padding: 18px 20px; border-bottom: .5px solid var(--border); display:flex; align-items:center; gap:10px; }
        .icon { width:40px; height:40px; border-radius: 12px; background: var(--c-blue-bg); color: var(--c-blue); display:flex; align-items:center; justify-content:center; }
        .body { padding: 18px 20px; }
    </style>
</head>
<body>
<div class="wrap">
    <div class="card">
        <div class="head">
            <div class="icon"><i class="fas fa-user"></i></div>
            <div>
                <div style="font-weight:800;font-size:16px">Resident Login</div>
                <div style="font-size:12px;color:var(--ink-muted)">Access your barangay requests</div>
            </div>
        </div>
        <div class="body">
            <?php if (session()->getFlashdata('success')): ?>
                <div style="background:var(--c-teal-bg);color:var(--c-teal);padding:12px 14px;border-radius:12px;margin-bottom:12px;border:1px solid rgba(var(--c-teal-rgb),.2)">
                    <?= esc(session()->getFlashdata('success')) ?>
                </div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('error')): ?>
                <div style="background:var(--c-rose-bg);color:var(--c-rose);padding:12px 14px;border-radius:12px;margin-bottom:12px;border:1px solid rgba(var(--c-rose-rgb),.2)">
                    <?= esc(session()->getFlashdata('error')) ?>
                </div>
            <?php endif; ?>

            <form method="post" action="<?= base_url('portal/login') ?>">
                <?= csrf_field() ?>
                <label class="ds-input-label">Email or Phone</label>
                <input class="ds-input" name="identifier" placeholder="you@email.com / 09xxxxxxxxx" required>

                <div style="height:10px"></div>
                <label class="ds-input-label">Password</label>
                <input class="ds-input" type="password" name="password" required>

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


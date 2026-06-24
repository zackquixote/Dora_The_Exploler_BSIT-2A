<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP</title>
    <link rel="stylesheet" href="<?= base_url('assets/adminlte/plugins/fontawesome-free/css/all.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/bmis-design-system.css') ?>">
    <style>
        body { background: var(--bg); margin:0; font-family: var(--font); color: var(--ink); }
        .wrap { min-height: 100vh; display:flex; align-items:center; justify-content:center; padding: 24px; }
        .card { width:100%; max-width: 520px; background: var(--white); border: .5px solid var(--border); border-radius: var(--r-lg); box-shadow: var(--shadow); overflow:hidden; }
        .head { padding: 18px 20px; border-bottom: .5px solid var(--border); display:flex; align-items:center; gap:12px; }
        .icon { width:44px; height:44px; border-radius: 14px; background: var(--c-teal-bg); color: var(--c-teal); display:flex; align-items:center; justify-content:center; }
        .body { padding: 20px; }
    </style>
</head>
<body>
<div class="wrap">
    <div class="card">
        <div class="head">
            <div class="icon"><i class="fas fa-key"></i></div>
            <div>
                <div style="font-weight:800;font-size:18px">OTP Verification</div>
                <div style="font-size:12px;color:var(--ink-muted)">Confirm your contact method to activate your resident portal access.</div>
            </div>
        </div>

        <div class="body">
            <?php if (session()->getFlashdata('error')): ?>
                <div style="background:var(--c-rose-bg);color:var(--c-rose);padding:12px 14px;border-radius:12px;margin-bottom:12px;border:1px solid rgba(var(--c-rose-rgb),.2)">
                    <?= esc(session()->getFlashdata('error')) ?>
                </div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('success')): ?>
                <div style="background:var(--c-green-bg);color:var(--c-green);padding:12px 14px;border-radius:12px;margin-bottom:12px;border:1px solid rgba(var(--c-green-rgb),.2)">
                    <?= esc(session()->getFlashdata('success')) ?>
                </div>
            <?php endif; ?>

            <div style="background:#f8fafc;border:1px solid var(--border);border-radius:16px;padding:16px;margin-bottom:14px">
                <div style="font-size:13px;color:var(--ink-muted)">OTP channel</div>
                <div style="font-weight:800;margin-top:4px;text-transform:uppercase"><?= esc($verification['otp_channel'] ?? 'email') ?></div>
                <div style="margin-top:8px;font-size:13px;color:var(--ink-muted)">
                    Enter the 6-digit OTP sent to your selected contact method. The code expires in 10 minutes.
                </div>
            </div>

            <form method="post" action="<?= base_url('portal/verify-otp') ?>">
                <?= csrf_field() ?>
                <label class="ds-input-label">OTP Code</label>
                <input class="ds-input" type="text" name="otp_code" inputmode="numeric" maxlength="6" placeholder="Enter 6-digit OTP" required>

                <div style="display:flex;gap:10px;margin-top:14px">
                    <button class="ds-btn ds-btn-teal" style="flex:1;height:44px">
                        <i class="fas fa-check-circle"></i> Verify OTP
                    </button>
                </div>
            </form>

            <form method="post" action="<?= base_url('portal/resend-otp') ?>" style="margin-top:10px">
                <?= csrf_field() ?>
                <button class="ds-btn ds-btn-ghost" style="width:100%;height:42px;background:var(--white)">
                    <i class="fas fa-redo"></i> Resend OTP
                </button>
            </form>

            <div style="margin-top:12px;text-align:center">
                <a href="<?= base_url('portal/verification-status') ?>" style="font-size:13px;color:var(--c-blue);text-decoration:none">
                    Back to verification status
                </a>
            </div>
        </div>
    </div>
</div>
</body>
</html>

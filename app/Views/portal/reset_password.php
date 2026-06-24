<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password - Resident Portal</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Design System -->
    <link rel="stylesheet" href="<?= base_url('assets/css/bmis-design-system.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/adminlte/plugins/fontawesome-free/css/all.min.css') ?>">
</head>
<body style="background: var(--bg-tertiary); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px;">

    <div class="ds-card" style="width: 100%; max-width: 420px; box-shadow: var(--shadow-xl); border: 1px solid var(--border);">
        <div style="text-align: center; padding: 32px 32px 0;">
            <div style="width: 56px; height: 56px; background: var(--c-emerald-bg); color: var(--c-emerald); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px; margin: 0 auto 16px;">
                <i class="fas fa-lock-open"></i>
            </div>
            <h2 style="font-size: 22px; font-weight: 800; color: var(--ink); margin-bottom: 8px;">Reset Password</h2>
            <p style="font-size: 14px; color: var(--ink-muted); margin: 0;">Create a new, strong password for your account.</p>
        </div>

        <div class="ds-card-body" style="padding: 32px;">
            <?php if (session()->getFlashdata('error')): ?>
                <div class="ds-alert ds-alert-danger" style="font-size: 13px;">
                    <i class="fas fa-exclamation-circle" style="margin-right: 8px;"></i>
                    <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('portal/reset-password?token=' . esc($token)) ?>" method="POST">
                <?= csrf_field() ?>
                
                <div class="ds-form-group">
                    <label class="ds-form-label">New Password</label>
                    <input type="password" name="password" class="ds-input" required minlength="6" placeholder="At least 6 characters">
                </div>

                <div class="ds-form-group">
                    <label class="ds-form-label">Confirm Password</label>
                    <input type="password" name="confirm_password" class="ds-input" required minlength="6" placeholder="Repeat your new password">
                </div>

                <button type="submit" class="ds-btn ds-btn-primary" style="width: 100%; margin-top: 12px; justify-content: center;">
                    Reset Password
                </button>
            </form>
        </div>
    </div>

</body>
</html>

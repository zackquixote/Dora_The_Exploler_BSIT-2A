<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password - Resident Portal</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Design System -->
    <link rel="stylesheet" href="<?= base_url('assets/css/bmis-design-system.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/adminlte/plugins/fontawesome-free/css/all.min.css') ?>">
</head>
<body style="background: var(--bg-tertiary); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px;">

    <div class="ds-card" style="width: 100%; max-width: 420px; box-shadow: var(--shadow-xl); border: 1px solid var(--border);">
        <div style="text-align: center; padding: 32px 32px 0;">
            <div style="width: 56px; height: 56px; background: var(--c-blue-bg); color: var(--c-blue); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px; margin: 0 auto 16px;">
                <i class="fas fa-key"></i>
            </div>
            <h2 style="font-size: 22px; font-weight: 800; color: var(--ink); margin-bottom: 8px;">Forgot Password?</h2>
            <p style="font-size: 14px; color: var(--ink-muted); margin: 0;">Enter your registered email address and we'll send you a link to reset your password.</p>
        </div>

        <div class="ds-card-body" style="padding: 32px;">
            <?php if (session()->getFlashdata('success')): ?>
                <div class="ds-alert ds-alert-success" style="font-size: 13px;">
                    <i class="fas fa-check-circle" style="margin-right: 8px;"></i>
                    <?= session()->getFlashdata('success') ?>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="ds-alert ds-alert-danger" style="font-size: 13px;">
                    <i class="fas fa-exclamation-circle" style="margin-right: 8px;"></i>
                    <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('portal/forgot-password') ?>" method="POST">
                <?= csrf_field() ?>
                
                <div class="ds-form-group">
                    <label class="ds-form-label">Email Address</label>
                    <input type="email" name="email" class="ds-input" required placeholder="juan@example.com" value="<?= old('email') ?>">
                </div>

                <button type="submit" class="ds-btn ds-btn-primary" style="width: 100%; margin-top: 12px; justify-content: center;">
                    Send Reset Link
                </button>

                <div style="text-align: center; margin-top: 24px; font-size: 14px;">
                    <a href="<?= base_url('portal/login') ?>" style="color: var(--c-blue); font-weight: 600; text-decoration: none;">
                        <i class="fas fa-arrow-left" style="margin-right: 6px;"></i> Back to Login
                    </a>
                </div>
            </form>
        </div>
    </div>

</body>
</html>

<?= $this->extend('theme/template') ?>
<?= $this->section('content') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/advanced-features.css') ?>">

<div class="bmis-content af-container" style="display:flex; flex-direction:column; align-items:center; justify-content:center; min-height:70vh">
    <div class="af-card <?= (isset($verification['valid']) && $verification['valid']) ? 'verify-valid' : '' ?>" style="max-width: 500px; width: 100%; text-align: center; padding: 40px 20px">
        
        <?php if (isset($verification['valid']) && $verification['valid']): ?>
            <div style="color:#10b981; font-size:72px; margin-bottom:24px; animation: slideUp 0.5s ease-out">
                <i class="fas fa-check-circle"></i>
            </div>
            <h2 style="color:#10b981; font-weight:800; font-size:32px; margin-bottom:12px; animation: slideUp 0.6s ease-out backwards">
                Authentic <?= esc(ucfirst($type)) ?>
            </h2>
            <p style="color:var(--ink-muted); font-size:15px; animation: slideUp 0.7s ease-out backwards">
                This QR code is valid and officially verified by the Barangay System.
            </p>
            <div style="margin-top: 32px; background: rgba(16,185,129,0.1); padding: 16px; border-radius: 12px; color: #065f46; font-weight: 600; animation: slideUp 0.8s ease-out backwards">
                Scanned Successfully at <?= date('h:i A, M d Y') ?>
            </div>
        <?php else: ?>
            <div style="color:#ef4444; font-size:72px; margin-bottom:24px; animation: slideUp 0.5s ease-out">
                <i class="fas fa-times-circle"></i>
            </div>
            <h2 style="color:#ef4444; font-weight:800; font-size:32px; margin-bottom:12px; animation: slideUp 0.6s ease-out backwards">
                Invalid or Expired
            </h2>
            <p style="color:var(--ink-muted); font-size:15px; animation: slideUp 0.7s ease-out backwards">
                This QR code could not be verified in our records. It may be fraudulent or has been revoked.
            </p>
            <div style="margin-top: 32px; background: rgba(239,68,68,0.1); padding: 16px; border-radius: 12px; color: #991b1b; font-weight: 600; animation: slideUp 0.8s ease-out backwards">
                Verification Failed
            </div>
        <?php endif; ?>

        <div style="margin-top:40px; animation: slideUp 0.9s ease-out backwards">
            <a href="<?= base_url('advanced/qr-generator') ?>" class="ds-btn ds-btn-ghost">
                <i class="fas fa-arrow-left"></i> Back to Generator
            </a>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

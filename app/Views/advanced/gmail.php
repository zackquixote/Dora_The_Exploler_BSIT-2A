<?= $this->extend('theme/template') ?>
<?= $this->section('content') ?>

<?php
    $configured = (bool) ($gmail_configured ?? false);
    $authorized = (bool) ($gmail_authorized ?? false);
?>

<div class="bmis-content">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px">
        <div style="display:flex;align-items:center;gap:16px">
            <div style="width:48px;height:48px;border-radius:12px;background:rgba(59,130,246,0.12);color:#2563eb;display:flex;align-items:center;justify-content:center;font-size:20px">
                <i class="fas fa-envelope"></i>
            </div>
            <div>
                <div class="ds-page-title" style="margin:0;font-size:24px;font-weight:800;color:var(--ink)">Gmail Integration</div>
                <div style="font-size:13px;color:var(--ink-muted);margin-top:2px">Connect Gmail OAuth so the system can send emails</div>
            </div>
        </div>
        <div style="display:flex;gap:10px">
            <?php if ($configured): ?>
                <a class="ds-btn ds-btn-primary" href="<?= base_url('advanced/gmail/connect') ?>" style="height:36px">
                    <i class="fas fa-link"></i> Connect
                </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="ds-grid-4" style="margin-bottom:14px">
        <div class="ds-stat">
            <div class="ds-stat-stripe str-blue"></div>
            <div class="ds-stat-top">
                <div class="ds-stat-icon ic-blue"><i class="fas fa-file-alt"></i></div>
            </div>
            <div class="ds-stat-num"><?= $configured ? 'Yes' : 'No' ?></div>
            <div class="ds-stat-label">Credentials File</div>
            <div class="ds-stat-footer ft-blue">writable/credentials/gmail.json</div>
        </div>

        <div class="ds-stat">
            <div class="ds-stat-stripe str-teal"></div>
            <div class="ds-stat-top">
                <div class="ds-stat-icon ic-green"><i class="fas fa-key"></i></div>
            </div>
            <div class="ds-stat-num"><?= $authorized ? 'Yes' : 'No' ?></div>
            <div class="ds-stat-label">Authorized</div>
            <div class="ds-stat-footer ft-teal">OAuth token saved</div>
        </div>
    </div>

    <div class="ds-card">
        <div class="ds-card-body">
            <div class="ds-filter-header"><i class="fas fa-info-circle"></i> Setup</div>
            <div style="font-size:13px;color:var(--ink);line-height:1.6">
                <div style="margin-bottom:8px">
                    1) Put your Google OAuth credentials JSON at <code>writable/credentials/gmail.json</code>
                </div>
                <div style="margin-bottom:8px">
                    2) Make sure your Google Cloud OAuth redirect URI matches:
                    <code><?= esc(site_url('advanced/gmail/callback')) ?></code>
                    (or set <code>GMAIL_OAUTH_REDIRECT_URI</code> in your env)
                </div>
                <div style="margin-bottom:8px">
                    3) Click <strong>Connect</strong> to generate the token at <code>writable/credentials/gmail_token.json</code>
                </div>
                <div style="font-size:12px;color:var(--ink-muted)">
                    After connecting, NotificationService will use Gmail API first (and fallback to SMTP if needed).
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>


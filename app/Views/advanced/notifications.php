<?= $this->extend('theme/template') ?>
<?= $this->section('content') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/advanced-features.css') ?>">

<div class="bmis-content af-container">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px">
        <div style="display:flex;align-items:center;gap:16px">
            <div style="width:56px;height:56px;border-radius:16px;background:rgba(14,165,233,0.12);color:#0ea5e9;display:flex;align-items:center;justify-content:center;font-size:24px">
                <i class="fas fa-bell"></i>
            </div>
            <div>
                <h1 class="ds-page-title" style="margin:0;font-size:28px;font-weight:800;color:var(--ink)">Notification Center</h1>
                <p style="font-size:14px;color:var(--ink-muted);margin-top:2px">Manage system alerts and broadcasts</p>
            </div>
        </div>
        <div>
            <a href="<?= base_url('advanced/send-notification') ?>" class="af-btn-primary" style="background: linear-gradient(135deg, #0EA5E9, #0284C7); box-shadow: 0 4px 15px rgba(14,165,233,0.3)">
                <i class="fas fa-paper-plane"></i> Send Broadcast
            </a>
        </div>
    </div>
    
    <div class="af-card">
        <div class="af-card-body" style="text-align:center; padding: 80px 20px">
            <div style="width: 120px; height: 120px; background: rgba(14,165,233,0.05); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px; font-size: 48px; color: #0ea5e9">
                <i class="fas fa-bell-slash"></i>
            </div>
            <h3 style="font-weight: 800; color: var(--ink); margin-bottom: 12px">All Caught Up!</h3>
            <p style="color: var(--ink-muted); max-width: 400px; margin: 0 auto;">
                You don't have any new system notifications at the moment. Use the Send Broadcast button above to notify residents of any updates.
            </p>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

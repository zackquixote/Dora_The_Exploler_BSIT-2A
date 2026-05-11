<?= $this->extend('theme/template') ?>
<?= $this->section('content') ?>

<div class="bmis-content">

    <?php if (session()->getFlashdata('errors')): ?>
        <div style="background:var(--c-rose-bg);color:var(--c-rose);padding:12px 16px;border-radius:var(--r-sm);margin-bottom:14px;font-size:12px;font-weight:600">
            <i class="fas fa-exclamation-circle" style="margin-right:6px"></i> Please fix:
            <ul style="margin:6px 0 0 16px;padding:0"><?php foreach (session()->getFlashdata('errors') as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?></ul>
        </div>
    <?php endif; ?>

    <div class="ds-card">
        <div class="ds-card-head">
            <div class="ds-card-title"><i class="fas fa-edit"></i> Update Certificate</div>
        </div>
        <form action="<?= base_url('certificate/update/' . $cert['id']) ?>" method="POST" id="updateCertForm">
            <?= csrf_field() ?>
            <div class="ds-card-body">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
                    <div>
                        <label class="ds-input-label">Resident</label>
                        <input type="text" class="ds-input" value="<?= esc(($resident['first_name'] ?? '') . ' ' . ($resident['last_name'] ?? '')) ?>" readonly style="background:var(--bg)">
                    </div>
                    <div>
                        <label class="ds-input-label">Certificate Type</label>
                        <input type="text" class="ds-input" value="<?= esc($cert['certificate_type']) ?>" readonly style="background:var(--bg)">
                    </div>
                </div>
                <div style="margin-top:14px">
                    <label class="ds-input-label">Purpose</label>
                    <textarea name="purpose" class="ds-input" rows="3" style="resize:vertical"><?= esc($cert['purpose']) ?></textarea>
                </div>
            </div>
            <div style="padding:12px 18px;border-top:.5px solid var(--border);display:flex;justify-content:flex-end;gap:8px">
                <a href="<?= base_url('certificate') ?>" class="ds-btn ds-btn-ghost">Cancel</a>
                <a href="<?= base_url('certificate/print/' . $cert['id']) ?>" target="_blank" class="ds-btn" style="background:var(--c-blue);color:#fff"><i class="fas fa-print"></i> Print</a>
                <button type="submit" class="ds-btn ds-btn-primary"><i class="fas fa-save"></i> Update</button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
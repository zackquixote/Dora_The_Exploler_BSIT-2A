<?= $this->extend('theme/template') ?>
<?= $this->section('content') ?>

<div class="bmis-content">

    <div class="bmis-page-header">
        <div class="bmis-page-title">
            <h1 style="font-weight:800"><i class="fas fa-file-edit"></i> Edit Certificate Template</h1>
            <p>Editing template for: <strong><?= esc($template['name']) ?></strong></p>
        </div>
        <div class="bmis-page-actions">
            <a href="<?= base_url('admin/certificateTypes') ?>" class="ds-btn ds-btn-ghost"><i class="fas fa-arrow-left"></i> Back</a>
        </div>
    </div>

    <?php if (session()->getFlashdata('errors')): ?>
        <div style="background:var(--c-rose-bg);color:var(--c-rose);padding:12px 16px;border-radius:var(--r-sm);margin-bottom:14px;font-size:12px;font-weight:600">
            <i class="fas fa-exclamation-circle"></i> Please fix the following:
            <ul style="margin:6px 0 0 16px;padding:0">
                <?php foreach (session()->getFlashdata('errors') as $e): ?>
                    <li><?= esc($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="<?= base_url('admin/certificateTypes/update/' . $template['id']) ?>" method="POST">
        <?= csrf_field() ?>
        <!-- Hidden id field as fallback (controller also reads from URL segment) -->
        <input type="hidden" name="id" value="<?= $template['id'] ?>">

        <div class="ds-card" style="margin-bottom:14px">
            <div class="ds-card-head">
                <div class="ds-card-title"><i class="fas fa-file-alt"></i> Template Details</div>
            </div>
            <div class="ds-card-body">
                <div style="margin-bottom:14px">
                    <label class="ds-input-label">Certificate Type</label>
                    <input type="text" class="ds-input" value="<?= esc($template['name']) ?>" disabled style="background:var(--bg);color:var(--ink-muted)">
                    <!-- Name is fixed — changing it would break the ENUM constraint -->
                    <input type="hidden" name="name" value="<?= esc($template['name']) ?>">
                </div>
                <div>
                    <label class="ds-input-label">Template Content (HTML)</label>
                    <p style="font-size:11px;color:var(--ink-muted);margin-bottom:8px">
                        Available placeholders: <code>{resident_name}</code>, <code>{age}</code>, <code>{civil_status}</code>,
                        <code>{address}</code>, <code>{purpose}</code>, <code>{date_issued}</code>,
                        <code>{barangay_name}</code>, <code>{municipality}</code>, <code>{province}</code>,
                        <code>{captain_name}</code>, <code>{ctrl_number}</code>
                    </p>
                    <textarea name="content" class="ds-input" rows="16" style="font-family:monospace;font-size:12px;resize:vertical"><?= old('content', $template['content'] ?? '') ?></textarea>
                </div>
            </div>
            <div style="padding:12px 18px;border-top:.5px solid var(--border);display:flex;justify-content:flex-end;gap:8px">
                <a href="<?= base_url('admin/certificateTypes') ?>" class="ds-btn ds-btn-ghost">Cancel</a>
                <button type="submit" class="ds-btn ds-btn-primary"><i class="fas fa-save"></i> Update Template</button>
            </div>
        </div>
    </form>
</div>

<?= $this->endSection() ?>

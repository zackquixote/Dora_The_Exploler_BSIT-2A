<?= $this->extend('theme/template') ?>
<?= $this->section('content') ?>

<div class="bmis-content">

    <div class="bmis-page-header">
        <div class="bmis-page-title">
            <h1 style="font-weight:800"><i class="fas fa-file-alt"></i> Certificate Templates</h1>
            <p>Manage HTML templates for each certificate type.</p>
        </div>
        <div class="bmis-page-actions">
            <a href="<?= base_url('admin/certificateTypes/create') ?>" class="ds-btn ds-btn-primary">
                <i class="fas fa-plus"></i> Add Template
            </a>
        </div>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div style="background:var(--c-green-bg);color:var(--c-green);padding:12px 16px;border-radius:var(--r-sm);margin-bottom:14px;font-size:12px;font-weight:600">
            <i class="fas fa-check-circle"></i> <?= esc(session()->getFlashdata('success')) ?>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div style="background:var(--c-rose-bg);color:var(--c-rose);padding:12px 16px;border-radius:var(--r-sm);margin-bottom:14px;font-size:12px;font-weight:600">
            <i class="fas fa-exclamation-circle"></i> <?= esc(session()->getFlashdata('error')) ?>
        </div>
    <?php endif; ?>

    <div class="ds-card">
        <div class="ds-card-body" style="padding:0">
            <table class="ds-table" style="width:100%">
                <thead>
                    <tr>
                        <th>Certificate Type</th>
                        <th>Template Status</th>
                        <th>Last Updated</th>
                        <th style="text-align:right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($types as $t): ?>
                    <tr>
                        <td style="font-weight:600"><?= esc($t['name']) ?></td>
                        <td>
                            <?php if (!empty($t['id'])): ?>
                                <span class="ds-badge ds-badge-green">Template Set</span>
                            <?php else: ?>
                                <span class="ds-badge" style="background:var(--c-amber-bg);color:var(--c-amber)">No Template</span>
                            <?php endif; ?>
                        </td>
                        <td style="font-size:12px;color:var(--ink-muted)">
                            <?= !empty($t['updated_at']) ? date('M d, Y', strtotime($t['updated_at'])) : '—' ?>
                        </td>
                        <td style="text-align:right">
                            <?php if (!empty($t['id'])): ?>
                                <a href="<?= base_url('admin/certificateTypes/edit/' . $t['id']) ?>" class="ds-action-btn ab-blue" title="Edit"><i class="fas fa-edit"></i></a>
                                <button type="button" class="ds-action-btn ab-rose" title="Delete"
                                    onclick="confirmDelete(<?= $t['id'] ?>, '<?= esc($t['name']) ?>')">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            <?php else: ?>
                                <a href="<?= base_url('admin/certificateTypes/create?name=' . urlencode($t['name'])) ?>" class="ds-btn ds-btn-ghost" style="height:28px;font-size:11px">
                                    <i class="fas fa-plus"></i> Create
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function confirmDelete(id, name) {
    if (!confirm('Delete template for "' + name + '"? This cannot be undone.')) return;
    fetch('<?= base_url('admin/certificateTypes/delete/') ?>' + id, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/x-www-form-urlencoded' },
        body: '<?= csrf_token() ?>=' + '<?= csrf_hash() ?>'
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) location.reload();
        else alert(data.message || 'Delete failed.');
    });
}
</script>

<?= $this->endSection() ?>

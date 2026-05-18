<?= $this->extend('theme/template') ?>
<?= $this->section('content') ?>

<div class="bmis-content">
    <div class="ds-card">
        <div class="ds-card-body">
            <div class="ds-page-title" style="margin:0 0 6px 0;font-size:20px;font-weight:800;color:var(--ink)">Upload Document</div>
            <div style="font-size:13px;color:var(--ink-muted);margin-bottom:12px">Use the Document Management page to upload and manage versions.</div>
            <a class="ds-btn ds-btn-primary" href="<?= base_url('advanced/documents') ?>" style="height:36px">
                <i class="fas fa-folder-open"></i> Open Document Management
            </a>
        </div>
    </div>
</div>

<?= $this->endSection() ?>


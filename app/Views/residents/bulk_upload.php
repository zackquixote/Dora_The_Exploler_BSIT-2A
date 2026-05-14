<?= $this->extend('theme/template') ?>
<?= $this->section('content') ?>

<div class="bmis-content">

    <!-- Premium Page Header -->
    <div class="bmis-page-header">
        <div class="bmis-page-title">
            <h1 style="font-weight: 800;"><i class="fas fa-file-upload text-primary"></i> Bulk Upload Residents</h1>
            <p>Upload a CSV file to insert multiple residents into the system at once.</p>
        </div>
        <div class="bmis-page-actions">
            <a href="<?= base_url('resident') ?>" class="btn btn-light btn-sm rounded-pill px-3 fw-bold shadow-sm" style="border: 1px solid var(--border);"><i class="fas fa-arrow-left me-2"></i> Back to Directory</a>
        </div>
    </div>

    <!-- Errors -->
    <?php if (session()->getFlashdata('error')): ?>
        <div style="background:var(--c-rose-bg);color:var(--c-rose);padding:12px 16px;border-radius:var(--r-sm);margin-bottom:14px;font-size:12px;font-weight:600">
            <i class="fas fa-exclamation-circle" style="margin-right:6px"></i> <?= esc(session()->getFlashdata('error')) ?>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('success')): ?>
        <div style="background:var(--c-green-bg);color:var(--c-green);padding:12px 16px;border-radius:var(--r-sm);margin-bottom:14px;font-size:12px;font-weight:600">
            <i class="fas fa-check-circle" style="margin-right:6px"></i> <?= esc(session()->getFlashdata('success')) ?>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('bulk_errors')): ?>
        <div style="background:var(--c-amber-bg);color:var(--c-amber);padding:12px 16px;border-radius:var(--r-sm);margin-bottom:14px;font-size:12px;font-weight:600">
            <i class="fas fa-exclamation-triangle" style="margin-right:6px"></i> <strong>Some rows were skipped:</strong>
            <ul style="margin:6px 0 0 16px;padding:0;max-height:150px;overflow-y:auto;">
                <?php foreach (session()->getFlashdata('bulk_errors') as $e): ?>
                    <li><?= esc($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- Instructions & Template -->
    <div class="ds-card" style="margin-bottom:14px">
        <div class="ds-card-head">
            <div class="ds-card-title"><i class="fas fa-info-circle"></i> Instructions</div>
            <div class="ds-card-actions">
                <a href="<?= base_url('resident/download-template') ?>" class="btn btn-primary btn-sm rounded-pill px-3 fw-bold shadow-sm" style="background: var(--c-blue); border-color: var(--c-blue);">
                    <i class="fas fa-download me-1"></i> Download CSV Template
                </a>
            </div>
        </div>
        <div class="ds-card-body" style="font-size: 13px; line-height: 1.6;">
            <p>To ensure successful upload, please follow these guidelines:</p>
            <ul>
                <li>Download the provided CSV template.</li>
                <li>Do not change the header row.</li>
                <li><strong>Required columns:</strong> First Name, Last Name, Birthdate (YYYY-MM-DD), Sex (Male or Female).</li>
                <li>If a duplicate resident is found (same First Name, Last Name, and Birthdate), that row will be skipped to prevent duplicates.</li>
                <li>Save your file as a <strong>CSV (Comma Delimited)</strong> before uploading.</li>
            </ul>
        </div>
    </div>

    <!-- Upload Form -->
    <form action="<?= base_url('resident/process-bulk-upload') ?>" method="POST" enctype="multipart/form-data">
        <?= csrf_field() ?>
        
        <div class="ds-card" style="margin-bottom:14px">
            <div class="ds-card-head">
                <div class="ds-card-title"><i class="fas fa-upload"></i> Upload CSV File</div>
            </div>
            <div class="ds-card-body" style="text-align: center; padding: 40px 20px;">
                <input type="file" name="csv_file" id="csv_file" accept=".csv" required style="display: none;" onchange="updateFileName()">
                <label for="csv_file" style="cursor: pointer; display: inline-block; padding: 20px; border: 2px dashed var(--border); border-radius: var(--r-md); background: var(--bg-soft); transition: background 0.2s;">
                    <i class="fas fa-file-csv" style="font-size: 48px; color: var(--c-blue); margin-bottom: 10px;"></i>
                    <div style="font-weight: 600; font-size: 14px;">Click here to browse for a CSV file</div>
                    <div id="file-name-display" style="margin-top: 10px; font-size: 13px; color: var(--ink-muted);">No file selected</div>
                </label>
            </div>
            <div style="padding:12px 18px;border-top:.5px solid var(--border);display:flex;align-items:center;justify-content:flex-end">
                <button type="submit" class="ds-btn ds-btn-primary" id="uploadBtn"><i class="fas fa-cloud-upload-alt"></i> Process Upload</button>
            </div>
        </div>
    </form>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function updateFileName() {
    var input = document.getElementById('csv_file');
    var display = document.getElementById('file-name-display');
    if (input.files && input.files.length > 0) {
        display.innerHTML = '<span style="color:var(--c-blue);font-weight:700;">' + input.files[0].name + '</span>';
    } else {
        display.innerHTML = 'No file selected';
    }
}

// Add loading state to button to prevent double submission
document.querySelector('form').addEventListener('submit', function() {
    var btn = document.getElementById('uploadBtn');
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
    btn.classList.add('disabled');
    btn.style.pointerEvents = 'none';
});
</script>
<?= $this->endSection() ?>

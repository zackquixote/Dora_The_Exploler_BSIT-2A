<?= $this->extend('theme/template') ?>
<?= $this->section('content') ?>

<div class="bmis-content">
    
    <!-- Errors -->
    <?php if (session()->getFlashdata('error')): ?>
        <div style="background:var(--c-rose-bg);color:var(--c-rose);padding:12px 16px;border-radius:var(--r-sm);margin-bottom:14px;font-size:12px;font-weight:600">
            <i class="fas fa-exclamation-circle" style="margin-right:6px"></i> <?= esc(session()->getFlashdata('error')) ?>
        </div>
    <?php endif; ?>

    <div class="ds-card">
        <div class="ds-card-head" style="display:flex; justify-content:space-between; align-items:center;">
            <div class="ds-card-title">
                <i class="fas fa-users"></i> Bulk Issue Certificate
            </div>
            <a href="<?= base_url('certificate') ?>" class="btn btn-light btn-sm rounded-pill px-3 fw-bold shadow-sm" style="border: 1px solid var(--border);"><i class="fas fa-arrow-left me-2"></i> Back to Log</a>
        </div>
        <form action="<?= base_url('certificate/bulk-store') ?>" method="POST" id="certTypeForm">
            <?= csrf_field() ?>
            <div class="ds-card-body">
                <div style="margin-bottom: 20px; font-size: 13px; color: var(--ink-muted);">
                    Select multiple residents, choose the certificate type, and provide the purpose. The system will automatically generate a certificate for each selected resident.
                </div>
                
                <div style="margin-bottom: 14px">
                    <label class="ds-input-label">Select Residents <span style="color:var(--c-rose)">*</span></label>
                    <!-- Using select2 with multiple="multiple" -->
                    <select name="resident_ids[]" class="ds-select select2" multiple="multiple" style="width:100%" required>
                        <?php foreach($residents as $r): ?>
                            <option value="<?= $r['id'] ?>"><?= esc($r['last_name'] . ', ' . $r['first_name'] . (!empty($r['middle_name']) ? ' ' . substr($r['middle_name'], 0, 1) . '.' : '')) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <div style="font-size: 11px; color: var(--ink-soft); margin-top: 4px;"><i class="fas fa-info-circle"></i> You can search and select as many residents as you need.</div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px; margin-top:20px;">
                    <div>
                        <label class="ds-input-label">Certificate Type <span style="color:var(--c-rose)">*</span></label>
                        <select name="certificate_type" class="ds-select" required>
                            <option value="">Select Type</option>
                            <?php foreach($types as $t): ?>
                                <option value="<?= $t ?>"><?= esc($t) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="ds-input-label">Purpose <span style="color:var(--c-rose)">*</span></label>
                        <textarea name="purpose" class="ds-input" rows="2" required placeholder="e.g. For employment" style="resize:vertical"></textarea>
                    </div>
                </div>
            </div>
            <div style="padding:12px 18px;border-top:.5px solid var(--border);display:flex;justify-content:flex-end;gap:8px">
                <a href="<?= base_url('certificate') ?>" class="ds-btn ds-btn-ghost">Cancel</a>
                <button type="submit" class="ds-btn" style="background:var(--c-violet);color:#fff"><i class="fas fa-cogs"></i> Generate Certificates</button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
/* Premium Select2 Styling */
.select2-container--default .select2-selection--multiple {
    border: 1.5px solid var(--border);
    border-radius: var(--r-md);
    min-height: 52px;
    padding: 6px 12px;
    background: var(--bg-soft);
    transition: all 0.2s ease;
}
.select2-container--default.select2-container--focus .select2-selection--multiple {
    border-color: var(--c-blue);
    background: #fff;
    box-shadow: 0 0 0 4px rgba(var(--c-blue-rgb), 0.1);
}
.select2-container--default .select2-selection--multiple .select2-selection__choice {
    background: var(--c-blue-bg, #E6F1FB);
    border: 1px solid rgba(0, 110, 255, 0.2);
    color: var(--c-blue, #185FA5);
    border-radius: 6px;
    padding: 6px 10px 6px 30px; /* Space for the 'x' on the left */
    margin-top: 6px;
    margin-right: 6px;
    font-size: 13.5px;
    font-weight: 600;
    position: relative;
}
.select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
    color: var(--c-blue, #185FA5);
    border: none;
    background: transparent;
    font-size: 18px;
    font-weight: bold;
    line-height: 1;
    position: absolute;
    left: 8px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
}
.select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
    color: var(--c-rose, #e11d48);
    background: transparent;
}
.select2-container--default .select2-search--inline .select2-search__field {
    margin-top: 8px;
    font-family: inherit;
    font-size: 14px;
    color: var(--ink);
}
.select2-container--default .select2-search--inline .select2-search__field::placeholder {
    color: var(--ink-muted);
}
.select2-dropdown {
    border: 1px solid #cbd5e1;
    border-radius: var(--r-md);
    box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    overflow: hidden;
    margin-top: 4px;
    background: #ffffff;
    z-index: 1060;
}
.select2-results__option {
    padding: 12px 16px;
    font-size: 15px;
    color: #1e293b; /* Dark slate for high contrast */
    border-bottom: 1px solid #f1f5f9;
    transition: background 0.15s;
    background: #ffffff;
}
.select2-results__option:last-child {
    border-bottom: none;
}
.select2-results__option--highlighted[aria-selected] {
    background: #eff6ff !important; /* light blue */
    color: #1d4ed8 !important; /* dark blue */
    font-weight: 600;
}
.select2-results__option[aria-selected=true] {
    background: #f8fafc;
    color: #94a3b8;
    font-style: italic;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function(){ 
        $('.select2').select2({ 
            placeholder: "🔍 Search and click to select residents...",
            allowClear: true,
            width: '100%',
            language: {
                noResults: function() {
                    return "No resident found";
                }
            }
        }); 
        
        // Add loading state to button
        $('#certTypeForm').on('submit', function() {
            var btn = $(this).find('button[type="submit"]');
            btn.html('<i class="fas fa-spinner fa-spin"></i> Generating...');
            btn.prop('disabled', true);
            // Allow form submission to proceed
        });
    });
</script>
<?= $this->endSection() ?>

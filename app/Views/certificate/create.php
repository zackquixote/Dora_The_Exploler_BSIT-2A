<?= $this->extend('theme/template') ?>
<?= $this->section('content') ?>

<div class="bmis-content">
    <div class="ds-card">
        <div class="ds-card-head">
            <div class="ds-card-title"><i class="fas fa-file-contract"></i> New Certificate</div>
        </div>
        <form action="<?= base_url('certificate/store') ?>" method="POST" id="certTypeForm">
            <?= csrf_field() ?>
            <div class="ds-card-body">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
                    <div>
                        <label class="ds-input-label">Resident <span style="color:var(--c-rose)">*</span></label>
                        <select name="resident_id" class="ds-select select2" style="width:100%" required>
                            <option value="">Select Resident</option>
                            <?php foreach($residents as $r): ?>
                                <option value="<?= $r['id'] ?>"><?= esc($r['last_name'] . ', ' . $r['first_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="ds-input-label">Type <span style="color:var(--c-rose)">*</span></label>
                        <select name="certificate_type" class="ds-select" required>
                            <option value="">Select Type</option>
                            <?php foreach($types as $t): ?>
                                <option value="<?= $t ?>"><?= esc($t) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div style="margin-top:14px">
                    <label class="ds-input-label">Purpose <span style="color:var(--c-rose)">*</span></label>
                    <textarea name="purpose" class="ds-input" rows="3" required placeholder="e.g. Employment" style="resize:vertical"></textarea>
                </div>
            </div>
            <div style="padding:12px 18px;border-top:.5px solid var(--border);display:flex;justify-content:flex-end;gap:8px">
                <a href="<?= base_url('certificate') ?>" class="ds-btn ds-btn-ghost">Cancel</a>
                <button type="submit" class="ds-btn" style="background:var(--c-violet);color:#fff"><i class="fas fa-file-download"></i> Generate</button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>$(document).ready(function(){ $('.select2').select2({ theme: 'bootstrap4' }); });</script>
<?= $this->endSection() ?>

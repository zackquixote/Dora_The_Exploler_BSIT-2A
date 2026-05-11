<?= $this->extend('theme/template') ?>
<?= $this->section('content') ?>

<div class="bmis-content">

    <div class="bmis-page-header">
        <div class="bmis-page-title">
            <h1 style="font-weight:800"><i class="fas fa-user-plus"></i> Add Official</h1>
            <p>Register a new barangay official.</p>
        </div>
        <div class="bmis-page-actions">
            <a href="<?= base_url('officials') ?>" class="ds-btn ds-btn-ghost"><i class="fas fa-arrow-left"></i> Back</a>
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

    <form action="<?= base_url('officials/store') ?>" method="POST" enctype="multipart/form-data">
        <?= csrf_field() ?>

        <div class="ds-card" style="margin-bottom:14px">
            <div class="ds-card-head">
                <div class="ds-card-title"><i class="fas fa-id-badge"></i> Official Details</div>
            </div>
            <div class="ds-card-body">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
                    <div>
                        <label class="ds-input-label">Position <span style="color:var(--c-rose)">*</span></label>
                        <select name="position" class="ds-select" required>
                            <option value="">Select Position</option>
                            <?php foreach ($positions as $pos): ?>
                                <option value="<?= $pos ?>" <?= old('position') === $pos ? 'selected' : '' ?>><?= $pos ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="ds-input-label">Link to Resident (Optional)</label>
                        <select name="resident_id" class="ds-select">
                            <option value="">— None / Enter name manually —</option>
                            <?php foreach ($residents as $r): ?>
                                <option value="<?= $r['id'] ?>" <?= old('resident_id') == $r['id'] ? 'selected' : '' ?>>
                                    <?= esc($r['last_name'] . ', ' . $r['first_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="ds-input-label">Full Name <span style="color:var(--c-rose)">*</span></label>
                        <input type="text" name="full_name" class="ds-input" value="<?= old('full_name') ?>" placeholder="Auto-filled if resident is selected" required>
                    </div>
                    <div>
                        <label class="ds-input-label">Contact Number</label>
                        <input type="text" name="contact_number" class="ds-input" value="<?= old('contact_number') ?>" placeholder="e.g. 09123456789">
                    </div>
                    <div>
                        <label class="ds-input-label">Photo (Optional)</label>
                        <input type="file" name="photo" class="ds-input" accept="image/*">
                    </div>
                </div>
            </div>
            <div style="padding:12px 18px;border-top:.5px solid var(--border);display:flex;justify-content:flex-end;gap:8px">
                <a href="<?= base_url('officials') ?>" class="ds-btn ds-btn-ghost">Cancel</a>
                <button type="submit" class="ds-btn ds-btn-primary"><i class="fas fa-save"></i> Save Official</button>
            </div>
        </div>
    </form>
</div>

<?= $this->endSection() ?>

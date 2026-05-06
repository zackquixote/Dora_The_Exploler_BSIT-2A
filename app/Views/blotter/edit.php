<?php
$role = session()->get('role');
$template = ($role == 'admin') ? 'theme/admin/template' : 'theme/template';
?>
<?= $this->extend($template) ?>
<?= $this->section('content') ?>

<div class="bmis-content">
    <form action="<?= base_url('blotter/update/'.$case['id']) ?>" method="POST" id="blotter-form">
        <?= csrf_field() ?>

        <!-- Incident Details -->
        <div class="ds-card" style="margin-bottom:14px">
            <div class="ds-card-head"><div class="ds-card-title"><i class="fas fa-edit"></i> Edit Case <?= esc($case['case_number']) ?></div></div>
            <div class="ds-card-body">
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px">
                    <div>
                        <label class="ds-input-label">Incident Type <span style="color:var(--c-rose)">*</span></label>
                        <select name="incident_type" class="ds-select" required>
                            <option value="">Select Type</option>
                            <?php foreach (['Physical Violence','Oral Defamation','Property Damage','Disturbance','Land Dispute','Others'] as $t): ?>
                                <option value="<?= $t ?>" <?= ($case['incident_type'] ?? '') == $t ? 'selected' : '' ?>><?= $t ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="ds-input-label">Date of Incident <span style="color:var(--c-rose)">*</span></label>
                        <input type="date" name="incident_date" class="ds-input" required value="<?= esc($case['incident_date']) ?>">
                    </div>
                    <div>
                        <label class="ds-input-label">Purok / Sitio</label>
                        <select name="purok" class="ds-select">
                            <option value="">Select Purok</option>
                            <?php foreach (['Purok Malipayon','Purok Masagana','Purok Cory','Purok Kawayan','Purok Pagla-um'] as $p): ?>
                                <option value="<?= $p ?>" <?= ($case['purok'] ?? '') == $p ? 'selected' : '' ?>><?= $p ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div style="margin-top:14px">
                    <label class="ds-input-label">Specific Location</label>
                    <input type="text" name="incident_location" class="ds-input" value="<?= esc($case['incident_location'] ?? '') ?>">
                </div>
                <div style="margin-top:14px">
                    <label class="ds-input-label">Narrative <span style="color:var(--c-rose)">*</span></label>
                    <textarea name="details" class="ds-input" rows="5" required style="resize:vertical"><?= esc($case['details']) ?></textarea>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-top:14px">
                    <div>
                        <label class="ds-input-label">Status</label>
                        <select name="status" class="ds-select">
                            <?php foreach (['Pending','Investigating','Ongoing','For Hearing','Settled','Dismissed','Referred','Unsettled'] as $s): ?>
                                <option value="<?= $s ?>" <?= ($case['status'] ?? '') == $s ? 'selected' : '' ?>><?= $s ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="ds-input-label">Action Taken</label>
                        <textarea name="action_taken" class="ds-input" rows="2" style="resize:vertical"><?= esc($case['action_taken'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Involved Parties -->
        <div class="ds-card" style="margin-bottom:14px">
            <div class="ds-card-head">
                <div class="ds-card-title"><i class="fas fa-users"></i> Involved Parties</div>
                <button type="button" class="ds-btn ds-btn-teal" id="add-party-btn" style="height:30px;font-size:11px"><i class="fas fa-plus"></i> Add Party</button>
            </div>
            <div class="ds-card-body" id="parties-container">
                <?php foreach ($parties as $index => $p): ?>
                <div class="party-entry" style="padding:14px;background:var(--bg);border-radius:var(--r-sm);margin-bottom:10px;border:.5px solid var(--border)">
                    <div style="display:grid;grid-template-columns:140px 140px 1fr auto;gap:10px;align-items:end">
                        <div>
                            <label class="ds-input-label">Role</label>
                            <select name="parties[<?= $index ?>][role]" class="ds-select" required>
                                <option value="complainant" <?= $p['role'] == 'complainant' ? 'selected' : '' ?>>Complainant</option>
                                <option value="respondent" <?= $p['role'] == 'respondent' ? 'selected' : '' ?>>Respondent</option>
                                <option value="witness" <?= $p['role'] == 'witness' ? 'selected' : '' ?>>Witness</option>
                            </select>
                        </div>
                        <div>
                            <label class="ds-input-label">Type</label>
                            <div style="display:flex;gap:4px">
                                <label style="flex:1;display:flex;align-items:center;justify-content:center;padding:6px;border-radius:var(--r-sm);font-size:10.5px;font-weight:700;cursor:pointer;border:1px solid var(--border);background:<?= empty($p['resident_id']) ? 'var(--white)' : 'var(--c-blue-bg)' ?>">
                                    <input type="radio" name="parties[<?= $index ?>][type]" value="resident" <?= empty($p['resident_id']) ? '' : 'checked' ?> style="display:none"> Resident
                                </label>
                                <label style="flex:1;display:flex;align-items:center;justify-content:center;padding:6px;border-radius:var(--r-sm);font-size:10.5px;font-weight:700;cursor:pointer;border:1px solid var(--border);background:<?= empty($p['resident_id']) ? 'var(--c-amber-bg)' : 'var(--white)' ?>">
                                    <input type="radio" name="parties[<?= $index ?>][type]" value="outsider" <?= empty($p['resident_id']) ? 'checked' : '' ?> style="display:none"> Outsider
                                </label>
                            </div>
                        </div>
                        <div>
                            <div class="resident-fields" style="<?= empty($p['resident_id']) ? 'display:none' : '' ?>">
                                <label class="ds-input-label">Search Resident</label>
                                <select name="parties[<?= $index ?>][resident_id]" class="resident-select ds-select" style="width:100%">
                                    <?php if (!empty($p['resident_id'])): ?>
                                        <option value="<?= $p['resident_id'] ?>" selected><?= esc($p['resident_name'] ?? '') ?></option>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="outsider-fields" style="<?= empty($p['resident_id']) ? '' : 'display:none' ?>">
                                <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px">
                                    <div><label class="ds-input-label">Name</label><input type="text" name="parties[<?= $index ?>][outsider_name]" class="ds-input" value="<?= esc($p['outsider_name'] ?? '') ?>"></div>
                                    <div><label class="ds-input-label">Address</label><input type="text" name="parties[<?= $index ?>][outsider_address]" class="ds-input" value="<?= esc($p['outsider_address'] ?? '') ?>"></div>
                                </div>
                            </div>
                        </div>
                        <div>
                            <button type="button" class="ds-action-btn ab-rose remove-party" <?= count($parties) <= 2 ? 'disabled style="opacity:.3"' : '' ?>><i class="fas fa-trash-alt"></i></button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div style="padding:12px 18px;border-top:.5px solid var(--border);display:flex;justify-content:flex-end;gap:8px">
                <a href="<?= base_url('blotter/view/'.$case['id']) ?>" class="ds-btn ds-btn-ghost">Cancel</a>
                <button type="submit" class="ds-btn ds-btn-primary"><i class="fas fa-save"></i> Update Case</button>
            </div>
        </div>
    </form>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    window.blotterConfig = {
        searchUrl: '<?= base_url('blotter/searchResidents') ?>',
        partyIndex: <?= count($parties) ?>
    };
</script>
<script src="<?= base_url('js/blotter/blotter-edit.js') ?>"></script>
<?= $this->endSection() ?>
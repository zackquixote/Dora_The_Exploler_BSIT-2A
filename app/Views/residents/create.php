<?php
$role = strtolower(session()->get('role') ?? 'staff');
$template = ($role == 'admin') ? 'theme/admin/template' : 'theme/template';
?>
<?= $this->extend($template) ?>
<?= $this->section('content') ?>

<div class="bmis-content">

    <!-- Errors -->
    <?php if (session()->getFlashdata('errors')): ?>
        <div style="background:var(--c-rose-bg);color:var(--c-rose);padding:12px 16px;border-radius:var(--r-sm);margin-bottom:14px;font-size:12px;font-weight:600">
            <i class="fas fa-exclamation-circle" style="margin-right:6px"></i> Please fix the following:
            <ul style="margin:6px 0 0 16px;padding:0"><?php foreach (session()->getFlashdata('errors') as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?></ul>
        </div>
    <?php endif; ?>

    <form id="residentForm" action="<?= base_url('resident/store') ?>" method="POST" enctype="multipart/form-data">
        <?= csrf_field() ?>

        <!-- SECTION 1: BASIC INFO -->
        <div class="ds-card" style="margin-bottom:14px">
            <div class="ds-card-head">
                <div class="ds-card-title"><i class="fas fa-user-circle"></i> Basic Information</div>
            </div>
            <div class="ds-card-body">
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px">
                    <div>
                        <label class="ds-input-label">First Name <span style="color:var(--c-rose)">*</span></label>
                        <input type="text" name="first_name" class="ds-input" value="<?= old('first_name') ?>" placeholder="e.g. Juan" required>
                    </div>
                    <div>
                        <label class="ds-input-label">Middle Name</label>
                        <input type="text" name="middle_name" class="ds-input" value="<?= old('middle_name') ?>" placeholder="e.g. Dela Cruz">
                    </div>
                    <div>
                        <label class="ds-input-label">Last Name <span style="color:var(--c-rose)">*</span></label>
                        <input type="text" name="last_name" class="ds-input" value="<?= old('last_name') ?>" placeholder="e.g. Santos" required>
                    </div>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px;margin-top:14px">
                    <div>
                        <label class="ds-input-label">Gender <span style="color:var(--c-rose)">*</span></label>
                        <select name="sex" class="ds-select" required>
                            <option value="">Select Gender</option>
                            <option value="male" <?= old('sex') === 'male' ? 'selected' : '' ?>>Male</option>
                            <option value="female" <?= old('sex') === 'female' ? 'selected' : '' ?>>Female</option>
                        </select>
                    </div>
                    <div>
                        <label class="ds-input-label">Birthdate <span style="color:var(--c-rose)">*</span></label>
                        <input type="date" name="birthdate" class="ds-input" value="<?= old('birthdate') ?>" required>
                    </div>
                    <div>
                        <label class="ds-input-label">Civil Status</label>
                        <select name="civil_status" class="ds-select">
                            <option value="">Select Status</option>
                            <?php foreach (['Single','Married','Widowed','Separated'] as $cs): ?>
                                <option value="<?= $cs ?>" <?= old('civil_status') === $cs ? 'selected' : '' ?>><?= $cs ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px;margin-top:14px">
                    <div>
                        <label class="ds-input-label">Occupation</label>
                        <input type="text" name="occupation" class="ds-input" value="<?= old('occupation') ?>" placeholder="e.g. Teacher">
                    </div>
                    <div>
                        <label class="ds-input-label">Citizenship</label>
                        <input type="text" name="citizenship" class="ds-input" value="<?= old('citizenship') ?? 'Filipino' ?>">
                    </div>
                    <div>
                        <label class="ds-input-label">Profile Picture</label>
                        <input type="file" name="profile_picture" class="ds-input" id="profile_picture" accept="image/*" style="padding:6px 12px">
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION 2: ADDRESS & HOUSEHOLD -->
        <div class="ds-card" style="margin-bottom:14px">
            <div class="ds-card-head">
                <div class="ds-card-title"><i class="fas fa-home"></i> Address &amp; Household</div>
            </div>
            <div class="ds-card-body">
                <div style="margin-bottom:14px">
                    <label class="ds-input-label">Street / House Number</label>
                    <input type="text" name="street_address" class="ds-input" value="<?= old('street_address') ?>" placeholder="e.g., Block 5 Lot 12, Phase 1">
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px">
                    <div>
                        <label class="ds-input-label">Sitio / Zone <span style="color:var(--c-rose)">*</span></label>
                        <select name="sitio" id="sitioSelect" class="ds-select" required>
                            <option value="">Select Sitio</option>
                            <?php foreach (['Purok Malipayon','Purok Masagana','Purok Cory','Purok Kawayan','Purok Pagla-um'] as $s): ?>
                                <option value="<?= $s ?>" <?= old('sitio') === $s ? 'selected' : '' ?>><?= $s ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="ds-input-label">Household (Optional)</label>
                        <select name="household_id" id="householdSelect" class="ds-select">
                            <option value="">Select Household</option>
                        </select>
                    </div>
                    <div>
                        <label class="ds-input-label">Relationship to Head</label>
                        <select name="relationship_to_head" class="ds-select">
                            <option value="" disabled selected>Select Relationship</option>
                            <?php
                            $currentRel = old('relationship_to_head', '');
                            $relOptions = ['Head','Spouse','Son','Daughter','Father','Mother','Grandfather','Grandmother','Grandson','Granddaughter','Brother','Sister','Uncle','Aunt','Nephew','Niece','Cousin','Son-in-law','Daughter-in-law','Brother-in-law','Sister-in-law','Other Relative','Non-Relative'];
                            foreach ($relOptions as $opt): ?>
                                <option value="<?= $opt ?>" <?= $currentRel == $opt ? 'selected' : '' ?>><?= $opt ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION 3: STATUS & FLAGS -->
        <div class="ds-card" style="margin-bottom:14px">
            <div class="ds-card-head">
                <div class="ds-card-title"><i class="fas fa-flag"></i> Government Status &amp; Benefits</div>
            </div>
            <div class="ds-card-body">
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px">
                    <label style="display:flex;align-items:flex-start;gap:10px;padding:14px;background:var(--c-blue-bg);border-radius:var(--r-sm);cursor:pointer;border:.5px solid transparent;transition:border .15s">
                        <input type="checkbox" name="is_voter" value="1" <?= old('is_voter') ? 'checked' : '' ?> style="margin-top:2px;accent-color:var(--c-blue)">
                        <div>
                            <div style="font-size:12px;font-weight:700;color:var(--c-blue)">Registered Voter</div>
                            <div style="font-size:10.5px;color:var(--ink-muted);margin-top:2px">Eligible to vote in elections</div>
                        </div>
                    </label>
                    <label style="display:flex;align-items:flex-start;gap:10px;padding:14px;background:var(--c-green-bg);border-radius:var(--r-sm);cursor:pointer;border:.5px solid transparent;transition:border .15s">
                        <input type="checkbox" name="is_senior_citizen" value="1" <?= old('is_senior_citizen') ? 'checked' : '' ?> style="margin-top:2px;accent-color:var(--c-green)">
                        <div>
                            <div style="font-size:12px;font-weight:700;color:var(--c-green)">Senior Citizen</div>
                            <div style="font-size:10.5px;color:var(--ink-muted);margin-top:2px">60+ years old eligible for benefits</div>
                        </div>
                    </label>
                    <label style="display:flex;align-items:flex-start;gap:10px;padding:14px;background:var(--c-amber-bg);border-radius:var(--r-sm);cursor:pointer;border:.5px solid transparent;transition:border .15s">
                        <input type="checkbox" name="is_pwd" value="1" <?= old('is_pwd') ? 'checked' : '' ?> style="margin-top:2px;accent-color:var(--c-amber)">
                        <div>
                            <div style="font-size:12px;font-weight:700;color:var(--c-amber)">PWD</div>
                            <div style="font-size:10.5px;color:var(--ink-muted);margin-top:2px">Person with Disability</div>
                        </div>
                    </label>
                </div>
            </div>
            <div style="padding:12px 18px;border-top:.5px solid var(--border);display:flex;align-items:center;justify-content:space-between">
                <span style="font-size:10.5px;color:var(--ink-soft)"><i class="fas fa-info-circle" style="margin-right:4px"></i> Required fields marked with <span style="color:var(--c-rose)">*</span></span>
                <div style="display:flex;gap:8px">
                    <button type="button" class="ds-btn ds-btn-ghost" onclick="window.history.back()">Cancel</button>
                    <button type="submit" class="ds-btn ds-btn-primary"><i class="fas fa-save"></i> Save Resident</button>
                </div>
            </div>
        </div>

    </form>
</div>

<script>
    var BASE_URL         = "<?= base_url() ?>";
    var CSRF_TOKEN_NAME  = "<?= csrf_token() ?>";
    var CSRF_TOKEN_VALUE = "<?= csrf_hash() ?>";
</script>
<script src="<?= base_url('js/residents/residents-create.js') ?>"></script>

<?= $this->endSection() ?>
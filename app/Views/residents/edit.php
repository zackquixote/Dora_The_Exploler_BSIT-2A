<?= $this->extend('theme/template') ?>
<?= $this->section('content') ?>
<?php $validationErrors = session()->getFlashdata('errors') ?? []; ?>

<div class="bmis-content">

    <!-- Premium Page Header -->
    <div class="bmis-page-header">
        <div class="bmis-page-title">
            <h1 style="font-weight: 800;"><i class="fas fa-user-edit text-primary"></i> Edit Resident Profile</h1>
            <p>Modifying profile for: <strong style="color:var(--ink)"><?= esc($resident['first_name'] . ' ' . $resident['last_name']) ?></strong></p>
        </div>
        <div class="bmis-page-actions">
            <a href="<?= base_url('resident') ?>" class="btn btn-light btn-sm rounded-pill px-3 fw-bold shadow-sm" style="border: 1px solid var(--border);"><i class="fas fa-arrow-left me-2"></i> Back to Directory</a>
        </div>
    </div>

    <!-- Errors -->
    <?php if (session()->getFlashdata('errors')): ?>
        <div style="background:var(--c-rose-bg);color:var(--c-rose);padding:12px 16px;border-radius:var(--r-sm);margin-bottom:14px;font-size:12px;font-weight:600">
            <i class="fas fa-exclamation-circle" style="margin-right:6px"></i> Please fix the following:
            <ul style="margin:6px 0 0 16px;padding:0"><?php foreach (session()->getFlashdata('errors') as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?></ul>
        </div>
    <?php endif; ?>

    <form id="residentForm" action="<?= base_url('resident/update/' . $resident['id']) ?>" method="POST" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <input type="hidden" name="_method" value="PUT">

        <!-- SECTION 1: BASIC INFO -->
        <div class="ds-card" style="margin-bottom:14px">
            <div class="ds-card-head">
                <div class="ds-card-title"><i class="fas fa-user-circle"></i> Basic Information</div>
            </div>
            <div class="ds-card-body">
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px">
                    <div>
                        <label class="ds-input-label">First Name <span style="color:var(--c-rose)">*</span></label>
                        <input type="text" name="first_name" class="ds-input name-only <?= isset($validationErrors['first_name']) ? 'ds-input-invalid' : '' ?>" value="<?= old('first_name', $resident['first_name']) ?>" required>
                        <?php if (isset($validationErrors['first_name'])): ?>
                            <div class="ds-error-feedback"><i class="fas fa-exclamation-circle"></i> <?= esc($validationErrors['first_name']) ?></div>
                        <?php endif; ?>
                    </div>
                    <div>
                        <label class="ds-input-label">Middle Name</label>
                        <input type="text" name="middle_name" class="ds-input name-only <?= isset($validationErrors['middle_name']) ? 'ds-input-invalid' : '' ?>" value="<?= old('middle_name', $resident['middle_name'] ?? '') ?>">
                        <?php if (isset($validationErrors['middle_name'])): ?>
                            <div class="ds-error-feedback"><i class="fas fa-exclamation-circle"></i> <?= esc($validationErrors['middle_name']) ?></div>
                        <?php endif; ?>
                    </div>
                    <div>
                        <label class="ds-input-label">Last Name <span style="color:var(--c-rose)">*</span></label>
                        <input type="text" name="last_name" class="ds-input name-only <?= isset($validationErrors['last_name']) ? 'ds-input-invalid' : '' ?>" value="<?= old('last_name', $resident['last_name']) ?>" required>
                        <?php if (isset($validationErrors['last_name'])): ?>
                            <div class="ds-error-feedback"><i class="fas fa-exclamation-circle"></i> <?= esc($validationErrors['last_name']) ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px;margin-top:14px">
                    <div>
                        <label class="ds-input-label">Gender <span style="color:var(--c-rose)">*</span></label>
                        <select name="sex" class="ds-select <?= isset($validationErrors['sex']) ? 'ds-select-invalid' : '' ?>" required>
                            <option value="">Select Gender</option>
                            <option value="male" <?= old('sex', $resident['sex'] ?? '') === 'male' ? 'selected' : '' ?>>Male</option>
                            <option value="female" <?= old('sex', $resident['sex'] ?? '') === 'female' ? 'selected' : '' ?>>Female</option>
                        </select>
                        <?php if (isset($validationErrors['sex'])): ?>
                            <div class="ds-error-feedback"><i class="fas fa-exclamation-circle"></i> <?= esc($validationErrors['sex']) ?></div>
                        <?php endif; ?>
                    </div>
                    <div>
                        <label class="ds-input-label">Birthdate <span style="color:var(--c-rose)">*</span></label>
                        <input type="date" name="birthdate" class="ds-input <?= isset($validationErrors['birthdate']) ? 'ds-input-invalid' : '' ?>" max="<?= date('Y-m-d') ?>" value="<?= old('birthdate', $resident['birthdate']) ?>" required>
                        <?php if (isset($validationErrors['birthdate'])): ?>
                            <div class="ds-error-feedback"><i class="fas fa-exclamation-circle"></i> <?= esc($validationErrors['birthdate']) ?></div>
                        <?php endif; ?>
                    </div>
                    <div>
                        <label class="ds-input-label">Civil Status</label>
                        <select name="civil_status" class="ds-select <?= isset($validationErrors['civil_status']) ? 'ds-select-invalid' : '' ?>">
                            <option value="">Select Status</option>
                            <?php foreach (['single' => 'Single','married' => 'Married','widowed' => 'Widowed','separated' => 'Separated'] as $val => $label): ?>
                                <option value="<?= $val ?>" <?= old('civil_status', $resident['civil_status'] ?? '') === $val ? 'selected' : '' ?>><?= $label ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($validationErrors['civil_status'])): ?>
                            <div class="ds-error-feedback"><i class="fas fa-exclamation-circle"></i> <?= esc($validationErrors['civil_status']) ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px;margin-top:14px">
                    <div>
                        <label class="ds-input-label">Contact Number</label>
                        <input type="text" name="contact_number" class="ds-input phone-only <?= isset($validationErrors['contact_number']) ? 'ds-input-invalid' : '' ?>" value="<?= old('contact_number', $resident['contact_number'] ?? '') ?>" placeholder="e.g. 09123456789" maxlength="20">
                        <?php if (isset($validationErrors['contact_number'])): ?>
                            <div class="ds-error-feedback"><i class="fas fa-exclamation-circle"></i> <?= esc($validationErrors['contact_number']) ?></div>
                        <?php endif; ?>
                    </div>
                    <div>
                        <label class="ds-input-label">Occupation</label>
                        <input type="text" name="occupation" class="ds-input <?= isset($validationErrors['occupation']) ? 'ds-input-invalid' : '' ?>" value="<?= old('occupation', $resident['occupation'] ?? '') ?>">
                        <?php if (isset($validationErrors['occupation'])): ?>
                            <div class="ds-error-feedback"><i class="fas fa-exclamation-circle"></i> <?= esc($validationErrors['occupation']) ?></div>
                        <?php endif; ?>
                    </div>
                    <div>
                        <label class="ds-input-label">Citizenship</label>
                        <input type="text" name="citizenship" class="ds-input <?= isset($validationErrors['citizenship']) ? 'ds-input-invalid' : '' ?>" value="<?= old('citizenship', $resident['citizenship'] ?? 'Filipino') ?>">
                        <?php if (isset($validationErrors['citizenship'])): ?>
                            <div class="ds-error-feedback"><i class="fas fa-exclamation-circle"></i> <?= esc($validationErrors['citizenship']) ?></div>
                        <?php endif; ?>
                    </div>
                    <div style="grid-column: 1 / -1">
                        <label class="ds-input-label">Profile Picture</label>
                        <div class="ds-photo-upload <?= isset($validationErrors['profile_picture']) ? 'ds-input-invalid' : '' ?>" id="photoDropZone">
                            <div id="photoPreviewWrap">
                                <?php if (!empty($resident['profile_picture'])): ?>
                                    <img src="<?= base_url('uploads/' . $resident['profile_picture']) ?>" class="ds-photo-preview" id="photoPreview">
                                <?php else: ?>
                                    <div class="ds-photo-preview empty" id="photoPreview">
                                        <i class="fas fa-camera"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="ds-photo-info">
                                <div class="title"><?= !empty($resident['profile_picture']) ? 'Change photo' : 'Upload a photo' ?></div>
                                <div class="subtitle">Drag & drop or click to browse. JPG, PNG up to 2MB.</div>
                                <div class="file-name" id="photoFileName" style="display:none"></div>
                            </div>
                            <input type="file" name="profile_picture" id="profile_picture" accept="image/*" style="display:none">
                            <button type="button" class="ds-btn ds-btn-ghost" id="photoBrowseBtn" style="height:32px;font-size:11px"><i class="fas fa-upload"></i> Browse</button>
                        </div>
                        <?php if (isset($validationErrors['profile_picture'])): ?>
                            <div class="ds-error-feedback"><i class="fas fa-exclamation-circle"></i> <?= esc($validationErrors['profile_picture']) ?></div>
                        <?php endif; ?>
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
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px">
                    <div>
                        <label class="ds-input-label">Sitio / Zone <span style="color:var(--c-rose)">*</span></label>
                        <select name="sitio" id="sitioSelect" class="ds-select" required>
                            <option value="">Select Sitio</option>
                            <?php foreach ($purokList as $s): ?>
                                <option value="<?= $s ?>" <?= old('sitio', $resident['sitio'] ?? '') === $s ? 'selected' : '' ?>><?= $s ?></option>
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
                            $currentRel = old('relationship_to_head', $resident['relationship_to_head'] ?? '');
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
                    <label style="display:flex;align-items:flex-start;gap:10px;padding:14px;background:var(--c-blue-bg);border-radius:var(--r-sm);cursor:pointer">
                        <input type="checkbox" name="is_voter" value="1" <?= !empty(old('is_voter', $resident['is_voter'])) ? 'checked' : '' ?> style="margin-top:2px;accent-color:var(--c-blue)">
                        <div>
                            <div style="font-size:12px;font-weight:700;color:var(--c-blue)">Registered Voter</div>
                            <div style="font-size:10.5px;color:var(--ink-muted);margin-top:2px">Eligible to vote in elections</div>
                        </div>
                    </label>
                    <label style="display:flex;align-items:flex-start;gap:10px;padding:14px;background:var(--c-green-bg);border-radius:var(--r-sm);cursor:pointer">
                        <input type="checkbox" name="is_senior_citizen" value="1" <?= !empty(old('is_senior_citizen', $resident['is_senior_citizen'])) ? 'checked' : '' ?> style="margin-top:2px;accent-color:var(--c-green)">
                        <div>
                            <div style="font-size:12px;font-weight:700;color:var(--c-green)">Senior Citizen</div>
                            <div style="font-size:10.5px;color:var(--ink-muted);margin-top:2px">60+ years old eligible for benefits</div>
                        </div>
                    </label>
                    <label style="display:flex;align-items:flex-start;gap:10px;padding:14px;background:var(--c-amber-bg);border-radius:var(--r-sm);cursor:pointer">
                        <input type="checkbox" name="is_pwd" value="1" <?= !empty(old('is_pwd', $resident['is_pwd'])) ? 'checked' : '' ?> style="margin-top:2px;accent-color:var(--c-amber)">
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
                    <button type="submit" class="ds-btn ds-btn-primary"><i class="fas fa-save"></i> Update Resident</button>
                </div>
            </div>
        </div>

    </form>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    var BASE_URL             = "<?= base_url() ?>";
    var CSRF_TOKEN_NAME      = "<?= csrf_token() ?>";
    var CSRF_TOKEN_VALUE     = "<?= csrf_hash() ?>";
    var CURRENT_SITIO        = "<?= esc($resident['sitio'] ?? '') ?>";
    var CURRENT_HOUSEHOLD_ID = "<?= esc($resident['household_id'] ?? '') ?>";
</script>
<script src="<?= base_url('js/shared/photo-upload.js') ?>"></script>
<script src="<?= base_url('js/residents/residents-edit.js') ?>"></script>
<script>
// ── Input restrictions ────────────────────────────────────────────────
(function () {
    $(document).on('keypress', '.name-only', function (e) {
        var ch = String.fromCharCode(e.which);
        if (!/[a-zA-ZÀ-ÿ\s'\-\.]/.test(ch)) { e.preventDefault(); }
    });
    $(document).on('input', '.name-only', function () {
        this.value = this.value.replace(/[0-9]/g, '');
    });

    $(document).on('keypress', '.phone-only', function (e) {
        var ch = String.fromCharCode(e.which);
        if (!/[\d\+\-\s\(\)]/.test(ch)) { e.preventDefault(); }
    });
    $(document).on('input', '.phone-only', function () {
        this.value = this.value.replace(/[^0-9\+\-\s\(\)]/g, '');
    });

    // ── Auto Senior Citizen ───────────────────────────────────────────
    var $birthdateInput = $('input[name="birthdate"]');
    var $seniorCheckbox = $('input[name="is_senior_citizen"]');
    var $seniorLabel    = $seniorCheckbox.closest('label');
    var $seniorNote     = $('<div id="senior-auto-note" style="font-size:10px;color:var(--c-green);font-weight:700;margin-top:4px;display:none"><i class="fas fa-magic" style="margin-right:3px"></i>Auto-marked (age 60+)</div>');
    $seniorLabel.append($seniorNote);

    function updateSeniorStatus() {
        var bd = $birthdateInput.val();
        if (!bd) return;

        var birth = new Date(bd);
        var today = new Date();
        var age   = today.getFullYear() - birth.getFullYear();
        var m     = today.getMonth() - birth.getMonth();
        if (m < 0 || (m === 0 && today.getDate() < birth.getDate())) age--;

        if (age >= 60) {
            $seniorCheckbox.prop('checked', true).prop('disabled', true);
            $seniorNote.show();
        } else {
            $seniorCheckbox.prop('disabled', false);
            if ($seniorNote.is(':visible')) {
                $seniorCheckbox.prop('checked', false);
                $seniorNote.hide();
            }
        }
    }

    $birthdateInput.on('change input', updateSeniorStatus);
    if ($birthdateInput.val()) updateSeniorStatus();
})();
</script>
<?= $this->endSection() ?>

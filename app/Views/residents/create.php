<?php
// ---------------------------------------------------------
// SMART THEME LOADER
// ---------------------------------------------------------
$role = strtolower(session()->get('role') ?? 'staff');
$template = ($role == 'admin') ? 'theme/admin/template' : 'theme/template';
?>

<?= $this->extend($template) ?>
<?= $this->section('content') ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Residents</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Create New</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="rc-container">
                <div class="rc-header">
                    <div class="rc-title">
                        <a href="<?= base_url('resident') ?>" class="text-muted text-decoration-none small">
                            &larr; Back to List
                        </a>
                        <h1>Create New Resident</h1>
                        <p>Fill in the details below to register a new resident.</p>
                    </div>
                    <div class="rc-header-actions">
                        <button type="button" class="rc-btn rc-btn-secondary" onclick="window.history.back()">
                            Cancel
                        </button>
                        <button type="submit" form="residentForm" class="rc-btn rc-btn-primary">
                            <i class="fas fa-save"></i> Save Resident
                        </button>
                    </div>
                </div>

                <?php if (session()->getFlashdata('errors')): ?>
                    <div class="rc-alert">
                        <div style="flex-shrink: 0;">
                            <i class="fas fa-exclamation-circle fa-2x"></i>
                        </div>
                        <div style="margin-left: 1rem;">
                            <h4 style="margin: 0 0 0.5rem 0;">Please fix the following errors:</h4>
                            <ul>
                                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                    <li><?= esc($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                <?php endif; ?>

                <form id="residentForm" action="<?= base_url('resident/store') ?>" method="POST" enctype="multipart/form-data">
                    <?= csrf_field() ?>

                    <!-- SECTION 1: BASIC INFO -->
                    <div class="rc-card">
                        <div class="rc-section-header">
                            <i class="fas fa-user-circle rc-section-icon"></i>
                            <h2 class="rc-section-title">Basic Information</h2>
                        </div>
                        <div class="rc-card-body">
                            <div class="rc-grid">
                                <div class="rc-field">
                                    <label class="rc-label">First Name <span>*</span></label>
                                    <input type="text" name="first_name" class="rc-input" value="<?= old('first_name') ?>" placeholder="e.g. Juan" required>
                                </div>
                                <div class="rc-field">
                                    <label class="rc-label">Middle Name</label>
                                    <input type="text" name="middle_name" class="rc-input" value="<?= old('middle_name') ?>" placeholder="e.g. Dela Cruz">
                                </div>
                                <div class="rc-field">
                                    <label class="rc-label">Last Name <span>*</span></label>
                                    <input type="text" name="last_name" class="rc-input" value="<?= old('last_name') ?>" placeholder="e.g. Santos" required>
                                </div>
                                <div class="rc-field">
                                    <label class="rc-label">Gender <span>*</span></label>
                                    <select name="sex" class="rc-select" required>
                                        <option value="">Select Gender</option>
                                        <option value="male" <?= old('sex') === 'male' ? 'selected' : '' ?>>Male</option>
                                        <option value="female" <?= old('sex') === 'female' ? 'selected' : '' ?>>Female</option>
                                    </select>
                                </div>
                                <div class="rc-field">
                                    <label class="rc-label">Birthdate <span>*</span></label>
                                    <input type="date" name="birthdate" class="rc-input" value="<?= old('birthdate') ?>" required>
                                </div>
                                <div class="rc-field">
                                    <label class="rc-label">Civil Status</label>
                                    <select name="civil_status" class="rc-select">
                                        <option value="">Select Status</option>
                                        <?php foreach (['Single', 'Married', 'Widowed', 'Separated'] as $cs): ?>
                                            <option value="<?= $cs ?>" <?= old('civil_status') === $cs ? 'selected' : '' ?>><?= $cs ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="rc-field">
                                    <label class="rc-label">Occupation</label>
                                    <input type="text" name="occupation" class="rc-input" value="<?= old('occupation') ?>" placeholder="e.g. Teacher">
                                </div>
                                <div class="rc-field">
                                    <label class="rc-label">Citizenship</label>
                                    <input type="text" name="citizenship" class="rc-input" value="<?= old('citizenship') ?? 'Filipino' ?>">
                                </div>
                                <div class="rc-field">
                                    <label class="rc-label">Profile Picture</label>
                                    <div class="rc-file-area">
                                        <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                                        <p class="text-muted small mb-0">Click to upload photo</p>
                                        <label for="profile_picture" class="rc-file-trigger">Choose File</label>
                                        <input type="file" name="profile_picture" id="profile_picture" accept="image/*">
                                        <div id="fileName" class="small text-muted mt-2"><?= old('profile_picture') ?: 'No file chosen' ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 2: ADDRESS -->
                    <div class="rc-card">
                        <div class="rc-section-header">
                            <i class="fas fa-home rc-section-icon" style="color: var(--success);"></i>
                            <h2 class="rc-section-title">Address & Household</h2>
                        </div>
                        <div class="rc-card-body">
                            <div class="rc-grid">
                                <div class="rc-field full-width">
                                    <label class="rc-label">Street / House Number</label>
                                    <input type="text" name="street_address" class="rc-input" value="<?= old('street_address') ?>" placeholder="e.g., Block 5 Lot 12, Phase 1">
                                </div>
                                <div class="rc-field">
                                    <label class="rc-label">Sitio / Zone <span>*</span></label>
                                    <select name="sitio" id="sitioSelect" class="rc-select" required>
                                        <option value="">Select Sitio</option>
                                        <option value="Purok Malipayon" <?= old('sitio') === 'Purok Malipayon' ? 'selected' : '' ?>>Purok Malipayon</option>
                                        <option value="Purok Masagana"  <?= old('sitio') === 'Purok Masagana'  ? 'selected' : '' ?>>Purok Masagana</option>
                                        <option value="Purok Cory"      <?= old('sitio') === 'Purok Cory'      ? 'selected' : '' ?>>Purok Cory</option>
                                        <option value="Purok Kawayan"   <?= old('sitio') === 'Purok Kawayan'   ? 'selected' : '' ?>>Purok Kawayan</option>
                                        <option value="Purok Pagla-um"  <?= old('sitio') === 'Purok Pagla-um'  ? 'selected' : '' ?>>Purok Pagla-um</option>
                                    </select>
                                </div>
                                <div class="rc-field">
                                    <label class="rc-label">Household (Optional)</label>
                                    <div style="position: relative;">
                                        <select name="household_id" id="householdSelect" class="rc-select">
                                            <option value="">Select Household</option>
                                        </select>
                                        <div id="householdLoading" class="spinner-border spinner-border-sm text-primary" style="position: absolute; right: 12px; top: 10px; display: none;" role="status"></div>
                                    </div>
                                </div>
                                <div class="rc-field">
                                    <label class="rc-label">Relationship to Head</label>
                                    <select name="relationship_to_head" class="rc-select">
                                        <option value="" disabled selected>Select Relationship</option>
                                        <?php 
                                        $currentRel = old('relationship_to_head', '');
                                        $relOptions = ['Head', 'Spouse', 'Son', 'Daughter', 'Father', 'Mother',
                                            'Grandfather', 'Grandmother', 'Grandson', 'Granddaughter',
                                            'Brother', 'Sister', 'Uncle', 'Aunt', 'Nephew', 'Niece',
                                            'Cousin', 'Son-in-law', 'Daughter-in-law', 'Brother-in-law',
                                            'Sister-in-law', 'Other Relative', 'Non-Relative'];
                                        foreach ($relOptions as $opt): ?>
                                            <option value="<?= $opt ?>" <?= $currentRel == $opt ? 'selected' : '' ?>><?= $opt ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 3: STATUS & FLAGS -->
                    <div class="rc-card">
                        <div class="rc-section-header">
                            <i class="fas fa-flag-checkered rc-section-icon" style="color: #F59E0B;"></i>
                            <h2 class="rc-section-title">Government Status & Benefits</h2>
                        </div>
                        <div class="rc-card-body">
                            <div class="rc-grid">
                                <label class="rc-checkbox-group">
                                    <div class="rc-checkbox">
                                        <input type="checkbox" name="is_voter" id="is_voter" value="1" <?= old('is_voter') ? 'checked' : '' ?>>
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" style="display:none;"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                    </div>
                                    <div class="rc-check-label">
                                        <h4>Registered Voter</h4>
                                        <p>Eligible to vote in elections</p>
                                    </div>
                                </label>
                                <label class="rc-checkbox-group">
                                    <div class="rc-checkbox">
                                        <input type="checkbox" name="is_senior_citizen" id="is_senior_citizen" value="1" <?= old('is_senior_citizen') ? 'checked' : '' ?>>
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" style="display:none;"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                    </div>
                                    <div class="rc-check-label">
                                        <h4>Senior Citizen</h4>
                                        <p>60+ years old eligible for benefits</p>
                                    </div>
                                </label>
                                <label class="rc-checkbox-group">
                                    <div class="rc-checkbox">
                                        <input type="checkbox" name="is_pwd" id="is_pwd" value="1" <?= old('is_pwd') ? 'checked' : '' ?>>
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" style="display:none;"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                    </div>
                                    <div class="rc-check-label">
                                        <h4>PWD</h4>
                                        <p>Person with Disability</p>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Footer Actions -->
                    <div class="rc-card">
                        <div class="rc-footer">
                            <small class="text-muted">
                                <i class="fas fa-info-circle mr-1"></i> Required fields are marked with <span style="color: var(--danger)">*</span>
                            </small>
                            <div style="display: flex; gap: 0.75rem;">
                                <button type="button" class="rc-btn rc-btn-secondary" onclick="window.history.back()">Cancel</button>
                                <button type="submit" class="rc-btn rc-btn-primary"><i class="fas fa-save"></i> Save Resident</button>
                            </div>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </section>
</div>

<!-- Script Variables -->
<script>
    var BASE_URL            = "<?= base_url() ?>";
    var CSRF_TOKEN_NAME     = "<?= csrf_token() ?>";
    var CSRF_TOKEN_VALUE    = "<?= csrf_hash() ?>";
</script>
<script src="<?= base_url('js/residents/residents-create.js') ?>"></script>

<?= $this->endSection() ?>
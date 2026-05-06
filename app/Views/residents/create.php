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
                    <h1>Create New Resident</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('resident') ?>">Residents</a></li>
                        <li class="breadcrumb-item active">Create New</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <?php if (session()->getFlashdata('errors')): ?>
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h5><i class="icon fas fa-ban"></i> Please fix the following errors:</h5>
                    <ul>
                        <?php foreach (session()->getFlashdata('errors') as $error): ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form id="residentForm" action="<?= base_url('resident/store') ?>" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>

                <!-- SECTION 1: BASIC INFO -->
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-user-circle mr-1"></i> Basic Information</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label>First Name <span class="text-danger">*</span></label>
                                <input type="text" name="first_name" class="form-control" value="<?= old('first_name') ?>" placeholder="e.g. Juan" required>
                            </div>
                            <div class="col-md-4 form-group">
                                <label>Middle Name</label>
                                <input type="text" name="middle_name" class="form-control" value="<?= old('middle_name') ?>" placeholder="e.g. Dela Cruz">
                            </div>
                            <div class="col-md-4 form-group">
                                <label>Last Name <span class="text-danger">*</span></label>
                                <input type="text" name="last_name" class="form-control" value="<?= old('last_name') ?>" placeholder="e.g. Santos" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label>Gender <span class="text-danger">*</span></label>
                                <select name="sex" class="custom-select" required>
                                    <option value="">Select Gender</option>
                                    <option value="male" <?= old('sex') === 'male' ? 'selected' : '' ?>>Male</option>
                                    <option value="female" <?= old('sex') === 'female' ? 'selected' : '' ?>>Female</option>
                                </select>
                            </div>
                            <div class="col-md-4 form-group">
                                <label>Birthdate <span class="text-danger">*</span></label>
                                <input type="date" name="birthdate" class="form-control" value="<?= old('birthdate') ?>" required>
                            </div>
                            <div class="col-md-4 form-group">
                                <label>Civil Status</label>
                                <select name="civil_status" class="custom-select">
                                    <option value="">Select Status</option>
                                    <?php foreach (['Single', 'Married', 'Widowed', 'Separated'] as $cs): ?>
                                        <option value="<?= $cs ?>" <?= old('civil_status') === $cs ? 'selected' : '' ?>><?= $cs ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label>Occupation</label>
                                <input type="text" name="occupation" class="form-control" value="<?= old('occupation') ?>" placeholder="e.g. Teacher">
                            </div>
                            <div class="col-md-4 form-group">
                                <label>Citizenship</label>
                                <input type="text" name="citizenship" class="form-control" value="<?= old('citizenship') ?? 'Filipino' ?>">
                            </div>
                            <div class="col-md-4 form-group">
                                <label>Profile Picture</label>
                                <div class="custom-file">
                                    <input type="file" name="profile_picture" class="custom-file-input" id="profile_picture" accept="image/*">
                                    <label class="custom-file-label" for="profile_picture">Choose file</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SECTION 2: ADDRESS -->
                <div class="card card-success card-outline">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-home mr-1"></i> Address & Household</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12 form-group">
                                <label>Street / House Number</label>
                                <input type="text" name="street_address" class="form-control" value="<?= old('street_address') ?>" placeholder="e.g., Block 5 Lot 12, Phase 1">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label>Sitio / Zone <span class="text-danger">*</span></label>
                                <select name="sitio" id="sitioSelect" class="custom-select" required>
                                    <option value="">Select Sitio</option>
                                    <option value="Purok Malipayon" <?= old('sitio') === 'Purok Malipayon' ? 'selected' : '' ?>>Purok Malipayon</option>
                                    <option value="Purok Masagana"  <?= old('sitio') === 'Purok Masagana'  ? 'selected' : '' ?>>Purok Masagana</option>
                                    <option value="Purok Cory"      <?= old('sitio') === 'Purok Cory'      ? 'selected' : '' ?>>Purok Cory</option>
                                    <option value="Purok Kawayan"   <?= old('sitio') === 'Purok Kawayan'   ? 'selected' : '' ?>>Purok Kawayan</option>
                                    <option value="Purok Pagla-um"  <?= old('sitio') === 'Purok Pagla-um'  ? 'selected' : '' ?>>Purok Pagla-um</option>
                                </select>
                            </div>
                            <div class="col-md-4 form-group">
                                <label>Household (Optional)</label>
                                <div class="input-group">
                                    <select name="household_id" id="householdSelect" class="custom-select">
                                        <option value="">Select Household</option>
                                    </select>
                                    <div class="input-group-append" id="householdLoading" style="display: none;">
                                        <span class="input-group-text"><i class="fas fa-spinner fa-spin"></i></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 form-group">
                                <label>Relationship to Head</label>
                                <select name="relationship_to_head" class="custom-select">
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
                <div class="card card-warning card-outline">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-flag-checkered mr-1"></i> Government Status & Benefits</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="custom-control custom-checkbox">
                                    <input class="custom-control-input custom-control-input-primary custom-control-input-outline" type="checkbox" name="is_voter" id="is_voter" value="1" <?= old('is_voter') ? 'checked' : '' ?>>
                                    <label for="is_voter" class="custom-control-label">Registered Voter</label>
                                    <p class="text-muted small">Eligible to vote in elections</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="custom-control custom-checkbox">
                                    <input class="custom-control-input custom-control-input-success custom-control-input-outline" type="checkbox" name="is_senior_citizen" id="is_senior_citizen" value="1" <?= old('is_senior_citizen') ? 'checked' : '' ?>>
                                    <label for="is_senior_citizen" class="custom-control-label">Senior Citizen</label>
                                    <p class="text-muted small">60+ years old eligible for benefits</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="custom-control custom-checkbox">
                                    <input class="custom-control-input custom-control-input-info custom-control-input-outline" type="checkbox" name="is_pwd" id="is_pwd" value="1" <?= old('is_pwd') ? 'checked' : '' ?>>
                                    <label for="is_pwd" class="custom-control-label">PWD</label>
                                    <p class="text-muted small">Person with Disability</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <small class="text-muted">
                            <i class="fas fa-info-circle mr-1"></i> Required fields are marked with <span class="text-danger">*</span>
                        </small>
                        <div class="float-right">
                            <button type="button" class="btn btn-default mr-2" onclick="window.history.back()">Cancel</button>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Save Resident</button>
                        </div>
                    </div>
                </div>

            </form>
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
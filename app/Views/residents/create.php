<?= $this->extend('theme/template') ?>
<?= $this->section('content') ?>

<div class="content-wrapper bg-light min-vh-100 p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="<?= base_url('resident') ?>" class="text-muted small text-decoration-none">
                <i class="fas fa-arrow-left"></i> Back to Resident List
            </a>
            <h2 class="font-weight-bold mt-1">Add New Resident</h2>
            <p class="text-muted">Register a new person into the barangay database.</p>
        </div>
        <div>
            <button type="button" class="btn btn-outline-secondary mr-2 px-4 shadow-sm" onclick="window.history.back()">
                <i class="fas fa-times mr-1"></i> Cancel
            </button>
            <button type="submit" form="residentForm" class="btn btn-primary px-4 shadow-sm bg-navy border-0">
                <i class="fas fa-save mr-1"></i> Save Resident
            </button>
        </div>
    </div>

    <!-- Display validation errors -->
    <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <h6><i class="fas fa-exclamation-triangle mr-2"></i> Please fix the following errors:</h6>
            <ul class="mb-0 small">
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <form id="residentForm" action="<?= base_url('resident/store') ?>" method="POST" enctype="multipart/form-data">
        <?= csrf_field() ?>

        <!-- BASIC INFO -->
        <div class="card shadow-sm border-0 rounded-lg mb-4">
            <div class="card-header bg-white border-0 pt-4 px-4">
                <h5 class="mb-0 font-weight-bold"><i class="fas fa-user-circle mr-2 text-primary"></i> Basic Information</h5>
            </div>
            <div class="card-body p-4">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="small font-weight-bold text-secondary">First Name <span class="text-danger">*</span></label>
                        <input type="text" name="first_name" class="form-control form-control-lg bg-light border-0" value="<?= old('first_name') ?>" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="small font-weight-bold text-secondary">Middle Name</label>
                        <input type="text" name="middle_name" class="form-control form-control-lg bg-light border-0" value="<?= old('middle_name') ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="small font-weight-bold text-secondary">Last Name <span class="text-danger">*</span></label>
                        <input type="text" name="last_name" class="form-control form-control-lg bg-light border-0" value="<?= old('last_name') ?>" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="small font-weight-bold text-secondary">Gender <span class="text-danger">*</span></label>
                        <select name="sex" class="form-control form-control-lg bg-light border-0" required>
                            <option value="" disabled selected>Select gender</option>
                            <option value="male" <?= old('sex') == 'male' ? 'selected' : '' ?>>Male</option>
                            <option value="female" <?= old('sex') == 'female' ? 'selected' : '' ?>>Female</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="small font-weight-bold text-secondary">Occupation</label>
                        <input type="text" name="occupation" class="form-control form-control-lg bg-light border-0" value="<?= old('occupation') ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="small font-weight-bold text-secondary">Citizenship</label>
                        <input type="text" name="citizenship" class="form-control form-control-lg bg-light border-0" value="<?= old('citizenship', 'Filipino') ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="small font-weight-bold text-secondary">Birthdate <span class="text-danger">*</span></label>
                        <input type="date" name="birthdate" class="form-control form-control-lg bg-light border-0" value="<?= old('birthdate') ?>" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="small font-weight-bold text-secondary">Civil Status</label>
                        <select name="civil_status" class="form-control form-control-lg bg-light border-0">
                            <option value="" disabled selected>Select status</option>
                            <option value="single" <?= old('civil_status') == 'single' ? 'selected' : '' ?>>Single</option>
                            <option value="married" <?= old('civil_status') == 'married' ? 'selected' : '' ?>>Married</option>
                            <option value="widowed" <?= old('civil_status') == 'widowed' ? 'selected' : '' ?>>Widowed</option>
                            <option value="separated" <?= old('civil_status') == 'separated' ? 'selected' : '' ?>>Separated</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="small font-weight-bold text-secondary">Profile Picture</label>
                        <input type="file" name="profile_picture" class="form-control form-control-lg bg-light border-0" accept="image/*">
                        <small class="text-muted">Accepted formats: JPG, PNG, GIF (Max: 2MB)</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- ADDRESS & HOUSEHOLD INFO -->
        <div class="card shadow-sm border-0 rounded-lg mb-4">
            <div class="card-header bg-white border-0 pt-4 px-4">
                <h5 class="mb-0 font-weight-bold"><i class="fas fa-home mr-2 text-success"></i> Address & Household</h5>
            </div>
            <div class="card-body p-4">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="small font-weight-bold text-secondary">Sitio / Purok <span class="text-danger">*</span></label>
                        <select name="sitio" id="sitioSelect" class="form-control form-control-lg bg-light border-0" required>
                            <option value="" disabled selected>Select Sitio</option>
                            <option value="Purok Malipayon" <?= old('sitio') == 'Purok Malipayon' ? 'selected' : '' ?>>Purok Malipayon</option>
                            <option value="Purok Masagana" <?= old('sitio') == 'Purok Masagana' ? 'selected' : '' ?>>Purok Masagana</option>
                            <option value="Purok Cory" <?= old('sitio') == 'Purok Cory' ? 'selected' : '' ?>>Purok Cory</option>
                            <option value="Purok Kawayan" <?= old('sitio') == 'Purok Kawayan' ? 'selected' : '' ?>>Purok Kawayan</option>
                            <option value="Purok Pagla-um" <?= old('sitio') == 'Purok Pagla-um' ? 'selected' : '' ?>>Purok Pagla-um</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="small font-weight-bold text-secondary">Household <span class="text-danger">*</span></label>
                        <select name="household_id" id="householdSelect" class="form-control form-control-lg bg-light border-0" required disabled>
                            <option value="" disabled selected>First select a Sitio</option>
                        </select>
                        <!-- Loading indicator -->
                        <div id="householdLoading" class="spinner-border spinner-border-sm text-primary ml-2" style="display:none;" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                    
                    <!-- UPDATED: Relationship to Head as Dropdown -->
                    <div class="col-md-4 mb-3">
                        <label class="small font-weight-bold text-secondary">Relationship to Head</label>
                        <select name="relationship_to_head" class="form-control form-control-lg bg-light border-0" required>
                            <option value="" disabled selected>Select Relationship</option>
                            <?php 
                            // Options derived from your requested JS array
                            $relOptions = [
                                'Head', 'Spouse', 'Son', 'Daughter', 'Father', 'Mother',
                                'Grandfather', 'Grandmother', 'Grandson', 'Granddaughter',
                                'Brother', 'Sister', 'Uncle', 'Aunt', 'Nephew', 'Niece',
                                'Cousin', 'Son-in-law', 'Daughter-in-law', 'Brother-in-law',
                                'Sister-in-law', 'Other Relative', 'Non-Relative'
                            ];
                            foreach ($relOptions as $opt): ?>
                                <option value="<?= $opt ?>" <?= old('relationship_to_head') == $opt ? 'selected' : '' ?>><?= $opt ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="small font-weight-bold text-secondary">Contact Number</label>
                        <input type="text" name="contact_number" class="form-control form-control-lg bg-light border-0" value="<?= old('contact_number') ?>" placeholder="e.g., 09123456789">
                    </div>
                </div>
            </div>
        </div>

        <!-- STATUS & FLAGS -->
        <div class="card shadow-sm border-0 rounded-lg mb-4">
            <div class="card-header bg-white border-0 pt-4 px-4">
                <h5 class="mb-0 font-weight-bold"><i class="fas fa-flag-checkered mr-2 text-warning"></i> Resident Status & Flags</h5>
            </div>
            <div class="card-body p-4">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" name="is_voter" id="is_voter" value="1" <?= old('is_voter') == '1' ? 'checked' : '' ?>>
                            <label class="custom-control-label font-weight-bold" for="is_voter">
                                <i class="fas fa-check-circle text-success mr-1"></i> Registered Voter
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" name="is_senior_citizen" id="is_senior_citizen" value="1" <?= old('is_senior_citizen') == '1' ? 'checked' : '' ?>>
                            <label class="custom-control-label font-weight-bold" for="is_senior_citizen">
                                <i class="fas fa-user-graduate text-info mr-1"></i> Senior Citizen
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" name="is_pwd" id="is_pwd" value="1" <?= old('is_pwd') == '1' ? 'checked' : '' ?>>
                            <label class="custom-control-label font-weight-bold" for="is_pwd">
                                <i class="fas fa-wheelchair text-danger mr-1"></i> Person with Disability
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- FORM FOOTER -->
        <div class="card border-0 shadow-sm mt-5 mb-5">
            <div class="card-body d-flex justify-content-between align-items-center py-3 px-4 bg-white rounded">
                <small class="text-muted">
                    <i class="fas fa-info-circle mr-1"></i> All fields marked with <span class="text-danger">*</span> are required.
                </small>
                <div>
                    <button type="button" class="btn btn-outline-secondary mr-2 px-4" onclick="window.history.back()">
                        <i class="fas fa-times mr-1"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary px-4 bg-navy border-0">
                        <i class="fas fa-save mr-1"></i> Save Resident
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
.bg-navy { background-color: #03213b !important; }
.custom-checkbox .custom-control-input:checked ~ .custom-control-label::before {
    background-color: #03213b;
    border-color: #03213b;
}
.form-control:focus {
    box-shadow: none;
    border-color: #80bdff;
}
.card {
    transition: transform 0.2s;
}
.card:hover {
    transform: translateY(-2px);
}
</style>

<!-- Load script properly using DOMContentLoaded -->
<script>
    // Define global variables first
    var BASE_URL         = "<?= base_url() ?>";
    var CSRF_TOKEN_NAME  = "<?= csrf_token() ?>";
    var CSRF_TOKEN_VALUE = "<?= csrf_hash() ?>";
    
    document.addEventListener("DOMContentLoaded", function(event) {
        if (typeof jQuery !== 'undefined') {
            console.log('jQuery found, loading residents-create.js');
            var script = document.createElement('script');
            script.src = BASE_URL + 'js/residents/residents-create.js';
            document.body.appendChild(script);
        } else {
            console.error('jQuery is not loaded. Script execution skipped.');
        }
    });
</script>

<?= $this->endSection() ?>
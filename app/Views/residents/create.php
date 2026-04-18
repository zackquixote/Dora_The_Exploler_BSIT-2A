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
            <button type="button" class="btn btn-outline-secondary mr-2 px-4 shadow-sm" onclick="window.history.back()">✕ Cancel</button>
            <button type="submit" form="residentForm" class="btn btn-primary px-4 shadow-sm bg-navy border-0">
                <i class="fas fa-save mr-1"></i> Save Resident
            </button>
        </div>
    </div>

<a href="<?= base_url('resident') ?>" class="btn btn-secondary">Cancel</a>
        <?= csrf_field() ?>

        <!-- BASIC INFO -->
        <div class="card shadow-sm border-0 rounded-lg mb-4">
            <div class="card-body p-4">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="small font-weight-bold">First Name</label>
                        <input type="text" name="first_name" class="form-control form-control-lg bg-light border-0" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="small font-weight-bold">Middle Name</label>
                        <input type="text" name="middle_name" class="form-control form-control-lg bg-light border-0">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="small font-weight-bold">Last Name</label>
                        <input type="text" name="last_name" class="form-control form-control-lg bg-light border-0" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="small font-weight-bold">Gender</label>
                        <select name="sex" class="form-control form-control-lg bg-light border-0">
                            <option disabled selected>Select gender</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="small font-weight-bold">Occupation</label>
                        <input type="text" name="occupation" class="form-control form-control-lg bg-light border-0">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="small font-weight-bold">Citizenship</label>
                        <input type="text" name="citizenship" class="form-control form-control-lg bg-light border-0">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="small font-weight-bold">Birthdate</label>
                        <input type="date" name="birthdate" class="form-control form-control-lg bg-light border-0" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="small font-weight-bold">Civil Status</label>
                        <select name="civil_status" class="form-control form-control-lg bg-light border-0">
                            <option disabled selected>Select status</option>
                            <option value="Single">Single</option>
                            <option value="Married">Married</option>
                            <option value="Widowed">Widowed</option>
                            <option value="Separated">Separated</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="small font-weight-bold">Profile Picture</label>
                        <input type="file" name="profile_picture" class="form-control form-control-lg bg-light border-0">
                    </div>
                </div>
            </div>
        </div>

        <!-- ADDRESS -->
        <div class="card shadow-sm border-0 rounded-lg mb-4">
            <div class="card-body p-4">
                <div class="row">
                    <div class="col-md-8 mb-3">
                        <label class="small font-weight-bold">Street / House Number</label>
                        <input type="text" name="street_address" class="form-control form-control-lg bg-light border-0">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="small font-weight-bold">Sitio / Zone</label>
                        <select name="sitio" class="form-control form-control-lg bg-light border-0" required>
                            <option disabled selected>Select Sitio</option>
                            <option value="Purok Malipayon">Purok Malipayon</option>
                            <option value="Purok Masagana">Purok Masagana</option>
                            <option value="Purok Cory">Purok Cory</option>
                            <option value="Purok Kawayan">Purok Kawayan</option>
                            <option value="Purok Pagla-um">Purok Pagla-um</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="small font-weight-bold">Household</label>
                        <select name="household_id" class="form-control form-control-lg bg-light border-0">
                            <option disabled selected>Select Household</option>
                            <?php foreach($households as $h): ?>
                                <option value="<?= $h['id'] ?>">#<?= $h['household_no'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="small font-weight-bold">Relationship to Head</label>
                        <input type="text" name="relationship_to_head" class="form-control form-control-lg bg-light border-0">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="small font-weight-bold">Contact Number</label>
                        <input type="text" name="contact_number" class="form-control form-control-lg bg-light border-0">
                    </div>
                </div>
            </div>
        </div>

        <!-- STATUS -->
        <h6 class="font-weight-bold mb-3"><i class="fas fa-stream mr-2"></i> Resident Status & Flags</h6>
        <div class="row">
            <div class="col-md-4 mb-3">
                <div class="card shadow-sm border-0 p-2">
                    <div class="card-body d-flex align-items-start">
                        <input type="checkbox" name="is_voter" class="mr-2 mt-1" value="1">
                        <p class="mb-0 font-weight-bold small">Registered Voter</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card shadow-sm border-0 p-2">
                    <div class="card-body d-flex align-items-start">
                        <input type="checkbox" name="is_senior_citizen" class="mr-2 mt-1" value="1">
                        <p class="mb-0 font-weight-bold small">Senior Citizen</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card shadow-sm border-0 p-2">
                    <div class="card-body d-flex align-items-start">
                        <input type="checkbox" name="is_pwd" class="mr-2 mt-1" value="1">
                        <p class="mb-0 font-weight-bold small">Person with Disability</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- FOOTER -->
        <div class="card border-0 shadow-sm mt-5 mb-5">
            <div class="card-body d-flex justify-content-between align-items-center py-3 px-4">
                <small class="text-muted">All fields are saved locally until submitted.</small>
                <div>
                    <button type="button" class="btn btn-outline-secondary mr-2" onclick="window.history.back()">Cancel</button>
                    <button type="submit" class="btn btn-primary bg-navy border-0">Save Resident</button>
                </div>
            </div>
        </div>
    </form>
</div>

<style>.bg-navy { background-color: #03213b !important; }</style>

<?= $this->endSection() ?>
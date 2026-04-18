<?= $this->extend('theme/template') ?>
<?= $this->section('content') ?>

<div class="content-wrapper bg-light min-vh-100 p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="<?= base_url('staff/residents') ?>" class="text-muted small text-decoration-none">
                <i class="fas fa-arrow-left"></i> Back to Resident List
            </a>
            <h2 class="font-weight-bold mt-1">Edit Resident</h2>
            <p class="text-muted">
                Modifying profile for: <strong><?= esc($resident['first_name'] . ' ' . $resident['last_name']) ?></strong>
            </p>
        </div>

        <div>
            <button type="button" class="btn btn-outline-secondary mr-2 px-4 shadow-sm"
                onclick="window.history.back()">✕ Cancel</button>
            <button type="submit" form="residentForm"
                class="btn btn-primary px-4 shadow-sm bg-navy border-0">
                <i class="fas fa-save mr-1"></i> Update Resident
            </button>
        </div>
    </div>

    <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger border-0 shadow-sm mb-4">
            <ul class="mb-0 small">
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form id="residentForm"
        action="<?= base_url('resident/update/' . $resident['id']) ?>"
        method="POST"
        enctype="multipart/form-data">
        <a href="<?= base_url('resident') ?>" class="btn btn-secondary">Cancel</a>


        <?= csrf_field() ?>

        <!-- BASIC INFO -->
        <div class="card shadow-sm border-0 rounded-lg mb-4">
            <div class="card-body p-4">

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="small font-weight-bold">First Name</label>
                        <input type="text" name="first_name"
                            class="form-control form-control-lg bg-light border-0"
                            value="<?= esc($resident['first_name']) ?>" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="small font-weight-bold">Middle Name</label>
                        <input type="text" name="middle_name"
                            class="form-control form-control-lg bg-light border-0"
                            value="<?= esc($resident['middle_name'] ?? '') ?>">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="small font-weight-bold">Last Name</label>
                        <input type="text" name="last_name"
                            class="form-control form-control-lg bg-light border-0"
                            value="<?= esc($resident['last_name']) ?>" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="small font-weight-bold">Gender</label>
                        <select name="sex" class="form-control form-control-lg bg-light border-0">
                            <option value="male" <?= ($resident['sex'] ?? '') === 'male' ? 'selected' : '' ?>>Male</option>
                            <option value="female" <?= ($resident['sex'] ?? '') === 'female' ? 'selected' : '' ?>>Female</option>
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="small font-weight-bold">Occupation</label>
                        <input type="text" name="occupation"
                            class="form-control form-control-lg bg-light border-0"
                            value="<?= esc($resident['occupation'] ?? '') ?>">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="small font-weight-bold">Citizenship</label>
                        <input type="text" name="citizenship"
                            class="form-control form-control-lg bg-light border-0"
                            value="<?= esc($resident['citizenship'] ?? '') ?>">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="small font-weight-bold">Birthdate</label>
                        <input type="date" name="birthdate"
                            class="form-control form-control-lg bg-light border-0"
                            value="<?= esc($resident['birthdate']) ?>" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="small font-weight-bold">Civil Status</label>
                        <select name="civil_status" class="form-control form-control-lg bg-light border-0">
                            <?php foreach (['Single', 'Married', 'Widowed', 'Separated'] as $cs): ?>
                                <option value="<?= $cs ?>"
                                    <?= ($resident['civil_status'] ?? '') === $cs ? 'selected' : '' ?>>
                                    <?= $cs ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="small font-weight-bold">Profile Picture</label>
                        <input type="file" name="profile_picture"
                            class="form-control form-control-lg bg-light border-0">

                        <?php if (!empty($resident['profile_picture'])): ?>
                            <div class="mt-2">
                                <small class="text-muted">Current photo:</small><br>
                                <img src="<?= base_url('uploads/' . $resident['profile_picture']) ?>"
                                    height="60" class="rounded shadow-sm mt-1">
                            </div>
                        <?php endif; ?>
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
                        <input type="text" name="street_address"
                            class="form-control form-control-lg bg-light border-0"
                            value="<?= esc($resident['street_address'] ?? '') ?>">
                    </div>

                    <!-- ✅ FIXED ENUM SITIO -->
                    <div class="col-md-4 mb-3">
                        <label class="small font-weight-bold">Sitio / Zone</label>
                        <select name="sitio" class="form-control form-control-lg bg-light border-0">
                            <option disabled>Select Sitio</option>

                            <option value="Purok Malipayon"
                                <?= ($resident['sitio'] ?? '') === 'Purok Malipayon' ? 'selected' : '' ?>>
                                Purok Malipayon
                            </option>

                            <option value="Purok Masagana"
                                <?= ($resident['sitio'] ?? '') === 'Purok Masagana' ? 'selected' : '' ?>>
                                Purok Masagana
                            </option>

                            <option value="Purok Cory"
                                <?= ($resident['sitio'] ?? '') === 'Purok Cory' ? 'selected' : '' ?>>
                                Purok Cory
                            </option>

                            <option value="Purok Kawayan"
                                <?= ($resident['sitio'] ?? '') === 'Purok Kawayan' ? 'selected' : '' ?>>
                                Purok Kawayan
                            </option>

                            <option value="Purok Pagla-um"
                                <?= ($resident['sitio'] ?? '') === 'Purok Pagla-um' ? 'selected' : '' ?>>
                                Purok Pagla-um
                            </option>
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="small font-weight-bold">Household</label>
                        <select name="household_id" class="form-control form-control-lg bg-light border-0">
                            <option disabled>Select Household</option>
                            <?php foreach ($households as $h): ?>
                                <option value="<?= $h['id'] ?>"
                                    <?= ($resident['household_id'] ?? '') == $h['id'] ? 'selected' : '' ?>>
                                    #<?= $h['household_no'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="small font-weight-bold">Relationship to Head</label>
                        <input type="text" name="relationship_to_head"
                            class="form-control form-control-lg bg-light border-0"
                            value="<?= esc($resident['relationship_to_head'] ?? '') ?>">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="small font-weight-bold">Contact Number</label>
                        <input type="text" name="contact_number"
                            class="form-control form-control-lg bg-light border-0"
                            value="<?= esc($resident['contact_number'] ?? '') ?>">
                    </div>
                </div>
            </div>
        </div>

        <!-- STATUS -->
        <h6 class="font-weight-bold mb-3"><i class="fas fa-stream mr-2"></i> Resident Status & Flags</h6>

        <div class="row">
            <div class="col-md-4 mb-3">
                <input type="checkbox" name="is_voter" value="1"
                    <?= !empty($resident['is_voter']) ? 'checked' : '' ?>>
                Registered Voter
            </div>

            <div class="col-md-4 mb-3">
                <input type="checkbox" name="is_senior_citizen" value="1"
                    <?= !empty($resident['is_senior_citizen']) ? 'checked' : '' ?>>
                Senior Citizen
            </div>

            <div class="col-md-4 mb-3">
                <input type="checkbox" name="is_pwd" value="1"
                    <?= !empty($resident['is_pwd']) ? 'checked' : '' ?>>
                Person with Disability
            </div>
        </div>

        <!-- FOOTER -->
        <div class="card border-0 shadow-sm mt-5 mb-5 bg-white">
            <div class="card-body d-flex justify-content-between align-items-center py-3 px-4">
                <small class="text-muted">All fields are saved locally until submitted.</small>
                <div>
                    <button type="button" class="btn btn-outline-secondary mr-2"
                        onclick="window.history.back()">Cancel</button>
                    <button type="submit" class="btn btn-primary bg-navy border-0">
                        Update Resident
                    </button>
                </div>
            </div>
        </div>

    </form>
</div>

<style>
    .bg-navy { background-color: #03213b !important; }
</style>

<?= $this->endSection() ?>
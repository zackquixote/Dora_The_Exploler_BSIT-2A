<?= $this->extend('theme/template') ?>
<?= $this->section('content') ?>

<div class="content-wrapper bg-light min-vh-100 p-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="<?= base_url('staff/residents') ?>" class="text-muted small text-decoration-none">
                <i class="fas fa-arrow-left"></i> Back to Resident List
            </a>
            <h2 class="font-weight-bold mt-1">Resident Profile</h2>
            <p class="text-muted">Viewing details for: <strong><?= esc($resident['first_name'] . ' ' . $resident['last_name']) ?></strong></p>
        </div>
        <div>
            <a href="<?= base_url('staff/residents/edit/' . $resident['id']) ?>" class="btn btn-warning px-4 shadow-sm border-0">
                <i class="fas fa-edit mr-1"></i> Edit Resident
            </a>
        </div>
    </div>

    <div class="row">

        <!-- LEFT: Photo + Tags -->
        <div class="col-md-3 mb-4">
            <div class="card shadow-sm border-0 rounded-lg text-center p-4">
                <?php if (!empty($resident['profile_picture'])): ?>
                    <img src="<?= base_url('uploads/' . $resident['profile_picture']) ?>" class="rounded-circle shadow mb-3" style="width:100px;height:100px;object-fit:cover;">
                <?php else: ?>
                    <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center mx-auto mb-3" style="width:100px;height:100px;">
                        <i class="fas fa-user fa-3x text-white"></i>
                    </div>
                <?php endif; ?>

                <h5 class="font-weight-bold mb-0"><?= esc($resident['first_name'] . ' ' . ($resident['middle_name'] ?? '') . ' ' . $resident['last_name']) ?></h5>
                <small class="text-muted"><?= esc($resident['occupation'] ?? 'No occupation listed') ?></small>

                <hr>

                <div class="d-flex flex-wrap justify-content-center">
                    <?php if (!empty($resident['is_voter'])): ?>
                        <span class="badge badge-primary m-1">Voter</span>
                    <?php endif; ?>
                    <?php if (!empty($resident['is_senior_citizen'])): ?>
                        <span class="badge badge-warning m-1">Senior Citizen</span>
                    <?php endif; ?>
                    <?php if (!empty($resident['is_pwd'])): ?>
                        <span class="badge badge-info m-1">PWD</span>
                    <?php endif; ?>
                    <?php if (empty($resident['is_voter']) && empty($resident['is_senior_citizen']) && empty($resident['is_pwd'])): ?>
                        <small class="text-muted">No special tags</small>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- RIGHT: Details -->
        <div class="col-md-9">

            <!-- Basic Info -->
            <div class="card shadow-sm border-0 rounded-lg mb-4">
                <div class="card-body p-4">
                    <div class="d-flex mb-3">
                        <div class="bg-light rounded p-3 mr-3 d-flex align-items-center justify-content-center" style="width:50px;height:50px;">
                            <i class="fas fa-user text-muted"></i>
                        </div>
                        <div>
                            <h6 class="font-weight-bold mb-0">Basic Information</h6>
                            <small class="text-muted">Legal name and identity details.</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <small class="text-muted d-block">First Name</small>
                            <strong><?= esc($resident['first_name']) ?></strong>
                        </div>
                        <div class="col-md-4 mb-3">
                            <small class="text-muted d-block">Middle Name</small>
                            <strong><?= esc($resident['middle_name'] ?? '—') ?></strong>
                        </div>
                        <div class="col-md-4 mb-3">
                            <small class="text-muted d-block">Last Name</small>
                            <strong><?= esc($resident['last_name']) ?></strong>
                        </div>
                        <div class="col-md-4 mb-3">
                            <small class="text-muted d-block">Gender</small>
                            <strong><?= esc(ucfirst($resident['sex'])) ?></strong>
                        </div>
                        <div class="col-md-4 mb-3">
                            <small class="text-muted d-block">Birthdate</small>
                            <strong><?= esc($resident['birthdate']) ?></strong>
                        </div>
                        <div class="col-md-4 mb-3">
                            <small class="text-muted d-block">Civil Status</small>
                            <strong><?= esc($resident['civil_status']) ?></strong>
                        </div>
                        <div class="col-md-4 mb-3">
                            <small class="text-muted d-block">Occupation</small>
                            <strong><?= esc($resident['occupation'] ?? '—') ?></strong>
                        </div>
                        <div class="col-md-4 mb-3">
                            <small class="text-muted d-block">Citizenship</small>
                            <strong><?= esc($resident['citizenship'] ?? '—') ?></strong>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Address & Contact -->
            <div class="card shadow-sm border-0 rounded-lg mb-4">
                <div class="card-body p-4">
                    <div class="d-flex mb-3">
                        <div class="bg-light rounded p-3 mr-3 d-flex align-items-center justify-content-center" style="width:50px;height:50px;">
                            <i class="fas fa-map-marker-alt text-muted"></i>
                        </div>
                        <div>
                            <h6 class="font-weight-bold mb-0">Address & Contact Details</h6>
                            <small class="text-muted">Current residential location and contact information.</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <small class="text-muted d-block">Street / House Number</small>
                            <strong><?= esc($resident['street_address'] ?? '—') ?></strong>
                        </div>
                        <div class="col-md-4 mb-3">
                            <small class="text-muted d-block">Sitio / Zone</small>
                            <strong><?= esc($resident['sitio'] ?? '—') ?></strong>
                        </div>
                        <div class="col-md-4 mb-3">
                            <small class="text-muted d-block">Household #</small>
                            <strong><?= esc($resident['household_no'] ?? '—') ?></strong>
                        </div>
                        <div class="col-md-4 mb-3">
                            <small class="text-muted d-block">Relationship to Head</small>
                            <strong><?= esc($resident['relationship_to_head'] ?? '—') ?></strong>
                        </div>
                        <div class="col-md-4 mb-3">
                            <small class="text-muted d-block">Contact Number</small>
                            <strong><?= esc($resident['contact_number'] ?? '—') ?></strong>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>

<style>
    .bg-navy { background-color: #03213b !important; }
    .card { border-radius: 12px; }
</style>

<?= $this->endSection() ?>
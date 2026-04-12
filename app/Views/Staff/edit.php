<?= $this->extend('theme/template') ?>

<?= $this->section('content') ?>
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <i class="fas fa-user-edit mr-2 text-warning"></i> Edit Resident
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('staff/dashboard') ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('residents') ?>">Residents</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">

            <?php if (session()->getFlashdata('errors')): ?>
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <ul class="mb-0">
                        <?php foreach (session()->getFlashdata('errors') as $error): ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="card card-warning card-outline">
                <div class="card-header">
                    <h3 class="card-title">Resident Details</h3>
                </div>
                <form action="<?= base_url('staff/resident/update/' . $resident['id']) ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>First Name <span class="text-danger">*</span></label>
                                    <input type="text" name="first_name" class="form-control" required
                                        value="<?= esc($resident['first_name']) ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Middle Name</label>
                                    <input type="text" name="middle_name" class="form-control"
                                        value="<?= esc($resident['middle_name'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Last Name <span class="text-danger">*</span></label>
                                    <input type="text" name="last_name" class="form-control" required
                                        value="<?= esc($resident['last_name']) ?>">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Birthdate <span class="text-danger">*</span></label>
                                    <input type="date" name="birthdate" class="form-control" required
                                        value="<?= esc($resident['birthdate']) ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Sex <span class="text-danger">*</span></label>
                                    <select name="sex" class="form-control" required>
                                        <option value="male"   <?= $resident['sex'] === 'male'   ? 'selected' : '' ?>>Male</option>
                                        <option value="female" <?= $resident['sex'] === 'female' ? 'selected' : '' ?>>Female</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Civil Status</label>
                                    <select name="civil_status" class="form-control">
                                        <?php foreach (['single','married','widowed','separated'] as $cs): ?>
                                            <option value="<?= $cs ?>" <?= $resident['civil_status'] === $cs ? 'selected' : '' ?>>
                                                <?= ucfirst($cs) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Contact Number</label>
                                    <input type="text" name="contact_number" class="form-control"
                                        value="<?= esc($resident['contact_number'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Relationship to Head</label>
                                    <input type="text" name="relationship_to_head" class="form-control"
                                        value="<?= esc($resident['relationship_to_head'] ?? '') ?>">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Household ID</label>
                                    <input type="number" name="household_id" class="form-control"
                                        value="<?= esc($resident['household_id'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Occupation</label>
                                    <input type="text" name="occupation" class="form-control"
                                        value="<?= esc($resident['occupation'] ?? '') ?>">
                                </div>
                            </div>
                        </div>
                        <hr>
                        <label><strong>Categories</strong></label>
                        <div class="row mt-1">
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_voter" value="1"
                                        <?= !empty($resident['is_voter']) ? 'checked' : '' ?>>
                                    <label class="form-check-label">Registered Voter</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_pwd" value="1"
                                        <?= !empty($resident['is_pwd']) ? 'checked' : '' ?>>
                                    <label class="form-check-label">PWD</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_senior_citizen" value="1"
                                        <?= !empty($resident['is_senior_citizen']) ? 'checked' : '' ?>>
                                    <label class="form-check-label">Senior Citizen</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save mr-1"></i> Update Resident
                        </button>
                        <a href="<?= base_url('residents') ?>" class="btn btn-default ml-2">
                            <i class="fas fa-times mr-1"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>
<?= $this->endSection() ?>
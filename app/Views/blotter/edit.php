<?php
// SMART THEME LOADER
$role = session()->get('role');
$template = ($role == 'admin') ? 'theme/admin/template' : 'theme/template';
?>
<?= $this->extend($template) ?>

<?= $this->section('content') ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Edit Case <?= esc($case['case_number']) ?></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('blotter') ?>">Blotter</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <form action="<?= base_url('blotter/update/' . $case['id']) ?>" method="POST" id="blotter-form">
                <!-- Incident Details Card -->
                <div class="card card-warning">
                    <div class="card-header">
                        <h3 class="card-title">Incident Details</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Incident Type</label>
                                    <select name="incident_type" class="form-control custom-select" required>
                                        <option value="">Select Type</option>
                                        <?php $types = ['Physical Violence','Oral Defamation','Property Damage','Disturbance','Land Dispute','Others']; ?>
                                        <?php foreach ($types as $t): ?>
                                            <option value="<?= $t ?>" <?= $case['incident_type'] == $t ? 'selected' : '' ?>><?= $t ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Date of Incident</label>
                                    <input type="date" name="incident_date" class="form-control" required value="<?= esc($case['incident_date']) ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Purok</label>
                                    <select name="purok" class="form-control custom-select">
                                        <option value="">Select Purok</option>
                                        <?php $puroks = ['Purok Malipayon','Purok Masagana','Purok Cory','Purok Kawayan','Purok Pagla-um']; ?>
                                        <?php foreach ($puroks as $p): ?>
                                            <option value="<?= $p ?>" <?= ($case['purok'] ?? '') == $p ? 'selected' : '' ?>><?= $p ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Incident Location</label>
                            <input type="text" name="incident_location" class="form-control" value="<?= esc($case['incident_location']) ?>">
                        </div>
                        <div class="form-group">
                            <label>Details</label>
                            <textarea name="details" class="form-control" rows="5" required><?= esc($case['details']) ?></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Status</label>
                                    <select name="status" class="form-control custom-select">
                                        <?php
                                        $statuses = ['Pending','Investigating','Ongoing','For Hearing','Settled','Dismissed','Referred','Unsettled'];
                                        foreach ($statuses as $s): ?>
                                            <option value="<?= $s ?>" <?= $case['status'] == $s ? 'selected' : '' ?>><?= $s ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Action Taken</label>
                                    <textarea name="action_taken" class="form-control" rows="3"><?= esc($case['action_taken'] ?? '') ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Parties Card (edit mode) -->
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">Involved Parties</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" id="add-party-btn" title="Add party">
                                <i class="fas fa-plus"></i> Add Party
                            </button>
                        </div>
                    </div>
                    <div class="card-body" id="parties-container">
                        <?php
                        $index = 0;
                        foreach ($parties as $p): 
                        ?>
                            <div class="party-entry card card-outline card-secondary mb-3">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Role</label>
                                                <select name="parties[<?= $index ?>][role]" class="form-control role-select" required>
                                                    <option value="complainant" <?= $p['role'] == 'complainant' ? 'selected' : '' ?>>Complainant</option>
                                                    <option value="respondent" <?= $p['role'] == 'respondent' ? 'selected' : '' ?>>Respondent</option>
                                                    <option value="witness" <?= $p['role'] == 'witness' ? 'selected' : '' ?>>Witness</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-9">
                                            <div class="form-group">
                                                <label>Type</label>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input type-toggle" type="radio" name="parties[<?= $index ?>][type]" value="resident"
                                                        <?= empty($p['resident_id']) ? '' : 'checked' ?> data-index="<?= $index ?>">
                                                    <label class="form-check-label">Resident</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input type-toggle" type="radio" name="parties[<?= $index ?>][type]" value="outsider"
                                                        <?= empty($p['resident_id']) ? 'checked' : '' ?> data-index="<?= $index ?>">
                                                    <label class="form-check-label">Outsider</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- TomSelect resident dropdown -->
                                    <div class="resident-fields" <?= empty($p['resident_id']) ? 'style="display:none"' : '' ?>>
                                        <div class="form-group">
                                            <label>Select Resident</label>
                                            <select name="parties[<?= $index ?>][resident_id]" class="resident-select" data-index="<?= $index ?>" style="width:100%;">
                                                <?php if (!empty($p['resident_id'])): ?>
                                                    <option value="<?= $p['resident_id'] ?>" selected><?= esc($p['resident_name'] ?? '') ?></option>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="outsider-fields" <?= empty($p['resident_id']) ? '' : 'style="display:none"' ?>>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <input type="text" name="parties[<?= $index ?>][outsider_name]" class="form-control" placeholder="Full name" value="<?= esc($p['outsider_name'] ?? '') ?>">
                                            </div>
                                            <div class="col-md-6">
                                                <input type="text" name="parties[<?= $index ?>][outsider_address]" class="form-control" placeholder="Address" value="<?= esc($p['outsider_address'] ?? '') ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-danger remove-party" <?= count($parties) <= 2 ? 'disabled' : '' ?>>Remove</button>
                                </div>
                            </div>
                        <?php $index++; endforeach; ?>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-warning">Update Case</button>
                        <a href="<?= base_url('blotter') ?>" class="btn btn-secondary">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </section>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    window.blotterConfig = {
        searchUrl: '<?= base_url('blotter/searchResidents') ?>',
        partyIndex: <?= $index ?? 0 ?>
    };
</script>
<script src="<?= base_url('js/blotter/blotter-edit.js') ?>"></script>
<?= $this->endSection() ?>
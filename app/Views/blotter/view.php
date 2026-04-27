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
                    <h1 class="m-0">Blotter Details #<?= $blotter['id'] ?></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('blotter') ?>">Blotter</a></li>
                        <li class="breadcrumb-item active">View</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <!-- Incident Info -->
                <div class="col-md-8">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title">Incident Report</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <p><strong>Incident Type:</strong> <span class="badge badge-secondary"><?= esc($blotter['incident_type']) ?></span></p>
                                    <p><strong>Date:</strong> <?= date('F d, Y', strtotime($blotter['incident_date'])) ?></p>
                                    <p><strong>Location:</strong> <?= esc($blotter['incident_location']) ?></p>
                                </div>
                                <div class="col-12 mt-3">
                                    <h5>Narrative / Details</h5>
                                    <div class="p-3 bg-light border rounded">
                                        <?= nl2br(esc($blotter['details'])) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Process/Update -->
                <div class="col-md-4">
                    <div class="card card-warning">
                        <div class="card-header">
                            <h3 class="card-title">Update Status</h3>
                        </div>
                        <form action="<?= base_url('blotter/update/' . $blotter['id']) ?>" method="POST">
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Current Status</label>
                                    <select name="status" class="form-control custom-select">
                                        <option value="Pending" <?= ($blotter['status'] == 'Pending') ? 'selected' : '' ?>>Pending</option>
                                        <option value="Investigating" <?= ($blotter['status'] == 'Investigating') ? 'selected' : '' ?>>Investigating</option>
                                        <option value="Settled" <?= ($blotter['status'] == 'Settled') ? 'selected' : '' ?>>Settled</option>
                                        <option value="Unsettled" <?= ($blotter['status'] == 'Unsettled') ? 'selected' : '' ?>>Unsettled</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Action Taken</label>
                                    <textarea name="action_taken" class="form-control" rows="5" placeholder="Describe the settlement or action..."><?= esc($blotter['action_taken'] ?? '') ?></textarea>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-warning btn-block">Update</button>
                                <a href="<?= base_url('blotter') ?>" class="btn btn-secondary btn-block mt-2">Back</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?= $this->endSection() ?>
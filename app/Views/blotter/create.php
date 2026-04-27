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
                    <h1 class="m-0">Record New Blotter</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('blotter') ?>">Blotter</a></li>
                        <li class="breadcrumb-item active">New</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title">Incident Details</h3>
                </div>
                <form action="<?= base_url('blotter/store') ?>" method="POST">
                    <div class="card-body">
                        <div class="row">
                            <!-- Complainant Info -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Complainant Name (Victim)</label>
                                    <input type="text" name="complainant" class="form-control" placeholder="Full Name" required value="<?= old('complainant') ?>">
                                </div>
                            </div>
                            
                            <!-- Respondent Info -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Respondent Name (Accused)</label>
                                    <input type="text" name="respondent" class="form-control" placeholder="Full Name" required value="<?= old('respondent') ?>">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Incident Type</label>
                                    <select name="incident_type" class="form-control custom-select" required>
                                        <option value="">Select Type</option>
                                        <option value="Physical Violence">Physical Violence</option>
                                        <option value="Oral Defamation">Oral Defamation</option>
                                        <option value="Property Damage">Property Damage</option>
                                        <option value="Disturbance">Disturbance</option>
                                        <option value="Land Dispute">Land Dispute</option>
                                        <option value="Others">Others</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Date of Incident</label>
                                    <input type="date" name="incident_date" class="form-control" required value="<?= old('incident_date') ?>">
                                </div>
                            </div>
                            
                            <!-- UPDATED: Purok Dropdown Here -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Purok</label>
                                    <select name="purok" class="form-control custom-select">
                                        <option value="">Select Purok</option>
                                        <option value="Purok Malipayon" <?= (old('purok') == 'Purok Malipayon') ? 'selected' : '' ?>>Purok Malipayon</option>
                                        <option value="Purok Masagana" <?= (old('purok') == 'Purok Masagana') ? 'selected' : '' ?>>Purok Masagana</option>
                                        <option value="Purok Cory" <?= (old('purok') == 'Purok Cory') ? 'selected' : '' ?>>Purok Cory</option>
                                        <option value="Purok Kawayan" <?= (old('purok') == 'Purok Kawayan') ? 'selected' : '' ?>>Purok Kawayan</option>
                                        <option value="Purok Pagla-um" <?= (old('purok') == 'Purok Pagla-um') ? 'selected' : '' ?>>Purok Pagla-um</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Incident Location (Specific)</label>
                            <input type="text" name="incident_location" class="form-control" placeholder="e.g., Near Chapel, Basketball Court..." value="<?= old('incident_location') ?>">
                        </div>

                        <div class="form-group">
                            <label>Details of Incident</label>
                            <textarea name="details" class="form-control" rows="6" required placeholder="Describe what happened..."><?= old('details') ?></textarea>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-warning">Save Record</button>
                        <a href="<?= base_url('blotter') ?>" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>

<?= $this->endSection() ?>
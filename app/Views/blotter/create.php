<?php
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
                    <h1 class="m-0"><i class="fas fa-plus-circle"></i> Record New Blotter Case</h1>
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
            <form action="<?= base_url('blotter/store') ?>" method="POST" id="blotter-form">
                <?= csrf_field() ?>
                
                <!-- Incident Details -->
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-info-circle"></i> Incident Details</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Incident Type <span class="text-danger">*</span></label>
                                    <select name="incident_type" class="form-control" required>
                                        <option value="">Select Type</option>
                                        <?php $types = ['Physical Violence','Oral Defamation','Property Damage','Disturbance','Land Dispute','Others']; ?>
                                        <?php foreach ($types as $t): ?>
                                            <option value="<?= $t ?>" <?= old('incident_type') == $t ? 'selected' : '' ?>><?= $t ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Date of Incident <span class="text-danger">*</span></label>
                                    <input type="date" name="incident_date" class="form-control" required value="<?= old('incident_date') ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Purok / Sitio</label>
                                    <select name="purok" class="form-control">
                                        <option value="">Select Purok</option>
                                        <?php $puroks = ['Purok Malipayon','Purok Masagana','Purok Cory','Purok Kawayan','Purok Pagla-um']; ?>
                                        <?php foreach ($puroks as $p): ?>
                                            <option value="<?= $p ?>" <?= old('purok') == $p ? 'selected' : '' ?>><?= $p ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Specific Location</label>
                            <input type="text" name="incident_location" class="form-control" placeholder="e.g., Near Chapel, Basketball Court..." value="<?= old('incident_location') ?>">
                        </div>
                        <div class="form-group">
                            <label>Narrative <span class="text-danger">*</span></label>
                            <textarea name="details" class="form-control" rows="5" required placeholder="Describe what happened..."><?= old('details') ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Involved Parties -->
                <div class="card card-info card-outline">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-users"></i> Involved Parties</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-sm btn-success" id="add-party-btn">
                                <i class="fas fa-plus"></i> Add Party
                            </button>
                        </div>
                    </div>
                    <div class="card-body" id="parties-container">
                        <?php
                        $oldParties = old('parties') ?? [];
                        if (empty($oldParties)) {
                            $oldParties = [
                                ['role' => 'complainant', 'type' => 'outsider', 'outsider_name' => '', 'outsider_address' => ''],
                                ['role' => 'respondent', 'type' => 'outsider', 'outsider_name' => '', 'outsider_address' => '']
                            ];
                        }
                        foreach ($oldParties as $index => $p):
                            $roleSelected = $p['role'] ?? '';
                        ?>
                        <div class="party-entry card mb-3 <?= $index < 2 ? 'border-secondary' : 'border-primary' ?>">
                            <div class="card-body">
                                <div class="row align-items-end">
                                    <div class="col-md-3">
                                        <label>Role</label>
                                        <select name="parties[<?= $index ?>][role]" class="form-control" required>
                                            <option value="complainant" <?= $roleSelected == 'complainant' ? 'selected' : '' ?>>👤 Complainant</option>
                                            <option value="respondent" <?= $roleSelected == 'respondent' ? 'selected' : '' ?>>👥 Respondent</option>
                                            <option value="witness" <?= $roleSelected == 'witness' ? 'selected' : '' ?>>👁️ Witness</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label>Type</label>
                                        <div class="btn-group btn-group-toggle w-100" data-toggle="buttons">
                                            <label class="btn btn-outline-primary btn-sm <?= ($p['type'] ?? 'outsider') == 'resident' ? 'active' : '' ?>">
                                                <input type="radio" name="parties[<?= $index ?>][type]" value="resident" autocomplete="off" <?= ($p['type'] ?? '') == 'resident' ? 'checked' : '' ?>> Resident
                                            </label>
                                            <label class="btn btn-outline-secondary btn-sm <?= ($p['type'] ?? 'outsider') == 'outsider' ? 'active' : '' ?>">
                                                <input type="radio" name="parties[<?= $index ?>][type]" value="outsider" autocomplete="off" <?= ($p['type'] ?? 'outsider') == 'outsider' ? 'checked' : '' ?>> Outsider
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-7">
                                        <div class="resident-fields" style="<?= ($p['type'] ?? 'outsider') == 'resident' ? '' : 'display:none' ?>">
                                            <label>Search Resident</label>
                                            <select name="parties[<?= $index ?>][resident_id]" class="resident-select" style="width:100%">
                                                <?php if (!empty($p['resident_id'])): ?>
                                                    <option value="<?= $p['resident_id'] ?>" selected><?= esc($p['resident_name_display'] ?? '') ?></option>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                        <div class="outsider-fields" style="<?= ($p['type'] ?? 'outsider') == 'outsider' ? '' : 'display:none' ?>">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <input type="text" name="parties[<?= $index ?>][outsider_name]" class="form-control" placeholder="Full name" value="<?= esc($p['outsider_name'] ?? '') ?>">
                                                </div>
                                                <div class="col-md-6">
                                                    <input type="text" name="parties[<?= $index ?>][outsider_address]" class="form-control" placeholder="Address" value="<?= esc($p['outsider_address'] ?? '') ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-auto mt-2">
                                        <button type="button" class="btn btn-danger btn-sm remove-party" <?= $index < 2 ? 'disabled' : '' ?>>
                                            <i class="fas fa-trash-alt"></i> Remove
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> Save Case</button>
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
        partyIndex: <?= count($oldParties) ?>
    };
</script>
<script src="<?= base_url('js/blotter/blotter-create.js') ?>"></script>
<?= $this->endSection() ?>
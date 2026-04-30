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
                <div class="col-sm-6"><h1 class="m-0">Issue Certificate</h1></div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('certificate') ?>">Certificates</a></li>
                        <li class="breadcrumb-item active">Create</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-default">
                <div class="card-header">New Certificate</div>
                <form action="<?= base_url('certificate/store') ?>" method="POST" id="certTypeForm">
                    <?= csrf_field() ?>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Resident</label>
                                    <select name="resident_id" class="form-control select2" style="width:100%;" required>
                                        <option value="">Select Resident</option>
                                        <?php foreach($residents as $r): ?>
                                            <option value="<?= $r['id'] ?>"><?= esc($r['last_name'] . ', ' . $r['first_name']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Type</label>
                                    <select name="certificate_type" class="form-control" required>
                                        <option value="">Select Type</option>
                                        <?php foreach($types as $t): ?>
                                            <option value="<?= $t ?>"><?= esc($t) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Purpose</label>
                            <textarea name="purpose" class="form-control" rows="3" required placeholder="e.g. Employment"></textarea>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Generate</button>
                        <a href="<?= base_url('certificate') ?>" class="btn btn-default">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>$(document).ready(function(){ $('.select2').select2({ theme: 'bootstrap4' }); });</script>

<?= $this->endSection() ?>
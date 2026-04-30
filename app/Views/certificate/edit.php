<?php 
// ---------------------------------------------------------
// SMART THEME LOADER
// ---------------------------------------------------------
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
                    <h1 class="m-0">Edit Certificate</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('certificate') ?>">Certificates</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            
            <!-- Error Message -->
            <?php if (session()->getFlashdata('errors')): ?>
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h5><i class="icon fas fa-ban"></i> Alert!</h5>
                    <ul class="mb-0">
                        <?php foreach (session()->getFlashdata('errors') as $error): ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="card card-default">
                <div class="card-header">
                    <h3 class="card-title">Update Certificate Details</h3>
                </div>
                
                <form action="<?= base_url('certificate/update/' . $cert['id']) ?>" method="POST" id="updateCertForm">
                    <?= csrf_field() ?>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Resident</label>
                                    <input type="text" class="form-control" value="<?= esc(($resident['first_name'] ?? '') . ' ' . ($resident['last_name'] ?? '')) ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Certificate Type</label>
                                    <input type="text" class="form-control" value="<?= esc($cert['certificate_type']) ?>" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Purpose</label>
                            <textarea name="purpose" class="form-control" rows="3"><?= esc($cert['purpose']) ?></textarea>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Update Details</button>
                        <a href="<?= base_url('certificate/print/' . $cert['id']) ?>" target="_blank" class="btn btn-info">
                            <i class="fas fa-print"></i> Print
                        </a>
                        <a href="<?= base_url('certificate') ?>" class="btn btn-default">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>

<?= $this->endSection() ?>
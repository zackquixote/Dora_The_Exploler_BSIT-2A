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
                    <h1 class="m-0">Barangay Officials</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Officials</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">List of Officials</h3>
                    <a href="<?= base_url('officials/create') ?>" class="btn btn-primary btn-sm float-right">
                        <i class="fas fa-plus"></i> Add Official
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="row p-3">
                        <?php foreach($officials as $official): ?>
                            <div class="col-md-3">
                                <div class="card card-widget widget-user shadow-sm <?= $official['is_active'] ? '' : 'bg-secondary' ?>">
                                    <!-- Add the bg color to the user-header -->
                                    <div class="widget-user-header bg-info">
                                        <h3 class="widget-user-username"><?= esc($official['full_name']) ?></h3>
                                        <h5 class="widget-user-desc"><?= esc($official['position']) ?></h5>
                                    </div>
                                    <div class="widget-user-image">
                                        <?php if(!empty($official['photo'])): ?>
                                            <img class="img-circle elevation-2" style="height:100px; width:auto;" 
                                                 src="<?= base_url('uploads/officials/' . $official['photo']) ?>" 
                                                 alt="User Avatar">
                                        <?php else: ?>
                                            <img class="img-circle elevation-2" style="height:100px; width:auto;" 
                                                 src="<?= base_url('assets/img/default.png') ?>" 
                                                 alt="Default Avatar">
                                        <?php endif; ?>
                                    </div>
                                    <div class="card-footer">
                                        <div class="description-block">
                                            <h5 class="description-header text-muted"><?= esc($official['contact_number'] ?? 'N/A') ?></h5>
                                            <span class="description-text">CONTACT</span>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-sm-6 border-right">
                                                <a href="<?= base_url('officials/edit/' . $official['id']) ?>" class="btn btn-sm btn-block btn-primary">Edit</a>
                                            </div>
                                            <div class="col-sm-6">
                                                <a href="<?= base_url('officials/delete/' . $official['id']) ?>" class="btn btn-sm btn-block btn-danger" onclick="return confirm('Are you sure?')">Remove</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?= $this->endSection() ?>
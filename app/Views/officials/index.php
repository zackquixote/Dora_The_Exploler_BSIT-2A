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
                    <h1 class="m-0">
                        <?= esc($settings['barangay_name'] ?? 'Barangay') ?> Officials
                    </h1>
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

            <?php if (session()->get('role') == 'admin'): ?>
            <div class="alert alert-info alert-dismissible">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <a href="<?= base_url('admin/settings') ?>" class="btn btn-primary btn-sm pull-right">
                    Manage Assignments
                </a>
                <strong>Admin:</strong> Click Manage to assign officials from the resident list.
            </div>
            <?php endif; ?>

            <div class="row">
                <?php if (!empty($officials)): ?>
                    <?php foreach ($officials as $official): ?>
                    <div class="col-md-3 mb-4">
                        <div class="card shadow-sm text-center h-100">
                            <div class="card-body">
                                <?php
                                $photoPath = base_url('uploads/' . $official['photo']);
                                ?>
                                <img src="<?= $photoPath ?>"
                                     style="width:90px;height:90px;object-fit:cover;"
                                     class="rounded-circle mb-2 mx-auto d-block"
                                     onerror="this.src='<?= base_url('uploads/default.png') ?>'">

                                <h6 class="text-dark"><?= esc($official['full_name']) ?></h6>
                                <p class="text-muted small mb-0 badge badge-primary">
                                    <?= esc($official['position']) ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center p-5">
                        <h4 class="text-muted">No officials assigned yet. Please go to Settings.</h4>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
</div>

<?= $this->endSection() ?>
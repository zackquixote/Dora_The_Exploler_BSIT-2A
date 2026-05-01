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
                    <h1 class="m-0">Certificates</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Certificates</li>
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
                    <h3 class="card-title">Issued Certificates</h3>
                    <a href="<?= base_url('certificate/create') ?>" class="btn btn-primary btn-sm float-right">
                        <i class="fas fa-plus"></i> Issue New
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Certificate No.</th>
                                    <th>Type</th>
                                    <th>Resident</th>
                                    <th>Purpose</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($certificates as $c): ?>
                                <tr>
                                    <td><strong><?= esc($c['certificate_number'] ?? 'N/A') ?></strong></td>
                                    <td><?= esc($c['certificate_type']) ?></td>
                                    <td><?= esc($c['first_name'] . ' ' . $c['last_name']) ?></td>
                                    <td><?= esc($c['purpose']) ?></td>
                                    <td><?= date('M d, Y', strtotime($c['created_at'])) ?></td>
                                    <td>
                                        <a href="<?= base_url('certificate/print/' . $c['id']) ?>" class="btn btn-sm btn-info">
                                            <i class="fas fa-print"></i> Print
                                        </a>
                                        <a href="<?= base_url('certificate/edit/' . $c['id']) ?>" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?= $this->endSection() ?>
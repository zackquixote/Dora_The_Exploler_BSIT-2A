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
                <div class="col-sm-6"><h1 class="m-0">Issued Certificates</h1></div>
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
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <?= session()->getFlashdata('success') ?>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">List</h3>
                    <a href="<?= base_url('certificate/create') ?>" class="btn btn-primary btn-sm float-right">Issue New</a>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Resident</th>
                                <th>Type</th>
                                <th>Purpose</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($certificates)): ?>
                                <tr><td colspan="5" class="text-center">No records found</td></tr>
                            <?php else: ?>
                                <?php foreach($certificates as $cert): ?>
                                <tr>
                                    <td><?= esc($cert['last_name'] . ', ' . $cert['first_name']) ?></td>
                                    <td><span class="badge badge-info"><?= esc($cert['certificate_type']) ?></span></td>
                                    <td><?= esc($cert['purpose']) ?></td>
                                    <td><?= date('M d, Y', strtotime($cert['created_at'])) ?></td>
                                    <td>
                                        <a href="<?= base_url('certificate/print/' . $cert['id']) ?>" target="_blank" class="btn btn-info btn-sm"><i class="fas fa-print"></i> Print</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<?= $this->endSection() ?>
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
                    <h1 class="m-0">Blotter Records</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Blotter</li>
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
                    <h3 class="card-title">Incident List</h3>
                    <a href="<?= base_url('blotter/create') ?>" class="btn btn-primary btn-sm float-right">
                        <i class="fas fa-plus"></i> New Record
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped projects">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Complainant</th>
                                    <th>vs</th>
                                    <th>Respondent</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($blotters as $b): ?>
                                <tr>
                                    <td><?= date('M d, Y', strtotime($b['incident_date'])) ?></td>
                                    <td><?= esc($b['complainant']) ?></td>
                                    <td class="text-center font-weight-bold">VS</td>
                                    <td><?= esc($b['respondent']) ?></td>
                                    <td><?= esc($b['incident_type']) ?></td>
                                    <td>
                                        <?php 
                                        $color = 'secondary';
                                        if($b['status'] == 'Pending') $color = 'warning';
                                        if($b['status'] == 'Investigating') $color = 'info';
                                        if($b['status'] == 'Settled') $color = 'success';
                                        if($b['status'] == 'Unsettled') $color = 'danger';
                                        ?>
                                        <span class="badge badge-<?= $color ?>">
                                            <?= esc($b['status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="<?= base_url('blotter/view/' . $b['id']) ?>" class="btn btn-info btn-sm">
                                            <i class="fas fa-search"></i> View
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
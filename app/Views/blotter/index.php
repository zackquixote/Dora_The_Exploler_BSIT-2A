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
                    <h1 class="m-0">📋 Blotter Records</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('staff/dashboard') ?>">Home</a></li>
                        <li class="breadcrumb-item active">Blotter</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i> <?= session()->getFlashdata('success') ?>
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            <?php endif; ?>

            <!-- Filter Bar -->
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-filter"></i> Filter Cases</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <input type="text" id="searchCase" class="form-control" placeholder="🔍 Search case #, party name...">
                        </div>
                        <div class="col-md-3">
                            <select id="filterStatus" class="form-control">
                                <option value="">All Status</option>
                                <?php foreach(['Pending','Investigating','Ongoing','For Hearing','Settled','Dismissed','Referred','Unsettled'] as $s): ?>
                                    <option value="<?= $s ?>"><?= $s ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select id="filterPurok" class="form-control">
                                <option value="">All Puroks</option>
                                <?php foreach(['Purok Malipayon','Purok Masagana','Purok Cory','Purok Kawayan','Purok Pagla-um'] as $p): ?>
                                    <option value="<?= $p ?>"><?= $p ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button id="clearFilters" class="btn btn-default btn-block">Clear</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cases Table -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Case List</h3>
                    <a href="<?= base_url('blotter/create') ?>" class="btn btn-primary btn-sm float-right">
                        <i class="fas fa-plus-circle"></i> New Case
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped" id="blotterTable">
                            <thead>
                                <tr>
                                    <th>Case No.</th>
                                    <th>Date</th>
                                    <th>Purok</th>
                                    <th>Complainant</th>
                                    <th>Respondent</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th style="width: 140px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($blotters as $b): ?>
                                <tr data-status="<?= $b['status'] ?>" data-purok="<?= $b['purok'] ?? '' ?>">
                                    <td><strong><?= esc($b['case_number']) ?></strong></td>
                                    <td><?= date('Y-m-d', strtotime($b['incident_date'])) ?></td>
                                    <td><span class="badge badge-info"><?= esc($b['purok'] ?? '-') ?></span></td>
                                    <td><?= esc($b['complainant_name'] ?? 'N/A') ?></td>
                                    <td><?= esc($b['respondent_name'] ?? 'N/A') ?></td>
                                    <td><?= esc($b['incident_type']) ?></td>
                                    <td>
                                        <?php
                                        $statusClass = match($b['status']) {
                                            'Pending' => 'warning', 'Settled' => 'success', 'Dismissed' => 'dark',
                                            'For Hearing' => 'primary', 'Unsettled' => 'danger', default => 'secondary'
                                        };
                                        ?>
                                        <span class="badge badge-<?= $statusClass ?> badge-status"><?= esc($b['status']) ?></span>
                                    </td>
                                    <td class="text-nowrap">
                                        <a href="<?= base_url('blotter/view/'.$b['id']) ?>" class="btn btn-sm btn-outline-info" title="View"><i class="fas fa-folder-open"></i></a>
                                        <a href="<?= base_url('blotter/edit/'.$b['id']) ?>" class="btn btn-sm btn-outline-warning" title="Edit"><i class="fas fa-edit"></i></a>
                                        <button class="btn btn-sm btn-outline-danger delete-btn" data-id="<?= $b['id'] ?>" data-case="<?= esc($b['case_number']) ?>" title="Delete"><i class="fas fa-trash-alt"></i></button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if (empty($blotters)): ?>
                                <td><td colspan="8" class="text-center text-muted py-3">No blotter records found.<?= $this->endSection() ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete <strong id="delete-case-ref"></strong>?</p>
                <p class="text-muted small">This will also remove all associated parties and hearings.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <form id="delete-form" method="POST" action="">
                    <?= csrf_field() ?>
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="btn btn-danger">Delete Case</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    window.blotterConfig = {
        deleteUrl: '<?= base_url('blotter/delete') ?>'
    };
</script>
<script src="<?= base_url('js/blotter/blotter-index.js') ?>"></script>
<?= $this->endSection() ?>
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
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= session()->getFlashdata('success') ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Case List</h3>
                    <a href="<?= base_url('blotter/create') ?>" class="btn btn-primary btn-sm float-right">
                        <i class="fas fa-plus"></i> New Case
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped projects">
                            <thead>
                                <tr>
                                    <th>Case No.</th>
                                    <th>Date</th>
                                    <th>Purok</th>
                                    <th>Complainant</th>
                                    <th>vs</th>
                                    <th>Respondent</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th style="width: 180px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($blotters as $b): ?>
                                <tr>
                                    <td><strong><?= esc($b['case_number']) ?></strong></td>
                                    <td><?= date('M d, Y', strtotime($b['incident_date'])) ?></td>
                                    <td><span class="badge badge-info"><?= esc($b['purok'] ?? '-') ?></span></td>
                                    <td><?= esc($b['complainant_name'] ?? 'N/A') ?></td>
                                    <td class="text-center font-weight-bold">VS</td>
                                    <td><?= esc($b['respondent_name'] ?? 'N/A') ?></td>
                                    <td><?= esc($b['incident_type']) ?></td>
                                    <td>
                                        <?php 
                                        $status = $b['status'];
                                        $color = 'secondary';
                                        if ($status == 'Pending') $color = 'warning';
                                        elseif ($status == 'Investigating' || $status == 'Ongoing') $color = 'info';
                                        elseif ($status == 'For Hearing') $color = 'primary';
                                        elseif ($status == 'Settled') $color = 'success';
                                        elseif ($status == 'Dismissed') $color = 'dark';
                                        elseif ($status == 'Referred') $color = 'purple';
                                        elseif ($status == 'Unsettled') $color = 'danger';
                                        ?>
                                        <span class="badge badge-<?= $color ?>"><?= esc($status) ?></span>
                                    </td>
                                    <td class="text-nowrap">
                                        <a href="<?= base_url('blotter/view/' . $b['id']) ?>" class="btn btn-info btn-sm" title="View">
                                            <i class="fas fa-folder-open"></i> View
                                        </a>
                                        <a href="<?= base_url('blotter/edit/' . $b['id']) ?>" class="btn btn-warning btn-sm" title="Edit">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <button type="button" class="btn btn-danger btn-sm delete-btn"
                                                data-id="<?= $b['id'] ?>"
                                                data-case="<?= esc($b['case_number']) ?>"
                                                title="Delete">
                                            <i class="fas fa-trash-alt"></i> Delete
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if (empty($blotters)): ?>
                                <tr><td colspan="9" class="text-center text-muted py-3">No blotter records found.<?= $this->endSection() ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete <strong id="delete-case-ref"></strong>?</p>
                <p class="text-muted small">This will also remove all associated parties and cannot be undone.</p>
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
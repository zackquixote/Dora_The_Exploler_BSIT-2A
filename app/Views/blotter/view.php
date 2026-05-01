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
                    <h1 class="m-0">Blotter Case <?= esc($case['case_number']) ?></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('blotter') ?>">Blotter</a></li>
                        <li class="breadcrumb-item active">View</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <!-- Case Header -->
            <div class="callout callout-info">
                <h4><i class="fas fa-file-alt"></i> Case # <?= esc($case['case_number']) ?></h4>
                <p class="mb-0">
                    Status: <span class="badge badge-<?= 
                        $status = $case['status'];
                        $color = 'secondary';
                        if ($status == 'Pending') $color = 'warning';
                        elseif ($status == 'Investigating' || $status == 'Ongoing') $color = 'info';
                        elseif ($status == 'For Hearing') $color = 'primary';
                        elseif ($status == 'Settled') $color = 'success';
                        elseif ($status == 'Dismissed') $color = 'dark';
                        elseif ($status == 'Referred') $color = 'purple';
                        elseif ($status == 'Unsettled') $color = 'danger';
                        echo $color;
                    ?>"><?= esc($status) ?></span>
                    &nbsp; | &nbsp;
                    <i class="far fa-calendar-alt"></i> Incident Date: <?= date('F d, Y', strtotime($case['incident_date'])) ?>
                    &nbsp; | &nbsp;
                    <i class="fas fa-map-marker-alt"></i> Purok: <?= esc($case['purok'] ?? 'Not specified') ?>
                </p>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <!-- Incident Details -->
                    <div class="card card-outline card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Incident Report</h3>
                            <div class="card-tools">
                                <a href="<?= base_url('blotter/edit/' . $case['id']) ?>" class="btn btn-tool text-warning">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <dl class="row">
                                <dt class="col-sm-3">Type</dt>
                                <dd class="col-sm-9"><?= esc($case['incident_type']) ?></dd>

                                <dt class="col-sm-3">Location</dt>
                                <dd class="col-sm-9"><?= esc($case['incident_location'] ?? 'Not specified') ?></dd>

                                <dt class="col-sm-3">Recorded On</dt>
                                <dd class="col-sm-9"><?= date('F d, Y h:i A', strtotime($case['created_at'])) ?></dd>

                                <dt class="col-sm-3">Created By</dt>
                                <dd class="col-sm-9"><?= esc($case['created_by_name'] ?? 'System') ?></dd>

                                <dt class="col-sm-3">Action Taken</dt>
                                <dd class="col-sm-9"><?= nl2br(esc($case['action_taken'] ?? 'None recorded')) ?></dd>
                            </dl>

                            <h5 class="mt-4">Narrative</h5>
                            <div class="p-3 bg-light border rounded" style="white-space: pre-wrap;"><?= esc($case['details']) ?></div>
                        </div>
                    </div>

                    <!-- Involved Parties -->
                    <div class="card card-outline card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">Involved Parties</h3>
                        </div>
                        <div class="card-body">
                            <?php foreach (['complainant','respondent','witness'] as $role): ?>
                                <?php if (!empty($parties[$role])): ?>
                                    <h5 class="text-uppercase">
                                        <i class="fas <?= $role == 'complainant' ? 'fa-user-edit' : ($role == 'respondent' ? 'fa-user-alt-slash' : 'fa-eye') ?>"></i> 
                                        <?= $role ?>s
                                    </h5>
                                    <ul class="list-group mb-3">
                                        <?php foreach ($parties[$role] as $p): ?>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <span>
                                                    <?php if (!empty($p['resident_id'])): ?>
                                                        <i class="fas fa-check-circle text-success"></i>
                                                        <strong><?= esc($p['resident_name']) ?></strong> (Resident #<?= $p['resident_id'] ?>)
                                                    <?php else: ?>
                                                        <i class="fas fa-user text-secondary"></i>
                                                        <?= esc($p['outsider_name']) ?>
                                                        <?php if (!empty($p['outsider_address'])): ?>
                                                            <small class="text-muted"> — <?= esc($p['outsider_address']) ?></small>
                                                        <?php endif; ?>
                                                    <?php endif; ?>
                                                </span>
                                                <span class="badge badge-<?= $role == 'complainant' ? 'warning' : ($role == 'respondent' ? 'danger' : 'info') ?>">
                                                    <?= ucfirst($role) ?>
                                                </span>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Hearings / Proceedings -->
                    <div class="card card-outline card-success">
                        <div class="card-header">
                            <h3 class="card-title">Hearings / Proceedings</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#addHearingModal">
                                    <i class="fas fa-plus"></i> Add Hearing
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <?php if (empty($hearings)): ?>
                                <p class="text-muted">No hearings recorded yet.</p>
                            <?php else: ?>
                                <div class="timeline">
                                    <?php foreach ($hearings as $h): ?>
                                        <div class="time-label">
                                            <span class="bg-green">
                                                <?= date('F d, Y', strtotime($h['hearing_date'])) ?>
                                                <?= $h['hearing_time'] ? ' at ' . date('h:i A', strtotime($h['hearing_time'])) : '' ?>
                                            </span>
                                        </div>
                                        <div>
                                            <i class="fas fa-gavel bg-primary"></i>
                                            <div class="timeline-item">
                                                <span class="time">
                                                    <span class="badge badge-<?= $h['status'] == 'Completed' ? 'success' : ($h['status'] == 'Cancelled' ? 'danger' : 'warning') ?>">
                                                        <?= $h['status'] ?>
                                                    </span>
                                                </span>
                                                <h3 class="timeline-header">
                                                    <strong><?= esc($h['presiding_officer'] ?? 'N/A') ?></strong> – <?= esc($h['venue'] ?? 'No venue') ?>
                                                </h3>
                                                <div class="timeline-body">
                                                    <?= nl2br(esc($h['notes'] ?? '')) ?>
                                                    <?php if ($h['outcome']): ?>
                                                        <p class="mt-2"><strong>Outcome:</strong> <?= esc($h['outcome']) ?></p>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="timeline-footer">
                                                    <a href="#" class="btn btn-xs btn-info edit-hearing"
                                                       data-id="<?= $h['id'] ?>"
                                                       data-date="<?= $h['hearing_date'] ?>"
                                                       data-time="<?= $h['hearing_time'] ?>"
                                                       data-venue="<?= esc($h['venue']) ?>"
                                                       data-officer="<?= esc($h['presiding_officer']) ?>"
                                                       data-notes="<?= esc($h['notes']) ?>"
                                                       data-outcome="<?= esc($h['outcome']) ?>"
                                                       data-status="<?= $h['status'] ?>">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </a>
                                                    <a href="#" class="btn btn-xs btn-danger delete-hearing" data-id="<?= $h['id'] ?>">
                                                        <i class="fas fa-trash"></i> Delete
                                                    </a>
                                                    <a href="<?= base_url('blotter/print/' . $case['id']) ?>" class="btn btn-outline-primary btn-block mt-2" target="_blank">
                                                        <i class="fas fa-print"></i> Print Case Summary
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Status History -->
                    <div class="card card-outline card-info">
                        <div class="card-header">
                            <h3 class="card-title">Status History</h3>
                        </div>
                        <div class="card-body">
                            <?php if (empty($timeline)): ?>
                                <p class="text-muted">No status changes recorded yet.</p>
                            <?php else: ?>
                                <ul class="list-group">
                                    <?php foreach ($timeline as $entry): ?>
                                        <li class="list-group-item">
                                            <div class="d-flex justify-content-between">
                                                <div>
                                                    <?php if ($entry['old_status']): ?>
                                                        Changed from <strong><?= esc($entry['old_status']) ?></strong> to <strong><?= esc($entry['new_status']) ?></strong>
                                                    <?php else: ?>
                                                        Initial status: <strong><?= esc($entry['new_status']) ?></strong>
                                                    <?php endif; ?>
                                                    <?php if (!empty($entry['remarks'])): ?>
                                                        <br><small class="text-muted"><?= esc($entry['remarks']) ?></small>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="text-muted text-sm">
                                                    <?= date('M d, Y H:i', strtotime($entry['created_at'])) ?>
                                                </div>
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-md-4">
                    <!-- Status Update Form -->
                    <div class="card card-warning card-outline">
                        <div class="card-header">
                            <h3 class="card-title">Update Status / Action</h3>
                        </div>
                        <form action="<?= base_url('blotter/update/' . $case['id']) ?>" method="POST">
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Status</label>
                                    <select name="status" class="form-control">
                                        <?php 
                                        $statuses = ['Pending','Investigating','Ongoing','For Hearing','Settled','Dismissed','Referred','Unsettled'];
                                        foreach ($statuses as $s): ?>
                                            <option value="<?= $s ?>" <?= $case['status'] == $s ? 'selected' : '' ?>><?= $s ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Action Taken</label>
                                    <textarea name="action_taken" class="form-control" rows="4"><?= esc($case['action_taken'] ?? '') ?></textarea>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-warning btn-block"><i class="fas fa-save"></i> Update Case</button>
                            </div>
                        </form>
                    </div>

                    <!-- Danger Zone -->
                    <div class="card card-outline card-danger">
                        <div class="card-header"><h3 class="card-title">Danger Zone</h3></div>
                        <div class="card-body">
                            <button type="button" class="btn btn-outline-danger btn-block delete-btn"
                                    data-id="<?= $case['id'] ?>"
                                    data-case="<?= esc($case['case_number']) ?>">
                                <i class="fas fa-trash-alt"></i> Delete This Case
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Add Hearing Modal -->
<div class="modal fade" id="addHearingModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="hearing-form" action="<?= base_url('blotter/hearing/add/' . $case['id']) ?>" method="POST">
                <div class="modal-header bg-success">
                    <h5 class="modal-title">Add Hearing / Proceeding</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Date</label>
                        <input type="date" name="hearing_date" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Time</label>
                        <input type="time" name="hearing_time" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Venue</label>
                        <input type="text" name="venue" class="form-control" placeholder="e.g., Barangay Hall">
                    </div>
                    <div class="form-group">
                        <label>Presiding Officer</label>
                        <input type="text" name="presiding_officer" class="form-control" placeholder="Name of official">
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="Scheduled">Scheduled</option>
                            <option value="In Progress">In Progress</option>
                            <option value="Completed">Completed</option>
                            <option value="Cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Notes</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="Details of the hearing..."></textarea>
                    </div>
                    <input type="hidden" name="hearing_id" id="hearing-id">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success" id="modal-save-btn">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete <strong id="delete-case-ref"></strong>?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <form id="delete-form" method="POST" action="">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-danger">Delete Forever</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    window.blotterConfig = {
        deleteUrl: '<?= base_url('blotter/delete') ?>',
        hearingAddUrl: '<?= base_url('blotter/hearing/add/' . $case['id']) ?>',
        hearingUpdateUrl: '<?= base_url('blotter/hearing/update') ?>',
        hearingDeleteUrl: '<?= base_url('blotter/hearing/delete') ?>',
        csrfToken: '<?= csrf_token() ?>',
        csrfHash: '<?= csrf_hash() ?>'
    };
</script>
<script src="<?= base_url('js/blotter/blotter-view.js') ?>"></script>
<?= $this->endSection() ?>
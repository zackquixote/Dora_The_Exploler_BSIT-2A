<?php
$role = strtolower(session()->get('role') ?? 'staff');
$template = ($role == 'admin') ? 'theme/admin/template' : 'theme/template';
?>
<?= $this->extend($template) ?>
<?= $this->section('content') ?>

<div class="content-wrapper residents-index">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">👥 Residents Management</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url(strtolower(session()->get('role') . '/dashboard')) ?>">Home</a></li>
                        <li class="breadcrumb-item active">Residents</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <!-- Flash messages -->
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle mr-1"></i> <?= session()->getFlashdata('success') ?>
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle mr-1"></i> <?= session()->getFlashdata('error') ?>
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            <?php endif; ?>

            <!-- Statistics Cards -->
            <div class="row">
                <div class="col-md-3 col-sm-6">
                    <div class="info-box bg-gradient-info">
                        <span class="info-box-icon"><i class="fas fa-users"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Residents</span>
                            <span class="info-box-number"><?= array_sum($purokCounts) ?></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="info-box bg-gradient-success">
                        <span class="info-box-icon"><i class="fas fa-user-graduate"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Senior Citizens</span>
                            <span class="info-box-number" id="seniorCount">0</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="info-box bg-gradient-warning">
                        <span class="info-box-icon"><i class="fas fa-wheelchair"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">PWD</span>
                            <span class="info-box-number" id="pwdCount">0</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="info-box bg-gradient-primary">
                        <span class="info-box-icon"><i class="fas fa-check-circle"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Voters</span>
                            <span class="info-box-number" id="voterCount">0</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter Card -->
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-filter"></i> Filter Residents</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Search by name</label>
                                <input type="text" id="searchName" class="form-control" placeholder="First or last name...">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Purok / Sitio</label>
                                <select id="filterPurok" class="form-control">
                                    <option value="all" <?= ($selectedPurok ?? 'all') == 'all' ? 'selected' : '' ?>>All Puroks</option>
                                    <?php foreach (['Purok Malipayon','Purok Masagana','Purok Cory','Purok Kawayan','Purok Pagla-um','Unassigned'] as $p): ?>
                                        <option value="<?= $p ?>" <?= ($selectedPurok ?? '') == $p ? 'selected' : '' ?>><?= $p ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Household No.</label>
                                <input type="text" id="filterHousehold" class="form-control" placeholder="e.g., HH-2025-001">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button id="clearFilters" class="btn btn-secondary btn-block">Clear Filters</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Residents Table -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Resident Directory</h3>
                    <a href="<?= base_url('resident/create') ?>" class="btn btn-primary btn-sm float-right">
                        <i class="fas fa-plus-circle"></i> Add Resident
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="residentsTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Photo</th>
                                    <th>Full Name</th>
                                    <th>Sex</th>
                                    <th>Age</th>
                                    <th>Civil Status</th>
                                    <th>Purok / Sitio</th>
                                    <th>Household No.</th>
                                    <th>Occupation</th>
                                    <th>Flags</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($residents)): ?>
                                    <tr><td colspan="11" class="text-center py-4 text-muted">No residents found.<?= $this->endSection() ?>
                                <?php else: ?>
                                    <?php foreach ($residents as $r): 
                                        $profileImg = !empty($r['profile_picture']) ? base_url('uploads/' . $r['profile_picture']) : base_url('assets/img/default.png');
                                        $flags = [];
                                        if (!empty($r['is_senior_citizen'])) $flags[] = '<span class="badge badge-info">Senior</span>';
                                        if (!empty($r['is_pwd'])) $flags[] = '<span class="badge badge-warning">PWD</span>';
                                        if (!empty($r['is_voter'])) $flags[] = '<span class="badge badge-success">Voter</span>';
                                    ?>
                                        <tr>
                                            <td><?= $r['id'] ?></td>
                                            <td><img src="<?= $profileImg ?>" class="img-circle elevation-1" width="35" height="35" style="object-fit: cover;"></td>
                                            <td class="font-weight-bold"><?= esc($r['first_name']) ?> <?= esc($r['last_name']) ?></td>
                                            <td><?= ucfirst($r['sex']) ?></td>
                                            <td><?= $r['age'] ?? '—' ?></td>
                                            <td><?= esc($r['civil_status'] ?? '—') ?></td>
                                            <td><span class="badge badge-secondary"><?= esc($r['sitio'] ?? 'Unassigned') ?></span></td>
                                            <td><?= esc($r['household_no'] ?? '—') ?></td>
                                            <td><?= esc($r['occupation'] ?? '—') ?></td>
                                            <td><?= implode(' ', $flags) ?: '—' ?></td>
                                            <td class="text-nowrap">
                                                <a href="<?= base_url('resident/view/'.$r['id']) ?>" class="btn btn-sm btn-outline-info" title="View"><i class="fas fa-eye"></i></a>
                                                <a href="<?= base_url('resident/edit/'.$r['id']) ?>" class="btn btn-sm btn-outline-warning" title="Edit"><i class="fas fa-edit"></i></a>
                                                <button class="btn btn-sm btn-outline-danger delete-resident" data-id="<?= $r['id'] ?>" data-name="<?= esc($r['first_name'].' '.$r['last_name']) ?>" title="Delete"><i class="fas fa-trash"></i></button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Purok Tiles -->
            <div class="row mt-4">
                <?php foreach ($purokCounts as $purok => $count): 
                    $color = match($purok) {
                        'Purok Malipayon' => 'primary',
                        'Purok Masagana'  => 'success',
                        'Purok Cory'      => 'info',
                        'Purok Kawayan'   => 'warning',
                        'Purok Pagla-um'  => 'secondary',
                        default => 'dark'
                    };
                ?>
                <div class="col-md-2 col-sm-4 col-6 mb-3">
                    <a href="?purok=<?= urlencode($purok) ?>" class="small-box bg-gradient-<?= $color ?> text-white text-center p-3 rounded d-block" style="text-decoration: none;">
                        <h3><?= $count ?></h3>
                        <p><?= esc($purok) ?></p>
                    </a>
                </div>
                <?php endforeach; ?>
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
                <p>Are you sure you want to delete <strong id="deleteResidentName"></strong>?</p>
                <p class="text-muted small">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" id="confirmDeleteBtn" class="btn btn-danger">Delete</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    window.ResidentConfig = {
        baseUrl: "<?= base_url() ?>",
        csrfName: "<?= csrf_token() ?>",
        csrfHash: "<?= csrf_hash() ?>"
    };
</script>
<script>
    window.RESIDENTS_CONFIG = {
        baseUrl: "<?= base_url() ?>",
        csrfName: "<?= csrf_token() ?>",
        csrfHash: "<?= csrf_hash() ?>",
        currentPurok: "<?= $selectedPurok ?? 'all' ?>"
    };
</script>
<script src="<?= base_url('js/residents/residents-index.js') ?>"></script>
<?= $this->endSection() ?>
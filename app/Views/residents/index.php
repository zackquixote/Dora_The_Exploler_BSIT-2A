<?php
// ---------------------------------------------------------
// SMART THEME LOADER
// ---------------------------------------------------------
// If Admin → Load Admin Template (Red Sidebar)
// If Staff  → Load Staff Template (Green Sidebar)
 $role = strtolower(session()->get('role') ?? 'staff');
 $template = ($role == 'admin') ? 'theme/admin/template' : 'theme/template';
?>

<?= $this->extend($template) ?>

<?= $this->section('content') ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Residents Management</h1>
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
            <!-- Flash Messages -->
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <?= session()->getFlashdata('success') ?>
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?= session()->getFlashdata('error') ?>
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <h3 class="card-title mb-2 mb-md-0">
                            <i class="fas fa-users mr-2"></i> List of Residents
                            <?php if ($selectedPurok !== 'all'): ?>
                                <span class="badge badge-info ml-2">Filtered by: <?= esc($selectedPurok) ?></span>
                            <?php endif; ?>
                        </h3>
                        <div class="d-flex flex-wrap gap-2">
                            <!-- Purok Filter Form -->
                            <form method="GET" action="<?= base_url('resident') ?>" class="d-flex" id="purokFilterForm">
                                <select name="purok" id="purokFilter" class="form-control form-control-sm mr-2" style="min-width: 180px;" onchange="this.form.submit()">
                                        <option value="all" <?= ($selectedPurok ?? 'all') == 'all' ? 'selected' : '' ?>>All Puroks</option>
                                        <option value="Purok Malipayon" <?= ($selectedPurok ?? '') == 'Purok Malipayon' ? 'selected' : '' ?>>Purok Malipayon</option>
                                        <option value="Purok Masagana" <?= ($selectedPurok ?? '') == 'Purok Masagana' ? 'selected' : '' ?>>Purok Masagana</option>
                                        <option value="Purok Cory" <?= ($selectedPurok ?? '') == 'Purok Cory' ? 'selected' : '' ?>>Purok Cory</option>
                                        <option value="Purok Kawayan" <?= ($selectedPurok ?? '') == 'Purok Kawayan' ? 'selected' : '' ?>>Purok Kawayan</option>
                                        <option value="Purok Pagla-um" <?= ($selectedPurok ?? '') == 'Purok Pagla-um' ? 'selected' : '' ?>>Purok Pagla-um</option>
                                        <option value="Unassigned" <?= ($selectedPurok ?? '') == 'Unassigned' ? 'selected' : '' ?>>Unassigned</option>
                                </select>
                                <?php if (($selectedPurok ?? 'all') != 'all'): ?>
                                    <a href="<?= base_url('resident') ?>" class="btn btn-secondary btn-sm" id="clearFilterBtn">
                                        <i class="fas fa-times"></i> Clear
                                    </a>
                                <?php endif; ?>
                            </form>
                            <a href="<?= base_url('resident/create') ?>" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus-circle mr-1"></i> Add New Resident
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="residentsTable" class="table table-bordered table-striped table-sm w-100">
                            <thead>
                                <tr>
                                    <th style="width: 40px;">ID</th>
                                    <th style="width: 60px;">Profile</th>
                                    <th>Full Name</th>
                                    <th style="width: 50px;">Sex</th>
                                    <th style="width: 40px;">Age</th>
                                    <th style="width: 100px;">Civil Status</th>
                                    <th style="width: 120px;">Purok/Sitio</th>
                                    <th style="width: 80px;">Household No.</th>
                                    <th>Occupation</th>
                                    <th>Citizenship</th>
                                    <th style="width: 60px;">Voter</th>
                                    <th style="width: 120px;">Flags</th>
                                    <th style="width: 150px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($residents)): ?>
                                    <tr>
                                        <td colspan="13" class="text-center">
                                            No residents found <?= $selectedPurok != 'all' ? 'in ' . esc($selectedPurok) : '' ?>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($residents as $r): 
                                        // Calculate Age
                                        $age = '';
                                        if (!empty($r['birthdate'])) {
                                            $birth = new DateTime($r['birthdate']);
                                            $today = new DateTime();
                                            $age = $birth->diff($today)->y;
                                        }

                                        // Profile Image Logic
                                        $profileImg = !empty($r['profile_picture'])
                                            ? base_url('uploads/' . $r['profile_picture'])
                                            : base_url('assets/img/default.png');

                                        // --- BADGE LOGIC ---
                                        
                                        // Voter Badge
                                        if (!empty($r['is_voter'])) {
                                            $voterBadge = '<span class="badge badge-success">Yes</span>';
                                        } else {
                                            $voterBadge = '<span class="badge badge-secondary">No</span>';
                                        }

                                        // Senior Citizen Badge
                                        if (!empty($r['is_senior_citizen'])) {
                                            $seniorBadge = '<span class="badge badge-info">Senior</span>';
                                        } else {
                                            $seniorBadge = '';
                                        }

                                        // PWD Badge
                                        if (!empty($r['is_pwd'])) {
                                            $pwdBadge = '<span class="badge badge-warning">PWD</span>';
                                        } else {
                                            $pwdBadge = '';
                                        }

                                        $flags = trim($seniorBadge . ' ' . $pwdBadge);

                                        // Purok Display Logic
                                        $purokDisplay = !empty($r['sitio']) ? $r['sitio'] : 'Unassigned';
                                        $purokBadge = $purokDisplay != 'Unassigned'
                                            ? '<span class="badge badge-primary">' . esc($purokDisplay) . '</span>'
                                            : '<span class="badge badge-secondary">Unassigned</span>';
                                    ?>
                                        <tr>
                                            <td><?= $r['id'] ?></td>
                                            <td><img src="<?= $profileImg ?>" width="40" height="40" class="rounded-circle" alt="Profile"></td>
                                            <td><?= esc($r['first_name']) ?> <?= esc($r['middle_name'] ?? '') ?> <?= esc($r['last_name']) ?></td>
                                            <td><?= ucfirst($r['sex']) ?></td>
                                            <td><?= $age ?></td>
                                            <td><?= ucfirst($r['civil_status'] ?? '') ?></td>
                                            <td><?= $purokBadge ?></td>
                                            <td><?= $r['household_no'] ?? '-' ?></td>
                                            <td><?= esc($r['occupation'] ?? '-') ?></td>
                                            <td><?= esc($r['citizenship'] ?? '-') ?></td>
                                            <td><?= $voterBadge ?></td>
                                            <td><?= $flags ?: '-' ?></td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="<?= base_url('resident/view/' . $r['id']) ?>" class="btn btn-info" title="View">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="<?= base_url('resident/edit/' . $r['id']) ?>" class="btn btn-warning" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-danger delete-resident" data-id="<?= $r['id'] ?>" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Purok Statistics Cards -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0"><i class="fas fa-chart-pie mr-2"></i> Residents per Purok</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <?php
                                $colors = ['primary', 'success', 'warning', 'danger', 'info', 'secondary'];
                                $i = 0;
                                foreach ($purokCounts as $purok => $count):
                                    $color = $colors[$i % count($colors)];
                                ?>
                                    <div class="col-md-2 col-sm-3 col-6 mb-3">
                                        <a href="<?= base_url('resident?purok=' . urlencode($purok)) ?>" class="purok-stat-card text-decoration-none" data-purok-name="<?= esc($purok) ?>">
                                            <div class="small-box bg-<?= $color ?> text-white p-3 text-center rounded">
                                                <h3 class="mb-1"><?= $count ?></h3>
                                                <small><?= esc($purok) ?></small>
                                            </div>
                                        </a>
                                    </div>
                                <?php
                                    $i++;
                                endforeach;
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
.gap-2 { gap: 0.5rem; }
.small-box { transition: transform 0.2s, box-shadow 0.2s; cursor: pointer; }
.small-box:hover { transform: translateY(-5px); box-shadow: 0 4px 15px rgba(0,0,0,0.2); }
/* Ensure Voter column has a minimum width */
#residentsTable th:nth-child(11), 
#residentsTable td:nth-child(11) {
    min-width: 70px;
    text-align: center;
}
</style>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- DataTables CSS -->
<link rel="stylesheet" href="<?= base_url('assets/adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/adminlte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') ?>">
<!-- DataTables JS -->
<script src="<?= base_url('assets/adminlte/plugins/datatables/jquery.dataTables.min.js') ?>"></script>
<script src="<?= base_url('assets/adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') ?>"></script>
<script src="<?= base_url('assets/adminlte/plugins/datatables-responsive/js/dataTables.responsive.min.js') ?>"></script>

<!-- Configuration variables for JS -->
<script>
    var RESIDENTS_CONFIG = {
        baseUrl: "<?= base_url() ?>",
        csrfName: "<?= csrf_token() ?>",
        csrfHash: "<?= csrf_hash() ?>",
        currentPurok: "<?= $selectedPurok ?? 'all' ?>"
    };
</script>

<!-- Custom JavaScript -->
<script src="<?= base_url('js/residents/residents-index.js') ?>"></script>
<?= $this->endSection() ?>
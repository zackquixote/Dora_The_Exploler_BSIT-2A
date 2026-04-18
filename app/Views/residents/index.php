<?= $this->extend('theme/template') ?>
<?= $this->section('content') ?>

<!-- DataTables CSS -->
<link rel="stylesheet" href="<?= base_url('assets/adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/adminlte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') ?>">

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Residents Management</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('staff/dashboard') ?>">Home</a></li>
                        <li class="breadcrumb-item active">Residents</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
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
                                <span class="badge badge-info ml-2">Filtered by: <?= $selectedPurok ?></span>
                            <?php endif; ?>
                        </h3>
                        <div class="d-flex flex-wrap gap-2">
                            <!-- Purok Filter Form - FIXED -->
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
                                    <th>ID</th>
                                    <th>Profile</th>
                                    <th>Full Name</th>
                                    <th>Sex</th>
                                    <th>Age</th>
                                    <th>Civil Status</th>
                                    <th>Purok/Sitio</th>
                                    <th>Household No.</th>
                                    <th>Occupation</th>
                                    <th>Citizenship</th>
                                    <th>Voter</th>
                                    <th>Flags</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($residents)): ?>
                                    <tr>
                                        <td colspan="13" class="text-center">
                                            No residents found <?= $selectedPurok != 'all' ? 'in ' . $selectedPurok : '' ?>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($residents as $r):
                                        $age = '';
                                        if (!empty($r['birthdate'])) {
                                            $birth = new DateTime($r['birthdate']);
                                            $today = new DateTime();
                                            $age = $birth->diff($today)->y;
                                        }
                                        
                                        $profileImg = !empty($r['profile_picture']) 
                                            ? base_url('uploads/' . $r['profile_picture']) 
                                            : base_url('assets/img/default.png');
                                        
                                        $voterBadge = $r['is_voter'] ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-secondary">No</span>';
                                        $seniorBadge = $r['is_senior_citizen'] ? '<span class="badge bg-info">Senior</span>' : '';
                                        $pwdBadge = $r['is_pwd'] ? '<span class="badge bg-warning">PWD</span>' : '';
                                        $flags = trim($seniorBadge . ' ' . $pwdBadge);
                                        
                                        $purokDisplay = !empty($r['sitio']) ? $r['sitio'] : 'Unassigned';
                                        $purokBadge = $purokDisplay != 'Unassigned' ? '<span class="badge bg-primary">' . esc($purokDisplay) . '</span>' : '<span class="badge bg-secondary">Unassigned</span>';
                                    ?>
                                        <tr>
                                            <td><?= $r['id'] ?></td>
                                            <td><img src="<?= $profileImg ?>" width="40" height="40" class="rounded-circle"></td>
                                            <td><?= esc($r['first_name']) ?> <?= esc($r['middle_name'] ?? '') ?> <?= esc($r['last_name']) ?></td>
                                            <td><?= ucfirst($r['sex']) ?></td>
                                            <td><?= $age ?></td>
                                            <td><?= ucfirst($r['civil_status'] ?? '') ?></td>
                                            <td><?= $purokBadge ?></td>
                                            <td><?= $r['household_no'] ?? '-' ?></td>
                                            <td><?= esc($r['occupation'] ?? '-') ?></td>
                                            <td><?= esc($r['citizenship'] ?? '-') ?></td>
                                            <td><?= $voterBadge ?></td>
                                            <td><?= $flags ?></td>
                                            <td>
                                                <a href="<?= base_url('resident/view/'.$r['id']) ?>" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                                <a href="<?= base_url('resident/edit/'.$r['id']) ?>" class="btn btn-sm btn-warning">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                                <button type="button" class="btn btn-sm btn-danger delete-resident" data-id="<?= $r['id'] ?>">
    <i class="fas fa-trash"></i> Delete
</button>
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
                                        <a href="<?= base_url('resident?purok=' . urlencode($purok)) ?>" class="purok-stat-card text-decoration-none">
                                            <div class="small-box bg-<?= $color ?> text-white p-3 text-center rounded">
                                                <h3 class="mb-1"><?= $count ?></h3>
                                                <small><?= $purok ?></small>
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

<!-- DataTables JS -->
<script src="<?= base_url('assets/adminlte/plugins/datatables/jquery.dataTables.min.js') ?>"></script>
<script src="<?= base_url('assets/adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') ?>"></script>
<script src="<?= base_url('assets/adminlte/plugins/datatables-responsive/js/dataTables.responsive.min.js') ?>"></script>

<script>
$(document).ready(function() {
    // Initialize DataTable
    var residentsTable = $('#residentsTable').DataTable({
        "order": [[0, "desc"]],
        "pageLength": 10,
        "responsive": true,
        "autoWidth": false,
        "language": {
            "search": "Search residents:",
            "emptyTable": "No residents found",
            "info": "Showing _START_ to _END_ of _TOTAL_ residents",
            "infoEmpty": "Showing 0 to 0 of 0 residents",
            "infoFiltered": "(filtered from _MAX_ total residents)"
        },
        "columnDefs": [
            { "orderable": true, "targets": [0, 2, 3, 4, 5, 6, 7, 8, 9] },
            { "orderable": false, "targets": [1, 10, 11, 12] }
        ]
    });
    
    // Delete resident handler
    $(document).on('click', '.delete-resident', function() {
        var residentId = $(this).data('id');
        var row = $(this).closest('tr');
        
        if (confirm('Are you sure you want to delete this resident? This action cannot be undone.')) {
            $.ajax({
                url: BASE_URL + 'resident/delete/' + residentId,
                type: 'POST',
                data: {
                    [CSRF_TOKEN_NAME]: CSRF_TOKEN_VALUE
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        residentsTable.row(row).remove().draw();
                        showAlert('success', 'Resident deleted successfully!');
                        setTimeout(function() {
                            location.reload();
                        }, 1000);
                    } else {
                        showAlert('danger', 'Delete failed: ' + (response.message || 'Unknown error'));
                    }
                },
                error: function() {
                    showAlert('danger', 'Error deleting resident. Please try again.');
                }
            });
        }
    });
    
    // Function to show alert messages
    function showAlert(type, message) {
        var alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        var alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="close" data-dismiss="alert">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        `;
        $('.alert').not('.alert-dismissible').remove();
        $('.content .container-fluid').prepend(alertHtml);
        setTimeout(function() {
            $('.alert').fadeOut('slow', function() {
                $(this).remove();
            });
        }, 3000);
    }
    
    // Highlight current purok in stats cards
    var currentPurok = "<?= $selectedPurok ?? 'all' ?>";
    if (currentPurok !== 'all') {
        $('.purok-stat-card').each(function() {
            var href = $(this).attr('href');
            if (href && href.indexOf(encodeURIComponent(currentPurok)) !== -1) {
                $(this).find('.small-box').css({
                    'border': '3px solid #ffc107',
                    'transform': 'scale(1.05)'
                });
            }
        });
    }
});

// Pass PHP variables to JavaScript
var BASE_URL = "<?= base_url() ?>";
var CSRF_TOKEN_NAME = "<?= csrf_token() ?>";
var CSRF_TOKEN_VALUE = "<?= csrf_hash() ?>";
</script>

<style>
.gap-2 { gap: 0.5rem; }
.small-box { transition: transform 0.2s, box-shadow 0.2s; cursor: pointer; }
.small-box:hover { transform: translateY(-5px); box-shadow: 0 4px 15px rgba(0,0,0,0.2); }
.bg-primary { background-color: #007bff !important; }
.bg-success { background-color: #28a745 !important; }
.bg-warning { background-color: #ffc107 !important; color: #333 !important; }
.bg-danger { background-color: #dc3545 !important; }
.bg-info { background-color: #17a2b8 !important; }
.bg-secondary { background-color: #6c757d !important; }
.text-white { color: #fff !important; }
.small-box h3 { font-size: 2rem; }
</style>

<?= $this->endSection() ?>
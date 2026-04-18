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
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">List of Residents</h3>
                    <a href="<?= base_url('resident/create') ?>" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus-circle mr-1"></i> Add New Resident
                    </a>
                </div>
                <div class="card-body">
                    <table id="residentsTable" class="table table-bordered table-striped table-sm w-100">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Profile</th>
                                <th>Full Name</th>
                                <th>Sex</th>
                                <th>Age</th>
                                <th>Civil Status</th>
                                <th>Household No.</th>
                                <th>Occupation</th>
                                <th>Citizenship</th>
                                <th>Voter</th>
                                <th>Flags</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $db = \Config\Database::connect();
                            $residents = $db->table('residents')
                                ->select('residents.*, households.household_no')
                                ->join('households', 'households.id = residents.household_id', 'left')
                                ->orderBy('residents.id', 'DESC')
                                ->get()
                                ->getResultArray();
                            
                            foreach ($residents as $r):
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
                            ?>
                            <tr>
                                <td><?= $r['id'] ?></td>
                                <td><img src="<?= $profileImg ?>" width="40" height="40" class="rounded-circle"></td>
                                <td><?= esc($r['first_name']) ?> <?= esc($r['middle_name']) ?> <?= esc($r['last_name']) ?></td>
                                <td><?= ucfirst($r['sex']) ?></td>
                                <td><?= $age ?></td>
                                <td><?= ucfirst($r['civil_status'] ?? '') ?></td>
                                <td><?= $r['household_no'] ?? '-' ?></td>
                                <td><?= esc($r['occupation'] ?? '-') ?></td>
                                <td><?= esc($r['citizenship'] ?? '-') ?></td>
                                <td><?= $voterBadge ?></td>
                                <td><?= $flags ?></td>
                                <td>
                                    <a href="<?= base_url('resident/view/'.$r['id']) ?>" class="btn btn-sm btn-info">View</a>
                                    <a href="<?= base_url('resident/edit/'.$r['id']) ?>" class="btn btn-sm btn-warning">Edit</a>
                                    <button class="btn btn-sm btn-danger delete-resident" data-id="<?= $r['id'] ?>">Delete</button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<script src="<?= base_url('assets/adminlte/plugins/datatables/jquery.dataTables.min.js') ?>"></script>
<script src="<?= base_url('assets/adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') ?>"></script>

<script>
$(document).ready(function() {
    $('#residentsTable').DataTable({
        "order": [[0, "desc"]],
        "pageLength": 10,
        "responsive": true
    });
    
    $(document).on('click', '.delete-resident', function() {
        var id = $(this).data('id');
        if (confirm('Are you sure you want to delete this resident?')) {
            $.ajax({
                url: '<?= base_url('resident/delete') ?>/' + id,
                type: 'POST',
                data: { '<?= csrf_token() ?>': '<?= csrf_hash() ?>' },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        location.reload();
                    } else {
                        alert('Delete failed');
                    }
                }
            });
        }
    });
});
</script>

<?= $this->endSection() ?>
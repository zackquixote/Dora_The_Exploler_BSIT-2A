<?= $this->extend('theme/template') ?>
<?= $this->section('content') ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Resident Details</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('staff/dashboard') ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('resident') ?>">Residents</a></li>
                        <li class="breadcrumb-item active">View</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle mr-1"></i> <?= session()->getFlashdata('success') ?>
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fas fa-exclamation-circle mr-1"></i> <?= session()->getFlashdata('error') ?>
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            <?php endif; ?>

            <div class="row">
                <!-- Profile Sidebar -->
                <div class="col-md-3">
                    <div class="card card-primary card-outline">
                        <div class="card-body box-profile">
                            <div class="text-center">
                                <img class="profile-user-img img-fluid img-circle"
                                     src="<?= base_url(!empty($resident['profile_picture']) ? 'uploads/' . $resident['profile_picture'] : 'assets/img/default.png') ?>"
                                     alt="Profile picture"
                                     style="width: 120px; height: 120px; object-fit: cover;">
                            </div>
                            <h3 class="profile-username text-center mt-2">
                                <?= esc($resident['first_name']) ?> <?= esc($resident['middle_name'] ?? '') ?> <?= esc($resident['last_name']) ?>
                            </h3>
                            <p class="text-muted text-center">
                                <?= ucfirst(esc($resident['civil_status'] ?? 'N/A')) ?>
                            </p>
                            
                            <ul class="list-group list-group-unbordered mb-3">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <b><i class="fas fa-venus-mars mr-2"></i>Gender</b>
                                    <span><?= ucfirst(esc($resident['sex'])) ?></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <b><i class="fas fa-calendar mr-2"></i>Birthdate</b>
                                    <span><?= esc($resident['birthdate']) ?></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <b><i class="fas fa-birthday-cake mr-2"></i>Age</b>
                                    <span>
                                        <?php
                                        if (!empty($resident['birthdate'])) {
                                            $birth = new DateTime($resident['birthdate']);
                                            $today = new DateTime();
                                            echo $birth->diff($today)->y . ' years old';
                                        } else {
                                            echo 'N/A';
                                        }
                                        ?>
                                    </span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <b><i class="fas fa-phone mr-2"></i>Contact</b>
                                    <span><?= esc($resident['contact_number'] ?? 'N/A') ?></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <b><i class="fas fa-circle mr-2"></i>Status</b>
                                    <span>
                                        <?php $status = $resident['status'] ?? 'active'; ?>
                                        <span class="badge badge-<?= $status == 'active' ? 'success' : 'secondary' ?>">
                                            <?= ucfirst($status) ?>
                                        </span>
                                    </span>
                                </li>
                            </ul>
                            
                            <a href="<?= base_url('resident/edit/' . $resident['id']) ?>" class="btn btn-primary btn-block">
                                <i class="fas fa-edit mr-1"></i> Edit Profile
                            </a>
                            <a href="<?= base_url('resident') ?>" class="btn btn-secondary btn-block mt-2">
                                <i class="fas fa-arrow-left mr-1"></i> Back to List
                            </a>
                        </div>
                    </div>
                    
                    <!-- Quick Actions Card -->
                    <div class="card card-info card-outline">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-bolt mr-1"></i> Quick Actions</h3>
                        </div>
                        <div class="card-body p-0">
                            <div class="list-group list-group-flush">
                                <a href="#" class="list-group-item list-group-item-action" onclick="printProfile()">
                                    <i class="fas fa-print mr-2 text-primary"></i> Print Profile
                                </a>
                                <a href="#" class="list-group-item list-group-item-action" onclick="generateCertificate()">
                                    <i class="fas fa-file-certificate mr-2 text-success"></i> Generate Certificate
                                </a>
                                <a href="#" class="list-group-item list-group-item-action text-danger" onclick="deleteResident()">
                                    <i class="fas fa-trash mr-2"></i> Delete Resident
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="col-md-9">
                    <div class="card">
                        <div class="card-header p-2">
                            <ul class="nav nav-pills">
                                <li class="nav-item">
                                    <a class="nav-link active" href="#personal" data-toggle="tab">
                                        <i class="fas fa-user mr-1"></i> Personal Details
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#household" data-toggle="tab">
                                        <i class="fas fa-home mr-1"></i> Household Information
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#status" data-toggle="tab">
                                        <i class="fas fa-flag-checkered mr-1"></i> Status & Flags
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content">
                                <!-- Personal Details Tab -->
                                <div class="tab-pane active" id="personal">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <table class="table table-bordered">
                                                <tr>
                                                    <th width="40%">First Name</th>
                                                    <td><?= esc($resident['first_name']) ?></td>
                                                </tr>
                                                <tr>
                                                    <th>Middle Name</th>
                                                    <td><?= esc($resident['middle_name'] ?? 'N/A') ?></td>
                                                </tr>
                                                <tr>
                                                    <th>Last Name</th>
                                                    <td><?= esc($resident['last_name']) ?></td>
                                                </tr>
                                                <tr>
                                                    <th>Gender</th>
                                                    <td><?= ucfirst(esc($resident['sex'])) ?></td>
                                                </tr>
                                                <tr>
                                                    <th>Birthdate</th>
                                                    <td><?= date('F d, Y', strtotime($resident['birthdate'])) ?></td>
                                                </tr>
                                                <tr>
                                                    <th>Civil Status</th>
                                                    <td><?= esc($resident['civil_status'] ?? 'N/A') ?></td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="col-md-6">
                                            <table class="table table-bordered">
                                                <tr>
                                                    <th width="40%">Occupation</th>
                                                    <td><?= esc($resident['occupation'] ?? 'N/A') ?></td>
                                                </tr>
                                                <tr>
                                                    <th>Citizenship</th>
                                                    <td><?= esc($resident['citizenship'] ?? 'N/A') ?></td>
                                                </tr>
                                                <tr>
                                                    <th>Contact Number</th>
                                                    <td><?= esc($resident['contact_number'] ?? 'N/A') ?></td>
                                                </tr>
                                                <tr>
                                                    <th>Registered Date</th>
                                                    <td>
                                                        <?= !empty($resident['created_at']) 
                                                            ? date('F d, Y', strtotime($resident['created_at'])) 
                                                            : 'N/A' ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Last Updated</th>
                                                    <td>
                                                        <?= !empty($resident['updated_at']) 
                                                            ? date('F d, Y h:i A', strtotime($resident['updated_at'])) 
                                                            : 'N/A' ?>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <!-- Household Information Tab -->
                                <div class="tab-pane" id="household">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th width="30%">Household Number</th>
                                            <td>
                                                <?php if (!empty($resident['household_no'])): ?>
                                                    <span class="badge badge-success" style="font-size: 1rem;">
                                                        <?= esc($resident['household_no']) ?>
                                                    </span>
                                                    <a href="<?= base_url('households/view/' . $resident['household_id']) ?>" 
                                                       class="btn btn-sm btn-outline-info ml-2">
                                                        <i class="fas fa-external-link-alt mr-1"></i> View Household
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-muted">Not assigned to any household</span>
                                                    <a href="<?= base_url('households/create') ?>" 
                                                       class="btn btn-sm btn-outline-primary ml-2">
                                                        <i class="fas fa-plus mr-1"></i> Create Household
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Complete Address</th>
                                            <td>
                                                <?php if (!empty($resident['household_address'])): ?>
                                                    <i class="fas fa-map-marker-alt text-danger mr-2"></i>
                                                    <?= esc($resident['household_address']) ?>
                                                <?php else: ?>
                                                    <span class="text-muted">No address on file</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Sitio / Zone</th>
                                            <td>
                                                <?php if (!empty($resident['sitio'])): ?>
                                                    <span class="badge badge-primary"><?= esc($resident['sitio']) ?></span>
                                                <?php else: ?>
                                                    <span class="text-muted">Unassigned</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Relationship to Head</th>
                                            <td>
                                                <?php if (!empty($resident['relationship_to_head'])): ?>
                                                    <span class="badge badge-info"><?= esc($resident['relationship_to_head']) ?></span>
                                                <?php else: ?>
                                                    <span class="text-muted">Not specified</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    </table>
                                    
                                    <?php if (!empty($resident['household_id'])): ?>
                                    <div class="alert alert-info mt-3">
                                        <i class="fas fa-info-circle mr-2"></i>
                                        <strong>Note:</strong> This resident's address is based on their assigned household. 
                                        To update the address, please edit the household information.
                                    </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Status & Flags Tab -->
                                <div class="tab-pane" id="status">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th width="30%">Registered Voter</th>
                                            <td>
                                                <?php if (!empty($resident['is_voter'])): ?>
                                                    <span class="badge badge-success">
                                                        <i class="fas fa-check-circle mr-1"></i> Yes
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge badge-secondary">
                                                        <i class="fas fa-times-circle mr-1"></i> No
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Senior Citizen</th>
                                            <td>
                                                <?php if (!empty($resident['is_senior_citizen'])): ?>
                                                    <span class="badge badge-info">
                                                        <i class="fas fa-user-graduate mr-1"></i> Yes
                                                    </span>
                                                    <small class="text-muted ml-2">
                                                        (Qualifies for senior citizen benefits)
                                                    </small>
                                                <?php else: ?>
                                                    <span class="badge badge-secondary">
                                                        <i class="fas fa-times-circle mr-1"></i> No
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Person with Disability (PWD)</th>
                                            <td>
                                                <?php if (!empty($resident['is_pwd'])): ?>
                                                    <span class="badge badge-warning">
                                                        <i class="fas fa-wheelchair mr-1"></i> Yes
                                                    </span>
                                                    <small class="text-muted ml-2">
                                                        (Qualifies for PWD benefits)
                                                    </small>
                                                <?php else: ?>
                                                    <span class="badge badge-secondary">
                                                        <i class="fas fa-times-circle mr-1"></i> No
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Account Status</th>
                                            <td>
                                                <?php 
                                                $status = $resident['status'] ?? 'active';
                                                $statusBadge = [
                                                    'active' => 'success',
                                                    'inactive' => 'secondary',
                                                    'deceased' => 'dark',
                                                    'transferred' => 'warning'
                                                ];
                                                $badge = $statusBadge[$status] ?? 'secondary';
                                                ?>
                                                <span class="badge badge-<?= $badge ?>">
                                                    <?= ucfirst($status) ?>
                                                </span>
                                            </td>
                                        </tr>
                                    </table>
                                    
                                    <?php if (!empty($resident['is_voter']) || !empty($resident['is_senior_citizen']) || !empty($resident['is_pwd'])): ?>
                                    <div class="alert alert-success mt-3">
                                        <i class="fas fa-check-circle mr-2"></i>
                                        <strong>Benefits Eligibility:</strong> This resident may qualify for the following:
                                        <ul class="mb-0 mt-2">
                                            <?php if (!empty($resident['is_senior_citizen'])): ?>
                                                <li>Senior Citizen discount (20% on purchases)</li>
                                            <?php endif; ?>
                                            <?php if (!empty($resident['is_pwd'])): ?>
                                                <li>PWD discount (20% on purchases)</li>
                                            <?php endif; ?>
                                            <?php if (!empty($resident['is_voter'])): ?>
                                                <li>Eligible to vote in barangay elections</li>
                                            <?php endif; ?>
                                        </ul>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
.profile-user-img {
    border: 3px solid #adb5bd;
    padding: 3px;
}
.list-group-item {
    border-left: none;
    border-right: none;
}
.table th {
    background-color: #f8f9fa;
    font-weight: 600;
}
.nav-pills .nav-link.active {
    background-color: #007bff;
}
.badge {
    padding: 5px 10px;
    font-size: 0.9rem;
}
</style>

<!-- SweetAlert2 for better dialogs -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Global variables -->
<script>
    var BASE_URL         = "<?= base_url() ?>";
    var CSRF_TOKEN_NAME  = "<?= csrf_token() ?>";
    var CSRF_TOKEN_VALUE = "<?= csrf_hash() ?>";
    var RESIDENT_ID      = "<?= $resident['id'] ?>";
    var RESIDENT_NAME    = "<?= esc($resident['first_name'] . ' ' . $resident['last_name'], 'js') ?>";
    
    // Legacy APP object kept for backward compatibility
    var APP = {
        baseUrl:     BASE_URL,
        csrfName:    CSRF_TOKEN_NAME,
        csrfHash:    CSRF_TOKEN_VALUE,
        currentPurok: "<?= isset($selectedPurok) ? esc($selectedPurok, 'js') : 'all' ?>"
    };
</script>

<!-- External JavaScript -->
<script src="<?= base_url('js/residents/residents-view.js') ?>"></script>

<!-- Inline functions for quick actions -->
<script>
function printProfile() {
    window.print();
}

function generateCertificate() {
    Swal.fire({
        title: 'Generate Certificate',
        text: 'Select certificate type for ' + RESIDENT_NAME,
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: 'Generate',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            // Redirect to certificate generation page
            // window.location.href = BASE_URL + 'certificates/generate/' + RESIDENT_ID;
            Swal.fire('Info', 'Certificate generation feature coming soon!', 'info');
        }
    });
}

function deleteResident() {
    Swal.fire({
        title: 'Delete Resident?',
        text: 'Are you sure you want to delete ' + RESIDENT_NAME + '? This action cannot be undone!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: BASE_URL + 'resident/delete/' + RESIDENT_ID,
                type: 'POST',
                data: {
                    [CSRF_TOKEN_NAME]: CSRF_TOKEN_VALUE
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        Swal.fire(
                            'Deleted!',
                            'Resident has been deleted.',
                            'success'
                        ).then(() => {
                            window.location.href = BASE_URL + 'resident';
                        });
                    } else {
                        Swal.fire('Error!', response.message || 'Failed to delete.', 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error!', 'An error occurred.', 'error');
                }
            });
        }
    });
}
</script>

<?= $this->endSection() ?>
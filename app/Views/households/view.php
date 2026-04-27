<?php
// ---------------------------------------------------------
// SMART THEME LOADER
// ---------------------------------------------------------
 $role = strtolower(session()->get('role') ?? 'staff');
 $template = ($role == 'admin') ? 'theme/admin/template' : 'theme/template';
?>

<?= $this->extend($template) ?>

<?= $this->section('content') ?>
<div class="content-wrapper bg-light">
    
    <!-- HEADER -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2 align-items-center">
                <div class="col-sm-8">
                    <div class="d-flex align-items-center">
                        <a href="<?= base_url('households') ?>" class="btn btn-light btn-sm rounded-circle mr-3 shadow-sm">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                        <div>
                            <h1 class="m-0 text-dark font-weight-bold">Household #<?= esc($household['household_no']) ?></h1>
                            <p class="text-muted mb-0 small"><i class="fas fa-map-marker-alt mr-1"></i> <?= esc($household['address'] ?? 'Address not set') ?></p>
                        </div>
                    </div>
                </div> 
                <div class="col-sm-4 text-right">
                    <div class="dropdown">
                        <button class="btn btn-primary shadow-sm" data-toggle="dropdown">
                            <i class="fas fa-ellipsis-v"></i> Actions
                        </button>
                        <div class="dropdown-menu dropdown-menu-right shadow-sm border-0">
                            <a class="dropdown-item" href="<?= base_url('households/edit/'.$household['id']) ?>">
                                <i class="fas fa-edit text-primary mr-2"></i> Edit Details
                            </a>
                            <a class="dropdown-item" href="<?= base_url('resident/create?household_id='.$household['id']) ?>">
                                <i class="fas fa-user-plus text-success mr-2"></i> Quick Add New Member
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item text-danger delete-household-view"
                               href="#"
                               data-id="<?= $household['id'] ?>"
                               data-no="<?= esc($household['household_no']) ?>">
                                <i class="fas fa-trash-alt mr-2"></i> Delete Household
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            
            <!-- STATISTICS ROW -->
            <?php
            $voterCount  = 0;
            $seniorCount = 0;
            $pwdCount    = 0;
            foreach ($residents as $r) {
                if (!empty($r['is_voter']) && $r['is_voter'] == 1) $voterCount++;
                if (!empty($r['is_senior_citizen']) && $r['is_senior_citizen'] == 1) $seniorCount++;
                if (!empty($r['is_pwd']) && $r['is_pwd'] == 1) $pwdCount++;
            }
            ?>
            
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white shadow-sm border-0 rounded-lg">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 class="mb-0 font-weight-bold"><?= $residentCount ?></h3>
                                    <small>Members</small>
                                </div>
                                <i class="fas fa-users fa-2x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-white text-dark shadow-sm border-0 rounded-lg">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 class="mb-0 font-weight-bold"><?= $voterCount ?></h3>
                                    <small class="text-muted">Voters</small>
                                </div>
                                <i class="fas fa-vote-yea fa-2x text-success opacity-20"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-white text-dark shadow-sm border-0 rounded-lg">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 class="mb-0 font-weight-bold"><?= $seniorCount ?></h3>
                                    <small class="text-muted">Seniors</small>
                                </div>
                                <i class="fas fa-user-clock fa-2x text-warning opacity-20"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-white text-dark shadow-sm border-0 rounded-lg">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 class="mb-0 font-weight-bold"><?= $pwdCount ?></h3>
                                    <small class="text-muted">PWD</small>
                                </div>
                                <i class="fas fa-wheelchair fa-2x text-secondary opacity-20"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- MAIN CONTENT -->
            <div class="row">
                <!-- LEFT PANEL -->
                <div class="col-md-4">
                    <!-- Head Profile Card -->
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-body box-profile text-center">
                            <?php 
                                $profileSrc = 'assets/img/default.png';
                                if ($headResident && !empty($headResident['profile_picture'])) {
                                    $profileSrc = 'uploads/' . $headResident['profile_picture'];
                                }
                                $headName = $headResident ? ($headResident['first_name'] . ' ' . $headResident['last_name']) : 'Unassigned';
                            ?>
                            <img class="profile-user-img img-fluid img-circle"
                                 src="<?= base_url($profileSrc) ?>"
                                 alt="User profile picture"
                                 style="width: 120px; height: 120px; object-fit: cover;">
                            
                            <h3 class="profile-username text-center mt-2 font-weight-bold">
                                <?= esc($headName) ?>
                            </h3>
                            <p class="text-muted text-center mb-3">Head of Household</p>
                            
                            <div class="d-flex justify-content-center mb-3">
                                <?php if ($headResident && !empty($headResident['is_voter']) && $headResident['is_voter'] == 1): ?>
                                    <span class="badge badge-success mr-1"><i class="fas fa-check"></i> Voter</span>
                                <?php endif; ?>
                                <?php if ($headResident && !empty($headResident['is_senior_citizen']) && $headResident['is_senior_citizen'] == 1): ?>
                                    <span class="badge badge-info mr-1"><i class="fas fa-user-graduate"></i> Senior</span>
                                <?php endif; ?>
                            </div>

                            <hr>
                            
                            <div class="text-left small">
                                <p class="mb-1 d-flex align-items-center">
                                    <strong class="w-25"><i class="fas fa-briefcase text-muted mr-2"></i></strong> 
                                    <span class="text-truncate" title="<?= esc($headResident['occupation'] ?? 'N/A') ?>">
                                        <?= esc($headResident['occupation'] ?? 'N/A') ?>
                                    </span>
                                </p>
                                <p class="mb-1 d-flex align-items-center">
                                    <strong class="w-25"><i class="fas fa-phone text-muted mr-2"></i></strong> 
                                    <span class="text-truncate" title="<?= esc($headResident['contact_number'] ?? 'N/A') ?>">
                                        <?= esc($headResident['contact_number'] ?? 'N/A') ?>
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Location & Details -->
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white border-bottom-0">
                            <h5 class="card-title text-dark mb-0">Location Details</h5>
                        </div>
                        <div class="card-body">
                            <div class="list-group list-group-flush">
                                <div class="list-group-item px-0 pb-2 border-0">
                                    <small class="text-muted text-uppercase font-weight-bold" style="font-size: 10px;">Sitio / Purok</small>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-map-pin text-primary mr-2"></i> <?= esc($household['sitio'] ?? '—') ?>
                                    </div>
                                </div>
                                <div class="list-group-item px-0 pb-2 border-0">
                                    <small class="text-muted text-uppercase font-weight-bold" style="font-size: 10px;">Street Address</small>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-road text-primary mr-2"></i> <?= esc($household['street_address'] ?? '—') ?>
                                    </div>
                                </div>
                                <div class="list-group-item px-0 pb-2 border-0">
                                    <small class="text-muted text-uppercase font-weight-bold" style="font-size: 10px;">House Type</small>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-home text-primary mr-2"></i> <?= esc($household['house_type'] ?? '—') ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- RIGHT PANEL - Members -->
                <div class="col-md-8">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white border-bottom-0 d-flex justify-content-between align-items-center">
                            <h5 class="card-title text-dark mb-0 font-weight-bold">Household Members (<?= $residentCount ?>)</h5>
                            <button type="button" class="btn btn-sm btn-primary shadow-sm" data-toggle="modal" data-target="#addMemberModal">
                                <i class="fas fa-plus"></i> Add Member
                            </button>
                        </div>
                        <div class="card-body p-0">
                            <?php if (empty($residents)): ?>
                                <div class="text-center py-5">
                                    <img src="<?= base_url('assets/img/default.png') ?>" class="rounded-circle mb-3 opacity-50" style="width:80px;height:80px;">
                                    <p class="text-muted">No members assigned to this household yet.</p>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0 table-align-middle">
                                        <thead class="bg-light">
                                            <tr>
                                                <th class="pl-4">Resident</th>
                                                <th>Age</th>
                                                <th>Relationship</th>
                                                <th>Status</th>
                                                <th class="pr-4">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($residents as $r): 
                                                $age = '';
                                                if (!empty($r['birthdate'])) {
                                                    $age = (new DateTime($r['birthdate']))->diff(new DateTime())->y;
                                                }
                                                $isHead = ($headResident && $headResident['id'] == $r['id']);
                                                $fullName = $r['first_name'] . ' ' . $r['last_name'];
                                                $memberImg = 'https://ui-avatars.com/api/?name='.urlencode($fullName).'&background=random&color=fff&size=32';
                                                if (!empty($r['profile_picture'])) {
                                                    $memberImg = base_url('uploads/' . $r['profile_picture']);
                                                }
                                            ?>
                                            <tr>
                                                <td class="pl-4">
                                                    <div class="d-flex align-items-center">
                                                        <img src="<?= $memberImg ?>" 
                                                             class="rounded-circle mr-3" style="width:32px;height:32px;object-fit:cover;" alt="">
                                                        <div>
                                                            <div class="font-weight-bold text-dark"><?= esc($fullName) ?></div>
                                                            <?php if ($isHead): ?>
                                                                <span class="badge badge-warning badge-pill badge-sm mt-1"><i class="fas fa-star mr-1"></i>Head</span>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-muted"><?= $age ? $age . ' yrs' : '—' ?></td>
                                                <td><?= esc(ucfirst($r['relationship_to_head'] ?? '—')) ?></td>
                                                <td>
                                                    <?php if (!empty($r['is_voter']) && $r['is_voter'] == 1): ?>
                                                        <span class="badge badge-light text-success border mr-1" title="Voter"><i class="fas fa-check"></i></span>
                                                    <?php endif; ?>
                                                    <?php if (!empty($r['is_senior_citizen']) && $r['is_senior_citizen'] == 1): ?>
                                                        <span class="badge badge-light text-info border mr-1" title="Senior"><i class="fas fa-user-graduate"></i></span>
                                                    <?php endif; ?>
                                                    <?php if (!empty($r['is_pwd']) && $r['is_pwd'] == 1): ?>
                                                        <span class="badge badge-light text-warning border mr-1" title="PWD"><i class="fas fa-wheelchair"></i></span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="pr-4 text-right">
                                                    <a href="<?= base_url('resident/view/'.$r['id']) ?>" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="<?= base_url('resident/edit/'.$r['id']) ?>" class="btn btn-sm btn-outline-secondary ml-1">
                                                        <i class="fas fa-pen"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- ADD MEMBER MODAL -->
<div class="modal fade" id="addMemberModal" tabindex="-1" role="dialog" aria-labelledby="addMemberModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content shadow border-0">
            <div class="modal-header bg-light">
                <h5 class="modal-title font-weight-bold">Add Member to Household</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p class="text-muted text-center mb-4">Choose an option below:</p>
                
                <div class="d-grid gap-3">
                    <!-- Option 1: Add New -->
                    <a href="<?= base_url('resident/create?household_id='.$household['id']) ?>" class="card border-0 shadow-sm text-decoration-none text-dark">
                        <div class="card-body p-3 text-center">
                            <div class="mb-2">
                                <i class="fas fa-user-plus fa-2x text-primary"></i>
                            </div>
                            <h6 class="font-weight-bold mb-1">Create New Resident</h6>
                            <p class="small text-muted mb-0">Add a person who is not yet in the system.</p>
                        </div>
                    </a>

                    <!-- Option 2: Add Existing -->
                    <a href="<?= base_url('resident/assign-search?household_id='.$household['id']) ?>" class="card border-0 shadow-sm text-decoration-none text-dark">
                        <div class="card-body p-3 text-center">
                            <div class="mb-2">
                                <i class="fas fa-search fa-2x text-success"></i>
                            </div>
                            <h6 class="font-weight-bold mb-1">Add Existing Resident</h6>
                            <p class="small text-muted mb-0">Search for a resident already in the database.</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JS VARIABLES BRIDGE -->
<div id="js-variables" style="display:none;"
     data-base-url="<?= base_url() ?>"
     data-csrf-token="<?= csrf_token() ?>"
     data-csrf-hash="<?= csrf_hash() ?>"
     data-resident-count="<?= $residentCount ?>">
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('js/households/households-view.js') ?>"></script>
<?= $this->endSection() ?>
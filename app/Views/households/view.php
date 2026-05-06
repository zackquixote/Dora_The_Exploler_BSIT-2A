<?php
$role = strtolower(session()->get('role') ?? 'staff');
$template = ($role == 'admin') ? 'theme/admin/template' : 'theme/template';
?>
<?= $this->extend($template) ?>
<?= $this->section('content') ?>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= base_url('assets/css/household/view.css') ?>">

<div class="content-wrapper">
    <section class="content px-3 pt-4">
        <div class="container-fluid">
            
            <div class="hh-hero">
                <div class="hh-hero-content">
                    <div class="hh-title-group">
                        <a href="<?= base_url('households') ?>" class="hh-back-btn" title="Back to Directory">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                        <div>
                            <h1 class="hh-title">Household #<?= esc($household['household_no']) ?></h1>
                            <p class="hh-subtitle">
                                <i class="fas fa-map-marker-alt"></i> 
                                <?= esc($household['address'] ?? 'Address not set') ?>
                            </p>
                        </div>
                    </div>
                    
                    <div class="hh-actions">
                        <div class="dropdown">
                            <button class="hh-btn" data-toggle="dropdown">
                                <i class="fas fa-cog"></i> Manage
                            </button>
                            <div class="dropdown-menu dropdown-menu-right shadow-sm border-0 mt-2" style="border-radius: 12px; overflow: hidden;">
                                <a class="dropdown-item py-2" href="<?= base_url('households/edit/'.$household['id']) ?>">
                                    <i class="fas fa-edit text-primary mr-2 w-20px text-center"></i> Edit Details
                                </a>
                                <a class="dropdown-item py-2" href="<?= base_url('resident/create?household_id='.$household['id']) ?>">
                                    <i class="fas fa-user-plus text-success mr-2 w-20px text-center"></i> Quick Add Member
                                </a>
                                <div class="dropdown-divider my-0"></div>
                                <a class="dropdown-item py-2 text-danger delete-household-view" href="#"
                                   data-id="<?= $household['id'] ?>"
                                   data-no="<?= esc($household['household_no']) ?>">
                                    <i class="fas fa-trash-alt mr-2 w-20px text-center"></i> Delete Household
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php
            $voterCount = $seniorCount = $pwdCount = 0;
            foreach ($residents as $r) {
                if (!empty($r['is_voter']) && $r['is_voter'] == 1) $voterCount++;
                if (!empty($r['is_senior_citizen']) && $r['is_senior_citizen'] == 1) $seniorCount++;
                if (!empty($r['is_pwd']) && $r['is_pwd'] == 1) $pwdCount++;
            }
            ?>
            <div class="hh-stats-grid">
                <div class="hh-stat-card hh-stat-primary">
                    <div class="hh-stat-info">
                        <h3><?= $residentCount ?></h3>
                        <p>Total Members</p>
                    </div>
                    <div class="hh-stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
                <div class="hh-stat-card hh-stat-success">
                    <div class="hh-stat-info">
                        <h3><?= $voterCount ?></h3>
                        <p>Registered Voters</p>
                    </div>
                    <div class="hh-stat-icon">
                        <i class="fas fa-vote-yea"></i>
                    </div>
                </div>
                <div class="hh-stat-card hh-stat-warning">
                    <div class="hh-stat-info">
                        <h3><?= $seniorCount ?></h3>
                        <p>Senior Citizens</p>
                    </div>
                    <div class="hh-stat-icon">
                        <i class="fas fa-user-clock"></i>
                    </div>
                </div>
                <div class="hh-stat-card hh-stat-secondary">
                    <div class="hh-stat-info">
                        <h3><?= $pwdCount ?></h3>
                        <p>PWD Members</p>
                    </div>
                    <div class="hh-stat-icon">
                        <i class="fas fa-wheelchair"></i>
                    </div>
                </div>
            </div>

            <div class="hh-main-grid">
                
                <!-- Left Sidebar -->
                <div class="hh-sidebar">
                    <div class="hh-card">
                        <div class="hh-head-profile">
                            <?php 
                                $profileSrc = 'assets/img/default.png';
                                if ($headResident && !empty($headResident['profile_picture'])) {
                                    $profileSrc = 'uploads/' . $headResident['profile_picture'];
                                }
                                $headName = $headResident ? ($headResident['first_name'] . ' ' . $headResident['last_name']) : 'Unassigned';
                            ?>
                            <div class="hh-avatar-wrapper">
                                <img class="hh-avatar" src="<?= base_url($profileSrc) ?>" alt="Head Profile">
                                <div class="hh-head-badge" title="Head of Household">
                                    <i class="fas fa-star"></i>
                                </div>
                            </div>
                            <h3 class="hh-head-name"><?= esc($headName) ?></h3>
                            <div class="hh-head-role">Head of Household</div>
                            
                            <div class="hh-head-flags">
                                <?php if ($headResident && !empty($headResident['is_voter'])): ?>
                                    <span class="hh-flag-badge hh-flag-voter"><i class="fas fa-check"></i> Voter</span>
                                <?php endif; ?>
                                <?php if ($headResident && !empty($headResident['is_senior_citizen'])): ?>
                                    <span class="hh-flag-badge hh-flag-senior"><i class="fas fa-user-graduate"></i> Senior</span>
                                <?php endif; ?>
                            </div>
                            
                            <div class="hh-head-details">
                                <div class="hh-detail-item">
                                    <div class="hh-detail-icon"><i class="fas fa-briefcase"></i></div>
                                    <div class="hh-detail-text">
                                        <span class="hh-detail-label">Occupation</span>
                                        <span class="hh-detail-value"><?= esc($headResident['occupation'] ?? 'N/A') ?></span>
                                    </div>
                                </div>
                                <div class="hh-detail-item">
                                    <div class="hh-detail-icon"><i class="fas fa-phone"></i></div>
                                    <div class="hh-detail-text">
                                        <span class="hh-detail-label">Contact No.</span>
                                        <span class="hh-detail-value"><?= esc($headResident['contact_number'] ?? 'N/A') ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="hh-card">
                        <div class="hh-card-header">
                            <h3 class="hh-card-title"><i class="fas fa-map-marked-alt"></i> Location Details</h3>
                        </div>
                        <div class="hh-card-body p-0">
                            <div class="list-group list-group-flush">
                                <div class="list-group-item px-4 py-3 border-0">
                                    <small class="text-muted text-uppercase font-weight-bold d-block mb-1" style="font-size: 10px;">Sitio / Purok</small>
                                    <div class="font-weight-bold text-dark d-flex align-items-center">
                                        <i class="fas fa-map-pin text-primary mr-2 opacity-50"></i> <?= esc($household['sitio'] ?? '—') ?>
                                    </div>
                                </div>
                                <div class="list-group-item px-4 py-3 border-0 border-top">
                                    <small class="text-muted text-uppercase font-weight-bold d-block mb-1" style="font-size: 10px;">Street Address</small>
                                    <div class="font-weight-bold text-dark d-flex align-items-center">
                                        <i class="fas fa-road text-primary mr-2 opacity-50"></i> <?= esc($household['street_address'] ?? '—') ?>
                                    </div>
                                </div>
                                <div class="list-group-item px-4 py-3 border-0 border-top">
                                    <small class="text-muted text-uppercase font-weight-bold d-block mb-1" style="font-size: 10px;">House Type</small>
                                    <div class="font-weight-bold text-dark d-flex align-items-center">
                                        <i class="fas fa-home text-primary mr-2 opacity-50"></i> <?= esc($household['house_type'] ?? '—') ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="hh-main-content">
                    <div class="hh-card">
                        <div class="hh-card-header">
                            <h3 class="hh-card-title"><i class="fas fa-users"></i> Household Members (<?= $residentCount ?>)</h3>
                            <button type="button" class="btn btn-primary btn-sm shadow-sm font-weight-bold px-3" data-toggle="modal" data-target="#addMemberModal">
                                <i class="fas fa-plus mr-1"></i> Add Member
                            </button>
                        </div>
                        <div class="hh-card-body p-0">
                            <?php if (empty($residents)): ?>
                                <div class="hh-empty-state">
                                    <div class="hh-empty-icon"><i class="fas fa-user-slash"></i></div>
                                    <h3>No Members Found</h3>
                                    <p>There are no members assigned to this household yet.</p>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive hh-table-wrapper">
                                    <table class="hh-table">
                                        <thead>
                                            <tr>
                                                <th>Resident</th>
                                                <th>Age</th>
                                                <th>Relationship</th>
                                                <th>Membership</th>
                                                <th>Flags</th>
                                                <th class="text-right">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($residents as $r): 
                                                $age = $r['age'] ?? '';
                                                if (empty($age) && !empty($r['birthdate'])) {
                                                    $age = (new DateTime($r['birthdate']))->diff(new DateTime())->y;
                                                }
                                                $isHead = ($headResident && $headResident['id'] == $r['id']);
                                                $fullName = $r['first_name'] . ' ' . $r['last_name'];
                                                $memberImg = !empty($r['profile_picture'])
                                                    ? base_url('uploads/' . $r['profile_picture'])
                                                    : 'https://ui-avatars.com/api/?name='.urlencode($fullName).'&background=random&color=fff&size=32';
                                                $memberStatus = $r['member_status'] ?? 'Active';
                                                $statusColors = [
                                                    'Active'      => 'success',
                                                    'Inactive'    => 'secondary',
                                                    'Transferred' => 'warning',
                                                    'Deceased'    => 'dark'
                                                ];
                                                $statusClass = $statusColors[$memberStatus] ?? 'secondary';
                                            ?>
                                                <tr id="member-row-<?= $r['id'] ?>">
                                                    <td>
                                                        <div class="hh-member-cell">
                                                            <img src="<?= $memberImg ?>" class="hh-member-avatar" alt="">
                                                            <div class="hh-member-info">
                                                                <h4><?= esc($fullName) ?></h4>
                                                                <?php if ($isHead): ?>
                                                                    <span class="badge badge-warning badge-pill badge-sm" style="font-size:0.6rem;"><i class="fas fa-star mr-1"></i>Head</span>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="font-weight-bold"><?= $age ? $age . ' <span class="text-muted font-weight-normal">yrs</span>' : '—' ?></span>
                                                    </td>
                                                    <td>
                                                        <span class="font-weight-bold text-dark"><?= esc(ucfirst($r['relationship_to_head'] ?? '—')) ?></span>
                                                    </td>
                                                    <td>
                                                        <span id="membership-display-<?= $r['id'] ?>">
                                                            <span class="hh-badge hh-badge-<?= $statusClass ?>" id="membership-badge-<?= $r['id'] ?>">
                                                                <?= esc($memberStatus) ?>
                                                            </span>
                                                            <button type="button" class="btn btn-link btn-sm p-0 ml-2 text-muted edit-membership-icon" 
                                                                    data-resident-id="<?= $r['id'] ?>" title="Edit status">
                                                                <i class="fas fa-pencil-alt" style="font-size: 0.8rem;"></i>
                                                            </button>
                                                        </span>
                                                        <span id="membership-editor-<?= $r['id'] ?>" style="display:none;" class="d-flex align-items-center gap-1">
                                                            <select id="membership-select-<?= $r['id'] ?>"
                                                                    class="form-control form-control-sm border-light shadow-sm"
                                                                    style="width:auto; font-size:0.8rem; height: 28px;">
                                                                <option value="Active" <?= $memberStatus == 'Active' ? 'selected' : '' ?>>Active</option>
                                                                <option value="Inactive" <?= $memberStatus == 'Inactive' ? 'selected' : '' ?>>Inactive</option>
                                                                <option value="Transferred" <?= $memberStatus == 'Transferred' ? 'selected' : '' ?>>Transferred</option>
                                                                <option value="Deceased" <?= $memberStatus == 'Deceased' ? 'selected' : '' ?>>Deceased</option>
                                                            </select>
                                                            <button type="button" class="btn btn-link btn-sm p-0 ml-1 text-success save-membership-icon" 
                                                                    data-resident-id="<?= $r['id'] ?>" title="Save">
                                                                <i class="fas fa-check"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-link btn-sm p-0 ml-1 text-danger cancel-membership-icon" 
                                                                    data-resident-id="<?= $r['id'] ?>" title="Cancel">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <?php if (!empty($r['is_voter'])): ?>
                                                            <span class="hh-badge hh-badge-success mr-1" title="Voter" style="padding: 0.35rem;"><i class="fas fa-check"></i></span>
                                                        <?php endif; ?>
                                                        <?php if (!empty($r['is_senior_citizen'])): ?>
                                                            <span class="hh-badge hh-badge-secondary mr-1" title="Senior" style="padding: 0.35rem;"><i class="fas fa-user-graduate"></i></span>
                                                        <?php endif; ?>
                                                        <?php if (!empty($r['is_pwd'])): ?>
                                                            <span class="hh-badge hh-badge-warning mr-1" title="PWD" style="padding: 0.35rem;"><i class="fas fa-wheelchair"></i></span>
                                                        <?php endif; ?>
                                                        <?php if (empty($r['is_voter']) && empty($r['is_senior_citizen']) && empty($r['is_pwd'])): ?>
                                                            <span class="text-muted" style="font-size:0.8rem">—</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <div class="hh-actions-cell">
                                                            <a href="<?= base_url('resident/view/'.$r['id']) ?>" class="hh-action-btn" title="View Profile">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            <a href="<?= base_url('resident/edit/'.$r['id']) ?>" class="hh-action-btn" title="Edit Profile">
                                                                <i class="fas fa-pen"></i>
                                                            </a>
                                                        </div>
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

<div class="modal fade" id="addMemberModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content shadow border-0">
            <div class="modal-header bg-light">
                <h5 class="modal-title font-weight-bold">Add Member to Household</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <p class="text-muted text-center mb-4">Choose an option below:</p>
                <div class="d-grid gap-3">
                    <a href="<?= base_url('resident/create?household_id='.$household['id']) ?>" class="card border-0 shadow-sm text-decoration-none text-dark">
                        <div class="card-body p-3 text-center">
                            <div class="mb-2"><i class="fas fa-user-plus fa-2x text-primary"></i></div>
                            <h6 class="font-weight-bold mb-1">Create New Resident</h6>
                            <p class="small text-muted mb-0">Add a person who is not yet in the system.</p>
                        </div>
                    </a>
                    <a href="<?= base_url('resident/assign-search?household_id='.$household['id']) ?>" class="card border-0 shadow-sm text-decoration-none text-dark">
                        <div class="card-body p-3 text-center">
                            <div class="mb-2"><i class="fas fa-search fa-2x text-success"></i></div>
                            <h6 class="font-weight-bold mb-1">Add Existing Resident</h6>
                            <p class="small text-muted mb-0">Search for a resident already in the database.</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

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
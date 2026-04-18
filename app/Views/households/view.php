<?= $this->extend('theme/template') ?>
<?= $this->section('content') ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Household Details</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('staff/dashboard') ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('households') ?>">Households</a></li>
                        <li class="breadcrumb-item active">View</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-4">
                    <div class="card card-primary card-outline">
                        <div class="card-body box-profile">
                            <div class="text-center">
                                <i class="fas fa-home fa-4x text-primary mb-3"></i>
                            </div>
                            <h3 class="profile-username text-center">
                                Household #<?= esc($household['household_no']) ?>
                            </h3>
                            <p class="text-muted text-center">
                                <span class="badge bg-primary"><?= esc($household['sitio']) ?></span>
                            </p>
                            <ul class="list-group list-group-unbordered mb-3">
                                <li class="list-group-item">
                                    <b>House Type</b> 
                                    <span class="float-right"><?= esc($household['house_type'] ?? 'N/A') ?></span>
                                </li>
                                <li class="list-group-item">
                                    <b>Total Members</b> 
                                    <span class="float-right">
                                        <span class="badge bg-info"><?= $residentCount ?> residents</span>
                                    </span>
                                </li>
                                <li class="list-group-item">
                                    <b>Head of Household</b> 
                                    <span class="float-right">
                                        <?php if ($headResident): ?>
                                            <strong><?= esc($headResident['first_name']) ?> <?= esc($headResident['last_name']) ?></strong>
                                        <?php else: ?>
                                            <span class="text-muted">Not assigned</span>
                                        <?php endif; ?>
                                    </span>
                                </li>
                                <li class="list-group-item">
                                    <b>Address</b> 
                                    <span class="float-right"><?= esc($household['street_address'] ?? $household['address'] ?? 'N/A') ?></span>
                                </li>
                            </ul>
                            <a href="<?= base_url('households/edit/'.$household['id']) ?>" class="btn btn-primary btn-block">
                                <i class="fas fa-edit"></i> Edit Household
                            </a>
                            <a href="<?= base_url('households') ?>" class="btn btn-secondary btn-block mt-2">
                                <i class="fas fa-arrow-left"></i> Back to List
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-users mr-2"></i> Household Members
                            </h3>
                            <div class="card-tools">
                                <a href="<?= base_url('resident/create?household_id=' . $household['id']) ?>" class="btn btn-primary btn-sm">
                                    <i class="fas fa-user-plus"></i> Add Member
                                </a>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <?php if (empty($residents)): ?>
                                <div class="text-center p-5">
                                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No residents assigned to this household yet.</p>
                                    <a href="<?= base_url('resident/create?household_id=' . $household['id']) ?>" class="btn btn-primary">
                                        <i class="fas fa-user-plus"></i> Add First Member
                                    </a>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped mb-0">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Profile</th>
                                                <th>Full Name</th>
                                                <th>Gender</th>
                                                <th>Age</th>
                                                <th>Civil Status</th>
                                                <th>Relationship to Head</th>
                                                <th>Contact</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($residents as $resident): 
                                                $age = '';
                                                if (!empty($resident['birthdate'])) {
                                                    $birth = new DateTime($resident['birthdate']);
                                                    $today = new DateTime();
                                                    $age = $birth->diff($today)->y;
                                                }
                                                
                                                $isHead = ($headResident && $headResident['id'] == $resident['id']);
                                                $profileImg = !empty($resident['profile_picture']) 
                                                    ? base_url('uploads/' . $resident['profile_picture']) 
                                                    : base_url('assets/img/default.png');
                                            ?>
                                                <tr>
                                                    <td><?= $resident['id'] ?>
                                                        <?php if ($isHead): ?>
                                                            <span class="badge bg-warning ml-1">Head</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><img src="<?= $profileImg ?>" width="40" height="40" class="rounded-circle"></td>
                                                    <td>
                                                        <strong><?= esc($resident['first_name']) ?> <?= esc($resident['last_name']) ?></strong>
                                                        <?php if (!empty($resident['middle_name'])): ?>
                                                            <br><small><?= esc($resident['middle_name']) ?></small>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?= ucfirst($resident['sex']) ?></td>
                                                    <td><?= $age ?> yrs old</td>
                                                    <td><?= ucfirst($resident['civil_status'] ?? 'N/A') ?></td>
                                                    <td><?= esc($resident['relationship_to_head'] ?? 'N/A') ?></td>
                                                    <td><?= esc($resident['contact_number'] ?? 'N/A') ?></td>
                                                    <td>
                                                        <a href="<?= base_url('resident/view/'.$resident['id']) ?>" class="btn btn-sm btn-info">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="<?= base_url('resident/edit/'.$resident['id']) ?>" class="btn btn-sm btn-warning">
                                                            <i class="fas fa-edit"></i>
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

<style>
.profile-user-img {
    width: 100px;
    height: 100px;
    object-fit: cover;
}
.table td {
    vertical-align: middle;
}
</style>

<?= $this->endSection() ?>
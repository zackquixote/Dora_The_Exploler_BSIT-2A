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
            <div class="row">
                <div class="col-md-3">
                    <div class="card card-primary card-outline">
                        <div class="card-body box-profile">
                            <div class="text-center">
                                <img class="profile-user-img img-fluid img-circle"
                                     src="<?= base_url(!empty($resident['profile_picture']) ? 'uploads/'.$resident['profile_picture'] : 'assets/img/default.png') ?>"
                                     alt="Profile picture">
                            </div>
                            <h3 class="profile-username text-center">
                                <?= esc($resident['first_name']) ?> <?= esc($resident['middle_name']) ?> <?= esc($resident['last_name']) ?>
                            </h3>
                            <p class="text-muted text-center"><?= ucfirst(esc($resident['civil_status'] ?? 'N/A')) ?></p>
                            <ul class="list-group list-group-unbordered mb-3">
                                <li class="list-group-item">
                                    <b>Gender</b> <span class="float-right"><?= ucfirst(esc($resident['sex'])) ?></span>
                                </li>
                                <li class="list-group-item">
                                    <b>Birthdate</b> <span class="float-right"><?= esc($resident['birthdate']) ?></span>
                                </li>
                                <li class="list-group-item">
                                    <b>Age</b> <span class="float-right">
                                        <?php 
                                        if (!empty($resident['birthdate'])) {
                                            $birth = new DateTime($resident['birthdate']);
                                            $today = new DateTime();
                                            echo $birth->diff($today)->y;
                                        } else {
                                            echo 'N/A';
                                        }
                                        ?>
                                    </span>
                                </li>
                                <li class="list-group-item">
                                    <b>Contact</b> <span class="float-right"><?= esc($resident['contact_number'] ?? 'N/A') ?></span>
                                </li>
                            </ul>
                            <a href="<?= base_url('resident/edit/'.$resident['id']) ?>" class="btn btn-primary btn-block">
                                <i class="fas fa-edit"></i> Edit Profile
                            </a>
                            <a href="<?= base_url('resident') ?>" class="btn btn-secondary btn-block mt-2">
                                <i class="fas fa-arrow-left"></i> Back to List
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-md-9">
                    <div class="card">
                        <div class="card-header p-2">
                            <ul class="nav nav-pills">
                                <li class="nav-item"><a class="nav-link active" href="#details" data-toggle="tab">Personal Details</a></li>
                                <li class="nav-item"><a class="nav-link" href="#address" data-toggle="tab">Address Information</a></li>
                                <li class="nav-item"><a class="nav-link" href="#status" data-toggle="tab">Status & Flags</a></li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content">
                                <div class="tab-pane active" id="details">
                                    <table class="table table-bordered">
                                        <tr><th width="30%">First Name</th><td><?= esc($resident['first_name']) ?></td></tr>
                                        <tr><th>Middle Name</th><td><?= esc($resident['middle_name'] ?? 'N/A') ?></td></tr>
                                        <tr><th>Last Name</th><td><?= esc($resident['last_name']) ?></td></tr>
                                        <tr><th>Occupation</th><td><?= esc($resident['occupation'] ?? 'N/A') ?></td></tr>
                                        <tr><th>Citizenship</th><td><?= esc($resident['citizenship'] ?? 'N/A') ?></td></tr>
                                    </table>
                                </div>
                                <div class="tab-pane" id="address">
                                    <table class="table table-bordered">
                                        <tr><th width="30%">Household No.</th><td><?= esc($resident['household_no'] ?? 'N/A') ?></td></tr>
                                        <tr><th>Street Address</th><td><?= esc($resident['street_address'] ?? 'N/A') ?></td></tr>
                                        <tr><th>Sitio / Zone</th><td><?= esc($resident['sitio'] ?? 'N/A') ?></td></tr>
                                        <tr><th>Relationship to Head</th><td><?= esc($resident['relationship_to_head'] ?? 'N/A') ?></td></tr>
                                    </table>
                                </div>
                                <div class="tab-pane" id="status">
                                    <table class="table table-bordered">
                                        <tr><th width="30%">Voter</th><td><?= !empty($resident['is_voter']) ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-secondary">No</span>' ?></td></tr>
                                        <tr><th>Senior Citizen</th><td><?= !empty($resident['is_senior_citizen']) ? '<span class="badge bg-info">Yes</span>' : '<span class="badge bg-secondary">No</span>' ?></td></tr>
                                        <tr><th>PWD</th><td><?= !empty($resident['is_pwd']) ? '<span class="badge bg-warning">Yes</span>' : '<span class="badge bg-secondary">No</span>' ?></td></tr>
                                        <tr><th>Status</th><td><span class="badge bg-success"><?= ucfirst(esc($resident['status'] ?? 'Active')) ?></span></td></tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?= $this->endSection() ?>
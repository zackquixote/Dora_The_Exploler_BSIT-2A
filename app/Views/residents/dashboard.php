<?= $this->extend('theme/template') ?>
<?= $this->section('content') ?>

<div class="content-wrapper" style="background:#f4f6f9;">
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <i class="fas fa-tachometer-alt mr-2 text-primary"></i>
                        Staff Dashboard
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="content">
        <div class="container-fluid">

            <!-- Stats Row -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><?= $totalResidents ?? 0 ?></h3>
                            <p>Total Residents</p>
                        </div>
                        <div class="icon"><i class="fas fa-users"></i></div>
                        <a href="<?= base_url('resident') ?>" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3><?= $totalHouseholds ?? 0 ?></h3>
                            <p>Total Households</p>
                        </div>
                        <div class="icon"><i class="fas fa-home"></i></div>
                        <a href="<?= base_url('households') ?>" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3><?= $pendingCerts ?? 0 ?></h3>
                            <p>Pending Certificates</p>
                        </div>
                        <div class="icon"><i class="fas fa-file-invoice"></i></div>
                        <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3><?= $blotterCount ?? 0 ?></h3>
                            <p>Blotter Records</p>
                        </div>
                        <div class="icon"><i class="fas fa-exclamation-triangle"></i></div>
                        <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            </div>

            <!-- Main Row -->
            <div class="row">
                <div class="col-md-8">
                    <div class="card card-default">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-bullhorn mr-1"></i> Recent Activity</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table m-0 table-striped">
                                    <thead>
                                        <tr><th>Type</th><th>Details</th><th>Date</th></tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($latestBlotters)): ?>
                                            <?php foreach ($latestBlotters as $blotter): ?>
                                                <tr>
                                                    <td><span class="badge badge-danger">Blotter</span></td>
                                                    <td><?= esc($blotter['complainant']) ?> vs <?= esc($blotter['respondent']) ?></td>
                                                    <td><small><?= date('M d, Y', strtotime($blotter['created_at'])) ?></small></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                        <?php if (!empty($latestCerts)): ?>
                                            <?php foreach ($latestCerts as $cert): ?>
                                                <tr>
                                                    <td><span class="badge badge-info">Certificate</span></td>
                                                    <td><?= esc($cert['type']) ?> — <?= esc($cert['resident_name']) ?></td>
                                                    <td><small><?= date('M d, Y', strtotime($cert['created_at'])) ?></small></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                        <?php if (empty($latestBlotters) && empty($latestCerts)): ?>
                                            <tr><td colspan="3" class="text-center text-muted">No recent activity found.</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer text-center">
                            <a href="#" class="text-uppercase text-sm">View All Activity</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-success card-outline">
                        <div class="card-header"><h3 class="card-title"><i class="fas fa-bolt mr-1"></i> Quick Actions</h3></div>
                        <div class="card-body">
                            <a href="<?= base_url('resident/create') ?>" class="btn btn-primary btn-block mb-2">
                                <i class="fas fa-user-plus mr-2"></i> Add New Resident
                            </a>
                            <a href="<?= base_url('resident') ?>" class="btn btn-info btn-block mb-2">
                                <i class="fas fa-users mr-2"></i> View All Residents
                            </a>
                            <a href="#" class="btn btn-success btn-block mb-2">
                                <i class="fas fa-file-alt mr-2"></i> Issue Certificate
                            </a>
                            <a href="#" class="btn btn-danger btn-block">
                                <i class="fas fa-gavel mr-2"></i> Record Blotter
                            </a>
                        </div>
                    </div>
                    <div class="card card-default">
                        <div class="card-header"><h3 class="card-title"><i class="fas fa-user-circle mr-1"></i> Logged In As</h3></div>
                        <div class="card-body text-center">
                            <i class="fas fa-user-tie fa-3x text-secondary mb-2"></i>
                            <h5><?= esc(session()->get('name') ?? 'Staff User') ?></h5>
                            <small class="text-muted"><?= esc(session()->get('role') ?? 'Staff') ?></small>
                            <hr>
                            <a href="<?= base_url('logout') ?>" class="btn btn-sm btn-outline-danger">
                                <i class="fas fa-sign-out-alt mr-1"></i> Logout
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- External JavaScript files -->
<script src="<?= base_url('assets/js/resident/residents-dashboard.js') ?>"></script>
<?= $this->endSection() ?>
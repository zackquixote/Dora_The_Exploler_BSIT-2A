<?php $this->extend('theme/template') ?>

<?= $this->section('content') ?>

<!-- Shared Dashboard CSS -->
<link rel="stylesheet" href="<?= base_url('assets/css/dashboard/style.css') ?>">
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

<div class="content-wrapper">
    <!-- Content Header -->
    <div class="content-header" style="margin-bottom: 20px;">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-7">
                    <h1 class="m-0 display-5 font-weight-bold" style="color: var(--text-main);">
                        Staff Dashboard
                    </h1>
                    <p class="text-muted font-weight-light mt-1">Welcome back, <?= esc(session()->get('name')) ?>. Here's what's happening today.</p>
                </div>
                <div class="col-sm-5">
                    <ol class="breadcrumb float-sm-right bg-transparent p-0 mt-2">
                        <li class="breadcrumb-item"><a href="#" class="text-muted"><i class="fas fa-home"></i> Home</a></li>
                        <li class="breadcrumb-item active font-weight-bold" style="color: var(--text-main);">Dashboard</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="content">
        <div class="container-fluid">

            <!-- ROW 1: Stats -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner"><h3><?= $totalResidents ?? 0 ?></h3><p>Total Residents</p></div>
                        <div class="icon"><i class="fas fa-users"></i></div>
                        <a href="<?= base_url('resident') ?>" class="small-box-footer">Manage <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner"><h3><?= $totalHouseholds ?? 0 ?></h3><p>Households</p></div>
                        <div class="icon"><i class="fas fa-home"></i></div>
                        <a href="<?= base_url('households') ?>" class="small-box-footer">View <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner"><h3><?= $pendingCerts ?? 0 ?></h3><p>Certificates</p></div>
                        <div class="icon"><i class="fas fa-file-invoice"></i></div>
                        <a href="#" class="small-box-footer"><?= $dailyCerts ?? 0 ?> today <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner"><h3><?= $blotterCount ?? 0 ?></h3><p>Blotter Cases</p></div>
                        <div class="icon"><i class="fas fa-exclamation-triangle"></i></div>
                        <a href="<?= base_url('blotter') ?>" class="small-box-footer">Records <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            </div>

            <!-- ROW 1b: Detailed Stats -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner"><h3><?= $openCases ?? 0 ?></h3><p>Open Cases</p></div>
                        <div class="icon"><i class="fas fa-folder-open"></i></div>
                        <a href="<?= base_url('blotter') ?>" class="small-box-footer">View <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner"><h3><?= $hearingsToday ?? 0 ?></h3><p>Hearings Today</p></div>
                        <div class="icon"><i class="fas fa-gavel"></i></div>
                        <a href="#" class="small-box-footer">Details <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner"><h3><?= $settledThisMonth ?? 0 ?></h3><p>Settled (Month)</p></div>
                        <div class="icon"><i class="fas fa-check-circle"></i></div>
                        <a href="#" class="small-box-footer">History <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner"><h3><?= $blotterCount ?? 0 ?></h3><p>Total Cases</p></div>
                        <div class="icon"><i class="fas fa-file-alt"></i></div>
                        <a href="<?= base_url('blotter') ?>" class="small-box-footer">View <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            </div>

            <!-- ROW 2: Info Boxes -->
            <div class="row">
                <div class="col-lg-3 col-md-6 col-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-primary elevation-1"><i class="fas fa-vote-yea"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Voters</span>
                            <span class="info-box-number"><?= $totalVoters ?? 0 ?></span>
                            <div class="progress"><div class="progress-bar bg-primary" style="width: <?= ($totalResidents ?? 0) > 0 ? round((($totalVoters ?? 0) / ($totalResidents ?? 1)) * 100) : 0 ?>%"></div></div>
                            <span class="progress-description"><?= ($totalResidents ?? 0) > 0 ? round((($totalVoters ?? 0) / ($totalResidents ?? 1)) * 100) : 0 ?>% of Pop.</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-indigo elevation-1"><i class="fas fa-wheelchair"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">PWDs</span>
                            <span class="info-box-number"><?= $totalPwd ?? 0 ?></span>
                            <div class="progress"><div class="progress-bar bg-indigo" style="width: <?= ($totalResidents ?? 0) > 0 ? round((($totalPwd ?? 0) / ($totalResidents ?? 1)) * 100) : 0 ?>%"></div></div>
                            <span class="progress-description"><?= ($totalResidents ?? 0) > 0 ? round((($totalPwd ?? 0) / ($totalResidents ?? 1)) * 100) : 0 ?>% of Pop.</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-teal elevation-1"><i class="fas fa-user-clock"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Seniors</span>
                            <span class="info-box-number"><?= $totalSenior ?? 0 ?></span>
                            <div class="progress"><div class="progress-bar bg-teal" style="width: <?= ($totalResidents ?? 0) > 0 ? round((($totalSenior ?? 0) / ($totalResidents ?? 1)) * 100) : 0 ?>%"></div></div>
                            <span class="progress-description"><?= ($totalResidents ?? 0) > 0 ? round((($totalSenior ?? 0) / ($totalResidents ?? 1)) * 100) : 0 ?>% of Pop.</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-orange elevation-1"><i class="fas fa-chart-line"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Avg/Household</span>
                            <span class="info-box-number"><?= $avgPerHousehold ?? 0 ?></span>
                            <div class="progress"><div class="progress-bar bg-orange" style="width: 60%"></div></div>
                            <span class="progress-description">Residents per HH</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ROW 3: Chart -->
            <div class="row">
                <div class="col-lg-5 col-md-6 col-12">
                    <div class="card">
                        <div class="card-header"><h3 class="card-title"><i class="fas fa-chart-bar mr-1"></i> Population by Purok</h3></div>
                        <div class="card-body"><canvas id="purokChart" style="height: 250px;"></canvas></div>
                    </div>
                </div>
            </div>

            <!-- ROW 4: Tables -->
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header"><h3 class="card-title">Upcoming Hearings</h3></div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead><tr><th>Date</th><th>Case No.</th><th>Venue</th></tr></thead>
                                    <tbody>
                                        <?php if (!empty($upcomingHearings)): ?>
                                            <?php foreach ($upcomingHearings as $h): ?>
                                                <tr>
                                                    <td><strong><?= date('M d', strtotime($h['hearing_date'])) ?></strong></td>
                                                    <td><?php $case = (new \App\Models\BlotterModel())->find($h['blotter_id']); echo esc($case['case_number'] ?? '--'); ?></td>
                                                    <td class="text-muted small"><?= esc($h['venue'] ?? 'Main Hall') ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr><td colspan="3" class="text-center text-muted py-3">No hearings scheduled</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header"><h3 class="card-title">Recent Cases</h3></div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead><tr><th>Case No.</th><th>Type</th><th>Status</th></tr></thead>
                                    <tbody>
                                        <?php if (!empty($recentCases)): ?>
                                            <?php foreach ($recentCases as $rc): ?>
                                                <tr>
                                                    <td><strong><?= esc($rc['case_number']) ?></strong></td>
                                                    <td><?= esc($rc['incident_type']) ?></td>
                                                    <td><span class="badge badge-<?= 
                                                        $status = $rc['status'];
                                                        $color = 'secondary';
                                                        if ($status == 'Pending') $color = 'warning';
                                                        elseif (in_array($status, ['Investigating','Ongoing'])) $color = 'info';
                                                        elseif ($status == 'For Hearing') $color = 'primary';
                                                        elseif ($status == 'Settled') $color = 'success';
                                                        elseif ($status == 'Dismissed') $color = 'dark';
                                                        elseif ($status == 'Referred') $color = 'purple';
                                                        elseif ($status == 'Unsettled') $color = 'danger';
                                                        echo $color;
                                                    ?>"><?= esc($status) ?></span></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr><td colspan="3" class="text-center text-muted py-3">No cases found</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ROW 5: Actions & User Profile -->
            <div class="row">
                <div class="col-md-7">
                    <div class="card h-100">
                        <div class="card-header"><h3 class="card-title">Quick Actions</h3></div>
                        <div class="card-body">
                            <div class="action-grid">
                                <a href="<?= base_url('resident/create') ?>" class="action-card ac-blue"><i class="fas fa-user-plus"></i><span>Add Resident</span></a>
                                <a href="<?= base_url('resident') ?>" class="action-card ac-cyan"><i class="fas fa-users"></i><span>View Residents</span></a>
                                <a href="<?= base_url('households') ?>" class="action-card ac-gray"><i class="fas fa-home"></i><span>Households</span></a>
                                <a href="<?= base_url('certificate/create') ?>" class="action-card ac-green"><i class="fas fa-file-alt"></i><span>Certificates</span></a>
                                <a href="<?= base_url('blotter/create') ?>" class="action-card ac-red"><i class="fas fa-gavel"></i><span>Blotter</span></a>
                                <a href="<?= base_url('certificate') ?>" class="action-card ac-teal"><i class="fas fa-file-contract"></i><span>Issuance Log</span></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="user-profile">
                        <div class="user-avatar-ring"><div class="user-avatar-inner"><i class="fas fa-user-shield"></i></div></div>
                        <h4 class="font-weight-bold mb-1"><?= esc(session()->get('name') ?? 'Staff') ?></h4>
                        <span class="user-badge mb-3 d-inline-block"><?= esc(session()->get('role') ?? 'Staff') ?></span>
                        <p class="small opacity-75">System Administrator</p>
                        <hr class="border-secondary">
                        <a href="<?= base_url('logout') ?>" class="btn btn-outline-light btn-sm px-4 rounded-pill mt-2 font-weight-bold">
                            <i class="fas fa-power-off mr-2"></i> Logout
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    const DASHBOARD_DATA = {
        baseUrl: "<?= base_url() ?>",
        csrfTokenName: "<?= csrf_token() ?>",
        csrfTokenValue: "<?= csrf_hash() ?>",
        purokLabels: <?= json_encode(array_column($purokCounts ?? [], 'sitio')) ?>,
        purokValues: <?= json_encode(array_column($purokCounts ?? [], 'count')) ?>
    };
</script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script src="<?= base_url('js/dashboard/admin.js') ?>"></script>

<?= $this->endSection() ?>
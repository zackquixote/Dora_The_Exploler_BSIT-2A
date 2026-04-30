<?php $this->extend('theme/admin/template') ?>

<?= $this->section('content') ?>

<!-- ================================================================
     Dashboard CSS – externalised for easy maintenance
     ================================================================ -->
<link rel="stylesheet" href="<?= base_url('assets/css/dashboard.css') ?>">

<div class="content-wrapper">
    <!-- Content Header (Page title & breadcrumb) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <i class="fas fa-tachometer-alt mr-2 text-danger"></i>
                        Admin Dashboard
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

            <!-- ================================================================
                 ROW 1 – Primary Statistic Cards
                 ================================================================ -->
            <div class="row">
                <!-- Total Residents -->
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
                <!-- Total Households -->
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
                <!-- Certificates (total + today) -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3><?= $pendingCerts ?? 0 ?></h3>
                            <p>Total Certificates</p>
                        </div>
                        <div class="icon"><i class="fas fa-file-invoice"></i></div>
                        <a href="#" class="small-box-footer">
                            <?= $dailyCerts ?? 0 ?> issued today <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                <!-- Blotter Records -->
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

            <!-- ================================================================
                 ROW 2 – Secondary Statistics (info boxes)
                 ================================================================ -->
            <div class="row">
                <!-- Registered Voters -->
                <div class="col-lg-3 col-md-6 col-12">
                    <div class="info-box shadow-sm">
                        <span class="info-box-icon bg-primary elevation-1"><i class="fas fa-vote-yea"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Registered Voters</span>
                            <span class="info-box-number"><?= $totalVoters ?? 0 ?></span>
                            <div class="progress">
                                <div class="progress-bar bg-primary" style="width: <?= ($totalResidents ?? 0) > 0 ? round((($totalVoters ?? 0) / ($totalResidents ?? 1)) * 100) : 0 ?>%"></div>
                            </div>
                            <span class="progress-description">
                                <?= ($totalResidents ?? 0) > 0 ? round((($totalVoters ?? 0) / ($totalResidents ?? 1)) * 100) : 0 ?>% of residents
                            </span>
                        </div>
                    </div>
                </div>
                <!-- PWD Residents -->
                <div class="col-lg-3 col-md-6 col-12">
                    <div class="info-box shadow-sm">
                        <span class="info-box-icon bg-indigo elevation-1"><i class="fas fa-wheelchair"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">PWD Residents</span>
                            <span class="info-box-number"><?= $totalPwd ?? 0 ?></span>
                            <div class="progress">
                                <div class="progress-bar bg-indigo" style="width: <?= ($totalResidents ?? 0) > 0 ? round((($totalPwd ?? 0) / ($totalResidents ?? 1)) * 100) : 0 ?>%"></div>
                            </div>
                            <span class="progress-description">
                                <?= ($totalResidents ?? 0) > 0 ? round((($totalPwd ?? 0) / ($totalResidents ?? 1)) * 100) : 0 ?>% of residents
                            </span>
                        </div>
                    </div>
                </div>
                <!-- Senior Citizens -->
                <div class="col-lg-3 col-md-6 col-12">
                    <div class="info-box shadow-sm">
                        <span class="info-box-icon bg-teal elevation-1"><i class="fas fa-user-clock"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Senior Citizens</span>
                            <span class="info-box-number"><?= $totalSenior ?? 0 ?></span>
                            <div class="progress">
                                <div class="progress-bar bg-teal" style="width: <?= ($totalResidents ?? 0) > 0 ? round((($totalSenior ?? 0) / ($totalResidents ?? 1)) * 100) : 0 ?>%"></div>
                            </div>
                            <span class="progress-description">
                                <?= ($totalResidents ?? 0) > 0 ? round((($totalSenior ?? 0) / ($totalResidents ?? 1)) * 100) : 0 ?>% of residents
                            </span>
                        </div>
                    </div>
                </div>
                <!-- Average Household Size -->
                <div class="col-lg-3 col-md-6 col-12">
                    <div class="info-box shadow-sm">
                        <span class="info-box-icon bg-orange elevation-1"><i class="fas fa-chart-line"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Avg. Household Size</span>
                            <span class="info-box-number"><?= $avgPerHousehold ?? 0 ?></span>
                            <div class="progress">
                                <div class="progress-bar bg-orange" style="width: 60%"></div>
                            </div>
                            <span class="progress-description">residents per household</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ================================================================
                 ROW 3 – Admin-specific stats & gender breakdown
                 ================================================================ -->
            <div class="row">
                <!-- Admin Accounts -->
                <div class="col-lg-3 col-md-6 col-12">
                    <div class="info-box shadow-sm">
                        <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-user-shield"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Admin Accounts</span>
                            <span class="info-box-number"><?= $totalAdmins ?? 0 ?></span>
                            <div class="progress">
                                <div class="progress-bar bg-danger" style="width: 100%"></div>
                            </div>
                            <span class="progress-description">system administrators</span>
                        </div>
                    </div>
                </div>
                <!-- Staff Accounts -->
                <div class="col-lg-3 col-md-6 col-12">
                    <div class="info-box shadow-sm">
                        <span class="info-box-icon bg-secondary elevation-1"><i class="fas fa-user-tie"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Staff Accounts</span>
                            <span class="info-box-number"><?= $totalStaff ?? 0 ?></span>
                            <div class="progress">
                                <div class="progress-bar bg-secondary" style="width: 100%"></div>
                            </div>
                            <span class="progress-description">active staff users</span>
                        </div>
                    </div>
                </div>
                <!-- Male Residents -->
                <div class="col-lg-3 col-md-6 col-12">
                    <div class="info-box shadow-sm">
                        <span class="info-box-icon bg-info elevation-1"><i class="fas fa-male"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Male Residents</span>
                            <span class="info-box-number"><?= $totalMale ?? 0 ?></span>
                            <div class="progress">
                                <div class="progress-bar bg-info" style="width: <?= ($totalResidents ?? 0) > 0 ? round((($totalMale ?? 0) / ($totalResidents ?? 1)) * 100) : 0 ?>%"></div>
                            </div>
                            <span class="progress-description">
                                <?= ($totalResidents ?? 0) > 0 ? round((($totalMale ?? 0) / ($totalResidents ?? 1)) * 100) : 0 ?>% of residents
                            </span>
                        </div>
                    </div>
                </div>
                <!-- Female Residents -->
                <div class="col-lg-3 col-md-6 col-12">
                    <div class="info-box shadow-sm">
                        <span class="info-box-icon elevation-1" style="background:#e91e8c;"><i class="fas fa-female"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Female Residents</span>
                            <span class="info-box-number"><?= $totalFemale ?? 0 ?></span>
                            <div class="progress">
                                <div class="progress-bar" style="background:#e91e8c; width: <?= ($totalResidents ?? 0) > 0 ? round((($totalFemale ?? 0) / ($totalResidents ?? 1)) * 100) : 0 ?>%"></div>
                            </div>
                            <span class="progress-description">
                                <?= ($totalResidents ?? 0) > 0 ? round((($totalFemale ?? 0) / ($totalResidents ?? 1)) * 100) : 0 ?>% of residents
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ================================================================
                 ROW 4 – Charts (Gender, Purok, Civil Status)
                 ================================================================ -->
            <div class="row">
                <!-- Gender Pie / Doughnut -->
                <div class="col-lg-4 col-md-6 col-12">
                    <div class="card card-default shadow-sm">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-venus-mars mr-1" style="color:#e91e8c;"></i> Gender Distribution</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                            </div>
                        </div>
                        <div class="card-body text-center">
                            <canvas id="genderChart" style="max-height:200px;"></canvas>
                            <div class="mt-3 d-flex justify-content-center">
                                <span class="mr-3"><i class="fas fa-circle text-primary"></i> Male: <strong><?= $totalMale ?? 0 ?></strong></span>
                                <span><i class="fas fa-circle" style="color:#e91e8c;"></i> Female: <strong><?= $totalFemale ?? 0 ?></strong></span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Residents per Purok (bar chart) -->
                <div class="col-lg-5 col-md-6 col-12">
                    <div class="card card-default shadow-sm">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-map-marker-alt mr-1 text-danger"></i> Residents per Purok</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                            </div>
                        </div>
                        <div class="card-body">
                            <canvas id="purokChart" style="min-height:200px; max-height:220px;"></canvas>
                        </div>
                    </div>
                </div>
                <!-- Civil Status (pie chart) -->
                <div class="col-lg-3 col-md-6 col-12">
                    <div class="card card-default shadow-sm">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-heart mr-1 text-danger"></i> Civil Status</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                            </div>
                        </div>
                        <div class="card-body text-center">
                            <canvas id="civilStatusChart" style="max-height:200px;"></canvas>
                        </div>
                        <div class="card-footer p-2">
                            <div class="row text-center" style="font-size:0.75rem;">
                                <?php if (!empty($civilStatusData)): ?>
                                    <?php foreach ($civilStatusData as $cs): ?>
                                        <div class="col-6 mb-1">
                                            <strong><?= esc($cs['civil_status'] ?? 'N/A') ?></strong><br>
                                            <span class="text-muted"><?= $cs['count'] ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ================================================================
                 ROW 5 – Recent Activity & Quick Actions
                 ================================================================ -->
            <div class="row">
                <!-- Recent System Activity Panel -->
                <div class="col-md-8">
                    <div class="rv-activity-panel">
                        <div class="rv-activity-header">
                            <div class="rv-activity-title">
                                <i class="fas fa-history"></i>
                                Recent System Activity
                            </div>
                            <a href="<?= base_url('logs') ?>" style="font-size:0.75rem; color:var(--rv-primary); text-decoration:none; font-weight:600;">View All</a>
                        </div>

                        <div class="rv-activity-feed">
                            <?php if (empty($recentLogs)): ?>
                                <div class="rv-activity-empty">
                                    <i class="fas fa-history"></i>
                                    <p>No recent activity found.</p>
                                </div>
                            <?php else: ?>
                                <?php foreach ($recentLogs as $log): ?>
                                    <?php 
                                    // Determine icon based on action type
                                    $iconClass = 'view';
                                    $iconIcon  = 'fa-eye';
                                    $actionStr = strtolower($log['ACTION'] ?? '');
                                    if (strpos($actionStr, 'delete') !== false || strpos($actionStr, 'remove') !== false) {
                                        $iconClass = 'cert'; $iconIcon = 'fa-trash-alt';
                                    } elseif (strpos($actionStr, 'edit') !== false || strpos($actionStr, 'update') !== false) {
                                        $iconClass = 'edit'; $iconIcon = 'fa-edit';
                                    } elseif (strpos($actionStr, 'print') !== false) {
                                        $iconClass = 'print'; $iconIcon = 'fa-print';
                                    } elseif (strpos($actionStr, 'create') !== false || strpos($actionStr, 'add') !== false) {
                                        $iconClass = 'success'; $iconIcon = 'fa-plus-circle';
                                    }
                                    ?>
                                    <div class="rv-activity-item">
                                        <div class="rv-activity-icon <?= $iconClass ?>">
                                            <i class="fas <?= $iconIcon ?>"></i>
                                        </div>
                                        <div class="rv-activity-content">
                                            <div class="rv-activity-action"><?= esc($log['ACTION']) ?></div>
                                            <div class="rv-activity-user">
                                                by <strong><?= esc($log['USER_NAME'] ?? 'System') ?></strong>
                                            </div>
                                            <div class="rv-activity-time">
                                                <i class="fas fa-clock" style="font-size:0.65rem; margin-right:3px; opacity:0.6"></i>
                                                <?= time_elapsed_string($log['DATELOG'] . ' ' . $log['TIMELOG']) ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions & User Info -->
                <div class="col-md-4">
                    <div class="card card-danger card-outline shadow-sm">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-bolt mr-1"></i> Quick Actions</h3>
                        </div>
                        <div class="card-body">
                            <a href="<?= base_url('resident/create') ?>" class="btn btn-primary btn-block mb-2">
                                <i class="fas fa-user-plus mr-2"></i> Add New Resident
                            </a>
                            <a href="<?= base_url('resident') ?>" class="btn btn-info btn-block mb-2">
                                <i class="fas fa-users mr-2"></i> View All Residents
                            </a>
                            <a href="<?= base_url('households') ?>" class="btn btn-secondary btn-block mb-2">
                                <i class="fas fa-home mr-2"></i> View Households
                            </a>
                            <a href="<?= base_url('certificate/create') ?>" class="btn btn-success btn-block mb-2">
                                <i class="fas fa-file-alt mr-2"></i> Issue Certificate
                            </a>
                          <a href="<?= base_url('admin/users/create') ?>" class="btn btn-warning btn-block mb-2">
    <i class="fas fa-users-cog mr-2"></i> Manage Users
</a>
                            <a href="<?= base_url('blotter/create') ?>" class="btn btn-danger btn-block">
                                <i class="fas fa-gavel mr-2"></i> Record Blotter
                            </a>
                        </div>
                    </div>
                    <div class="card card-default shadow-sm">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-user-circle mr-1"></i> Logged In As</h3>
                        </div>
                        <div class="card-body text-center">
                            <i class="fas fa-user-shield fa-3x text-danger mb-2"></i>
                            <h5><?= esc(session()->get('name') ?? 'Admin User') ?></h5>
                            <small class="text-muted badge badge-danger"><?= esc(session()->get('role') ?? 'Admin') ?></small>
                            <hr>
                            <a href="<?= base_url('logout') ?>" class="btn btn-sm btn-outline-danger">
                                <i class="fas fa-sign-out-alt mr-1"></i> Logout
                            </a>
                        </div>
                    </div>
                </div>
            </div> <!-- end row 5 -->

        </div><!-- /.container-fluid -->
    </div><!-- /.content -->
</div><!-- /.content-wrapper -->

<!-- ================================================================
     Pass server-side data to JavaScript as a clean JSON object
     ================================================================ -->
<script>
    // All data needed for dashboard charts and interactions
    const DASHBOARD_DATA = {
        baseUrl: "<?= base_url() ?>",
        csrfTokenName: "<?= csrf_token() ?>",
        csrfTokenValue: "<?= csrf_hash() ?>",
        gender: {
            male: <?= (int)($totalMale ?? 0) ?>,
            female: <?= (int)($totalFemale ?? 0) ?>
        },
        purokLabels: <?= json_encode(array_column($purokCounts ?? [], 'sitio')) ?>,
        purokValues: <?= json_encode(array_column($purokCounts ?? [], 'count')) ?>,
        civilLabels: <?= json_encode(array_column($civilStatusData ?? [], 'civil_status')) ?>,
        civilValues: <?= json_encode(array_column($civilStatusData ?? [], 'count')) ?>
    };
</script>

<!-- ================================================================
     External JavaScript – handles all chart rendering and interactions
     ================================================================ -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script src="<?= base_url('js/dashboard/admin.js') ?>"></script>

<?= $this->endSection() ?>
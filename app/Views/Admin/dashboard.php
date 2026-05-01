<?php $this->extend('theme/admin/template') ?>

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
                        Admin Dashboard
                    </h1>
                    <p class="text-muted font-weight-light mt-1">System Overview & Administration</p>
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

            <!-- ROW 3: Admin Specific -->
            <div class="row">
                <div class="col-lg-3 col-md-6 col-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-user-shield"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Admins</span>
                            <span class="info-box-number"><?= $totalAdmins ?? 0 ?></span>
                            <div class="progress"><div class="progress-bar bg-danger" style="width: 100%"></div></div>
                            <span class="progress-description">System Administrators</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-secondary elevation-1"><i class="fas fa-user-tie"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Staff</span>
                            <span class="info-box-number"><?= $totalStaff ?? 0 ?></span>
                            <div class="progress"><div class="progress-bar bg-secondary" style="width: 100%"></div></div>
                            <span class="progress-description">Active Staff Users</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-info elevation-1"><i class="fas fa-male"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Males</span>
                            <span class="info-box-number"><?= $totalMale ?? 0 ?></span>
                            <div class="progress"><div class="progress-bar bg-info" style="width: <?= ($totalResidents ?? 0) > 0 ? round((($totalMale ?? 0) / ($totalResidents ?? 1)) * 100) : 0 ?>%"></div></div>
                            <span class="progress-description"><?= ($totalResidents ?? 0) > 0 ? round((($totalMale ?? 0) / ($totalResidents ?? 1)) * 100) : 0 ?>% of Pop.</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-12">
                    <div class="info-box">
                        <span class="info-box-icon elevation-1" style="background:#e91e8c;"><i class="fas fa-female"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Females</span>
                            <span class="info-box-number"><?= $totalFemale ?? 0 ?></span>
                            <div class="progress"><div class="progress-bar" style="background:#e91e8c; width: <?= ($totalResidents ?? 0) > 0 ? round((($totalFemale ?? 0) / ($totalResidents ?? 1)) * 100) : 0 ?>%"></div></div>
                            <span class="progress-description"><?= ($totalResidents ?? 0) > 0 ? round((($totalFemale ?? 0) / ($totalResidents ?? 1)) * 100) : 0 ?>% of Pop.</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ROW 4: Charts -->
            <div class="row">
                <div class="col-lg-4 col-md-6 col-12">
                    <div class="card">
                        <div class="card-header"><h3 class="card-title"><i class="fas fa-venus-mars mr-1" style="color:#e91e8c;"></i> Gender Distribution</h3></div>
                        <div class="card-body text-center">
                            <canvas id="genderChart" style="max-height:200px;"></canvas>
                            <div class="mt-3 d-flex justify-content-center">
                                <span class="mr-3"><i class="fas fa-circle text-primary"></i> Male: <strong><?= $totalMale ?? 0 ?></strong></span>
                                <span><i class="fas fa-circle" style="color:#e91e8c;"></i> Female: <strong><?= $totalFemale ?? 0 ?></strong></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5 col-md-6 col-12">
                    <div class="card">
                        <div class="card-header"><h3 class="card-title"><i class="fas fa-chart-bar mr-1"></i> Population by Purok</h3></div>
                        <div class="card-body"><canvas id="purokChart" style="height: 250px;"></canvas></div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-12">
                    <div class="card">
                        <div class="card-header"><h3 class="card-title"><i class="fas fa-heart mr-1 text-danger"></i> Civil Status</h3></div>
                        <div class="card-body text-center"><canvas id="civilStatusChart" style="max-height:200px;"></canvas></div>
                        <div class="card-footer p-2">
                            <div class="row text-center" style="font-size:0.75rem;">
                                <?php if (!empty($civilStatusData)): ?>
                                    <?php foreach ($civilStatusData as $cs): ?>
                                        <div class="col-6 mb-1"><strong><?= esc($cs['civil_status'] ?? 'N/A') ?></strong><br><span class="text-muted"><?= $cs['count'] ?></span></div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ROW 4b: Tables -->
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

            <!-- ROW 5: Activity & User -->
            <div class="row">
                <div class="col-md-8">
                    <div class="rv-activity-panel">
                        <div class="rv-activity-header">
                            <div class="rv-activity-title"><i class="fas fa-history"></i> Recent System Activity</div>
                            <a href="<?= base_url('logs') ?>" style="font-size:0.75rem; color:var(--text-main); text-decoration:none; font-weight:600;">View All</a>
                        </div>
                        <div class="rv-activity-feed">
                            <?php if (empty($recentLogs)): ?>
                                <div class="text-center p-4 text-muted"><i class="fas fa-history fa-2x mb-2 opacity-25"></i><p>No recent activity found.</p></div>
                            <?php else: ?>
                                <?php foreach ($recentLogs as $log): ?>
                                    <?php 
                                    $iconClass = 'view'; $iconIcon = 'fa-eye';
                                    $actionStr = strtolower($log['ACTION'] ?? '');
                                    if (strpos($actionStr, 'delete') !== false || strpos($actionStr, 'remove') !== false) { $iconClass = 'cert'; $iconIcon = 'fa-trash-alt'; }
                                    elseif (strpos($actionStr, 'edit') !== false || strpos($actionStr, 'update') !== false) { $iconClass = 'edit'; $iconIcon = 'fa-edit'; }
                                    elseif (strpos($actionStr, 'print') !== false) { $iconClass = 'print'; $iconIcon = 'fa-print'; }
                                    elseif (strpos($actionStr, 'create') !== false || strpos($actionStr, 'add') !== false) { $iconClass = 'success'; $iconIcon = 'fa-plus-circle'; }
                                    ?>
                                    <div class="rv-activity-item">
                                        <div class="rv-activity-icon <?= $iconClass ?>"><i class="fas <?= $iconIcon ?>"></i></div>
                                        <div class="rv-activity-content">
                                            <div class="rv-activity-action"><?= esc($log['ACTION']) ?></div>
                                            <div class="rv-activity-user">by <strong><?= esc($log['USER_NAME'] ?? 'System') ?></strong></div>
                                            <div class="rv-activity-time"><i class="fas fa-clock" style="font-size:0.65rem; margin-right:3px; opacity:0.6"></i> <?= time_elapsed_string($log['DATELOG'] . ' ' . $log['TIMELOG']) ?></div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-header"><h3 class="card-title">Quick Actions</h3></div>
                        <div class="card-body">
                            <div class="action-grid">
                                <a href="<?= base_url('resident/create') ?>" class="action-card ac-blue"><i class="fas fa-user-plus"></i><span>Add Resident</span></a>
                                <a href="<?= base_url('resident') ?>" class="action-card ac-cyan"><i class="fas fa-users"></i><span>View Residents</span></a>
                                <a href="<?= base_url('households') ?>" class="action-card ac-gray"><i class="fas fa-home"></i><span>Households</span></a>
                                <a href="<?= base_url('certificate/create') ?>" class="action-card ac-green"><i class="fas fa-file-alt"></i><span>Certificates</span></a>
                                <a href="<?= base_url('admin/users/create') ?>" class="action-card ac-yellow"><i class="fas fa-users-cog"></i><span>Manage Users</span></a>
                                <a href="<?= base_url('blotter/create') ?>" class="action-card ac-red"><i class="fas fa-gavel"></i><span>Blotter</span></a>
                            </div>
                        </div>
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
        gender: { male: <?= (int)($totalMale ?? 0) ?>, female: <?= (int)($totalFemale ?? 0) ?> },
        purokLabels: <?= json_encode(array_column($purokCounts ?? [], 'sitio')) ?>,
        purokValues: <?= json_encode(array_column($purokCounts ?? [], 'count')) ?>,
        civilLabels: <?= json_encode(array_column($civilStatusData ?? [], 'civil_status')) ?>,
        civilValues: <?= json_encode(array_column($civilStatusData ?? [], 'count')) ?>
    };
</script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script src="<?= base_url('js/dashboard/admin.js') ?>"></script>

<?= $this->endSection() ?>
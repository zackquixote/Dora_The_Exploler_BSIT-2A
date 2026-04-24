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

            <!-- ===================== ROW 1: Primary Stats ===================== -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3 id="totalResidents"><?= $totalResidents ?? 0 ?></h3>
                            <p>Total Residents</p>
                        </div>
                        <div class="icon"><i class="fas fa-users"></i></div>
                        <a href="<?= base_url('resident') ?>" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3 id="totalHouseholds"><?= $totalHouseholds ?? 0 ?></h3>
                            <p>Total Households</p>
                        </div>
                        <div class="icon"><i class="fas fa-home"></i></div>
                        <a href="<?= base_url('households') ?>" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3 id="pendingCerts"><?= $pendingCerts ?? 0 ?></h3>
                            <p>Pending Certificates</p>
                        </div>
                        <div class="icon"><i class="fas fa-file-invoice"></i></div>
                        <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3 id="blotterCount"><?= $blotterCount ?? 0 ?></h3>
                            <p>Blotter Records</p>
                        </div>
                        <div class="icon"><i class="fas fa-exclamation-triangle"></i></div>
                        <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            </div>

            <!-- ===================== ROW 2: Secondary Stats ===================== -->
            <div class="row">
                <!-- Registered Voters -->
                <div class="col-lg-3 col-md-6 col-12">
                    <div class="info-box shadow-sm">
                        <span class="info-box-icon bg-primary elevation-1">
                            <i class="fas fa-vote-yea"></i>
                        </span>
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

                <!-- PWD -->
                <div class="col-lg-3 col-md-6 col-12">
                    <div class="info-box shadow-sm">
                        <span class="info-box-icon bg-indigo elevation-1">
                            <i class="fas fa-wheelchair"></i>
                        </span>
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
                        <span class="info-box-icon bg-teal elevation-1">
                            <i class="fas fa-user-clock"></i>
                        </span>
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

                <!-- Avg Household Size -->
                <div class="col-lg-3 col-md-6 col-12">
                    <div class="info-box shadow-sm">
                        <span class="info-box-icon bg-orange elevation-1">
                            <i class="fas fa-chart-line"></i>
                        </span>
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

            <!-- ===================== ROW 3: Charts + Activity ===================== -->
            <div class="row">

                <!-- Gender Distribution Doughnut Chart -->
                <div class="col-lg-4 col-md-6 col-12">
                    <div class="card card-default shadow-sm">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-venus-mars mr-1 text-pink"></i> Gender Distribution</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                            </div>
                        </div>
                        <div class="card-body text-center">
                            <canvas id="genderChart" style="max-height:200px;"></canvas>
                            <div class="mt-3 d-flex justify-content-center">
                                <span class="mr-3"><i class="fas fa-circle text-primary"></i> Male: <strong><?= $totalMale ?? 0 ?></strong></span>
                                <span><i class="fas fa-circle text-pink"></i> Female: <strong><?= $totalFemale ?? 0 ?></strong></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Residents per Purok Bar Chart -->
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

                <!-- Civil Status Pie Chart -->
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

            <!-- ===================== ROW 4: Activity + Quick Actions ===================== -->
            <div class="row">
                <div class="col-md-8">
                    <div class="card card-default shadow-sm">
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
                    <!-- Quick Actions -->
                    <div class="card card-success card-outline shadow-sm">
                        <div class="card-header"><h3 class="card-title"><i class="fas fa-bolt mr-1"></i> Quick Actions</h3></div>
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
                            <a href="#" class="btn btn-success btn-block mb-2">
                                <i class="fas fa-file-alt mr-2"></i> Issue Certificate
                            </a>
                            <a href="#" class="btn btn-danger btn-block">
                                <i class="fas fa-gavel mr-2"></i> Record Blotter
                            </a>
                        </div>
                    </div>

                    <!-- Logged In As -->
                    <div class="card card-default shadow-sm">
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

        </div><!-- /.container-fluid -->
    </div><!-- /.content -->
</div><!-- /.content-wrapper -->

<!--
    JS variables declared BEFORE external scripts.
    Pass PHP data to JavaScript for chart rendering.
-->
<script>
    var BASE_URL         = "<?= base_url() ?>";
    var CSRF_TOKEN_NAME  = "<?= csrf_token() ?>";
    var CSRF_TOKEN_VALUE = "<?= csrf_hash() ?>";

    // Gender data
    var GENDER_DATA = {
        male:   <?= (int)($totalMale   ?? 0) ?>,
        female: <?= (int)($totalFemale ?? 0) ?>
    };

    // Purok data — built from $purokCounts passed by the controller
    var PUROK_LABELS = <?= json_encode(array_column($purokCounts ?? [], 'sitio'))   ?>;
    var PUROK_VALUES = <?= json_encode(array_column($purokCounts ?? [], 'count'))   ?>;

    // Civil status data
    var CIVIL_LABELS = <?= json_encode(array_column($civilStatusData ?? [], 'civil_status')) ?>;
    var CIVIL_VALUES = <?= json_encode(array_column($civilStatusData ?? [], 'count'))        ?>;
</script>
<script src="<?= base_url('js/residents/residents-dashboard.js') ?>"></script>

<!--
    Chart.js — loaded from CDN (already bundled in AdminLTE; remove if duplicate)
    If Chart.js is already included in your AdminLTE layout, delete the next line.
-->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

<script>
(function () {
    // ── Colour palette ──────────────────────────────────────────────────
    var COLORS = ['#3c8dbc','#e91e8c','#00a65a','#f39c12','#605ca8','#00c0ef','#d81b60','#ff7701'];

    // ── Gender Doughnut ─────────────────────────────────────────────────
    var gCtx = document.getElementById('genderChart');
    if (gCtx) {
        new Chart(gCtx, {
            type: 'doughnut',
            data: {
                labels: ['Male', 'Female'],
                datasets: [{
                    data: [GENDER_DATA.male, GENDER_DATA.female],
                    backgroundColor: ['#3c8dbc', '#e91e8c'],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { display: false }
                }
            }
        });
    }

    // ── Residents per Purok Bar ─────────────────────────────────────────
    var pCtx = document.getElementById('purokChart');
    if (pCtx) {
        new Chart(pCtx, {
            type: 'bar',
            data: {
                labels: PUROK_LABELS.length ? PUROK_LABELS : ['No Data'],
                datasets: [{
                    label: 'Residents',
                    data: PUROK_VALUES.length ? PUROK_VALUES : [0],
                    backgroundColor: COLORS.slice(0, PUROK_LABELS.length || 1),
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0 }
                    },
                    x: {
                        ticks: {
                            maxRotation: 30,
                            minRotation: 0
                        }
                    }
                }
            }
        });
    }

    // ── Civil Status Pie ────────────────────────────────────────────────
    var cCtx = document.getElementById('civilStatusChart');
    if (cCtx) {
        new Chart(cCtx, {
            type: 'pie',
            data: {
                labels: CIVIL_LABELS.length ? CIVIL_LABELS : ['No Data'],
                datasets: [{
                    data: CIVIL_VALUES.length ? CIVIL_VALUES : [1],
                    backgroundColor: COLORS,
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { display: false }
                }
            }
        });
    }
})();
</script>

<?= $this->endSection() ?>
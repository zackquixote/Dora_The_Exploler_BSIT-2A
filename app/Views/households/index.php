<?= $this->extend('theme/template') ?>
<?= $this->section('content') ?>

<div class="content-wrapper bg-light">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Household Management</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('staff/dashboard') ?>">Home</a></li>
                        <li class="breadcrumb-item active">Households</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            
            <!-- FLASH MESSAGES -->
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show shadow-sm">
                    <i class="fas fa-check-circle mr-2"></i> <?= session()->getFlashdata('success') ?>
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            <?php endif; ?>

            <!-- TOOLBAR & STATS -->
            <div class="row">
                <!-- Search & Filter -->
                <div class="col-md-12 mb-3">
                    <div class="card border-0 shadow-sm rounded-lg">
                        <div class="card-body p-3 d-flex flex-wrap gap-2 align-items-center justify-content-between">
                            
                            <div class="d-flex align-items-center" style="flex: 1;">
                                <div class="input-group input-group-sm" style="max-width: 300px;">
                                    <div class="input-group-prepend bg-light border-right-0">
                                        <span class="input-group-text bg-transparent border-right-0"><i class="fas fa-search text-muted"></i></span>
                                    </div>
                                    <input type="text" id="hhSearch" class="form-control border-left-0 bg-light" placeholder="Search HH #, Head name...">
                                </div>
                                
                                <select class="form-control form-control-sm ml-3" style="width: auto;" id="purokFilter" onchange="location.href='<?= base_url('households') ?>?purok='+encodeURIComponent(this.value)">
                                    <option value="all" <?= ($selectedPurok ?? 'all') == 'all' ? 'selected' : '' ?>>All Puroks</option>
                                    <option value="Purok Malipayon" <?= ($selectedPurok ?? '') == 'Purok Malipayon' ? 'selected' : '' ?>>Purok Malipayon</option>
                                    <option value="Purok Masagana"  <?= ($selectedPurok ?? '') == 'Purok Masagana'  ? 'selected' : '' ?>>Purok Masagana</option>
                                    <option value="Purok Cory"      <?= ($selectedPurok ?? '') == 'Purok Cory'      ? 'selected' : '' ?>>Purok Cory</option>
                                </select>
                            </div>

                            <a href="<?= base_url('households/create') ?>" class="btn btn-primary btn-sm shadow-sm">
                                <i class="fas fa-plus mr-1"></i> New Household
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="col-md-4">
                    <div class="card card-primary card-outline shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="text-primary mb-1"><?= $totalHouseholds ?? 0 ?></h5>
                                    <span class="text-muted text-uppercase font-weight-bold text-xs">Total Households</span>
                                </div>
                                <i class="fas fa-home fa-2x text-primary opacity-20"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-success card-outline shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="text-success mb-1"><?= $totalResidents ?? 0 ?></h5>
                                    <span class="text-muted text-uppercase font-weight-bold text-xs">Total Residents</span>
                                </div>
                                <i class="fas fa-users fa-2x text-success opacity-20"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-warning card-outline shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="text-warning mb-1"><?= $avgPerHousehold ?? 0 ?></h5>
                                    <span class="text-muted text-uppercase font-weight-bold text-xs">Avg. Members</span>
                                </div>
                                <i class="fas fa-chart-pie fa-2x text-warning opacity-20"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TABLE -->
            <div class="card border-0 shadow-sm rounded-lg">
                <div class="card-header bg-white border-0 pt-3">
                    <h3 class="card-title text-dark font-weight-bold">Household Directory</h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-align-middle mb-0" id="hhTable">
                            <thead class="bg-light">
                                <tr>
                                    <th class="pl-4">Household No</th>
                                    <th>Head of Family</th>
                                    <th>Sitio</th>
                                    <th>Address</th>
                                    <th>Members</th>
                                    <th class="text-right pr-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($households)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-5">
                                            <i class="fas fa-home fa-3x text-muted mb-3 opacity-50"></i>
                                            <p class="text-muted mb-0">No households found.</p>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($households as $h): 
                                        // Determine occupancy badge
                                        $count = $h['resident_count'];
                                        $badgeClass = 'bg-secondary';
                                        $badgeText = 'Low';
                                        
                                        if ($count >= 4 && $count < 7) { $badgeClass = 'bg-info'; $badgeText = 'Medium'; }
                                        if ($count >= 7) { $badgeClass = 'bg-warning'; $badgeText = 'High'; }
                                        if ($count >= 10) { $badgeClass = 'bg-danger'; $badgeText = 'Crowded'; }
                                        
                                        // Generate Initials Avatar
                                        $initials = strtoupper(substr($h['head_name'], 0, 1));
                                    ?>
                                    <tr>
                                        <td class="pl-4">
                                            <a href="<?= base_url('households/view/'.$h['id']) ?>" class="text-primary font-weight-bold text-decoration-none">
                                                <?= esc($h['household_no']) ?>
                                            </a>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="https://ui-avatars.com/api/?name=<?= urlencode($h['head_name']) ?>&background=random&color=fff&size=32" 
                                                     class="rounded-circle mr-2" alt="">
                                                <span class="font-weight-bold text-dark"><?= esc($h['head_name']) ?></span>
                                            </div>
                                        </td>
                                        <td><span class="badge badge-light border text-muted"><?= esc($h['sitio']) ?></span></td>
                                        <td class="text-muted text-truncate" style="max-width: 200px;" title="<?= esc($h['address'] ?: $h['street_address']) ?>">
                                            <?= esc($h['address'] ?: $h['street_address']) ?>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="badge <?= $badgeClass ?> mr-2"><?= $count ?></span>
                                                <small class="text-muted"><?= $badgeText ?></small>
                                            </div>
                                        </td>
                                        <td class="text-right pr-4">
                                            <div class="btn-group btn-group-sm">
                                                <a href="<?= base_url('households/view/'.$h['id']) ?>" class="btn btn-info" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="<?= base_url('households/edit/'.$h['id']) ?>" class="btn btn-warning" title="Edit">
                                                    <i class="fas fa-pen"></i>
                                                </a>
                                                <button class="btn btn-danger delete-household" data-id="<?= $h['id'] ?>" data-no="<?= esc($h['household_no']) ?>" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>

<!-- CSS Enhancements -->
<style>
    .card { border-radius: 0.75rem; }
    .card-outline { border-top: 3px solid; }
    .text-xs { font-size: 0.75rem; letter-spacing: 0.5px; }
    .table td { vertical-align: middle; border-bottom: 1px solid #f0f0f0; }
    .table tr:hover { background-color: #f9fafb; }
    .badge-light { background-color: #f8f9fa; color: #495057; font-weight: 500; }
    .opacity-20 { opacity: 0.2; }
</style>

<!-- Load external JavaScript -->
<script src="<?= base_url('js/households/households-index.js') ?>"></script>
<script>
// Initialize the household index module
if (typeof HouseholdIndex !== 'undefined') {
    HouseholdIndex.init({
        baseUrl: '<?= base_url() ?>',
        csrfToken: '<?= csrf_token() ?>',
        csrfHash: '<?= csrf_hash() ?>'
    });
}
</script>

<?= $this->endSection() ?>
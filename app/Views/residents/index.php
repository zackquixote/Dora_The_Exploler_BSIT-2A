<?php
// ---------------------------------------------------------
// SMART THEME LOADER
// ---------------------------------------------------------
 $role = strtolower(session()->get('role') ?? 'staff');
 $template = ($role == 'admin') ? 'theme/admin/template' : 'theme/template';
?>

<?= $this->extend($template) ?>

<?= $this->section('content') ?>

<div class="content-wrapper residents-page">
    <div class="content-header">
        <div class="container-fluid">
            <div class="rp-page-header">
                <h1 class="rp-page-title">
                    <i class="fas fa-users"></i> Residents Management
                </h1>
                <nav class="rp-breadcrumb">
                    <a href="<?= base_url(strtolower(session()->get('role') . '/dashboard')) ?>">Home</a>
                    <i class="fas fa-chevron-right"></i>
                    <span>Residents</span>
                </nav>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">

            <!-- Flash Messages -->
            <?php if (session()->getFlashdata('success')): ?>
                <div class="rp-alert rp-alert-success alert-dismissible fade show" role="alert">
                    <div>
                        <i class="fas fa-check-circle mr-1"></i> 
                        <span><?= session()->getFlashdata('success') ?></span>
                    </div>
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="rp-alert rp-alert-danger alert-dismissible fade show" role="alert">
                    <div>
                        <i class="fas fa-exclamation-circle mr-1"></i> 
                        <span><?= session()->getFlashdata('error') ?></span>
                    </div>
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            <?php endif; ?>

            <!-- Main Table Card -->
            <div class="rp-card">
                <div class="rp-card-header">
                    <h3 class="rp-card-title">
                        List of Residents
                        <?php if ($selectedPurok !== 'all'): ?>
                            <span class="rp-filter-badge">
                                <i class="fas fa-filter"></i>
                                <?= esc($selectedPurok) ?>
                            </span>
                        <?php endif; ?>
                    </h3>

                    <div class="rp-toolbar">
                        <!-- Purok Filter -->
                        <form method="GET" action="<?= base_url('resident') ?>" id="purokFilterForm">
                            <select name="purok" id="purokFilter" class="rp-select" onchange="this.form.submit()">
                                <option value="all"             <?= ($selectedPurok ?? 'all') == 'all'            ? 'selected' : '' ?>>All Puroks</option>
                                <option value="Purok Malipayon" <?= ($selectedPurok ?? '') == 'Purok Malipayon'   ? 'selected' : '' ?>>Purok Malipayon</option>
                                <option value="Purok Masagana"  <?= ($selectedPurok ?? '') == 'Purok Masagana'    ? 'selected' : '' ?>>Purok Masagana</option>
                                <option value="Purok Cory"      <?= ($selectedPurok ?? '') == 'Purok Cory'        ? 'selected' : '' ?>>Purok Cory</option>
                                <option value="Purok Kawayan"   <?= ($selectedPurok ?? '') == 'Purok Kawayan'     ? 'selected' : '' ?>>Purok Kawayan</option>
                                <option value="Purok Pagla-um"  <?= ($selectedPurok ?? '') == 'Purok Pagla-um'    ? 'selected' : '' ?>>Purok Pagla-um</option>
                                <option value="Unassigned"      <?= ($selectedPurok ?? '') == 'Unassigned'        ? 'selected' : '' ?>>Unassigned</option>
                            </select>
                            <?php if (($selectedPurok ?? 'all') != 'all'): ?>
                                <a href="<?= base_url('resident') ?>" class="rp-btn rp-btn-ghost" id="clearFilterBtn">
                                    <i class="fas fa-times"></i> Clear
                                </a>
                            <?php endif; ?>
                        </form>

                        <a href="<?= base_url('resident/create') ?>" class="rp-btn rp-btn-primary">
                            <i class="fas fa-plus"></i> Add Resident
                        </a>
                    </div>
                </div>

                <div class="rp-card-body">
                    <div class="table-responsive">
                        <table id="residentsTable" class="table">
                            <thead>
                                <tr>
                                    <th style="width: 40px;">ID</th>
                                    <th style="width: 60px;">Profile</th>
                                    <th>Full Name</th>
                                    <th style="width: 60px;">Sex</th>
                                    <th style="width: 50px;">Age</th>
                                    <th style="width: 100px;">Civil Status</th>
                                    <th style="width: 130px;">Purok / Sitio</th>
                                    <th style="width: 90px;">Household</th>
                                    <th>Occupation</th>
                                    <th>Citizenship</th>
                                    <th style="text-align: center; width: 80px;">Voter</th>
                                    <th style="width: 110px;">Flags</th>
                                    <th style="width: 100px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($residents)): ?>
                                    <tr>
                                        <td colspan="13" class="rp-empty">
                                            <i class="fas fa-inbox mb-2 d-block" style="font-size: 2rem; opacity: 0.3;"></i>
                                            <p style="margin:0;">No residents found<?= $selectedPurok != 'all' ? ' in ' . esc($selectedPurok) : '' ?>.</p>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($residents as $r): 
                                        // Calculate Age
                                        $age = '';
                                        if (!empty($r['birthdate'])) {
                                            $birth = new DateTime($r['birthdate']);
                                            $today = new DateTime();
                                            $age = $birth->diff($today)->y;
                                        }

                                        // Profile Image
                                        $profileImg = !empty($r['profile_picture'])
                                            ? base_url('uploads/' . $r['profile_picture'])
                                            : base_url('assets/img/default.png');

                                        // Voter Badge
                                        $voterBadge = !empty($r['is_voter'])
                                            ? '<span class="rp-badge rp-badge-voter-yes">Yes</span>'
                                            : '<span class="rp-badge rp-badge-voter-no">No</span>';

                                        // Senior Citizen Badge
                                        $seniorBadge = !empty($r['is_senior_citizen'])
                                            ? '<span class="rp-badge rp-badge-senior">Senior</span>'
                                            : '';

                                        // PWD Badge
                                        $pwdBadge = !empty($r['is_pwd'])
                                            ? '<span class="rp-badge rp-badge-pwd">PWD</span>'
                                            : '';

                                        $flags = trim($seniorBadge . ' ' . $pwdBadge);

                                        // Purok Display Logic
                                        $purokDisplay = !empty($r['sitio']) ? $r['sitio'] : 'Unassigned';
                                        $purokBadge = $purokDisplay != 'Unassigned'
                                            ? '<span class="rp-badge rp-badge-purok">' . esc($purokDisplay) . '</span>'
                                            : '<span class="rp-badge rp-badge-unassigned">Unassigned</span>';
                                    ?>
                                        <tr>
                                            <td><?= $r['id'] ?></td>
                                            <td>
                                                <img src="<?= $profileImg ?>" class="rp-avatar" alt="Profile">
                                            </td>
                                            <td class="td-name">
                                                <?= esc($r['first_name']) ?> <?= esc($r['middle_name'] ?? '') ?> <?= esc($r['last_name']) ?>
                                            </td>
                                            <td><?= ucfirst($r['sex']) ?></td>
                                            <td><?= $age ?></td>
                                            <td><?= ucfirst($r['civil_status'] ?? '') ?></td>
                                            <td><?= $purokBadge ?></td>
                                            <td><?= esc($r['household_no'] ?? '—') ?></td>
                                            <td><?= esc($r['occupation'] ?? '—') ?></td>
                                            <td><?= esc($r['citizenship'] ?? '—') ?></td>
                                            <td style="text-align:center;"><?= $voterBadge ?></td>
                                            <td>
                                                <?php if ($flags): ?>
                                                    <div style="display:flex;gap:0.25rem;flex-wrap:wrap;"><?= $flags ?></div>
                                                <?php else: ?>
                                                    <span style="color:#CBD5E0;">—</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="rp-actions">
                                                    <a href="<?= base_url('resident/view/' . $r['id']) ?>" class="rp-action-btn rp-action-view" title="View">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="<?= base_url('resident/edit/' . $r['id']) ?>" class="rp-action-btn rp-action-edit" title="Edit">
                                                        <i class="fas fa-pen"></i>
                                                    </a>
                                                    <button type="button" class="rp-action-btn rp-action-delete delete-resident" data-id="<?= $r['id'] ?>" title="Delete">
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

            <!-- Purok Statistics Cards -->
            <div class="rp-stats-card">
                <div class="rp-stats-header">
                    <i class="fas fa-chart-pie"></i>
                    <h5>Residents per Purok</h5>
                </div>
                <div class="rp-stats-body">
                    <?php
                    $tileAccents = ['#2B4FBF','#2D7F4E','#B45309','#B91C1C','#1D5FAD','#6B7280'];
                    $i = 0;
                    foreach ($purokCounts as $purok => $count):
                        $accent = $tileAccents[$i % count($tileAccents)];
                    ?>
                        <a href="<?= base_url('resident?purok=' . urlencode($purok)) ?>" 
                           class="rp-purok-tile"
                           data-purok-name="<?= esc($purok) ?>">
                            <div class="rp-purok-tile-inner" style="--tile-accent: <?= $accent ?>;">
                                <div class="rp-purok-count"><?= $count ?></div>
                                <div class="rp-purok-name"><?= esc($purok) ?></div>
                            </div>
                        </a>
                    <?php
                        $i++;
                    endforeach;
                    ?>
                </div>
            </div>

        </div>
    </section>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
    <!-- Config for JS -->
    <script>
        var RESIDENTS_CONFIG = {
            baseUrl:      "<?= base_url() ?>",
            csrfName:     "<?= csrf_token() ?>",
            csrfHash:     "<?= csrf_hash() ?>",
            currentPurok: "<?= $selectedPurok ?? 'all' ?>"
        };
    </script>

    <!-- External JS -->
     <link rel="stylesheet" href="<?= base_url('assets/css/resident/residents-index.css') ?>">

    <script src="<?= base_url('js/residents/residents-index.js') ?>"></script>
    <!-- Link CSS (Put here if <head> section is not available in template) -->
<?= $this->endSection() ?>
<?= $this->extend('theme/template') ?>
<?= $this->section('content') ?>

<div class="bmis-content">

    <!-- Page Header -->
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px">
        <div style="display:flex;align-items:center;gap:16px">
            <div style="width:48px;height:48px;border-radius:12px;background:var(--c-blue-bg);color:var(--c-blue);display:flex;align-items:center;justify-content:center;font-size:20px">
                <i class="fas fa-users"></i>
            </div>
            <div>
                <div class="ds-page-title" style="margin:0;font-size:24px;font-weight:800;color:var(--ink)">Resident Directory</div>
                <div style="font-size:13px;color:var(--ink-muted);margin-top:2px">Manage all registered residents in the barangay</div>
            </div>
        </div>
        <div style="display:flex;gap:10px">
            <a href="<?= base_url('resident/exportCsv' . (($selectedPurok ?? 'all') !== 'all' ? '?purok='.urlencode($selectedPurok) : '')) ?>" class="ds-btn ds-btn-ghost" style="height:40px;padding:0 20px;border-radius:20px;background:var(--white)"><i class="fas fa-file-csv"></i> Export CSV</a>
            <a href="<?= base_url('resident/create') ?>" class="ds-btn ds-btn-primary" style="height:40px;padding:0 20px;border-radius:20px;box-shadow:0 4px 12px rgba(var(--c-blue-rgb), 0.3)">
                <i class="fas fa-plus"></i> Add Resident
            </a>
        </div>
    </div>

    <!-- Flash messages -->
    <?php if (session()->getFlashdata('success')): ?>
        <div style="background:var(--c-teal-bg);color:var(--c-teal);padding:14px 20px;border-radius:var(--r-md);margin-bottom:24px;font-size:13px;font-weight:600;display:flex;align-items:center;gap:10px;border:1px solid rgba(var(--c-teal-rgb), 0.2)">
            <i class="fas fa-check-circle" style="font-size:16px"></i> <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div style="background:var(--c-rose-bg);color:var(--c-rose);padding:14px 20px;border-radius:var(--r-md);margin-bottom:24px;font-size:13px;font-weight:600;display:flex;align-items:center;gap:10px;border:1px solid rgba(var(--c-rose-rgb), 0.2)">
            <i class="fas fa-exclamation-circle" style="font-size:16px"></i> <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <!-- STAT CARDS -->
    <div class="ds-grid-4">
        <div class="ds-stat">
            <div class="ds-stat-stripe str-blue"></div>
            <div class="ds-stat-top"><div class="ds-stat-icon ic-blue"><i class="fas fa-users"></i></div></div>
            <div class="ds-stat-num"><?= array_sum($purokCounts) ?></div>
            <div class="ds-stat-label">Total Residents</div>
            <a href="<?= base_url('resident') ?>" class="ds-stat-footer ft-blue"><i class="fas fa-arrow-right"></i> View All</a>
        </div>
        <div class="ds-stat">
            <div class="ds-stat-stripe str-teal"></div>
            <div class="ds-stat-top"><div class="ds-stat-icon ic-green"><i class="fas fa-user-graduate"></i></div></div>
            <div class="ds-stat-num" id="seniorCount">0</div>
            <div class="ds-stat-label">Senior Citizens</div>
            <div class="ds-stat-footer ft-teal" style="cursor:default"><i class="fas fa-info-circle"></i> Age 60+</div>
        </div>
        <div class="ds-stat">
            <div class="ds-stat-stripe" style="background:var(--c-amber)"></div>
            <div class="ds-stat-top"><div class="ds-stat-icon ic-amber"><i class="fas fa-wheelchair"></i></div></div>
            <div class="ds-stat-num" id="pwdCount">0</div>
            <div class="ds-stat-label">PWD</div>
            <div class="ds-stat-footer" style="color:var(--c-amber);cursor:default"><i class="fas fa-info-circle"></i> Persons w/ Disability</div>
        </div>
        <div class="ds-stat">
            <div class="ds-stat-stripe" style="background:var(--c-green)"></div>
            <div class="ds-stat-top"><div class="ds-stat-icon ic-green"><i class="fas fa-check-circle"></i></div></div>
            <div class="ds-stat-num" id="voterCount">0</div>
            <div class="ds-stat-label">Voters</div>
            <div class="ds-stat-footer" style="color:var(--c-green);cursor:default"><i class="fas fa-info-circle"></i> Registered</div>
        </div>
    </div>

    <!-- FILTER BAR -->
    <div class="ds-card" style="margin-bottom:14px">
        <div class="ds-card-body">
            <div class="ds-filter-header"><i class="fas fa-filter"></i> Filter Residents</div>
            <div style="display:grid;grid-template-columns:2fr 1.5fr 1.5fr auto;gap:12px;align-items:end">
                <div>
                    <label class="ds-input-label">Search by name</label>
                    <input type="text" id="searchName" class="ds-input" placeholder="First or last name...">
                </div>
                <div>
                    <label class="ds-input-label">Purok / Sitio</label>
                    <select id="filterPurok" class="ds-select">
                        <option value="all" <?= ($selectedPurok ?? 'all') == 'all' ? 'selected' : '' ?>>All Puroks</option>
                        <?php foreach (['Purok Malipayon','Purok Masagana','Purok Cory','Purok Kawayan','Purok Pagla-um','Unassigned'] as $p): ?>
                            <option value="<?= $p ?>" <?= ($selectedPurok ?? '') == $p ? 'selected' : '' ?>><?= $p ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="ds-input-label">Household No.</label>
                    <input type="text" id="filterHousehold" class="ds-input" placeholder="e.g., HH-2025-001">
                </div>
                <div>
                    <button id="clearFilters" class="ds-btn ds-btn-ghost" style="height:36px">Clear</button>
                </div>
            </div>
        </div>
    </div>

    <!-- RESIDENTS TABLE -->
    <div class="ds-card" style="margin-bottom:24px;border:none;box-shadow:0 10px 30px rgba(0,0,0,0.03);border-radius:var(--r-lg);overflow:hidden">
        <div class="ds-card-head" style="background:var(--white);padding:20px 24px;border-bottom:1px solid var(--border)">
            <div class="ds-card-title"><i class="fas fa-list"></i> Full Resident List</div>
        </div>
        <div class="ds-card-body p0">
            <div style="overflow-x:auto">
                <table class="ds-table" id="residentsTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Photo</th>
                            <th>Full Name</th>
                            <th>Sex</th>
                            <th>Age</th>
                            <th>Civil Status</th>
                            <th>Purok / Sitio</th>
                            <th>Household No.</th>
                            <th>Occupation</th>
                            <th>Flags</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($residents as $r):
                            $profileImg = !empty($r['profile_picture']) ? base_url('uploads/' . $r['profile_picture']) : base_url('assets/img/default.png');
                            $flags = [];
                            if (!empty($r['is_senior_citizen'])) $flags[] = '<span class="ds-badge ds-badge-green">Senior</span>';
                            if (!empty($r['is_pwd'])) $flags[] = '<span class="ds-badge ds-badge-amber">PWD</span>';
                            if (!empty($r['is_voter'])) $flags[] = '<span class="ds-badge ds-badge-blue">Voter</span>';
                        ?>
                        <tr>
                            <td class="mono"><?= $r['id'] ?></td>
                            <td><img src="<?= $profileImg ?>" style="width:34px;height:34px;border-radius:50%;object-fit:cover;border:1px solid var(--border)"></td>
                            <td><strong class="font-serif" style="font-size:14px;letter-spacing:-0.01em;"><?= esc($r['first_name']) ?> <?= esc($r['last_name']) ?></strong></td>
                            <td><?= ucfirst($r['sex']) ?></td>
                            <td><?= $r['age'] ?? '—' ?></td>
                            <td><?= esc($r['civil_status'] ?? '—') ?></td>
                            <td style="font-size:10.5px;font-weight:700;text-transform:uppercase;color:var(--ink-muted)"><?= esc($r['sitio'] ?? 'Unassigned') ?></td>
                            <td class="mono"><?= esc($r['household_no'] ?? '—') ?></td>
                            <td><?= esc($r['occupation'] ?? '—') ?></td>
                            <td><?= implode(' ', $flags) ?: '—' ?></td>
                            <td style="white-space:nowrap">
                                <a href="<?= base_url('resident/view/'.$r['id']) ?>" class="ds-action-btn ab-blue" title="View"><i class="fas fa-eye"></i></a>
                                <a href="<?= base_url('resident/edit/'.$r['id']) ?>" class="ds-action-btn ab-amber" title="Edit"><i class="fas fa-edit"></i></a>
                                <button
                                    type="button"
                                    class="ds-action-btn ab-rose delete-resident"
                                    title="Delete"
                                    data-id="<?= esc($r['id']) ?>"
                                    data-name="<?= esc($r['first_name'].' '.$r['last_name'], 'attr') ?>"
                                ><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- PUROK TILES -->
    <div class="ds-section-label">Population by Purok</div>
    <div class="ds-grid-4" style="grid-template-columns:repeat(6,1fr)">
        <?php
        $purokColors = ['#185FA5','#1D9E75','#534AB7','#854F0B','#A32D2D','#3B6D11'];
        $purokBgs = ['#E6F1FB','#E1F5EE','#EEEDFE','#FAEEDA','#FCEBEB','#EAF3DE'];
        $i = 0;
        foreach ($purokCounts as $purok => $count): ?>
        <a href="?purok=<?= urlencode($purok) ?>" class="ds-mini" style="flex-direction:column;text-align:center;text-decoration:none;gap:6px;padding:16px 10px">
            <div style="font-size:22px;font-weight:800;color:<?= $purokColors[$i % 6] ?>"><?= $count ?></div>
            <div style="font-size:9.5px;font-weight:700;text-transform:uppercase;letter-spacing:.04em;color:var(--ink-muted)"><?= esc($purok) ?></div>
        </a>
        <?php $i++; endforeach; ?>
    </div>
</div>



<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    window.ResidentConfig = {
        baseUrl: "<?= base_url() ?>",
        csrfName: "<?= csrf_token() ?>",
        csrfHash: "<?= csrf_hash() ?>"
    };
</script>
<script>
    window.RESIDENTS_CONFIG = {
        baseUrl: "<?= base_url() ?>",
        csrfName: "<?= csrf_token() ?>",
        csrfHash: "<?= csrf_hash() ?>",
        currentPurok: "<?= $selectedPurok ?? 'all' ?>"
    };
</script>
<script src="<?= base_url('js/residents/residents-index.js') ?>"></script>
<?= $this->endSection() ?>
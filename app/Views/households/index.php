<?= $this->extend('theme/template') ?>
<?= $this->section('content') ?>

<div class="bmis-content">

    <!-- Page Header -->
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px">
        <div style="display:flex;align-items:center;gap:16px">
            <div style="width:48px;height:48px;border-radius:12px;background:var(--c-teal-bg);color:var(--c-teal);display:flex;align-items:center;justify-content:center;font-size:20px">
                <i class="fas fa-home"></i>
            </div>
            <div>
                <div class="ds-page-title" style="margin:0;font-size:24px;font-weight:800;color:var(--ink)">Household Directory</div>
                <div style="font-size:13px;color:var(--ink-muted);margin-top:2px">Manage family groupings and addresses in the barangay</div>
            </div>
        </div>
        <a href="<?= base_url('households/create') ?>" class="ds-btn ds-btn-teal" style="height:40px;padding:0 20px;border-radius:20px;box-shadow:0 4px 12px rgba(var(--c-teal-rgb), 0.3)">
            <i class="fas fa-plus"></i> New Household
        </a>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div style="background:var(--c-teal-bg);color:var(--c-teal);padding:14px 20px;border-radius:var(--r-md);margin-bottom:24px;font-size:13px;font-weight:600;display:flex;align-items:center;gap:10px;border:1px solid rgba(var(--c-teal-rgb), 0.2)">
            <i class="fas fa-check-circle" style="font-size:16px"></i> <?= esc(session()->getFlashdata('success')) ?>
        </div>
    <?php endif; ?>

    <!-- STAT CARDS -->
    <div class="ds-grid-3">
        <div class="ds-stat"><div class="ds-stat-stripe str-teal"></div><div class="ds-stat-top"><div class="ds-stat-icon ic-teal"><i class="fas fa-home"></i></div></div><div class="ds-stat-num"><?= $totalHouseholds ?? 0 ?></div><div class="ds-stat-label">Total Households</div><a href="<?= base_url('households') ?>" class="ds-stat-footer ft-teal"><i class="fas fa-arrow-right"></i> View All</a></div>
        <div class="ds-stat"><div class="ds-stat-stripe str-blue"></div><div class="ds-stat-top"><div class="ds-stat-icon ic-blue"><i class="fas fa-users"></i></div></div><div class="ds-stat-num"><?= $totalResidents ?? 0 ?></div><div class="ds-stat-label">Total Residents</div><a href="<?= base_url('resident') ?>" class="ds-stat-footer ft-blue"><i class="fas fa-arrow-right"></i> View</a></div>
        <div class="ds-stat"><div class="ds-stat-stripe" style="background:var(--c-amber)"></div><div class="ds-stat-top"><div class="ds-stat-icon ic-amber"><i class="fas fa-chart-pie"></i></div></div><div class="ds-stat-num"><?= $avgPerHousehold ?? 0 ?></div><div class="ds-stat-label">Avg. Members</div><div class="ds-stat-footer" style="color:var(--c-amber);cursor:default"><i class="fas fa-info-circle"></i> Per Household</div></div>
    </div>

    <!-- FILTER + ADD -->
    <div class="ds-card" style="margin-bottom:14px">
        <div class="ds-card-body" style="display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap">
            <div style="display:flex;gap:12px;align-items:center;flex:1">
                <div style="position:relative;width:100%;max-width:280px">
                    <i class="fas fa-search" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--ink-soft);font-size:12px"></i>
                    <input type="text" id="hhSearch" class="ds-input" placeholder="Search HH #, Head name..." style="padding-left:32px">
                </div>
                <select id="purokFilter" class="ds-select" style="width:auto;min-width:160px" onchange="location.href='<?= base_url('households') ?>?purok='+encodeURIComponent(this.value)">
                    <option value="all" <?= ($selectedPurok ?? 'all') == 'all' ? 'selected' : '' ?>>All Puroks</option>
                    <?php foreach (['Purok Malipayon','Purok Masagana','Purok Cory','Purok Kawayan','Purok Pagla-um'] as $p): ?>
                        <option value="<?= $p ?>" <?= ($selectedPurok ?? '') == $p ? 'selected' : '' ?>><?= $p ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>

    <!-- TABLE -->
    <div class="ds-card" style="margin-bottom:24px;border:none;box-shadow:0 10px 30px rgba(0,0,0,0.03);border-radius:var(--r-lg);overflow:hidden">
        <div class="ds-card-head" style="background:var(--white);padding:20px 24px;border-bottom:1px solid var(--border)">
            <div class="ds-card-title"><i class="fas fa-list"></i> Full Household List</div>
        </div>
        <div class="ds-card-body p0">
            <div style="overflow-x:auto">
                <table class="ds-table" id="hhTable">
                    <thead><tr><th>Household No</th><th>Head of Family</th><th>Sitio</th><th>Address</th><th>Members</th><th>Actions</th></tr></thead>
                    <tbody>

                        <?php if (empty($households)): ?>
                            <tr><td colspan="6" style="text-align:center;padding:32px;color:var(--ink-soft)"><i class="fas fa-home" style="font-size:20px;opacity:.3;display:block;margin-bottom:8px"></i>No households found.</td></tr>
                        <?php else: foreach ($households as $h):
                            $count = $h['resident_count'];
                            $bc = 'ds-badge-gray'; $bt = 'Low';
                            if ($count >= 4 && $count < 7) { $bc = 'ds-badge-blue'; $bt = 'Medium'; }
                            if ($count >= 7) { $bc = 'ds-badge-amber'; $bt = 'High'; }
                            if ($count >= 10) { $bc = 'ds-badge-rose'; $bt = 'Crowded'; }
                        ?>
                        <tr>
                            <td><a href="<?= base_url('households/view/'.$h['id']) ?>" style="color:var(--c-teal);font-weight:700;text-decoration:none;font-family:var(--mono);font-size:11.5px"><?= esc($h['household_no']) ?></a></td>
                            <td>
                                <div style="display:flex;align-items:center;gap:10px">
                                    <img src="<?= base_url($h['head_photo']) ?>" 
                                         style="width:28px;height:28px;border-radius:50%;object-fit:cover;border:1px solid var(--border)">
                                    <strong><?= esc($h['head_name']) ?></strong>
                                </div>
                            </td>
                            <td style="font-size:10.5px;font-weight:700;text-transform:uppercase;color:var(--ink-muted)"><?= esc($h['sitio']) ?></td>
                            <td style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="<?= esc($h['address'] ?: $h['street_address']) ?>"><?= esc($h['address'] ?: $h['street_address']) ?></td>
                            <td>
                                <span class="ds-badge <?= $bc ?> resident-count"><?= $count ?></span>
                                <span style="font-size:10px;color:var(--ink-soft)"><?= $bt ?></span>
                            </td>
                            <td style="white-space:nowrap">
                                <a href="<?= base_url('households/view/'.$h['id']) ?>" class="ds-action-btn ab-blue" title="View"><i class="fas fa-eye"></i></a>
                                <a href="<?= base_url('households/edit/'.$h['id']) ?>" class="ds-action-btn ab-amber" title="Edit"><i class="fas fa-pen"></i></a>
                                <button
                                    type="button"
                                    class="ds-action-btn ab-rose delete-household"
                                    title="Delete"
                                    data-id="<?= esc($h['id']) ?>"
                                    data-no="<?= esc($h['household_no'], 'attr') ?>"
                                ><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div id="js-variables" style="display:none;" data-base-url="<?= base_url() ?>" data-csrf-token="<?= csrf_token() ?>" data-csrf-hash="<?= csrf_hash() ?>"></div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('js/households/households-index.js') ?>"></script>
<?= $this->endSection() ?>
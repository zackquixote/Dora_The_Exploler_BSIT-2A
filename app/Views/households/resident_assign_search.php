<?php
$role = strtolower(session()->get('role') ?? 'staff');
$template = ($role == 'admin') ? 'theme/admin/template' : 'theme/template';
?>
<?= $this->extend($template) ?>
<?= $this->section('content') ?>

<div class="bmis-content">
    <!-- Premium Page Header -->
    <div class="bmis-page-header">
        <div class="bmis-page-title">
            <h1 style="font-weight: 800;"><i class="fas fa-user-plus text-primary"></i> Assign Existing Residents</h1>
            <p>Target: <strong style="color:var(--ink)">Household #<?= esc($household_id) ?></strong></p>
        </div>
        <div class="bmis-page-actions">
            <a href="<?= base_url('households') ?>" class="btn btn-light btn-sm rounded-pill px-3 fw-bold shadow-sm" style="border: 1px solid var(--border);"><i class="fas fa-arrow-left me-2"></i> Back to Directory</a>
        </div>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div style="background:var(--c-teal-bg);color:var(--c-teal);padding:12px 16px;border-radius:var(--r-sm);margin-bottom:14px;font-size:12px;font-weight:600">
            <i class="fas fa-check-circle" style="margin-right:6px"></i> <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>

    <!-- Search / Filter -->
    <div class="ds-card" style="margin-bottom:14px">
        <div class="ds-card-body">
            <div style="display:grid;grid-template-columns:1fr 2fr;gap:20px">
                <div>
                    <div class="ds-section-label" style="margin-bottom:6px">Filter by Location</div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px">
                        <select id="filter_purok" class="ds-select">
                            <option value="">All Purok</option>
                            <?php foreach (['Purok Malipayon', 'Purok Masagana', 'Purok Cory', 'Purok Kawayan', 'Purok Pagla-um'] as $p): ?>
                                <option value="<?= $p ?>" <?= ($filterPurok == $p) ? 'selected' : '' ?>><?= $p ?></option>
                            <?php endforeach; ?>
                        </select>
                        <select id="filter_household_id" name="filter_household_id" class="ds-select">
                            <option value="">All Houses</option>
                        </select>
                    </div>
                </div>
                <div>
                    <div class="ds-section-label" style="margin-bottom:6px">Search by Name</div>
                    <form action="" method="get" style="display:flex;gap:8px">
                        <input type="hidden" name="household_id" value="<?= esc($household_id) ?>">
                        <input type="hidden" name="filter_purok" id="hidden_purok" value="<?= esc($filterPurok) ?>">
                        <input type="hidden" name="filter_household_id" id="hidden_household" value="<?= esc($filterHouseId) ?>">
                        
                        <input type="text" name="q" class="ds-input" placeholder="Search name..." value="<?= esc($keyword) ?>">
                        <button type="submit" class="ds-btn ds-btn-blue"><i class="fas fa-search"></i> Search</button>
                        <?php if(!empty($filterPurok) || !empty($filterHouseId) || !empty($keyword)): ?>
                            <a href="<?= base_url('resident/assign-search?household_id='.$household_id) ?>" class="ds-btn ds-btn-rose"><i class="fas fa-times"></i> Clear</a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Results Table -->
    <form action="<?= base_url('resident/assignBulk') ?>" method="post" id="bulkForm">
        <input type="hidden" name="target_household_id" value="<?= esc($household_id) ?>">
        <div class="ds-card">
            <div class="ds-card-head">
                <div class="ds-card-title"><i class="fas fa-users"></i> Available Residents</div>
                <button type="button" class="ds-btn ds-btn-teal" id="assignSelectedBtn" style="height:30px;font-size:11px"><i class="fas fa-check"></i> Assign Selected</button>
            </div>
            <div class="ds-card-body p0">
                <?php if (empty($residents)): ?>
                    <div style="text-align:center;padding:40px;color:var(--ink-soft)">
                        <i class="fas fa-search" style="font-size:24px;opacity:0.3;margin-bottom:10px;display:block"></i>
                        <div style="font-size:13px;font-weight:700;color:var(--ink)">No residents found</div>
                        <div style="font-size:11px">Try adjusting your search criteria</div>
                    </div>
                <?php else: ?>
                    <div style="overflow-x:auto">
                        <table class="ds-table">
                            <thead>
                                <tr>
                                    <th style="width:40px;text-align:center">Select</th>
                                    <th>Resident</th>
                                    <th>Current Info</th>
                                    <th style="width:250px">Relationship to Head</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($residents as $r): 
                                    $profileSrc = 'https://ui-avatars.com/api/?name='.urlencode($r['first_name'] . ' ' . $r['last_name']).'&background=random&color=fff&size=40';
                                    if (!empty($r['profile_picture'])) $profileSrc = base_url('uploads/' . $r['profile_picture']);
                                ?>
                                <tr>
                                    <td style="text-align:center">
                                        <input type="checkbox" name="selected_residents[]" value="<?= $r['id'] ?>" id="check_<?= $r['id'] ?>" style="accent-color:var(--c-teal);width:14px;height:14px">
                                    </td>
                                    <td>
                                        <div style="display:flex;align-items:center;gap:10px">
                                            <img src="<?= $profileSrc ?>" style="width:32px;height:32px;border-radius:50%;object-fit:cover">
                                            <strong><?= esc($r['first_name'] . ' ' . $r['last_name']) ?></strong>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="ds-badge ds-badge-blue"><?= esc($r['sitio']) ?></span>
                                        <div style="font-size:10px;color:var(--ink-muted);margin-top:4px">
                                            <?= $r['household_id'] ? 'Household #'.$r['household_id'] : 'No Household' ?>
                                        </div>
                                    </td>
                                    <td>
                                        <select name="relationships[<?= $r['id'] ?>]" id="rel_<?= $r['id'] ?>" class="ds-select" style="height:28px;font-size:11px" disabled>
                                            <option value="">Select Relationship...</option>
                                            <?php foreach (['Head','Spouse','Son','Daughter','Father','Mother','Brother','Sister','Grandchild','Other'] as $rel): ?>
                                                <option value="<?= $rel ?>"><?= $rel ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
            <?php if (!empty($pager) && $pager->getPageCount() > 1): ?>
            <div style="padding:14px;border-top:1px solid var(--border);display:flex;justify-content:center">
                <?= $pager->links() ?>
            </div>
            <?php endif; ?>
        </div>
    </form>
</div>

<div id="js-variables" style="display:none;"
     data-base-url="<?= base_url() ?>"
     data-csrf-token="<?= csrf_token() ?>"
     data-csrf-hash="<?= csrf_hash() ?>"
     data-household-id="<?= esc($household_id) ?>">
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('js/residents/resident-assign-search.js') ?>"></script>
<?= $this->endSection() ?>
<?= $this->extend('theme/template') ?>
<?= $this->section('content') ?>

<div class="bmis-content">

    <!-- Header -->
    <div class="bmis-page-header">
        <div class="bmis-page-title">
            <h1 style="font-weight:800"><i class="fas fa-user-plus text-primary"></i> Add Existing Resident</h1>
            <p>Assigning to: <strong style="color:var(--ink)">Household #<?= esc($household_id) ?></strong>
                <?php if (!empty($householdSitio)): ?>
                    <span class="ds-badge ds-badge-blue" style="margin-left:6px"><?= esc($householdSitio) ?></span>
                <?php endif; ?>
            </p>
        </div>
        <div class="bmis-page-actions">
            <a href="<?= base_url('households/view/' . $household_id) ?>" class="ds-btn ds-btn-ghost">
                <i class="fas fa-arrow-left"></i> Back to Household
            </a>
        </div>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div style="background:var(--c-teal-bg);color:var(--c-teal);padding:12px 16px;border-radius:var(--r-sm);margin-bottom:14px;font-size:12px;font-weight:600">
            <i class="fas fa-check-circle" style="margin-right:6px"></i> <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div style="background:var(--c-rose-bg);color:var(--c-rose);padding:12px 16px;border-radius:var(--r-sm);margin-bottom:14px;font-size:12px;font-weight:600">
            <i class="fas fa-exclamation-circle" style="margin-right:6px"></i> <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <!-- Filter bar -->
    <div class="ds-card" style="margin-bottom:14px">
        <div class="ds-card-body">
            <form action="" method="get" id="filterForm">
                <input type="hidden" name="household_id" value="<?= esc($household_id) ?>">
                <div style="display:grid;grid-template-columns:1fr 1fr 2fr auto;gap:12px;align-items:end">
                    <div>
                        <label class="ds-input-label">Filter by Purok</label>
                        <select name="filter_purok" id="filter_purok" class="ds-select">
                            <option value="">All Puroks</option>
                            <?php foreach (['Purok Malipayon','Purok Masagana','Purok Cory','Purok Kawayan','Purok Pagla-um'] as $p): ?>
                                <option value="<?= $p ?>" <?= ($filterPurok == $p) ? 'selected' : '' ?>><?= $p ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (!empty($householdSitio)): ?>
                            <div style="font-size:10px;color:var(--ink-soft);margin-top:3px">
                                <i class="fas fa-info-circle" style="color:var(--c-blue)"></i>
                                Pre-filtered to household's purok
                            </div>
                        <?php endif; ?>
                    </div>
                    <div>
                        <label class="ds-input-label">Show Only</label>
                        <select name="filter_status" id="filter_status" class="ds-select">
                            <option value="">All Residents</option>
                            <option value="no_household" <?= (($_GET['filter_status'] ?? '') == 'no_household') ? 'selected' : '' ?>>Without a Household</option>
                        </select>
                    </div>
                    <div>
                        <label class="ds-input-label">Search by Name</label>
                        <div style="position:relative">
                            <i class="fas fa-search" style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:var(--ink-soft);font-size:11px"></i>
                            <input type="text" name="q" class="ds-input" placeholder="Type a name…" value="<?= esc($keyword) ?>" style="padding-left:30px">
                        </div>
                    </div>
                    <div style="display:flex;gap:6px">
                        <button type="submit" class="ds-btn ds-btn-primary" style="height:36px"><i class="fas fa-search"></i> Search</button>
                        <?php if (!empty($filterPurok) || !empty($keyword) || !empty($this->request->getGet('filter_status'))): ?>
                            <a href="<?= base_url('resident/assign-search?household_id=' . $household_id) ?>" class="ds-btn ds-btn-ghost" style="height:36px" title="Reset filters"><i class="fas fa-times"></i></a>
                        <?php endif; ?>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Results + Assign form -->
    <form action="<?= base_url('resident/assignBulk') ?>" method="post" id="bulkForm">
        <?= csrf_field() ?>
        <input type="hidden" name="target_household_id" value="<?= esc($household_id) ?>">

        <div class="ds-card">
            <div class="ds-card-head">
                <div class="ds-card-title">
                    <i class="fas fa-users"></i> Available Residents
                    <?php if (!empty($residents)): ?>
                        <span class="ds-badge ds-badge-blue" style="margin-left:6px"><?= count($residents) ?> found</span>
                    <?php endif; ?>
                </div>
                <div style="display:flex;align-items:center;gap:10px">
                    <?php if (!empty($residents)): ?>
                        <label style="display:flex;align-items:center;gap:6px;font-size:11px;font-weight:600;color:var(--ink-muted);cursor:pointer">
                            <input type="checkbox" id="selectAllCheckbox" style="accent-color:var(--c-teal);width:14px;height:14px">
                            Select All
                        </label>
                        <span id="selectedCount" style="font-size:11px;font-weight:700;color:var(--c-teal);display:none">
                            <i class="fas fa-check-circle"></i> <span id="selectedNum">0</span> selected
                        </span>
                    <?php endif; ?>
                    <button type="submit" class="ds-btn ds-btn-teal" id="assignBtn" style="height:32px;font-size:11px" disabled>
                        <i class="fas fa-user-check"></i> Assign Selected
                    </button>
                </div>
            </div>

            <div class="ds-card-body p0">
                <?php if (empty($residents)): ?>
                    <div style="text-align:center;padding:48px 24px;color:var(--ink-soft)">
                        <i class="fas fa-search" style="font-size:28px;opacity:0.25;display:block;margin-bottom:12px"></i>
                        <div style="font-size:13px;font-weight:700;color:var(--ink);margin-bottom:4px">No residents found</div>
                        <div style="font-size:11px">
                            <?php if (!empty($filterPurok)): ?>
                                No active residents in <strong><?= esc($filterPurok) ?></strong> match your search.
                            <?php else: ?>
                                Try adjusting your search or filter.
                            <?php endif; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div style="overflow-x:auto">
                        <table class="ds-table" id="residentsTable">
                            <thead>
                                <tr>
                                    <th style="width:44px;text-align:center"></th>
                                    <th>Resident</th>
                                    <th>Purok</th>
                                    <th>Current Household</th>
                                    <th style="width:220px">Relationship to Head</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($residents as $r):
                                    $profileSrc = !empty($r['profile_picture'])
                                        ? base_url('uploads/' . $r['profile_picture'])
                                        : 'https://ui-avatars.com/api/?name=' . urlencode($r['first_name'] . ' ' . $r['last_name']) . '&background=4F46E5&color=fff&size=40&bold=true';
                                ?>
                                <tr class="resident-row">
                                    <td style="text-align:center">
                                        <input type="checkbox"
                                               name="selected_residents[]"
                                               value="<?= $r['id'] ?>"
                                               class="resident-checkbox"
                                               style="accent-color:var(--c-teal);width:15px;height:15px"
                                               checked>
                                    </td>
                                    <td>
                                        <div style="display:flex;align-items:center;gap:10px">
                                            <img src="<?= $profileSrc ?>"
                                                 onerror="this.src='https://ui-avatars.com/api/?name=<?= urlencode($r['first_name'] . ' ' . $r['last_name']) ?>&background=4F46E5&color=fff&size=40&bold=true'"
                                                 style="width:34px;height:34px;border-radius:50%;object-fit:cover;flex-shrink:0">
                                            <div>
                                                <strong style="font-size:13px;color:var(--ink)"><?= esc($r['last_name'] . ', ' . $r['first_name']) ?></strong>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if (!empty($r['sitio'])): ?>
                                            <span class="ds-badge ds-badge-blue" style="font-size:9.5px"><?= esc($r['sitio']) ?></span>
                                        <?php else: ?>
                                            <span style="color:var(--ink-soft);font-size:11px">—</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($r['household_id'])): ?>
                                            <span style="font-size:11px;color:var(--ink-muted)">
                                                <i class="fas fa-home" style="margin-right:4px;color:var(--c-amber)"></i>
                                                HH #<?= esc($r['household_id']) ?>
                                            </span>
                                        <?php else: ?>
                                            <span style="font-size:11px;color:var(--c-teal);font-weight:600">
                                                <i class="fas fa-check-circle" style="margin-right:4px"></i>No household
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($headResidentId) && (int)$r['id'] === (int)$headResidentId): ?>
                                            <!-- Head resident: relationship is locked to "Head" -->
                                            <input type="hidden" name="relationships[<?= $r['id'] ?>]" value="Head">
                                            <span class="ds-badge" style="background:var(--c-amber-bg);color:var(--c-amber);font-size:10px;font-weight:700">
                                                <i class="fas fa-star" style="margin-right:3px"></i> Head
                                            </span>
                                        <?php else: ?>
                                        <select name="relationships[<?= $r['id'] ?>]"
                                                class="ds-select relationship-select"
                                                style="height:30px;font-size:11px">
                                            <option value="">Select…</option>
                                            <?php foreach (['Spouse','Son','Daughter','Father','Mother','Brother','Sister','Grandfather','Grandmother','Grandson','Granddaughter','Uncle','Aunt','Nephew','Niece','Cousin','Son-in-law','Daughter-in-law','Brother-in-law','Sister-in-law','Other Relative','Non-Relative'] as $rel): ?>
                                                <option value="<?= $rel ?>" <?= ($r['relationship_to_head'] ?? '') === $rel ? 'selected' : '' ?>><?= $rel ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

            <?php if (!empty($residents)): ?>
            <div style="padding:12px 18px;border-top:.5px solid var(--border);display:flex;align-items:center;justify-content:space-between">
                <span style="font-size:11px;color:var(--ink-soft)">
                    <i class="fas fa-info-circle" style="color:var(--c-blue);margin-right:4px"></i>
                    All residents are pre-selected. Uncheck any you don't want to assign.
                </span>
                <button type="submit" class="ds-btn ds-btn-teal" id="assignBtnBottom" style="height:36px">
                    <i class="fas fa-user-check"></i> Assign Selected
                </button>
            </div>
            <?php endif; ?>
        </div>
    </form>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
(function ($) {
    'use strict';

    var $checkboxes   = $('.resident-checkbox');
    var $selectAll    = $('#selectAllCheckbox');
    var $assignBtn    = $('#assignBtn, #assignBtnBottom');
    var $countDisplay = $('#selectedCount');
    var $countNum     = $('#selectedNum');

    function updateState() {
        var total   = $checkboxes.length;
        var checked = $checkboxes.filter(':checked').length;

        // Assign button — enabled only when at least one is checked
        $assignBtn.prop('disabled', checked === 0);

        // Count badge
        if (checked > 0) {
            $countNum.text(checked);
            $countDisplay.show();
        } else {
            $countDisplay.hide();
        }

        // Select-all checkbox state
        if ($selectAll.length) {
            $selectAll.prop('indeterminate', checked > 0 && checked < total);
            $selectAll.prop('checked', total > 0 && checked === total);
        }
    }

    // Individual checkbox change
    $(document).on('change', '.resident-checkbox', function () {
        updateState();
    });

    // Select-all toggle
    $selectAll.on('change', function () {
        $checkboxes.prop('checked', $(this).is(':checked'));
        updateState();
    });

    // Confirm before submitting if any resident already has a household
    $('#bulkForm').on('submit', function (e) {
        var checked = $checkboxes.filter(':checked');
        if (checked.length === 0) {
            e.preventDefault();
            alert('Please select at least one resident to assign.');
            return;
        }

        // Warn about residents already in another household
        var alreadyAssigned = [];
        checked.each(function () {
            var row  = $(this).closest('tr');
            var info = row.find('td:nth-child(4)').text().trim();
            if (info.indexOf('HH #') !== -1) {
                var name = row.find('strong').text().trim();
                alreadyAssigned.push(name);
            }
        });

        if (alreadyAssigned.length > 0) {
            var msg = alreadyAssigned.length + ' selected resident(s) already belong to another household:\n\n' +
                      alreadyAssigned.join('\n') +
                      '\n\nThey will be moved to this household. Continue?';
            if (!confirm(msg)) {
                e.preventDefault();
            }
        }
    });

    // Run on load to set initial state (all pre-checked)
    updateState();

})(jQuery);
</script>
<?= $this->endSection() ?>

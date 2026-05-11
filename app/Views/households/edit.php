<?= $this->extend('theme/template') ?>
<?= $this->section('content') ?>

<div class="bmis-content">

    <!-- Premium Page Header -->
    <div class="bmis-page-header">
        <div class="bmis-page-title">
            <h1 style="font-weight: 800;"><i class="fas fa-home text-primary"></i> Edit Household Profile</h1>
            <p>Modifying details for Household: <strong style="color:var(--ink)"><?= esc($household['household_no']) ?></strong></p>
        </div>
        <div class="bmis-page-actions">
            <a href="<?= base_url('households') ?>" class="btn btn-light btn-sm rounded-pill px-3 fw-bold shadow-sm" style="border: 1px solid var(--border);"><i class="fas fa-arrow-left me-2"></i> Back to Directory</a>
        </div>
    </div>

    <?php if (session()->getFlashdata('errors')): ?>
        <div style="background:var(--c-rose-bg);color:var(--c-rose);padding:12px 16px;border-radius:var(--r-sm);margin-bottom:14px;font-size:12px;font-weight:600">
            <i class="fas fa-exclamation-circle" style="margin-right:6px"></i> Please fix:
            <ul style="margin:6px 0 0 16px;padding:0"><?php foreach (session()->getFlashdata('errors') as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?></ul>
        </div>
    <?php endif; ?>

    <form id="householdForm" action="<?= base_url('households/update/'.$household['id']) ?>" method="POST">
        <?= csrf_field() ?>
        <input type="hidden" name="id" value="<?= $household['id'] ?>">

        <!-- HOUSEHOLD INFO -->
        <div class="ds-card" style="margin-bottom:14px">
            <div class="ds-card-head"><div class="ds-card-title"><i class="fas fa-home"></i> Household Information</div></div>
            <div class="ds-card-body">
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px">
                    <div>
                        <label class="ds-input-label">Household Number</label>
                        <input type="text" name="household_no" id="householdNo" class="ds-input" value="<?= esc($household['household_no']) ?>" readonly style="background:var(--bg)">
                        <div style="font-size:10px;color:var(--ink-soft);margin-top:4px">Cannot be changed</div>
                    </div>
                    <div>
                        <label class="ds-input-label">Purok / Sitio <span style="color:var(--c-rose)">*</span></label>
                        <select name="sitio" id="sitioSelect" class="ds-select" required>
                            <option value="">Select Purok</option>
                            <?php foreach (['Purok Malipayon','Purok Masagana','Purok Cory','Purok Kawayan','Purok Pagla-um'] as $s): ?>
                                <option value="<?= $s ?>" <?= ($household['sitio']??'')==$s?'selected':'' ?>><?= $s ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="ds-input-label">House Type</label>
                        <select name="house_type" class="ds-select">
                            <option value="">Select Type</option>
                            <?php foreach (['Concrete','Semi-Concrete','Wood','Light Materials'] as $ht): ?>
                                <option value="<?= $ht ?>" <?= ($household['house_type']??'')==$ht?'selected':'' ?>><?= $ht ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- ADDRESS -->
        <div class="ds-card" style="margin-bottom:14px">
            <div class="ds-card-head"><div class="ds-card-title"><i class="fas fa-map-marker-alt"></i> Address</div></div>
            <div class="ds-card-body">
                <input type="hidden" name="address" id="completeAddress" value="<?= esc($household['address'] ?? '') ?>">
                <label class="ds-input-label">Street Address</label>
                <input type="text" name="street_address" id="streetAddress" class="ds-input" value="<?= esc($household['street_address'] ?? '') ?>" placeholder="e.g., Block 1, Lot 2">
            </div>
        </div>

        <!-- HEAD OF HOUSEHOLD -->
        <div class="ds-card" style="margin-bottom:14px">
            <div class="ds-card-head"><div class="ds-card-title"><i class="fas fa-user-tie"></i> Head of Household</div></div>
            <div class="ds-card-body">
                <div style="max-width:400px">
                    <label class="ds-input-label">Select Head Resident</label>
                    <select name="head_resident_id" id="headResidentSelect" class="ds-select">
                        <option value="">Select Head</option>
                    </select>
                </div>
                <div id="membersLoadingAlert" style="display:none;margin-top:10px;padding:8px 12px;background:var(--c-blue-bg);color:var(--c-blue);border-radius:var(--r-sm);font-size:11px">
                    <i class="fas fa-spinner fa-spin" style="margin-right:6px"></i> Loading…
                </div>
            </div>
        </div>

        <?php if (!empty($residentCount) && $residentCount > 0): ?>
        <div style="background:var(--c-blue-bg);color:var(--c-blue);padding:10px 16px;border-radius:var(--r-sm);margin-bottom:14px;font-size:12px;font-weight:600">
            <i class="fas fa-info-circle" style="margin-right:6px"></i> This household has <strong><?= $residentCount ?></strong> registered resident(s).
        </div>
        <?php endif; ?>

        <!-- MEMBERS -->
        <div class="ds-card" style="margin-bottom:14px">
            <div class="ds-card-head">
                <div class="ds-card-title"><i class="fas fa-users"></i> Manage Members</div>
                <div style="display:flex;gap:8px;align-items:center">
                    <span class="ds-badge ds-badge-blue" id="selectedCount">0 selected</span>
                    <button type="button" class="ds-btn ds-btn-ghost" id="toggleAllMembers" style="height:28px;font-size:10px"><i class="fas fa-check-double"></i> Toggle All</button>
                </div>
            </div>
            <div class="ds-card-body p0">
                <div id="membersTableContainer">
                    <div style="overflow-x:auto">
                        <table class="ds-table" id="membersTable">
                            <thead><tr><th style="width:40px"><input type="checkbox" id="selectAllCheckbox" style="accent-color:var(--c-teal)"></th><th>Resident Name</th><th style="width:250px">Relationship to Head</th></tr></thead>
                            <tbody id="membersTableBody"></tbody>
                        </table>
                    </div>
                </div>
                <div id="emptyMembersState" style="display:none;text-align:center;padding:32px;color:var(--ink-soft)">
                    <i class="fas fa-users" style="font-size:24px;opacity:.3;display:block;margin-bottom:8px"></i>
                    <div style="font-weight:700;color:var(--ink)">No Residents Found</div>
                </div>
            </div>
            <div style="padding:10px 18px;border-top:.5px solid var(--border);font-size:10px;color:var(--ink-soft)">
                <i class="fas fa-info-circle" style="margin-right:4px;color:var(--c-blue)"></i> Check to add, uncheck to remove.
            </div>
        </div>

        <input type="hidden" name="household_members_data" id="householdMembersData" value="[]">

        <div id="js-variables" style="display:none;"
             data-base-url="<?= base_url() ?>"
             data-csrf-token="<?= csrf_token() ?>"
             data-csrf-hash="<?= csrf_hash() ?>"
             data-household-id="<?= $household['id'] ?>"
             data-head-id="<?= $household['head_resident_id'] ?: 'null' ?>"
             data-current-sitio="<?= esc($household['sitio'] ?? '') ?>"
             data-current-members='<?= json_encode($currentMembers ?? []) ?>'>
        </div>

        <div style="display:flex;justify-content:space-between;margin-bottom:24px">
            <a href="<?= base_url('households/view/'.$household['id']) ?>" class="ds-btn ds-btn-ghost"><i class="fas fa-arrow-left"></i> Cancel</a>
            <button type="submit" class="ds-btn ds-btn-primary" id="submitBtn"><i class="fas fa-save"></i> Update Household</button>
        </div>
    </form>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('js/households/households-edit.js') ?>"></script>
<?= $this->endSection() ?>
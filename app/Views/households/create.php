<?= $this->extend('theme/template') ?>
<?= $this->section('content') ?>
<?php $validationErrors = session()->getFlashdata('errors') ?? []; ?>

<div class="bmis-content">

    <!-- Premium Page Header -->
    <div class="bmis-page-header">
        <div class="bmis-page-title">
            <h1 style="font-weight: 800;"><i class="fas fa-home text-primary"></i> Add New Household</h1>
            <p>Register a new household and assign members.</p>
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

    <form id="householdForm" action="<?= base_url('households/store') ?>" method="POST">
        <?= csrf_field() ?>

        <!-- HOUSEHOLD INFO -->
        <div class="ds-card" style="margin-bottom:14px">
            <div class="ds-card-head"><div class="ds-card-title"><i class="fas fa-home"></i> Household Information</div></div>
            <div class="ds-card-body">
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px">
                    <div>
                        <label class="ds-input-label">Household Number <span style="color:var(--c-rose)">*</span></label>
                        <div style="display:flex;gap:4px">
                            <input type="text" name="household_no" id="householdNo" class="ds-input <?= isset($validationErrors['household_no']) ? 'ds-input-invalid' : '' ?>" value="<?= old('household_no', $generatedHouseholdNo ?? '') ?>" placeholder="e.g., HH-2024-001" style="flex:1">
                            <button type="button" class="ds-action-btn ab-blue" id="generateHouseholdNo" title="Generate"><i class="fas fa-sync-alt"></i></button>
                            <button type="button" class="ds-action-btn" id="checkHouseholdNo" title="Check" style="background:var(--c-teal-bg);color:var(--c-teal)"><i class="fas fa-check"></i></button>
                        </div>
                        <?php if (isset($validationErrors['household_no'])): ?>
                            <div class="ds-error-feedback"><i class="fas fa-exclamation-circle"></i> <?= esc($validationErrors['household_no']) ?></div>
                        <?php else: ?>
                        <div style="font-size:10px;color:var(--ink-soft);margin-top:4px" id="householdNoFeedback">Auto-generated unique number</div>
                        <?php endif; ?>
                    </div>
                    <div>
                        <label class="ds-input-label">Purok / Sitio <span style="color:var(--c-rose)">*</span></label>
                        <select name="sitio" id="sitioSelect" class="ds-select <?= isset($validationErrors['sitio']) ? 'ds-select-invalid' : '' ?>" required>
                            <option value="">Select Purok</option>
                            <?php foreach ($purokList as $s): ?>
                                <option value="<?= $s ?>" <?= old('sitio') == $s ? 'selected' : '' ?>><?= $s ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($validationErrors['sitio'])): ?>
                            <div class="ds-error-feedback"><i class="fas fa-exclamation-circle"></i> <?= esc($validationErrors['sitio']) ?></div>
                        <?php endif; ?>
                    </div>
                    <div>
                        <label class="ds-input-label">House Type</label>
                        <select name="house_type" class="ds-select">
                            <option value="">Select Type</option>
                            <?php foreach (['Concrete','Semi-Concrete','Wood','Light Materials'] as $ht): ?>
                                <option value="<?= $ht ?>" <?= old('house_type') == $ht ? 'selected' : '' ?>><?= $ht ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- ADDRESS -->
        <div class="ds-card" style="margin-bottom:14px">
            <div class="ds-card-head"><div class="ds-card-title"><i class="fas fa-map-marker-alt"></i> Address Information</div></div>
            <div class="ds-card-body">
                <input type="hidden" name="address" id="completeAddress" value="<?= old('address') ?>">
                <label class="ds-input-label">Street Address</label>
                <input type="text" name="street_address" id="streetAddress" class="ds-input" value="<?= old('street_address') ?>" placeholder="e.g., Block 1, Lot 2, House #12">
                <div style="font-size:10px;color:var(--ink-soft);margin-top:4px">Enter specific house details. Full address auto-generated.</div>
            </div>
        </div>

        <!-- HEAD OF HOUSEHOLD -->
        <div class="ds-card" style="margin-bottom:14px">
            <div class="ds-card-head"><div class="ds-card-title"><i class="fas fa-user-tie"></i> Head of Household</div></div>
            <div class="ds-card-body">
                <div style="max-width:400px">
                    <label class="ds-input-label">Select Head Resident</label>
                    <select name="head_resident_id" id="headResidentSelect" class="ds-select" disabled>
                        <option value="">Select Purok/Sitio first</option>
                    </select>
                    <div style="font-size:10px;color:var(--ink-soft);margin-top:4px">Shows residents from selected purok.</div>
                </div>
                <div id="loadingAlert" style="display:none;margin-top:10px;padding:8px 12px;background:var(--c-blue-bg);color:var(--c-blue);border-radius:var(--r-sm);font-size:11px">
                    <i class="fas fa-spinner fa-spin" style="margin-right:6px"></i> Loading residents…
                </div>
                <div id="noResidentsAlert" style="display:none;margin-top:10px;padding:8px 12px;background:var(--c-amber-bg);color:var(--c-amber);border-radius:var(--r-sm);font-size:11px">
                    <i class="fas fa-exclamation-triangle" style="margin-right:6px"></i> No residents found. <a href="<?= base_url('resident/create') ?>" style="color:var(--c-blue);font-weight:700">Add one →</a>
                </div>
            </div>
        </div>

        <!-- HOUSEHOLD MEMBERS -->
        <div class="ds-card" style="margin-bottom:14px">
            <div class="ds-card-head">
                <div class="ds-card-title"><i class="fas fa-users"></i> Household Members</div>
                <div style="display:flex;gap:8px;align-items:center">
                    <span class="ds-badge ds-badge-blue" id="selectedCount">0 selected</span>
                    <button type="button" class="ds-btn ds-btn-ghost" id="toggleAllMembers" style="height:28px;font-size:10px" disabled><i class="fas fa-check-double"></i> Toggle All</button>
                </div>
            </div>
            <div class="ds-card-body p0">
                <div id="membersLoadingAlert" style="display:none;padding:14px;color:var(--c-blue);font-size:11px"><i class="fas fa-spinner fa-spin" style="margin-right:6px"></i> Loading…</div>
                <div id="noResidentsWarning" style="display:none;padding:14px;color:var(--c-amber);font-size:11px"><i class="fas fa-exclamation-triangle" style="margin-right:6px"></i> No residents found. <a href="<?= base_url('resident/create') ?>" style="color:var(--c-blue)">Add one →</a></div>
                <div id="membersTableContainer" style="display:none">
                    <div style="overflow-x:auto">
                        <table class="ds-table" id="membersTable">
                            <thead><tr><th style="width:40px"><input type="checkbox" id="selectAllCheckbox" style="accent-color:var(--c-teal)"></th><th>Resident Name</th><th style="width:250px">Relationship to Head</th></tr></thead>
                            <tbody id="membersTableBody"></tbody>
                        </table>
                    </div>
                </div>
                <div id="emptyMembersState" style="text-align:center;padding:32px;color:var(--ink-soft)">
                    <i class="fas fa-users" style="font-size:24px;opacity:.3;display:block;margin-bottom:8px"></i>
                    <div style="font-weight:700;color:var(--ink);margin-bottom:4px">No Purok Selected</div>
                    <div style="font-size:11px">Select a Purok above to load residents</div>
                </div>
            </div>
            <div style="padding:10px 18px;border-top:.5px solid var(--border);font-size:10px;color:var(--ink-soft)">
                <i class="fas fa-info-circle" style="margin-right:4px;color:var(--c-blue)"></i> Check residents to add them. Set their relationship to the head.
            </div>
        </div>

        <input type="hidden" name="household_members_data" id="householdMembersData" value="[]">

        <div style="display:flex;justify-content:space-between;margin-bottom:24px">
            <a href="<?= base_url('households') ?>" class="ds-btn ds-btn-ghost"><i class="fas fa-arrow-left"></i> Cancel</a>
            <button type="submit" class="ds-btn ds-btn-primary" id="submitBtn"><i class="fas fa-save"></i> Save Household</button>
        </div>
    </form>
</div>

<div id="js-variables" style="display:none;" data-base-url="<?= base_url() ?>" data-csrf-token="<?= csrf_token() ?>" data-csrf-hash="<?= csrf_hash() ?>" data-old-household-no="<?= old('household_no') ?>"></div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('js/households/household-create.js') ?>"></script>
<?= $this->endSection() ?>
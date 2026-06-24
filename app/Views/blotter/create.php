<?= $this->extend('theme/template') ?>
<?= $this->section('content') ?>

<style>
/* ── Party card role colors ── */
.party-entry { border-radius:var(--r-sm);margin-bottom:12px;border:1.5px solid var(--border);background:var(--white);transition:border-color .15s,box-shadow .15s;overflow:hidden }
.party-entry:hover { box-shadow:0 4px 14px rgba(0,0,0,.06) }
.party-entry.role-complainant { border-left:4px solid var(--c-blue) }
.party-entry.role-respondent  { border-left:4px solid var(--c-rose) }
.party-entry.role-witness     { border-left:4px solid var(--c-amber) }

/* ── Party header strip ── */
.party-header { display:flex;align-items:center;justify-content:space-between;padding:10px 14px;border-bottom:1px solid var(--border);background:var(--bg) }
.party-role-badge { display:inline-flex;align-items:center;gap:6px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.04em }
.party-role-badge.complainant { color:var(--c-blue) }
.party-role-badge.respondent  { color:var(--c-rose) }
.party-role-badge.witness     { color:var(--c-amber) }
.party-role-dot { width:8px;height:8px;border-radius:50% }
.party-role-dot.complainant { background:var(--c-blue) }
.party-role-dot.respondent  { background:var(--c-rose) }
.party-role-dot.witness     { background:var(--c-amber) }

/* ── Type pill toggle ── */
.type-pill-group { display:flex;gap:4px;background:var(--bg);border-radius:20px;padding:3px }
.type-pill { padding:4px 14px;border-radius:16px;font-size:10.5px;font-weight:700;cursor:pointer;border:none;background:transparent;color:var(--ink-muted);transition:all .15s;white-space:nowrap }
.type-pill.active-resident { background:var(--c-blue);color:#fff;box-shadow:0 2px 6px rgba(var(--c-blue-rgb),.35) }
.type-pill.active-outsider  { background:var(--c-amber);color:#fff;box-shadow:0 2px 6px rgba(var(--c-amber-rgb),.35) }

/* ── Resident search result item ── */
.ts-dropdown .option { display:flex;align-items:center;gap:8px;padding:8px 12px }
.ts-dropdown .option .res-avatar { width:28px;height:28px;border-radius:50%;background:var(--c-blue-bg);color:var(--c-blue);display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;flex-shrink:0 }

/* ── Validation error state ── */
.party-entry.has-error { border-color:var(--c-rose) }
.field-error { font-size:10px;color:var(--c-rose);margin-top:3px;display:none }
.field-error.show { display:block }

/* ── Party count badge ── */
.party-count-pill { display:inline-flex;align-items:center;gap:6px;font-size:11px;font-weight:700;color:var(--ink-muted) }
.party-count-pill span { background:var(--c-blue-bg);color:var(--c-blue);border-radius:10px;padding:1px 7px;font-size:10px }
</style>

<div class="bmis-content">

    <!-- Page Header -->
    <div class="bmis-page-header" style="margin-bottom:18px">
        <div class="bmis-page-title">
            <h1 style="font-weight:800"><i class="fas fa-gavel" style="color:var(--c-rose);margin-right:8px"></i>File New Blotter Case</h1>
            <p style="color:var(--ink-muted)">Record an incident and the parties involved.</p>
        </div>
        <div class="bmis-page-actions">
            <a href="<?= base_url('blotter') ?>" class="ds-btn ds-btn-ghost"><i class="fas fa-arrow-left"></i> Back to Records</a>
        </div>
    </div>

    <!-- Flash errors -->
    <?php if (session()->getFlashdata('errors')): ?>
    <div style="background:var(--c-rose-bg);color:var(--c-rose);padding:12px 16px;border-radius:var(--r-sm);margin-bottom:14px;font-size:12px;font-weight:600">
        <i class="fas fa-exclamation-circle" style="margin-right:6px"></i> Please fix the following:
        <ul style="margin:6px 0 0 16px;padding:0">
            <?php foreach ((array) session()->getFlashdata('errors') as $e): ?>
                <li><?= esc($e) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
    <div style="background:var(--c-rose-bg);color:var(--c-rose);padding:12px 16px;border-radius:var(--r-sm);margin-bottom:14px;font-size:12px;font-weight:600">
        <i class="fas fa-exclamation-circle" style="margin-right:6px"></i> <?= esc(session()->getFlashdata('error')) ?>
    </div>
    <?php endif; ?>

    <form action="<?= base_url('blotter/store') ?>" method="POST" id="blotter-form">
        <?= csrf_field() ?>

        <!-- ── Incident Details ── -->
        <div class="ds-card" style="margin-bottom:14px">
            <div class="ds-card-head">
                <div class="ds-card-title"><i class="fas fa-info-circle"></i> Incident Details</div>
            </div>
            <div class="ds-card-body">
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px">
                    <div>
                        <label class="ds-input-label">Incident Type <span style="color:var(--c-rose)">*</span></label>
                        <select name="incident_type" class="ds-select" required>
                            <option value="">Select Type</option>
                            <?php foreach (['Physical Violence','Oral Defamation','Property Damage','Disturbance','Land Dispute','Others'] as $t): ?>
                                <option value="<?= $t ?>" <?= old('incident_type') == $t ? 'selected' : '' ?>><?= $t ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="ds-input-label">Date of Incident <span style="color:var(--c-rose)">*</span></label>
                        <input type="date" name="incident_date" class="ds-input" required max="<?= date('Y-m-d') ?>" value="<?= old('incident_date') ?>">
                    </div>
                    <div>
                        <label class="ds-input-label">Purok / Sitio</label>
                        <select name="purok" class="ds-select">
                            <option value="">Select Purok</option>
                            <?php foreach ($purokList as $p): ?>
                                <option value="<?= $p ?>" <?= old('purok') == $p ? 'selected' : '' ?>><?= $p ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div style="margin-top:14px">
                    <label class="ds-input-label">Specific Location</label>
                    <input type="text" name="incident_location" class="ds-input" placeholder="e.g., Near Chapel, Basketball Court..." value="<?= old('incident_location') ?>">
                </div>
                <div style="margin-top:14px">
                    <label class="ds-input-label">Narrative <span style="color:var(--c-rose)">*</span></label>
                    <textarea name="details" class="ds-input" rows="5" required placeholder="Describe what happened..." style="resize:vertical"><?= old('details') ?></textarea>
                </div>
            </div>
        </div>

        <!-- ── Involved Parties ── -->
        <div class="ds-card" style="margin-bottom:14px">
            <div class="ds-card-head">
                <div class="ds-card-title">
                    <i class="fas fa-users"></i> Involved Parties
                    <span class="party-count-pill" style="margin-left:10px" id="party-count-display">
                        <span id="party-count-num">2</span> added
                    </span>
                </div>
                <button type="button" class="ds-btn ds-btn-teal" id="add-party-btn" style="height:32px;font-size:11px;border-radius:16px;padding:0 14px">
                    <i class="fas fa-plus"></i> Add Party
                </button>
            </div>

            <!-- Legend -->
            <div style="padding:8px 18px;border-bottom:1px solid var(--border);display:flex;gap:16px;background:var(--bg)">
                <span style="font-size:10.5px;color:var(--ink-muted);display:flex;align-items:center;gap:5px"><span style="width:10px;height:10px;border-radius:2px;background:var(--c-blue);display:inline-block"></span> Complainant — person filing the complaint</span>
                <span style="font-size:10.5px;color:var(--ink-muted);display:flex;align-items:center;gap:5px"><span style="width:10px;height:10px;border-radius:2px;background:var(--c-rose);display:inline-block"></span> Respondent — person being complained against</span>
                <span style="font-size:10.5px;color:var(--ink-muted);display:flex;align-items:center;gap:5px"><span style="width:10px;height:10px;border-radius:2px;background:var(--c-amber);display:inline-block"></span> Witness — third-party observer</span>
            </div>

            <div class="ds-card-body" id="parties-container" style="padding:14px">
                <?php
                $oldParties = old('parties') ?? [];
                if (empty($oldParties)) {
                    $oldParties = [
                        ['role' => 'complainant', 'type' => 'outsider', 'outsider_name' => '', 'outsider_address' => ''],
                        ['role' => 'respondent',  'type' => 'outsider', 'outsider_name' => '', 'outsider_address' => '']
                    ];
                }
                foreach ($oldParties as $index => $p):
                    $role = $p['role'] ?? 'complainant';
                    $type = $p['type'] ?? 'outsider';
                    $roleIcons = ['complainant' => 'fa-user-edit', 'respondent' => 'fa-user-alt-slash', 'witness' => 'fa-eye'];
                    $roleLabels = ['complainant' => 'Complainant', 'respondent' => 'Respondent', 'witness' => 'Witness'];
                ?>
                <div class="party-entry role-<?= $role ?>" data-index="<?= $index ?>">
                    <!-- Party Header -->
                    <div class="party-header">
                        <div style="display:flex;align-items:center;gap:10px">
                            <span class="party-role-badge <?= $role ?>">
                                <span class="party-role-dot <?= $role ?>"></span>
                                <i class="fas <?= $roleIcons[$role] ?? 'fa-user' ?>"></i>
                                <span class="role-label"><?= $roleLabels[$role] ?? ucfirst($role) ?></span>
                            </span>
                            <select name="parties[<?= $index ?>][role]" class="role-select ds-select" style="height:28px;font-size:11px;padding:0 24px 0 8px;width:auto;border-radius:6px" required>
                                <option value="complainant" <?= $role == 'complainant' ? 'selected' : '' ?>>Complainant</option>
                                <option value="respondent"  <?= $role == 'respondent'  ? 'selected' : '' ?>>Respondent</option>
                                <option value="witness"     <?= $role == 'witness'     ? 'selected' : '' ?>>Witness</option>
                            </select>
                        </div>
                        <div style="display:flex;align-items:center;gap:8px">
                            <!-- Type pill toggle -->
                            <div class="type-pill-group">
                                <button type="button" class="type-pill <?= $type === 'resident' ? 'active-resident' : '' ?>" data-type="resident">
                                    <i class="fas fa-id-card" style="margin-right:4px"></i>Resident
                                </button>
                                <button type="button" class="type-pill <?= $type === 'outsider' ? 'active-outsider' : '' ?>" data-type="outsider">
                                    <i class="fas fa-user-slash" style="margin-right:4px"></i>Outsider
                                </button>
                            </div>
                            <input type="hidden" name="parties[<?= $index ?>][type]" class="type-hidden" value="<?= $type ?>">
                            <!-- Remove button (disabled for first 2) -->
                            <button type="button" class="ds-action-btn ab-rose remove-party" <?= $index < 2 ? 'disabled style="opacity:.3;cursor:not-allowed"' : '' ?> title="Remove party">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Party Body -->
                    <div style="padding:14px">
                        <!-- Resident search -->
                        <div class="resident-fields" style="<?= $type === 'resident' ? '' : 'display:none' ?>">
                            <label class="ds-input-label">Search Resident <span style="color:var(--c-rose)">*</span></label>
                            <select name="parties[<?= $index ?>][resident_id]" class="resident-select ds-select" style="width:100%">
                                <?php if (!empty($p['resident_id'])): ?>
                                    <option value="<?= $p['resident_id'] ?>" selected><?= esc($p['resident_name_display'] ?? '') ?></option>
                                <?php endif; ?>
                            </select>
                            <div class="field-error" id="err-resident-<?= $index ?>">Please select a resident.</div>
                        </div>

                        <!-- Outsider fields -->
                        <div class="outsider-fields" style="<?= $type === 'outsider' ? '' : 'display:none' ?>">
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                                <div>
                                    <label class="ds-input-label">Full Name <span style="color:var(--c-rose)">*</span></label>
                                    <input type="text" name="parties[<?= $index ?>][outsider_name]" class="ds-input outsider-name-input" placeholder="e.g. Juan Dela Cruz" value="<?= esc($p['outsider_name'] ?? '') ?>">
                                    <div class="field-error" id="err-name-<?= $index ?>">Name is required.</div>
                                </div>
                                <div>
                                    <label class="ds-input-label">Address</label>
                                    <input type="text" name="parties[<?= $index ?>][outsider_address]" class="ds-input" placeholder="e.g. Purok Masagana" value="<?= esc($p['outsider_address'] ?? '') ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Validation summary -->
            <div id="party-validation-msg" style="display:none;margin:0 14px 14px;padding:10px 14px;background:var(--c-rose-bg);color:var(--c-rose);border-radius:var(--r-sm);font-size:12px;font-weight:600">
                <i class="fas fa-exclamation-circle" style="margin-right:6px"></i>
                <span id="party-validation-text"></span>
            </div>

            <div style="padding:12px 18px;border-top:.5px solid var(--border);display:flex;align-items:center;justify-content:space-between">
                <span style="font-size:10.5px;color:var(--ink-soft)">
                    <i class="fas fa-info-circle" style="color:var(--c-blue);margin-right:4px"></i>
                    At least one <strong>Complainant</strong> and one <strong>Respondent</strong> are required.
                </span>
                <div style="display:flex;gap:8px">
                    <a href="<?= base_url('blotter') ?>" class="ds-btn ds-btn-ghost">Cancel</a>
                    <button type="submit" class="ds-btn ds-btn-primary" id="submit-btn">
                        <i class="fas fa-save"></i> Save Case
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.default.min.css">
<script>
    window.blotterConfig = {
        searchUrl:  '<?= base_url('blotter/searchResidents') ?>',
        partyIndex: <?= count($oldParties) ?>
    };
</script>
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script src="<?= base_url('js/blotter/blotter-create.js') ?>"></script>
<?= $this->endSection() ?>

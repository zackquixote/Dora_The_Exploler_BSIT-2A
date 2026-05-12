<?php
$profileImg = base_url(!empty($resident['profile_picture']) ? 'uploads/' . $resident['profile_picture'] : 'assets/img/default.png');
$currentStatus = $resident['status'] ?? 'active';
$statusBadge = ['active'=>'ds-badge-teal','inactive'=>'ds-badge-gray','deceased'=>'ds-badge-rose','transferred'=>'ds-badge-amber'];
?>
<?= $this->extend('theme/template') ?>
<?= $this->section('content') ?>

<style>
.rv-grid{display:grid;grid-template-columns:260px 1fr 240px;gap:14px}
.rv-tab-btn{padding:8px 16px;border:none;background:none;font-family:var(--font);font-size:11.5px;font-weight:600;color:var(--ink-soft);cursor:pointer;border-bottom:2px solid transparent;transition:all .15s}
.rv-tab-btn.active{color:var(--c-teal);border-bottom-color:var(--c-teal)}
.rv-tab-content{display:none}.rv-tab-content.active{display:block}
.rv-detail-grid{display:grid;grid-template-columns:1fr 1fr;gap:0}
.rv-detail-row{padding:10px 0;border-bottom:.5px solid var(--border);display:flex;flex-direction:column;gap:2px}
.rv-detail-row:last-child{border-bottom:none}
.rv-detail-lbl{font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.04em;color:var(--ink-soft)}
.rv-detail-val{font-size:12.5px;font-weight:600;color:var(--ink)}
.rv-flag{padding:14px;border-radius:var(--r-sm);display:flex;align-items:center;gap:10px}
.rv-flag.yes{background:var(--c-teal-bg)}.rv-flag.no{background:var(--bg)}
.rv-flag-dot{width:32px;height:32px;border-radius:var(--r-sm);display:flex;align-items:center;justify-content:center;font-size:13px}
.rv-flag.yes .rv-flag-dot{background:var(--c-teal);color:#fff}
.rv-flag.no .rv-flag-dot{background:#e2e8f0;color:var(--ink-soft)}
@media(max-width:1200px){.rv-grid{grid-template-columns:1fr}}
</style>

<div class="bmis-content">

    <?php if (session()->getFlashdata('success')): ?>
        <div style="background:var(--c-teal-bg);color:var(--c-teal);padding:10px 16px;border-radius:var(--r-sm);margin-bottom:14px;font-size:12px;font-weight:600;display:flex;align-items:center;gap:8px">
            <i class="fas fa-check-circle"></i> <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>

    <!-- Premium Page Header -->
    <div class="bmis-page-header">
        <div class="bmis-page-title">
            <h1 style="font-weight: 800;"><i class="fas fa-user text-primary"></i> Resident Profile</h1>
            <p>Detailed records for: <strong style="color:var(--ink)"><?= esc($resident['first_name']) ?> <?= esc($resident['last_name']) ?></strong></p>
        </div>
        <div class="bmis-page-actions">
            <a href="<?= base_url('resident') ?>" class="btn btn-light btn-sm rounded-pill px-3 fw-bold shadow-sm" style="border: 1px solid var(--border);"><i class="fas fa-arrow-left me-2"></i> Back to Directory</a>
            <a href="<?= base_url('resident/edit/'.$resident['id']) ?>" class="btn btn-light btn-sm rounded-pill px-3 fw-bold shadow-sm" style="border: 1px solid var(--border);"><i class="fas fa-edit me-2"></i> Edit Profile</a>
        </div>
    </div>

    <!-- ── THREE-COLUMN GRID ── -->
    <div class="rv-grid">

        <!-- LEFT: PROFILE CARD -->
        <div>
            <div class="ds-card" style="text-align:center">
                <div class="ds-card-body" style="padding:24px 18px">
                    <!-- Avatar -->
                    <div style="position:relative;display:inline-block;margin-bottom:12px">
                        <img src="<?= $profileImg ?>" style="width:72px;height:72px;border-radius:50%;object-fit:cover;border:3px solid var(--c-teal-bg)">
                        <?php if ($currentStatus === 'active'): ?>
                            <span style="position:absolute;bottom:2px;right:2px;width:14px;height:14px;border-radius:50%;background:var(--c-teal);border:2.5px solid var(--white)"></span>
                        <?php endif; ?>
                    </div>
                    <div class="font-serif" style="font-size:18px;font-weight:700;color:var(--ink);margin-bottom:2px;letter-spacing:-0.01em;line-height:1.2;"><?= esc($resident['first_name']) ?> <?= esc($resident['last_name']) ?></div>
                    <div style="font-size:11px;color:var(--ink-muted);margin-bottom:10px"><?= ucfirst(esc($resident['civil_status'] ?? 'N/A')) ?> · <?= ucfirst(esc($resident['sex'])) ?></div>

                    <!-- Mini Stats -->
                    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:6px;margin-bottom:16px;padding:10px;background:var(--bg);border-radius:var(--r-sm)">
                        <div><div style="font-size:16px;font-weight:800;color:var(--ink)"><?= esc($resident['age'] ?? '—') ?></div><div style="font-size:9px;font-weight:700;text-transform:uppercase;color:var(--ink-soft)">Age</div></div>
                        <div><div style="font-size:16px;font-weight:800;color:var(--ink)"><?= !empty($resident['is_voter']) ? 'Yes' : 'No' ?></div><div style="font-size:9px;font-weight:700;text-transform:uppercase;color:var(--ink-soft)">Voter</div></div>
                        <div><div style="font-size:11px;font-weight:700;color:var(--ink);font-family:var(--mono)"><?= !empty($resident['household_no']) ? esc($resident['household_no']) : '—' ?></div><div style="font-size:9px;font-weight:700;text-transform:uppercase;color:var(--ink-soft)">HH No.</div></div>
                    </div>

                    <!-- Info Rows -->
                    <div style="text-align:left">
                        <?php
                        $infoRows = [
                            ['icon'=>'fa-phone','label'=>'Contact','val'=>$resident['contact_number']??'N/A'],
                            ['icon'=>'fa-briefcase','label'=>'Occupation','val'=>$resident['occupation']??'N/A'],
                            ['icon'=>'fa-flag','label'=>'Citizenship','val'=>$resident['citizenship']??'N/A'],
                        ];
                        foreach ($infoRows as $ir): ?>
                        <div style="display:flex;align-items:center;justify-content:space-between;padding:8px 0;border-bottom:.5px solid var(--border)">
                            <span style="font-size:11px;color:var(--ink-muted);display:flex;align-items:center;gap:6px"><i class="fas <?= $ir['icon'] ?>" style="width:12px;text-align:center"></i> <?= $ir['label'] ?></span>
                            <span style="font-size:11.5px;font-weight:600;color:var(--ink)"><?= esc($ir['val']) ?></span>
                        </div>
                        <?php endforeach; ?>

                        <!-- Status -->
                        <div style="display:flex;align-items:center;justify-content:space-between;padding:8px 0">
                            <span style="font-size:11px;color:var(--ink-muted);display:flex;align-items:center;gap:6px"><i class="fas fa-circle" style="width:12px;text-align:center"></i> Status</span>
                            <span id="status-container">
                                <span id="status-display" style="display:flex;align-items:center;gap:6px">
                                    <span class="ds-badge <?= $statusBadge[$currentStatus] ?? 'ds-badge-gray' ?>" id="status-badge"><?= ucfirst($currentStatus) ?></span>
                                    <i class="fas fa-pencil-alt" id="edit-status-icon" style="cursor:pointer;font-size:10px;color:var(--ink-soft)" title="Change"></i>
                                </span>
                                <span id="status-editor" style="display:none;align-items:center;gap:4px">
                                    <select id="status-select" class="ds-select" style="height:28px;font-size:11px;padding:0 8px">
                                        <option value="active" <?= $currentStatus=='active'?'selected':'' ?>>Active</option>
                                        <option value="inactive" <?= $currentStatus=='inactive'?'selected':'' ?>>Inactive</option>
                                        <option value="deceased" <?= $currentStatus=='deceased'?'selected':'' ?>>Deceased</option>
                                        <option value="transferred" <?= $currentStatus=='transferred'?'selected':'' ?>>Transferred</option>
                                    </select>
                                    <i class="fas fa-check" id="save-status-icon" style="cursor:pointer;font-size:11px;color:var(--c-teal)" title="Save"></i>
                                    <i class="fas fa-times" id="cancel-status-icon" style="cursor:pointer;font-size:11px;color:var(--c-rose)" title="Cancel"></i>
                                </span>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div style="border-top:.5px solid var(--border);padding:12px 18px;display:flex;flex-direction:column;gap:6px">
                    <a href="#" onclick="printProfile();return false;" class="ds-quick-btn qb-blue" style="justify-content:center;width:100%"><i class="fas fa-print"></i> Print Profile</a>
                    <a href="#" onclick="generateCertificate();return false;" class="ds-quick-btn qb-violet" style="justify-content:center;width:100%"><i class="fas fa-file-alt"></i> Generate Certificate</a>
                    <a href="<?= base_url('resident') ?>" class="ds-quick-btn qb-slate" style="justify-content:center;width:100%"><i class="fas fa-arrow-left"></i> Back to List</a>
                </div>
            </div>
        </div>

        <!-- CENTER: TABS -->
        <div>
            <div class="ds-card">
                <div style="padding:0 18px;border-bottom:.5px solid var(--border);display:flex;gap:0;overflow-x:auto">
                    <button class="rv-tab-btn active" onclick="switchTab('personal',this)"><i class="fas fa-user" style="margin-right:4px"></i> Personal</button>
                    <button class="rv-tab-btn" onclick="switchTab('household',this)"><i class="fas fa-home" style="margin-right:4px"></i> Household</button>
                    <button class="rv-tab-btn" onclick="switchTab('status',this)"><i class="fas fa-flag" style="margin-right:4px"></i> Status</button>
                    <button class="rv-tab-btn" onclick="switchTab('documents',this)"><i class="fas fa-file-alt" style="margin-right:4px"></i> Documents</button>
                    <button class="rv-tab-btn" onclick="switchTab('cases',this)"><i class="fas fa-balance-scale" style="margin-right:4px"></i> Cases</button>
                    <button class="rv-tab-btn" onclick="switchTab('transfers',this)"><i class="fas fa-exchange-alt" style="margin-right:4px"></i> Transfers <?php if(!empty($transferHistory)): ?><span class="ds-badge ds-badge-blue" style="font-size:9px;margin-left:3px"><?= count($transferHistory) ?></span><?php endif; ?></button>
                </div>
                <div class="ds-card-body">

                    <!-- PERSONAL TAB -->
                    <div id="personal" class="rv-tab-content active">
                        <div class="ds-section-label" style="margin-top:0">Personal Information</div>
                        <div class="rv-detail-grid">
                            <?php
                            $details = [
                                ['First Name', $resident['first_name']],
                                ['Middle Name', $resident['middle_name'] ?? '—'],
                                ['Last Name', $resident['last_name']],
                                ['Birthdate', date('F d, Y', strtotime($resident['birthdate']))],
                                ['Sex', ucfirst($resident['sex'])],
                                ['Civil Status', $resident['civil_status'] ?? '—'],
                                ['Occupation', $resident['occupation'] ?? '—'],
                                ['Citizenship', $resident['citizenship'] ?? '—'],
                                ['Contact No.', $resident['contact_number'] ?? '—'],
                                ['Registered On', date('M d, Y', strtotime($resident['created_at']))],
                            ];
                            foreach ($details as $d): ?>
                            <div class="rv-detail-row">
                                <div class="rv-detail-lbl"><?= $d[0] ?></div>
                                <div class="rv-detail-val"><?= esc($d[1]) ?></div>
                            </div>
                            <?php endforeach; ?>
                            <div class="rv-detail-row" style="grid-column:1/-1">
                                <div class="rv-detail-lbl">Last Updated</div>
                                <div class="rv-detail-val">
                                    <?php if (!empty($resident['updated_at'])): ?>
                                        <?= date('M d, Y · h:i A', strtotime($resident['updated_at'])) ?>
                                    <?php else: ?>
                                        —
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- HOUSEHOLD TAB -->
                    <div id="household" class="rv-tab-content">
                        <div class="ds-section-label" style="margin-top:0">Household Information</div>
                        <div style="display:flex;flex-direction:column;gap:12px">
                            <div class="ds-mini">
                                <div class="ds-mini-icon ic-teal"><i class="fas fa-hashtag"></i></div>
                                <div>
                                    <div class="rv-detail-lbl">Household Number</div>
                                    <?php if (!empty($resident['household_no'])): ?>
                                        <div style="display:flex;align-items:center;gap:8px;margin-top:4px">
                                            <span class="ds-badge ds-badge-teal" style="font-size:11px"><?= esc($resident['household_no']) ?></span>
                                            <a href="<?= base_url('households/view/' . $resident['household_id']) ?>" class="ds-btn ds-btn-ghost" style="height:26px;font-size:10px;padding:0 10px"><i class="fas fa-external-link-alt"></i> View</a>
                                        </div>
                                    <?php else: ?>
                                        <span class="ds-badge ds-badge-gray" style="margin-top:4px">Not Assigned</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="ds-mini">
                                <div class="ds-mini-icon ic-rose"><i class="fas fa-map-marker-alt"></i></div>
                                <div><div class="rv-detail-lbl">Address</div><div class="rv-detail-val" style="margin-top:4px"><?= esc($resident['household_address'] ?? 'No address on file') ?></div></div>
                            </div>
                            <div class="ds-mini">
                                <div class="ds-mini-icon ic-violet"><i class="fas fa-layer-group"></i></div>
                                <div><div class="rv-detail-lbl">Sitio / Zone</div><span class="ds-badge ds-badge-blue" style="margin-top:4px"><?= esc($resident['sitio'] ?? 'Unassigned') ?></span></div>
                            </div>
                        </div>
                    </div>

                    <!-- STATUS & FLAGS TAB -->
                    <div id="status" class="rv-tab-content">
                        <div class="ds-section-label" style="margin-top:0">Classification Flags</div>
                        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px">
                            <?php
                            $flags = [
                                ['key'=>'is_voter','label'=>'Registered Voter','icon'=>'fa-vote-yea','color'=>'blue'],
                                ['key'=>'is_senior_citizen','label'=>'Senior Citizen','icon'=>'fa-user-graduate','color'=>'green'],
                                ['key'=>'is_pwd','label'=>'PWD','icon'=>'fa-wheelchair','color'=>'amber'],
                            ];
                            foreach ($flags as $f):
                                $yes = !empty($resident[$f['key']]);
                            ?>
                            <div class="rv-flag <?= $yes ? 'yes' : 'no' ?>">
                                <div class="rv-flag-dot"><i class="fas <?= $f['icon'] ?>"></i></div>
                                <div>
                                    <div style="font-size:12px;font-weight:700;color:var(--ink)"><?= $f['label'] ?></div>
                                    <div style="font-size:10px;font-weight:700;color:<?= $yes ? 'var(--c-teal)' : 'var(--ink-soft)' ?>"><?= $yes ? '✓ Yes' : '— No' ?></div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- DOCUMENTS TAB -->
                    <div id="documents" class="rv-tab-content">
                        <div class="ds-section-label" style="margin-top:0;display:flex;justify-content:space-between;align-items:center">
                            <span>Document History</span>
                            <span class="ds-badge ds-badge-violet"><?= count($certificates ?? []) ?> issued</span>
                        </div>
                        <?php if(!empty($certificates)): ?>
                            <div style="display:flex;flex-direction:column;gap:8px">
                                <?php foreach($certificates as $cert): ?>
                                    <div style="padding:12px;border:1px solid var(--border);border-radius:var(--r-sm);display:flex;justify-content:space-between;align-items:center">
                                        <div>
                                            <div style="font-weight:700;color:var(--ink);font-size:12px"><?= esc($cert['certificate_type']) ?></div>
                                            <div style="font-size:10px;color:var(--ink-soft);margin-top:2px">
                                                <i class="fas fa-hashtag" style="margin-right:3px"></i><?= esc($cert['certificate_number']) ?> · 
                                                <i class="fas fa-calendar" style="margin-right:3px"></i><?= date('M d, Y', strtotime($cert['created_at'])) ?>
                                            </div>
                                            <div style="font-size:11px;color:var(--ink-muted);margin-top:4px;font-style:italic">"<?= esc($cert['purpose']) ?>"</div>
                                        </div>
                                        <a href="<?= base_url('certificate/print/' . $cert['id']) ?>" target="_blank" class="ds-btn ds-btn-ghost" style="height:28px;padding:0 10px;font-size:10px;color:var(--c-violet)"><i class="fas fa-print"></i> Reprint</a>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div style="text-align:center;padding:30px;color:var(--ink-soft);font-size:12px">
                                <i class="fas fa-folder-open" style="font-size:24px;opacity:0.3;display:block;margin-bottom:8px"></i>
                                No documents issued yet.
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- CASES TAB -->
                    <div id="cases" class="rv-tab-content">
                        <div class="ds-section-label" style="margin-top:0;display:flex;justify-content:space-between;align-items:center">
                            <span>Blotter History</span>
                            <span class="ds-badge ds-badge-rose"><?= count($blotterHistory ?? []) ?> records</span>
                        </div>
                        <?php if(!empty($blotterHistory)): ?>
                            <div style="display:flex;flex-direction:column;gap:8px">
                                <?php foreach($blotterHistory as $blotter): 
                                    $roleColor = $blotter['role'] == 'complainant' ? 'c-blue' : ($blotter['role'] == 'respondent' ? 'c-rose' : 'c-amber');
                                    $statusColor = strtolower($blotter['status']) == 'settled' ? 'c-teal' : 'c-amber';
                                ?>
                                    <div style="padding:12px;border:1px solid var(--border);border-left:3px solid var(--<?= $roleColor ?>);border-radius:var(--r-sm);display:flex;justify-content:space-between;align-items:center">
                                        <div>
                                            <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px">
                                                <span style="font-weight:700;color:var(--ink);font-size:12px"><?= esc($blotter['case_number']) ?></span>
                                                <span class="ds-badge" style="background:var(--<?= $roleColor ?>-bg);color:var(--<?= $roleColor ?>);font-size:9px;padding:2px 6px"><?= ucfirst(esc($blotter['role'])) ?></span>
                                            </div>
                                            <div style="font-size:11px;color:var(--ink);font-weight:600"><?= esc($blotter['incident_type']) ?></div>
                                            <div style="font-size:10px;color:var(--ink-soft);margin-top:2px">
                                                <i class="fas fa-calendar" style="margin-right:3px"></i><?= date('M d, Y', strtotime($blotter['incident_date'])) ?>
                                            </div>
                                        </div>
                                        <div style="text-align:right">
                                            <span style="font-size:10px;font-weight:700;color:var(--<?= $statusColor ?>);text-transform:uppercase;display:block;margin-bottom:4px"><i class="fas fa-circle" style="font-size:6px;vertical-align:middle;margin-right:3px"></i><?= esc($blotter['status']) ?></span>
                                            <a href="<?= base_url('blotter/view/' . $blotter['id']) ?>" class="ds-btn ds-btn-ghost" style="height:24px;padding:0 8px;font-size:10px"><i class="fas fa-folder-open"></i> View Case</a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div style="text-align:center;padding:30px;color:var(--ink-soft);font-size:12px">
                                <i class="fas fa-check-circle" style="font-size:24px;color:var(--c-teal);opacity:0.5;display:block;margin-bottom:8px"></i>
                                Clean record. No blotter cases found.
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- TRANSFER HISTORY TAB -->
                    <div id="transfers" class="rv-tab-content">
                        <div class="ds-section-label" style="margin-top:0;display:flex;justify-content:space-between;align-items:center">
                            <span>Household Transfer History</span>
                            <span class="ds-badge ds-badge-blue"><?= count($transferHistory ?? []) ?> records</span>
                        </div>
                        <?php if (!empty($transferHistory)): ?>
                            <div style="position:relative;padding-left:20px">
                                <!-- Timeline line -->
                                <div style="position:absolute;left:7px;top:8px;bottom:8px;width:2px;background:var(--border)"></div>
                                <?php foreach ($transferHistory as $t): ?>
                                <div style="position:relative;margin-bottom:16px">
                                    <!-- Dot -->
                                    <div style="position:absolute;left:-20px;top:10px;width:10px;height:10px;border-radius:50%;background:var(--c-blue);border:2px solid var(--white);box-shadow:0 0 0 2px var(--c-blue-bg)"></div>
                                    <div style="padding:12px 14px;background:var(--bg);border-radius:var(--r-sm);border:.5px solid var(--border)">
                                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px">
                                            <div style="display:flex;align-items:center;gap:8px">
                                                <?php if ($t['from_household_no']): ?>
                                                    <span class="ds-badge ds-badge-rose" style="font-size:10px"><?= esc($t['from_household_no']) ?></span>
                                                    <i class="fas fa-arrow-right" style="font-size:9px;color:var(--ink-soft)"></i>
                                                <?php else: ?>
                                                    <span class="ds-badge ds-badge-gray" style="font-size:10px">No Household</span>
                                                    <i class="fas fa-arrow-right" style="font-size:9px;color:var(--ink-soft)"></i>
                                                <?php endif; ?>
                                                <?php if ($t['to_household_no']): ?>
                                                    <span class="ds-badge ds-badge-teal" style="font-size:10px"><?= esc($t['to_household_no']) ?></span>
                                                <?php else: ?>
                                                    <span class="ds-badge ds-badge-gray" style="font-size:10px">Unassigned</span>
                                                <?php endif; ?>
                                            </div>
                                            <span style="font-size:10px;color:var(--ink-soft)"><?= date('M d, Y · h:i A', strtotime($t['transferred_at'])) ?></span>
                                        </div>
                                        <?php if (!empty($t['reason'])): ?>
                                            <div style="font-size:11px;color:var(--ink-muted);font-style:italic">
                                                <i class="fas fa-comment-alt" style="margin-right:4px;opacity:.5"></i><?= esc($t['reason']) ?>
                                            </div>
                                        <?php endif; ?>
                                        <?php if (!empty($t['transferred_by_name'])): ?>
                                            <div style="font-size:10px;color:var(--ink-soft);margin-top:4px">
                                                <i class="fas fa-user" style="margin-right:3px"></i>by <?= esc($t['transferred_by_name']) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div style="text-align:center;padding:30px;color:var(--ink-soft);font-size:12px">
                                <i class="fas fa-exchange-alt" style="font-size:24px;opacity:.3;display:block;margin-bottom:8px"></i>
                                No household transfers recorded.
                            </div>
                        <?php endif; ?>
                    </div>

                </div>
            </div>
        </div>

        <!-- RIGHT: ACTIVITY PANEL -->
        <div>
            <div class="ds-card" style="height:100%">
                <div class="ds-card-head">
                    <div class="ds-card-title" style="font-size:11px"><i class="fas fa-history"></i> Activity</div>
                    <span class="ds-badge ds-badge-gray" id="rv-activity-count" style="display:none">0</span>
                </div>
                <div class="ds-card-body" style="padding:0">
                    <div class="ds-activity-feed" id="rv-activity-feed" style="padding:14px;max-height:400px">
                        <div style="text-align:center;padding:24px;color:var(--ink-soft);font-size:11px"><i class="fas fa-spinner fa-spin" style="margin-right:4px"></i> Loading activity…</div>
                    </div>
                </div>
                <div style="padding:10px 14px;border-top:.5px solid var(--border);text-align:center">
                    <a href="<?= base_url('logs') ?>" style="font-size:10.5px;font-weight:700;color:var(--c-blue);text-decoration:none"><i class="fas fa-list" style="margin-right:4px"></i> View All Logs</a>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Generate Certificate Modal -->
<div class="ds-modal-overlay" id="certModalOverlay">
    <div class="ds-modal">
        <div class="ds-modal-icon" style="background:var(--c-violet-bg);color:var(--c-violet)"><i class="fas fa-file-alt"></i></div>
        <h3>Generate Certificate</h3>
        <div class="subtitle">For <?= esc($resident['first_name'] . ' ' . $resident['last_name']) ?></div>
        <form action="<?= base_url('certificate/store') ?>" method="POST">
            <?= csrf_field() ?>
            <input type="hidden" name="resident_id" value="<?= $resident['id'] ?>">
            <div style="margin-bottom:12px">
                <label class="ds-input-label">Certificate Type</label>
                <select name="certificate_type" class="ds-select">
                    <option>Barangay Clearance</option>
                    <option>Certificate of Indigency</option>
                    <option>Certificate of Residency</option>
                    <option>Business Permit</option>
                    <option>Solo Parent</option>
                </select>
            </div>
            <div style="margin-bottom:16px">
                <label class="ds-input-label">Purpose</label>
                <input type="text" name="purpose" class="ds-input" placeholder="e.g. Employment Requirement" required>
            </div>
            <div class="ds-modal-actions">
                <button type="button" class="ds-btn ds-btn-ghost" onclick="document.getElementById('certModalOverlay').classList.remove('show')">Cancel</button>
                <button type="submit" class="ds-btn" style="background:var(--c-violet);color:#fff"><i class="fas fa-file-download"></i> Generate</button>
            </div>
        </form>
    </div>
</div>

<script>
function switchTab(id, btn) {
    document.querySelectorAll('.rv-tab-content').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.rv-tab-btn').forEach(b => b.classList.remove('active'));
    document.getElementById(id).classList.add('active');
    btn.classList.add('active');
}
function generateCertificate() {
    document.getElementById('certModalOverlay').classList.add('show');
}
document.getElementById('certModalOverlay').addEventListener('click', function(e) {
    if (e.target === this) this.classList.remove('show');
});
</script>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    window.BASE_URL         = "<?= base_url() ?>";
    window.CSRF_TOKEN_NAME  = "<?= csrf_token() ?>";
    window.CSRF_TOKEN_VALUE = "<?= csrf_hash() ?>";
    window.RESIDENT_ID      = "<?= $resident['id'] ?>";
    window.RESIDENT_NAME    = "<?= esc($resident['first_name'] . ' ' . $resident['last_name'], 'js') ?>";
    window.CURRENT_USER     = "<?= esc(session()->get('name') ?? session()->get('username') ?? 'User', 'js') ?>";
    window.CURRENT_ROLE     = "<?= esc(session()->get('role') ?? 'staff', 'js') ?>";
    window.STATUS_BADGES    = {
        active:      'ds-badge-teal',
        inactive:    'ds-badge-gray',
        deceased:    'ds-badge-rose',
        transferred: 'ds-badge-amber'
    };
</script>
<script src="<?= base_url('js/residents/residents-view.js') ?>"></script>
<?= $this->endSection() ?>
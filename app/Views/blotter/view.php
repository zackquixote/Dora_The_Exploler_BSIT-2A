<?php
$status = $case['status'];
$sc = match($status) {
    'Pending'=>'ds-badge-amber','Settled'=>'ds-badge-teal','Dismissed'=>'ds-badge-gray',
    'For Hearing'=>'ds-badge-blue','Unsettled'=>'ds-badge-rose','Referred'=>'ds-badge-violet',
    default=>'ds-badge-blue'
};
?>
<?= $this->extend('theme/template') ?>
<?= $this->section('content') ?>

<div class="bmis-content">

    <!-- HEADER BAR -->
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;padding:14px 18px;background:var(--white);border-radius:var(--r);border:.5px solid var(--border)">
        <div style="display:flex;align-items:center;gap:12px">
            <a href="<?= base_url('blotter') ?>" class="ds-action-btn ab-blue"><i class="fas fa-arrow-left"></i></a>
            <div>
                <div style="font-size:15px;font-weight:700;color:var(--ink)">Case #<?= esc($case['case_number']) ?></div>
                <div style="font-size:11px;color:var(--ink-muted);display:flex;gap:12px;margin-top:2px">
                    <span><i class="far fa-calendar-alt" style="margin-right:4px"></i><?= date('F d, Y', strtotime($case['incident_date'])) ?></span>
                    <span><i class="fas fa-map-marker-alt" style="margin-right:4px"></i><?= esc($case['purok'] ?? 'N/A') ?></span>
                </div>
            </div>
        </div>
        <div style="display:flex;gap:8px;align-items:center">
            <span class="ds-badge <?= $sc ?>"><?= esc($status) ?></span>
            <a href="<?= base_url('blotter/edit/' . $case['id']) ?>" class="ds-btn ds-btn-ghost"><i class="fas fa-edit"></i> Edit</a>
            <a href="<?= base_url('blotter/print/' . $case['id']) ?>" target="_blank" class="ds-btn" style="background:var(--c-blue);color:#fff;height:32px;font-size:11px"><i class="fas fa-print"></i> Print</a>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 280px;gap:14px">

        <!-- MAIN CONTENT -->
        <div>
            <!-- Incident Details -->
            <div class="ds-card" style="margin-bottom:14px">
                <div class="ds-card-head"><div class="ds-card-title"><i class="fas fa-file-alt"></i> Incident Report</div></div>
                <div class="ds-card-body">
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:0">
                        <?php foreach ([['Type',$case['incident_type']],['Location',$case['incident_location']??'Not specified'],['Recorded On',date('F d, Y h:i A', strtotime($case['created_at']))],['Created By',$case['created_by_name']??'System'],['Action Taken',nl2br(esc($case['action_taken']??'None recorded'))]] as $d): ?>
                        <div style="padding:10px 0;border-bottom:.5px solid var(--border)">
                            <div style="font-size:10px;font-weight:700;text-transform:uppercase;color:var(--ink-soft)"><?= $d[0] ?></div>
                            <div style="font-size:12.5px;font-weight:600;color:var(--ink);margin-top:2px"><?= $d[1] ?></div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="ds-section-label" style="margin-top:16px">Narrative</div>
                    <div style="padding:14px;background:var(--bg);border-radius:var(--r-sm);font-size:12px;color:var(--ink);white-space:pre-wrap;line-height:1.6"><?= esc($case['details']) ?></div>
                </div>
            </div>

            <!-- Involved Parties -->
            <div class="ds-card" style="margin-bottom:14px">
                <div class="ds-card-head"><div class="ds-card-title"><i class="fas fa-users"></i> Involved Parties</div></div>
                <div class="ds-card-body">
                    <?php foreach (['complainant','respondent','witness'] as $prole):
                        if (empty($parties[$prole])) continue;
                        $rc = ['complainant'=>['c-amber','fa-user-edit'],'respondent'=>['c-rose','fa-user-alt-slash'],'witness'=>['c-blue','fa-eye']];
                    ?>
                    <div class="ds-section-label"><?= ucfirst($prole) ?>s</div>
                    <?php foreach ($parties[$prole] as $p): ?>
                    <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 14px;background:var(--bg);border-radius:var(--r-sm);margin-bottom:6px">
                        <div style="display:flex;align-items:center;gap:10px">
                            <?php if (!empty($p['resident_id'])): ?>
                                <img src="<?= base_url(!empty($p['profile_picture']) ? 'uploads/' . $p['profile_picture'] : 'assets/img/default.png') ?>" 
                                     style="width:32px;height:32px;border-radius:50%;object-fit:cover;border:2px solid var(--<?= $rc[$prole][0] ?>-bg)">
                            <?php else: ?>
                                <div style="width:32px;height:32px;border-radius:50%;background:var(--<?= $rc[$prole][0] ?>-bg);color:var(--<?= $rc[$prole][0] ?>);display:flex;align-items:center;justify-content:center;font-size:11px"><i class="fas <?= $rc[$prole][1] ?>"></i></div>
                            <?php endif; ?>
                            <div>
                                <?php if (!empty($p['resident_id'])): ?>
                                    <strong style="font-size:12px;color:var(--ink)"><?= esc($p['resident_name']) ?></strong>
                                    <span style="font-size:10px;color:var(--ink-muted);margin-left:4px">Resident #<?= $p['resident_id'] ?></span>
                                <?php else: ?>
                                    <strong style="font-size:12px;color:var(--ink)"><?= esc($p['outsider_name']) ?></strong>
                                    <?php if (!empty($p['outsider_address'])): ?>
                                        <span style="font-size:10px;color:var(--ink-muted);margin-left:4px"><?= esc($p['outsider_address']) ?></span>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                        <span class="ds-badge ds-badge-<?= $rc[$prole][0] == 'c-amber' ? 'amber' : ($rc[$prole][0] == 'c-rose' ? 'rose' : 'blue') ?>"><?= ucfirst($prole) ?></span>
                    </div>
                    <?php endforeach; ?>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Hearings -->
            <div class="ds-card" style="margin-bottom:14px">
                <div class="ds-card-head">
                    <div class="ds-card-title"><i class="fas fa-gavel"></i> Hearings</div>
                    <button type="button" class="ds-btn ds-btn-teal" style="height:30px;font-size:11px" data-bs-toggle="modal" data-bs-target="#addHearingModal"><i class="fas fa-plus"></i> Add</button>
                </div>
                <div class="ds-card-body">
                    <?php if (empty($hearings)): ?>
                        <div style="text-align:center;padding:24px;color:var(--ink-soft);font-size:12px">No hearings recorded.</div>
                    <?php else: ?>
                    <div class="ds-activity-feed" style="max-height:none">
                        <?php foreach ($hearings as $h):
                            $hc = $h['status']=='Completed'?'ds-ai-create':($h['status']=='Cancelled'?'ds-ai-delete':'ds-ai-edit');
                        ?>
                        <div class="ds-activity-item">
                            <div class="ds-activity-icon <?= $hc ?>"><i class="fas fa-gavel"></i></div>
                            <div style="flex:1">
                                <div class="ds-activity-action"><?= date('M d, Y', strtotime($h['hearing_date'])) ?><?= $h['hearing_time'] ? ' · ' . date('h:i A', strtotime($h['hearing_time'])) : '' ?></div>
                                <div class="ds-activity-meta"><strong><?= esc($h['presiding_officer'] ?? 'N/A') ?></strong> — <?= esc($h['venue'] ?? 'No venue') ?></div>
                                <?php if ($h['notes']): ?><div style="font-size:11px;color:var(--ink);margin-top:4px"><?= nl2br(esc($h['notes'])) ?></div><?php endif; ?>
                                <?php if ($h['outcome']): ?><div style="font-size:11px;color:var(--c-teal);margin-top:4px;font-weight:600">Outcome: <?= esc($h['outcome']) ?></div><?php endif; ?>
                                <div style="margin-top:6px;display:flex;gap:6px">
                                    <span class="ds-badge <?= $h['status']=='Completed'?'ds-badge-teal':($h['status']=='Cancelled'?'ds-badge-rose':'ds-badge-amber') ?>"><?= $h['status'] ?></span>
                                    <a href="<?= base_url('blotter/print-summon/' . $case['id'] . '/' . $h['id']) ?>" target="_blank" class="ds-action-btn ab-violet" style="width:auto;height:22px;font-size:9px;padding:0 8px;border-radius:4px"><i class="fas fa-envelope" style="margin-right:4px"></i> Summon</a>
                                    <a href="#" class="ds-action-btn ab-blue edit-hearing" style="width:22px;height:22px;font-size:9px"
                                       data-id="<?= $h['id'] ?>" data-date="<?= $h['hearing_date'] ?>" data-time="<?= $h['hearing_time'] ?>"
                                       data-venue="<?= esc($h['venue']) ?>" data-officer="<?= esc($h['presiding_officer']) ?>"
                                       data-notes="<?= esc($h['notes']) ?>" data-outcome="<?= esc($h['outcome']) ?>" data-status="<?= $h['status'] ?>"><i class="fas fa-edit"></i></a>
                                    <a href="#" class="ds-action-btn ab-rose delete-hearing" style="width:22px;height:22px;font-size:9px" data-id="<?= $h['id'] ?>"><i class="fas fa-trash"></i></a>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Status History -->
            <div class="ds-card">
                <div class="ds-card-head"><div class="ds-card-title"><i class="fas fa-history"></i> Status History</div></div>
                <div class="ds-card-body">
                    <?php if (empty($timeline)): ?>
                        <div style="color:var(--ink-soft);font-size:12px">No status changes recorded.</div>
                    <?php else: ?>
                        <?php foreach ($timeline as $entry): ?>
                        <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:.5px solid var(--border)">
                            <div style="font-size:12px">
                                <?php if ($entry['old_status']): ?>
                                    Changed from <strong><?= esc($entry['old_status']) ?></strong> → <strong><?= esc($entry['new_status']) ?></strong>
                                <?php else: ?>
                                    Initial: <strong><?= esc($entry['new_status']) ?></strong>
                                <?php endif; ?>
                                <?php if (!empty($entry['remarks'])): ?><br><span style="font-size:10.5px;color:var(--ink-soft)"><?= esc($entry['remarks']) ?></span><?php endif; ?>
                            </div>
                            <div style="font-size:10.5px;color:var(--ink-soft);white-space:nowrap"><?= date('M d, Y H:i', strtotime($entry['created_at'])) ?></div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- SIDEBAR -->
        <div>
            <!-- Document Generation -->
            <div class="ds-card" style="margin-bottom:14px">
                <div class="ds-card-head"><div class="ds-card-title" style="font-size:11px"><i class="fas fa-file-signature"></i> Legal Documents</div></div>
                <div class="ds-card-body" style="display:flex;flex-direction:column;gap:8px">
                    <?php if(strtolower($case['status']) === 'settled'): ?>
                        <a href="<?= base_url('blotter/print-settlement/' . $case['id']) ?>" target="_blank" class="ds-btn" style="background:var(--c-teal);color:#fff;width:100%;justify-content:center"><i class="fas fa-handshake"></i> Settlement Contract</a>
                    <?php else: ?>
                        <button class="ds-btn ds-btn-ghost" style="width:100%;justify-content:center" disabled title="Case must be Settled first"><i class="fas fa-handshake"></i> Settlement Contract</button>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Quick Update -->
            <div class="ds-card" style="margin-bottom:14px">
                <div class="ds-card-head"><div class="ds-card-title" style="font-size:11px"><i class="fas fa-bolt"></i> Quick Update</div></div>
                <form action="<?= base_url('blotter/update/' . $case['id']) ?>" method="POST">
                    <?= csrf_field() ?>
                    <!-- Hidden fields to satisfy update() validation -->
                    <input type="hidden" name="_quick_update" value="1">
                    <input type="hidden" name="incident_type" value="<?= esc($case['incident_type']) ?>">
                    <input type="hidden" name="incident_date" value="<?= esc($case['incident_date']) ?>">
                    <input type="hidden" name="incident_location" value="<?= esc($case['incident_location'] ?? '') ?>">
                    <input type="hidden" name="details" value="<?= esc($case['details']) ?>">
                    <input type="hidden" name="purok" value="<?= esc($case['purok'] ?? '') ?>">
                    <div class="ds-card-body">
                        <div style="margin-bottom:10px">
                            <label class="ds-input-label">Status</label>
                            <select name="status" class="ds-select">
                                <?php foreach (['Pending','Investigating','Ongoing','For Hearing','Settled','Dismissed','Referred','Unsettled'] as $s): ?>
                                    <option value="<?= $s ?>" <?= $case['status'] == $s ? 'selected' : '' ?>><?= $s ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="ds-input-label">Action Taken</label>
                            <textarea name="action_taken" class="ds-input" rows="4" style="resize:vertical"><?= esc($case['action_taken'] ?? '') ?></textarea>
                        </div>
                    </div>
                    <div style="padding:10px 18px;border-top:.5px solid var(--border)">
                        <button type="submit" class="ds-btn ds-btn-primary" style="width:100%"><i class="fas fa-save"></i> Update</button>
                    </div>
                </form>
            </div>

            <!-- Danger Zone -->
            <div class="ds-card">
                <div class="ds-card-head"><div class="ds-card-title" style="font-size:11px;color:var(--c-rose)"><i class="fas fa-exclamation-triangle"></i> Danger Zone</div></div>
                <div class="ds-card-body">
                    <button type="button" class="ds-btn ds-btn-rose delete-btn" style="width:100%" data-id="<?= $case['id'] ?>" data-case="<?= esc($case['case_number']) ?>"><i class="fas fa-trash-alt"></i> Delete This Case</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Hearing Modal -->
<div class="modal fade" id="addHearingModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content shadow border-0" style="border-radius:var(--r)">
            <form id="hearing-form" action="<?= base_url('blotter/hearing/add/' . $case['id']) ?>" method="POST">
                <?= csrf_field() ?>
                <div style="padding:16px 20px;border-bottom:.5px solid var(--border)">
                    <h5 style="font-size:14px;font-weight:700;color:var(--ink);margin:0"><i class="fas fa-gavel" style="margin-right:6px;color:var(--c-teal)"></i> Add Hearing</h5>
                </div>
                <div style="padding:16px 20px">
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:10px">
                        <div><label class="ds-input-label">Date *</label><input type="date" name="hearing_date" class="ds-input" required></div>
                        <div><label class="ds-input-label">Time</label><input type="time" name="hearing_time" class="ds-input"></div>
                    </div>
                    <div style="margin-bottom:10px"><label class="ds-input-label">Venue</label><input type="text" name="venue" class="ds-input" placeholder="e.g., Barangay Hall"></div>
                    <div style="margin-bottom:10px"><label class="ds-input-label">Presiding Officer</label><input type="text" name="presiding_officer" class="ds-input" placeholder="Name of official"></div>
                    <div style="margin-bottom:10px"><label class="ds-input-label">Status</label>
                        <select name="status" class="ds-select"><option>Scheduled</option><option>In Progress</option><option>Completed</option><option>Cancelled</option></select>
                    </div>
                    <div><label class="ds-input-label">Notes</label><textarea name="notes" class="ds-input" rows="3" style="resize:vertical" placeholder="Details..."></textarea></div>
                    <input type="hidden" name="hearing_id" id="hearing-id">
                </div>
                <div style="padding:12px 20px;border-top:.5px solid var(--border);display:flex;justify-content:flex-end;gap:8px">
                    <button type="button" class="ds-btn ds-btn-ghost" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="ds-btn ds-btn-teal" id="modal-save-btn">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog"><div class="modal-content shadow border-0" style="border-radius:var(--r)">
        <div style="padding:20px;text-align:center">
            <div style="width:48px;height:48px;border-radius:50%;background:var(--c-rose-bg);color:var(--c-rose);display:flex;align-items:center;justify-content:center;margin:0 auto 12px;font-size:18px"><i class="fas fa-trash-alt"></i></div>
            <h5 style="font-size:14px;font-weight:700;color:var(--ink)">Confirm Delete</h5>
            <p style="font-size:12px;color:var(--ink-muted)">Delete <strong id="delete-case-ref"></strong>?</p>
            <div style="display:flex;justify-content:center;gap:8px;margin-top:16px">
                <button class="ds-btn ds-btn-ghost" data-bs-dismiss="modal">Cancel</button>
                <form id="delete-form" method="POST" action=""><?= csrf_field() ?><button type="submit" class="ds-btn ds-btn-rose">Delete</button></form>
            </div>
        </div>
    </div></div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    window.blotterConfig = {
        deleteUrl: '<?= base_url('blotter/delete') ?>',
        hearingAddUrl: '<?= base_url('blotter/hearing/add/' . $case['id']) ?>',
        hearingUpdateUrl: '<?= base_url('blotter/hearing/update') ?>',
        hearingDeleteUrl: '<?= base_url('blotter/hearing/delete') ?>',
        csrfToken: '<?= csrf_token() ?>',
        csrfHash: '<?= csrf_hash() ?>'
    };
</script>
<script src="<?= base_url('js/blotter/blotter-view.js') ?>"></script>
<?= $this->endSection() ?>
<?php
$profileSrc = ($headResident && !empty($headResident['profile_picture'])) ? 'uploads/' . $headResident['profile_picture'] : 'assets/img/default.png';
$headName = $headResident ? ($headResident['first_name'] . ' ' . $headResident['last_name']) : 'Unassigned';
$voterCount = $seniorCount = $pwdCount = 0;
foreach ($residents as $r) {
    if (!empty($r['is_voter']) && $r['is_voter'] == 1) $voterCount++;
    if (!empty($r['is_senior_citizen']) && $r['is_senior_citizen'] == 1) $seniorCount++;
    if (!empty($r['is_pwd']) && $r['is_pwd'] == 1) $pwdCount++;
}
?>
<?= $this->extend('theme/template') ?>
<?= $this->section('content') ?>

<div class="bmis-content">

    <!-- HEADER -->
    <div class="bmis-page-header">
        <div class="bmis-page-title">
            <h1 style="font-weight: 800;"><i class="fas fa-home text-primary"></i> Household #<?= esc($household['household_no']) ?></h1>
            <p><i class="fas fa-map-marker-alt" style="margin-right:4px"></i> <?= esc($household['address'] ?? 'Address not set') ?></p>
        </div>
        <div class="bmis-page-actions">
            <a href="<?= base_url('households') ?>" class="btn btn-light btn-sm rounded-pill px-3 fw-bold shadow-sm" style="border: 1px solid var(--border);"><i class="fas fa-arrow-left me-2"></i> Back to Directory</a>
            <a href="<?= base_url('households/edit/'.$household['id']) ?>" class="btn btn-light btn-sm rounded-pill px-3 fw-bold shadow-sm" style="border: 1px solid var(--border);"><i class="fas fa-edit me-2"></i> Edit Household</a>
            <a href="<?= base_url('resident/create?household_id='.$household['id']) ?>" class="btn btn-primary btn-sm rounded-pill px-3 fw-bold shadow-sm"><i class="fas fa-user-plus me-2"></i> Add Member</a>
            <button
                type="button"
                class="btn btn-danger btn-sm rounded-pill px-3 fw-bold shadow-sm delete-household-view"
                data-id="<?= esc($household['id']) ?>"
                data-no="<?= esc($household['household_no'], 'attr') ?>"
                title="Delete Household"
            ><i class="fas fa-trash me-2"></i> Delete</button>
        </div>
    </div>

    <!-- STATS -->
    <div class="ds-grid-4" style="margin-bottom:14px">
        <div class="ds-stat"><div class="ds-stat-stripe str-blue"></div><div class="ds-stat-top"><div class="ds-stat-icon ic-blue"><i class="fas fa-users"></i></div></div><div class="ds-stat-num"><?= $residentCount ?></div><div class="ds-stat-label">Total Members</div></div>
        <div class="ds-stat"><div class="ds-stat-stripe" style="background:var(--c-green)"></div><div class="ds-stat-top"><div class="ds-stat-icon ic-green"><i class="fas fa-vote-yea"></i></div></div><div class="ds-stat-num"><?= $voterCount ?></div><div class="ds-stat-label">Voters</div></div>
        <div class="ds-stat"><div class="ds-stat-stripe" style="background:var(--c-amber)"></div><div class="ds-stat-top"><div class="ds-stat-icon ic-amber"><i class="fas fa-user-clock"></i></div></div><div class="ds-stat-num"><?= $seniorCount ?></div><div class="ds-stat-label">Senior Citizens</div></div>
        <div class="ds-stat"><div class="ds-stat-stripe str-violet"></div><div class="ds-stat-top"><div class="ds-stat-icon ic-violet"><i class="fas fa-wheelchair"></i></div></div><div class="ds-stat-num"><?= $pwdCount ?></div><div class="ds-stat-label">PWD</div></div>
    </div>

    <div style="display:grid;grid-template-columns:260px 1fr;gap:14px">
        <!-- LEFT: HEAD + LOCATION -->
        <div>
            <!-- Head of Household -->
            <div class="ds-card" style="text-align:center;margin-bottom:14px">
                <div class="ds-card-body" style="padding:20px 14px">
                    <div style="position:relative;display:inline-block;margin-bottom:10px">
                        <img src="<?= base_url($profileSrc) ?>" style="width:64px;height:64px;border-radius:50%;object-fit:cover;border:3px solid var(--c-teal-bg)">
                        <span style="position:absolute;bottom:0;right:0;width:20px;height:20px;border-radius:50%;background:var(--c-amber);color:#fff;display:flex;align-items:center;justify-content:center;font-size:8px;border:2px solid var(--white)"><i class="fas fa-star"></i></span>
                    </div>
                    <div class="font-serif" style="font-size:16px;font-weight:700;color:var(--ink);letter-spacing:-0.01em"><?= esc($headName) ?></div>
                    <div style="font-size:10px;font-weight:700;text-transform:uppercase;color:var(--ink-soft);margin-bottom:8px">Head of Household</div>
                    <?php if ($headResident): ?>
                    <div style="text-align:left;margin-top:12px">
                        <?php foreach ([['fa-briefcase','Occupation',$headResident['occupation']??'N/A'],['fa-phone','Contact',$headResident['contact_number']??'N/A']] as $d): ?>
                        <div style="display:flex;align-items:center;justify-content:space-between;padding:6px 0;border-bottom:.5px solid var(--border)">
                            <span style="font-size:10.5px;color:var(--ink-muted)"><i class="fas <?= $d[0] ?>" style="width:12px;text-align:center;margin-right:6px"></i><?= $d[1] ?></span>
                            <span style="font-size:11px;font-weight:600;color:var(--ink)"><?= esc($d[2]) ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Location -->
            <div class="ds-card">
                <div class="ds-card-head"><div class="ds-card-title" style="font-size:11px"><i class="fas fa-map-marked-alt"></i> Location</div></div>
                <div class="ds-card-body" style="padding:0">
                    <?php foreach ([['Sitio / Purok',$household['sitio']??'—','fa-map-pin'],['Street Address',$household['street_address']??'—','fa-road'],['House Type',$household['house_type']??'—','fa-home']] as $l): ?>
                    <div style="padding:10px 18px;border-bottom:.5px solid var(--border)">
                        <div class="rv-detail-lbl"><?= $l[0] ?></div>
                        <div style="font-size:12px;font-weight:600;color:var(--ink);margin-top:2px"><i class="fas <?= $l[2] ?>" style="color:var(--c-blue);margin-right:6px;width:12px;text-align:center"></i><?= esc($l[1]) ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- RIGHT: MEMBERS TABLE -->
        <div>
            <div class="ds-card">
                <div class="ds-card-head">
                    <div class="ds-card-title"><i class="fas fa-users"></i> Household Members (<?= $residentCount ?>)</div>
                    <button type="button" class="ds-btn ds-btn-teal" style="height:30px;font-size:11px" data-toggle="modal" data-target="#addMemberModal"><i class="fas fa-plus"></i> Add</button>
                </div>
                <div class="ds-card-body p0">
                    <?php if (empty($residents)): ?>
                        <div style="text-align:center;padding:32px;color:var(--ink-soft)"><i class="fas fa-user-slash" style="font-size:20px;opacity:.3;display:block;margin-bottom:8px"></i>No members found.</div>
                    <?php else: ?>
                    <div style="overflow-x:auto">
                        <table class="ds-table">
                            <thead><tr><th>Resident</th><th>Age</th><th>Relationship</th><th>Status</th><th>Flags</th><th>Actions</th></tr></thead>
                            <tbody>
                            <?php foreach ($residents as $r):
                                $age = $r['age'] ?? '';
                                if (empty($age) && !empty($r['birthdate'])) $age = (new DateTime($r['birthdate']))->diff(new DateTime())->y;
                                $isHead = ($headResident && $headResident['id'] == $r['id']);
                                $fullName = $r['first_name'] . ' ' . $r['last_name'];
                                $memberStatusKey = strtolower($r['status'] ?? 'active');
                                $memberStatus = ucfirst($memberStatusKey);
                                $sc = ['active'=>'ds-badge-teal','inactive'=>'ds-badge-gray','transferred'=>'ds-badge-amber','deceased'=>'ds-badge-rose'];
                            ?>
                            <tr id="member-row-<?= $r['id'] ?>">
                                <td>
                                    <div style="display:flex;align-items:center;gap:8px">
                                        <strong class="font-serif" style="font-size:14px;letter-spacing:-0.01em;"><?= esc($fullName) ?></strong>
                                        <?php if ($isHead): ?><span class="ds-badge" style="background:var(--c-amber-bg);color:var(--c-amber);font-size:9px"><i class="fas fa-star"></i> Head</span><?php endif; ?>
                                    </div>
                                </td>
                                <td><?= $age ? $age . ' yrs' : '—' ?></td>
                                <td><strong><?= esc(ucfirst($r['relationship_to_head'] ?? '—')) ?></strong></td>
                                <td>
                                    <span id="membership-display-<?= $r['id'] ?>">
                                        <span class="ds-badge <?= $sc[$memberStatusKey] ?? 'ds-badge-gray' ?>" id="membership-badge-<?= $r['id'] ?>"><?= esc($memberStatus) ?></span>
                                        <i class="fas fa-pencil-alt edit-membership-icon" data-resident-id="<?= $r['id'] ?>" style="cursor:pointer;font-size:9px;color:var(--ink-soft);margin-left:4px" title="Edit"></i>
                                    </span>
                                    <span id="membership-editor-<?= $r['id'] ?>" style="display:none;align-items:center;gap:4px">
                                        <select id="membership-select-<?= $r['id'] ?>" class="ds-select" style="height:26px;font-size:10px;padding:0 8px;width:auto">
                                            <?php foreach (['active'=>'Active','inactive'=>'Inactive','transferred'=>'Transferred','deceased'=>'Deceased'] as $msVal => $msLabel): ?>
                                                <option value="<?= $msVal ?>" <?= $memberStatusKey == $msVal ? 'selected' : '' ?>><?= $msLabel ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <i class="fas fa-check save-membership-icon" data-resident-id="<?= $r['id'] ?>" style="cursor:pointer;font-size:11px;color:var(--c-teal)" title="Save"></i>
                                        <i class="fas fa-times cancel-membership-icon" data-resident-id="<?= $r['id'] ?>" style="cursor:pointer;font-size:11px;color:var(--c-rose)" title="Cancel"></i>
                                    </span>
                                </td>
                                <td>
                                    <?php if (!empty($r['is_voter'])): ?><span class="ds-badge ds-badge-green" title="Voter" style="font-size:9px"><i class="fas fa-check"></i></span><?php endif; ?>
                                    <?php if (!empty($r['is_senior_citizen'])): ?><span class="ds-badge ds-badge-gray" title="Senior" style="font-size:9px"><i class="fas fa-user-graduate"></i></span><?php endif; ?>
                                    <?php if (!empty($r['is_pwd'])): ?><span class="ds-badge ds-badge-amber" title="PWD" style="font-size:9px"><i class="fas fa-wheelchair"></i></span><?php endif; ?>
                                    <?php if (empty($r['is_voter']) && empty($r['is_senior_citizen']) && empty($r['is_pwd'])): ?><span style="color:var(--ink-soft);font-size:10px">—</span><?php endif; ?>
                                </td>
                                <td style="white-space:nowrap">
                                    <?php if (!$isHead): ?>
                                        <button type="button" class="ds-action-btn ab-green" title="Set as Household Head" onclick="setAsHead(<?= $r['id'] ?>)"><i class="fas fa-crown"></i></button>
                                    <?php endif; ?>
                                    <a href="<?= base_url('resident/view/'.$r['id']) ?>" class="ds-action-btn ab-blue" title="View"><i class="fas fa-eye"></i></a>
                                    <a href="<?= base_url('resident/edit/'.$r['id']) ?>" class="ds-action-btn ab-amber" title="Edit"><i class="fas fa-pen"></i></a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Member Modal -->
<div class="modal fade" id="addMemberModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content shadow border-0" style="border-radius:var(--r)">
            <div style="padding:20px 24px;border-bottom:.5px solid var(--border)">
                <h5 style="font-size:14px;font-weight:700;color:var(--ink);margin:0">Add Member to Household</h5>
            </div>
            <div style="padding:20px 24px">
                <p style="color:var(--ink-muted);text-align:center;margin-bottom:16px;font-size:12px">Choose an option:</p>
                <div style="display:grid;gap:10px">
                    <a href="<?= base_url('resident/create?household_id='.$household['id']) ?>" class="ds-card" style="text-decoration:none;text-align:center">
                        <div class="ds-card-body" style="padding:16px">
                            <i class="fas fa-user-plus" style="font-size:20px;color:var(--c-blue);margin-bottom:6px;display:block"></i>
                            <div style="font-size:12px;font-weight:700;color:var(--ink)">Create New Resident</div>
                            <div style="font-size:10px;color:var(--ink-soft)">Add a new person to the system</div>
                        </div>
                    </a>
                    <a href="<?= base_url('resident/assign-search?household_id='.$household['id']) ?>" class="ds-card" style="text-decoration:none;text-align:center">
                        <div class="ds-card-body" style="padding:16px">
                            <i class="fas fa-search" style="font-size:20px;color:var(--c-teal);margin-bottom:6px;display:block"></i>
                            <div style="font-size:12px;font-weight:700;color:var(--ink)">Add Existing Resident</div>
                            <div style="font-size:10px;color:var(--ink-soft)">Search for a resident already in the database</div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="js-variables" style="display:none;" data-base-url="<?= base_url() ?>" data-csrf-token="<?= csrf_token() ?>" data-csrf-hash="<?= csrf_hash() ?>" data-resident-count="<?= $residentCount ?>"></div>

<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script src="<?= base_url('js/households/households-view.js') ?>"></script>
<script>
function setAsHead(residentId) {
    if(!confirm('Are you sure you want to set this resident as the new Household Head?')) return;
    
    fetch('<?= base_url('households/set-head/') ?>' + residentId, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: '<?= csrf_token() ?>=' + document.getElementById('js-variables').dataset.csrfHash
    })
    .then(response => response.json())
    .then(data => {
        if(data.status === 'success') {
            window.location.reload();
        } else {
            alert(data.message || 'Error updating household head');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('A network error occurred.');
    });
}
</script>
<?= $this->endSection() ?>
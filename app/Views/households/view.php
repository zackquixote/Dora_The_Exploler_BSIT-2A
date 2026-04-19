<?= $this->extend('theme/template') ?>
<?= $this->section('content') ?>

<style>
@import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=DM+Mono:wght@400;500&display=swap');

:root {
    --navy:      #03213b;
    --accent:    #2563eb;
    --accent-lt: #eff6ff;
    --success:   #16a34a;
    --warn:      #d97706;
    --danger:    #dc2626;
    --senior:    #7c3aed;
    --muted:     #6b7280;
    --border:    #e5e7eb;
    --bg:        #f9fafb;
    --card:      #ffffff;
    --text:      #111827;
    --text-sm:   #374151;
    --radius:    12px;
    --shadow:    0 1px 3px rgba(0,0,0,.08), 0 4px 16px rgba(0,0,0,.05);
}
body, .content-wrapper { background: var(--bg) !important; font-family: 'DM Sans', sans-serif; }

/* PAGE HEADER */
.vw-header { padding: 24px 32px 0; display: flex; align-items: flex-start; justify-content: space-between; flex-wrap: wrap; gap: 12px; }
.vw-breadcrumb { font-size: 12px; color: var(--muted); margin-bottom: 6px; }
.vw-breadcrumb a { color: var(--muted); text-decoration: none; }
.vw-breadcrumb a:hover { color: var(--accent); }
.vw-header h1 { font-size: 26px; font-weight: 700; color: var(--text); margin: 0; letter-spacing: -.4px; }
.vw-header-actions { display: flex; gap: 8px; align-items: center; padding-top: 16px; }
.btn-back { display:inline-flex; align-items:center; gap:6px; padding:9px 16px; border:1px solid var(--border); border-radius:8px; background:var(--card); font-size:14px; font-family:'DM Sans',sans-serif; color:var(--text-sm); text-decoration:none; font-weight:500; transition:all .15s; }
.btn-back:hover { border-color:var(--accent); color:var(--accent); text-decoration:none; }
.btn-actions { display:inline-flex; align-items:center; gap:6px; padding:9px 16px; border:none; border-radius:8px; background:var(--navy); font-size:14px; font-family:'DM Sans',sans-serif; color:#fff; font-weight:600; cursor:pointer; }

/* STAT ROW */
.vw-stats { display: grid; grid-template-columns: repeat(4,1fr); gap: 14px; padding: 20px 32px 0; }
.vw-stat { background:var(--card); border-radius:var(--radius); box-shadow:var(--shadow); padding:18px 20px; display:flex; align-items:center; gap:14px; }
.vw-stat-icon { width:42px; height:42px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:18px; flex-shrink:0; }
.vsi-blue   { background:#eff6ff; color:#2563eb; }
.vsi-green  { background:#f0fdf4; color:#16a34a; }
.vsi-orange { background:#fff7ed; color:#d97706; }
.vsi-purple { background:#f5f3ff; color:#7c3aed; }
.vw-stat-body .vst-label { font-size:11px; font-weight:600; color:var(--muted); text-transform:uppercase; letter-spacing:.5px; }
.vw-stat-body .vst-val   { font-size:24px; font-weight:700; color:var(--text); line-height:1.1; }
.vw-stat-body .vst-sub   { font-size:12px; color:var(--muted); }

/* LAYOUT */
.vw-body { display: grid; grid-template-columns: 320px 1fr; gap: 20px; padding: 20px 32px 32px; align-items: start; }

/* LEFT PANEL */
.left-panel { display: flex; flex-direction: column; gap: 16px; }
.panel-card { background:var(--card); border-radius:var(--radius); box-shadow:var(--shadow); overflow:hidden; }

.head-profile-banner { background:var(--navy); height:80px; }
.head-profile-body { padding: 0 20px 20px; }
.head-avatar-wrap { display:flex; justify-content:center; margin-top:-36px; margin-bottom:12px; }
.head-avatar { width:72px; height:72px; border-radius:50%; border:3px solid var(--card); object-fit:cover; background:var(--navy); display:flex; align-items:center; justify-content:center; color:#fff; font-size:24px; font-weight:700; }
.head-name-center { text-align:center; }
.head-name-center h3 { font-size:18px; font-weight:700; color:var(--text); margin:0 0 3px; }
.head-name-center .job-title { font-size:13px; color:var(--muted); margin:0 0 10px; }
.hbadge-row { display:flex; gap:6px; justify-content:center; flex-wrap:wrap; margin-bottom:16px; }
.hbadge { display:inline-block; padding:3px 10px; border-radius:6px; font-size:12px; font-weight:600; }
.hbadge-head   { background:#fef3c7; color:#92400e; }
.hbadge-voter  { background:#eff6ff; color:#1e40af; }

.head-meta { display:flex; flex-direction:column; gap:9px; padding-top:4px; border-top:1px solid var(--border); }
.head-meta-row { display:flex; align-items:center; gap:10px; font-size:13px; color:var(--text-sm); }
.head-meta-row i { width:16px; color:var(--muted); flex-shrink:0; font-size:13px; }

/* Location card */
.loc-card-body { padding:20px; }
.loc-card-body h5 { font-size:14px; font-weight:700; color:var(--text); margin:0 0 14px; display:flex; align-items:center; gap:8px; }
.loc-row { margin-bottom:12px; }
.loc-row .loc-row-label { font-size:11px; font-weight:600; color:var(--muted); text-transform:uppercase; letter-spacing:.5px; margin-bottom:3px; }
.loc-row .loc-row-val   { font-size:14px; color:var(--text-sm); }
.loc-grid { display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-top:8px; }
.map-link { display:inline-flex; align-items:center; gap:6px; font-size:13px; color:var(--accent); text-decoration:none; font-weight:500; margin-top:4px; }
.map-link:hover { text-decoration:underline; }

/* Admin controls card */
.admin-card-body { padding:20px; background:#f8faff; }
.admin-card-body h5 { font-size:13px; font-weight:700; color:var(--navy); margin:0 0 4px; display:flex; align-items:center; gap:6px; }
.admin-card-body .admin-desc { font-size:12px; color:var(--muted); margin:0 0 14px; }
.btn-admin { display:block; width:100%; padding:10px; border-radius:8px; font-size:14px; font-family:'DM Sans',sans-serif; font-weight:600; text-align:center; cursor:pointer; text-decoration:none; margin-bottom:8px; transition:all .15s; }
.btn-admin-primary { background:var(--navy); color:#fff; border:none; }
.btn-admin-primary:hover { background:#0a3259; color:#fff; text-decoration:none; }
.btn-admin-ghost { background:var(--card); color:var(--text-sm); border:1px solid var(--border); }
.btn-admin-ghost:hover { border-color:var(--accent); color:var(--accent); text-decoration:none; }

/* RIGHT PANEL - Members table */
.right-panel { display:flex; flex-direction:column; gap:16px; }
.members-card { background:var(--card); border-radius:var(--radius); box-shadow:var(--shadow); overflow:hidden; }
.members-card-header { padding:18px 22px 14px; border-bottom:1px solid var(--border); display:flex; align-items:flex-start; justify-content:space-between; }
.members-card-header h3 { font-size:16px; font-weight:700; color:var(--text); margin:0 0 3px; }
.members-card-header p  { font-size:13px; color:var(--muted); margin:0; }
.btn-add-member { display:inline-flex; align-items:center; gap:6px; padding:8px 14px; border:none; border-radius:8px; background:var(--navy); color:#fff; font-size:13px; font-family:'DM Sans',sans-serif; font-weight:600; text-decoration:none; }
.btn-add-member:hover { background:#0a3259; color:#fff; text-decoration:none; }

.mem-table { width:100%; border-collapse:collapse; }
.mem-table thead th { padding:10px 18px; font-size:11px; font-weight:600; color:var(--muted); text-transform:uppercase; letter-spacing:.6px; border-bottom:1px solid var(--border); background:#fafafa; white-space:nowrap; }
.mem-table tbody tr { border-bottom:1px solid var(--border); transition:background .1s; }
.mem-table tbody tr:last-child { border-bottom:none; }
.mem-table tbody tr:hover { background:#f8faff; }
.mem-table tbody td { padding:12px 18px; font-size:14px; color:var(--text-sm); vertical-align:middle; }
.mem-table tbody tr.is-head { background:#fffbeb; }

.mem-name-cell { display:flex; align-items:center; gap:10px; }
.mem-avatar { width:36px; height:36px; border-radius:50%; object-fit:cover; flex-shrink:0; }
.mem-avatar-placeholder { width:36px; height:36px; border-radius:50%; background:var(--navy); color:#fff; font-size:12px; font-weight:700; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.mem-full-name { font-size:14px; font-weight:600; color:var(--text); }
.mem-occupation { font-size:12px; color:var(--muted); }

.status-tag { display:inline-block; padding:3px 9px; border-radius:6px; font-size:11px; font-weight:600; }
.st-voter  { background:#eff6ff; color:#1e40af; }
.st-senior { background:#f5f3ff; color:#5b21b6; }
.st-na     { color:var(--muted); font-size:13px; }
.head-badge-inline { background:#fef3c7; color:#92400e; border-radius:6px; padding:2px 7px; font-size:11px; font-weight:700; margin-left:6px; }

.arrow-btn { width:28px; height:28px; border-radius:6px; border:1px solid var(--border); background:var(--card); color:var(--muted); display:inline-flex; align-items:center; justify-content:center; font-size:12px; text-decoration:none; transition:all .15s; }
.arrow-btn:hover { border-color:var(--accent); color:var(--accent); background:var(--accent-lt); text-decoration:none; }

/* Bottom rows */
.vw-bottom-grid { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
.info-card { background:var(--card); border-radius:var(--radius); box-shadow:var(--shadow); padding:20px; }
.info-card h4 { font-size:14px; font-weight:700; color:var(--text); margin:0 0 14px; padding-bottom:10px; border-bottom:1px solid var(--border); }
.info-row { display:flex; justify-content:space-between; align-items:center; padding:8px 0; border-bottom:1px solid var(--bg); font-size:13px; }
.info-row:last-child { border-bottom:none; }
.info-row .ir-label { color:var(--muted); }
.info-row .ir-val   { font-weight:600; color:var(--text-sm); }
.cert-item { display:flex; justify-content:space-between; align-items:flex-start; padding:8px 0; border-bottom:1px solid var(--bg); font-size:13px; }
.cert-item:last-child { border-bottom:none; }
.cert-item .ci-name { color:var(--text-sm); font-weight:500; }
.cert-item .ci-when { color:var(--muted); font-size:12px; white-space:nowrap; }
.cert-none { font-size:13px; color:var(--muted); font-style:italic; }
</style>

<div class="content-wrapper">

    <!-- PAGE HEADER -->
    <div class="vw-header">
        <div>
            <div class="vw-breadcrumb">
                <a href="<?= base_url('staff/dashboard') ?>">Home</a>
                <span class="mx-1">›</span>
                <a href="<?= base_url('households') ?>">Households</a>
                <span class="mx-1">›</span>
                <span>Details</span>
            </div>
            <h1>Household <?= esc($household['household_no']) ?></h1>
        </div>
        <div class="vw-header-actions">
            <a href="<?= base_url('households') ?>" class="btn-back">Back to List</a>
            <div class="dropdown">
                <button class="btn-actions" data-toggle="dropdown">
                    Actions <i class="fas fa-ellipsis-v ml-1"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" href="<?= base_url('households/edit/'.$household['id']) ?>">
                        <i class="fas fa-edit mr-2"></i> Edit Household
                    </a>
                    <a class="dropdown-item" href="<?= base_url('resident/create?household_id='.$household['id']) ?>">
                        <i class="fas fa-user-plus mr-2"></i> Add Member
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item text-danger delete-household-view"
                       href="#"
                       data-id="<?= $household['id'] ?>"
                       data-no="<?= esc($household['household_no']) ?>">
                        <i class="fas fa-trash-alt mr-2"></i> Delete Household
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- STAT ROW -->
    <?php
    $voterCount  = 0;
    $seniorCount = 0;
    $pwdCount    = 0;
    foreach ($residents as $r) {
        if (!empty($r['voter_status']) && strtolower($r['voter_status']) === 'registered') $voterCount++;
        if (!empty($r['birthdate'])) {
            $age = (new DateTime($r['birthdate']))->diff(new DateTime())->y;
            if ($age >= 60) $seniorCount++;
        }
        if (!empty($r['is_pwd']) && $r['is_pwd']) $pwdCount++;
    }
    $established = !empty($household['created_at']) ? date('M Y', strtotime($household['created_at'])) : 'N/A';
    ?>
    <div class="vw-stats">
        <div class="vw-stat">
            <div class="vw-stat-icon vsi-blue"><i class="fas fa-users"></i></div>
            <div class="vw-stat-body">
                <div class="vst-label">Total Members</div>
                <div class="vst-val"><?= $residentCount ?></div>
            </div>
        </div>
        <div class="vw-stat">
            <div class="vw-stat-icon vsi-green"><i class="fas fa-vote-yea"></i></div>
            <div class="vw-stat-body">
                <div class="vst-label">Active Voters</div>
                <div class="vst-val"><?= $voterCount ?></div>
            </div>
        </div>
        <div class="vw-stat">
            <div class="vw-stat-icon vsi-orange"><i class="fas fa-user-clock"></i></div>
            <div class="vw-stat-body">
                <div class="vst-label">Seniors / PWD</div>
                <div class="vst-val"><?= $seniorCount ?> / <?= $pwdCount ?></div>
            </div>
        </div>
        <div class="vw-stat">
            <div class="vw-stat-icon vsi-purple"><i class="fas fa-calendar-alt"></i></div>
            <div class="vw-stat-body">
                <div class="vst-label">Date Established</div>
                <div class="vst-val" style="font-size:18px;"><?= $established ?></div>
            </div>
        </div>
    </div>

    <!-- BODY -->
    <div class="vw-body">

        <!-- ── LEFT PANEL ── -->
        <div class="left-panel">

            <!-- Head profile card -->
            <div class="panel-card">
                <div class="head-profile-banner"></div>
                <div class="head-profile-body">
                    <div class="head-avatar-wrap">
                        <?php if ($headResident && !empty($headResident['profile_picture'])): ?>
                            <img src="<?= base_url('uploads/'.$headResident['profile_picture']) ?>"
                                 class="head-avatar" alt="Head photo">
                        <?php else: ?>
                            <div class="head-avatar">
                                <?php if ($headResident):
                                    echo strtoupper(substr($headResident['first_name'],0,1).substr($headResident['last_name'],0,1));
                                else: ?>
                                    <i class="fas fa-user"></i>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="head-name-center">
                        <?php if ($headResident): ?>
                            <h3><?= esc($headResident['first_name'].' '.$headResident['last_name']) ?></h3>
                            <p class="job-title"><?= esc($headResident['occupation'] ?? 'Resident') ?></p>
                            <div class="hbadge-row">
                                <span class="hbadge hbadge-head">Household Head</span>
                                <?php if (!empty($headResident['voter_status']) && strtolower($headResident['voter_status']) === 'registered'): ?>
                                    <span class="hbadge hbadge-voter">Registered Voter</span>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <h3 style="color:var(--muted);font-size:15px;">No Head Assigned</h3>
                            <p class="job-title">—</p>
                        <?php endif; ?>
                    </div>
                    <?php if ($headResident): ?>
                    <div class="head-meta">
                        <?php if (!empty($headResident['contact_number'])): ?>
                        <div class="head-meta-row">
                            <i class="fas fa-phone"></i>
                            <span><?= esc($headResident['contact_number']) ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if (!empty($headResident['created_at'])): ?>
                        <div class="head-meta-row">
                            <i class="fas fa-calendar"></i>
                            <span>Resident since <?= date('Y', strtotime($headResident['created_at'])) ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if (!empty($headResident['monthly_income'])): ?>
                        <div class="head-meta-row">
                            <i class="fas fa-money-bill"></i>
                            <span><?= esc($headResident['monthly_income']) ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Location card -->
            <div class="panel-card">
                <div class="loc-card-body">
                    <h5><i class="fas fa-map-marker-alt" style="color:var(--accent);"></i> Location Details</h5>
                    <div class="loc-row">
                        <div class="loc-row-label">Full Address</div>
                        <div class="loc-row-val">
                            <?= esc($household['street_address'] ?? '') ?>
                            <?= !empty($household['address']) ? ', '.$household['address'] : '' ?>
                        </div>
                    </div>
                    <div class="loc-grid">
                        <div>
                            <div class="loc-row-label">Sitio</div>
                            <div class="loc-row-val"><?= esc($household['sitio'] ?? '—') ?></div>
                        </div>
                        <div>
                            <div class="loc-row-label">House Type</div>
                            <div class="loc-row-val"><?= esc($household['house_type'] ?? '—') ?></div>
                        </div>
                    </div>
                    <a href="#" class="map-link">
                        <i class="fas fa-map"></i> View on Barangay Map →
                    </a>
                </div>
            </div>

            <!-- Admin controls -->
            <div class="panel-card">
                <div class="admin-card-body">
                    <h5><i class="fas fa-shield-alt" style="color:var(--accent);"></i> Admin Controls</h5>
                    <p class="admin-desc">Special administrative tools for managing this specific household unit.</p>
                    <a href="<?= base_url('households/edit/'.$household['id']) ?>" class="btn-admin btn-admin-primary">
                        <i class="fas fa-edit mr-1"></i> Edit Household Info
                    </a>
                    <a href="#" class="btn-admin btn-admin-ghost">
                        <i class="fas fa-exchange-alt mr-1"></i> Transfer All to New Address
                    </a>
                    <a href="#" class="btn-admin btn-admin-ghost">
                        <i class="fas fa-history mr-1"></i> Audit Household Logs
                    </a>
                </div>
            </div>

        </div><!-- /.left-panel -->

        <!-- ── RIGHT PANEL ── -->
        <div class="right-panel">

            <!-- Members table -->
            <div class="members-card">
                <div class="members-card-header">
                    <div>
                        <h3>Household Members</h3>
                        <p>Comprehensive list of all residents registered at this address.</p>
                    </div>
                    <a href="<?= base_url('resident/create?household_id='.$household['id']) ?>"
                       class="btn-add-member">
                        <i class="fas fa-user-plus"></i> Add Member
                    </a>
                </div>

                <?php if (empty($residents)): ?>
                    <div style="text-align:center;padding:56px;color:var(--muted);">
                        <i class="fas fa-users" style="font-size:36px;display:block;margin-bottom:12px;opacity:.25;"></i>
                        No residents assigned to this household yet.
                    </div>
                <?php else: ?>
                <div style="overflow-x:auto;">
                    <table class="mem-table">
                        <thead>
                            <tr>
                                <th>Full Name</th>
                                <th>Age</th>
                                <th>Relationship</th>
                                <th>Status / Tags</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($residents as $r):
                                $age    = '';
                                $isSenior = false;
                                if (!empty($r['birthdate'])) {
                                    $age = (new DateTime($r['birthdate']))->diff(new DateTime())->y;
                                    $isSenior = $age >= 60;
                                }
                                $isHead   = ($headResident && $headResident['id'] == $r['id']);
                                $isVoter  = !empty($r['voter_status']) && strtolower($r['voter_status']) === 'registered';
                                $photo    = !empty($r['profile_picture'])
                                    ? base_url('uploads/'.$r['profile_picture'])
                                    : null;
                                $initials = strtoupper(substr($r['first_name'],0,1).substr($r['last_name'],0,1));
                            ?>
                            <tr class="<?= $isHead ? 'is-head' : '' ?>">
                                <td>
                                    <div class="mem-name-cell">
                                        <?php if ($photo): ?>
                                            <img src="<?= $photo ?>" class="mem-avatar" alt="">
                                        <?php else: ?>
                                            <div class="mem-avatar-placeholder"><?= esc($initials) ?></div>
                                        <?php endif; ?>
                                        <div>
                                            <div class="mem-full-name">
                                                <?= esc($r['first_name'].' '.$r['last_name']) ?>
                                                <?php if ($isHead): ?>
                                                    <span class="head-badge-inline">Head</span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="mem-occupation">
                                                <?= esc(strtoupper($r['occupation'] ?? $r['civil_status'] ?? '')) ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td><?= $age ? $age : '—' ?></td>
                                <td style="font-size:13px;"><?= esc(ucfirst($r['relationship_to_head'] ?? '—')) ?></td>
                                <td>
                                    <?php if ($isVoter): ?>
                                        <span class="status-tag st-voter">Voter</span>
                                    <?php elseif ($isSenior): ?>
                                        <span class="status-tag st-senior">Senior</span>
                                    <?php else: ?>
                                        <span class="st-na">N/A</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="<?= base_url('resident/view/'.$r['id']) ?>" class="arrow-btn">→</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>

            <!-- Bottom info grid -->
            <div class="vw-bottom-grid">
                <!-- Health Profile -->
                <div class="info-card">
                    <h4>Household Health Profile</h4>
                    <div class="info-row">
                        <span class="ir-label">Vaccination Status</span>
                        <span class="ir-val" style="color:var(--success);">—</span>
                    </div>
                    <div class="info-row">
                        <span class="ir-label">PhilHealth Coverage</span>
                        <span class="ir-val"><?= $residentCount ?> / <?= $residentCount ?> Members</span>
                    </div>
                    <div class="info-row">
                        <span class="ir-label">Special Needs</span>
                        <span class="ir-val"><?= $pwdCount > 0 ? $pwdCount.' PWD(s)' : 'None Reported' ?></span>
                    </div>
                    <div class="info-row">
                        <span class="ir-label">Senior Citizens</span>
                        <span class="ir-val"><?= $seniorCount > 0 ? $seniorCount.' Senior(s)' : 'None' ?></span>
                    </div>
                </div>

                <!-- Recent Certifications -->
                <div class="info-card">
                    <h4>Recent Certifications</h4>
                    <?php if ($headResident): ?>
                        <div class="cert-item">
                            <span class="ci-name">Barangay Clearance (<?= esc($headResident['first_name']) ?>)</span>
                            <span class="ci-when" style="color:var(--muted);">—</span>
                        </div>
                    <?php endif; ?>
                    <?php if ($seniorCount > 0): ?>
                        <div class="cert-item">
                            <span class="ci-name">Senior Citizen ID</span>
                            <span class="ci-when" style="color:var(--muted);">—</span>
                        </div>
                    <?php endif; ?>
                    <p class="cert-none mt-2">• No other recent requests</p>
                </div>
            </div>

        </div><!-- /.right-panel -->
    </div><!-- /.vw-body -->

</div>

<script>
$(document).ready(function () {
    $('.delete-household-view').on('click', function (e) {
        e.preventDefault();
        var id = $(this).data('id'), no = $(this).data('no');
        if (!confirm('Delete Household ' + no + '?\nAll data will be permanently removed.')) return;
        $.ajax({
            url: '<?= base_url('households/delete') ?>/' + id,
            type: 'POST',
            data: { '<?= csrf_token() ?>': '<?= csrf_hash() ?>' },
            dataType: 'json',
            success: function (res) {
                if (res.status === 'success') window.location.href = '<?= base_url('households') ?>';
                else alert(res.message);
            }
        });
    });
});
</script>

<?= $this->endSection() ?>
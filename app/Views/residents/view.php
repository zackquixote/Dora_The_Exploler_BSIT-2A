<?php
$role = strtolower(session()->get('role') ?? 'staff');
$template = ($role == 'admin') ? 'theme/admin/template' : 'theme/template';
?>
<?= $this->extend($template) ?>

<?= $this->section('content') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/resident/view.css') ?>">

<div class="rv-page-wrapper">

    <!-- ── HERO BANNER ── -->
    <div class="rv-hero">
        <div class="rv-hero-content">
            <div class="rv-hero-icon">
                <i class="fas fa-id-card"></i>
            </div>
            <div class="rv-hero-text">
                <h2><?= esc($resident['first_name']) ?> <?= esc($resident['last_name']) ?></h2>
                <p>Resident Profile &nbsp;·&nbsp; ID #<?= esc($resident['id']) ?> &nbsp;·&nbsp; <?= ucfirst(esc($resident['status'] ?? 'active')) ?></p>
            </div>
            <div class="rv-hero-pills">
                <span class="rv-hero-pill">
                    <i class="fas fa-map-marker-alt"></i>
                    <?= esc($resident['sitio'] ?? 'No Sitio') ?>
                </span>
                <span class="rv-hero-pill">
                    <i class="fas fa-calendar-alt"></i>
                    Since <?= date('Y', strtotime($resident['created_at'])) ?>
                </span>
                <?php if (!empty($resident['is_voter'])): ?>
                <span class="rv-hero-pill">
                    <i class="fas fa-vote-yea"></i> Voter
                </span>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- ── MAIN CONTAINER ── -->
    <div style="max-width:100%; padding: 0 0.25rem;">

        <!-- BREADCRUMB + EDIT BUTTON (above grid) -->
        <div class="rv-header" style="margin-bottom:1.25rem;">
            <div class="rv-title">
                <div class="rv-breadcrumb">
                    <a href="<?= base_url(strtolower(session()->get('role') . '/dashboard')) ?>">
                        <i class="fas fa-home" style="margin-right:3px;"></i> Dashboard
                    </a>
                    <span class="sep">/</span>
                    <a href="<?= base_url('resident') ?>">Residents</a>
                    <span class="sep">/</span>
                    <span>View Profile</span>
                </div>
                <h1>Resident Details</h1>
            </div>
            <a href="<?= base_url('resident/edit/' . $resident['id']) ?>" class="rv-btn rv-btn-primary">
                <i class="fas fa-edit"></i> Edit Profile
            </a>
        </div>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="rv-flash-success">
                <i class="fas fa-check-circle"></i>
                <?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>

        <!-- ── THREE-COLUMN GRID ── -->
        <div class="rv-grid">

            <!-- ═══════════════════════════════
                 LEFT: PROFILE CARD
            ════════════════════════════════ -->
            <aside>
                <div class="rv-profile-card">
                    <!-- Avatar + Name -->
                    <div class="rv-avatar-zone">
                        <div class="rv-avatar-ring">
                            <img class="rv-avatar-large"
                                 src="<?= base_url(!empty($resident['profile_picture']) ? 'uploads/' . $resident['profile_picture'] : 'assets/img/default.png') ?>"
                                 alt="Profile Photo">
                            <?php if (($resident['status'] ?? 'active') === 'active'): ?>
                                <span class="rv-avatar-status-dot"></span>
                            <?php endif; ?>
                        </div>
                        <div class="rv-resident-name"><?= esc($resident['first_name']) ?> <?= esc($resident['last_name']) ?></div>
                        <div class="rv-resident-sub"><?= ucfirst(esc($resident['civil_status'] ?? 'N/A')) ?> &nbsp;·&nbsp; <?= ucfirst(esc($resident['sex'])) ?></div>
                    </div>

                    <!-- Stats Strip -->
                    <div class="rv-stats-strip">
                        <div class="rv-stat-item">
                            <span class="rv-stat-val"><?= esc($resident['age'] ?? '—') ?></span>
                            <span class="rv-stat-lbl">Age</span>
                        </div>
                        <div class="rv-stat-item">
                            <span class="rv-stat-val"><?= !empty($resident['is_voter']) ? 'Yes' : 'No' ?></span>
                            <span class="rv-stat-lbl">Voter</span>
                        </div>
                        <div class="rv-stat-item">
                            <span class="rv-stat-val"><?= !empty($resident['household_no']) ? esc($resident['household_no']) : '—' ?></span>
                            <span class="rv-stat-lbl">HH No.</span>
                        </div>
                    </div>

                    <!-- Info Rows -->
                    <div class="rv-info-list">

                        <div class="rv-info-row">
                            <span class="rv-info-label"><i class="fas fa-phone"></i> Contact</span>
                            <span class="rv-info-value"><?= esc($resident['contact_number'] ?? 'N/A') ?></span>
                        </div>

                        <div class="rv-info-row">
                            <span class="rv-info-label"><i class="fas fa-briefcase"></i> Occupation</span>
                            <span class="rv-info-value"><?= esc($resident['occupation'] ?? 'N/A') ?></span>
                        </div>

                        <div class="rv-info-row">
                            <span class="rv-info-label"><i class="fas fa-flag"></i> Citizenship</span>
                            <span class="rv-info-value"><?= esc($resident['citizenship'] ?? 'N/A') ?></span>
                        </div>

                        <!-- Inline Status Editor -->
                        <div class="rv-info-row">
                            <span class="rv-info-label"><i class="fas fa-circle"></i> Status</span>
                            <span id="status-container">
                                <?php
                                $statusBadge = [
                                    'active'      => 'rv-badge-success',
                                    'inactive'    => 'rv-badge-secondary',
                                    'deceased'    => 'rv-badge-dark',
                                    'transferred' => 'rv-badge-warning',
                                ];
                                $currentStatus = $resident['status'] ?? 'active';
                                ?>
                                <span id="status-display" style="display:flex;align-items:center;gap:0.35rem;">
                                    <span class="rv-badge <?= $statusBadge[$currentStatus] ?? 'rv-badge-secondary' ?>" id="status-badge">
                                        <?= ucfirst($currentStatus) ?>
                                    </span>
                                    <i class="fas fa-pencil-alt" id="edit-status-icon"
                                       style="cursor:pointer;font-size:0.65rem;color:var(--ink-soft);"
                                       title="Change status"></i>
                                </span>
                                <span id="status-editor" style="display:none;align-items:center;gap:0.4rem;">
                                    <select id="status-select" class="form-control form-control-sm"
                                            style="font-size:0.75rem;padding:2px 6px;border-radius:6px;border:1.5px solid var(--accent);font-family:var(--font);font-weight:600;">
                                        <option value="active"      <?= $currentStatus=='active'      ?'selected':'' ?>>Active</option>
                                        <option value="inactive"    <?= $currentStatus=='inactive'    ?'selected':'' ?>>Inactive</option>
                                        <option value="deceased"    <?= $currentStatus=='deceased'    ?'selected':'' ?>>Deceased</option>
                                        <option value="transferred" <?= $currentStatus=='transferred' ?'selected':'' ?>>Transferred</option>
                                    </select>
                                    <i class="fas fa-check text-success" id="save-status-icon"   style="cursor:pointer;font-size:0.8rem;" title="Save"></i>
                                    <i class="fas fa-times text-danger"  id="cancel-status-icon" style="cursor:pointer;font-size:0.8rem;" title="Cancel"></i>
                                </span>
                            </span>
                        </div>

                    </div><!-- /rv-info-list -->

                    <!-- Action Buttons -->
                    <div class="rv-card-actions">
                        <a href="#" onclick="printProfile(); return false;" class="rv-action-btn print">
                            <i class="fas fa-print"></i> Print Profile
                        </a>
                        <a href="#" onclick="generateCertificate(); return false;" class="rv-action-btn cert">
                            <i class="fas fa-file-alt"></i> Generate Certificate
                        </a>
                        <a href="<?= base_url('resident') ?>" class="rv-action-btn back">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>

                </div>
            </aside>

            <!-- ═══════════════════════════════
                 CENTER: TABS
            ════════════════════════════════ -->
            <main class="rv-content-card">

                <div class="rv-tabs">
                    <button class="rv-tab-btn active" onclick="switchTab('personal', this)">
                        <i class="fas fa-user"></i> Personal
                    </button>
                    <button class="rv-tab-btn" onclick="switchTab('household', this)">
                        <i class="fas fa-home"></i> Household
                    </button>
                    <button class="rv-tab-btn" onclick="switchTab('status', this)">
                        <i class="fas fa-flag"></i> Status & Flags
                    </button>
                </div>

                <!-- PERSONAL TAB -->
                <div id="personal" class="rv-tab-content active">
                    <p class="rv-section-heading"><i class="fas fa-user-circle"></i> Personal Information</p>
                    <div class="rv-details-grid">
                        <div class="rv-detail-item">
                            <div class="rv-detail-label">First Name</div>
                            <div class="rv-detail-value"><?= esc($resident['first_name']) ?></div>
                        </div>
                        <div class="rv-detail-item">
                            <div class="rv-detail-label">Middle Name</div>
                            <div class="rv-detail-value"><?= esc($resident['middle_name'] ?? '—') ?></div>
                        </div>
                        <div class="rv-detail-item">
                            <div class="rv-detail-label">Last Name</div>
                            <div class="rv-detail-value"><?= esc($resident['last_name']) ?></div>
                        </div>
                        <div class="rv-detail-item">
                            <div class="rv-detail-label">Birthdate</div>
                            <div class="rv-detail-value"><?= date('F d, Y', strtotime($resident['birthdate'])) ?></div>
                        </div>
                        <div class="rv-detail-item">
                            <div class="rv-detail-label">Sex</div>
                            <div class="rv-detail-value"><?= ucfirst(esc($resident['sex'])) ?></div>
                        </div>
                        <div class="rv-detail-item">
                            <div class="rv-detail-label">Civil Status</div>
                            <div class="rv-detail-value"><?= esc($resident['civil_status'] ?? '—') ?></div>
                        </div>
                        <div class="rv-detail-item">
                            <div class="rv-detail-label">Occupation</div>
                            <div class="rv-detail-value"><?= esc($resident['occupation'] ?? '—') ?></div>
                        </div>
                        <div class="rv-detail-item">
                            <div class="rv-detail-label">Citizenship</div>
                            <div class="rv-detail-value"><?= esc($resident['citizenship'] ?? '—') ?></div>
                        </div>
                        <div class="rv-detail-item">
                            <div class="rv-detail-label">Contact No.</div>
                            <div class="rv-detail-value"><?= esc($resident['contact_number'] ?? '—') ?></div>
                        </div>
                        <div class="rv-detail-item">
                            <div class="rv-detail-label">Registered On</div>
                            <div class="rv-detail-value"><?= date('M d, Y', strtotime($resident['created_at'])) ?></div>
                        </div>
                        <div class="rv-detail-item" style="grid-column: 1 / -1;">
                            <div class="rv-detail-label">Last Updated</div>
                            <div class="rv-detail-value"><?= date('M d, Y · h:i A', strtotime($resident['updated_at'])) ?></div>
                        </div>
                    </div>
                </div>

                <!-- HOUSEHOLD TAB -->
                <div id="household" class="rv-tab-content">
                    <p class="rv-section-heading"><i class="fas fa-home"></i> Household Information</p>

                    <div class="rv-household-block" style="margin-bottom:1rem;">
                        <div class="rv-household-icon">
                            <i class="fas fa-hashtag"></i>
                        </div>
                        <div class="rv-household-info">
                            <h4>Household Number</h4>
                            <?php if (!empty($resident['household_no'])): ?>
                                <p>
                                    <span class="rv-badge rv-badge-success" style="margin-right:8px;"><?= esc($resident['household_no']) ?></span>
                                    <a href="<?= base_url('households/view/' . $resident['household_id']) ?>"
                                       class="rv-btn rv-btn-secondary" style="padding:0.3rem 0.8rem;font-size:0.75rem;display:inline-flex;">
                                        <i class="fas fa-external-link-alt"></i> View Household
                                    </a>
                                </p>
                            <?php else: ?>
                                <p><span class="rv-badge rv-badge-secondary">Not Assigned</span></p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="rv-household-block" style="margin-bottom:1rem;">
                        <div class="rv-household-icon" style="background:var(--danger-light);color:var(--danger);">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="rv-household-info">
                            <h4>Address</h4>
                            <p><?= esc($resident['household_address'] ?? 'No address on file') ?></p>
                        </div>
                    </div>

                    <div class="rv-household-block">
                        <div class="rv-household-icon" style="background:var(--purple-light);color:var(--purple);">
                            <i class="fas fa-layer-group"></i>
                        </div>
                        <div class="rv-household-info">
                            <h4>Sitio / Zone</h4>
                            <p>
                                <?php if (!empty($resident['sitio'])): ?>
                                    <span class="rv-badge rv-badge-info"><?= esc($resident['sitio']) ?></span>
                                <?php else: ?>
                                    <span class="rv-badge rv-badge-secondary">Unassigned</span>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- STATUS & FLAGS TAB -->
                <div id="status" class="rv-tab-content">
                    <p class="rv-section-heading"><i class="fas fa-shield-alt"></i> Classification Flags</p>
                    <div class="rv-flags-grid">
                        <?php
                        $flags = [
                            ['key' => 'is_voter',          'label' => 'Registered Voter', 'icon' => 'fa-vote-yea'],
                            ['key' => 'is_senior_citizen', 'label' => 'Senior Citizen',   'icon' => 'fa-user-graduate'],
                            ['key' => 'is_pwd',            'label' => 'PWD',              'icon' => 'fa-wheelchair'],
                        ];
                        foreach ($flags as $f):
                            $yes = !empty($resident[$f['key']]);
                        ?>
                        <div class="rv-flag-card <?= $yes ? 'yes' : 'no' ?>">
                            <div class="rv-flag-icon">
                                <i class="fas <?= $f['icon'] ?>"></i>
                            </div>
                            <div class="rv-flag-name"><?= $f['label'] ?></div>
                            <div class="rv-flag-status"><?= $yes ? '✓ Yes' : '— No' ?></div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

            </main>

            <!-- ═══════════════════════════════
                 RIGHT: ACTIVITY PANEL
            ════════════════════════════════ -->
            <aside class="rv-activity-panel">
                <div class="rv-activity-header">
                    <div class="rv-activity-title">
                        <i class="fas fa-history"></i> Recent Activity
                    </div>
                    <span class="rv-activity-count" id="rv-activity-count" style="display:none;">0</span>
                </div>

                <div class="rv-activity-feed" id="rv-activity-feed">
                    <div class="rv-activity-empty">
                        <i class="fas fa-history"></i>
                        <p>Loading activity…</p>
                    </div>
                </div>

                <div class="rv-activity-footer">
                    <a href="<?= base_url('logs') ?>">
                        <i class="fas fa-list"></i> View All Logs
                    </a>
                </div>
            </aside>

        </div><!-- /rv-grid -->
    </div><!-- /container -->
</div><!-- /rv-page-wrapper -->


<!-- ── Generate Certificate Modal ── -->
<div class="modal fade" id="generateCertificateModal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius:14px;overflow:hidden;border:1px solid var(--border);">
            <div class="modal-header" style="background:var(--canvas);border-bottom:1px solid var(--border);padding:1.25rem 1.5rem;">
                <h4 class="modal-title" style="font-size:1rem;font-weight:800;margin:0;color:var(--ink);font-family:'Plus Jakarta Sans',sans-serif;">
                    <i class="fas fa-file-alt" style="color:var(--accent);margin-right:8px;"></i>
                    Generate Certificate
                </h4>
                <button type="button" class="close" data-dismiss="modal" style="color:var(--ink-soft);">&times;</button>
            </div>
            <form action="<?= base_url('certificate/store') ?>" method="POST">
                <div class="modal-body" style="padding:1.5rem;">
                    <input type="hidden" name="resident_id" value="<?= $resident['id'] ?>">
                    <div class="form-group" style="margin-bottom:1rem;">
                        <label style="display:block;margin-bottom:0.4rem;font-weight:700;font-size:0.85rem;color:var(--ink);font-family:'Plus Jakarta Sans',sans-serif;">Certificate Type</label>
                        <select name="certificate_type" class="form-control" style="border-radius:8px;border:1px solid var(--border);font-family:'Plus Jakarta Sans',sans-serif;font-weight:500;">
                            <option>Barangay Clearance</option>
                            <option>Certificate of Indigency</option>
                            <option>Certificate of Residency</option>
                            <option>Business Permit</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label style="display:block;margin-bottom:0.4rem;font-weight:700;font-size:0.85rem;color:var(--ink);font-family:'Plus Jakarta Sans',sans-serif;">Purpose</label>
                        <input type="text" name="purpose" class="form-control"
                               placeholder="e.g. Employment Requirement"
                               required
                               style="border-radius:8px;border:1px solid var(--border);font-family:'Plus Jakarta Sans',sans-serif;">
                    </div>
                </div>
                <div class="modal-footer" style="background:var(--canvas);border-top:1px solid var(--border);padding:1rem 1.5rem;gap:0.5rem;">
                    <button type="button" class="rv-btn rv-btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="rv-btn rv-btn-primary">
                        <i class="fas fa-file-download"></i> Generate
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

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
        active:      'rv-badge-success',
        inactive:    'rv-badge-secondary',
        deceased:    'rv-badge-dark',
        transferred: 'rv-badge-warning'
    };
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="<?= base_url('js/residents/residents-view.js') ?>"></script>
<?= $this->endSection() ?>
<?php
// ---------------------------------------------------------
// SMART THEME LOADER
// ---------------------------------------------------------
 $role = strtolower(session()->get('role') ?? 'staff');
 $template = ($role == 'admin') ? 'theme/admin/template' : 'theme/template';
?>
<!-- Link specific View Styles -->
<link rel="stylesheet" href="<?= base_url('assets/css/resident/residents-view.css') ?>">

<?= $this->extend($template) ?>

<?= $this->section('content') ?>

<div class="rv-page-wrapper">
    <div class="residents-view-container">

        <!-- Header -->
        <header class="rv-header">
            <div class="rv-title">
                <h1>Resident Details</h1>
                <div class="rv-breadcrumb">
                    <a href="<?= base_url(strtolower(session()->get('role') . '/dashboard')) ?>">Dashboard</a> /
                    <a href="<?= base_url('resident') ?>">Residents</a> /
                    <span>View Profile</span>
                </div>
            </div>
            <div>
                <a href="<?= base_url('resident/edit/' . $resident['id']) ?>" class="rv-btn rv-btn-primary">
                    <i class="fas fa-edit"></i> Edit Profile
                </a>
            </div>
        </header>

        <!-- Flash Message -->
        <?php if (session()->getFlashdata('success')): ?>
            <div class="rv-flash-success">
                <i class="fas fa-check-circle" style="margin-right:6px;"></i>
                <?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>

        <!-- 3-Column Grid -->
        <div class="rv-grid">

            <!-- ================================================
                 LEFT: Profile Card
                 ================================================ -->
            <aside>
                <div class="rv-profile-card">
                    <div class="rv-profile-header"></div>
                    <div class="rv-profile-body">
                        <div class="rv-avatar-container">
                            <img class="rv-avatar-large"
                                 src="<?= base_url(!empty($resident['profile_picture']) ? 'uploads/' . $resident['profile_picture'] : 'assets/img/default.png') ?>"
                                 alt="Profile Photo">
                        </div>

                        <h2 class="rv-name"><?= esc($resident['first_name']) ?> <?= esc($resident['last_name']) ?></h2>
                        <p class="rv-subtitle"><?= ucfirst(esc($resident['civil_status'] ?? 'N/A')) ?></p>

                        <!-- Mini Bio Stats -->
                        <div class="rv-meta-list">
                            <div class="rv-meta-item">
                                <span class="rv-meta-label">Age</span>
                                <span class="rv-meta-value">
                                    <?php
                                    if (!empty($resident['birthdate'])) {
                                        $birth = new DateTime($resident['birthdate']);
                                        echo $birth->diff(new DateTime())->y . ' yrs';
                                    } else { echo 'N/A'; }
                                    ?>
                                </span>
                            </div>
                            <div class="rv-meta-item">
                                <span class="rv-meta-label">Gender</span>
                                <span class="rv-meta-value"><?= ucfirst(esc($resident['sex'])) ?></span>
                            </div>
                            <div class="rv-meta-item">
                                <span class="rv-meta-label">Status</span>
                                <span class="rv-meta-value">
                                    <?php
                                    $status = $resident['status'] ?? 'active';
                                    $statusBadge = [
                                        'active'      => 'rv-badge-success',
                                        'inactive'    => 'rv-badge-secondary',
                                        'deceased'    => 'rv-badge-dark',
                                        'transferred' => 'rv-badge-warning'
                                    ];
                                    ?>
                                    <span class="rv-badge <?= $statusBadge[$status] ?? 'rv-badge-secondary' ?>">
                                        <?= ucfirst($status) ?>
                                    </span>
                                </span>
                            </div>
                            <div class="rv-meta-item">
                                <span class="rv-meta-label">Contact</span>
                                <span class="rv-meta-value"><?= esc($resident['contact_number'] ?? 'N/A') ?></span>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="rv-actions">
                            <a href="#" onclick="printProfile(); return false;" class="rv-action-link">
                                <i class="fas fa-print"></i> Print Profile
                            </a>
                            <a href="#" onclick="generateCertificate(); return false;" class="rv-action-link">
                                <i class="fas fa-file-alt"></i> Generate Certificate
                            </a>
                            <a href="<?= base_url('resident') ?>" class="rv-action-link">
                                <i class="fas fa-arrow-left"></i> Back to List
                            </a>
                        </div>
                    </div>
                </div>
            </aside>

            <!-- ================================================
                 CENTER: Details Tabs
                 ================================================ -->
            <main class="rv-content-card">
                <!-- Tab Nav -->
                <div class="rv-tabs">
                    <button class="rv-tab-btn active" onclick="switchTab('personal', this)">
                        <i class="fas fa-user"></i> Personal
                    </button>
                    <button class="rv-tab-btn" onclick="switchTab('household', this)">
                        <i class="fas fa-home"></i> Household
                    </button>
                    <button class="rv-tab-btn" onclick="switchTab('status', this)">
                        <i class="fas fa-flag-checkered"></i> Status & Flags
                    </button>
                </div>

                <!-- Tab: Personal -->
                <div id="personal" class="rv-tab-content active">
                    <div class="rv-tab-two-col">
                        <table class="rv-details-table">
                            <tr><th>First Name</th>  <td><?= esc($resident['first_name']) ?></td></tr>
                            <tr><th>Middle Name</th> <td><?= esc($resident['middle_name'] ?? '—') ?></td></tr>
                            <tr><th>Last Name</th>   <td><?= esc($resident['last_name']) ?></td></tr>
                            <tr><th>Birthdate</th>   <td><?= date('F d, Y', strtotime($resident['birthdate'])) ?></td></tr>
                            <tr><th>Civil Status</th><td><?= esc($resident['civil_status'] ?? '—') ?></td></tr>
                        </table>
                        <table class="rv-details-table">
                            <tr><th>Occupation</th>    <td><?= esc($resident['occupation']     ?? '—') ?></td></tr>
                            <tr><th>Citizenship</th>   <td><?= esc($resident['citizenship']    ?? '—') ?></td></tr>
                            <tr><th>Contact No.</th>   <td><?= esc($resident['contact_number'] ?? '—') ?></td></tr>
                            <tr><th>Registered</th>    <td><?= date('M d, Y', strtotime($resident['created_at'])) ?></td></tr>
                            <tr><th>Last Updated</th>  <td><?= date('M d, Y h:i A', strtotime($resident['updated_at'])) ?></td></tr>
                        </table>
                    </div>
                </div>

                <!-- Tab: Household -->
                <div id="household" class="rv-tab-content">
                    <table class="rv-details-table">
                        <tr>
                            <th>Household No.</th>
                            <td>
                                <?php if (!empty($resident['household_no'])): ?>
                                    <span class="rv-badge rv-badge-success" style="font-size:0.85rem;"><?= esc($resident['household_no']) ?></span>
                                    <a href="<?= base_url('households/view/' . $resident['household_id']) ?>"
                                       class="rv-btn rv-btn-secondary"
                                       style="width:auto;margin-left:10px;padding:0.3rem 0.7rem;font-size:0.78rem;">
                                        <i class="fas fa-external-link-alt"></i> View Household
                                    </a>
                                <?php else: ?>
                                    <span class="rv-badge rv-badge-secondary">Not assigned</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Address</th>
                            <td>
                                <i class="fas fa-map-marker-alt" style="color:#EF4444;margin-right:6px;"></i>
                                <?= esc($resident['household_address'] ?? 'No address on file') ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Sitio / Zone</th>
                            <td>
                                <?php if (!empty($resident['sitio'])): ?>
                                    <span class="rv-badge rv-badge-info"><?= esc($resident['sitio']) ?></span>
                                <?php else: ?>
                                    <span class="rv-badge rv-badge-secondary">Unassigned</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- Tab: Status & Flags -->
                <div id="status" class="rv-tab-content">
                    <table class="rv-details-table">
                        <tr>
                            <th>Registered Voter</th>
                            <td>
                                <?= !empty($resident['is_voter'])
                                    ? '<span class="rv-badge rv-badge-success"><i class="fas fa-check" style="margin-right:4px;"></i> Yes</span>'
                                    : '<span class="rv-badge rv-badge-secondary">No</span>' ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Senior Citizen</th>
                            <td>
                                <?= !empty($resident['is_senior_citizen'])
                                    ? '<span class="rv-badge rv-badge-info"><i class="fas fa-user-graduate" style="margin-right:4px;"></i> Yes</span>'
                                    : '<span class="rv-badge rv-badge-secondary">No</span>' ?>
                            </td>
                        </tr>
                        <tr>
                            <th>PWD</th>
                            <td>
                                <?= !empty($resident['is_pwd'])
                                    ? '<span class="rv-badge rv-badge-warning"><i class="fas fa-wheelchair" style="margin-right:4px;"></i> Yes</span>'
                                    : '<span class="rv-badge rv-badge-secondary">No</span>' ?>
                            </td>
                        </tr>
                    </table>
                </div>
            </main>

            <!-- ================================================
                 RIGHT: Recent Activity Panel
                 ================================================ -->
            <aside class="rv-activity-panel">
                <div class="rv-activity-header">
                    <div class="rv-activity-title">
                        <i class="fas fa-history"></i>
                        Recent Activity
                    </div>
                    <span class="rv-activity-count" id="rv-activity-count" style="display:none;">0</span>
                </div>

                <!-- Feed renders here via JS -->
                <div class="rv-activity-feed" id="rv-activity-feed">
                    <div class="rv-activity-empty">
                        <i class="fas fa-history"></i>
                        <p>Loading activity...</p>
                    </div>
                </div>

                <div class="rv-activity-footer">
                    <a href="<?= base_url('admin/activity-logs') ?>">
                        <i class="fas fa-list" style="margin-right:4px;font-size:0.7rem;"></i>
                        View All Activity Logs
                    </a>
                </div>
            </aside>

        </div><!-- /.rv-grid -->
    </div><!-- /.residents-view-container -->
</div><!-- /.rv-page-wrapper -->


<!-- Generate Certificate Modal -->
<div class="modal fade" id="generateCertificateModal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius:12px;overflow:hidden;border:1px solid #E2E8F0;">
            <div class="modal-header" style="background:#FAFBFD;border-bottom:1px solid #E2E8F0;padding:1rem 1.5rem;">
                <h4 class="modal-title" style="font-size:1rem;font-weight:700;margin:0;color:#1E293B;">
                    <i class="fas fa-file-alt" style="color:#2B4FBF;margin-right:8px;"></i>
                    Generate Certificate
                </h4>
                <button type="button" class="close" data-dismiss="modal" style="color:#64748B;">&times;</button>
            </div>
            <form action="<?= base_url('certificate/store') ?>" method="POST">
                <div class="modal-body" style="padding:1.5rem;">
                    <input type="hidden" name="resident_id" value="<?= $resident['id'] ?>">
                    <div class="form-group" style="margin-bottom:1rem;">
                        <label style="display:block;margin-bottom:0.4rem;font-weight:600;font-size:0.875rem;color:#1E293B;">Certificate Type</label>
                        <select name="certificate_type" class="form-control" style="border-radius:8px;border:1px solid #E2E8F0;font-family:'DM Sans',sans-serif;">
                            <option>Barangay Clearance</option>
                            <option>Certificate of Indigency</option>
                            <option>Certificate of Residency</option>
                            <option>Business Permit</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label style="display:block;margin-bottom:0.4rem;font-weight:600;font-size:0.875rem;color:#1E293B;">Purpose</label>
                        <input type="text" name="purpose" class="form-control"
                               placeholder="e.g. Employment Requirement"
                               required
                               style="border-radius:8px;border:1px solid #E2E8F0;font-family:'DM Sans',sans-serif;">
                    </div>
                </div>
                <div class="modal-footer" style="background:#FAFBFD;border-top:1px solid #E2E8F0;padding:1rem 1.5rem;gap:0.5rem;">
                    <button type="button" class="rv-btn rv-btn-secondary" data-dismiss="modal" style="width:auto;">Cancel</button>
                    <button type="submit" class="rv-btn rv-btn-primary" style="width:auto;">
                        <i class="fas fa-file-download"></i> Generate
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JS Config Variables -->
<script>
    window.BASE_URL        = "<?= base_url() ?>";
    window.CSRF_TOKEN_NAME = "<?= csrf_token() ?>";
    window.CSRF_TOKEN_VALUE= "<?= csrf_hash() ?>";
    window.RESIDENT_ID     = "<?= $resident['id'] ?>";
    window.RESIDENT_NAME   = "<?= esc($resident['first_name'] . ' ' . $resident['last_name'], 'js') ?>";
    window.CURRENT_USER    = "<?= esc(session()->get('name') ?? session()->get('username') ?? 'User', 'js') ?>";
    window.CURRENT_ROLE    = "<?= esc(session()->get('role') ?? 'staff', 'js') ?>";
</script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Custom JS -->
<script src="<?= base_url('js/residents/residents-view.js') ?>"></script>

<?= $this->endSection() ?>
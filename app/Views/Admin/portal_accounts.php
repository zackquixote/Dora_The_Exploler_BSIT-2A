<?= $this->extend('theme/template') ?>

<?= $this->section('content') ?>
<div class="bmis-header">
    <div class="bmis-title">
        <div class="icon"><i class="fas fa-users-cog"></i></div>
        <div>
            <h2>Resident Verification Queue</h2>
            <p>Review uploaded national IDs, approve portal access, and manage active resident accounts.</p>
        </div>
    </div>
</div>

<div class="ds-container">
    <?php if (session()->getFlashdata('success')): ?>
        <div class="ds-alert ds-alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="ds-alert ds-alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <!-- Verification Queue -->
    <div class="ds-card" style="margin-bottom:24px">
        <div class="ds-card-header">
            <h3>Verification Queue (<?= count($pendingVerifications) ?>)</h3>
        </div>
        <div class="ds-card-body" style="padding:0">
            <div class="ds-table-wrapper">
                <table class="ds-table">
                    <thead>
                        <tr>
                            <th>Applicant</th>
                            <th>Address</th>
                            <th>Contact</th>
                            <th>Status</th>
                            <th>Submitted</th>
                            <th style="text-align:right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($pendingVerifications)): ?>
                        <tr><td colspan="6" style="text-align:center;padding:24px;color:var(--ink-muted)">No resident verifications are waiting for review.</td></tr>
                        <?php else: ?>
                            <?php foreach ($pendingVerifications as $verification): ?>
                            <tr>
                                <td>
                                    <strong><?= esc(trim($verification['first_name'] . ' ' . $verification['last_name'])) ?></strong>
                                    <div style="font-size:12px;color:var(--ink-muted)">ID: <?= esc($verification['national_id_number']) ?></div>
                                </td>
                                <td style="max-width:260px;font-size:13px;color:var(--ink-muted)"><?= esc($verification['address_submitted']) ?></td>
                                <td>
                                    <div><?= esc($verification['contact_email_submitted'] ?: '—') ?></div>
                                    <div style="font-size:12px;color:var(--ink-muted)"><?= esc($verification['contact_phone_submitted'] ?: '—') ?></div>
                                </td>
                                <td>
                                    <span class="ds-badge <?= $verification['status'] === 'pending_admin_review' ? 'ds-badge-blue' : ($verification['status'] === 'needs_resubmission' ? 'ds-badge-amber' : ($verification['status'] === 'pending_otp' ? 'ds-badge-teal' : 'ds-badge-rose')) ?>">
                                        <?= esc(str_replace('_', ' ', $verification['status'])) ?>
                                    </span>
                                </td>
                                <td><?= !empty($verification['submitted_at']) ? date('M d, Y h:i A', strtotime($verification['submitted_at'])) : '—' ?></td>
                                <td style="text-align:right">
                                    <div style="display:flex;gap:8px;justify-content:flex-end">
                                        <a href="<?= base_url('admin/portal-accounts/verification/' . $verification['id']) ?>" class="ds-btn ds-btn-sm ds-btn-teal">
                                            <i class="fas fa-search"></i> Review
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Active Accounts -->
    <div class="ds-card">
        <div class="ds-card-header">
            <h3>Active Accounts (<?= count($activeAccounts) ?>)</h3>
        </div>
        <div class="ds-card-body" style="padding:0">
            <div class="ds-table-wrapper">
                <table class="ds-table">
                    <thead>
                        <tr>
                            <th>Resident Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Last Login</th>
                            <th style="text-align:right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($activeAccounts)): ?>
                        <tr><td colspan="5" style="text-align:center;padding:24px;color:var(--ink-muted)">No active accounts.</td></tr>
                        <?php else: ?>
                            <?php foreach ($activeAccounts as $acc): ?>
                            <tr>
                                <td><strong><?= esc($acc['first_name'] . ' ' . $acc['last_name']) ?></strong></td>
                                <td><?= esc($acc['email'] ?? '—') ?></td>
                                <td><?= esc($acc['phone'] ?? '—') ?></td>
                                <td style="font-size:12px;color:var(--ink-muted)"><?= !empty($acc['last_login_at']) ? date('M d, Y h:i A', strtotime($acc['last_login_at'])) : 'Never' ?></td>
                                <td style="text-align:right">
                                    <div style="display:inline-flex;gap:4px">
                                        <button type="button" class="ds-btn ds-btn-sm ds-btn-light" title="Reset Password" onclick="openResetModal(<?= $acc['id'] ?>, '<?= esc($acc['first_name'] . ' ' . $acc['last_name']) ?>')">
                                            <i class="fas fa-key"></i>
                                        </button>
                                        <form method="post" action="<?= base_url('admin/portal-accounts/suspend/' . $acc['id']) ?>" style="margin:0" onsubmit="return confirm('Suspend this account?')">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="ds-btn ds-btn-sm ds-btn-amber" title="Suspend">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Reset Password Modal -->
<div id="resetModal" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center">
    <div class="ds-card" style="width:420px;margin:0 auto;margin-top:10vh">
        <form id="resetForm" method="post" action="">
            <?= csrf_field() ?>
            <div class="ds-card-header"><h3><i class="fas fa-key" style="color:#f59e0b;margin-right:8px"></i>Reset Password — <span id="resetName"></span></h3></div>
            <div class="ds-card-body">
                <p style="font-size:13px;color:var(--ink-muted);margin-bottom:16px">Set a new temporary password for this resident. Tell them to change it after logging in.</p>
                <div class="ds-form-group">
                    <label class="ds-form-label">New Password (min. 6 characters)</label>
                    <input type="text" name="new_password" class="ds-input" required minlength="6" placeholder="Enter new password...">
                </div>
            </div>
            <div class="ds-card-footer" style="display:flex;justify-content:flex-end;gap:10px">
                <button type="button" class="ds-btn ds-btn-light" onclick="document.getElementById('resetModal').style.display='none'">Cancel</button>
                <button type="submit" class="ds-btn ds-btn-amber">Set New Password</button>
            </div>
        </form>
    </div>
</div>

<script>
function openResetModal(id, name) {
    document.getElementById('resetForm').action = '<?= base_url('admin/portal-accounts/reset-password/') ?>' + id;
    document.getElementById('resetName').textContent = name;
    document.getElementById('resetModal').style.display = 'flex';
}
</script>
<?= $this->endSection() ?>

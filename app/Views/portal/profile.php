<?= $this->extend('portal/layout') ?>
<?= $this->section('content') ?>

<div class="af-container">
    <div class="af-header">
        <h1 class="af-title">My Profile</h1>
        <p class="af-subtitle">View and update your personal information and account settings.</p>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success af-alert" style="display: flex; align-items: center; gap: 10px; padding: 16px 20px; border-radius: 12px; margin-bottom: 24px; background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; font-weight: 600;">
            <i class="fas fa-check-circle"></i> <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger af-alert" style="display: flex; align-items: center; gap: 10px; padding: 16px 20px; border-radius: 12px; margin-bottom: 24px; background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; font-weight: 600;">
            <i class="fas fa-exclamation-circle"></i> <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <div class="row" style="gap: 24px 0;">
        <!-- Left Column: Read-Only Info -->
        <div class="col-md-5">
            <div class="af-card" style="padding: 0; overflow: hidden;">
                <!-- Profile Header -->
                <div style="background: linear-gradient(135deg, #4f46e5, #7c3aed); padding: 32px 24px; text-align: center; color: white;">
                    <div style="width: 80px; height: 80px; border-radius: 50%; background: rgba(255,255,255,0.2); backdrop-filter: blur(8px); display: flex; align-items: center; justify-content: center; font-size: 32px; margin: 0 auto 12px; border: 3px solid rgba(255,255,255,0.3);">
                        <i class="fas fa-user"></i>
                    </div>
                    <h3 style="font-weight: 800; font-size: 20px; margin: 0;">
                        <?= esc($resident['first_name'] ?? '') ?> <?= esc($resident['middle_name'] ?? '') ?> <?= esc($resident['last_name'] ?? '') ?>
                    </h3>
                    <div style="font-size: 13px; opacity: 0.85; margin-top: 4px;">Resident ID: <?= esc($resident['id'] ?? 'N/A') ?></div>
                </div>

                <!-- Personal Details (read-only from admin) -->
                <div style="padding: 20px 24px;">
                    <div style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: var(--ink-muted); margin-bottom: 14px;">
                        <i class="fas fa-lock" style="margin-right: 4px;"></i> Personal Information (Admin-managed)
                    </div>
                    <ul style="list-style: none; padding: 0; margin: 0;">
                        <li style="padding: 10px 0; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; font-size: 13px;">
                            <span style="color: var(--ink-muted); font-weight: 600;">Birthdate</span>
                            <span style="color: var(--ink); font-weight: 700;"><?= !empty($resident['birthdate']) ? date('M d, Y', strtotime($resident['birthdate'])) : 'N/A' ?></span>
                        </li>
                        <li style="padding: 10px 0; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; font-size: 13px;">
                            <span style="color: var(--ink-muted); font-weight: 600;">Sex</span>
                            <span style="color: var(--ink); font-weight: 700;"><?= ucfirst(esc($resident['sex'] ?? 'N/A')) ?></span>
                        </li>
                        <li style="padding: 10px 0; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; font-size: 13px;">
                            <span style="color: var(--ink-muted); font-weight: 600;">Civil Status</span>
                            <span style="color: var(--ink); font-weight: 700;"><?= ucfirst(esc($resident['civil_status'] ?? 'N/A')) ?></span>
                        </li>
                        <li style="padding: 10px 0; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; font-size: 13px;">
                            <span style="color: var(--ink-muted); font-weight: 600;">Purok / Sitio</span>
                            <span style="color: var(--ink); font-weight: 700;"><?= esc($resident['sitio'] ?? 'N/A') ?></span>
                        </li>
                        <li style="padding: 10px 0; display: flex; justify-content: space-between; font-size: 13px;">
                            <span style="color: var(--ink-muted); font-weight: 600;">Occupation</span>
                            <span style="color: var(--ink); font-weight: 700;"><?= esc($resident['occupation'] ?? 'N/A') ?></span>
                        </li>
                    </ul>
                    <div style="margin-top: 14px; font-size: 12px; color: var(--ink-muted); background: rgba(15,23,42,0.03); padding: 10px 14px; border-radius: 8px;">
                        <i class="fas fa-info-circle"></i> To update personal details, please visit the barangay hall.
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Editable Fields -->
        <div class="col-md-7">
            <form action="<?= base_url('portal/profile/update') ?>" method="POST">
                <?= csrf_field() ?>

                <!-- Contact Information -->
                <div class="af-card" style="padding: 24px; margin-bottom: 20px;">
                    <h3 style="font-size: 1.1rem; font-weight: 800; color: var(--ink); margin-bottom: 20px; border-bottom: 1px solid var(--border); padding-bottom: 10px;">
                        <i class="fas fa-phone-alt" style="color: #3b82f6; margin-right: 8px;"></i> Contact Information
                    </h3>

                    <div class="af-form-group">
                        <label class="af-label">Mobile Number</label>
                        <input type="text" name="contact_number" class="af-input" 
                               value="<?= esc($resident['contact_number'] ?? '') ?>" 
                               placeholder="e.g. 09123456789">
                    </div>

                    <div class="af-form-group" style="margin-top: 14px;">
                        <label class="af-label">Email Address</label>
                        <input type="email" name="email" class="af-input" 
                               value="<?= esc($account['email'] ?? '') ?>" 
                               placeholder="e.g. juan@email.com">
                    </div>

                    <div class="af-form-group" style="margin-top: 14px;">
                        <label class="af-label">Account Phone</label>
                        <input type="text" name="phone" class="af-input" 
                               value="<?= esc($account['phone'] ?? '') ?>" 
                               placeholder="Phone linked to your portal account">
                    </div>
                </div>

                <!-- Change Password -->
                <div class="af-card" style="padding: 24px; margin-bottom: 20px;">
                    <h3 style="font-size: 1.1rem; font-weight: 800; color: var(--ink); margin-bottom: 20px; border-bottom: 1px solid var(--border); padding-bottom: 10px;">
                        <i class="fas fa-key" style="color: #f59e0b; margin-right: 8px;"></i> Change Password
                        <span style="font-size: 12px; font-weight: 500; color: var(--ink-muted); margin-left: 8px;">(optional)</span>
                    </h3>

                    <div class="af-form-group">
                        <label class="af-label">New Password</label>
                        <input type="password" name="new_password" class="af-input" placeholder="Leave blank to keep current password" minlength="6">
                    </div>

                    <div class="af-form-group" style="margin-top: 14px;">
                        <label class="af-label">Confirm New Password</label>
                        <input type="password" name="confirm_password" class="af-input" placeholder="Re-enter new password">
                    </div>
                </div>

                <!-- Submit -->
                <div style="display: flex; gap: 12px; justify-content: flex-end;">
                    <a href="<?= base_url('portal/home') ?>" class="af-btn" style="background: #e2e8f0; color: var(--ink);">Cancel</a>
                    <button type="submit" class="af-btn" id="saveBtn">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.querySelector('form').addEventListener('submit', function() {
        const btn = document.getElementById('saveBtn');
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
        btn.style.opacity = '0.7';
        btn.style.pointerEvents = 'none';
    });
</script>

<?= $this->endSection() ?>

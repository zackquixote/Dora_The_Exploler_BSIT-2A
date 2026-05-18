<?= $this->extend('portal/layout') ?>
<?= $this->section('content') ?>

<div class="af-container">

    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success af-alert" style="display: flex; align-items: center; gap: 10px; padding: 16px 20px; border-radius: 12px; margin-bottom: 24px; background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; font-weight: 600; animation: slideDown 0.3s ease;">
            <i class="fas fa-check-circle" style="font-size: 18px;"></i> <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger af-alert" style="display: flex; align-items: center; gap: 10px; padding: 16px 20px; border-radius: 12px; margin-bottom: 24px; background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; font-weight: 600; animation: slideDown 0.3s ease;">
            <i class="fas fa-exclamation-circle" style="font-size: 18px;"></i> <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <!-- Welcome Header -->
    <div style="margin-bottom: 32px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 20px;">
        <div>
            <h2 style="font-weight: 800; font-size: 36px; color: var(--ink); margin: 0;">
                Hello, <?= esc($resident['first_name'] ?? 'Resident') ?>! 👋
            </h2>
            <p style="font-size: 16px; color: var(--ink-muted); margin-top: 4px;">
                Welcome to your personalized barangay dashboard.
            </p>
        </div>
        <div style="display: flex; gap: 12px;">
            <div style="background: white; padding: 10px 16px; border-radius: 12px; border: 1px solid var(--border); box-shadow: var(--shadow-sm); display: flex; align-items: center; gap: 10px;">
                <div style="width: 10px; height: 10px; border-radius: 50%; background: #10b981; box-shadow: 0 0 10px rgba(16,185,129,0.5);"></div>
                <span style="font-weight: 600; font-size: 13px; color: var(--ink-muted);">Account Active</span>
            </div>
        </div>
    </div>

    <!-- Quick Actions Grid -->
    <h3 class="ds-section-label" style="font-size: 12px; margin-bottom: 16px;">Quick Services</h3>
    <div class="ds-grid-3" style="grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));">
        
        <!-- Generate QR Code -->
        <a href="<?= base_url('advanced/qr-generator') ?>" style="text-decoration: none;" class="af-card">
            <div class="af-card-body" style="text-align: center; padding: 32px 24px; transition: transform 0.3s ease;">
                <div style="width: 64px; height: 64px; border-radius: 16px; background: rgba(139,92,246,0.12); color: #8b5cf6; display: flex; align-items: center; justify-content: center; font-size: 28px; margin: 0 auto 16px;">
                    <i class="fas fa-qrcode"></i>
                </div>
                <h4 style="font-size: 18px; font-weight: 800; color: var(--ink); margin-bottom: 8px;">My QR ID</h4>
                <p style="font-size: 13px; color: var(--ink-muted); margin: 0;">View your official resident QR code for fast transactions.</p>
            </div>
        </a>

        <!-- My Digital ID -->
        <a href="<?= base_url('portal/my-id') ?>" style="text-decoration: none;" class="af-card">
            <div class="af-card-body" style="text-align: center; padding: 32px 24px;">
                <div style="width: 64px; height: 64px; border-radius: 16px; background: rgba(59,130,246,0.12); color: #3b82f6; display: flex; align-items: center; justify-content: center; font-size: 28px; margin: 0 auto 16px;">
                    <i class="fas fa-id-card"></i>
                </div>
                <h4 style="font-size: 18px; font-weight: 800; color: var(--ink); margin-bottom: 8px;">My Digital ID</h4>
                <p style="font-size: 13px; color: var(--ink-muted); margin: 0;">View or print your PVC-ready Digital Barangay ID.</p>
            </div>
        </a>

        <!-- File Incident -->
        <a href="<?= base_url('portal/file-blotter') ?>" style="text-decoration: none;" class="af-card">
            <div class="af-card-body" style="text-align: center; padding: 32px 24px;">
                <div style="width: 64px; height: 64px; border-radius: 16px; background: rgba(245,158,11,0.12); color: #f59e0b; display: flex; align-items: center; justify-content: center; font-size: 28px; margin: 0 auto 16px;">
                    <i class="fas fa-balance-scale"></i>
                </div>
                <h4 style="font-size: 18px; font-weight: 800; color: var(--ink); margin-bottom: 8px;">File an Incident</h4>
                <p style="font-size: 13px; color: var(--ink-muted); margin: 0;">Formally report an incident or complaint to the Lupon.</p>
            </div>
        </a>

        <!-- Book Facility -->
        <a href="<?= base_url('portal/facilities') ?>" style="text-decoration: none;" class="af-card">
            <div class="af-card-body" style="text-align: center; padding: 32px 24px;">
                <div style="width: 64px; height: 64px; border-radius: 16px; background: rgba(14,165,233,0.12); color: #0ea5e9; display: flex; align-items: center; justify-content: center; font-size: 28px; margin: 0 auto 16px;">
                    <i class="fas fa-building"></i>
                </div>
                <h4 style="font-size: 18px; font-weight: 800; color: var(--ink); margin-bottom: 8px;">Book Facility</h4>
                <p style="font-size: 13px; color: var(--ink-muted); margin: 0;">Reserve barangay venues, vehicles, or equipment.</p>
            </div>
        </a>

        <!-- Emergency Report -->
        <a href="<?= base_url('advanced/report-emergency') ?>" style="text-decoration: none;" class="af-card theme-emergency">
            <div class="af-card-body" style="text-align: center; padding: 32px 24px;">
                <div class="pulse-icon" style="width: 64px; height: 64px; border-radius: 16px; background: rgba(239,68,68,0.12); color: #ef4444; display: flex; align-items: center; justify-content: center; font-size: 28px; margin: 0 auto 16px;">
                    <i class="fas fa-ambulance"></i>
                </div>
                <h4 style="font-size: 18px; font-weight: 800; color: var(--ink); margin-bottom: 8px;">Emergency</h4>
                <p style="font-size: 13px; color: var(--ink-muted); margin: 0;">Quickly dispatch response teams to your location.</p>
            </div>
        </a>

        <!-- Community Events -->
        <a href="<?= base_url('advanced/events') ?>" style="text-decoration: none;" class="af-card theme-event">
            <div class="af-card-body" style="text-align: center; padding: 32px 24px;">
                <div style="width: 64px; height: 64px; border-radius: 16px; background: rgba(16,185,129,0.12); color: #10b981; display: flex; align-items: center; justify-content: center; font-size: 28px; margin: 0 auto 16px;">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <h4 style="font-size: 18px; font-weight: 800; color: var(--ink); margin-bottom: 8px;">Events</h4>
                <p style="font-size: 13px; color: var(--ink-muted); margin: 0;">View and register for upcoming barangay events.</p>
            </div>
        </a>

    </div>

    <!-- Additional Information Section -->
    <div class="ds-grid-2" style="margin-top: 32px;">
        <!-- Profile Summary -->
        <div class="af-card">
            <div class="af-card-header">
                <div class="ds-card-title"><i class="fas fa-user-circle"></i> Personal Profile</div>
            </div>
            <div class="af-card-body">
                <ul style="list-style: none; padding: 0; margin: 0;">
                    <li style="padding: 12px 0; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between;">
                        <span style="color: var(--ink-muted); font-weight: 600;">Full Name</span>
                        <span style="color: var(--ink); font-weight: 800;"><?= esc($resident['first_name'] ?? '') ?> <?= esc($resident['last_name'] ?? '') ?></span>
                    </li>
                    <li style="padding: 12px 0; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between;">
                        <span style="color: var(--ink-muted); font-weight: 600;">Resident ID</span>
                        <span style="color: var(--ink); font-weight: 800;"><?= esc($resident['id'] ?? 'N/A') ?></span>
                    </li>
                    <li style="padding: 12px 0; display: flex; justify-content: space-between;">
                        <span style="color: var(--ink-muted); font-weight: 600;">Contact</span>
                        <span style="color: var(--ink); font-weight: 800;"><?= esc($account['email'] ?? $account['phone'] ?? 'N/A') ?></span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Recent Activity (Real Data) -->
        <div class="af-card">
            <div class="af-card-header">
                <div class="ds-card-title"><i class="fas fa-history"></i> Recent Activity</div>
            </div>
            <div class="af-card-body">
                <?php if (empty($activities)): ?>
                    <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 200px; text-align: center;">
                        <div style="font-size: 40px; color: rgba(15,23,42,0.1); margin-bottom: 16px;">
                            <i class="fas fa-clipboard-check"></i>
                        </div>
                        <p style="color: var(--ink-muted); font-weight: 600; font-size: 14px;">No recent transactions yet.</p>
                        <p style="color: var(--ink-muted); font-size: 12px; margin-top: 4px;">File an incident or book a facility to get started!</p>
                    </div>
                <?php else: ?>
                    <div style="display: flex; flex-direction: column; gap: 0;">
                        <?php foreach ($activities as $i => $act): ?>
                            <div style="display: flex; gap: 14px; padding: 14px 0; <?= $i < count($activities) - 1 ? 'border-bottom: 1px solid var(--border);' : '' ?>">
                                <!-- Icon -->
                                <div style="width: 38px; height: 38px; border-radius: 10px; background: <?= $act['color'] ?>18; color: <?= $act['color'] ?>; display: flex; align-items: center; justify-content: center; font-size: 15px; flex-shrink: 0;">
                                    <i class="<?= $act['icon'] ?>"></i>
                                </div>
                                <!-- Details -->
                                <div style="flex: 1; min-width: 0;">
                                    <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 8px;">
                                        <div>
                                            <div style="font-weight: 700; font-size: 13px; color: var(--ink);"><?= esc($act['title']) ?></div>
                                            <div style="font-size: 12px; color: var(--ink-muted); margin-top: 2px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 260px;"><?= esc($act['desc']) ?></div>
                                        </div>
                                        <?php
                                            $badgeBg = '#e2e8f0'; $badgeColor = '#64748b';
                                            if ($act['status'] === 'Pending')   { $badgeBg = '#fef3c7'; $badgeColor = '#92400e'; }
                                            if ($act['status'] === 'Approved' || $act['status'] === 'Resolved')  { $badgeBg = '#dcfce7'; $badgeColor = '#166534'; }
                                            if ($act['status'] === 'Rejected' || $act['status'] === 'Cancelled') { $badgeBg = '#fef2f2'; $badgeColor = '#991b1b'; }
                                        ?>
                                        <span style="font-size: 11px; font-weight: 700; background: <?= $badgeBg ?>; color: <?= $badgeColor ?>; padding: 3px 10px; border-radius: 20px; white-space: nowrap;">
                                            <?= esc($act['status']) ?>
                                        </span>
                                    </div>
                                    <div style="font-size: 11px; color: var(--ink-muted); margin-top: 6px;">
                                        <i class="far fa-clock" style="margin-right: 4px;"></i>
                                        <?= date('M d, Y h:i A', strtotime($act['date'])) ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<?= $this->endSection() ?>

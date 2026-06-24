<?= $this->extend('portal/layout') ?>
<?= $this->section('content') ?>

<div style="margin-bottom: 32px;">
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px; margin-bottom: 24px;">
        <div>
            <h2 style="font-size: 28px; font-weight: 800; color: var(--ink); margin: 0; display: flex; align-items: center; gap: 12px;">
                <div style="width: 48px; height: 48px; border-radius: 16px; background: linear-gradient(135deg, #f59e0b, #d97706); display: flex; align-items: center; justify-content: center; color: white; font-size: 22px;">
                    <i class="fas fa-bell"></i>
                </div>
                My Notifications
            </h2>
            <p style="color: var(--ink-muted); margin: 8px 0 0 60px; font-size: 14px;">Stay updated on your requests and activities</p>
        </div>
        <?php if (!empty($notifications)): ?>
        <form method="post" action="<?= base_url('portal/notifications/read') ?>">
            <?= csrf_field() ?>
            <button type="submit" style="padding: 10px 20px; border-radius: 12px; border: none; background: linear-gradient(135deg, #4f46e5, #7c3aed); color: white; font-weight: 700; font-size: 13px; cursor: pointer; display: flex; align-items: center; gap: 8px; transition: all 0.2s;">
                <i class="fas fa-check-double"></i> Mark All as Read
            </button>
        </form>
        <?php endif; ?>
    </div>


    <?php if (empty($notifications)): ?>
        <div style="background: rgba(255,255,255,0.7); backdrop-filter: blur(12px); border-radius: 20px; border: 1px solid rgba(15,23,42,0.06); padding: 60px 40px; text-align: center;">
            <div style="width: 80px; height: 80px; border-radius: 50%; background: rgba(245,158,11,0.1); display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; font-size: 36px; color: #f59e0b;">
                <i class="fas fa-bell-slash"></i>
            </div>
            <h3 style="font-size: 20px; font-weight: 700; color: var(--ink); margin: 0 0 8px;">No Notifications</h3>
            <p style="color: var(--ink-muted); margin: 0;">You're all caught up! New notifications will appear here.</p>
        </div>
    <?php else: ?>
        <div style="display: flex; flex-direction: column; gap: 12px;">
            <?php foreach ($notifications as $notif): ?>
                <?php
                    $isUnread = ($notif['status'] === 'sent' || $notif['status'] === 'pending');
                    $typeIcons = [
                        'certificate' => ['fas fa-file-signature', '#8b5cf6'],
                        'booking' => ['fas fa-building', '#0ea5e9'],
                        'blotter' => ['fas fa-balance-scale', '#f59e0b'],
                        'system' => ['fas fa-cog', '#64748b'],
                        'alert' => ['fas fa-exclamation-triangle', '#ef4444'],
                    ];
                    $icon = $typeIcons[$notif['type']] ?? ['fas fa-info-circle', '#64748b'];
                ?>
                <div style="background: <?= $isUnread ? 'rgba(79,70,229,0.04)' : 'rgba(255,255,255,0.7)' ?>; backdrop-filter: blur(12px); border-radius: 16px; border: 1px solid <?= $isUnread ? 'rgba(79,70,229,0.12)' : 'rgba(15,23,42,0.06)' ?>; padding: 20px 24px; display: flex; align-items: flex-start; gap: 16px; transition: all 0.2s;">
                    <div style="width: 44px; height: 44px; border-radius: 14px; background: <?= $icon[1] ?>15; display: flex; align-items: center; justify-content: center; color: <?= $icon[1] ?>; font-size: 18px; flex-shrink: 0;">
                        <i class="<?= $icon[0] ?>"></i>
                    </div>
                    <div style="flex: 1; min-width: 0;">
                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                            <h4 style="font-size: 15px; font-weight: 700; color: var(--ink); margin: 0;"><?= esc($notif['title']) ?></h4>
                            <?php if ($isUnread): ?>
                                <span style="width: 8px; height: 8px; border-radius: 50%; background: #4f46e5; flex-shrink: 0;"></span>
                            <?php endif; ?>
                        </div>
                        <p style="font-size: 13px; color: var(--ink-muted); margin: 0 0 8px; line-height: 1.5;"><?= esc($notif['message']) ?></p>
                        <span style="font-size: 11px; color: var(--ink-muted); opacity: 0.7;">
                            <i class="fas fa-clock" style="margin-right: 4px;"></i>
                            <?= date('M d, Y \a\t h:i A', strtotime($notif['created_at'])) ?>
                        </span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>

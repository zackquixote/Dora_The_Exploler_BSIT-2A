<?php
$role = strtolower(session()->get('role') ?? 'staff');
$template = ($role == 'admin') ? 'theme/admin/template' : 'theme/template';
?>
<?= $this->extend($template) ?>
<?= $this->section('content') ?>

<div class="bmis-content">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px">
        <div style="display:flex;align-items:center;gap:12px">
            <a href="<?= base_url('admin/users') ?>" class="ds-action-btn ab-blue" title="Back"><i class="fas fa-arrow-left"></i></a>
            <div>
                <div class="ds-page-title" style="margin:0">User Details</div>
                <div style="font-size:11px;color:var(--ink-muted)">View administrator profile</div>
            </div>
        </div>
        <div style="display:flex;gap:6px">
            <a href="<?= base_url('admin/users/edit/'.$user['id']) ?>" class="ds-btn ds-btn-blue"><i class="fas fa-edit"></i> Edit User</a>
        </div>
    </div>

    <div class="ds-card" style="max-width:600px">
        <div class="ds-card-head">
            <div class="ds-card-title"><i class="fas fa-user-circle"></i> Profile Information</div>
        </div>
        <div class="ds-card-body p0">
            <div style="padding:24px;text-align:center;border-bottom:.5px solid var(--border)">
                <div style="width:80px;height:80px;border-radius:50%;background:var(--c-blue-bg);color:var(--c-blue);display:flex;align-items:center;justify-content:center;font-size:32px;margin:0 auto 12px">
                    <i class="fas fa-user"></i>
                </div>
                <div style="font-size:18px;font-weight:700;color:var(--ink)"><?= esc($user['name']) ?></div>
                <div style="font-size:12px;color:var(--ink-muted);margin-top:4px"><?= esc($user['email']) ?></div>
                <div style="margin-top:12px;display:flex;justify-content:center;gap:8px">
                    <span class="ds-badge <?= $user['role'] == 'admin' ? 'ds-badge-violet' : 'ds-badge-blue' ?>"><i class="fas fa-user-shield" style="margin-right:4px"></i> <?= ucfirst(esc($user['role'])) ?></span>
                    <span class="ds-badge <?= $user['status'] == 'active' ? 'ds-badge-green' : 'ds-badge-gray' ?>"><i class="fas fa-circle" style="font-size:6px;margin-right:4px;vertical-align:middle"></i> <?= ucfirst(esc($user['status'])) ?></span>
                </div>
            </div>
            
            <div>
                <?php foreach([
                    ['Phone Number', $user['phone'] ?: 'Not provided', 'fa-phone'],
                    ['Role Level', ucfirst(esc($user['role'])), 'fa-id-badge'],
                    ['Account Created', date('F d, Y h:i A', strtotime($user['created_at'] ?? 'now')), 'fa-calendar-alt'],
                    ['Last Updated', date('F d, Y h:i A', strtotime($user['updated_at'] ?? 'now')), 'fa-clock']
                ] as $item): ?>
                <div style="display:flex;align-items:center;padding:16px 24px;border-bottom:.5px solid var(--border)">
                    <div style="width:32px;color:var(--ink-soft);text-align:center"><i class="fas <?= $item[2] ?>"></i></div>
                    <div style="flex:1">
                        <div style="font-size:10px;font-weight:700;text-transform:uppercase;color:var(--ink-soft)"><?= $item[0] ?></div>
                        <div style="font-size:13px;font-weight:600;color:var(--ink);margin-top:2px"><?= esc($item[1]) ?></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

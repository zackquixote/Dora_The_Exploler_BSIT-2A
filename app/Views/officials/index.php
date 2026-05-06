<?php
$role = session()->get('role');
$template = ($role == 'admin') ? 'theme/admin/template' : 'theme/template';
?>
<?= $this->extend($template) ?>
<?= $this->section('content') ?>

<div class="bmis-content">

    <?php if (session()->get('role') == 'admin'): ?>
    <div style="background:var(--c-blue-bg);color:var(--c-blue);padding:10px 16px;border-radius:var(--r-sm);margin-bottom:14px;font-size:12px;font-weight:600;display:flex;align-items:center;justify-content:space-between">
        <span><i class="fas fa-info-circle" style="margin-right:6px"></i> Assign or update officials from the resident list.</span>
        <a href="<?= base_url('admin/settings') ?>" class="ds-btn ds-btn-primary" style="height:30px;font-size:11px"><i class="fas fa-cog"></i> Manage</a>
    </div>
    <?php endif; ?>

    <?php if (!empty($officials)):
        $captain = null; $mainOfficials = []; $kagawads = [];
        foreach ($officials as $o) {
            $pos = strtolower($o['position']);
            if (strpos($pos, 'punong') !== false || strpos($pos, 'captain') !== false) $captain = $o;
            elseif (strpos($pos, 'kagawad') !== false) $kagawads[] = $o;
            else $mainOfficials[] = $o;
        }
    ?>

    <!-- Punong Barangay -->
    <?php if ($captain): ?>
    <div class="ds-section-label">Punong Barangay</div>
    <div class="ds-card" style="margin-bottom:14px">
        <div class="ds-card-body" style="display:flex;align-items:center;gap:16px">
            <img src="<?= base_url('uploads/' . $captain['photo']) ?>" onerror="this.src='<?= base_url('uploads/default.png') ?>'" style="width:64px;height:64px;border-radius:50%;object-fit:cover;border:3px solid var(--c-teal-bg)">
            <div>
                <div style="font-size:10px;font-weight:700;text-transform:uppercase;color:var(--ink-soft);margin-bottom:2px">Head of Barangay</div>
                <div style="font-size:16px;font-weight:700;color:var(--ink)"><?= esc($captain['full_name']) ?></div>
                <span class="ds-badge ds-badge-teal" style="margin-top:4px"><?= esc($captain['position']) ?></span>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Executive Officials -->
    <?php if (!empty($mainOfficials)): ?>
    <div class="ds-section-label">Executive Officials</div>
    <div class="ds-grid-4" style="margin-bottom:14px">
        <?php foreach ($mainOfficials as $o): ?>
        <div class="ds-card" style="text-align:center">
            <div class="ds-card-body" style="padding:20px 14px">
                <img src="<?= base_url('uploads/' . $o['photo']) ?>" onerror="this.src='<?= base_url('uploads/default.png') ?>'" style="width:52px;height:52px;border-radius:50%;object-fit:cover;margin-bottom:10px;border:2px solid var(--c-blue-bg)">
                <div style="font-size:12.5px;font-weight:700;color:var(--ink)"><?= esc($o['full_name']) ?></div>
                <span class="ds-badge ds-badge-blue" style="margin-top:6px"><?= esc($o['position']) ?></span>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- Kagawads -->
    <?php if (!empty($kagawads)): ?>
    <div class="ds-section-label">Sangguniang Barangay Members</div>
    <div class="ds-grid-4" style="margin-bottom:14px">
        <?php foreach ($kagawads as $o): ?>
        <div class="ds-card" style="text-align:center">
            <div class="ds-card-body" style="padding:20px 14px">
                <img src="<?= base_url('uploads/' . $o['photo']) ?>" onerror="this.src='<?= base_url('uploads/default.png') ?>'" style="width:48px;height:48px;border-radius:50%;object-fit:cover;margin-bottom:8px;border:2px solid var(--c-violet-bg)">
                <div style="font-size:12px;font-weight:700;color:var(--ink)"><?= esc($o['full_name']) ?></div>
                <span class="ds-badge ds-badge-violet" style="margin-top:4px"><?= esc($o['position']) ?></span>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php else: ?>
    <div class="ds-card" style="text-align:center">
        <div class="ds-card-body" style="padding:48px">
            <i class="fas fa-users-slash" style="font-size:32px;color:var(--ink-soft);opacity:.3;margin-bottom:12px;display:block"></i>
            <p style="color:var(--ink-soft);font-size:13px;margin-bottom:16px">No officials assigned yet.</p>
            <?php if (session()->get('role') == 'admin'): ?>
                <a href="<?= base_url('admin/settings') ?>" class="ds-btn ds-btn-primary">Go to Settings</a>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
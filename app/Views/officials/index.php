<?php
$role = session()->get('role');
$template = ($role == 'admin') ? 'theme/admin/template' : 'theme/template';
?>
<?= $this->extend($template) ?>
<?= $this->section('content') ?>

<div class="bmis-content">

    <!-- Page Header -->
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px">
        <div style="display:flex;align-items:center;gap:16px">
            <div style="width:48px;height:48px;border-radius:12px;background:var(--c-amber-bg);color:var(--c-amber);display:flex;align-items:center;justify-content:center;font-size:20px">
                <i class="fas fa-user-tie"></i>
            </div>
            <div>
                <div class="ds-page-title" style="margin:0;font-size:24px;font-weight:800;color:var(--ink)">Barangay Officials</div>
                <div style="font-size:13px;color:var(--ink-muted);margin-top:2px;font-weight:700">Meet the dedicated leaders of our community</div>
            </div>
        </div>
        <?php if (session()->get('role') == 'admin'): ?>
            <a href="<?= base_url('admin/settings') ?>" class="ds-btn ds-btn-primary" style="height:40px;padding:0 20px;border-radius:20px;box-shadow:0 4px 12px rgba(var(--c-blue-rgb), 0.3)">
                <i class="fas fa-cog"></i> Manage Officials
            </a>
        <?php endif; ?>
    </div>

    <?php if (!empty($officials)):
        $captain = null; $mainOfficials = []; $kagawads = [];
        foreach ($officials as $o) {
            $pos = strtolower($o['position']);
            if (strpos($pos, 'punong') !== false || strpos($pos, 'captain') !== false) $captain = $o;
            elseif (strpos($pos, 'kagawad') !== false) $kagawads[] = $o;
            else $mainOfficials[] = $o;
        }
    ?>

    <!-- Punong Barangay Hero Card -->
    <?php if ($captain): ?>
    <div style="text-align:center;margin-bottom:40px">
        <div style="display:inline-block;text-align:left;width:100%;max-width:600px">
            <div class="ds-card" style="border:none;box-shadow:0 15px 35px rgba(0,0,0,0.06);border-radius:var(--r-lg);overflow:hidden;position:relative">
                <div style="height:140px;background:linear-gradient(135deg, var(--c-teal) 0%, #047857 100%);position:relative">
                    <svg width="100%" height="100%" style="position:absolute;top:0;left:0;opacity:0.1" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="dots2" width="20" height="20" patternUnits="userSpaceOnUse"><circle cx="2" cy="2" r="2" fill="#fff"/></pattern></defs><rect width="100%" height="100%" fill="url(#dots2)"/></svg>
                </div>
                
                <div style="padding:0 32px 32px;text-align:center;margin-top:-60px;position:relative;z-index:2">
                    <img src="<?= base_url('uploads/' . $captain['photo']) ?>" onerror="this.src='<?= base_url('uploads/default.png') ?>'" style="width:120px;height:120px;border-radius:50%;object-fit:cover;border:4px solid var(--white);box-shadow:0 8px 24px rgba(0,0,0,0.12);margin:0 auto 16px;background:var(--white)">
                    
                    <div style="font-size:11px;font-weight:800;text-transform:uppercase;color:var(--c-teal);letter-spacing:.08em;margin-bottom:6px">Head of Barangay</div>
                    <h2 style="margin:0 0 12px;font-size:26px;font-weight:800;color:var(--ink)"><?= esc($captain['full_name']) ?></h2>
                    
                    <span class="ds-badge" style="background:var(--c-teal-bg);color:var(--c-teal);padding:8px 16px;font-size:13px;font-weight:700;border:1px solid rgba(var(--c-teal-rgb), 0.2)">
                        <i class="fas fa-star" style="margin-right:6px"></i><?= esc($captain['position']) ?>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Executive Officials -->
    <?php if (!empty($mainOfficials)): ?>
    <div style="display:flex;align-items:center;gap:12px;margin-bottom:20px">
        <h3 style="margin:0;font-size:18px;font-weight:700;color:var(--ink)">Executive Officials</h3>
        <div style="height:1px;flex:1;background:linear-gradient(to right, var(--border), transparent)"></div>
    </div>
    <div class="ds-grid-4" style="margin-bottom:40px;gap:20px">
        <?php foreach ($mainOfficials as $o): ?>
        <div class="ds-card" style="border:none;box-shadow:0 8px 20px rgba(0,0,0,0.03);border-radius:var(--r-md);text-align:center;transition:transform .2s;cursor:default" onmouseover="this.style.transform='translateY(-4px)'" onmouseout="this.style.transform='translateY(0)'">
            <div class="ds-card-body" style="padding:28px 20px">
                <div style="position:relative;display:inline-block;margin-bottom:16px">
                    <img src="<?= base_url('uploads/' . $o['photo']) ?>" onerror="this.src='<?= base_url('uploads/default.png') ?>'" style="width:72px;height:72px;border-radius:50%;object-fit:cover;border:3px solid var(--white);box-shadow:0 4px 12px rgba(var(--c-blue-rgb), 0.15)">
                </div>
                <h4 style="margin:0 0 6px;font-size:15px;font-weight:700;color:var(--ink)"><?= esc($o['full_name']) ?></h4>
                <span class="ds-badge ds-badge-blue" style="font-size:10.5px;padding:4px 10px"><?= esc($o['position']) ?></span>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- Kagawads -->
    <?php if (!empty($kagawads)): ?>
    <div style="display:flex;align-items:center;gap:12px;margin-bottom:20px">
        <h3 style="margin:0;font-size:18px;font-weight:700;color:var(--ink)">Sangguniang Barangay Members</h3>
        <div style="height:1px;flex:1;background:linear-gradient(to right, var(--border), transparent)"></div>
    </div>
    <div class="ds-grid-4" style="margin-bottom:24px;gap:20px">
        <?php foreach ($kagawads as $o): ?>
        <div class="ds-card" style="border:none;box-shadow:0 8px 20px rgba(0,0,0,0.03);border-radius:var(--r-md);text-align:center;transition:transform .2s;cursor:default" onmouseover="this.style.transform='translateY(-4px)'" onmouseout="this.style.transform='translateY(0)'">
            <div class="ds-card-body" style="padding:28px 20px">
                <div style="position:relative;display:inline-block;margin-bottom:16px">
                    <img src="<?= base_url('uploads/' . $o['photo']) ?>" onerror="this.src='<?= base_url('uploads/default.png') ?>'" style="width:72px;height:72px;border-radius:50%;object-fit:cover;border:3px solid var(--white);box-shadow:0 4px 12px rgba(var(--c-violet-rgb), 0.15)">
                </div>
                <h4 style="margin:0 0 6px;font-size:15px;font-weight:700;color:var(--ink)"><?= esc($o['full_name']) ?></h4>
                <span class="ds-badge ds-badge-violet" style="font-size:10.5px;padding:4px 10px"><?= esc($o['position']) ?></span>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php else: ?>
    <div class="ds-card" style="text-align:center;border:none;box-shadow:0 10px 30px rgba(0,0,0,0.03);border-radius:var(--r-lg)">
        <div class="ds-card-body" style="padding:64px 24px">
            <div style="width:80px;height:80px;border-radius:50%;background:var(--bg);color:var(--ink-soft);display:flex;align-items:center;justify-content:center;font-size:32px;margin:0 auto 20px">
                <i class="fas fa-users-slash"></i>
            </div>
            <h3 style="margin:0 0 8px;font-size:18px;font-weight:700;color:var(--ink)">No Officials Assigned</h3>
            <p style="color:var(--ink-muted);font-size:14px;margin:0 0 24px;max-width:400px;margin-inline:auto">The official directory is currently empty. You need to assign residents to these positions from the settings panel.</p>
            <?php if (session()->get('role') == 'admin'): ?>
                <a href="<?= base_url('admin/settings') ?>" class="ds-btn ds-btn-primary" style="height:40px;padding:0 24px;border-radius:20px"><i class="fas fa-cog"></i> Go to Settings</a>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
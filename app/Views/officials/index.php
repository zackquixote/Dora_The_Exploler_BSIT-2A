<?= $this->extend('theme/template') ?>
<?= $this->section('content') ?>

<style>
/* ── Officials page ── */
.off-page-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 20px;
}

/* Captain strip — horizontal, compact */
.off-captain-strip {
    display: flex;
    align-items: center;
    gap: 20px;
    background: linear-gradient(135deg, #064e3b 0%, #065f46 50%, #047857 100%);
    border-radius: var(--r);
    padding: 20px 28px;
    margin-bottom: 24px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 8px 24px rgba(4, 120, 87, 0.25);
}
.off-captain-strip::before {
    content: '';
    position: absolute;
    inset: 0;
    background: url("data:image/svg+xml,%3Csvg width='40' height='40' viewBox='0 0 40 40' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23fff' fill-opacity='0.04'%3E%3Ccircle cx='20' cy='20' r='2'/%3E%3C/g%3E%3C/svg%3E");
    pointer-events: none;
}
.off-captain-avatar {
    width: 72px;
    height: 72px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid rgba(255,255,255,0.3);
    flex-shrink: 0;
    position: relative;
    z-index: 1;
}
.off-captain-info {
    flex: 1;
    position: relative;
    z-index: 1;
}
.off-captain-label {
    font-size: 10px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .1em;
    color: rgba(255,255,255,0.6);
    margin-bottom: 4px;
}
.off-captain-name {
    font-size: 22px;
    font-weight: 800;
    color: #fff;
    margin: 0 0 6px;
    line-height: 1.1;
}
.off-captain-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    background: rgba(255,255,255,0.15);
    border: 1px solid rgba(255,255,255,0.25);
    color: #fff;
    font-size: 10.5px;
    font-weight: 700;
    padding: 3px 10px;
    border-radius: 20px;
    backdrop-filter: blur(4px);
}
.off-captain-star {
    position: absolute;
    right: 28px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 80px;
    color: rgba(255,255,255,0.04);
    z-index: 0;
    pointer-events: none;
}

/* Section divider */
.off-section-head {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 14px;
}
.off-section-head h3 {
    margin: 0;
    font-size: 11px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .08em;
    color: var(--ink-soft);
    white-space: nowrap;
}
.off-section-head::after {
    content: '';
    flex: 1;
    height: 1px;
    background: var(--border);
}

/* Official card — compact horizontal pill */
.off-card {
    display: flex;
    align-items: center;
    gap: 12px;
    background: var(--white);
    border-radius: var(--r-md);
    border: 1px solid var(--border);
    padding: 12px 14px;
    transition: box-shadow .15s, transform .15s;
    cursor: default;
}
.off-card:hover {
    box-shadow: 0 6px 18px rgba(0,0,0,0.07);
    transform: translateY(-2px);
}
.off-card-avatar {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    object-fit: cover;
    flex-shrink: 0;
    border: 2px solid var(--border);
}
.off-card-info {
    flex: 1;
    min-width: 0;
}
.off-card-name {
    font-size: 13px;
    font-weight: 700;
    color: var(--ink);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    margin-bottom: 3px;
}
.off-card-pos {
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .04em;
}

/* Grids */
.off-grid-exec {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 10px;
    margin-bottom: 24px;
}
.off-grid-kagawad {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 10px;
    margin-bottom: 24px;
}
@media (max-width: 1100px) {
    .off-grid-exec    { grid-template-columns: repeat(2, 1fr); }
    .off-grid-kagawad { grid-template-columns: repeat(3, 1fr); }
}
@media (max-width: 700px) {
    .off-grid-exec, .off-grid-kagawad { grid-template-columns: 1fr 1fr; }
    .off-captain-strip { flex-direction: column; text-align: center; }
    .off-captain-star  { display: none; }
}
</style>

<div class="bmis-content">

    <!-- Header -->
    <div class="off-page-header">
        <div style="display:flex;align-items:center;gap:14px">
            <div style="width:40px;height:40px;border-radius:10px;background:var(--c-amber-bg);color:var(--c-amber);display:flex;align-items:center;justify-content:center;font-size:17px">
                <i class="fas fa-user-tie"></i>
            </div>
            <div>
                <div style="font-size:20px;font-weight:800;color:var(--ink);line-height:1.1">Barangay Officials</div>
                <div style="font-size:12px;color:var(--ink-muted);margin-top:1px">Current elected and appointed leaders</div>
            </div>
        </div>
        <?php if (session()->get('role') == 'admin'): ?>
            <a href="<?= base_url('admin/settings') ?>" class="ds-btn ds-btn-ghost" style="height:34px;font-size:11.5px">
                <i class="fas fa-cog"></i> Manage
            </a>
        <?php endif; ?>
    </div>

    <?php if (!empty($officials)):
        $captain = null;
        $mainOfficials = [];
        $kagawads = [];

        foreach ($officials as $o) {
            $pos = strtolower($o['position']);
            if (strpos($pos, 'punong') !== false || strpos($pos, 'captain') !== false) {
                $captain = $o;
            } elseif (strpos($pos, 'kagawad') !== false) {
                $kagawads[] = $o;
            } else {
                $mainOfficials[] = $o;
            }
        }

        // Helper: resolve photo src
        $photoSrc = function($photo) {
            if (empty($photo)) return base_url('assets/img/default.png');
            // profile_picture paths are like "purok_masagana/filename.jpg"
            return base_url('uploads/' . $photo);
        };
    ?>

    <!-- ── Punong Barangay ── -->
    <?php if ($captain): ?>
    <div class="off-captain-strip">
        <img src="<?= $photoSrc($captain['photo']) ?>"
             onerror="this.src='<?= base_url('assets/img/default.png') ?>'"
             class="off-captain-avatar" alt="<?= esc($captain['full_name']) ?>">
        <div class="off-captain-info">
            <div class="off-captain-label">Punong Barangay</div>
            <div class="off-captain-name"><?= esc($captain['full_name']) ?></div>
            <span class="off-captain-badge">
                <i class="fas fa-star" style="font-size:9px"></i>
                <?= esc($captain['position']) ?>
            </span>
        </div>
        <i class="fas fa-star off-captain-star"></i>
    </div>
    <?php endif; ?>

    <!-- ── Executive Officials ── -->
    <?php if (!empty($mainOfficials)): ?>
    <div class="off-section-head">
        <h3>Executive Officials</h3>
    </div>
    <div class="off-grid-exec">
        <?php foreach ($mainOfficials as $o):
            $posLower = strtolower($o['position']);
            $badgeCls = 'ds-badge-blue';
            $dotColor = 'var(--c-blue)';
            if (strpos($posLower, 'secretary') !== false)  { $badgeCls = 'ds-badge-violet'; $dotColor = 'var(--c-violet)'; }
            if (strpos($posLower, 'treasurer') !== false)  { $badgeCls = 'ds-badge-teal';   $dotColor = 'var(--c-teal)'; }
            if (strpos($posLower, 'sk') !== false)         { $badgeCls = 'ds-badge-amber';  $dotColor = 'var(--c-amber)'; }
        ?>
        <div class="off-card">
            <img src="<?= $photoSrc($o['photo']) ?>"
                 onerror="this.src='<?= base_url('assets/img/default.png') ?>'"
                 class="off-card-avatar" alt="<?= esc($o['full_name']) ?>">
            <div class="off-card-info">
                <div class="off-card-name"><?= esc($o['full_name']) ?></div>
                <span class="ds-badge <?= $badgeCls ?>" style="font-size:9px"><?= esc($o['position']) ?></span>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- ── Kagawads ── -->
    <?php if (!empty($kagawads)): ?>
    <div class="off-section-head">
        <h3>Sangguniang Barangay Members</h3>
    </div>
    <div class="off-grid-kagawad">
        <?php foreach ($kagawads as $o): ?>
        <div class="off-card">
            <img src="<?= $photoSrc($o['photo']) ?>"
                 onerror="this.src='<?= base_url('assets/img/default.png') ?>'"
                 class="off-card-avatar" alt="<?= esc($o['full_name']) ?>">
            <div class="off-card-info">
                <div class="off-card-name"><?= esc($o['full_name']) ?></div>
                <span class="ds-badge ds-badge-violet" style="font-size:9px"><?= esc($o['position']) ?></span>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php else: ?>
    <!-- Empty state -->
    <div class="ds-card" style="text-align:center">
        <div class="ds-card-body" style="padding:56px 24px">
            <div style="width:64px;height:64px;border-radius:50%;background:var(--bg);color:var(--ink-soft);display:flex;align-items:center;justify-content:center;font-size:26px;margin:0 auto 16px">
                <i class="fas fa-users-slash"></i>
            </div>
            <div style="font-size:16px;font-weight:700;color:var(--ink);margin-bottom:6px">No Officials Assigned</div>
            <p style="color:var(--ink-muted);font-size:13px;margin:0 0 20px;max-width:360px;margin-inline:auto">
                The official directory is empty. Assign residents to positions from the settings panel.
            </p>
            <?php if (session()->get('role') == 'admin'): ?>
                <a href="<?= base_url('admin/settings') ?>" class="ds-btn ds-btn-primary" style="height:36px;padding:0 20px;border-radius:18px">
                    <i class="fas fa-cog"></i> Go to Settings
                </a>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

</div>

<?= $this->endSection() ?>

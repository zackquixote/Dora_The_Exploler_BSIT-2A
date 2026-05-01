<?php
$role = session()->get('role');
$template = ($role == 'admin') ? 'theme/admin/template' : 'theme/template';
?>
<?= $this->extend($template) ?>
<?= $this->section('content') ?>

<link rel="stylesheet" href="<?= base_url('assets/css/officials/style.css') ?>">

<div class="content-wrapper">

    <!-- AdminLTE standard header — styled via external CSS -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-sm-8">
                    <p class="sub-label">Barangay Government Unit</p>
                    <h1><?= esc($settings['barangay_name'] ?? 'Barangay') ?> Officials</h1>
                </div>
                <div class="col-sm-4 text-right">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Officials</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Content -->
    <section class="content">
        <div class="container-fluid">

            <!-- Admin notice -->
            <?php if (session()->get('role') == 'admin'): ?>
            <div class="adm-notice alert-dismissible">
                <a href="<?= base_url('admin/settings') ?>" class="btn-mgr">⚙ Manage</a>
                <span>Assign or update officials from the resident list.</span>
                <button type="button" class="close" data-dismiss="alert">×</button>
            </div>
            <?php endif; ?>

            <?php if (!empty($officials)): ?>
                <?php
                $captain = null; $mainOfficials = []; $kagawads = [];
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
                ?>

                <!-- Punong Barangay -->
                <?php if ($captain): ?>
                <div class="sec-lbl">Punong Barangay</div>
                <div class="card-captain">
                    <img src="<?= base_url('uploads/' . $captain['photo']) ?>"
                         class="cap-photo"
                         onerror="this.src='<?= base_url('uploads/default.png') ?>'">
                    <div>
                        <div class="cap-role">Head of Barangay</div>
                        <div class="cap-name"><?= esc($captain['full_name']) ?></div>
                        <span class="cap-badge"><?= esc($captain['position']) ?></span>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Executive Officials -->
                <?php if (!empty($mainOfficials)): ?>
                <div class="sec-lbl">Executive Officials</div>
                <div class="off-grid">
                    <?php foreach ($mainOfficials as $o): ?>
                    <div class="card-off">
                        <img src="<?= base_url('uploads/' . $o['photo']) ?>"
                             class="off-photo"
                             onerror="this.src='<?= base_url('uploads/default.png') ?>'">
                        <div class="off-name"><?= esc($o['full_name']) ?></div>
                        <span class="off-badge"><?= esc($o['position']) ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <!-- Kagawads -->
                <?php if (!empty($kagawads)): ?>
                <div class="sec-lbl">Sangguniang Barangay Members</div>
                <div class="off-grid">
                    <?php foreach ($kagawads as $o): ?>
                    <div class="card-off kg">
                        <img src="<?= base_url('uploads/' . $o['photo']) ?>"
                             class="off-photo"
                             onerror="this.src='<?= base_url('uploads/default.png') ?>'">
                        <div class="off-name"><?= esc($o['full_name']) ?></div>
                        <span class="off-badge"><?= esc($o['position']) ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-users-slash"></i>
                    <p>No officials assigned yet.</p>
                    <?php if (session()->get('role') == 'admin'): ?>
                        <a href="<?= base_url('admin/settings') ?>"
                           class="btn btn-primary mt-3"
                           style="font-family:'DM Sans',sans-serif;">Go to Settings</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

        </div>
    </section>
</div>

<?= $this->endSection() ?>
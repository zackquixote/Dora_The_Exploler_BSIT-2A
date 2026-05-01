<?php
$role = session()->get('role');
$template = ($role == 'admin') ? 'theme/admin/template' : 'theme/template';
?>
<?= $this->extend($template) ?>
<?= $this->section('content') ?>

<link rel="stylesheet" href="<?= base_url('assets/css/baranggay/style.css') ?>">

<div class="content-wrapper">

    <div class="content-header">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-sm-7">
                    <h1 class="m-0">Barangay Settings</h1>
                </div>
                <div class="col-sm-5 text-right">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="<?= base_url('admin/dashboard') ?>">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item active">Settings</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">

            <?php if (session()->getFlashdata('success')): ?>
            <div class="flash ok alert-dismissible">
                <i class="fas fa-check-circle"></i>
                <?= session()->getFlashdata('success') ?>
                <button type="button" class="close" data-dismiss="alert">×</button>
            </div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('error')): ?>
            <div class="flash err alert-dismissible">
                <i class="fas fa-exclamation-circle"></i>
                <?= session()->getFlashdata('error') ?>
                <button type="button" class="close" data-dismiss="alert">×</button>
            </div>
            <?php endif; ?>

            <form action="<?= base_url('admin/settings/update') ?>" method="POST">
                <?= csrf_field() ?>

                <div class="set-card">

                    <!-- Barangay Info -->
                    <div class="s-block">
                        <div class="s-title"><span class="dot"></span>Barangay Information</div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="f-label">Barangay Name</label>
                                <input type="text" name="barangay_name" class="f-ctrl"
                                       value="<?= esc($settings['barangay_name'] ?? '') ?>"
                                       placeholder="e.g. Tabu">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="f-label">Municipality</label>
                                <input type="text" name="municipality" class="f-ctrl"
                                       value="<?= esc($settings['municipality'] ?? '') ?>"
                                       placeholder="e.g. Ilog City">
                            </div>
                            <div class="col-md-6">
                                <label class="f-label">Province</label>
                                <input type="text" name="province" class="f-ctrl"
                                       value="<?= esc($settings['province'] ?? '') ?>"
                                       placeholder="e.g. Negros Occidental">
                            </div>
                            <div class="col-md-6">
                                <label class="f-label">Contact Number</label>
                                <input type="text" name="contact_number" class="f-ctrl"
                                       value="<?= esc($settings['contact_number'] ?? '') ?>"
                                       placeholder="e.g. 09XX-XXX-XXXX">
                            </div>
                        </div>
                    </div>

                    <!-- Assign Officials -->
                    <div class="s-block">
                        <div class="s-title"><span class="dot"></span>Assign Officials</div>

                        <div class="filter-bar">
                            <div>
                                <label class="f-label">
                                    <i class="fas fa-search mr-1"></i>Search Resident
                                </label>
                                <input type="text" id="resident_search" class="f-ctrl"
                                       placeholder="Type name...">
                            </div>
                            <div>
                                <label class="f-label">
                                    <i class="fas fa-map-marker-alt mr-1"></i>Filter by Purok
                                </label>
                                <select id="purok_filter" class="f-ctrl">
                                    <option value="all">All Puroks</option>
                                    <?php foreach($puroks as $p): ?>
                                        <option value="<?= esc($p['sitio']) ?>"><?= esc($p['sitio']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="s-sub">Executive Positions</div>
                        <div class="assign-grid mb">
                            <?php
                            $mainRoles = [
                                'captain_id'   => 'Punong Barangay',
                                'secretary_id' => 'Secretary',
                                'treasurer_id' => 'Treasurer',
                                'sk_chair_id'  => 'SK Chairperson'
                            ];
                            foreach ($mainRoles as $fieldName => $label):
                                $assignedId = $assignments[$label]['id'] ?? '';
                            ?>
                            <div class="assign-card">
                                <label class="f-label"><?= $label ?></label>
                                <select name="<?= $fieldName ?>" class="f-ctrl official-select">
                                    <option value="">— Select Resident —</option>
                                    <?php foreach ($residents as $r):
                                        $rFullName = $r['first_name'] . ' ' . $r['last_name'];
                                    ?>
                                        <option value="<?= $r['id'] ?>"
                                            data-name="<?= strtolower($rFullName) ?>"
                                            data-purok="<?= $r['sitio'] ?>"
                                            <?= $assignedId == $r['id'] ? 'selected' : '' ?>>
                                            <?= esc(ucwords($r['last_name'] . ', ' . $r['first_name'])) ?>
                                            (<?= esc($r['sitio']) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="s-sub">Sangguniang Barangay Members</div>
                        <div class="assign-grid">
                            <?php for($i = 1; $i <= 7; $i++):
                                $posName    = "Kagawad $i";
                                $assignedId = $assignments[$posName]['id'] ?? '';
                            ?>
                            <div class="assign-card">
                                <label class="f-label"><?= $posName ?></label>
                                <select name="kagawad_<?= $i ?>_id" class="f-ctrl official-select">
                                    <option value="">— Select Resident —</option>
                                    <?php foreach ($residents as $r):
                                        $rFullName = $r['first_name'] . ' ' . $r['last_name'];
                                    ?>
                                        <option value="<?= $r['id'] ?>"
                                            data-name="<?= strtolower($rFullName) ?>"
                                            data-purok="<?= $r['sitio'] ?>"
                                            <?= $assignedId == $r['id'] ? 'selected' : '' ?>>
                                            <?= esc(ucwords($r['last_name'] . ', ' . $r['first_name'])) ?>
                                            (<?= esc($r['sitio']) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <?php endfor; ?>
                        </div>
                    </div>

                    <div class="set-footer">
                        <button type="submit" class="btn-save">
                            <i class="fas fa-save mr-2"></i>Save Changes
                        </button>
                    </div>

                </div>
            </form>

        </div>
    </section>
</div>

<script src="<?= base_url('js/baranggay/baranggay.js') ?>"></script>

<?= $this->endSection() ?>
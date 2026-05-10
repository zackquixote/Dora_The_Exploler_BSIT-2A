<?php
$role     = session()->get('role');
$template = ($role == 'admin') ? 'theme/admin/template' : 'theme/template';
?>
<?= $this->extend($template) ?>
<?= $this->section('content') ?>

<div class="bmis-content">

    <!-- Header -->
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
        <h1 class="ds-page-title" style="margin:0;">
            <i class="fas fa-cogs" style="color:var(--c-blue);margin-right:8px;"></i> Barangay Settings
        </h1>
    </div>

    <!-- Flash Alerts -->
    <?php if (session()->getFlashdata('success')): ?>
        <div style="background:var(--c-teal-bg);color:var(--c-teal);padding:14px 18px;border-radius:var(--r);margin-bottom:20px;font-size:13px;font-weight:600;display:flex;align-items:center;gap:10px;">
            <i class="fas fa-check-circle" style="font-size:16px;"></i>
            <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div style="background:var(--c-rose-bg);color:var(--c-rose);padding:14px 18px;border-radius:var(--r);margin-bottom:20px;font-size:13px;font-weight:600;display:flex;align-items:center;gap:10px;">
            <i class="fas fa-exclamation-circle" style="font-size:16px;"></i>
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <form action="<?= base_url('admin/settings/update') ?>" method="POST">
        <?= csrf_field() ?>

        <div style="display:grid;grid-template-columns:1fr;gap:20px;">

            <!-- ── Basic Information ─────────────────────────────────────── -->
            <div class="ds-card">
                <div class="ds-card-head">
                    <div class="ds-card-title"><i class="fas fa-info-circle"></i> Basic Information</div>
                </div>
                <div class="ds-card-body">
                    <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(250px, 1fr));gap:16px;">
                        <div>
                            <label class="ds-input-label">Barangay Name</label>
                            <input type="text" name="barangay_name" class="ds-input"
                                   value="<?= esc($settings['barangay_name'] ?? '') ?>"
                                   placeholder="e.g. Tabu">
                        </div>
                        <div>
                            <label class="ds-input-label">Municipality</label>
                            <input type="text" name="municipality" class="ds-input"
                                   value="<?= esc($settings['municipality'] ?? '') ?>"
                                   placeholder="e.g. Ilog">
                        </div>
                        <div>
                            <label class="ds-input-label">Province</label>
                            <input type="text" name="province" class="ds-input"
                                   value="<?= esc($settings['province'] ?? '') ?>"
                                   placeholder="e.g. Negros Occidental">
                        </div>
                        <div>
                            <label class="ds-input-label">Contact Number</label>
                            <input type="text" name="contact_number" class="ds-input"
                                   value="<?= esc($settings['contact_number'] ?? '') ?>"
                                   placeholder="e.g. 0912-345-6789">
                        </div>
                    </div>
                </div>
            </div>

            <!-- ── Official Assignments ──────────────────────────────────── -->
            <div class="ds-card">
                <div class="ds-card-head" style="display:flex;flex-wrap:wrap;gap:12px;">
                    <div class="ds-card-title"><i class="fas fa-users-cog"></i> Active Officials</div>
                    <div style="display:flex;gap:12px;margin-left:auto;">
                        <input type="text" id="resident_search" class="ds-input"
                               style="height:34px;width:200px;" placeholder="Search names...">
                        <select id="purok_filter" class="ds-select" style="height:34px;width:150px;">
                            <option value="all">All Puroks</option>
                            <?php foreach ($puroks as $p): ?>
                                <option value="<?= esc($p['sitio']) ?>"><?= esc($p['sitio']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="ds-card-body">

                    <!-- Executive Positions -->
                    <div style="margin-bottom:12px;font-size:12px;font-weight:700;color:var(--c-navy);text-transform:uppercase;letter-spacing:1px;border-bottom:1px solid var(--border);padding-bottom:8px;">
                        Executive Positions
                    </div>
                    <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(250px, 1fr));gap:16px;margin-bottom:24px;">

                        <?php
                        /*
                         * IMPORTANT: form field names must NOT use 'captain_id',
                         * 'secretary_id', or 'treasurer_id' — those columns have
                         * been dropped from barangay_settings. The controller reads
                         * 'punong_barangay_id', 'secretary_id' (officials table only),
                         * 'treasurer_id' (officials table only), 'sk_chair_id'.
                         *
                         * We keep secretary_id / treasurer_id as form names because
                         * they refer to the officials table assignment, not the old
                         * barangay_settings columns. Only captain_id was renamed to
                         * punong_barangay_id to eliminate any ambiguity.
                         */
                        $mainRoles = [
                            'punong_barangay_id' => 'Punong Barangay',
                            'secretary_id'       => 'Secretary',
                            'treasurer_id'       => 'Treasurer',
                            'sk_chair_id'        => 'SK Chairperson',
                        ];
                        foreach ($mainRoles as $fieldName => $label):
                            $assignedId = $assignments[$label]['id'] ?? '';
                        ?>
                        <div>
                            <label class="ds-input-label" style="color:var(--c-blue);">
                                <i class="fas fa-user-tie"></i> <?= $label ?>
                            </label>
                            <select name="<?= $fieldName ?>" class="ds-select official-select">
                                <option value="">— Select Resident —</option>
                                <?php foreach ($residents as $r):
                                    $rFullName = $r['first_name'] . ' ' . $r['last_name'];
                                ?>
                                    <option value="<?= $r['id'] ?>"
                                            data-name="<?= strtolower($rFullName) ?>"
                                            data-purok="<?= $r['sitio'] ?>"
                                            <?= ($assignedId == $r['id']) ? 'selected' : '' ?>>
                                        <?= esc(ucwords($r['last_name'] . ', ' . $r['first_name'])) ?>
                                        (<?= esc($r['sitio']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Sangguniang Barangay Members -->
                    <div style="margin-bottom:12px;font-size:12px;font-weight:700;color:var(--c-navy);text-transform:uppercase;letter-spacing:1px;border-bottom:1px solid var(--border);padding-bottom:8px;">
                        Sangguniang Barangay Members
                    </div>
                    <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(250px, 1fr));gap:16px;">
                        <?php for ($i = 1; $i <= 7; $i++):
                            $posName    = "Kagawad $i";
                            $assignedId = $assignments[$posName]['id'] ?? '';
                        ?>
                        <div>
                            <label class="ds-input-label">
                                <i class="fas fa-user"></i> <?= $posName ?>
                            </label>
                            <select name="kagawad_<?= $i ?>_id" class="ds-select official-select">
                                <option value="">— Select Resident —</option>
                                <?php foreach ($residents as $r):
                                    $rFullName = $r['first_name'] . ' ' . $r['last_name'];
                                ?>
                                    <option value="<?= $r['id'] ?>"
                                            data-name="<?= strtolower($rFullName) ?>"
                                            data-purok="<?= $r['sitio'] ?>"
                                            <?= ($assignedId == $r['id']) ? 'selected' : '' ?>>
                                        <?= esc(ucwords($r['last_name'] . ', ' . $r['first_name'])) ?>
                                        (<?= esc($r['sitio']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php endfor; ?>
                    </div>

                </div><!-- /ds-card-body -->
            </div><!-- /ds-card -->

            <!-- Save Button -->
            <div style="text-align:right;">
                <button type="submit" class="ds-btn ds-btn-primary" style="padding:0 32px;">
                    <i class="fas fa-save"></i> Save Settings
                </button>
            </div>

        </div><!-- /grid -->
    </form>

</div><!-- /bmis-content -->

<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('resident_search');
    const purokSelect = document.getElementById('purok_filter');
    const allSelects  = document.querySelectorAll('.official-select');

    function filterOptions() {
        const query = searchInput.value.toLowerCase();
        const purok = purokSelect.value;

        allSelects.forEach(function (selectEl) {
            const currentValue = selectEl.value;
            let valueStillVisible = false;

            Array.from(selectEl.options).forEach(function (opt) {
                if (opt.value === '') {
                    opt.style.display = 'block';
                    if (currentValue === '') valueStillVisible = true;
                    return;
                }

                const optName  = opt.getAttribute('data-name')  || '';
                const optPurok = opt.getAttribute('data-purok') || '';

                const matchesSearch = query === '' || optName.includes(query);
                const matchesPurok  = purok === 'all' || optPurok === purok;

                if (matchesSearch && matchesPurok) {
                    opt.style.display = 'block';
                    if (opt.value === currentValue) valueStillVisible = true;
                } else {
                    opt.style.display = 'none';
                }
            });

            if (!valueStillVisible) {
                selectEl.value = '';
            }
        });
    }

    searchInput.addEventListener('input', filterOptions);
    purokSelect.addEventListener('change', filterOptions);
});
</script>

<?= $this->endSection() ?>
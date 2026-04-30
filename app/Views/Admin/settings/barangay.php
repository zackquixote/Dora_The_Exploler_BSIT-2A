<?php
$role = session()->get('role');
$template = ($role == 'admin') ? 'theme/admin/template' : 'theme/template';
?>
<?= $this->extend($template) ?>
<?= $this->section('content') ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6"><h1 class="m-0">Barangay Settings</h1></div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item active">Settings</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">

            <!-- SUCCESS message -->
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <?= session()->getFlashdata('success') ?>
                </div>
            <?php endif; ?>

            <!-- ERROR message (duplicate, database error, etc.) -->
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Barangay Profile & Assign Officials</h3>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('admin/settings/update') ?>" method="POST">
                        <?= csrf_field() ?>

                        <!-- BARANGAY INFORMATION -->
                        <h5 class="mb-2 text-primary">Barangay Information</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Barangay Name</label>
                                    <input type="text" name="barangay_name" class="form-control"
                                           value="<?= esc($settings['barangay_name'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Municipality</label>
                                    <input type="text" name="municipality" class="form-control"
                                           value="<?= esc($settings['municipality'] ?? '') ?>">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Province</label>
                                    <input type="text" name="province" class="form-control"
                                           value="<?= esc($settings['province'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Contact Number</label>
                                    <input type="text" name="contact_number" class="form-control"
                                           value="<?= esc($settings['contact_number'] ?? '') ?>">
                                </div>
                            </div>
                        </div>
                        <hr>

                        <!-- SEARCH & FILTER -->
                        <h5 class="mb-2 text-primary">Assign Officials</h5>
                        <div class="row mb-3 p-3 bg-light rounded border">
                            <div class="col-md-6">
                                <label><i class="fas fa-search"></i> Search Resident Name</label>
                                <input type="text" id="resident_search" class="form-control" placeholder="Type name...">
                            </div>
                            <div class="col-md-6">
                                <label><i class="fas fa-map-marker-alt"></i> Filter by Purok</label>
                                <select id="purok_filter" class="form-control">
                                    <option value="all">All Puroks</option>
                                    <?php foreach($puroks as $p): ?>
                                        <option value="<?= esc($p['sitio']) ?>"><?= esc($p['sitio']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <!-- MAIN OFFICIALS -->
                        <div class="row">
                            <?php
                            $mainRoles = [
                                'captain_id'    => 'Punong Barangay',
                                'secretary_id'  => 'Secretary',
                                'treasurer_id'  => 'Treasurer',
                                'sk_chair_id'   => 'SK Chairperson'
                            ];
                            foreach ($mainRoles as $fieldName => $label):
                                $assignedId = $assignments[$label]['id'] ?? '';
                            ?>
                            <div class="col-md-3 mb-3">
                                <div class="form-group">
                                    <label><?= $label ?></label>
                                    <select name="<?= $fieldName ?>" class="form-control official-select">
                                        <option value="">-- Select Resident --</option>
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
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- 7 KAGAWADS -->
                        <h6 class="text-primary mt-3">Sangguniang Barangay Members (Kagawad)</h6>
                        <div class="row">
                            <?php for($i = 1; $i <= 7; $i++):
                                $posName = "Kagawad $i";
                                $assignedId = $assignments[$posName]['id'] ?? '';
                            ?>
                            <div class="col-md-4 mb-3">
                                <div class="form-group">
                                    <label><?= $posName ?></label>
                                    <select name="kagawad_<?= $i ?>_id" class="form-control official-select">
                                        <option value="">-- Select Resident --</option>
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
                            </div>
                            <?php endfor; ?>
                        </div>

                        <br>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Live filtering of dropdowns -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    const searchInput = document.getElementById('resident_search');
    const purokSelect = document.getElementById('purok_filter');
    const dropdowns = document.querySelectorAll('.official-select');

    function filterDropdowns() {
        const searchTerm = searchInput.value.toLowerCase();
        const selectedPurok = purokSelect.value;

        dropdowns.forEach(dropdown => {
            const currentSelectedId = dropdown.value;
            Array.from(dropdown.options).forEach(option => {
                if (option.value === "") return;
                const residentName = option.getAttribute('data-name');
                const residentPurok = option.getAttribute('data-purok');
                const matchesSearch = residentName.includes(searchTerm);
                const matchesPurok = (selectedPurok === 'all') || (residentPurok === selectedPurok);
                option.style.display = (matchesSearch && matchesPurok) ? 'block' : 'none';
            });

            // Always keep the currently selected option visible
            if (currentSelectedId) {
                const selectedOption = dropdown.querySelector(`option[value="${currentSelectedId}"]`);
                if (selectedOption) selectedOption.style.display = 'block';
            }
        });
    }

    searchInput.addEventListener('keyup', filterDropdowns);
    purokSelect.addEventListener('change', filterDropdowns);
});
</script>

<?= $this->endSection() ?>
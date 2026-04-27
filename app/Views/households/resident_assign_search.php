<?php
// ---------------------------------------------------------
// SMART THEME LOADER
// ---------------------------------------------------------
 $role = strtolower(session()->get('role') ?? 'staff');
 $template = ($role == 'admin') ? 'theme/admin/template' : 'theme/template';
?>

<?= $this->extend($template) ?>

<?= $this->section('content') ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Assign Existing Residents</h1>
                    <p class="text-muted small">Target: Household #<?= esc($household_id) ?></p>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('households') ?>">Households</a></li>
                        <li class="breadcrumb-item active">Assign Members</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            
            <!-- ALERTS -->
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong><i class="fas fa-check-circle"></i> Done!</strong> <?= session()->getFlashdata('success') ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
            <?php endif; ?>

            <!-- TOP CARD: Filters & Search -->
            <div class="card mb-4">
                <div class="card-header bg-white py-2">
                    <div class="row">
                        
                        <!-- FILTER BY LOCATION -->
                        <div class="col-md-4 border-right">
                            <small class="text-muted text-uppercase font-weight-bold">Filter Candidates by Location</small>
                            <div class="input-group input-group-sm mt-1">
                                <select id="filter_purok" class="form-control" onchange="loadHouseholds('filter')">
                                    <option value="">All Purok</option>
                                    <?php 
                                    $puroks = ['Purok Malipayon', 'Purok Masagana', 'Purok Cory', 'Purok Kawayan', 'Purok Pagla-um'];
                                    foreach ($puroks as $p): ?>
                                        <option value="<?= $p ?>" <?= ($filterPurok == $p) ? 'selected' : '' ?>><?= $p ?></option>
                                    <?php endforeach; ?>
                                </select>
                                
                                <select id="filter_household_id" name="filter_household_id" class="form-control">
                                    <option value="">All Houses</option>
                                    <!-- JS loads houses here if purok is selected -->
                                </select>
                            </div>
                        </div>

                        <!-- SEARCH BY NAME -->
                        <div class="col-md-8">
                            <small class="text-muted text-uppercase font-weight-bold">Search by Name</small>
                            <form action="" method="get" class="input-group input-group-sm mt-1">
                                <input type="hidden" name="household_id" value="<?= esc($household_id) ?>">
                                <input type="hidden" name="filter_purok" id="hidden_purok" value="<?= esc($filterPurok) ?>">
                                <input type="hidden" name="filter_household_id" id="hidden_household" value="<?= esc($filterHouseId) ?>">
                                
                                <input type="text" name="q" class="form-control" placeholder="Search name..." value="<?= esc($keyword) ?>">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-secondary"><i class="fas fa-search"></i></button>
                                </div>
                                <?php if(!empty($filterPurok) || !empty($filterHouseId) || !empty($keyword)): ?>
                                <div class="input-group-append">
                                    <a href="<?= base_url('resident/assign-search?household_id='.$household_id) ?>" class="btn btn-danger"><i class="fas fa-times"></i></a>
                                </div>
                                <?php endif; ?>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- MAIN CARD: Resident List -->
            <form action="<?= base_url('resident/assignBulk') ?>" method="post" id="bulkForm">
                <input type="hidden" name="target_household_id" value="<?= esc($household_id) ?>">
                
                <div class="card">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h3 class="card-title m-0">Available Residents</h3>
                        
                        <button type="button" onclick="validateAndSubmit()" class="btn btn-success btn-sm">
                            <i class="fas fa-users"></i> Assign Selected (Checked)
                        </button>
                    </div>
                    
                    <div class="card-body table-responsive p-0">
                        <?php if (empty($residents)): ?>
                            <div class="p-5 text-center text-muted">
                                <i class="fas fa-search fa-3x mb-3 opacity-50"></i>
                                <p>No residents found matching criteria.</p>
                            </div>
                        <?php else: ?>
                            <table class="table table-hover text-nowrap table-bordered">
                                <thead class="thead-light">
                                    <tr>
                                        <th width="50" class="text-center">Select</th>
                                        <th>Resident</th>
                                        <th>Current Info</th>
                                        <th width="250">Relationship to Head</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($residents as $r): 
                                        $age = '';
                                        if (!empty($r['birthdate'])) {
                                            $age = (new DateTime($r['birthdate']))->diff(new DateTime())->y;
                                        }
                                        
                                        // Image Logic
                                        $profileSrc = 'https://ui-avatars.com/api/?name='.urlencode($r['first_name'] . ' ' . $r['last_name']).'&background=random&color=fff&size=40';
                                        if (!empty($r['profile_picture'])) {
                                            $profileSrc = base_url('uploads/' . $r['profile_picture']);
                                        }
                                    ?>
                                    <tr>
                                        <!-- Checkbox -->
                                        <td class="text-center align-middle">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="selected_residents[]" value="<?= $r['id'] ?>" id="check_<?= $r['id'] ?>" onchange="toggleRelation('<?= $r['id'] ?>')">
                                            </div>
                                        </td>

                                        <!-- Info -->
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="<?= $profileSrc ?>" class="img-circle elevation-2 mr-3" style="width:35px; height:35px; object-fit:cover;" alt="">
                                                <div>
                                                    <strong><?= esc($r['first_name'] . ' ' . $r['last_name']) ?></strong>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Current Status -->
                                        <td>
                                            <span class="badge badge-info"><?= esc($r['sitio']) ?></span>
                                            <?php if($r['household_id']): ?>
                                                <br><span class="text-muted small">Household #<?= $r['household_id'] ?></span>
                                            <?php else: ?>
                                                <br><span class="text-muted small">No Household</span>
                                            <?php endif; ?>
                                        </td>

                                        <!-- Relationship Dropdown (Initially Disabled) -->
                                        <td>
                                            <select name="relationships[<?= $r['id'] ?>]" id="rel_<?= $r['id'] ?>" class="form-control form-control-sm" disabled>
                                                <option value="">Select Relationship...</option>
                                                <option value="Head">Head</option>
                                                <option value="Spouse">Spouse</option>
                                                <option value="Son">Son</option>
                                                <option value="Daughter">Daughter</option>
                                                <option value="Father">Father</option>
                                                <option value="Mother">Mother</option>
                                                <option value="Brother">Brother</option>
                                                <option value="Sister">Sister</option>
                                                <option value="Grandchild">Grandchild</option>
                                                <option value="Other">Other</option>
                                            </select>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </div>
                    
                    <div class="card-footer">
                        <?= $pager->links() ?>
                    </div>
                </div>
            </form>
        </div>
    </section>
</div>

<script>
// 1. Toggle Dropdown enable/disable based on checkbox
function toggleRelation(id) {
    var checkbox = document.getElementById('check_' + id);
    var dropdown = document.getElementById('rel_' + id);
    
    if (checkbox.checked) {
        dropdown.disabled = false;
        dropdown.focus();
    } else {
        dropdown.disabled = true;
        dropdown.value = ""; // Reset value
    }
}

// 2. Validate before submitting
function validateAndSubmit() {
    var checkboxes = document.querySelectorAll('input[name="selected_residents[]"]:checked');
    
    if (checkboxes.length === 0) {
        alert("Please select at least one resident.");
        return;
    }

    var valid = true;
    checkboxes.forEach(function(box) {
        var id = box.value;
        var dropdown = document.getElementById('rel_' + id);
        if (dropdown.value === "") {
            valid = false;
            alert("Please select a relationship for " + dropdown.parentElement.parentElement.parentElement.querySelector('strong').innerText);
        }
    });

    if (valid) {
        document.getElementById('bulkForm').submit();
    }
}

// 3. Filter Logic: Load Houses based on Purok
 $(document).ready(function() {
    // Set initial house if it exists in URL
    var initialPurok = "<?= esc($filterPurok) ?>";
    var initialHouse = "<?= esc($filterHouseId) ?>";
    
    if (initialPurok) {
        loadHouseholds('filter', initialHouse);
    }

    // Listen for changes on the Filter Purok dropdown
    $('#filter_purok').on('change', function() {
        var purok = $(this).val();
        $('#hidden_purok').val(purok);
        $('#hidden_household').val(''); // Reset house when purok changes
        loadHouseholds('filter');
    });

    // Listen for changes on the Filter House dropdown
    $('#filter_household_id').on('change', function() {
        $('#hidden_household').val($(this).val());
    });
});

function loadHouseholds(type, preSelectId = null) {
    var purok = $('#filter_purok').val();
    var targetId = (type === 'filter') ? '#filter_household_id' : '';
    
    if (!purok) {
        $(targetId).html('<option value="">All Houses</option>');
        return;
    }

    $.ajax({
        url: "<?= base_url('resident/getHouseholdsBySitio') ?>",
        type: "GET",
        data: { sitio: purok },
        dataType: "json",
        success: function(data) {
            $(targetId).empty().append('<option value="">All Houses</option>');
            $.each(data.data, function(i, item) {
                var selected = (preSelectId && item.id == preSelectId) ? 'selected' : '';
                $(targetId).append('<option value="'+ item.id +'" '+ selected +'>Household #'+ item.household_no +'</option>');
            });
        }
    });
}
</script>

<?= $this->endSection() ?>
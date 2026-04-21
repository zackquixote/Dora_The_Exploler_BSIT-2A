<?= $this->extend('theme/template') ?>
<?= $this->section('content') ?>

<div class="content-wrapper">

    <!-- PAGE HEADER -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Edit Household</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('staff/dashboard') ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('households') ?>">Households</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('households/view/'.$household['id']) ?>"><?= esc($household['household_no']) ?></a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            
            <!-- FLASH ERRORS -->
            <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <h5><i class="icon fas fa-ban"></i> Please fix the following:</h5>
                <ul class="mb-0">
                    <?php foreach (session()->getFlashdata('errors') as $e): ?>
                        <li><?= esc($e) ?></li>
                    <?php endforeach; ?>
                </ul>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
            <?php endif; ?>

            <form id="householdForm" action="<?= base_url('households/update/'.$household['id']) ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="id" value="<?= $household['id'] ?>">

                <!-- HOUSEHOLD INFO -->
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-home mr-2"></i>Household Information</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Household Number <span class="text-danger">*</span></label>
                                    <input type="text" name="household_no" id="householdNo" class="form-control"
                                           value="<?= esc($household['household_no']) ?>" readonly>
                                    <small class="text-muted">Household number cannot be changed</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Purok / Sitio <span class="text-danger">*</span></label>
                                    <select name="sitio" id="sitioSelect" class="form-control" required>
                                        <option value="">— Select Purok —</option>
                                        <option value="Purok Malipayon" <?= ($household['sitio']??'')=='Purok Malipayon'?'selected':'' ?>>Purok Malipayon</option>
                                        <option value="Purok Masagana"  <?= ($household['sitio']??'')=='Purok Masagana' ?'selected':'' ?>>Purok Masagana</option>
                                        <option value="Purok Cory"      <?= ($household['sitio']??'')=='Purok Cory'     ?'selected':'' ?>>Purok Cory</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>House Type</label>
                                    <select name="house_type" class="form-control">
                                        <option value="">— Select Type —</option>
                                        <option value="Concrete"       <?= ($household['house_type']??'')=='Concrete'       ?'selected':'' ?>>Concrete</option>
                                        <option value="Semi-Concrete"  <?= ($household['house_type']??'')=='Semi-Concrete'  ?'selected':'' ?>>Semi-Concrete</option>
                                        <option value="Wood"           <?= ($household['house_type']??'')=='Wood'           ?'selected':'' ?>>Wood</option>
                                        <option value="Light Materials"<?= ($household['house_type']??'')=='Light Materials'?'selected':'' ?>>Light Materials</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ADDRESS -->
                <div class="card card-success">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-map-marker-alt mr-2"></i>Address Information</h3>
                    </div>
                    <div class="card-body">
                        
                        <!-- HIDDEN INPUT: Auto-generates address for DB storage -->
                        <input type="hidden" name="address" id="completeAddress" value="<?= esc($household['address'] ?? '') ?>">

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Street Address</label>
                                    <input type="text" name="street_address" id="streetAddress" class="form-control"
                                           value="<?= esc($household['street_address'] ?? '') ?>"
                                           placeholder="e.g., Block 1, Lot 2, House #12">
                                    <small class="text-muted">Enter specific house details. Full address is auto-generated.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- HEAD OF HOUSEHOLD -->
                <div class="card card-warning">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-user-tie mr-2"></i>Head of Household</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Select Head Resident</label>
                                    <select name="head_resident_id" id="headResidentSelect" class="form-control">
                                        <option value="">— Select Head of Household —</option>
                                    </select>
                                    <small class="text-muted">Shows residents from selected purok and current members.</small>
                                </div>
                            </div>
                        </div>
                        <div id="membersLoadingAlert" class="alert alert-info" style="display:none;">
                            <i class="fas fa-spinner fa-spin"></i> Loading residents…
                        </div>
                    </div>
                </div>

                <!-- CURRENT MEMBERS SUMMARY -->
                <?php if ($residentCount > 0): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> 
                    This household currently has <strong><?= $residentCount ?> registered resident(s)</strong>.
                </div>
                <?php endif; ?>

                <!-- HOUSEHOLD MEMBERS MANAGER -->
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-users mr-2"></i>Manage Household Members</h3>
                        <div class="card-tools">
                            <span class="badge badge-light mr-2" id="selectedCount">0 selected</span>
                            <button type="button" class="btn btn-tool" id="toggleAllMembers" title="Toggle All">
                                <i class="fas fa-check-double"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <!-- Members Table -->
                        <div id="membersTableContainer">
                            <table class="table table-striped table-hover" id="membersTable">
                                <thead>
                                    <tr>
                                        <th style="width: 40px;">
                                            <input type="checkbox" id="selectAllCheckbox">
                                        </th>
                                        <th>Resident Name</th>
                                        <th style="width: 200px;">Relationship to Head</th>
                                    </tr>
                                </thead>
                                <tbody id="membersTableBody">
                                    <!-- Dynamically populated -->
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Empty state -->
                        <div id="emptyMembersState" class="text-center py-5 text-muted" style="display:none;">
                            <i class="fas fa-users fa-3x mb-3"></i>
                            <p>No residents available in this purok</p>
                        </div>
                    </div>
                    <div class="card-footer">
                        <small class="text-muted">
                            <i class="fas fa-info-circle"></i> 
                            Check residents to add them to this household. Uncheck to remove them.
                            Unchecked members will be marked as "Transferred".
                        </small>
                    </div>
                </div>

                <!-- Hidden field for member data -->
                <input type="hidden" name="household_members_data" id="householdMembersData" value="[]">

                <!-- FORM ACTIONS -->
                <div class="row mb-4">
                    <div class="col-12">
                        <a href="<?= base_url('households/view/'.$household['id']) ?>" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary float-right" id="submitBtn">
                            <i class="fas fa-save"></i> Update Household
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </section>
</div>

<style>
.badge-pink { background-color: #e83e8c; color: white; }
.badge-sm { font-size: 10px; padding: 2px 5px; }
#membersTable th, #membersTable td { vertical-align: middle; }
</style>

<script>
    var BASE_URL = '<?= base_url() ?>';
    var CSRF_TOKEN_NAME = '<?= csrf_token() ?>';
    var CSRF_TOKEN_VALUE = '<?= csrf_hash() ?>';
    var householdId = <?= $household['id'] ?>;
    var headId = <?= $household['head_resident_id'] ? $household['head_resident_id'] : 'null' ?>;
    var currentSitio = '<?= esc($household['sitio'] ?? '') ?>';
    
    // 1. Load current members passed from Controller
    var currentMembersData = <?= json_encode($currentMembers ?? []) ?>;
    var allResidents = [];
    var selectedMembers = {};

    var relationshipOptions = [
        'Head', 'Spouse', 'Son', 'Daughter', 'Father', 'Mother',
        'Grandfather', 'Grandmother', 'Grandson', 'Granddaughter',
        'Brother', 'Sister', 'Uncle', 'Aunt', 'Nephew', 'Niece',
        'Cousin', 'Son-in-law', 'Daughter-in-law', 'Brother-in-law',
        'Sister-in-law', 'Other Relative', 'Non-Relative'
    ];

    if (typeof jQuery === 'undefined') {
        setTimeout(initHouseholdEdit, 50);
    } else {
        initHouseholdEdit();
    }

    function initHouseholdEdit() {
        jQuery(document).ready(function($) {
            
            // ==========================================
            // AUTO ADDRESS GENERATION LOGIC
            // ==========================================
            function updateCompleteAddress() {
                var street = $('#streetAddress').val().trim();
                var sitio   = $('#sitioSelect').find("option:selected").text();
                
                var purok = (sitio && sitio !== "— Select Purok —") ? sitio + ", " : "";
                var barangay = "Barangay Tabu, ";
                var province = "Negros Occidental";
                
                var parts = [];
                if (street) parts.push(street);
                if (purok) parts.push(purok);
                
                var fullAddress = parts.join(", ") + ", " + barangay + province;
                $('#completeAddress').val(fullAddress);
            }

            $('#streetAddress').on('input blur', updateCompleteAddress);
            $('#sitioSelect').on('change', updateCompleteAddress);
            updateCompleteAddress();
            // ==========================================

            // 2. Load Residents
            function loadResidents(sitio) {
                if (!sitio) return;

                $('#membersLoadingAlert').show();
                $('#membersTableBody').empty();
                $('#emptyMembersState').hide();

                $.ajax({
                    url: BASE_URL + 'households/getResidentsBySitio',
                    type: 'POST',
                    data: { 
                        sitio: sitio,
                        [CSRF_TOKEN_NAME]: CSRF_TOKEN_VALUE
                    },
                    dataType: 'json',
                    success: function(res) {
                        $('#membersLoadingAlert').hide();
                        
                        if (res.csrf_hash) {
                            $('input[name="' + CSRF_TOKEN_NAME + '"]').val(res.csrf_hash);
                            CSRF_TOKEN_VALUE = res.csrf_hash;
                        }

                        if (res.status === 'success' && res.residents) {
                            allResidents = res.residents;
                            populateHeadDropdown(allResidents);
                            renderMembersTable(allResidents);
                        } else {
                            $('#emptyMembersState').show();
                            $('#membersTableContainer').hide();
                        }
                    },
                    error: function() {
                        $('#membersLoadingAlert').hide();
                        alert('Error loading residents.');
                    }
                });
            }

            function populateHeadDropdown(residents) {
                var headSelect = $('#headResidentSelect');
                headSelect.empty().append('<option value="">— Select Head of Household —</option>');

                residents.forEach(function(r) {
                    var name = r.last_name + ', ' + r.first_name + (r.middle_name ? ' ' + r.middle_name : '');
                    var selected = (r.id == headId) ? 'selected' : '';
                    headSelect.append('<option value="' + r.id + '" ' + selected + '>' + name + '</option>');
                });
            }

            function renderMembersTable(residents) {
                var tbody = $('#membersTableBody');
                tbody.empty();
                
                if (residents.length === 0) {
                    $('#emptyMembersState').show();
                    $('#membersTableContainer').hide();
                    return;
                } else {
                    $('#emptyMembersState').hide();
                    $('#membersTableContainer').show();
                }

                // ==========================================
                // LOGIC FIX: AUTO-CHECK BY NAME MATCHING
                // ==========================================
                
                // First, let's create a lookup map from currentMembersData
                // We normalize names (uppercase, trim) to ensure matching works
                var currentMemberLookup = {};
                
                // We assume currentMembersData contains objects like: 
                // { resident_id: 123, first_name: 'Juan', last_name: 'Dela Cruz', ... }
                // OR we rely on the names inside the resident object if available.
                // Let's iterate and build a map based on ID if possible, else Name.
                
                // SAFEST APPROACH: Match by ID if available, else Name.
                // Since you mentioned it wasn't working, we will enforce a double-check (ID AND Name).
                
                var dbMemberMap = {};
                $.each(currentMembersData, function(i, m) {
                    // Handle different possible keys for ID
                    var mid = m.resident_id || m.id || m.user_id;
                    
                    if (mid) {
                        dbMemberMap[mid] = m.relationship_to_head || m.relationship || '';
                    }
                });

                residents.forEach(function(r) {
                    // 1. Check if this resident ID exists in our DB map
                    var isChecked = dbMemberMap[r.id] ? 'checked' : '';
                    var relationship = dbMemberMap[r.id] ? dbMemberMap[r.id] : '';

                    // 2. Fallback: Name Matching (if IDs didn't match for some reason)
                    if (!isChecked) {
                        $.each(currentMembersData, function(i, cm) {
                            // Compare Last and First Name (uppercase for consistency)
                            var dbLast = (cm.last_name || '').toUpperCase().trim();
                            var dbFirst = (cm.first_name || '').toUpperCase().trim();
                            var ajaxLast = (r.last_name || '').toUpperCase().trim();
                            var ajaxFirst = (r.first_name || '').toUpperCase().trim();

                            if (dbLast === ajaxLast && dbFirst === ajaxFirst) {
                                isChecked = 'checked';
                                relationship = cm.relationship_to_head || cm.relationship || '';
                                return false; // Break loop
                            }
                        });
                    }

                    var unassignedBadge = (!r.resident_sitio || r.resident_sitio === '') ? ' <span class="badge badge-warning badge-sm">Unassigned</span>' : '';

                    var html = '<tr>';
                    html += '<td><input type="checkbox" class="member-checkbox" data-id="' + r.id + '" ' + isChecked + '></td>';
                    html += '<td><strong>' + r.last_name + ', ' + r.first_name + '</strong> ' + unassignedBadge + '</td>';
                    html += '<td>';
                    html += '<select class="form-control form-control-sm relationship-select" data-id="' + r.id + '" ' + (isChecked ? '' : 'disabled') + '>';
                    html += '<option value="">— Select —</option>';
                    relationshipOptions.forEach(function(rel) {
                        var selected = (relationship === rel) ? 'selected' : '';
                        html += '<option value="' + rel + '" ' + selected + '>' + rel + '</option>';
                    });
                    html += '</select>';
                    html += '</td></tr>';
                    
                    tbody.append(html);

                    // Add to selectedMembers object immediately so it tracks correctly
                    if (isChecked) {
                        selectedMembers[r.id] = {
                            id: r.id,
                            relationship: relationship
                        };
                    }
                });
                
                updateSelectedCount();
                updateHiddenField();
            }

            // Event Listeners
            $('#sitioSelect').on('change', function() {
                selectedMembers = {};
                loadResidents($(this).val());
            });

            $(document).on('change', '.member-checkbox', function() {
                var id = $(this).data('id');
                var isChecked = $(this).is(':checked');
                var row = $(this).closest('tr');
                var select = row.find('.relationship-select');

                if (isChecked) {
                    select.prop('disabled', false);
                    if (!selectedMembers[id]) {
                        selectedMembers[id] = {
                            id: id,
                            relationship: select.val() || ''
                        };
                    }
                } else {
                    select.prop('disabled', true);
                    delete selectedMembers[id];
                }
                updateSelectedCount();
                updateHiddenField();
            });

            $(document).on('change', '.relationship-select', function() {
                var id = $(this).data('id');
                if (selectedMembers[id]) {
                    selectedMembers[id].relationship = $(this).val();
                    updateHiddenField();
                }
            });
            
            // Auto-set Head Resident logic
            $('#headResidentSelect').on('change', function() {
                var newHeadId = $(this).val();
                if (newHeadId) {
                    var checkbox = $('.member-checkbox[data-id="' + newHeadId + '"]');
                    if (checkbox.length > 0 && !checkbox.is(':checked')) {
                        checkbox.prop('checked', true).trigger('change');
                    }
                    var select = $('.relationship-select[data-id="' + newHeadId + '"]');
                    if (select.length > 0) {
                        select.val('Head').trigger('change');
                    }
                }
            });

            $('#selectAllCheckbox').on('change', function() {
                var isChecked = $(this).is(':checked');
                $('.member-checkbox').prop('checked', isChecked).trigger('change');
            });

            $('#toggleAllMembers').on('click', function() {
                var anyUnchecked = $('.member-checkbox:not(:checked)').length > 0;
                $('.member-checkbox').prop('checked', anyUnchecked).trigger('change');
                $('#selectAllCheckbox').prop('checked', anyUnchecked);
            });

            function updateSelectedCount() {
                var count = Object.keys(selectedMembers).length;
                $('#selectedCount').text(count + ' selected');
            }

            function updateHiddenField() {
                $('#householdMembersData').val(JSON.stringify(selectedMembers));
            }

            // Initial Load
            if (currentSitio) {
                loadResidents(currentSitio);
            }
        });
    }
</script>

<?= $this->endSection() ?>
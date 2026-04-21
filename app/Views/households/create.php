<?= $this->extend('theme/template') ?>
<?= $this->section('content') ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Add New Household</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('staff/dashboard') ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('households') ?>">Households</a></li>
                        <li class="breadcrumb-item active">Add New</li>
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
                <h5><i class="icon fas fa-ban"></i> Please fix the following errors:</h5>
                <ul class="mb-0">
                    <?php foreach (session()->getFlashdata('errors') as $e): ?>
                        <li><?= esc($e) ?></li>
                    <?php endforeach; ?>
                </ul>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
            <?php endif; ?>

            <form id="householdForm" action="<?= base_url('households/store') ?>" method="POST">
                <?= csrf_field() ?>

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
                                    <div class="input-group">
                                        <input type="text" name="household_no" id="householdNo" class="form-control"
                                               value="<?= old('household_no', $generatedHouseholdNo ?? '') ?>"
                                               placeholder="e.g., HH-2024-001">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-outline-secondary" id="generateHouseholdNo" title="Generate new household number">
                                                <i class="fas fa-sync-alt"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-success" id="checkHouseholdNo" title="Check availability">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <small class="text-muted" id="householdNoFeedback">Auto-generated unique number (you can edit)</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Purok / Sitio <span class="text-danger">*</span></label>
                                    <select name="sitio" id="sitioSelect" class="form-control" required>
                                        <option value="">— Select Purok —</option>
                                        <option value="Purok Malipayon" <?= old('sitio')=='Purok Malipayon'?'selected':'' ?>>Purok Malipayon</option>
                                        <option value="Purok Masagana"  <?= old('sitio')=='Purok Masagana' ?'selected':'' ?>>Purok Masagana</option>
                                        <option value="Purok Cory"      <?= old('sitio')=='Purok Cory'     ?'selected':'' ?>>Purok Cory</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>House Type</label>
                                    <select name="house_type" class="form-control">
                                        <option value="">— Select Type —</option>
                                        <option value="Concrete"        <?= old('house_type')=='Concrete'       ?'selected':'' ?>>Concrete</option>
                                        <option value="Semi-Concrete"   <?= old('house_type')=='Semi-Concrete'  ?'selected':'' ?>>Semi-Concrete</option>
                                        <option value="Wood"            <?= old('house_type')=='Wood'            ?'selected':'' ?>>Wood</option>
                                        <option value="Light Materials" <?= old('house_type')=='Light Materials'?'selected':'' ?>>Light Materials</option>
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
                        
                        <!-- HIDDEN INPUT: Generates address in background for DB storage -->
                        <input type="hidden" name="address" id="completeAddress" value="<?= old('address') ?>">

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Street Address</label>
                                    <input type="text" name="street_address" id="streetAddress" class="form-control"
                                           value="<?= old('street_address') ?>"
                                           placeholder="e.g., Block 1, Lot 2, House #12">
                                    <small class="text-muted">Enter specific house details (Block/Lot #). The full address will be auto-generated.</small>
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
                                    <select name="head_resident_id" id="headResidentSelect" class="form-control" disabled>
                                        <option value="">— Select Purok/Sitio first —</option>
                                    </select>
                                    <small class="text-muted">Shows residents from selected purok and unassigned residents.</small>
                                </div>
                            </div>
                        </div>
                        <div id="loadingAlert" class="alert alert-info" style="display:none;">
                            <i class="fas fa-spinner fa-spin"></i> Loading residents from selected purok…
                        </div>
                        <div id="noResidentsAlert" class="alert alert-warning" style="display:none;">
                            <i class="fas fa-exclamation-triangle"></i> 
                            No residents found in this purok. 
                            <a href="<?= base_url('resident/create') ?>" class="alert-link">Add a resident first →</a>
                        </div>
                    </div>
                </div>

                <!-- HOUSEHOLD MEMBERS MANAGER -->
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-users mr-2"></i>Household Members</h3>
                        <div class="card-tools">
                            <span class="badge badge-light mr-2" id="selectedCount">0 selected</span>
                            <button type="button" class="btn btn-tool" id="toggleAllMembers" title="Toggle All" disabled>
                                <i class="fas fa-check-double"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div id="membersLoadingAlert" class="alert alert-info m-3" style="display:none;">
                            <i class="fas fa-spinner fa-spin"></i> Loading members list…
                        </div>
                        <div id="noResidentsWarning" class="alert alert-warning m-3" style="display:none;">
                            <i class="fas fa-exclamation-triangle"></i> 
                            No residents found in this purok. 
                            <a href="<?= base_url('resident/create') ?>" class="alert-link">Add a resident first →</a>
                        </div>
                        
                        <!-- Members Table -->
                        <div id="membersTableContainer" style="display:none;">
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
                        <div id="emptyMembersState" class="text-center py-5 text-muted">
                            <i class="fas fa-users fa-3x mb-3"></i>
                            <p>Select a Purok above to load residents</p>
                        </div>
                    </div>
                    <div class="card-footer">
                        <small class="text-muted">
                            <i class="fas fa-info-circle"></i> 
                            Check residents to add them to this household. Set their relationship to the head.
                        </small>
                    </div>
                </div>

                <!-- Hidden field to store member data -->
                <input type="hidden" name="household_members_data" id="householdMembersData" value="[]">

                <!-- FORM ACTIONS -->
                <div class="row mb-4">
                    <div class="col-12">
                        <a href="<?= base_url('households') ?>" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary float-right" id="submitBtn">
                            <i class="fas fa-save"></i> Save Household
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
.is-valid { border-color: #28a745 !important; }
.is-invalid { border-color: #dc3545 !important; }
.valid-feedback { color: #28a745; font-size: 80%; }
.invalid-feedback { color: #dc3545; font-size: 80%; }
</style>

<script>
    var BASE_URL = '<?= base_url() ?>';
    var allResidents = [];
    var selectedMembers = {};
    var isHouseholdNoValid = true;
    
    var relationshipOptions = [
        'Head', 'Spouse', 'Son', 'Daughter', 'Father', 'Mother',
        'Grandfather', 'Grandmother', 'Grandson', 'Granddaughter',
        'Brother', 'Sister', 'Uncle', 'Aunt', 'Nephew', 'Niece',
        'Cousin', 'Son-in-law', 'Daughter-in-law', 'Brother-in-law',
        'Sister-in-law', 'Other Relative', 'Non-Relative'
    ];
    
    function initHouseholdCreate() {
        if (typeof jQuery === 'undefined') {
            setTimeout(initHouseholdCreate, 50);
            return;
        }
        
        jQuery(document).ready(function($) {
            console.log('Household create page loaded');

            // ==========================================
            // AUTO ADDRESS GENERATION LOGIC (Hidden)
            // ==========================================
            function updateCompleteAddress() {
                var street = $('#streetAddress').val().trim();
                var sitio   = $('#sitioSelect').find("option:selected").text();
                
                // Default parts
                var purok = (sitio && sitio !== "— Select Purok —") ? sitio + ", " : "";
                var barangay = "Barangay Tabu, ";
                var province = "Negros Occidental";
                
                // Construct Address: [Street], [Purok], Barangay Tabu, Negros Occidental
                var parts = [];
                if (street) parts.push(street);
                if (purok) parts.push(purok);
                
                // Always append the static parts
                var fullAddress = parts.join(", ") + ", " + barangay + province;
                
                // Updates the HIDDEN field so the database gets the data
                $('#completeAddress').val(fullAddress);
            }

            // Trigger update when typing or changing dropdown
            $('#streetAddress').on('input blur', updateCompleteAddress);
            $('#sitioSelect').on('change', updateCompleteAddress);

            // Run once on load in case values are pre-filled
            updateCompleteAddress();
            // ==========================================

            
            // Generate new household number
            $('#generateHouseholdNo').on('click', function() {
                $.ajax({
                    url: BASE_URL + 'households/getNextHouseholdNo',
                    type: 'GET',
                    dataType: 'json',
                    success: function(res) {
                        if (res.status === 'success') {
                            $('#householdNo').val(res.household_no);
                            checkHouseholdNumber(res.household_no);
                        }
                    }
                });
            });
            
            // Check household number availability
            $('#checkHouseholdNo').on('click', function() {
                checkHouseholdNumber($('#householdNo').val());
            });
            
            $('#householdNo').on('blur', function() {
                checkHouseholdNumber($(this).val());
            });
            
            function checkHouseholdNumber(hhNo) {
                if (!hhNo) return;
                
                var $field = $('#householdNo');
                var $feedback = $('#householdNoFeedback');
                
                $.ajax({
                    url: BASE_URL + 'households/checkHouseholdNo',
                    type: 'GET',
                    data: { household_no: hhNo },
                    dataType: 'json',
                    success: function(res) {
                        $field.removeClass('is-valid is-invalid');
                        
                        if (res.exists) {
                            $field.addClass('is-invalid');
                            $feedback.html('<span class="text-danger"><i class="fas fa-times-circle"></i> This household number already exists! Please choose another.</span>');
                            isHouseholdNoValid = false;
                        } else {
                            $field.addClass('is-valid');
                            $feedback.html('<span class="text-success"><i class="fas fa-check-circle"></i> Household number is available!</span>');
                            isHouseholdNoValid = true;
                        }
                    },
                    error: function() {
                        $field.removeClass('is-valid is-invalid');
                        $feedback.html('<span class="text-muted">Auto-generated unique number (you can edit)</span>');
                        isHouseholdNoValid = true;
                    }
                });
            }
            
            // Initial check if value exists
            if ($('#householdNo').val()) {
                checkHouseholdNumber($('#householdNo').val());
            }
            
            // Load residents when sitio changes
            $('#sitioSelect').on('change', function () {
                var sitio = $(this).val();
                var headSelect = $('#headResidentSelect');

                $('#noResidentsAlert, #membersLoadingAlert').hide();
                $('#membersTableContainer').hide();
                $('#emptyMembersState').show();
                $('#toggleAllMembers').prop('disabled', true);

                if (!sitio) {
                    headSelect.html('<option value="">— Select Purok/Sitio first —</option>').prop('disabled', true);
                    $('#membersTableBody').empty();
                    allResidents = [];
                    selectedMembers = {};
                    updateSelectedCount();
                    return;
                }

                $('#membersLoadingAlert').show();
                headSelect.html('<option value="">Loading…</option>').prop('disabled', true);

                $.ajax({
                    url  : BASE_URL + 'households/getResidentsBySitio',
                    type : 'POST',
                    data : { 
                        sitio: sitio, 
                        '<?= csrf_token() ?>': '<?= csrf_hash() ?>' 
                    },
                    dataType : 'json',
                    success: function (res) {
                        $('#membersLoadingAlert').hide();
                        
                        if (res.csrf_hash) {
                            $('input[name="<?= csrf_token() ?>"]').val(res.csrf_hash);
                        }
                        
                        if (res.status === 'success' && res.residents && res.residents.length > 0) {
                            allResidents = res.residents;
                            
                            // Populate Head Resident dropdown
                            var headOpts = '<option value="">— Select Head of Household —</option>';
                            $.each(res.residents, function (i, r) {
                                var name = r.last_name + ', ' + r.first_name;
                                if (r.middle_name) name += ' ' + r.middle_name;
                                name += ' (' + (r.sex ? r.sex.charAt(0).toUpperCase() + r.sex.slice(1) : 'N/A') + ')';
                                if (!r.resident_sitio) name += ' [Unassigned]';
                                headOpts += '<option value="' + r.id + '">' + name + '</option>';
                            });
                            headSelect.html(headOpts).prop('disabled', false);
                            
                            // Populate Members table
                            renderMembersTable(res.residents);
                            $('#membersTableContainer').show();
                            $('#emptyMembersState').hide();
                            $('#toggleAllMembers').prop('disabled', false);
                        } else {
                            headSelect.html('<option value="">— No residents found —</option>').prop('disabled', true);
                            $('#membersTableBody').empty();
                            $('#noResidentsAlert').show();
                            allResidents = [];
                            selectedMembers = {};
                            updateSelectedCount();
                        }
                    },
                    error: function (xhr) {
                        $('#membersLoadingAlert').hide();
                        console.error('AJAX Error:', xhr.status, xhr.responseText);
                        headSelect.html('<option value="">— Request failed —</option>').prop('disabled', true);
                        alert('Failed to load residents. Please try again.');
                    }
                });
            });
            
            // Render members table
            function renderMembersTable(residents) {
                var tbody = $('#membersTableBody');
                var html = '';
                
                $.each(residents, function(i, r) {
                    var name = r.last_name + ', ' + r.first_name;
                    if (r.middle_name) name += ' ' + r.middle_name;
                    var unassignedBadge = !r.resident_sitio ? ' <span class="badge badge-warning badge-sm">Unassigned</span>' : '';
                    var sexBadge = r.sex === 'male' ? '<span class="badge badge-info badge-sm">M</span>' : '<span class="badge badge-pink badge-sm">F</span>';
                    
                    var isChecked = selectedMembers[r.id] ? 'checked' : '';
                    var relationship = selectedMembers[r.id]?.relationship || '';
                    
                    html += '<tr>';
                    html += '<td><input type="checkbox" class="member-checkbox" data-id="' + r.id + '" ' + isChecked + '></td>';
                    html += '<td>';
                    html += '<strong>' + name + '</strong> ' + sexBadge;
                    html += unassignedBadge;
                    html += '<br><small class="text-muted">ID: ' + r.id + '</small>';
                    html += '</td>';
                    html += '<td>';
                    html += '<select class="form-control form-control-sm relationship-select" data-id="' + r.id + '" ' + (isChecked ? '' : 'disabled') + '>';
                    html += '<option value="">— Select —</option>';
                    relationshipOptions.forEach(function(rel) {
                        var selected = (relationship === rel) ? 'selected' : '';
                        html += '<option value="' + rel + '" ' + selected + '>' + rel + '</option>';
                    });
                    html += '</select>';
                    html += '</td>';
                    html += '</tr>';
                });
                
                tbody.html(html);
            }
            
            // Handle checkbox changes
            $(document).on('change', '.member-checkbox', function() {
                var id = $(this).data('id');
                var isChecked = $(this).is(':checked');
                var row = $(this).closest('tr');
                
                row.find('.relationship-select').prop('disabled', !isChecked);
                
                if (isChecked) {
                    var resident = allResidents.find(r => r.id == id);
                    if (resident) {
                        selectedMembers[id] = {
                            id: id,
                            name: resident.last_name + ', ' + resident.first_name,
                            relationship: row.find('.relationship-select').val() || ''
                        };
                    }
                } else {
                    delete selectedMembers[id];
                    row.find('.relationship-select').val('');
                }
                
                updateSelectedCount();
                updateHiddenField();
            });
            
            // Handle relationship changes
            $(document).on('change', '.relationship-select', function() {
                var id = $(this).data('id');
                if (selectedMembers[id]) {
                    selectedMembers[id].relationship = $(this).val();
                    updateHiddenField();
                }
            });
            
            // Select All checkbox
            $('#selectAllCheckbox').on('change', function() {
                var isChecked = $(this).is(':checked');
                $('.member-checkbox').prop('checked', isChecked).trigger('change');
            });
            
            // Toggle All button
            $('#toggleAllMembers').on('click', function() {
                var anyUnchecked = $('.member-checkbox:not(:checked)').length > 0;
                $('.member-checkbox').prop('checked', anyUnchecked).trigger('change');
                $('#selectAllCheckbox').prop('checked', anyUnchecked);
            });
            
            // Auto-check head resident
            $('#headResidentSelect').on('change', function() {
                var headId = $(this).val();
                if (headId) {
                    if (!selectedMembers[headId]) {
                        $('.member-checkbox[data-id="' + headId + '"]').prop('checked', true).trigger('change');
                    }
                    $('.relationship-select[data-id="' + headId + '"]').val('Head');
                    if (selectedMembers[headId]) {
                        selectedMembers[headId].relationship = 'Head';
                    }
                    updateHiddenField();
                }
            });
            
            // Update selected count
            function updateSelectedCount() {
                var count = Object.keys(selectedMembers).length;
                $('#selectedCount').text(count + ' selected');
            }
            
            // Update hidden field with member data
            function updateHiddenField() {
                $('#householdMembersData').val(JSON.stringify(selectedMembers));
            }
            
            // Form submission
            $('#householdForm').on('submit', function (e) {
                var hhNo  = $('input[name="household_no"]').val().trim();
                var sitio = $('#sitioSelect').val();
                
                // Remove any existing alert
                $('.custom-alert').remove();
                
                if (!hhNo)  { 
                    showFormError('Please enter household number.');
                    e.preventDefault(); 
                    return false; 
                }
                
                if (!isHouseholdNoValid) {
                    showFormError('Please enter a unique household number. This number already exists.');
                    e.preventDefault(); 
                    return false; 
                }
                
                if (!sitio) { 
                    showFormError('Please select Purok/Sitio.');     
                    e.preventDefault(); 
                    return false; 
                }
                
                // Update hidden field before submit
                updateHiddenField();
                
                $('#submitBtn').html('<i class="fas fa-spinner fa-spin"></i> Saving…').prop('disabled', true);
            });
            
            function showFormError(message) {
                var alertHtml = '<div class="alert alert-danger custom-alert alert-dismissible fade show" style="margin-bottom: 20px;">' +
                    '<i class="fas fa-exclamation-circle mr-2"></i>' + message +
                    '<button type="button" class="close" data-dismiss="alert">&times;</button>' +
                    '</div>';
                $('.content .container-fluid').prepend(alertHtml);
                $('html, body').animate({ scrollTop: 0 }, 300);
            }
            
            // Check initial household number if exists
            <?php if (old('household_no')): ?>
            setTimeout(function() {
                checkHouseholdNumber('<?= old('household_no') ?>');
            }, 500);
            <?php endif; ?>
        });
    }
    
    initHouseholdCreate();
</script>

<?= $this->endSection() ?>
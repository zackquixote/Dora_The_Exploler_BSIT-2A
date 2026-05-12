/**
 * Household create page JS
 * 
 * Manages:
 * - Auto-generation of household number with uniqueness check.
 * - Dynamic address building from street and sitio.
 * - Loading residents by selected Sitio.
 * - Selection of household members with relationship assignment.
 * 
 * FIXES:
 * - Clears `selectedMembers` when changing Sitio to prevent stale selections.
 * - Synchronises "Select All" checkbox state with individual checkboxes.
 * - Updates CSRF token after every AJAX response.
 */
$(document).ready(function() {
    const vars = $('#js-variables').data();
    const BASE_URL = vars.baseUrl;
    const CSRF_TOKEN_NAME = vars.csrfToken;
    let CSRF_HASH = vars.csrfHash;
    
    let allResidents = [];
    let selectedMembers = {};
    let isHouseholdNoValid = true;
    
    const relationshipOptions = [
        'Head', 'Spouse', 'Son', 'Daughter', 'Father', 'Mother',
        'Grandfather', 'Grandmother', 'Grandson', 'Granddaughter',
        'Brother', 'Sister', 'Uncle', 'Aunt', 'Nephew', 'Niece',
        'Cousin', 'Son-in-law', 'Daughter-in-law', 'Brother-in-law',
        'Sister-in-law', 'Other Relative', 'Non-Relative'
    ];
    
    // ------------------- Address Auto-generation -------------------
    function updateCompleteAddress() {
        const street = $('#streetAddress').val().trim();
        const sitio = $('#sitioSelect').find("option:selected").text();
        const purok = (sitio && sitio !== "— Select Purok —") ? sitio + ", " : "";
        const barangay = "Barangay Tabu, ";
        const province = "Negros Occidental";
        let parts = [];
        if (street) parts.push(street);
        if (purok) parts.push(purok);
        const fullAddress = parts.join(", ") + ", " + barangay + province;
        $('#completeAddress').val(fullAddress);
    }
    
    $('#streetAddress').on('input blur', updateCompleteAddress);
    $('#sitioSelect').on('change', updateCompleteAddress);
    updateCompleteAddress();
    
    // ------------------- Household Number -------------------
    $('#generateHouseholdNo').on('click', function() {
        $.ajax({
            url: BASE_URL + 'households/getNextHouseholdNo',
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                if (res.csrf_hash) refreshCsrf(res.csrf_hash);
                if (res.status === 'success') {
                    $('#householdNo').val(res.household_no);
                    checkHouseholdNumber(res.household_no);
                }
            }
        });
    });
    
    function checkHouseholdNumber(hhNo) {
        if (!hhNo) return;
        const $field = $('#householdNo');
        const $feedback = $('#householdNoFeedback');
        $.ajax({
            url: BASE_URL + 'households/checkHouseholdNo',
            type: 'GET',
            data: { household_no: hhNo },
            dataType: 'json',
            success: function(res) {
                if (res.csrf_hash) refreshCsrf(res.csrf_hash);
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
    
    $('#checkHouseholdNo').on('click', function() {
        checkHouseholdNumber($('#householdNo').val());
    });
    $('#householdNo').on('blur', function() {
        checkHouseholdNumber($(this).val());
    });
    if ($('#householdNo').val()) checkHouseholdNumber($('#householdNo').val());
    
    // ------------------- Load Residents by Sitio -------------------
    $('#sitioSelect').on('change', function() {
        const sitio = $(this).val();
        const headSelect = $('#headResidentSelect');
        
        // Hide all alerts initially
        $('#loadingAlert, #noResidentsAlert, #membersLoadingAlert, #noResidentsWarning').hide();
        $('#membersTableContainer').hide();
        $('#emptyMembersState').show();
        $('#toggleAllMembers').prop('disabled', true);
        
        if (!sitio) {
            headSelect.html('<option value="">— Select Purok/Sitio first —</option>').prop('disabled', true);
            $('#membersTableBody').empty();
            allResidents = [];
            selectedMembers = {};               // ✅ FIX: Clear previous selections
            updateSelectedCount();
            return;
        }
        
        $('#loadingAlert, #membersLoadingAlert').show();
        headSelect.html('<option value="">Loading…</option>').prop('disabled', true);
        $('#emptyMembersState').hide();
        
        $.ajax({
            url: BASE_URL + 'households/getResidentsBySitio',
            type: 'POST',
            data: { sitio: sitio, [CSRF_TOKEN_NAME]: CSRF_HASH },
            dataType: 'json',
            success: function(res) {
                $('#loadingAlert, #membersLoadingAlert').hide();
                if (res.csrf_hash) refreshCsrf(res.csrf_hash);
                
                if (res.status === 'success' && res.residents && res.residents.length > 0) {
                    allResidents = res.residents;
                    selectedMembers = {};                     // ✅ FIX: Reset members for new Sitio
                    let headOpts = '<option value="">— Select Head of Household —</option>';
                    $.each(res.residents, function(i, r) {
                        let name = r.last_name + ', ' + r.first_name;
                        if (r.middle_name) name += ' ' + r.middle_name;
                        name += ' (' + (r.sex ? r.sex.charAt(0).toUpperCase() + r.sex.slice(1) : 'N/A') + ')';
                        if (!r.resident_sitio) name += ' [Unassigned]';
                        headOpts += '<option value="' + r.id + '">' + name + '</option>';
                    });
                    headSelect.html(headOpts).prop('disabled', false);
                    renderMembersTable(res.residents);
                    $('#membersTableContainer').show();
                    $('#toggleAllMembers').prop('disabled', false);
                } else {
                    headSelect.html('<option value="">— No residents found —</option>').prop('disabled', true);
                    $('#membersTableBody').empty();
                    $('#noResidentsAlert, #noResidentsWarning').show();
                    allResidents = [];
                    selectedMembers = {};
                    updateSelectedCount();
                }
            },
            error: function() {
                $('#loadingAlert, #membersLoadingAlert').hide();
                headSelect.html('<option value="">— Request failed —</option>').prop('disabled', true);
                $('#emptyMembersState').show();
                alert('Failed to load residents. Please try again.');
            }
        });
    });
    
    // ------------------- Render Members Table -------------------
    function renderMembersTable(residents) {
        let html = '';
        $.each(residents, function(i, r) {
            let name = r.last_name + ', ' + r.first_name;
            if (r.middle_name) name += ' ' + r.middle_name;
            let unassignedBadge = !r.resident_sitio ? ' <span class="badge badge-warning badge-sm">Unassigned</span>' : '';
            let sexBadge = r.sex === 'male' ? '<span class="badge badge-info badge-sm">M</span>' : '<span class="badge badge-pink badge-sm">F</span>';
            let isChecked = selectedMembers[r.id] ? 'checked' : '';
            let relationship = selectedMembers[r.id]?.relationship || '';
            html += '<tr>';
            html += '<td><input type="checkbox" class="member-checkbox" data-id="' + r.id + '" ' + isChecked + '></td>';
            html += '<td><strong>' + name + '</strong> ' + sexBadge + unassignedBadge + '<br><small class="text-muted">ID: ' + r.id + '</small></td>';
            html += '<td><select class="form-control form-control-sm relationship-select" data-id="' + r.id + '" ' + (isChecked ? '' : 'disabled') + '>';
            html += '<option value="">— Select —</option>';
            relationshipOptions.forEach(function(rel) {
                let selected = (relationship === rel) ? 'selected' : '';
                html += '<option value="' + rel + '" ' + selected + '>' + rel + '</option>';
            });
            html += '</select></td></tr>';
        });
        $('#membersTableBody').html(html);
    }
    
    // ------------------- Event Delegation for Checkboxes & Relationships -------------------
    $(document).on('change', '.member-checkbox', function() {
        const id = $(this).data('id');
        const isChecked = $(this).is(':checked');
        const row = $(this).closest('tr');
        row.find('.relationship-select').prop('disabled', !isChecked);
        
        if (isChecked) {
            const resident = allResidents.find(r => r.id == id);
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
        syncSelectAllCheckbox();   // ✅ FIX: Keep master checkbox in sync
    });
    
    $(document).on('change', '.relationship-select', function() {
        const id = $(this).data('id');
        if (selectedMembers[id]) {
            selectedMembers[id].relationship = $(this).val();
            updateHiddenField();
        }
    });
    
    // Master checkbox "Select All"
    $('#selectAllCheckbox').on('change', function() {
        const isChecked = $(this).is(':checked');
        $('.member-checkbox').prop('checked', isChecked).trigger('change');
    });
    
    // Toggle all members button
    $('#toggleAllMembers').on('click', function() {
        const anyUnchecked = $('.member-checkbox:not(:checked)').length > 0;
        $('.member-checkbox').prop('checked', anyUnchecked).trigger('change');
        $('#selectAllCheckbox').prop('checked', anyUnchecked);
    });
    
    // Synchronise master checkbox when individual checkboxes change
    function syncSelectAllCheckbox() {
        const total = $('.member-checkbox').length;
        const checked = $('.member-checkbox:checked').length;
        $('#selectAllCheckbox').prop('checked', total > 0 && checked === total);
        $('#selectAllCheckbox').prop('indeterminate', checked > 0 && checked < total);
    }
    
    // Head of household selection: auto-check and set relationship to "Head"
    $('#headResidentSelect').on('change', function() {
        const headId = $(this).val();
        if (headId) {
            if (!selectedMembers[headId]) {
                $('.member-checkbox[data-id="' + headId + '"]').prop('checked', true).trigger('change');
            }
            $('.relationship-select[data-id="' + headId + '"]').val('Head').trigger('change');
        }
    });
    
    // ------------------- Helper Functions -------------------
    function updateSelectedCount() {
        $('#selectedCount').text(Object.keys(selectedMembers).length + ' selected');
    }
    
    function updateHiddenField() {
        $('#householdMembersData').val(JSON.stringify(selectedMembers));
    }
    
    function refreshCsrf(newHash) {
        if (newHash) {
            CSRF_HASH = newHash;
            $('input[name="' + CSRF_TOKEN_NAME + '"]').val(newHash);
        }
    }
    
    // ------------------- Form Validation -------------------
    $('#householdForm').on('submit', function(e) {
        const hhNo = $('input[name="household_no"]').val().trim();
        const sitio = $('#sitioSelect').val();
        $('.custom-alert').remove();
        if (!hhNo) { showFormError('Please enter household number.'); e.preventDefault(); return false; }
        if (!isHouseholdNoValid) { showFormError('Please enter a unique household number. This number already exists.'); e.preventDefault(); return false; }
        if (!sitio) { showFormError('Please select Purok/Sitio.'); e.preventDefault(); return false; }
        updateHiddenField();
        $('#submitBtn').html('<i class="fas fa-spinner fa-spin"></i> Saving…').prop('disabled', true);
    });
    
    function showFormError(message) {
        // Remove any existing error alert
        $('#household-form-error').remove();
        const alertHtml = '<div id="household-form-error" style="background:var(--c-rose-bg);color:var(--c-rose);padding:12px 16px;border-radius:var(--r-sm);margin-bottom:14px;font-size:12px;font-weight:600">' +
            '<i class="fas fa-exclamation-circle" style="margin-right:6px"></i>' + message + '</div>';
        // Prepend inside the form, before the first card
        $('#householdForm .ds-card').first().before(alertHtml);
        $('html, body').animate({ scrollTop: 0 }, 300);
    }
    
    if (vars.oldHouseholdNo) {
        setTimeout(() => checkHouseholdNumber(vars.oldHouseholdNo), 500);
    }
});
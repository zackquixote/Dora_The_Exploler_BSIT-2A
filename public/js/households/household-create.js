// public/assets/js/household-create.js

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

 $(document).ready(function() {
    console.log('Household create page loaded');

    // Get variables from HTML data attributes
    var vars = $('#js-variables').data();
    var BASE_URL = vars.baseUrl;
    var CSRF_TOKEN_NAME = vars.csrfToken;
    var CSRF_HASH = vars.csrfHash;

    // ==========================================
    // AUTO ADDRESS GENERATION LOGIC
    // ==========================================
    function updateCompleteAddress() {
        var street = $('#streetAddress').val().trim();
        var sitio = $('#sitioSelect').find("option:selected").text();
        
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
                [CSRF_TOKEN_NAME]: CSRF_HASH 
            },
            dataType : 'json',
            success: function (res) {
                $('#membersLoadingAlert').hide();
                
                if (res.csrf_hash) {
                    $('input[name="' + CSRF_TOKEN_NAME + '"]').val(res.csrf_hash);
                    CSRF_HASH = res.csrf_hash; // Update local var
                }
                
                if (res.status === 'success' && res.residents && res.residents.length > 0) {
                    allResidents = res.residents;
                    
                    var headOpts = '<option value="">— Select Head of Household —</option>';
                    $.each(res.residents, function (i, r) {
                        var name = r.last_name + ', ' + r.first_name;
                        if (r.middle_name) name += ' ' + r.middle_name;
                        name += ' (' + (r.sex ? r.sex.charAt(0).toUpperCase() + r.sex.slice(1) : 'N/A') + ')';
                        if (!r.resident_sitio) name += ' [Unassigned]';
                        headOpts += '<option value="' + r.id + '">' + name + '</option>';
                    });
                    headSelect.html(headOpts).prop('disabled', false);
                    
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

    $(document).on('change', '.relationship-select', function() {
        var id = $(this).data('id');
        if (selectedMembers[id]) {
            selectedMembers[id].relationship = $(this).val();
            updateHiddenField();
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

    function updateSelectedCount() {
        var count = Object.keys(selectedMembers).length;
        $('#selectedCount').text(count + ' selected');
    }

    function updateHiddenField() {
        $('#householdMembersData').val(JSON.stringify(selectedMembers));
    }

    $('#householdForm').on('submit', function (e) {
        var hhNo  = $('input[name="household_no"]').val().trim();
        var sitio = $('#sitioSelect').val();
        
        $('.custom-alert').remove();
        
        if (!hhNo) { 
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
    if (vars.oldHouseholdNo) {
        setTimeout(function() {
            checkHouseholdNumber(vars.oldHouseholdNo);
        }, 500);
    }
});
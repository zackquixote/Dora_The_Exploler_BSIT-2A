// public/assets/js/households/household-edit.js

 $(document).ready(function() {
    console.log('Household Edit Page Loaded');

    // 1. GET VARIABLES FROM HTML
    var vars = $('#js-variables').data();
    var BASE_URL = vars.baseUrl;
    var CSRF_TOKEN_NAME = vars.csrfToken;
    var CSRF_HASH = vars.csrfHash;
    var householdId = vars.householdId;
    var headId = vars.headId;
    var currentSitio = vars.currentSitio;
    var currentMembersData = vars.currentMembers; // Passed from Controller

    var allResidents = [];
    var selectedMembers = {};

    var relationshipOptions = [
        'Head', 'Spouse', 'Son', 'Daughter', 'Father', 'Mother',
        'Grandfather', 'Grandmother', 'Grandson', 'Granddaughter',
        'Brother', 'Sister', 'Uncle', 'Aunt', 'Nephew', 'Niece',
        'Cousin', 'Son-in-law', 'Daughter-in-law', 'Brother-in-law',
        'Sister-in-law', 'Other Relative', 'Non-Relative'
    ];

    // ==========================================
    // AUTO ADDRESS GENERATION
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
    
    function loadResidents(sitio) {
        if (!sitio) return;

        $('#membersLoadingAlert').show();
        $('#membersTableBody').empty();
        $('#emptyMembersState').hide();
        $('#membersTableContainer').hide();

        $.ajax({
            url: BASE_URL + 'households/getResidentsBySitio',
            type: 'POST',
            data: { 
                sitio: sitio,
                [CSRF_TOKEN_NAME]: CSRF_HASH
            },
            dataType: 'json',
            success: function(res) {
                $('#membersLoadingAlert').hide();
                
                if (res.csrf_hash) {
                    $('input[name="' + CSRF_TOKEN_NAME + '"]').val(res.csrf_hash);
                    CSRF_HASH = res.csrf_hash;
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

        // --- PRE-CHECK EXISTING MEMBERS ---
        // Create a map of current members by ID for fast lookup
        var dbMemberMap = {};
        $.each(currentMembersData, function(i, m) {
            var mid = m.resident_id || m.id;
            if (mid) {
                dbMemberMap[mid] = m.relationship_to_head || m.relationship || '';
            }
        });

        residents.forEach(function(r) {
            // 1. Check if ID exists in map
            var isChecked = dbMemberMap[r.id] ? 'checked' : '';
            var relationship = dbMemberMap[r.id] ? dbMemberMap[r.id] : '';

            // 2. Fallback: Name Matching (if IDs didn't match due to type mismatch)
            if (!isChecked) {
                $.each(currentMembersData, function(i, cm) {
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

            // Sync with selectedMembers object
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

    // ==========================================
    // EVENTS
    // ==========================================
    $('#sitioSelect').on('change', function() {
        // Reset selections when changing sitio to avoid ghost data
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
    
    // Auto-set Head Resident
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

    // INITIAL LOAD
    if (currentSitio) {
        loadResidents(currentSitio);
    }
});
/**
 * Household edit page JS
 */
$(document).ready(function() {
    const vars = $('#js-variables').data();
    const BASE_URL = vars.baseUrl;
    const CSRF_TOKEN_NAME = vars.csrfToken;
    let CSRF_HASH = vars.csrfHash;
    const householdId = vars.householdId;
    const headId = vars.headId;
    const currentSitio = vars.currentSitio;
    const currentMembersData = vars.currentMembers || [];
    
    let allResidents = [];
    let selectedMembers = {};
    
    const relationshipOptions = [
        'Head', 'Spouse', 'Son', 'Daughter', 'Father', 'Mother',
        'Grandfather', 'Grandmother', 'Grandson', 'Granddaughter',
        'Brother', 'Sister', 'Uncle', 'Aunt', 'Nephew', 'Niece',
        'Cousin', 'Son-in-law', 'Daughter-in-law', 'Brother-in-law',
        'Sister-in-law', 'Other Relative', 'Non-Relative'
    ];
    
    // Auto address generation
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
    
    function loadResidents(sitio) {
        if (!sitio) return;
        $('#membersLoadingAlert').show();
        $('#membersTableBody').empty();
        $('#emptyMembersState').hide();
        $('#membersTableContainer').hide();
        $.ajax({
            url: BASE_URL + 'households/getResidentsBySitio',
            type: 'POST',
            data: { sitio: sitio, [CSRF_TOKEN_NAME]: CSRF_HASH },
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
        const headSelect = $('#headResidentSelect');
        headSelect.empty().append('<option value="">— Select Head of Household —</option>');
        residents.forEach(function(r) {
            let name = r.last_name + ', ' + r.first_name + (r.middle_name ? ' ' + r.middle_name : '');
            let selected = (r.id == headId) ? 'selected' : '';
            headSelect.append('<option value="' + r.id + '" ' + selected + '>' + name + '</option>');
        });
    }
    
    function renderMembersTable(residents) {
        const tbody = $('#membersTableBody');
        tbody.empty();
        if (residents.length === 0) {
            $('#emptyMembersState').show();
            $('#membersTableContainer').hide();
            return;
        } else {
            $('#emptyMembersState').hide();
            $('#membersTableContainer').show();
        }
        // Pre-check existing members
        const dbMemberMap = {};
        $.each(currentMembersData, function(i, m) {
            const mid = m.resident_id || m.id;
            if (mid) dbMemberMap[mid] = m.relationship_to_head || m.relationship || '';
        });
        residents.forEach(function(r) {
            let isChecked = dbMemberMap[r.id] ? 'checked' : '';
            let relationship = dbMemberMap[r.id] ? dbMemberMap[r.id] : '';
            if (!isChecked) {
                // fallback name matching
                $.each(currentMembersData, function(i, cm) {
                    if (cm.last_name === r.last_name && cm.first_name === r.first_name) {
                        isChecked = 'checked';
                        relationship = cm.relationship_to_head || cm.relationship || '';
                        return false;
                    }
                });
            }
            const unassignedBadge = (!r.resident_sitio || r.resident_sitio === '') ? ' <span class="badge badge-warning badge-sm">Unassigned</span>' : '';
            let html = '<tr>';
            html += '<td><input type="checkbox" class="member-checkbox" data-id="' + r.id + '" ' + isChecked + '></td>';
            html += '<td><strong>' + r.last_name + ', ' + r.first_name + '</strong> ' + unassignedBadge + '</td>';
            html += '<td><select class="form-control form-control-sm relationship-select" data-id="' + r.id + '" ' + (isChecked ? '' : 'disabled') + '>';
            html += '<option value="">— Select —</option>';
            relationshipOptions.forEach(function(rel) {
                let selected = (relationship === rel) ? 'selected' : '';
                html += '<option value="' + rel + '" ' + selected + '>' + rel + '</option>';
            });
            html += '</select></td></tr>';
            tbody.append(html);
            if (isChecked) {
                selectedMembers[r.id] = { id: r.id, relationship: relationship };
            }
        });
        updateSelectedCount();
        updateHiddenField();
    }
    
    $('#sitioSelect').on('change', function() {
        selectedMembers = {};
        loadResidents($(this).val());
    });
    
    $(document).on('change', '.member-checkbox', function() {
        const id = $(this).data('id');
        const isChecked = $(this).is(':checked');
        const row = $(this).closest('tr');
        row.find('.relationship-select').prop('disabled', !isChecked);
        if (isChecked) {
            if (!selectedMembers[id]) {
                selectedMembers[id] = { id: id, relationship: row.find('.relationship-select').val() || '' };
            }
        } else {
            delete selectedMembers[id];
        }
        updateSelectedCount();
        updateHiddenField();
    });
    
    $(document).on('change', '.relationship-select', function() {
        const id = $(this).data('id');
        if (selectedMembers[id]) {
            selectedMembers[id].relationship = $(this).val();
            updateHiddenField();
        }
    });
    
    $('#headResidentSelect').on('change', function() {
        const newHeadId = $(this).val();
        if (newHeadId) {
            const checkbox = $('.member-checkbox[data-id="' + newHeadId + '"]');
            if (checkbox.length && !checkbox.is(':checked')) checkbox.prop('checked', true).trigger('change');
            const select = $('.relationship-select[data-id="' + newHeadId + '"]');
            if (select.length) select.val('Head').trigger('change');
        }
    });
    
    $('#selectAllCheckbox').on('change', function() {
        const isChecked = $(this).is(':checked');
        $('.member-checkbox').prop('checked', isChecked).trigger('change');
    });
    
    $('#toggleAllMembers').on('click', function() {
        const anyUnchecked = $('.member-checkbox:not(:checked)').length > 0;
        $('.member-checkbox').prop('checked', anyUnchecked).trigger('change');
        $('#selectAllCheckbox').prop('checked', anyUnchecked);
    });
    
    function updateSelectedCount() {
        $('#selectedCount').text(Object.keys(selectedMembers).length + ' selected');
    }
    function updateHiddenField() {
        $('#householdMembersData').val(JSON.stringify(selectedMembers));
    }
    
    if (currentSitio) loadResidents(currentSitio);
});
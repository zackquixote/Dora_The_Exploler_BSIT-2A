/**
 * Households Edit Page JavaScript
 * Handles member selection and household editing
 */

var HouseholdEdit = (function() {
    'use strict';
    
    // Private variables
    var BASE_URL = '';
    var CSRF_TOKEN = '';
    var CSRF_HASH = '';
    var currentHouseholdId = null;
    var currentHeadId = null;
    var currentSitio = '';
    var currentMembers = [];
    var allResidents = [];
    var selectedMembers = {};
    
    var relationshipOptions = [
        'Head', 'Spouse', 'Son', 'Daughter', 'Father', 'Mother',
        'Grandfather', 'Grandmother', 'Grandson', 'Granddaughter',
        'Brother', 'Sister', 'Uncle', 'Aunt', 'Nephew', 'Niece',
        'Cousin', 'Son-in-law', 'Daughter-in-law', 'Brother-in-law',
        'Sister-in-law', 'Other Relative', 'Non-Relative'
    ];
    
    // Private methods
    function showToast(type, msg) {
        var bgColor = type === 'success' ? '#d4edda' : '#f8d7da';
        var textColor = type === 'success' ? '#155724' : '#721c24';
        var borderColor = type === 'success' ? '#c3e6cb' : '#f5c6cb';
        
        var $toast = jQuery('<div>')
            .css({
                'position': 'fixed',
                'bottom': '24px',
                'right': '24px',
                'z-index': '9999',
                'background': bgColor,
                'border': '1px solid ' + borderColor,
                'border-radius': '8px',
                'padding': '14px 20px',
                'font-size': '14px',
                'color': textColor,
                'box-shadow': '0 4px 12px rgba(0,0,0,0.15)',
                'font-weight': '500',
                'max-width': '340px'
            })
            .text(msg)
            .appendTo('body');
            
        setTimeout(function () {
            $toast.fadeOut(400, function () { 
                jQuery(this).remove(); 
            });
        }, 3500);
    }
    
    function updateCSRFToken(newHash) {
        if (newHash) {
            CSRF_HASH = newHash;
            jQuery('input[name="' + CSRF_TOKEN + '"]').val(newHash);
        }
    }
    
    function updateSelectedCount() {
        var count = Object.keys(selectedMembers).length;
        jQuery('#selectedCount').text(count + ' selected');
    }
    
    function updateHiddenField() {
        jQuery('#householdMembersData').val(JSON.stringify(selectedMembers));
    }
    
    function renderMembersTable(residents) {
        var tbody = jQuery('#membersTableBody');
        var html = '';
        
        jQuery.each(residents, function(i, r) {
            var name = r.last_name + ', ' + r.first_name;
            if (r.middle_name) name += ' ' + r.middle_name;
            
            var isChecked = selectedMembers[r.id] ? 'checked' : '';
            var relationship = selectedMembers[r.id]?.relationship || '';
            
            html += '<tr>';
            html += '<td><input type="checkbox" class="member-checkbox" data-id="' + r.id + '" ' + isChecked + '></td>';
            html += '<td><strong>' + name + '</strong></td>';
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
    
    function loadResidents(sitio) {
        var headSelect = jQuery('#headResidentSelect');
        
        if (!sitio) {
            headSelect.html('<option value="">— Select Purok first —</option>').prop('disabled', true);
            jQuery('#membersTableBody').empty();
            return;
        }
        
        jQuery('#membersLoadingAlert').show();
        headSelect.html('<option value="">Loading…</option>').prop('disabled', true);
        
        var requestData = {};
        requestData[CSRF_TOKEN] = CSRF_HASH;
        requestData['sitio'] = sitio;
        
        jQuery.ajax({
            url: BASE_URL + 'households/getResidentsBySitio',
            type: 'POST',
            data: requestData,
            dataType: 'json',
            success: function (res) {
                jQuery('#membersLoadingAlert').hide();
                
                if (res.csrf_hash) {
                    updateCSRFToken(res.csrf_hash);
                }
                
                if (res.status === 'success') {
                    allResidents = res.residents || [];
                    
                    // Add current members that might not be in the sitio list
                    currentMembers.forEach(function(member) {
                        if (!allResidents.find(function(r) { return r.id == member.id; })) {
                            allResidents.push(member);
                        }
                    });
                    
                    // Populate Head Resident dropdown
                    var headOpts = '<option value="">— Select Head of Household —</option>';
                    jQuery.each(allResidents, function (i, r) {
                        var name = r.last_name + ', ' + r.first_name;
                        if (r.middle_name) name += ' ' + r.middle_name;
                        var selected = (currentHeadId == r.id) ? 'selected' : '';
                        headOpts += '<option value="' + r.id + '" ' + selected + '>' + name + '</option>';
                    });
                    headSelect.html(headOpts).prop('disabled', false);
                    
                    // Render members table
                    renderMembersTable(allResidents);
                    jQuery('#membersTableContainer').show();
                    jQuery('#emptyMembersState').hide();
                    jQuery('#toggleAllMembers').prop('disabled', false);
                    
                    updateSelectedCount();
                } else {
                    headSelect.html('<option value="">— No residents found —</option>').prop('disabled', true);
                    jQuery('#membersTableBody').empty();
                    jQuery('#emptyMembersState').show();
                }
            },
            error: function (xhr) {
                jQuery('#membersLoadingAlert').hide();
                console.error('AJAX Error:', xhr.status, xhr.responseText);
                headSelect.html('<option value="">— Request failed —</option>').prop('disabled', true);
            }
        });
    }
    
    function initEvents() {
        // Load residents when sitio changes
        jQuery('#sitioSelect').on('change', function () {
            loadResidents(jQuery(this).val());
        });
        
        // Handle checkbox changes
        jQuery(document).on('change', '.member-checkbox', function() {
            var id = jQuery(this).data('id');
            var isChecked = jQuery(this).is(':checked');
            var row = jQuery(this).closest('tr');
            
            row.find('.relationship-select').prop('disabled', !isChecked);
            
            if (isChecked) {
                var resident = allResidents.find(function(r) { return r.id == id; });
                if (resident) {
                    selectedMembers[id] = {
                        id: id,
                        relationship: row.find('.relationship-select').val() || ''
                    };
                }
            } else {
                delete selectedMembers[id];
            }
            
            updateSelectedCount();
            updateHiddenField();
        });
        
        // Handle relationship changes
        jQuery(document).on('change', '.relationship-select', function() {
            var id = jQuery(this).data('id');
            if (selectedMembers[id]) {
                selectedMembers[id].relationship = jQuery(this).val();
                updateHiddenField();
            }
        });
        
        // Select All checkbox
        jQuery('#selectAllCheckbox').on('change', function() {
            var isChecked = jQuery(this).is(':checked');
            jQuery('.member-checkbox').prop('checked', isChecked).trigger('change');
        });
        
        // Toggle All button
        jQuery('#toggleAllMembers').on('click', function() {
            var anyUnchecked = jQuery('.member-checkbox:not(:checked)').length > 0;
            jQuery('.member-checkbox').prop('checked', anyUnchecked).trigger('change');
            jQuery('#selectAllCheckbox').prop('checked', anyUnchecked);
        });
        
        // Auto-check and set head resident
        jQuery('#headResidentSelect').on('change', function() {
            var headId = jQuery(this).val();
            if (headId) {
                if (!selectedMembers[headId]) {
                    jQuery('.member-checkbox[data-id="' + headId + '"]').prop('checked', true).trigger('change');
                }
                jQuery('.relationship-select[data-id="' + headId + '"]').val('Head');
                if (selectedMembers[headId]) {
                    selectedMembers[headId].relationship = 'Head';
                }
                updateHiddenField();
            }
        });
        
        // Form submission
        jQuery('#householdForm').on('submit', function (e) {
            var hhNo = jQuery('input[name="household_no"]').val().trim();
            var sitio = jQuery('#sitioSelect').val();
            
            if (!hhNo) { 
                alert('Please enter household number.'); 
                e.preventDefault(); 
                return false; 
            }
            if (!sitio) { 
                alert('Please select Purok/Sitio.');     
                e.preventDefault(); 
                return false; 
            }
            
            updateHiddenField();
            jQuery('#submitBtn').html('<i class="fas fa-spinner fa-spin"></i> Updating…').prop('disabled', true);
        });
    }
    
    // Public methods
    function init(config) {
        BASE_URL = config.baseUrl || '';
        CSRF_TOKEN = config.csrfToken || 'csrf_token';
        CSRF_HASH = config.csrfHash || '';
        currentHouseholdId = config.householdId || null;
        currentHeadId = config.headId || null;
        currentSitio = config.sitio || '';
        currentMembers = config.currentMembers || [];
        
        // Pre-populate selectedMembers from current members
        currentMembers.forEach(function(member) {
            var isHead = (member.id == currentHeadId);
            selectedMembers[member.id] = {
                id: member.id,
                relationship: isHead ? 'Head' : (member.relationship_to_head || '')
            };
        });
        
        if (typeof jQuery === 'undefined') {
            setTimeout(function() { init(config); }, 50);
            return;
        }
        
        jQuery(document).ready(function($) {
            console.log('Household edit initialized');
            
            // Initial load
            if (currentSitio) {
                loadResidents(currentSitio);
            }
            
            initEvents();
            updateHiddenField();
            updateSelectedCount();
        });
    }
    
    // Public API
    return {
        init: init
    };
    
})();
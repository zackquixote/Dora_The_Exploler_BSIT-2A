// public/assets/js/households/households-view.js

var HouseholdView = (function() {
    'use strict';
    
    let BASE_URL = '';
    let CSRF_TOKEN = '';
    let CSRF_HASH = '';
    let RESIDENT_COUNT = 0;
    

    
    function initDelete() {
        jQuery(document).on('click', '.delete-household-view', function (e) {
            e.preventDefault();
            var $btn = jQuery(this);
            var id = $btn.data('id');
            var no = $btn.data('no');
            
            var confirmMsg = 'Delete Household ' + no + '?';
            if (RESIDENT_COUNT > 0) {
                confirmMsg += '\n\nWARNING: This household has ' + RESIDENT_COUNT + ' resident(s).';
                confirmMsg += '\nDeleting will remove all residents from this household.';
            }
            confirmMsg += '\n\nThis action cannot be undone.';
            
            if (!confirm(confirmMsg)) {
                return;
            }
            
            var requestData = {};
            requestData[CSRF_TOKEN] = CSRF_HASH;
            requestData['force'] = true;
            
            jQuery.ajax({
                url: BASE_URL + 'households/delete/' + id,
                type: 'POST',
                data: requestData,
                dataType: 'json',
                success: function (res) {
                    if (res.status === 'success') {
                        window.location.href = BASE_URL + 'households';
                    } else {
                        showToast('error', res.message);
                        if (res.csrf_hash) refreshCsrf(res);
                    }
                },
                error: function (xhr, status, error) {
                    console.error('AJAX Error:', status, error);
                    showToast('error', 'Server error. Please try again.');
                }
            });
        });
    }
    
    function initMembershipEditor() {
        // Edit icon click
        $(document).on('click', '.edit-membership-icon', function(e) {
            e.stopPropagation();
            const residentId = $(this).data('resident-id');
            $(`#membership-display-${residentId}`).hide();
            $(`#membership-editor-${residentId}`).show();
            $(`#membership-select-${residentId}`).focus();
        });
        
        // Cancel icon click
        $(document).on('click', '.cancel-membership-icon', function(e) {
            e.stopPropagation();
            const residentId = $(this).data('resident-id');
            $(`#membership-editor-${residentId}`).hide();
            $(`#membership-display-${residentId}`).show();
        });
        
        // Save icon click
        $(document).on('click', '.save-membership-icon', function(e) {
            e.stopPropagation();
            const residentId = $(this).data('resident-id');
            const select = $(`#membership-select-${residentId}`);
            const newStatus = select.val();
            const badge = $(`#membership-badge-${residentId}`);
            
            $.ajax({
                url: BASE_URL + 'resident/updateMemberStatus/' + residentId,
                type: 'POST',
                data: {
                    member_status: newStatus,
                    [CSRF_TOKEN]: CSRF_HASH
                },
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'success') {
                        const badgeClasses = {
                            active: 'ds-badge-teal',
                            inactive: 'ds-badge-gray',
                            transferred: 'ds-badge-amber',
                            deceased: 'ds-badge-rose'
                        };
                        const cls = badgeClasses[String(newStatus).toLowerCase()] || 'ds-badge-gray';
                        const label = String(newStatus).charAt(0).toUpperCase() + String(newStatus).slice(1);
                        badge.text(label).attr('class', 'ds-badge ' + cls);
                        $(`#membership-editor-${residentId}`).hide();
                        $(`#membership-display-${residentId}`).show();
                        if (res.csrf_hash) refreshCsrf(res);
                    } else {
                        alert(res.message);
                    }
                },
                error: function() {
                    alert('Update failed. Please try again.');
                }
            });
        });
    }
    
    function init(config) {
        BASE_URL = config.baseUrl;
        CSRF_TOKEN = config.csrfToken;
        CSRF_HASH = config.csrfHash;
        RESIDENT_COUNT = config.residentCount;
        
        if (typeof jQuery === 'undefined') {
            setTimeout(() => init(config), 50);
            return;
        }
        
        $(document).ready(function() {
            initDelete();
            initMembershipEditor();
        });
    }
    
    return { init: init, showToast: showToast };
})();

$(document).ready(function() {
    const vars = $('#js-variables').data();
    if (typeof HouseholdView !== 'undefined') {
        HouseholdView.init({
            baseUrl: vars.baseUrl,
            csrfToken: vars.csrfToken,
            csrfHash: vars.csrfHash,
            residentCount: vars.residentCount
        });
    }

    // Open Add Member modal — Bootstrap 4 jQuery API
    $(document).on('click', '#open-add-member-btn', function () {
        $('#addMemberModal').modal('show');
    });
});
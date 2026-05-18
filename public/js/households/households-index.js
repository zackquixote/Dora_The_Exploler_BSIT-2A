// public/assets/js/households/households-index.js

var HouseholdIndex = (function() {
    'use strict';
    
    // Private variables
    var BASE_URL = '';
    var CSRF_TOKEN = '';
    var CSRF_HASH = '';
    
    // Private methods

    
    function initSearch() {
        jQuery('#hhSearch').on('input', function () {
            var q = jQuery(this).val().toLowerCase();
            jQuery('#hhTable tbody tr').each(function () {
                jQuery(this).toggle(jQuery(this).text().toLowerCase().includes(q));
            });
        });
    }
    
    function initDelete() {
        jQuery(document).on('click', '.delete-household', function (e) {
            e.preventDefault();
            e.stopPropagation();
            
            var $btn = jQuery(this);
            var id = $btn.data('id');
            var no = $btn.data('no');
            var $row = $btn.closest('tr');
            var residentCount = parseInt($row.find('.resident-count').text()) || 0;
            
            var confirmMsg = 'Delete Household ' + no + '?';
            if (residentCount > 0) {
                confirmMsg += '\n\nWARNING: This household has ' + residentCount + ' resident(s).';
                confirmMsg += '\nDeleting will remove all residents from this household.';
            }
            confirmMsg += '\n\nThis action cannot be undone.';
            
            if (!confirm(confirmMsg)) {
                return;
            }
            
            $btn.html('<i class="fas fa-spinner fa-spin"></i>').prop('disabled', true);
            
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
                        $row.fadeOut(350, function () { 
                            jQuery(this).remove(); 
                        });
                        showToast('success', res.message);
                        if (res.csrf_hash) refreshCsrf(res);
                    } else {
                        showToast('error', res.message);
                        if (res.csrf_hash) refreshCsrf(res);
                        $btn.html('<i class="fas fa-trash"></i>').prop('disabled', false);
                    }
                },
                error: function (xhr, status, error) {
                    console.error('AJAX Error:', status, error);
                    showToast('error', 'Server error. Please try again.');
                    $btn.html('<i class="fas fa-trash"></i>').prop('disabled', false);
                }
            });
        });
    }
    
    // Public methods
    function init(config) {
        BASE_URL = config.baseUrl || '';
        CSRF_TOKEN = config.csrfToken || 'csrf_token';
        CSRF_HASH = config.csrfHash || '';
        
        if (typeof jQuery === 'undefined') {
            setTimeout(function() { init(config); }, 50);
            return;
        }
        
        jQuery(document).ready(function($) {
            initSearch();
            initDelete();
        });
    }
    
    return {
        init: init,
        showToast: showToast
    };
    
})();

$(document).ready(function() {
    var vars = $('#js-variables').data();
    if (typeof HouseholdIndex !== 'undefined') {
        HouseholdIndex.init({
            baseUrl: vars.baseUrl,
            csrfToken: vars.csrfToken,
            csrfHash: vars.csrfHash
        });
    }
});
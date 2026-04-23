// public/assets/js/households/households-index.js

var HouseholdIndex = (function() {
    'use strict';
    
    // Private variables
    var BASE_URL = '';
    var CSRF_TOKEN = '';
    var CSRF_HASH = '';
    
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
            // Find count in the badge
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
            
            // Disable button and show loading
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
                        updateCSRFToken(res.csrf_hash);
                    } else {
                        showToast('error', res.message);
                        updateCSRFToken(res.csrf_hash);
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
        
        // Wait for jQuery to be available
        if (typeof jQuery === 'undefined') {
            setTimeout(function() { init(config); }, 50);
            return;
        }
        
        jQuery(document).ready(function($) {
            console.log('Household index initialized');
            
            // Initialize functions
            initSearch();
            initDelete();
        });
    }
    
    // Public API
    return {
        init: init,
        showToast: showToast
    };
    
})();

// Initialize with data from the HTML bridge
jQuery(document).ready(function() {
    var vars = jQuery('#js-variables').data();
    if (typeof HouseholdIndex !== 'undefined') {
        HouseholdIndex.init({
            baseUrl: vars.baseUrl,
            csrfToken: vars.csrfToken,
            csrfHash: vars.csrfHash
        });
    }
});
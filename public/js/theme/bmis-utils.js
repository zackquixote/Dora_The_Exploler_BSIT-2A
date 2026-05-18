/**
 * BMIS Global Utilities
 * 
 * Provides common helper functions for AJAX requests, notifications, and session management.
 */

window.redirectIfSessionExpired = function(xhr) {
    if (!xhr || (xhr.status !== 401 && xhr.status !== 403)) {
        return false;
    }
    try {
        var body = xhr.responseJSON || JSON.parse(xhr.responseText || '{}');
        if (body.redirect) {
            window.location.href = body.redirect;
            return true;
        }
    } catch (e) { /* ignore */ }
    var base = (window.baseUrl || '').replace(/\/+$/, '');
    window.location.href = base ? (base + '/login') : '/login';
    return true;
};

window.showToast = function(type, message) {
    if (typeof toastr !== 'undefined') {
        if (type === 'success') {
            toastr.success(message, 'Success');
        } else {
            toastr.error(message, 'Error');
        }
    } else {
        alert(type + ': ' + message);
    }
};

window.refreshCsrf = function(response) {
    if (response && response.csrf_hash) {
        window.csrfHash = response.csrf_hash;
        if (typeof $ !== 'undefined') {
            $('input[name="' + window.csrfName + '"]').val(response.csrf_hash);
        } else {
            const inputs = document.querySelectorAll('input[name="' + window.csrfName + '"]');
            inputs.forEach(input => input.value = response.csrf_hash);
        }
    }
};

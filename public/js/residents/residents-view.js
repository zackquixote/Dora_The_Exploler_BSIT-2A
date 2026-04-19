/**
 * Residents Management - View Page JavaScript
 * Handles print, clipboard copy, and print-mode styles.
 *
 * Globals expected (set in view.php before this script loads):
 *   BASE_URL, CSRF_TOKEN_NAME, CSRF_TOKEN_VALUE, APP
 */

$(document).ready(function () {

    // ============================================
    // PRINT
    // ============================================

    $('#printBtn').on('click', function () {
        window.print();
    });

    // ============================================
    // COPY TO CLIPBOARD
    // ============================================

    $('#copyInfoBtn').on('click', function () {
        var info = '';

        $('.profile-username').each(function () {
            info += 'Name: ' + $.trim($(this).text()) + '\n';
        });

        $('.list-group-item').each(function () {
            var label = $.trim($(this).find('b').text());
            var value = $.trim($(this).find('.float-right').text());
            if (label && value) {
                info += label + ': ' + value + '\n';
            }
        });

        if (navigator.clipboard && info) {
            navigator.clipboard.writeText(info)
                .then(function ()  { showToast('success', 'Resident information copied to clipboard!'); })
                .catch(function () { showToast('error',   'Failed to copy information'); });
        } else {
            showToast('error', 'Clipboard not available in this browser');
        }
    });

    // ============================================
    // TOAST HELPER
    // ============================================

    function showToast(type, message) {
        var bgClass = type === 'success' ? 'bg-success' : 'bg-danger';

        var html =
            '<div class="toast align-items-center text-white ' + bgClass + ' border-0 position-fixed" ' +
            'style="top:20px;right:20px;z-index:9999;" ' +
            'role="alert" aria-live="assertive" aria-atomic="true" ' +
            'data-autohide="true" data-delay="3000">' +
            '<div class="d-flex">' +
            '<div class="toast-body">' + message + '</div>' +
            '<button type="button" class="btn-close btn-close-white me-2 m-auto" ' +
            'data-bs-dismiss="toast" aria-label="Close"></button>' +
            '</div></div>';

        $('.toast').remove();
        $('body').append(html);
        $('.toast').toast('show');

        setTimeout(function () { $('.toast').remove(); }, 3500);
    }

    // ============================================
    // PRINT STYLES (injected at runtime)
    // ============================================

    $('head').append(
        '<style media="print">' +
        '.main-sidebar,.main-header,.footer,.breadcrumb,.btn,.nav-tabs { display:none !important; }' +
        '.content-wrapper,.content,.card,.tab-content { margin:0 !important; padding:0 !important; }' +
        '.profile-user-img { max-width:150px !important; }' +
        'body { background:white !important; }' +
        '</style>'
    );
});
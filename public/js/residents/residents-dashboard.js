/**
 * Residents Management - Dashboard Page JavaScript
 * Handles stat counters and optional auto-refresh.
 *
 * Globals expected (set in dashboard.php before this script loads):
 *   BASE_URL, CSRF_TOKEN_NAME, CSRF_TOKEN_VALUE
 */

$(document).ready(function () {

    console.log('Dashboard initialised');

    // ============================================
    // COUNTER ANIMATION
    // ============================================

    function animateCounter($el, target) {
        var current   = 0;
        var increment = Math.ceil(target / 50) || 1;
        var interval  = setInterval(function () {
            current += increment;
            if (current >= target) {
                current = target;
                clearInterval(interval);
            }
            $el.text(current);
        }, 20);
    }

    animateCounter($('#totalResidents'),  parseInt($('#totalResidents').text())  || 0);
    animateCounter($('#totalHouseholds'), parseInt($('#totalHouseholds').text()) || 0);
    animateCounter($('#pendingCerts'),    parseInt($('#pendingCerts').text())    || 0);
    animateCounter($('#blotterCount'),    parseInt($('#blotterCount').text())    || 0);

    // ============================================
    // OPTIONAL AUTO-REFRESH
    // ============================================

    var autoRefreshInterval = null;

    $('#toggleAutoRefresh').on('click', function () {
        if (autoRefreshInterval) {
            clearInterval(autoRefreshInterval);
            autoRefreshInterval = null;
            $(this).html('<i class="fas fa-play"></i> Auto-Refresh OFF');
        } else {
            autoRefreshInterval = setInterval(refreshStatistics, 30000);
            $(this).html('<i class="fas fa-pause"></i> Auto-Refresh ON');
        }
    });

    function refreshStatistics() {
        $.ajax({
            url:      BASE_URL + 'resident/dashboard-stats',
            type:     'GET',
            dataType: 'json',
            success: function (response) {
                if (response.status === 'success') {
                    $('#totalResidents').text(response.totalResidents);
                    $('#totalHouseholds').text(response.totalHouseholds);
                    $('#pendingCerts').text(response.pendingCerts);
                    $('#blotterCount').text(response.blotterCount);
                    showToast('info', 'Statistics refreshed!');
                }
            },
            error: function () {
                console.warn('Failed to refresh statistics');
            }
        });
    }

    // ============================================
    // TOAST HELPER
    // ============================================

    function showToast(type, message) {
        var bgClass = type === 'success' ? 'bg-success' : (type === 'info' ? 'bg-info' : 'bg-danger');

        var html =
            '<div class="toast align-items-center text-white ' + bgClass + ' border-0 position-fixed" ' +
            'style="top:20px;right:20px;z-index:9999;" ' +
            'role="alert" aria-live="assertive" aria-atomic="true" ' +
            'data-autohide="true" data-delay="2000">' +
            '<div class="d-flex">' +
            '<div class="toast-body">' + message + '</div>' +
            '<button type="button" class="btn-close btn-close-white me-2 m-auto" ' +
            'data-bs-dismiss="toast" aria-label="Close"></button>' +
            '</div></div>';

        $('.toast').remove();
        $('body').append(html);
        $('.toast').toast('show');

        setTimeout(function () { $('.toast').remove(); }, 2500);
    }
});
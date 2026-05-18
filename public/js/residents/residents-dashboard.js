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


});
/**
 * Residents Management - Dashboard Page JavaScript
 * Handles dashboard statistics and charts
 */

$(document).ready(function() {
    // Auto-refresh statistics every 30 seconds (optional)
    var autoRefreshInterval = null;
    
    // Toggle auto-refresh
    $('#toggleAutoRefresh').on('click', function() {
        if (autoRefreshInterval) {
            clearInterval(autoRefreshInterval);
            autoRefreshInterval = null;
            $(this).html('<i class="fas fa-play"></i> Auto-Refresh OFF');
        } else {
            autoRefreshInterval = setInterval(function() {
                refreshStatistics();
            }, 30000);
            $(this).html('<i class="fas fa-pause"></i> Auto-Refresh ON');
        }
    });
    
    // Refresh statistics via AJAX
    function refreshStatistics() {
        $.ajax({
            url: BASE_URL + 'resident/dashboard-stats',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    // Update stats
                    $('#totalResidents').text(response.totalResidents);
                    $('#totalHouseholds').text(response.totalHouseholds);
                    $('#pendingCerts').text(response.pendingCerts);
                    $('#blotterCount').text(response.blotterCount);
                    
                    showToast('info', 'Statistics refreshed!');
                }
            },
            error: function() {
                console.log('Failed to refresh statistics');
            }
        });
    }
    
    // Show toast notification
    function showToast(type, message) {
        var toastClass = type === 'success' ? 'bg-success' : (type === 'info' ? 'bg-info' : 'bg-danger');
        var toastHtml = `
            <div class="toast align-items-center text-white ${toastClass} border-0 position-fixed" style="top: 20px; right: 20px; z-index: 9999;" role="alert" aria-live="assertive" aria-atomic="true" data-autohide="true" data-delay="2000">
                <div class="d-flex">
                    <div class="toast-body">
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `;
        
        $('.toast').remove();
        $('body').append(toastHtml);
        $('.toast').toast('show');
        
        setTimeout(function() {
            $('.toast').remove();
        }, 2000);
    }
    
    // Animate counter on page load
    function animateCounter(element, targetValue) {
        var current = 0;
        var increment = Math.ceil(targetValue / 50);
        var interval = setInterval(function() {
            current += increment;
            if (current >= targetValue) {
                current = targetValue;
                clearInterval(interval);
            }
            $(element).text(current);
        }, 20);
    }
    
    // Animate all stat counters
    animateCounter('#totalResidents', parseInt($('#totalResidents').text()) || 0);
    animateCounter('#totalHouseholds', parseInt($('#totalHouseholds').text()) || 0);
    animateCounter('#pendingCerts', parseInt($('#pendingCerts').text()) || 0);
    animateCounter('#blotterCount', parseInt($('#blotterCount').text()) || 0);
    
    // Log initialization
    console.log('Dashboard initialized');
});
/**
 * Residents Management - Index Page JavaScript
 * Handles DataTable initialization, filtering, and delete operations
 */

$(document).ready(function() {
    // Initialize DataTable
    var residentsTable = $('#residentsTable').DataTable({
        "order": [[0, "desc"]],
        "pageLength": 10,
        "responsive": true,
        "language": {
            "search": "Search residents:",
            "emptyTable": "No residents found",
            "info": "Showing _START_ to _END_ of _TOTAL_ residents",
            "infoEmpty": "Showing 0 to 0 of 0 residents",
            "infoFiltered": "(filtered from _MAX_ total residents)"
        }
    });
    
    // Auto-submit purok filter when changed
    $('#purokFilter').on('change', function() {
        $('#purokFilterForm').submit();
    });
    
    // Delete resident handler
    $(document).on('click', '.delete-resident', function() {
        var residentId = $(this).data('id');
        var row = $(this).closest('tr');
        
        if (confirm('Are you sure you want to delete this resident? This action cannot be undone.')) {
            $.ajax({
                url: BASE_URL + 'resident/delete/' + residentId,
                type: 'POST',
                data: {
                    [CSRF_TOKEN_NAME]: CSRF_TOKEN_VALUE
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        // Remove row from table
                        residentsTable.row(row).remove().draw();
                        
                        // Show success message
                        showAlert('success', 'Resident deleted successfully!');
                        
                        // Reload page after 1 second to update counts
                        setTimeout(function() {
                            location.reload();
                        }, 1000);
                    } else {
                        showAlert('danger', 'Delete failed: ' + (response.message || 'Unknown error'));
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                    showAlert('danger', 'Error deleting resident. Please try again.');
                }
            });
        }
    });
    
    // Function to show alert messages
    function showAlert(type, message) {
        var alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        var alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        `;
        
        // Remove existing alerts
        $('.alert').remove();
        
        // Insert new alert at top of content
        $('.content').prepend(alertHtml);
        
        // Auto-dismiss after 3 seconds
        setTimeout(function() {
            $('.alert').fadeOut('slow', function() {
                $(this).remove();
            });
        }, 3000);
    }
    
    // Highlight current purok filter in statistics cards
    if (CURRENT_PUROK && CURRENT_PUROK !== 'all') {
        $('.purok-stat-card').each(function() {
            var href = $(this).attr('href');
            if (href && href.indexOf(CURRENT_PUROK) !== -1) {
                $(this).find('.small-box').css({
                    'border': '3px solid #ffc107',
                    'transform': 'scale(1.05)'
                });
            }
        });
    }
    
    // Confirm before clearing filter
    $('#clearFilterBtn, #showAllBtn').on('click', function(e) {
        if (CURRENT_PUROK && CURRENT_PUROK !== 'all') {
            var confirmClear = confirm('Clear filter and show all residents?');
            if (!confirmClear) {
                e.preventDefault();
            }
        }
    });
    
    // Log initialization
    console.log('Residents index page initialized');
    console.log('Current filter:', CURRENT_PUROK === 'all' ? 'All residents' : CURRENT_PUROK);
});
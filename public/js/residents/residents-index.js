/**
 * Residents Management - Index Page JavaScript
 */

// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded - Initializing residents page');
    
    // Initialize DataTable
    var residentsTable;
    
    if ($.fn.DataTable && $('#residentsTable').length) {
        residentsTable = $('#residentsTable').DataTable({
            "order": [[0, "desc"]],
            "pageLength": 10,
            "responsive": true,
            "autoWidth": false,
            "language": {
                "search": "Search residents:",
                "emptyTable": "No residents found",
                "info": "Showing _START_ to _END_ of _TOTAL_ residents",
                "infoEmpty": "Showing 0 to 0 of 0 residents",
                "infoFiltered": "(filtered from _MAX_ total residents)"
            }
        });
    }
    
    // Auto-submit purok filter when changed
    $('#purokFilter').on('change', function() {
        $('#purokFilterForm').submit();
    });
    
    // Delete resident handler - Using event delegation
    $(document).on('click', '.delete-resident', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        var $btn = $(this);
        var residentId = $btn.data('id');
        var $row = $btn.closest('tr');
        
        console.log('Delete button clicked for ID:', residentId);
        
        if (!residentId) {
            alert('Invalid resident ID');
            return;
        }
        
        if (confirm('Are you sure you want to delete this resident? This action cannot be undone.')) {
            // Disable button and show loading
            $btn.html('<i class="fas fa-spinner fa-spin"></i>').prop('disabled', true);
            
            $.ajax({
                url: BASE_URL + 'resident/delete/' + residentId,
                type: 'POST',
                data: {
                    [CSRF_TOKEN_NAME]: CSRF_TOKEN_VALUE
                },
                dataType: 'json',
                timeout: 30000,
                success: function(response) {
                    console.log('Delete response:', response);
                    
                    if (response.status === 'success') {
                        // Remove row from table
                        if (residentsTable) {
                            residentsTable.row($row).remove().draw();
                        } else {
                            $row.fadeOut(300, function() {
                                $(this).remove();
                            });
                        }
                        
                        alert('Resident deleted successfully!');
                        
                        // Reload page after 1 second
                        setTimeout(function() {
                            location.reload();
                        }, 1000);
                    } else {
                        alert('Delete failed: ' + (response.message || 'Unknown error'));
                        $btn.html('<i class="fas fa-trash"></i> Delete').prop('disabled', false);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', status, error);
                    alert('Error deleting resident. Please try again. Error: ' + error);
                    $btn.html('<i class="fas fa-trash"></i> Delete').prop('disabled', false);
                }
            });
        }
    });
    
    // Highlight current purok filter
    if (typeof CURRENT_PUROK !== 'undefined' && CURRENT_PUROK && CURRENT_PUROK !== 'all') {
        $('.purok-stat-card .small-box').each(function() {
            var parentLink = $(this).closest('a');
            if (parentLink.length && parentLink.attr('href') && parentLink.attr('href').indexOf(encodeURIComponent(CURRENT_PUROK)) !== -1) {
                $(this).css({
                    'border': '3px solid #ffc107',
                    'transform': 'scale(1.05)'
                });
            }
        });
    }
    
    // Clear filter confirmation
    $('#clearFilterBtn, #showAllBtn').on('click', function(e) {
        if (typeof CURRENT_PUROK !== 'undefined' && CURRENT_PUROK && CURRENT_PUROK !== 'all') {
            if (!confirm('Clear filter and show all residents?')) {
                e.preventDefault();
            }
        }
    });
    
    console.log('Residents page initialized successfully');
});

// Also bind on window load for safety
$(window).on('load', function() {
    console.log('Window loaded - checking delete buttons again');
    console.log('Delete buttons count:', $('.delete-resident').length);
});
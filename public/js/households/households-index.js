/**
 * Households Management - Index Page JavaScript
 */

// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded - Initializing households page');
    
    // Initialize DataTable if exists
    if ($.fn.DataTable && $('#householdsTable').length) {
        $('#householdsTable').DataTable({
            "order": [[0, "desc"]],
            "pageLength": 10,
            "responsive": true,
            "autoWidth": false,
            "language": {
                "search": "Search households:",
                "emptyTable": "No households found",
                "info": "Showing _START_ to _END_ of _TOTAL_ households",
                "infoEmpty": "Showing 0 to 0 of 0 households"
            }
        });
    }
    
    // Auto-submit purok filter when changed
    $('#purokFilter').on('change', function() {
        $('#purokFilterForm').submit();
    });
    
    // Delete household handler - Using event delegation
    $(document).on('click', '.delete-household', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        var $btn = $(this);
        var householdId = $btn.data('id');
        var $row = $btn.closest('tr');
        
        console.log('Delete button clicked for Household ID:', householdId);
        
        if (!householdId) {
            alert('Invalid household ID');
            return;
        }
        
        if (confirm('Are you sure you want to delete this household? This action cannot be undone.')) {
            // Disable button and show loading
            $btn.html('<i class="fas fa-spinner fa-spin"></i>').prop('disabled', true);
            
            $.ajax({
                url: BASE_URL + 'households/delete/' + householdId,
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
                        $row.fadeOut(300, function() {
                            $(this).remove();
                            // Check if table is empty
                            if ($('#householdsTable tbody tr').length === 0) {
                                location.reload();
                            }
                        });
                        
                        alert('Household deleted successfully!');
                        
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
                    
                    // Parse error response if possible
                    var errorMsg = 'Error deleting household. Please try again.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    
                    alert(errorMsg);
                    $btn.html('<i class="fas fa-trash"></i> Delete').prop('disabled', false);
                }
            });
        }
    });
    
    // Clear filter confirmation
    $('#clearFilterBtn, #showAllBtn').on('click', function(e) {
        if (typeof CURRENT_PUROK !== 'undefined' && CURRENT_PUROK && CURRENT_PUROK !== 'all') {
            if (!confirm('Clear filter and show all households?')) {
                e.preventDefault();
            }
        }
    });
    
    console.log('Households page initialized successfully');
});

// Also bind on window load for safety
$(window).on('load', function() {
    console.log('Window loaded - checking delete buttons again');
    console.log('Delete buttons count:', $('.delete-household').length);
});
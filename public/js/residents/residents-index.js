/**
 * Residents Management - Index Page JavaScript
 * Handles DataTable initialisation, purok filtering, and delete operations.
 */

(function() {
    'use strict';

    // Ensure dependencies are loaded
    function initWhenReady() {
        if (typeof jQuery === 'undefined' || typeof $.fn.DataTable === 'undefined') {
            setTimeout(initWhenReady, 50);
            return;
        }

        jQuery(document).ready(function($) {
            console.log('Residents index page loaded');

            // ============================================
            // DATATABLE INITIALISATION
            // ============================================
            var residentsTable = null;

            if ($('#residentsTable').length) {
                residentsTable = $('#residentsTable').DataTable({
                    order:      [[0, 'desc']],
                    pageLength: 10,
                    responsive: true,
                    autoWidth:  false,
                    language: {
                        search:      'Search residents:',
                        emptyTable:  'No residents found',
                        info:        'Showing _START_ to _END_ of _TOTAL_ residents',
                        infoEmpty:   'Showing 0 to 0 of 0 residents',
                        infoFiltered:'(filtered from _MAX_ total residents)',
                        lengthMenu:  'Show _MENU_ residents',
                        paginate: {
                            first:    'First',
                            last:     'Last',
                            next:     'Next',
                            previous: 'Previous'
                        }
                    },
                    columnDefs: [
                        { orderable: true,  targets: [0, 2, 3, 4, 5, 6, 7, 8, 9] },
                        { orderable: false, targets: [1, 10, 11, 12] }
                    ]
                });
            }

            // ============================================
            // PUROK FILTER LOGIC
            // ============================================
            $('#clearFilterBtn').on('click', function (e) {
                // Only confirm if we are actually on a filtered page
                if (RESIDENTS_CONFIG.currentPurok && RESIDENTS_CONFIG.currentPurok !== 'all') {
                    if (!confirm('Clear filter and show all residents?')) {
                        e.preventDefault();
                        return false;
                    }
                }
            });

            // Highlight active purok in stats cards based on URL
            var activePurok = RESIDENTS_CONFIG.currentPurok || 'all';
            
            if (activePurok && activePurok !== 'all') {
                $('.purok-stat-card').each(function () {
                    var cardName = $(this).data('purok-name');
                    
                    if (cardName === activePurok) {
                        $(this).find('.small-box').css({
                            border:    '3px solid #ffc107',
                            transform: 'scale(1.05)'
                        });
                    }
                });
            }

            // ============================================
            // DELETE RESIDENT LOGIC
            // ============================================
            $(document).on('click', '.delete-resident', function (e) {
                e.preventDefault();
                e.stopPropagation();

                var $btn       = $(this);
                var residentId = $btn.data('id');
                var $row       = $btn.closest('tr');

                if (!residentId) {
                    showAlert('danger', 'Invalid resident ID');
                    return;
                }

                if (!confirm('Are you sure you want to delete this resident? This action cannot be undone.')) {
                    return;
                }

                // Show loading state
                $btn.html('<i class="fas fa-spinner fa-spin"></i>').prop('disabled', true);

                // Prepare CSRF data
                var csrfData = {};
                if (RESIDENTS_CONFIG.csrfName && RESIDENTS_CONFIG.csrfHash) {
                    csrfData[RESIDENTS_CONFIG.csrfName] = RESIDENTS_CONFIG.csrfHash;
                }

                $.ajax({
                    url:      RESIDENTS_CONFIG.baseUrl + 'resident/delete/' + residentId,
                    type:     'POST',
                    data:     csrfData,
                    dataType: 'json',
                    timeout:  30000,
                    success: function (response) {
                        if (response.status === 'success') {
                            // Update CSRF token if a new one is provided
                            if (response.csrf_hash) {
                                RESIDENTS_CONFIG.csrfHash = response.csrf_hash;
                                $('input[name="' + RESIDENTS_CONFIG.csrfName + '"]').val(response.csrf_hash);
                            }

                            // Remove row from DataTable or DOM
                            if (residentsTable) {
                                residentsTable.row($row).remove().draw();
                            } else {
                                $row.fadeOut(300, function () { $(this).remove(); });
                            }

                            showAlert('success', 'Resident deleted successfully!');

                            // Reload page after short delay to update stats
                            setTimeout(function () { 
                                window.location.reload(); 
                            }, 1500);
                        } else {
                            showAlert('danger', 'Delete failed: ' + (response.message || 'Unknown error'));
                            $btn.html('<i class="fas fa-trash"></i>').prop('disabled', false);
                        }
                    },
                    error: function (xhr) {
                        var msg = 'Error deleting resident. Please try again.';
                        if (xhr.status === 403) {
                            msg = 'CSRF token mismatch. Please refresh the page.';
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        showAlert('danger', msg);
                        $btn.html('<i class="fas fa-trash"></i>').prop('disabled', false);
                    }
                });
            });

            // ============================================
            // ALERT HELPER FUNCTION
            // ============================================
            function showAlert(type, message) {
                var alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
                var icon       = type === 'success' ? 'check-circle'  : 'exclamation-circle';

                var html =
                    '<div class="alert ' + alertClass + ' alert-dismissible fade show" role="alert">' +
                    '<i class="fas fa-' + icon + ' mr-2"></i> ' + message +
                    '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                    '<span aria-hidden="true">&times;</span></button></div>';

                // Remove existing alerts and prepend new one
                $('.alert').remove();
                $('.content .container-fluid').prepend(html);

                // Auto dismiss after 5 seconds
                setTimeout(function () {
                    $('.alert').fadeOut('slow', function () { $(this).remove(); });
                }, 5000);
            }
        });
    }

    // Start initialization check
    initWhenReady();

})();
/**
 * Residents Management - Index Page JavaScript
 * Handles DataTable initialisation, purok filtering, and delete operations.
 *
 * Globals expected (set in index.php before this script loads):
 *   BASE_URL, CSRF_TOKEN_NAME, CSRF_TOKEN_VALUE, CURRENT_PUROK
 */

$(document).ready(function () {

    console.log('Residents index page loaded');

    // ============================================
    // DATATABLE INITIALISATION
    // ============================================

    var residentsTable = null;

    if ($.fn.DataTable && $('#residentsTable').length) {
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
    // PUROK FILTER
    // ============================================

    $('#purokFilter').on('change', function () {
        $('#purokFilterForm').submit();
    });

    $('#clearFilterBtn').on('click', function (e) {
        var current = (typeof CURRENT_PUROK !== 'undefined') ? CURRENT_PUROK : 'all';
        if (current && current !== 'all') {
            if (!confirm('Clear filter and show all residents?')) {
                e.preventDefault();
                return false;
            }
        }
    });

    // Highlight active purok in stats cards
    var activePurok = (typeof CURRENT_PUROK !== 'undefined') ? CURRENT_PUROK : 'all';
    if (activePurok && activePurok !== 'all') {
        $('.purok-stat-card').each(function () {
            var href = $(this).attr('href') || '';
            if (href.indexOf(encodeURIComponent(activePurok)) !== -1) {
                $(this).find('.small-box').css({
                    border:    '3px solid #ffc107',
                    transform: 'scale(1.05)'
                });
            }
        });
    }

    // ============================================
    // DELETE RESIDENT
    // ============================================

    $(document).on('click', '.delete-resident', function (e) {
        e.preventDefault();
        e.stopPropagation();

        var $btn        = $(this);
        var residentId  = $btn.data('id');
        var $row        = $btn.closest('tr');

        if (!residentId) {
            showAlert('danger', 'Invalid resident ID');
            return;
        }

        if (!confirm('Are you sure you want to delete this resident? This action cannot be undone.')) {
            return;
        }

        $btn.html('<i class="fas fa-spinner fa-spin"></i>').prop('disabled', true);

        var csrfData = {};
        if (typeof CSRF_TOKEN_NAME !== 'undefined' && typeof CSRF_TOKEN_VALUE !== 'undefined') {
            csrfData[CSRF_TOKEN_NAME] = CSRF_TOKEN_VALUE;
        }

        $.ajax({
            url:      BASE_URL + 'resident/delete/' + residentId,
            type:     'POST',
            data:     csrfData,
            dataType: 'json',
            timeout:  30000,
            success: function (response) {
                if (response.status === 'success') {
                    if (response.csrf_hash) {
                        CSRF_TOKEN_VALUE = response.csrf_hash;
                        $('input[name="' + CSRF_TOKEN_NAME + '"]').val(response.csrf_hash);
                    }

                    if (residentsTable) {
                        residentsTable.row($row).remove().draw();
                    } else {
                        $row.fadeOut(300, function () { $(this).remove(); });
                    }

                    showAlert('success', 'Resident deleted successfully!');
                    setTimeout(function () { location.reload(); }, 1500);
                } else {
                    showAlert('danger', 'Delete failed: ' + (response.message || 'Unknown error'));
                    $btn.html('<i class="fas fa-trash"></i> Delete').prop('disabled', false);
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
                $btn.html('<i class="fas fa-trash"></i> Delete').prop('disabled', false);
            }
        });
    });

    // ============================================
    // ALERT HELPER
    // ============================================

    function showAlert(type, message) {
        var alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        var icon       = type === 'success' ? 'check-circle'  : 'exclamation-circle';

        var html =
            '<div class="alert ' + alertClass + ' alert-dismissible fade show" role="alert">' +
            '<i class="fas fa-' + icon + ' mr-2"></i> ' + message +
            '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
            '<span aria-hidden="true">&times;</span></button></div>';

        $('.alert').remove();
        $('.content .container-fluid').prepend(html);

        setTimeout(function () {
            $('.alert').fadeOut('slow', function () { $(this).remove(); });
        }, 5000);
    }
});
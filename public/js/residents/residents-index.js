/**
 * Residents Index – DataTable + Live Filters + Stats
 */
(function($) {
    'use strict';

    let residentsTable = null;

    function updateStats() {
        // Count visible rows only (after DataTable search & filters)
        let rows = $('#residentsTable tbody tr:visible');
        let seniors = 0, pwd = 0, voters = 0;
        rows.each(function() {
            let flags = $(this).find('td:eq(9)').text();
            if (flags.includes('Senior')) seniors++;
            if (flags.includes('PWD')) pwd++;
            if (flags.includes('Voter')) voters++;
        });
        $('#seniorCount').text(seniors);
        $('#pwdCount').text(pwd);
        $('#voterCount').text(voters);
    }

    function filterTable() {
        let search = $('#searchName').val().toLowerCase();
        let purok = $('#filterPurok').val();
        let household = $('#filterHousehold').val().toLowerCase();

        // Use DataTable's search for the name field (column index 2 = Full Name)
        if (residentsTable) {
            residentsTable.columns(2).search(search).draw();
        }

        // For custom filters (purok & household), we manually hide rows
        $('#residentsTable tbody tr').each(function() {
            let rowPurok = $(this).find('td:eq(6)').text().trim();
            let rowHousehold = $(this).find('td:eq(7)').text().toLowerCase();

            let show = (purok === 'all' || rowPurok === purok) &&
                       (household === '' || rowHousehold.includes(household));
            $(this).toggle(show);
        });
        updateStats();
    }

    $(document).ready(function() {
        // Init DataTable
        if ($('#residentsTable').length) {
            residentsTable = $('#residentsTable').DataTable({
                order: [[0, 'desc']],
                pageLength: 10,
                responsive: true,
                autoWidth: false,
                language: {
                    search: 'Search residents:',
                    emptyTable: 'No residents found',
                    info: 'Showing _START_ to _END_ of _TOTAL_ residents',
                    infoEmpty: 'Showing 0 to 0 of 0 residents',
                    infoFiltered: '(filtered from _MAX_ total residents)'
                },
                columnDefs: [
                    { orderable: true, targets: [0,2,3,4,5,6,7,8,9] },
                    { orderable: false, targets: [1,10] }
                ]
            });

            // Attach filter events
            $('#searchName, #filterPurok, #filterHousehold').on('keyup change', filterTable);
            $('#clearFilters').on('click', function() {
                $('#searchName').val('');
                $('#filterPurok').val('all');
                $('#filterHousehold').val('');
                if (residentsTable) residentsTable.columns(2).search('').draw();
                filterTable();
            });
        }

        // Delete handler (using RESIDENTS_CONFIG)
        $(document).on('click', '.delete-resident', function(e) {
            e.preventDefault();
            let $btn = $(this);
            let id = $btn.data('id');
            let name = $btn.data('name');

            if (!id) return;
            if (!confirm(`Delete ${name}? This cannot be undone.`)) return;

            $btn.html('<i class="fas fa-spinner fa-spin"></i>').prop('disabled', true);

            let data = {};
            data[RESIDENTS_CONFIG.csrfName] = RESIDENTS_CONFIG.csrfHash;

            $.ajax({
                url: RESIDENTS_CONFIG.baseUrl + 'resident/delete/' + id,
                type: 'POST',
                data: data,
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'success') {
                        if (residentsTable) {
                            residentsTable.row($btn.closest('tr')).remove().draw();
                        } else {
                            $btn.closest('tr').remove();
                        }
                        updateStats();
                        showAlert('success', 'Resident deleted.');
                        if (res.csrf_hash) {
                            RESIDENTS_CONFIG.csrfHash = res.csrf_hash;
                            $('input[name="' + RESIDENTS_CONFIG.csrfName + '"]').val(res.csrf_hash);
                        }
                    } else {
                        showAlert('danger', res.message || 'Delete failed');
                        $btn.html('<i class="fas fa-trash"></i>').prop('disabled', false);
                    }
                },
                error: function() {
                    showAlert('danger', 'Server error. Please try again.');
                    $btn.html('<i class="fas fa-trash"></i>').prop('disabled', false);
                }
            });
        });

        function showAlert(type, msg) {
            $('.alert').remove();
            let alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
            let icon = type === 'success' ? 'check-circle' : 'exclamation-circle';
            let html = `<div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                <i class="fas fa-${icon} mr-2"></i> ${msg}
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>`;
            $('.content .container-fluid').prepend(html);
            setTimeout(() => $('.alert').fadeOut('slow', function() { $(this).remove(); }), 4000);
        }

        // Highlight active purok tile
        let activePurok = RESIDENTS_CONFIG.currentPurok;
        if (activePurok && activePurok !== 'all') {
            $('.small-box').each(function() {
                let tilePurok = $(this).closest('.col-md-2').find('p').text().trim();
                if (tilePurok === activePurok) {
                    $(this).css({ border: '3px solid #ffc107', transform: 'scale(1.05)' });
                }
            });
        }

        // Initial stats
        updateStats();
    });
})(jQuery);
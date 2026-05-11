/**
 * blotter-index.js
 * Handles the blotter index page:
 * - Delete confirmation (inline form submit)
 * - Live search + status/purok filters
 */
$(function () {

    // ── Delete confirmation ───────────────────────────────────────────
    // The delete buttons are inside <form data-confirm="..."> elements.
    // Intercept submit and show a native confirm dialog.
    $(document).on('submit', 'form[data-confirm]', function (e) {
        var msg = $(this).data('confirm') || 'Are you sure you want to delete this record?';
        if (!confirm(msg)) {
            e.preventDefault();
        }
    });

    // ── Live filters ──────────────────────────────────────────────────
    function filterTable() {
        var search = $('#searchCase').val().toLowerCase();
        var status = $('#filterStatus').val();
        var purok  = $('#filterPurok').val();

        $('#blotterTable tbody tr').each(function () {
            var text      = $(this).text().toLowerCase();
            var rowStatus = $(this).data('status');
            var rowPurok  = $(this).data('purok');
            var show = text.includes(search) &&
                       (!status || rowStatus === status) &&
                       (!purok  || rowPurok  === purok);
            $(this).toggle(show);
        });
    }

    $('#searchCase, #filterStatus, #filterPurok').on('keyup change', filterTable);

    $('#clearFilters').on('click', function () {
        $('#searchCase').val('');
        $('#filterStatus').val('');
        $('#filterPurok').val('');
        filterTable();
    });
});

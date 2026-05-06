$(function() {
    // Delete modal
    $('.delete-btn').click(function() {
        let id = $(this).data('id');
        let caseRef = $(this).data('case');
        $('#delete-case-ref').text(caseRef);
        $('#delete-form').attr('action', window.blotterConfig.deleteUrl + '/' + id);
        $('#deleteModal').modal('show');
    });

    // Live filters
    function filterTable() {
        let search = $('#searchCase').val().toLowerCase();
        let status = $('#filterStatus').val();
        let purok = $('#filterPurok').val();
        $('#blotterTable tbody tr').each(function() {
            let text = $(this).text().toLowerCase();
            let rowStatus = $(this).data('status');
            let rowPurok = $(this).data('purok');
            let show = text.includes(search) && 
                       (!status || rowStatus === status) && 
                       (!purok || rowPurok === purok);
            $(this).toggle(show);
        });
    }

    $('#searchCase, #filterStatus, #filterPurok').on('keyup change', filterTable);
    $('#clearFilters').click(function() {
        $('#searchCase').val('');
        $('#filterStatus').val('');
        $('#filterPurok').val('');
        filterTable();
    });
});
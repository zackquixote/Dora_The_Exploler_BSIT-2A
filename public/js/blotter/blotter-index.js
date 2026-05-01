/**
 * Blotter Index Page
 * Handles delete confirmation modal
 */

$(function() {
    $('.delete-btn').click(function() {
        let id = $(this).data('id');
        let caseRef = $(this).data('case');
        $('#delete-case-ref').text(caseRef);
        $('#delete-form').attr('action', window.blotterConfig.deleteUrl + '/' + id);
        $('#deleteModal').modal('show');
    });
});
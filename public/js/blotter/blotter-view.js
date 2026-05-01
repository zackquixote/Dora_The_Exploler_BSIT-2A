/**
 * Blotter View Page
 * Handles delete confirmation, hearing modal, AJAX for hearings
 */

$(function() {
    // Delete case modal
    $('.delete-btn').click(function() {
        let id = $(this).data('id');
        let caseRef = $(this).data('case');
        $('#delete-case-ref').text(caseRef);
        $('#delete-form').attr('action', window.blotterConfig.deleteUrl + '/' + id);
        $('#deleteModal').modal('show');
    });

    // Hearing modal reset
    $('#addHearingModal').on('show.bs.modal', function() {
        $('#hearing-id').val('');
        $('#hearing-form')[0].reset();
        $('#modal-save-btn').text('Save');
        $('#hearing-form').attr('action', window.blotterConfig.hearingAddUrl);
    });

    // Edit hearing - populate modal
    $(document).on('click', '.edit-hearing', function(e) {
        e.preventDefault();
        let btn = $(this);
        $('#hearing-id').val(btn.data('id'));
        $('input[name="hearing_date"]').val(btn.data('date'));
        $('input[name="hearing_time"]').val(btn.data('time'));
        $('input[name="venue"]').val(btn.data('venue'));
        $('input[name="presiding_officer"]').val(btn.data('officer'));
        $('textarea[name="notes"]').val(btn.data('notes'));
        $('select[name="status"]').val(btn.data('status'));
        let hearingId = btn.data('id');
        $('#hearing-form').attr('action', window.blotterConfig.hearingUpdateUrl + '/' + hearingId);
        $('#modal-save-btn').text('Update');
        $('#addHearingModal').modal('show');
    });

    // AJAX submit hearing form
    $('#hearing-form').submit(function(e) {
        e.preventDefault();
        let form = $(this);
        $.post(form.attr('action'), form.serialize(), function(resp) {
            if (resp.status === 'success') {
                location.reload();
            } else {
                alert(resp.message);
            }
        }, 'json');
    });

    // Delete hearing
    $(document).on('click', '.delete-hearing', function(e) {
        e.preventDefault();
        if (confirm('Delete this hearing?')) {
            let id = $(this).data('id');
            let data = {};
            data[window.blotterConfig.csrfToken] = window.blotterConfig.csrfHash;
            $.ajax({
                url: window.blotterConfig.hearingDeleteUrl + '/' + id,
                type: 'DELETE',
                data: data,
                success: function(resp) {
                    if (resp.status === 'success') location.reload();
                    else alert(resp.message);
                }
            });
        }
    });
});
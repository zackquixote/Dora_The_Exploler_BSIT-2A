/**
 * blotter-view.js
 * Handles the blotter case view page:
 * - Delete case modal (Bootstrap 4 jQuery API)
 * - Add/Edit/Delete hearing via AJAX
 * - CSRF token refresh after each AJAX call
 */
$(function () {

    // ── Helpers ──────────────────────────────────────────────────────
    function showModal(id) {
        $('#' + id).modal('show');
    }

    function hideModal(id) {
        $('#' + id).modal('hide');
    }

    function refreshCsrf(newHash) {
        if (!newHash) return;
        window.blotterConfig.csrfHash = newHash;
        $('input[name="' + window.blotterConfig.csrfToken + '"]').val(newHash);
    }

    // ── Open hearing modal (Add button) ───────────────────────────────
    $(document).on('click', '#open-hearing-modal-btn', function () {
        // Reset form to Add mode
        $('#hearing-id').val('');
        $('#hearing-form')[0].reset();
        $('#modal-save-btn').text('Save');
        $('#hearing-form').attr('action', window.blotterConfig.hearingAddUrl);
        showModal('addHearingModal');
    });

    // ── Cancel buttons ────────────────────────────────────────────────
    $(document).on('click', '#cancel-hearing-btn', function () {
        hideModal('addHearingModal');
    });

    $(document).on('click', '#cancel-delete-btn', function () {
        hideModal('deleteModal');
    });

    // ── Delete case modal ─────────────────────────────────────────────
    $(document).on('click', '.delete-btn', function () {
        var id      = $(this).data('id');
        var caseRef = $(this).data('case');
        $('#delete-case-ref').text(caseRef);
        $('#delete-form').attr('action', window.blotterConfig.deleteUrl + '/' + id);
        showModal('deleteModal');
    });

    // ── Edit hearing: populate modal ──────────────────────────────────
    $(document).on('click', '.edit-hearing', function (e) {
        e.preventDefault();
        var btn = $(this);
        $('#hearing-id').val(btn.data('id'));
        $('input[name="hearing_date"]').val(btn.data('date'));
        $('input[name="hearing_time"]').val(btn.data('time'));
        $('input[name="venue"]').val(btn.data('venue'));
        $('input[name="presiding_officer"]').val(btn.data('officer'));
        $('textarea[name="notes"]').val(btn.data('notes'));
        $('input[name="outcome"]').val(btn.data('outcome') || '');
        $('select[name="status"]').val(btn.data('status'));
        $('#hearing-form').attr('action', window.blotterConfig.hearingUpdateUrl + '/' + btn.data('id'));
        $('#modal-save-btn').text('Update');
        showModal('addHearingModal');
    });

    // ── AJAX submit hearing form ──────────────────────────────────────
    $('#hearing-form').on('submit', function (e) {
        e.preventDefault();
        var form = $(this);
        var $btn = $('#modal-save-btn');

        // Disable button to prevent double-submit
        $btn.prop('disabled', true).text('Saving…');

        // Refresh CSRF before serialising
        $('input[name="' + window.blotterConfig.csrfToken + '"]').val(window.blotterConfig.csrfHash);

        $.post(form.attr('action'), form.serialize(), function (resp) {
            refreshCsrf(resp.csrf_hash);
            if (resp.status === 'success') {
                hideModal('addHearingModal');
                location.reload();
            } else {
                $btn.prop('disabled', false).text($btn.data('original-text') || 'Save');
                // Show error inside modal
                var $err = $('#hearing-error-msg');
                if (!$err.length) {
                    $err = $('<div id="hearing-error-msg" style="background:var(--c-rose-bg);color:var(--c-rose);padding:8px 12px;border-radius:6px;font-size:12px;font-weight:600;margin-bottom:10px"></div>');
                    form.find('.ds-card-body, div[style*="padding:16px"]').first().prepend($err);
                }
                $err.text(resp.message || 'An error occurred. Please try again.').show();
            }
        }, 'json').fail(function (xhr) {
            $btn.prop('disabled', false).text('Save');
            alert('Request failed (HTTP ' + xhr.status + '). Please refresh and try again.');
        });
    });

    // Store original button text on page load
    $('#modal-save-btn').data('original-text', $('#modal-save-btn').text());

    // Clear error message when modal opens fresh
    $('#addHearingModal').on('show.bs.modal', function () {
        $('#hearing-error-msg').hide();
    });

    // ── Delete hearing ────────────────────────────────────────────────
    $(document).on('click', '.delete-hearing', function (e) {
        e.preventDefault();
        if (!confirm('Delete this hearing?')) return;

        var id   = $(this).data('id');
        var data = {};
        data[window.blotterConfig.csrfToken] = window.blotterConfig.csrfHash;

        $.ajax({
            url:      window.blotterConfig.hearingDeleteUrl + '/' + id,
            type:     'POST',
            data:     data,
            dataType: 'json',
            success: function (resp) {
                refreshCsrf(resp.csrf_hash);
                if (resp.status === 'success') {
                    location.reload();
                } else {
                    alert(resp.message || 'Delete failed.');
                }
            },
            error: function () {
                alert('Request failed. Please refresh and try again.');
            }
        });
    });
});

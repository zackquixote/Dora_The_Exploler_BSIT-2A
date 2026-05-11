/**
 * blotter-view.js
 * Handles the blotter case view page:
 * - Delete case modal
 * - Add/Edit/Delete hearing via AJAX (Bootstrap 5 modal)
 * - CSRF token refresh after each AJAX call
 */
$(function () {

    // ── Helpers ──────────────────────────────────────────────────────
    function getModal(id) {
        var el = document.getElementById(id);
        if (!el) return null;
        return bootstrap.Modal.getOrCreateInstance(el);
    }

    function refreshCsrf(newHash) {
        if (!newHash) return;
        window.blotterConfig.csrfHash = newHash;
        $('input[name="' + window.blotterConfig.csrfToken + '"]').val(newHash);
    }

    // ── Delete case modal ─────────────────────────────────────────────
    $(document).on('click', '.delete-btn', function () {
        var id       = $(this).data('id');
        var caseRef  = $(this).data('case');
        $('#delete-case-ref').text(caseRef);
        $('#delete-form').attr('action', window.blotterConfig.deleteUrl + '/' + id);
        getModal('deleteModal').show();
    });

    $(document).on('click', '[data-bs-dismiss="modal"]', function () {
        var modalEl = $(this).closest('.modal')[0];
        if (modalEl) bootstrap.Modal.getInstance(modalEl).hide();
    });

    // ── Hearing modal: reset on open ──────────────────────────────────
    var hearingModalEl = document.getElementById('addHearingModal');
    if (hearingModalEl) {
        hearingModalEl.addEventListener('show.bs.modal', function () {
            $('#hearing-id').val('');
            $('#hearing-form')[0].reset();
            $('#modal-save-btn').text('Save');
            $('#hearing-form').attr('action', window.blotterConfig.hearingAddUrl);
        });
    }

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
        getModal('addHearingModal').show();
    });

    // ── AJAX submit hearing form ──────────────────────────────────────
    $('#hearing-form').on('submit', function (e) {
        e.preventDefault();
        var form = $(this);

        // Always use the latest CSRF hash before serialising
        $('input[name="' + window.blotterConfig.csrfToken + '"]').val(window.blotterConfig.csrfHash);

        $.post(form.attr('action'), form.serialize(), function (resp) {
            refreshCsrf(resp.csrf_hash);
            if (resp.status === 'success') {
                location.reload();
            } else {
                alert(resp.message || 'An error occurred.');
            }
        }, 'json').fail(function () {
            alert('Request failed. Please refresh and try again.');
        });
    });

    // ── Delete hearing ────────────────────────────────────────────────
    $(document).on('click', '.delete-hearing', function (e) {
        e.preventDefault();
        if (!confirm('Delete this hearing?')) return;

        var id   = $(this).data('id');
        var data = {};
        data[window.blotterConfig.csrfToken] = window.blotterConfig.csrfHash;

        $.ajax({
            url:  window.blotterConfig.hearingDeleteUrl + '/' + id,
            type: 'POST',
            data: data,
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

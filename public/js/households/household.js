
function showToast(type, message) {
    if (type === 'success') {
        toastr.success(message, 'Success');
    } else {
        toastr.error(message, 'Error');
    }
}

$(function () {

    const BASE = "<?php echo base_url(); ?>";
    let deleteId = null;

    // ── helpers ────────────────────────────────────────────────────────────

    function showAlert(type, msg) {
        $('#alertBox').html(`
            <div class="alert alert-${type} alert-dismissible fade show">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                ${msg}
            </div>`);
        $('html, body').animate({ scrollTop: 0 }, 300);
    }

    function showErrors(containerId, errors) {
        let html = '<div class="alert alert-danger alert-dismissible fade show"><button type="button" class="close" data-dismiss="alert">&times;</button><ul class="mb-0">';
        $.each(errors, function (_k, v) { html += `<li>${v}</li>`; });
        html += '</ul></div>';
        $(containerId).html(html);
    }

    function csrfHeaders() {
        return {
            'X-Requested-With': 'XMLHttpRequest'
        };
    }

    // ── DataTable ──────────────────────────────────────────────────────────

    const table = $('#householdsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: BASE + 'households/list',
            type: 'POST',
            headers: csrfHeaders(),
            data: function (d) {
                d['<?php echo csrf_token(); ?>'] = '<?php echo csrf_hash(); ?>';
            }
        },
        columns: [
            { data: 'id', render: (_d, _t, _r, meta) => meta.row + 1 },
            { data: 'household_no', render: h => `<span class="badge badge-primary">${h}</span>` },
            { data: 'street_address', render: a => a || '—' },
            { data: 'sitio', render: s => s || '—' },
            { data: 'house_type', render: t => t ? t.charAt(0).toUpperCase() + t.slice(1) : '—' },
            { data: 'head_name', render: h => h || '<span class="text-muted">Not assigned</span>' },
            { data: 'id', orderable: false, render: id => `
                <button class="btn btn-xs btn-info btn-view mr-1" data-id="${id}" title="View">
                    <i class="fas fa-eye"></i>
                </button>
                <button class="btn btn-xs btn-warning btn-edit mr-1" data-id="${id}" title="Edit">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-xs btn-danger btn-delete" data-id="${id}" title="Delete">
                    <i class="fas fa-trash"></i>
                </button>` }
        ],
        order: [[0, 'desc']],
        pageLength: 10,
        language: {
            processing: '<i class="fas fa-spinner fa-spin mr-1"></i> Loading...',
            emptyTable: 'No households found.'
        }
    });

    // ── Load Residents into select ────────────────────────────────────────

    function loadResidents(selectId, selectedId = '') {
        $.get(BASE + 'households/residentsOptions', function (res) {
            if (res.status !== 'success') return;
            let opts = '<option value="">-- Select Head Resident --</option>';
            res.data.forEach(function (r) {
                const sel = selectedId == r.id ? 'selected' : '';
                opts += `<option value="${r.id}" ${sel}>${r.first_name} ${r.last_name}</option>`;
            });
            $(selectId).html(opts);
        });
    }

    // ── ADD ────────────────────────────────────────────────────────────────

    $('#btnAddHousehold').on('click', function () {
        $('#addHouseholdForm')[0].reset();
        $('#addErrors').html('');
        loadResidents('#add_head_resident_id');
        $('#addHouseholdModal').modal('show');
    });

    $('#addHouseholdForm').on('submit', function (e) {
        e.preventDefault();
        const btn = $('#btnSaveHousehold');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Saving...');

        $.ajax({
            url: BASE + 'households/store',
            method: 'POST',
            data: $(this).serialize(),
            headers: csrfHeaders(),
            success: function (res) {
                if (res.status === 'success') {
                    $('#addHouseholdModal').modal('hide');
                    showAlert('success', '<i class="fas fa-check-circle mr-1"></i> ' + res.message);
                    table.ajax.reload(null, false);
                } else {
                    showErrors('#addErrors', res.errors ?? { msg: res.message });
                }
            },
            error: function () { showErrors('#addErrors', { msg: 'Server error. Please try again.' }); },
            complete: function () { btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Save Household'); }
        });
    });

    // ── VIEW ───────────────────────────────────────────────────────────────

    $(document).on('click', '.btn-view', function () {
        const id = $(this).data('id');
        $('#viewHouseholdBody').html('<div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x text-muted"></i></div>');
        $('#viewHouseholdModal').modal('show');

        $.get(BASE + 'households/show/' + id, function (res) {
            if (res.status !== 'success') {
                $('#viewHouseholdBody').html('<p class="text-center text-danger">Household not found.</p>');
                return;
            }
            const h = res.data;
            $('#viewHouseholdBody').html(`
                <table class="table table-sm table-borderless">
                    <tr><th width="40%">Household No.</th><td>${h.household_no ?? '—'}</td></tr>
                    <tr><th>Street Address</th><td>${h.street_address ?? '—'}</td></tr>
                    <tr><th>Sitio</th><td>${h.sitio ?? '—'}</td></tr>
                    <tr><th>House Type</th><td>${h.house_type ? h.house_type.charAt(0).toUpperCase() + h.house_type.slice(1) : '—'}</td></tr>
                    <tr><th>Head Resident</th><td>${h.head_name ?? 'Not assigned'}</td></tr>
                    <tr><th>Created</th><td>${h.created_at ? new Date(h.created_at).toLocaleDateString('en-PH') : '—'}</td></tr>
                </table>`);
        });
    });

    // ── EDIT ───────────────────────────────────────────────────────────────

    $(document).on('click', '.btn-edit', function () {
        const id = $(this).data('id');
        $('#editErrors').html('');
        loadResidents('#edit_head_resident_id');
        $('#editHouseholdModal').modal('show');

        $.get(BASE + 'households/show/' + id, function (res) {
            if (res.status !== 'success') {
                alert('Failed to load household data.');
                return;
            }
            const h = res.data;
            $('#edit_id').val(h.id);
            $('#edit_household_no').val(h.household_no);
            $('#edit_sitio').val(h.sitio);
            $('#edit_street_address').val(h.street_address);
            $('#edit_house_type').val(h.house_type);
            // set head resident after load
            setTimeout(() => $('#edit_head_resident_id').val(h.head_resident_id), 400);
        });
    });

    $('#editHouseholdForm').on('submit', function (e) {
        e.preventDefault();
        const id  = $('#edit_id').val();
        const btn = $('#btnUpdateHousehold');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Updating...');

        $.ajax({
            url: BASE + 'households/update/' + id,
            method: 'POST',
            data: $(this).serialize(),
            headers: csrfHeaders(),
            success: function (res) {
                if (res.status === 'success') {
                    $('#editHouseholdModal').modal('hide');
                    showAlert('success', '<i class="fas fa-check-circle mr-1"></i> ' + res.message);
                    table.ajax.reload(null, false);
                } else {
                    showErrors('#editErrors', res.errors ?? { msg: res.message });
                }
            },
            error: function () { showErrors('#editErrors', { msg: 'Server error. Please try again.' }); },
            complete: function () { btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Update Changes'); }
        });
    });

    // ── DELETE ─────────────────────────────────────────────────────────────

    $(document).on('click', '.btn-delete', function () {
        deleteId = $(this).data('id');
        const row = table.row($(this).closest('tr')).data();
        const name = row ? row.household_no : 'this household';
        $('#deleteHouseholdName').text(name);
        $('#deleteHouseholdModal').modal('show');
    });

    $('#btnConfirmDelete').on('click', function () {
        if (!deleteId) return;
        $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i>');

        $.ajax({
            url: BASE + 'households/delete/' + deleteId,
            method: 'POST',
            data: { '<?php echo csrf_token(); ?>': '<?php echo csrf_hash(); ?>' },
            headers: csrfHeaders(),
            success: function (res) {
                $('#deleteHouseholdModal').modal('hide');
                if (res.status === 'success') {
                    showAlert('success', '<i class="fas fa-check-circle mr-1"></i> ' + res.message);
                    table.ajax.reload(null, false);
                } else {
                    showAlert('danger', res.message);
                }
            },
            error: function () { showAlert('danger', 'Server error. Please try again.'); },
            complete: function () {
                $('#btnConfirmDelete').prop('disabled', false).html('<i class="fas fa-trash mr-1"></i> Delete');
                deleteId = null;
            }
        });
    });

});
// ── Helpers ────────────────────────────────────────────────────────────────

function showToast(type, message) {
    if (type === 'success') {
        toastr.success(message, 'Success');
    } else {
        toastr.error(message, 'Error');
    }
}

function showErrors(containerId, errors) {
    let html = '<div class="alert alert-danger alert-dismissible fade show"><button type="button" class="close" data-dismiss="alert">&times;</button><ul class="mb-0">';
    $.each(errors, function (k, v) { html += `<li>${v}</li>`; });
    html += '</ul></div>';
    $(containerId).html(html);
}

function csrfHeaders() {
    return { 'X-Requested-With': 'XMLHttpRequest' };
}

function csrfData() {
    const name  = $('meta[name="csrf-name"]').attr('content');
    const token = $('meta[name="csrf-token"]').attr('content');
    return { [name]: token };
}

function refreshCsrf(res) {
    if (res && res.csrf_hash) {
        $('meta[name="csrf-token"]').attr('content', res.csrf_hash);
    }
}

// ── Load Households ────────────────────────────────────────────────────────

function loadHouseholds(selectId, selectedId = '') {
    $.get(APP.baseUrl + 'staff/resident/households', function (res) {
        if (res.status !== 'success') return;
        let opts = '<option value="">-- Select Household --</option>';
        res.data.forEach(function (h) {
            const sel = String(selectedId) === String(h.id) ? 'selected' : '';
            opts += `<option value="${h.id}" ${sel}>${h.household_no}${h.address ? ' — ' + h.address : ''}</option>`;
        });
        $(selectId).html(opts);
    });
}

// ── DataTable ──────────────────────────────────────────────────────────────

$(document).ready(function () {

    const table = $('#residentsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: APP.baseUrl + 'staff/resident/list',
            type: 'POST',
            headers: csrfHeaders(),
            data: function (d) {
                Object.assign(d, csrfData());
            }
        },
        columns: [
            {
                data: 'id',
                render: function (d, t, r, meta) {
                    const info = this.api().page.info();
                    return (info.page * info.length) + meta.row + 1;
                }
            },
            {
                data: null,
                render: r => `${r.last_name}, ${r.first_name}${r.middle_name ? ' ' + r.middle_name.charAt(0) + '.' : ''}`
            },
            {
                data: 'sex',
                render: s => s === 'male'
                    ? '<span class="badge badge-primary">Male</span>'
                    : '<span class="badge badge-danger">Female</span>'
            },
            {
                data: 'birthdate',
                render: d => d ? new Date(d).toLocaleDateString('en-PH', { year: 'numeric', month: 'short', day: 'numeric' }) : '—'
            },
            {
                data: 'civil_status',
                render: s => s ? s.charAt(0).toUpperCase() + s.slice(1) : '—'
            },
            {
                data: 'household_no',
                render: h => h ? `<span class="badge badge-secondary">${h}</span>` : '<span class="text-muted">None</span>'
            },
            {
                data: null,
                render: r => {
                    let tags = '';
                    if (r.is_voter == 1)          tags += '<span class="badge badge-success mr-1">Voter</span>';
                    if (r.is_pwd == 1)            tags += '<span class="badge badge-warning mr-1">PWD</span>';
                    if (r.is_senior_citizen == 1) tags += '<span class="badge badge-info mr-1">Senior</span>';
                    return tags || '<span class="text-muted">—</span>';
                }
            },
            {
                data: 'id',
                orderable: false,
                searchable: false,
                render: id => `
                    <button class="btn btn-xs btn-info btn-view mr-1" data-id="${id}" title="View">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-xs btn-warning btn-edit mr-1" data-id="${id}" title="Edit">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-xs btn-danger btn-delete" data-id="${id}" title="Delete">
                        <i class="fas fa-trash"></i>
                    </button>`
            }
        ],
        order: [[0, 'desc']],
        pageLength: 10,
        responsive: true,
        autoWidth: false,
        language: {
            processing: '<i class="fas fa-spinner fa-spin mr-1"></i> Loading...',
            emptyTable: 'No residents found.'
        }
    });

    // ── ADD ────────────────────────────────────────────────────────────────

    $('#btnAddResident').on('click', function () {
        $('#addResidentForm')[0].reset();
        $('#addErrors').html('');
        loadHouseholds('#add_household_id');
        $('#addResidentModal').modal('show');
    });

    $('#addResidentForm').on('submit', function (e) {
        e.preventDefault();
        const btn = $('#btnSaveResident');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Saving...');

        $.ajax({
            url: APP.baseUrl + 'staff/resident/store',
            method: 'POST',
            data: $(this).serialize() + '&' + $.param(csrfData()),
            headers: csrfHeaders(),
            success: function (res) {
                refreshCsrf(res);
                if (res.status === 'success') {
                    $('#addResidentModal').modal('hide');
                    $('#addResidentForm')[0].reset();
                    showToast('success', res.message);
                    table.ajax.reload(null, false);
                } else {
                    showErrors('#addErrors', res.errors ?? { msg: res.message });
                }
            },
            error: function () {
                showErrors('#addErrors', { msg: 'Server error. Please try again.' });
            },
            complete: function () {
                btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Save Resident');
            }
        });
    });

    // ── VIEW ───────────────────────────────────────────────────────────────

    $(document).on('click', '.btn-view', function () {
        const id = $(this).data('id');
        $('#viewResidentBody').html('<div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x text-muted"></i></div>');
        $('#viewResidentModal').modal('show');

        $.get(APP.baseUrl + 'staff/resident/show/' + id, function (res) {
            refreshCsrf(res);
            if (res.status !== 'success') {
                $('#viewResidentBody').html('<p class="text-center text-danger">Resident not found.</p>');
                return;
            }
            const r = res.data;
            const age = r.birthdate
                ? Math.floor((new Date() - new Date(r.birthdate)) / (365.25 * 24 * 3600 * 1000))
                : '—';
            const cats = [
                r.is_voter == 1          ? '<span class="badge badge-success">Voter</span>'  : '',
                r.is_pwd == 1            ? '<span class="badge badge-warning">PWD</span>'    : '',
                r.is_senior_citizen == 1 ? '<span class="badge badge-info">Senior</span>'   : '',
            ].filter(Boolean).join(' ') || '—';

            $('#viewResidentBody').html(`
                <table class="table table-sm table-borderless">
                    <tr><th width="40%">Full Name</th><td>${r.last_name}, ${r.first_name} ${r.middle_name ?? ''}</td></tr>
                    <tr><th>Birthdate</th><td>${r.birthdate ?? '—'} <em class="text-muted">(${age} yrs)</em></td></tr>
                    <tr><th>Sex</th><td>${r.sex ? r.sex.charAt(0).toUpperCase() + r.sex.slice(1) : '—'}</td></tr>
                    <tr><th>Civil Status</th><td>${r.civil_status ? r.civil_status.charAt(0).toUpperCase() + r.civil_status.slice(1) : '—'}</td></tr>
                    <tr><th>Contact</th><td>${r.contact_number ?? '—'}</td></tr>
                    <tr><th>Occupation</th><td>${r.occupation ?? '—'}</td></tr>
                    <tr><th>Household</th><td>${r.household_no ?? '—'}</td></tr>
                    <tr><th>Relationship</th><td>${r.relationship_to_head ?? '—'}</td></tr>
                    <tr><th>Categories</th><td>${cats}</td></tr>
                    <tr><th>Status</th><td><span class="badge badge-${r.status === 'active' ? 'success' : 'secondary'}">${r.status ?? '—'}</span></td></tr>
                </table>`);
        });
    });

    // ── EDIT ───────────────────────────────────────────────────────────────

    $(document).on('click', '.btn-edit', function () {
        const id = $(this).data('id');
        $('#editErrors').html('');
        $('#editResidentModal').modal('show');
        $('#editResidentBody').html('');

        $.get(APP.baseUrl + 'staff/resident/show/' + id, function (res) {
            refreshCsrf(res);
            if (res.status !== 'success') {
                showToast('error', 'Failed to load resident data.');
                $('#editResidentModal').modal('hide');
                return;
            }
            const r = res.data;
            $('#edit_id').val(r.id);
            $('#edit_first_name').val(r.first_name);
            $('#edit_middle_name').val(r.middle_name ?? '');
            $('#edit_last_name').val(r.last_name);
            $('#edit_birthdate').val(r.birthdate);
            $('#edit_sex').val(r.sex);
            $('#edit_civil_status').val(r.civil_status);
            $('#edit_contact_number').val(r.contact_number ?? '');
            $('#edit_relationship_to_head').val(r.relationship_to_head ?? '');
            $('#edit_occupation').val(r.occupation ?? '');
            $('#edit_is_voter').prop('checked', r.is_voter == 1);
            $('#edit_is_pwd').prop('checked', r.is_pwd == 1);
            $('#edit_is_senior').prop('checked', r.is_senior_citizen == 1);

            // Load households with selected value — no setTimeout needed
            loadHouseholds('#edit_household_id', r.household_id);
        });
    });

    $('#editResidentForm').on('submit', function (e) {
        e.preventDefault();
        const id  = $('#edit_id').val();
        const btn = $('#btnUpdateResident');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Updating...');

        $.ajax({
            url: APP.baseUrl + 'staff/resident/update/' + id,
            method: 'POST',
            data: $(this).serialize() + '&' + $.param(csrfData()),
            headers: csrfHeaders(),
            success: function (res) {
                refreshCsrf(res);
                if (res.status === 'success') {
                    $('#editResidentModal').modal('hide');
                    showToast('success', res.message);
                    table.ajax.reload(null, false);
                } else {
                    showErrors('#editErrors', res.errors ?? { msg: res.message });
                }
            },
            error: function () {
                showErrors('#editErrors', { msg: 'Server error. Please try again.' });
            },
            complete: function () {
                btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Update Changes');
            }
        });
    });

    // ── DELETE ─────────────────────────────────────────────────────────────

    let deleteId = null;

    $(document).on('click', '.btn-delete', function () {
        deleteId = $(this).data('id');
        const row = table.row($(this).closest('tr')).data();
        const name = row ? `${row.first_name} ${row.last_name}` : 'this resident';
        $('#deleteResidentName').text(name);
        $('#deleteResidentModal').modal('show');
    });

    $('#btnConfirmDelete').on('click', function () {
        if (!deleteId) return;
        const btn = $(this);
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i>');

        $.ajax({
            url: APP.baseUrl + 'staff/resident/delete/' + deleteId,
            method: 'POST',
            data: csrfData(),
            headers: csrfHeaders(),
            success: function (res) {
                refreshCsrf(res);
                $('#deleteResidentModal').modal('hide');
                if (res.status === 'success') {
                    showToast('success', res.message);
                    table.ajax.reload(null, false);
                } else {
                    showToast('error', res.message);
                }
            },
            error: function () {
                showToast('error', 'Server error. Please try again.');
            },
            complete: function () {
                btn.prop('disabled', false).html('<i class="fas fa-trash mr-1"></i> Delete');
                deleteId = null;
            }
        });
    });

}); // end document.ready
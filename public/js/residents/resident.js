$(document).ready(function () {

    // ── CSRF helpers ─────────────────────────────────────────────────
    function getCsrfName()  { return $('meta[name="csrf-name"]').attr('content'); }
    function getCsrfToken() { return $('meta[name="csrf-token"]').attr('content'); }

    function csrfData() {
        const obj = {};
        obj[getCsrfName()] = getCsrfToken();
        return obj;
    }

    function updateCsrf(hash) {
        if (hash) $('meta[name="csrf-token"]').attr('content', hash);
    }

    // ── ALERT helper ─────────────────────────────────────────────────
    function showAlert(type, msg) {
        $('#alertBox').html(`
            <div class="alert alert-${type} alert-dismissible fade show">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                ${msg}
            </div>`);
        setTimeout(() => $('#alertBox .alert').fadeOut(), 4000);
    }

    // ── LOAD HOUSEHOLDS into a <select> ──────────────────────────────
    function loadHouseholds(selectEl, selectedId = null) {
        $(selectEl).html('<option value="">Loading...</option>');

        $.get(BASE + 'staff/residents/households', function (res) {
            let opts = '<option value="">-- Select Household --</option>';
            if (res.status === 'success' && res.data && res.data.length) {
                res.data.forEach(h => {
                    const sel = selectedId && selectedId == h.id ? 'selected' : '';
                    opts += `<option value="${h.id}" ${sel}>${h.household_no} — ${h.address || ''}</option>`;
                });
                if (res.csrf_hash) updateCsrf(res.csrf_hash);
            } else {
                opts += '<option value="" disabled>No households found</option>';
            }
            $(selectEl).html(opts);
        }).fail(function () {
            $(selectEl).html('<option value="">-- Error loading households --</option>');
        });
    }

    // ── DATATABLE ────────────────────────────────────────────────────
    const table = $('#residentsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: BASE + 'staff/residents/list',
            type: 'POST',
            data: d => Object.assign(d, csrfData()),
            dataSrc: function (json) {
                if (json.csrf_hash) updateCsrf(json.csrf_hash);
                return json.data;
            }
        },
        columns: [
            { data: null, render: (d, t, r, meta) => meta.row + 1, orderable: false },
            {
                data: null,
                render: r => `${r.last_name}, ${r.first_name}${r.middle_name ? ' ' + r.middle_name.charAt(0) + '.' : ''}`
            },
            { data: 'birthdate', defaultContent: '—' },
            { data: 'sex',       defaultContent: '—' },
            { data: 'civil_status', defaultContent: '—' },
            { data: 'contact_number', defaultContent: '—' },
            { data: 'household_no',   defaultContent: '—' },
            {
                data: null,
                orderable: false,
                render: r => {
                    let badges = '';
                    if (r.is_voter      == 1) badges += '<span class="badge badge-primary mr-1">Voter</span>';
                    if (r.is_senior_citizen == 1) badges += '<span class="badge badge-success mr-1">Senior</span>';
                    if (r.is_pwd        == 1) badges += '<span class="badge badge-warning mr-1">PWD</span>';
                    return badges || '—';
                }
            },
            {
                data: 'id',
                orderable: false,
                render: id => `
                    <button class="btn btn-sm btn-info    btn-view"   data-id="${id}"><i class="fas fa-eye"></i></button>
                    <button class="btn btn-sm btn-warning btn-edit"   data-id="${id}"><i class="fas fa-edit"></i></button>
                    <button class="btn btn-sm btn-danger  btn-delete" data-id="${id}"><i class="fas fa-trash"></i></button>`
            }
        ]
    });

    // ── ADD MODAL ────────────────────────────────────────────────────
    $('#btnAddResident').on('click', function () {
        $('#addResidentForm')[0].reset();
        $('#addErrors').html('');
        loadHouseholds('#add_household_id');          // ← populates the dropdown
        $('#addResidentModal').modal('show');
    });

    $('#addResidentForm').on('submit', function (e) {
        e.preventDefault();
        $('#addErrors').html('');

        const formData = new FormData(this);
        Object.entries(csrfData()).forEach(([k, v]) => formData.append(k, v));

        $.ajax({
            url: BASE + 'staff/residents/store',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {
                if (res.csrf_hash) updateCsrf(res.csrf_hash);
                if (res.status === 'success') {
                    $('#addResidentModal').modal('hide');
                    table.ajax.reload(null, false);
                    showAlert('success', res.message);
                } else {
                    let html = '<div class="alert alert-danger"><ul>';
                    if (res.errors) Object.values(res.errors).forEach(e => html += `<li>${e}</li>`);
                    else html += `<li>${res.message}</li>`;
                    $('#addErrors').html(html + '</ul></div>');
                }
            }
        });
    });

    // ── EDIT MODAL ───────────────────────────────────────────────────
    $(document).on('click', '.btn-edit', function () {
        const id = $(this).data('id');
        $('#editErrors').html('');

        $.get(BASE + 'staff/residents/show/' + id, function (res) {
            if (res.csrf_hash) updateCsrf(res.csrf_hash);
            if (res.status !== 'success') { showAlert('danger', res.message); return; }

            const r = res.data;
            $('#edit_id').val(r.id);
            $('#edit_first_name').val(r.first_name);
            $('#edit_middle_name').val(r.middle_name);
            $('#edit_last_name').val(r.last_name);
            $('#edit_birthdate').val(r.birthdate);
            $('#edit_sex').val(r.sex);
            $('#edit_civil_status').val(r.civil_status);
            $('#edit_contact_number').val(r.contact_number);
            $('#edit_relationship_to_head').val(r.relationship_to_head);
            $('#edit_is_voter').prop('checked',          r.is_voter == 1);
            $('#edit_is_senior_citizen').prop('checked', r.is_senior_citizen == 1);
            $('#edit_is_pwd').prop('checked',            r.is_pwd == 1);

            // Load households then pre-select current one
            loadHouseholds('#edit_household_id', r.household_id);

            $('#editResidentModal').modal('show');
        });
    });

    $('#editResidentForm').on('submit', function (e) {
        e.preventDefault();
        $('#editErrors').html('');
        const id = $('#edit_id').val();

        const formData = new FormData(this);
        Object.entries(csrfData()).forEach(([k, v]) => formData.append(k, v));

        $.ajax({
            url: BASE + 'staff/residents/update/' + id,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {
                if (res.csrf_hash) updateCsrf(res.csrf_hash);
                if (res.status === 'success') {
                    $('#editResidentModal').modal('hide');
                    table.ajax.reload(null, false);
                    showAlert('success', res.message);
                } else {
                    let html = '<div class="alert alert-danger"><ul>';
                    if (res.errors) Object.values(res.errors).forEach(e => html += `<li>${e}</li>`);
                    else html += `<li>${res.message}</li>`;
                    $('#editErrors').html(html + '</ul></div>');
                }
            }
        });
    });

    // ── VIEW MODAL ───────────────────────────────────────────────────
    $(document).on('click', '.btn-view', function () {
        const id = $(this).data('id');

        $.get(BASE + 'staff/residents/show/' + id, function (res) {
            if (res.csrf_hash) updateCsrf(res.csrf_hash);
            if (res.status !== 'success') { showAlert('danger', res.message); return; }

            const r = res.data;
            const fullName = `${r.last_name}, ${r.first_name} ${r.middle_name || ''}`;

            let badges = '';
            if (r.is_voter == 1)          badges += '<span class="badge badge-primary mr-1">Voter</span>';
            if (r.is_senior_citizen == 1) badges += '<span class="badge badge-success mr-1">Senior Citizen</span>';
            if (r.is_pwd == 1)            badges += '<span class="badge badge-warning mr-1">PWD</span>';

            $('#viewResidentBody').html(`
                <table class="table table-bordered table-sm">
                    <tr><th width="40%">Full Name</th><td>${fullName}</td></tr>
                    <tr><th>Birthdate</th><td>${r.birthdate || '—'}</td></tr>
                    <tr><th>Sex</th><td>${r.sex || '—'}</td></tr>
                    <tr><th>Civil Status</th><td>${r.civil_status || '—'}</td></tr>
                    <tr><th>Contact Number</th><td>${r.contact_number || '—'}</td></tr>
                    <tr><th>Household</th><td>${r.household_no || '—'}</td></tr>
                    <tr><th>Relationship to Head</th><td>${r.relationship_to_head || '—'}</td></tr>
                    <tr><th>Attributes</th><td>${badges || '—'}</td></tr>
                </table>`);

            $('#viewResidentModal').modal('show');
        });
    });

    // ── DELETE MODAL ─────────────────────────────────────────────────
    let deleteId = null;

    $(document).on('click', '.btn-delete', function () {
        deleteId = $(this).data('id');
        const row = table.row($(this).closest('tr')).data();
        const name = row ? `${row.last_name}, ${row.first_name}` : '#' + deleteId;
        $('#deleteResidentName').text(name);
        $('#deleteResidentModal').modal('show');
    });

    $('#btnConfirmDelete').on('click', function () {
        if (!deleteId) return;

        const payload = Object.assign({ _method: 'DELETE' }, csrfData());

        $.post(BASE + 'staff/residents/delete/' + deleteId, payload, function (res) {
            if (res.csrf_hash) updateCsrf(res.csrf_hash);
            $('#deleteResidentModal').modal('hide');
            if (res.status === 'success') {
                table.ajax.reload(null, false);
                showAlert('success', res.message);
            } else {
                showAlert('danger', res.message);
            }
        });
    });

});
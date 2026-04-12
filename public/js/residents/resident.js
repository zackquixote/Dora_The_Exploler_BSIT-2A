$(document).ready(function () {

    var BASE   = APP.baseUrl;
    var CSRF_N = APP.csrfName;
    var CSRF_H = APP.csrfHash;

    // ── helpers ──────────────────────────────────────────────────────────────
    function toast(type, msg) {
        type === 'success' ? toastr.success(msg) : toastr.error(msg);
    }

    function loadHouseholds(selector, selectedId) {
        $.get(BASE + 'staff/household/list-all', function (data) {
            var $s = $(selector).find('option:not(:first)').remove().end();
            if (data && data.length) {
                $.each(data, function (i, h) {
                    var opt = $('<option>', { value: h.id, text: 'HH #' + h.household_no });
                    if (selectedId && h.id == selectedId) opt.prop('selected', true);
                    $s.append(opt);
                });
            }
        });
    }

    // ── DataTable ─────────────────────────────────────────────────────────────
    var table = $('#residentsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url:  BASE + 'staff/resident/list',
            type: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            data: function (d) { d[CSRF_N] = CSRF_H; },
            error: function (xhr) { console.error('DataTable error:', xhr.responseText); }
        },
        columns: [
            { data: null, render: function (d, t, r, m) { return m.row + 1; } },
            { data: null, render: function (r) { return r.last_name + ', ' + r.first_name + ' ' + (r.middle_name || ''); } },
            { data: 'sex' },
            { data: 'birthdate' },
            { data: 'civil_status' },
            { data: 'household_no', defaultContent: '—' },
            { data: null, render: function (r) {
                var t = '';
                if (r.is_voter == 1)          t += '<span class="badge badge-primary mr-1">Voter</span>';
                if (r.is_pwd == 1)            t += '<span class="badge badge-warning mr-1">PWD</span>';
                if (r.is_senior_citizen == 1) t += '<span class="badge badge-success mr-1">Senior</span>';
                return t || '—';
            }},
            { data: 'id', orderable: false, searchable: false, render: function (id) {
                return '<button class="btn btn-sm btn-info btn-view mr-1" data-id="' + id + '"><i class="fas fa-eye"></i></button>' +
                       '<button class="btn btn-sm btn-warning btn-edit mr-1" data-id="' + id + '"><i class="fas fa-edit"></i></button>' +
                       '<button class="btn btn-sm btn-danger btn-delete" data-id="' + id + '"><i class="fas fa-trash-alt"></i></button>';
            }}
        ],
        responsive: true,
        autoWidth: false
    });

    // ── ADD ───────────────────────────────────────────────────────────────────
    $('#btnAddResident').on('click', function () {
        $('#addResidentForm')[0].reset();
        $('#addErrors').html('');
        loadHouseholds('#add_household_id', null);
        $('#addResidentModal').modal('show');
    });

    $('#addResidentForm').on('submit', function (e) {
        e.preventDefault();
        var $btn = $('#btnSaveResident').prop('disabled', true).text('Saving...');
        $.ajax({
            url: BASE + 'staff/resident/store',
            method: 'POST',
            data: $(this).serialize() + '&' + CSRF_N + '=' + CSRF_H,
            dataType: 'json',
            success: function (res) {
                if (res.status === 'success') {
                    $('#addResidentModal').modal('hide');
                    $('#addResidentForm')[0].reset();
                    toast('success', 'Resident added successfully!');
                    setTimeout(function () { table.ajax.reload(); }, 800);
                } else {
                    var html = '<div class="alert alert-danger"><ul class="mb-0">';
                    $.each(res.errors || {}, function (k, v) { html += '<li>' + v + '</li>'; });
                    html += '</ul></div>';
                    $('#addErrors').html(html);
                }
            },
            error: function () { toast('error', 'Server error while saving.'); },
            complete: function () { $btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Save Resident'); }
        });
    });

    // ── VIEW ──────────────────────────────────────────────────────────────────
    $(document).on('click', '.btn-view', function () {
        var id = $(this).data('id');
        $('#viewResidentBody').html('<div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x text-muted"></i></div>');
        $('#viewResidentModal').modal('show');
        $.ajax({
            url: BASE + 'staff/resident/show/' + id,
            method: 'GET',
            dataType: 'json',
            success: function (res) {
                if (res.status === 'success') {
                    var d = res.data;
                    var cats = '';
                    if (d.is_voter == 1)          cats += '<span class="badge badge-primary mr-1">Voter</span>';
                    if (d.is_pwd == 1)            cats += '<span class="badge badge-warning mr-1">PWD</span>';
                    if (d.is_senior_citizen == 1) cats += '<span class="badge badge-success mr-1">Senior</span>';
                    $('#viewResidentBody').html(
                        '<table class="table table-sm table-bordered mb-0">' +
                        '<tr><th width="35%">Full Name</th><td>' + d.last_name + ', ' + d.first_name + ' ' + (d.middle_name || '') + '</td></tr>' +
                        '<tr><th>Sex</th><td>' + d.sex + '</td></tr>' +
                        '<tr><th>Birthdate</th><td>' + d.birthdate + '</td></tr>' +
                        '<tr><th>Civil Status</th><td>' + d.civil_status + '</td></tr>' +
                        '<tr><th>Contact</th><td>' + (d.contact_number || '—') + '</td></tr>' +
                        '<tr><th>Occupation</th><td>' + (d.occupation || '—') + '</td></tr>' +
                        '<tr><th>Household</th><td>' + (d.household_no || '—') + '</td></tr>' +
                        '<tr><th>Relationship</th><td>' + (d.relationship_to_head || '—') + '</td></tr>' +
                        '<tr><th>Categories</th><td>' + (cats || '—') + '</td></tr>' +
                        '</table>'
                    );
                } else {
                    $('#viewResidentBody').html('<p class="text-danger">Could not load resident.</p>');
                }
            },
            error: function () {
                $('#viewResidentBody').html('<p class="text-danger">Server error.</p>');
            }
        });
    });

    // ── EDIT ──────────────────────────────────────────────────────────────────
    $(document).on('click', '.btn-edit', function () {
        var id = $(this).data('id');
        $('#editErrors').html('');
        $.ajax({
            url: BASE + 'staff/resident/show/' + id,
            method: 'GET',
            dataType: 'json',
            success: function (res) {
                if (res.status === 'success') {
                    var d = res.data;
                    $('#edit_id').val(d.id);
                    $('#edit_first_name').val(d.first_name);
                    $('#edit_middle_name').val(d.middle_name || '');
                    $('#edit_last_name').val(d.last_name);
                    $('#edit_birthdate').val(d.birthdate);
                    $('#edit_sex').val(d.sex);
                    $('#edit_civil_status').val(d.civil_status);
                    $('#edit_contact_number').val(d.contact_number || '');
                    $('#edit_occupation').val(d.occupation || '');
                    $('#edit_relationship_to_head').val(d.relationship_to_head || '');
                    $('#edit_is_voter').prop('checked', d.is_voter == 1);
                    $('#edit_is_senior').prop('checked', d.is_senior_citizen == 1);
                    $('#edit_is_pwd').prop('checked', d.is_pwd == 1);
                    loadHouseholds('#edit_household_id', d.household_id);
                    $('#editResidentModal').modal('show');
                } else {
                    toast('error', 'Could not load resident data.');
                }
            },
            error: function () { toast('error', 'Server error.'); }
        });
    });

    $('#editResidentForm').on('submit', function (e) {
        e.preventDefault();
        var id = $('#edit_id').val();
        var $btn = $('#btnUpdateResident').prop('disabled', true).text('Updating...');
        $.ajax({
            url: BASE + 'staff/resident/update/' + id,
            method: 'POST',
            data: $(this).serialize() + '&' + CSRF_N + '=' + CSRF_H,
            dataType: 'json',
            success: function (res) {
                if (res.status === 'success') {
                    $('#editResidentModal').modal('hide');
                    toast('success', 'Resident updated successfully!');
                    setTimeout(function () { table.ajax.reload(); }, 800);
                } else {
                    var html = '<div class="alert alert-danger"><ul class="mb-0">';
                    $.each(res.errors || {}, function (k, v) { html += '<li>' + v + '</li>'; });
                    html += '</ul></div>';
                    $('#editErrors').html(html);
                }
            },
            error: function () { toast('error', 'Server error while updating.'); },
            complete: function () { $btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Update Changes'); }
        });
    });

    // ── DELETE ────────────────────────────────────────────────────────────────
    var deleteId = null;

    $(document).on('click', '.btn-delete', function () {
        deleteId = $(this).data('id');
        var name = $(this).closest('tr').find('td:nth-child(2)').text();
        $('#deleteResidentName').text(name);
        $('#deleteResidentModal').modal('show');
    });

    $('#btnConfirmDelete').on('click', function () {
        if (!deleteId) return;
        var $btn = $(this).prop('disabled', true).text('Deleting...');
        $.ajax({
            url: BASE + 'staff/resident/delete/' + deleteId,
            method: 'POST',
            data: { _method: 'DELETE', [CSRF_N]: CSRF_H },
            dataType: 'json',
            success: function (res) {
                $('#deleteResidentModal').modal('hide');
                if (res.status === 'success') {
                    toast('success', 'Resident deleted.');
                    setTimeout(function () { table.ajax.reload(); }, 800);
                } else {
                    toast('error', res.message || 'Failed to delete.');
                }
            },
            error: function () { toast('error', 'Server error.'); },
            complete: function () { $btn.prop('disabled', false).html('<i class="fas fa-trash mr-1"></i> Delete'); }
        });
    });

});
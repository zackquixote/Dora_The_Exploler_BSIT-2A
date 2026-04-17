$(document).ready(function () {

    var table = $('#residentsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url:  BASE_URL + 'staff/residents/list',
            type: 'POST',
            data: function (d) {
                d[CSRF_NAME] = CSRF_HASH;
            },
            dataSrc: function (json) {
                if (json.csrf_hash) CSRF_HASH = json.csrf_hash;
                return json.data;
            },
            error: function (xhr) {
                console.error('AJAX error:', xhr.status, xhr.responseText);
            }
        },
        columns: [
            { data: 'id' },
            {
                data: null,
                render: function (row) {
                    var name = [row.first_name, row.middle_name, row.last_name]
                        .filter(function(v){ return v && v.trim(); }).join(' ');
                    var pic = row.profile_picture
                        ? '<img src="' + BASE_URL + 'uploads/' + row.profile_picture + '" class="rounded-circle mr-2" style="width:30px;height:30px;object-fit:cover;vertical-align:middle;">'
                        : '<i class="fas fa-user-circle mr-1 text-secondary"></i>';
                    return pic + name;
                }
            },
            {
                data: 'sex',
                render: function (val) {
                    if (!val) return '—';
                    return val.charAt(0).toUpperCase() + val.slice(1);
                }
            },
            { data: 'birthdate', defaultContent: '—' },
            { data: 'civil_status', defaultContent: '—' },
            {
                data: 'household_no',
                defaultContent: '—',
                render: function (val) { return val ? '#' + val : '—'; }
            },
            { data: 'occupation',  defaultContent: '—' },
            { data: 'citizenship', defaultContent: '—' },
            {
                data: null,
                orderable: false,
                render: function (row) {
                    var tags = [];
                    if (parseInt(row.is_voter))          tags.push('<span class="badge badge-primary">Voter</span>');
                    if (parseInt(row.is_senior_citizen)) tags.push('<span class="badge badge-warning">Senior</span>');
                    if (parseInt(row.is_pwd))            tags.push('<span class="badge badge-info">PWD</span>');
                    return tags.length ? tags.join(' ') : '<small class="text-muted">—</small>';
                }
            },
            {
                data: 'id',
                orderable: false,
                render: function (id) {
                    return '<div class="btn-group btn-group-sm">'
                        + '<a href="' + BASE_URL + 'staff/residents/view/' + id + '" class="btn btn-info btn-xs"><i class="fas fa-eye"></i></a>'
                        + '<a href="' + BASE_URL + 'staff/residents/edit/' + id + '" class="btn btn-warning btn-xs"><i class="fas fa-edit"></i></a>'
                        + '<button class="btn btn-danger btn-xs btn-delete" data-id="' + id + '"><i class="fas fa-trash"></i></button>'
                        + '</div>';
                }
            }
        ],
        order: [[0, 'desc']],
        pageLength: 10,
        lengthMenu: [10, 25, 50],
        language: {
            processing:  '<i class="fas fa-spinner fa-spin mr-1"></i> Loading...',
            emptyTable:  'No residents found.',
            zeroRecords: 'No residents match your search.'
        }
    });

    $('#residentsTable').on('click', '.btn-delete', function () {
        var id = $(this).data('id');
        if (!confirm('Delete this resident? This cannot be undone.')) return;

        $.ajax({
            url:  BASE_URL + 'staff/residents/delete/' + id,
            type: 'POST',
            data: { [CSRF_NAME]: CSRF_HASH },
            dataType: 'json',
            success: function (res) {
                if (res.csrf_hash) CSRF_HASH = res.csrf_hash;
                if (res.status === 'success') {
                    table.ajax.reload(null, false);
                } else {
                    alert(res.message || 'Delete failed.');
                }
            },
            error: function (xhr) {
                console.error('Delete error:', xhr.responseText);
            }
        });
    });

});
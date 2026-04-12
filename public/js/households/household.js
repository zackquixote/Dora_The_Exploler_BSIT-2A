<script>
$(document).ready(function () {

    if (!$.fn.DataTable) {
        console.error("DataTables not loaded");
        return;
    }

    // ✅ Define loadHouseholds before using it
    function loadHouseholds(selector) {
        $.get(APP.baseUrl + 'staff/household/list-all', function (data) {
            var $sel = $(selector);
            $sel.find('option:not(:first)').remove();
            if (data && data.length) {
                $.each(data, function (i, h) {
                    $sel.append('<option value="' + h.id + '">HH #' + h.household_no + '</option>');
                });
            }
        }).fail(function () {
            console.warn('Could not load households.');
        });
    }

    $('#btnAddResident').on('click', function () {
        $('#addResidentForm')[0].reset();
        $('#addErrors').html('');
        loadHouseholds('#add_household_id');
        $('#addResidentModal').modal('show');
    });

    const table = $('#residentsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: APP.baseUrl + 'staff/resident/list',
            type: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            data: function (d) { d[APP.csrfName] = APP.csrfHash; },
            error: function (xhr) {
                console.log("AJAX ERROR:", xhr.responseText);
                alert("Failed to load residents. Check console.");
            }
        },
        columns: [
            { data: null },
            { data: null, render: r => `${r.last_name}, ${r.first_name}` },
            { data: 'sex' },
            { data: 'birthdate' },
            { data: 'civil_status' },
            { data: 'household_no' },
            {
                data: null,
                render: r => {
                    let t = '';
                    if (r.is_voter == 1) t += 'Voter ';
                    if (r.is_pwd == 1) t += 'PWD ';
                    if (r.is_senior_citizen == 1) t += 'Senior ';
                    return t || '-';
                }
            },
            {
                data: 'id',
                render: id => `
                    <button class="btn btn-sm btn-info btn-view" data-id="${id}">View</button>
                    <button class="btn btn-sm btn-warning btn-edit" data-id="${id}">Edit</button>
                    <button class="btn btn-sm btn-danger btn-delete" data-id="${id}">Delete</button>
                `
            }
        ]
    });

});
</script>
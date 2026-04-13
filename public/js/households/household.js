$(document).ready(function () {

   
    function loadResidents() {
        $.get(BASE + 'households/residentsOptions', function (res) {
            let options = '<option value="">-- Select Head Resident --</option>';

            res.data.forEach(r => {
                options += `<option value="${r.id}">
                    ${r.last_name}, ${r.first_name}
                </option>`;
            });

            $('#add_head_resident_id, #edit_head_resident_id').html(options);
        });
    }

  
    let table = $('#householdsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: BASE + 'households/list',
            type: 'POST'
        },
        columns: [
            {
                data: null,
                render: function (data, type, row, meta) {
                    return meta.row + 1;
                }
            },
            { data: 'household_no' },
            { data: 'street_address' },
            { data: 'sitio' },
            { data: 'house_type' },
            { data: 'head_name' },
            {
                data: null,
                render: function () {
                    return `
                        <button class="btn btn-sm btn-info">View</button>
                        <button class="btn btn-sm btn-warning">Edit</button>
                        <button class="btn btn-sm btn-danger">Delete</button>
                    `;
                }
            }
        ]
    });

   
    $('#btnAddHousehold').on('click', function () {
        loadResidents();
        $('#addHouseholdModal').modal('show');
    });

});
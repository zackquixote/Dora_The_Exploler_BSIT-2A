let table = $('#householdsTable').DataTable({
    processing: true,
    serverSide: false,
    ajax: {
        url: BASE + 'households/list',
        dataSrc: 'data'
    },
    columns: [
        {
            data: null,
            render: (data, type, row, meta) =>
                meta.row + meta.settings._iDisplayStart + 1
        },
        { data: 'household_no' },
        { data: 'street_address' },
        { data: 'sitio' },
        { data: 'house_type' },
        { data: 'head_name' },
        {
            data: null,
            render: function (data) {
                return `
                    <button class="btn btn-info btn-sm btn-view" data-id="${data.id}">View</button>
                    <button class="btn btn-warning btn-sm btn-edit" data-id="${data.id}">Edit</button>
                    <button class="btn btn-danger btn-sm btn-delete" data-id="${data.id}">Delete</button>
                `;
            }
        }
    ]
});
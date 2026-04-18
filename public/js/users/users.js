function showToast(type, message) {
    if (type === 'success') {
        toastr.success(message, 'Success');
    } else {
        toastr.error(message, 'Error');
    }
}

// ===================== ADD USER =====================
$('#addUserForm').on('submit', function (e) {
    e.preventDefault();

    $.ajax({
        url: baseUrl + 'staff/users/save',
        method: 'POST',
        data: $(this).serialize(),
        dataType: 'json',

        success: function (response) {
            if (response.status === 'success') {
                $('#AddNewModal').modal('hide');
                $('#addUserForm')[0].reset();
                showToast('success', 'User added successfully!');
                setTimeout(() => location.reload(), 1000);
            } else {
                showToast('error', response.message || 'Failed to add user.');
            }
        },
        error: function () {
            showToast('error', 'An error occurred.');
        }
    });
});


// ===================== EDIT USER =====================
$(document).on('click', '.edit-btn', function () {
    const userId = $(this).data('id');

    $.ajax({
        url: baseUrl + 'staff/users/edit/' + userId,
        method: 'GET',
        dataType: 'json',

        success: function (response) {
            if (response.data) {
                $('#editUserModal #name').val(response.data.name);
                $('#editUserModal #userId').val(response.data.id);
                $('#editUserModal #email').val(response.data.email);
                $('#editUserModal #password').val('');
                $('#editUserModal #role').val(response.data.role);
                $('#editUserModal #status').val(response.data.status);
                $('#editUserModal #phone').val(response.data.phone);
                $('#editUserModal').modal('show');
            } else {
                alert('Error fetching user data');
            }
        },
        error: function () {
            alert('Error fetching user data');
        }
    });
});


// ===================== UPDATE USER =====================
$('#editUserForm').on('submit', function (e) {
    e.preventDefault();

    $.ajax({
        url: baseUrl + 'staff/users/update',
        method: 'POST',
        data: $(this).serialize(),
        dataType: 'json',

        success: function (response) {
            if (response.success) {
                $('#editUserModal').modal('hide');
                showToast('success', 'User updated successfully!');
                setTimeout(() => location.reload(), 1000);
            } else {
                alert(response.message || 'Error updating');
            }
        },
        error: function (xhr) {
            console.error(xhr.responseText);
            alert('Error updating');
        }
    });
});


// ===================== DELETE USER =====================
$(document).on('click', '.deleteUserBtn', function () {
    const userId = $(this).data('id');

    if (confirm('Are you sure you want to delete this user?')) {
        $.ajax({
            url: baseUrl + 'staff/users/delete/' + userId,
            method: 'POST',
            data: { _method: 'DELETE' },

            success: function (response) {
                if (response.success) {
                    showToast('success', 'User deleted successfully.');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    alert(response.message || 'Failed to delete.');
                }
            },
            error: function () {
                alert('Something went wrong while deleting.');
            }
        });
    }
});


// ===================== DATATABLE =====================
$(document).ready(function () {

    $('#example1').DataTable({
        processing: true,
        serverSide: true,

        ajax: {
            url: baseUrl + 'staff/users/fetchRecords', // ✅ FIXED ROUTE
            type: 'POST'
        },

        columns: [
            {
                data: null,
                render: function (data, type, row, meta) {
                    return meta.row + 1; // ✅ row number
                }
            },
            { data: 'id', visible: false },
            { data: 'name' },
            { data: 'email' },
            { data: 'role' },
            { data: 'status' },
            { data: 'phone' },
            { data: 'created_at' },
            {
                data: null,
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    return `
                        <button class="btn btn-sm btn-warning edit-btn" data-id="${row.id}">
                            <i class="far fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger deleteUserBtn" data-id="${row.id}">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    `;
                }
            }
        ],

        responsive: true,
        autoWidth: false
    });

}); 

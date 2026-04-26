// public/js/users/users.js

 $(document).ready(function() {
    // 1. GLOBAL VARIABLES (Set by PHP in the View)
    // We rely on the View to pass 'baseUrl', 'csrfName', and 'csrfHash'
    
    // 2. DATATABLE INIT
    var table = $('#users-table').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": window.baseUrl + "/fetchRecords", // Use window.baseUrl
            "type": "POST",
            "data": function(d) {
                // Add CSRF Token dynamically
                d[window.csrfName] = window.csrfHash;
            },
            "error": function(xhr, error, thrown) {
                console.log("Ajax error:", error);
                console.log("Response:", xhr.responseText);
                // Optional: Show a toast error here
            }
        },
        "columns": [
            { "data": "no" },         // No.
            { "data": "name" },       // Name
            { "data": "email" },      // Email
            { "data": "role" },       // Role
            { "data": "status" },     // Status
            { 
                "data": "actions",    // Actions
                "orderable": false 
            }
        ],
        "order": [[0, 'asc']],
        "pageLength": 10
    });

    // 3. HELPER FUNCTION (Toast)
    // Assuming toastr is loaded in your template. If not, use alert().
    function showToast(type, message) {
        if (typeof toastr !== 'undefined') {
            if (type === 'success') {
                toastr.success(message, 'Success');
            } else {
                toastr.error(message, 'Error');
            }
        } else {
            alert(type + ': ' + message);
        }
    }

    // 4. ADD USER
    $('#addUserForm').on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            url: window.baseUrl + '/save',
            method: 'POST',
            data: $(this).serialize() + '&' + window.csrfName + '=' + window.csrfHash,
            dataType: 'json',
            success: function (response) {
                if (response.status === 'success') {
                    $('#AddNewModal').modal('hide');
                    $('#addUserForm')[0].reset();
                    showToast('success', 'User added successfully!');
                    table.ajax.reload(null, false);
                } else {
                    showToast('error', response.message);
                }
            },
            error: function () {
                showToast('error', 'An error occurred.');
            }
        });
    });

    // 5. EDIT USER (CLICK)
    $(document).on('click', '.edit-btn', function() {
        var userId = $(this).data('id');
        $.get(window.baseUrl + '/edit/' + userId, function(response) {
            if (response.data) {
                $('#editUserModal #name').val(response.data.name);
                $('#editUserModal #userId').val(response.data.id);
                $('#editUserModal #email').val(response.data.email);
                $('#editUserModal #password').val('');
                $('#editUserModal #role').val(response.data.role);
                $('#editUserModal #status').val(response.data.status);
                $('#editUserModal').modal('show');
            } else {
                alert('Error fetching user data');
            }
        });
    });

    // 6. UPDATE USER (SUBMIT)
    $('#editUserForm').on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            url: window.baseUrl + '/update',
            method: 'POST',
            data: $(this).serialize() + '&' + window.csrfName + '=' + window.csrfHash,
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    $('#editUserModal').modal('hide');
                    showToast('success', 'User updated successfully!');
                    table.ajax.reload(null, false);
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

    // 7. DELETE USER
    $(document).on('click', '.deleteUserBtn', function() {
        var userId = $(this).data('id');
        if (confirm('Are you sure you want to delete this user?')) {
            $.ajax({
                url: window.baseUrl + '/delete/' + userId,
                method: 'POST',
                data: { [window.csrfName]: window.csrfHash },
                success: function (response) {
                    if (response.success) {
                        showToast('success', 'User deleted successfully.');
                        table.ajax.reload(null, false);
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
});
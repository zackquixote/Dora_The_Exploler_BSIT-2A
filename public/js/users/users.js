// public/js/users/users.js

$(document).ready(function() {
    function redirectIfSessionExpired(xhr) {
        if (!xhr || (xhr.status !== 401 && xhr.status !== 403)) {
            return false;
        }
        try {
            var body = xhr.responseJSON || JSON.parse(xhr.responseText || '{}');
            if (body.redirect) {
                window.location.href = body.redirect;
                return true;
            }
        } catch (e) { /* ignore */ }
        var base = (window.baseUrl || '').replace(/\/+$/, '');
        window.location.href = base ? (base + '/login') : '/login';
        return true;
    }

    // 1. GLOBAL VARIABLES (Set by PHP in the View)
    // We rely on the View to pass 'baseUrl', 'csrfName', and 'csrfHash'
    
    // 2. DATATABLE INIT
    var table = $('#users-table').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": window.baseUrl + "/fetchRecords", // Use window.baseUrl
            "type": "POST",
            "headers": { "Accept": "application/json" },
            "data": function(d) {
                // Add CSRF Token dynamically
                d[window.csrfName] = window.csrfHash;
            },
            "error": function(xhr, error, thrown) {
                if (redirectIfSessionExpired(xhr)) {
                    return;
                }
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

    function refreshCsrf(response) {
        if (response && response.csrf_hash) {
            window.csrfHash = response.csrf_hash;
            $('input[name="' + window.csrfName + '"]').val(response.csrf_hash);
        }
    }

    // 4. ADD USER
    $('#addUserForm').on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            url: window.baseUrl + '/save',
            method: 'POST',
            headers: { Accept: 'application/json' },
            data: $(this).serialize() + '&' + window.csrfName + '=' + window.csrfHash,
            dataType: 'json',
            success: function (response) {
                refreshCsrf(response);
                if (response.status === 'success') {
                    $('#AddNewModal').modal('hide');
                    $('#addUserForm')[0].reset();
                    showToast('success', 'User added successfully!');
                    table.ajax.reload(null, false);
                } else {
                    showToast('error', response.message);
                }
            },
            error: function (xhr) {
                if (redirectIfSessionExpired(xhr)) {
                    return;
                }
                showToast('error', 'An error occurred.');
            }
        });
    });

    // 5. EDIT USER (CLICK)
    $(document).on('click', '.edit-btn', function() {
        var userId = $(this).data('id');
        $.ajax({
            url: window.baseUrl + '/edit/' + userId,
            method: 'GET',
            headers: { Accept: 'application/json' },
            dataType: 'json',
            success: function(response) {
                refreshCsrf(response);
                if (response.data) {
                    $('#editUserForm #name').val(response.data.name);
                    $('#editUserForm #userId').val(response.data.id);
                    $('#editUserForm #email').val(response.data.email);
                    $('#editUserForm #password').val('');
                    $('#editUserForm #role').val(response.data.role);
                    $('#editUserForm #status').val(response.data.status);
                    $('#editUserForm #phone').val(response.data.phone || '');
                    $('#editUserOverlay').addClass('show');
                } else {
                    alert('Error fetching user data');
                }
            },
            error: function(xhr) {
                if (redirectIfSessionExpired(xhr)) {
                    return;
                }
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
            headers: { Accept: 'application/json' },
            data: $(this).serialize() + '&' + window.csrfName + '=' + window.csrfHash,
            dataType: 'json',
            success: function (response) {
                refreshCsrf(response);
                if (response.success) {
                    $('#editUserOverlay').removeClass('show');
                    showToast('success', 'User updated successfully!');
                    table.ajax.reload(null, false);
                } else {
                    alert(response.message || 'Error updating');
                }
            },
            error: function (xhr) {
                if (redirectIfSessionExpired(xhr)) {
                    return;
                }
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
                headers: { Accept: 'application/json' },
                data: { [window.csrfName]: window.csrfHash },
                dataType: 'json',
                success: function (response) {
                    refreshCsrf(response);
                    if (response.success) {
                        showToast('success', 'User deleted successfully.');
                        table.ajax.reload(null, false);
                    } else {
                        alert(response.message || 'Failed to delete.');
                    }
                },
                error: function (xhr) {
                    if (redirectIfSessionExpired(xhr)) {
                        return;
                    }
                    alert('Something went wrong while deleting.');
                }
            });
        }
    });
});
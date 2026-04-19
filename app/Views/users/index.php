<?= $this->extend('theme/template') ?>

<?= $this->section('content') ?>
<div class="content-wrapper">
    <section class="content">
        <div class="container-fluid">
            <!-- Table Card -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">User Accounts</h3>
                    <button class="btn btn-primary float-right" data-toggle="modal" data-target="#AddNewModal">Add New</button>
                </div>
                <div class="card-body">
                    <table id="users-table" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data will be populated by DataTables -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Add/Edit Modals (Your existing modal HTML here) -->
<div class="toasts-top-right fixed" style="position: fixed; top: 1rem; right: 1rem; z-index: 9999;"></div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    const baseUrl = "<?= site_url('staff/users') ?>";
</script>
<!-- Include DataTables CSS and JS if not already included -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#users-table').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": baseUrl + "/fetchRecords",
            "type": "POST",
            "dataType": "json",
            "error": function(xhr, error, thrown) {
                console.log("Ajax error:", error);
                console.log("Status:", xhr.status);
                console.log("Response:", xhr.responseText);
                alert("Error loading data. Check console for details.");
            }
        },
        "columns": [
            { "data": 0 },
            { "data": 1 },
            { "data": 2 },
            { "data": 3 },
            { "data": 4 },
            { "data": 5, "orderable": false }
        ],
        "order": [[0, 'asc']],
        "pageLength": 10
    });
    
    // Handle edit button click (example)
    $(document).on('click', '.edit-btn', function() {
        var userId = $(this).data('id');
        // Load user data and show edit modal
        $.get(baseUrl + '/edit/' + userId, function(response) {
            if (response.data) {
                // Populate your edit modal with response.data
                console.log('User data:', response.data);
                // Show edit modal
                $('#EditModal').modal('show');
            }
        });
    });
    
    // Handle delete button click (example)
    $(document).on('click', '.delete-btn', function() {
        if (confirm('Are you sure you want to delete this user?')) {
            var userId = $(this).data('id');
            $.post(baseUrl + '/delete/' + userId, function(response) {
                if (response.success) {
                    $('#users-table').DataTable().ajax.reload();
                    alert(response.message);
                } else {
                    alert(response.message);
                }
            });
        }
    });
});
</script>
<?= $this->endSection() ?>
<?= $this->extend('theme/admin/template') ?>

<?= $this->section('content') ?>
<div class="content-wrapper">
    <section class="content">
        <div class="container-fluid">
            
            <!-- Flash Messages (For Create/Update/Delete Feedback) -->
            <?php if (session()->get('success')): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <?= session()->get('success') ?>
                </div>
            <?php endif; ?>

            <?php if (session()->get('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <?= session()->get('error') ?>
                </div>
            <?php endif; ?>

            <!-- Table Card -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">User Accounts</h3>
                    
                    <!-- Link to separate Create Page -->
                    <a href="<?= base_url('admin/users/create') ?>" class="btn btn-primary float-right">
                        <i class="fas fa-plus"></i> Add New
                    </a>
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
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- MODAL: EDIT USER -->
<div class="modal fade" id="editUserModal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editUserForm">
                <div class="modal-header">
                    <h5 class="modal-title">Edit User</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="userId" id="userId">
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" name="name" id="name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" id="email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Password (leave blank to keep current)</label>
                        <input type="password" name="password" id="password" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Role</label>
                        <select name="role" id="role" class="form-control">
                            <option value="staff">Staff</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" id="status" class="form-control">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- 1. Dependencies -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<!-- 2. Configuration -->
<script>
    window.baseUrl = "<?= site_url('admin/users') ?>";
    window.csrfName = "<?= csrf_token() ?>";
    window.csrfHash = "<?= csrf_hash() ?>";
</script>

<!-- 3. External JS File (Contains all logic) -->
<script src="<?= base_url('js/users/users.js') ?>"></script>

<?= $this->endSection() ?>


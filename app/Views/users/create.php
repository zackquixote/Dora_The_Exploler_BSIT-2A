<?= $this->extend('theme/admin/template') ?>

<?= $this->section('content') ?>
<div class="content-wrapper">
    <section class="content">
        <div class="container-fluid">
            <!-- FIX: Added style="position: relative; z-index: 99;" to prevent overlay issues -->
            <div class="card" style="position: relative; z-index: 99;">
                <div class="card-header">
                    <h3 class="card-title">Add New User</h3>
                    <a href="<?= base_url('admin/users') ?>" class="btn btn-secondary float-right">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                </div>
                <div class="card-body">
                    <!-- FIX: Added role="form" for better Bootstrap/AdminLTE support -->
                    <form action="<?= base_url('admin/users/save') ?>" method="POST" role="form" style="position: relative; z-index: 100;">
                        
                        <?= csrf_field() ?> 
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Full Name</label>
                                    <input type="text" name="name" class="form-control" id="name" placeholder="Enter full name" required autofocus>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Email Address</label>
                                    <input type="email" name="email" class="form-control" id="email" placeholder="Enter email" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password">Password</label>
                                    <input type="password" name="password" class="form-control" id="password" placeholder="Enter password" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phone">Phone Number</label>
                                    <input type="text" name="phone" class="form-control" id="phone" placeholder="Enter phone number">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="role">Role</label>
                                    <select name="role" class="form-control" id="role">
                                        <option value="staff">Staff</option>
                                        <option value="admin">Admin</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select name="status" class="form-control" id="status">
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-3" style="position: relative; z-index: 101;">
                            <button type="submit" class="btn btn-primary">Save User</button>
                            <a href="<?= base_url('admin/users') ?>" class="btn btn-default">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>
<?= $this->endSection() ?>
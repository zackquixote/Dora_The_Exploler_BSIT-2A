<?= $this->extend('theme/admin/template') ?>

<?= $this->section('content') ?>
<div class="content-wrapper">
    <section class="content">
        <div class="container-fluid">
            <div class="card" style="position: relative; z-index: 99;">
                <div class="card-header">
                    <h3 class="card-title">Edit User</h3>
                    <a href="<?= base_url('admin/users') ?>" class="btn btn-secondary float-right">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('admin/users/update/' . $user['id']) ?>" method="POST" role="form" style="position: relative; z-index: 100;">
                        
                        <?= csrf_field() ?> 
                        
                        <input type="hidden" name="userId" value="<?= $user['id'] ?>">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Full Name</label>
                                    <input type="text" name="name" class="form-control" id="name" value="<?= old('name', $user['name']) ?>" required autofocus>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Email Address</label>
                                    <input type="email" name="email" class="form-control" id="email" value="<?= old('email', $user['email']) ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password">Password (leave blank to keep current)</label>
                                    <input type="password" name="password" class="form-control" id="password" placeholder="Enter new password if changing">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phone">Phone Number</label>
                                    <input type="text" name="phone" class="form-control" id="phone" value="<?= old('phone', $user['phone']) ?>" placeholder="Enter phone number">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="role">Role</label>
                                    <select name="role" class="form-control" id="role">
                                        <option value="staff" <?= ($user['role'] == 'staff') ? 'selected' : '' ?>>Staff</option>
                                        <option value="admin" <?= ($user['role'] == 'admin') ? 'selected' : '' ?>>Admin</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select name="status" class="form-control" id="status">
                                        <option value="active" <?= ($user['status'] == 'active') ? 'selected' : '' ?>>Active</option>
                                        <option value="inactive" <?= ($user['status'] == 'inactive') ? 'selected' : '' ?>>Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-3" style="position: relative; z-index: 101;">
                            <button type="submit" class="btn btn-primary">Update User</button>
                            <a href="<?= base_url('admin/users') ?>" class="btn btn-default">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>
<?= $this->endSection() ?>
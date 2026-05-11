<?= $this->extend('theme/template') ?>
<?= $this->section('content') ?>

<div class="bmis-content">
    <div class="ds-card">
        <div class="ds-card-head">
            <div class="ds-card-title"><i class="fas fa-user-edit"></i> Edit User</div>
            <a href="<?= base_url('admin/users') ?>" class="ds-btn ds-btn-ghost"><i class="fas fa-arrow-left"></i> Back</a>
        </div>
        <form action="<?= base_url('admin/users/update') ?>" method="POST">
            <?= csrf_field() ?>
            <input type="hidden" name="userId" value="<?= $user['id'] ?>">
            <div class="ds-card-body">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
                    <div><label class="ds-input-label">Full Name <span style="color:var(--c-rose)">*</span></label><input type="text" name="name" class="ds-input" value="<?= old('name', $user['name']) ?>" required></div>
                    <div><label class="ds-input-label">Email Address <span style="color:var(--c-rose)">*</span></label><input type="email" name="email" class="ds-input" value="<?= old('email', $user['email']) ?>" required></div>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-top:14px">
                    <div><label class="ds-input-label">Password (leave blank to keep)</label><input type="password" name="password" class="ds-input" placeholder="Enter new password"></div>
                    <div><label class="ds-input-label">Phone Number</label><input type="text" name="phone" class="ds-input" value="<?= old('phone', $user['phone']) ?>"></div>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-top:14px">
                    <div><label class="ds-input-label">Role</label><select name="role" class="ds-select"><option value="staff" <?= ($user['role']=='staff')?'selected':'' ?>>Staff</option><option value="admin" <?= ($user['role']=='admin')?'selected':'' ?>>Admin</option></select></div>
                    <div><label class="ds-input-label">Status</label><select name="status" class="ds-select"><option value="active" <?= ($user['status']=='active')?'selected':'' ?>>Active</option><option value="inactive" <?= ($user['status']=='inactive')?'selected':'' ?>>Inactive</option></select></div>
                </div>
            </div>
            <div style="padding:12px 18px;border-top:.5px solid var(--border);display:flex;justify-content:flex-end;gap:8px">
                <a href="<?= base_url('admin/users') ?>" class="ds-btn ds-btn-ghost">Cancel</a>
                <button type="submit" class="ds-btn ds-btn-primary"><i class="fas fa-save"></i> Update User</button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
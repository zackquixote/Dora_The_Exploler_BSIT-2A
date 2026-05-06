<?= $this->extend('theme/admin/template') ?>
<?= $this->section('content') ?>

<div class="bmis-content">

    <?php if (session()->get('success')): ?>
        <div style="background:var(--c-teal-bg);color:var(--c-teal);padding:10px 16px;border-radius:var(--r-sm);margin-bottom:14px;font-size:12px;font-weight:600;display:flex;align-items:center;gap:8px">
            <i class="fas fa-check-circle"></i> <?= session()->get('success') ?>
        </div>
    <?php endif; ?>
    <?php if (session()->get('error')): ?>
        <div style="background:var(--c-rose-bg);color:var(--c-rose);padding:10px 16px;border-radius:var(--r-sm);margin-bottom:14px;font-size:12px;font-weight:600;display:flex;align-items:center;gap:8px">
            <i class="fas fa-exclamation-circle"></i> <?= session()->get('error') ?>
        </div>
    <?php endif; ?>

    <div class="ds-card">
        <div class="ds-card-head">
            <div class="ds-card-title"><i class="fas fa-user-lock"></i> User Accounts</div>
            <a href="<?= base_url('admin/users/create') ?>" class="ds-btn ds-btn-primary"><i class="fas fa-plus"></i> Add New</a>
        </div>
        <div class="ds-card-body">
            <table id="users-table" class="ds-table">
                <thead><tr><th>No.</th><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Actions</th></tr></thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL: EDIT USER -->
<div class="ds-modal-overlay" id="editUserOverlay">
    <div class="ds-modal" style="max-width:480px">
        <div class="ds-modal-icon" style="background:var(--c-blue-bg);color:var(--c-blue)"><i class="fas fa-user-edit"></i></div>
        <h3>Edit User</h3>
        <form id="editUserForm">
            <input type="hidden" name="userId" id="userId">
            <div style="margin-bottom:12px"><label class="ds-input-label">Name</label><input type="text" name="name" id="name" class="ds-input" required></div>
            <div style="margin-bottom:12px"><label class="ds-input-label">Email</label><input type="email" name="email" id="email" class="ds-input" required></div>
            <div style="margin-bottom:12px"><label class="ds-input-label">Password (leave blank to keep)</label><input type="password" name="password" id="password" class="ds-input"></div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:16px">
                <div><label class="ds-input-label">Role</label><select name="role" id="role" class="ds-select"><option value="staff">Staff</option><option value="admin">Admin</option></select></div>
                <div><label class="ds-input-label">Status</label><select name="status" id="status" class="ds-select"><option value="active">Active</option><option value="inactive">Inactive</option></select></div>
            </div>
            <div class="ds-modal-actions">
                <button type="button" class="ds-btn ds-btn-ghost" onclick="document.getElementById('editUserOverlay').classList.remove('show')">Close</button>
                <button type="submit" class="ds-btn ds-btn-primary">Update</button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    window.baseUrl = "<?= site_url('admin/users') ?>";
    window.csrfName = "<?= csrf_token() ?>";
    window.csrfHash = "<?= csrf_hash() ?>";
</script>
<script src="<?= base_url('js/users/users.js') ?>"></script>
<?= $this->endSection() ?>

<?= $this->extend('theme/template') ?>
<?= $this->section('content') ?>

<div class="bmis-content">

    <!-- Page Header -->
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px">
        <div style="display:flex;align-items:center;gap:16px">
            <div style="width:48px;height:48px;border-radius:12px;background:var(--c-blue-bg);color:var(--c-blue);display:flex;align-items:center;justify-content:center;font-size:20px">
                <i class="fas fa-users-cog"></i>
            </div>
            <div>
                <div class="ds-page-title" style="margin:0;font-size:24px;font-weight:800;color:var(--ink)">User Management</div>
                <div style="font-size:13px;color:var(--ink-muted);margin-top:2px;font-weight:700">Manage system administrators and staff access</div>
            </div>
        </div>
        <a href="<?= base_url('admin/users/create') ?>" class="ds-btn ds-btn-primary" style="height:40px;padding:0 20px;border-radius:20px;box-shadow:0 4px 12px rgba(var(--c-blue-rgb), 0.3)">
            <i class="fas fa-user-plus"></i> Add New User
        </a>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div style="background:var(--c-teal-bg);color:var(--c-teal);padding:14px 20px;border-radius:var(--r-md);margin-bottom:24px;font-size:13px;font-weight:600;display:flex;align-items:center;gap:10px;border:1px solid rgba(var(--c-teal-rgb), 0.2)">
            <i class="fas fa-check-circle" style="font-size:16px"></i> <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div style="background:var(--c-rose-bg);color:var(--c-rose);padding:14px 20px;border-radius:var(--r-md);margin-bottom:24px;font-size:13px;font-weight:600;display:flex;align-items:center;gap:10px;border:1px solid rgba(var(--c-rose-rgb), 0.2)">
            <i class="fas fa-exclamation-circle" style="font-size:16px"></i> <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <div class="ds-card" style="border:none;box-shadow:0 10px 30px rgba(0,0,0,0.03);border-radius:var(--r-lg);overflow:hidden">
        <div class="ds-card-head" style="background:var(--white);padding:20px 24px;border-bottom:1px solid var(--border)">
            <div class="ds-card-title"><i class="fas fa-list-ul"></i> System Users Directory</div>
        </div>
        <div class="ds-card-body p0">
            <div style="padding:24px">
                <table id="users-table" class="ds-table" style="width:100%">
                    <thead>
                        <tr>
                            <th style="width:50px">No.</th>
                            <th>Full Name</th>
                            <th>Email Address</th>
                            <th>Role Level</th>
                            <th>Account Status</th>
                            <th style="width:120px;text-align:right">Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- MODAL: EDIT USER -->
<div class="ds-modal-overlay" id="editUserOverlay">
    <div class="ds-modal" style="max-width:550px;padding:32px">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:24px">
            <div>
                <div class="ds-modal-icon" style="background:var(--c-blue-bg);color:var(--c-blue);margin-bottom:12px;margin-inline:0"><i class="fas fa-user-edit"></i></div>
                <h3 style="margin:0;font-size:18px;font-weight:700;color:var(--ink)">Edit User Account</h3>
                <p style="margin:4px 0 0;font-size:12px;color:var(--ink-soft)">Modify the details and access level for this user.</p>
            </div>
            <button type="button" class="tb-icon-btn" onclick="document.getElementById('editUserOverlay').classList.remove('show')"><i class="fas fa-times"></i></button>
        </div>
        
        <form id="editUserForm">
            <input type="hidden" name="userId" id="userId">
            
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px">
                <div>
                    <label class="ds-input-label">Full Name <span style="color:var(--c-rose)">*</span></label>
                    <input type="text" name="name" id="name" class="ds-input" placeholder="e.g. John Doe" required>
                </div>
                <div>
                    <label class="ds-input-label">Email Address <span style="color:var(--c-rose)">*</span></label>
                    <input type="email" name="email" id="email" class="ds-input" placeholder="user@bmis.com" required>
                </div>
            </div>

            <div style="margin-bottom:16px">
                <label class="ds-input-label">Phone Number</label>
                <input type="text" name="phone" id="phone" class="ds-input" placeholder="e.g. 09123456789">
            </div>
            
            <div style="margin-bottom:16px">
                <label class="ds-input-label">New Password</label>
                <div style="position:relative">
                    <input type="password" name="password" id="password" class="ds-input" placeholder="Leave blank to keep current password">
                    <i class="fas fa-lock" style="position:absolute;right:14px;top:50%;transform:translateY(-50%);color:var(--ink-soft);font-size:12px"></i>
                </div>
                <div style="font-size:10px;color:var(--ink-soft);margin-top:6px"><i class="fas fa-info-circle"></i> Only fill this if you want to reset the user's password.</div>
            </div>

            <div style="padding:16px;background:var(--bg);border-radius:var(--r-sm);border:.5px solid var(--border);margin-bottom:24px">
                <div style="font-size:11px;font-weight:700;color:var(--ink-muted);text-transform:uppercase;letter-spacing:.04em;margin-bottom:12px">Account Settings</div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
                    <div>
                        <label class="ds-input-label">Role</label>
                        <select name="role" id="role" class="ds-select">
                            <option value="staff">Staff (Limited Access)</option>
                            <option value="admin">Admin (Full Access)</option>
                        </select>
                    </div>
                    <div>
                        <label class="ds-input-label">Account Status</label>
                        <select name="status" id="status" class="ds-select">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
            </div>

            <div style="display:flex;justify-content:flex-end;gap:10px;border-top:.5px solid var(--border);padding-top:20px">
                <button type="button" class="ds-btn ds-btn-ghost" onclick="document.getElementById('editUserOverlay').classList.remove('show')">Cancel</button>
                <button type="submit" class="ds-btn ds-btn-primary"><i class="fas fa-save"></i> Save Changes</button>
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

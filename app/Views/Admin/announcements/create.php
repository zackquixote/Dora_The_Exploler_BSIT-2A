<?= $this->extend('theme/template') ?>

<?= $this->section('content') ?>
<div class="bmis-header">
    <div class="bmis-title">
        <a href="<?= base_url('admin/announcements') ?>" class="ds-btn ds-btn-light ds-btn-sm" style="margin-right:12px;border-radius:50%;width:32px;height:32px;padding:0;display:flex;align-items:center;justify-content:center"><i class="fas fa-arrow-left"></i></a>
        <div>
            <h2>Create Announcement</h2>
            <p>Post a new advisory or news update to the portal.</p>
        </div>
    </div>
</div>

<div class="ds-container">
    <?php if (session()->getFlashdata('error')): ?>
        <div class="ds-alert ds-alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <div class="ds-card" style="max-width:800px;margin:0 auto">
        <form method="post" action="<?= base_url('admin/announcements/create') ?>" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <div class="ds-card-body">
                <div class="ds-form-group">
                    <label class="ds-form-label">Announcement Title <span style="color:red">*</span></label>
                    <input type="text" name="title" class="ds-input" required placeholder="e.g. Barangay Fiesta Schedule">
                </div>

                <div class="ds-form-group">
                    <label class="ds-form-label">Content / Body <span style="color:red">*</span></label>
                    <textarea name="body" class="ds-input" rows="8" required placeholder="Write the full announcement here..."></textarea>
                </div>

                <div class="ds-form-group">
                    <label class="ds-form-label">Attach Image (Optional)</label>
                    <input type="file" name="image" class="ds-input" accept="image/jpeg,image/png,image/gif">
                    <small style="color:var(--ink-muted)">Max size: 2MB. Recommended format: JPG, PNG.</small>
                </div>

                <div class="ds-form-group" style="margin-top:20px">
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer">
                        <input type="checkbox" name="is_pinned" value="1" style="width:16px;height:16px">
                        <span style="font-weight:600;color:var(--ink)">Pin to top of portal home</span>
                    </label>
                    <p style="margin-top:4px;font-size:12px;color:var(--ink-muted);margin-left:24px">Pinned announcements stay visible at the top until unpinned or deleted.</p>
                </div>
            </div>
            <div class="ds-card-footer" style="display:flex;justify-content:flex-end;gap:12px">
                <a href="<?= base_url('admin/announcements') ?>" class="ds-btn ds-btn-light">Cancel</a>
                <button type="submit" class="ds-btn ds-btn-primary">Post Announcement</button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>

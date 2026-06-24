<?= $this->extend('theme/template') ?>

<?= $this->section('content') ?>
<div class="bmis-header">
    <div class="bmis-title">
        <div class="icon" style="background:#f59e0b;color:#fff"><i class="fas fa-bullhorn"></i></div>
        <div>
            <h2>Announcements</h2>
            <p>Manage official barangay news and advisories for residents.</p>
        </div>
    </div>
    <div class="bmis-header-actions">
        <a href="<?= base_url('admin/announcements/create') ?>" class="ds-btn ds-btn-primary">
            <i class="fas fa-plus"></i> New Announcement
        </a>
    </div>
</div>

<div class="ds-container">
    <?php if (session()->getFlashdata('success')): ?>
        <div class="ds-alert ds-alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="ds-alert ds-alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <div class="ds-card">
        <div class="ds-card-header">
            <h3>All Announcements (<?= count($announcements) ?>)</h3>
        </div>
        <div class="ds-card-body" style="padding:0">
            <div class="ds-table-wrapper">
                <table class="ds-table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Date Posted</th>
                            <th>Status</th>
                            <th style="text-align:right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($announcements)): ?>
                        <tr><td colspan="4" style="text-align:center;padding:24px;color:var(--ink-muted)">No announcements posted yet.</td></tr>
                        <?php else: ?>
                            <?php foreach ($announcements as $row): ?>
                            <tr>
                                <td>
                                    <strong><?= esc($row['title']) ?></strong>
                                    <?php if ($row['is_pinned']): ?>
                                        <span class="ds-badge ds-badge-amber" style="margin-left:8px"><i class="fas fa-thumbtack"></i> Pinned</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= date('M d, Y h:i A', strtotime($row['created_at'])) ?></td>
                                <td><span class="ds-badge ds-badge-success">Published</span></td>
                                <td style="text-align:right">
                                    <form method="post" action="<?= base_url('admin/announcements/delete/' . $row['id']) ?>" style="margin:0;display:inline-block" onsubmit="return confirm('Are you sure you want to delete this announcement?');">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="ds-btn ds-btn-danger ds-btn-sm" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

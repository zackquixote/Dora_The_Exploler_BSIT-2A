<?= $this->extend('theme/template') ?>

<?= $this->section('content') ?>
<div class="bmis-header">
    <div class="bmis-title">
        <div class="icon"><i class="fas fa-building"></i></div>
        <div>
            <h2>Facility Bookings</h2>
            <p>Manage and approve resident requests for barangay facilities and equipment.</p>
        </div>
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
        <div class="ds-card-body" style="padding:0">
            <table class="ds-table">
                <thead>
                    <tr>
                        <th>Resident</th>
                        <th>Facility</th>
                        <th>Schedule</th>
                        <th>Purpose</th>
                        <th>Status</th>
                        <th style="text-align:right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($bookings)): ?>
                        <tr><td colspan="6" style="text-align:center;padding:24px;color:var(--ink-muted)">No bookings found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($bookings as $b): ?>
                            <tr>
                                <td>
                                    <strong><?= esc($b['first_name'] . ' ' . $b['last_name']) ?></strong>
                                </td>
                                <td>
                                    <div style="font-weight:600"><?= esc($b['facility_name']) ?></div>
                                    <div style="font-size:11px;color:var(--ink-muted)"><?= esc($b['facility_type']) ?></div>
                                </td>
                                <td>
                                    <div style="font-size:13px;color:var(--ink)">
                                        <?= date('M d, Y h:i A', strtotime($b['start_datetime'])) ?>
                                    </div>
                                    <div style="font-size:13px;color:var(--ink-muted)">
                                        to <?= date('M d, Y h:i A', strtotime($b['end_datetime'])) ?>
                                    </div>
                                </td>
                                <td><?= esc($b['purpose']) ?></td>
                                <td>
                                    <?php if ($b['status'] === 'Pending'): ?>
                                        <span class="ds-badge ds-badge-amber">Pending</span>
                                    <?php elseif ($b['status'] === 'Approved'): ?>
                                        <span class="ds-badge ds-badge-teal">Approved</span>
                                    <?php elseif ($b['status'] === 'Rejected'): ?>
                                        <span class="ds-badge ds-badge-danger">Rejected</span>
                                    <?php elseif ($b['status'] === 'Cancelled'): ?>
                                        <span class="ds-badge ds-badge-gray">Cancelled</span>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align:right">
                                    <?php if ($b['status'] === 'Pending'): ?>
                                        <div style="display:inline-flex;gap:4px">
                                            <form method="post" action="<?= base_url('admin/facility-bookings/approve/' . $b['id']) ?>" style="margin:0">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="ds-btn ds-btn-sm ds-btn-primary" onclick="return confirm('Approve this booking?')"><i class="fas fa-check"></i></button>
                                            </form>
                                            <button type="button" class="ds-btn ds-btn-sm ds-btn-danger" onclick="rejectBooking(<?= $b['id'] ?>)"><i class="fas fa-times"></i></button>
                                        </div>
                                    <?php else: ?>
                                        <?php if (!empty($b['remarks'])): ?>
                                            <button class="ds-btn ds-btn-sm ds-btn-light" onclick="alert('Remarks: <?= esc(addslashes($b['remarks'])) ?>')"><i class="fas fa-info-circle"></i> Info</button>
                                        <?php else: ?>
                                            <span style="font-size:12px;color:var(--ink-muted)">No actions</span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
    <div class="ds-card" style="width:400px; margin:0 auto; margin-top:10vh;">
        <form id="rejectForm" method="post" action="">
            <?= csrf_field() ?>
            <div class="ds-card-header">
                <h3>Reject Booking</h3>
            </div>
            <div class="ds-card-body">
                <div class="ds-form-group">
                    <label class="ds-form-label">Reason for Rejection</label>
                    <textarea name="remarks" class="ds-input" rows="3" required placeholder="e.g. Facility is already booked, Under maintenance..."></textarea>
                </div>
            </div>
            <div class="ds-card-footer" style="display:flex;justify-content:flex-end;gap:10px">
                <button type="button" class="ds-btn ds-btn-light" onclick="document.getElementById('rejectModal').style.display='none'">Cancel</button>
                <button type="submit" class="ds-btn ds-btn-danger">Reject</button>
            </div>
        </form>
    </div>
</div>

<script>
function rejectBooking(id) {
    document.getElementById('rejectForm').action = '<?= base_url('admin/facility-bookings/reject/') ?>' + id;
    document.getElementById('rejectModal').style.display = 'flex';
}
</script>
<?= $this->endSection() ?>

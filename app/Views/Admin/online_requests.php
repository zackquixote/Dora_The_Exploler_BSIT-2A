<?= $this->extend('theme/template') ?>

<?= $this->section('content') ?>
<div class="bmis-header">
    <div class="bmis-title">
        <div class="icon"><i class="fas fa-inbox"></i></div>
        <div>
            <h2>Online Requests</h2>
            <p>Review and approve certificates and incidents submitted by residents via the portal.</p>
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

    <div class="ds-grid-2">
        <!-- Certificate Requests -->
        <div class="ds-card">
            <div class="ds-card-header" style="display:flex; justify-content:space-between; align-items:center;">
                <h3><i class="fas fa-file-signature"></i> Certificate Requests</h3>
                <span class="ds-badge ds-badge-warning"><?= count($certRequests) ?> Pending</span>
            </div>
            <div class="ds-card-body" style="padding:0">
                <div class="ds-table-wrapper">
                    <table class="ds-table">
                        <thead>
                            <tr>
                                <th>Resident</th>
                                <th>Certificate Type</th>
                                <th>Purpose</th>
                                <th>Date Requested</th>
                                <th style="text-align:right">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($certRequests)): ?>
                                <tr><td colspan="5" style="text-align:center;padding:24px;color:var(--ink-muted)">No pending certificate requests.</td></tr>
                            <?php else: ?>
                                <?php foreach ($certRequests as $req): ?>
                                <tr>
                                    <td>
                                        <strong><?= esc($req['first_name'] . ' ' . $req['last_name']) ?></strong><br>
                                        <small class="text-muted"><i class="fas fa-map-marker-alt"></i> <?= esc($req['sitio']) ?></small>
                                    </td>
                                    <td><span class="ds-badge ds-badge-primary"><?= esc($req['certificate_type']) ?></span></td>
                                    <td style="max-width: 150px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="<?= esc($req['purpose']) ?>">
                                        <?= esc($req['purpose']) ?>
                                    </td>
                                    <td><?= date('M d, Y', strtotime($req['created_at'])) ?></td>
                                    <td style="text-align:right">
                                        <div style="display:flex;gap:4px;justify-content:flex-end">
                                            <form method="post" action="<?= base_url('admin/online-requests/approve-certificate/' . $req['id']) ?>" style="margin:0" onsubmit="return confirm('Approve and issue this certificate?');">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="ds-btn ds-btn-teal ds-btn-sm" title="Approve & Issue">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                            <button type="button" class="ds-btn ds-btn-danger ds-btn-sm" title="Reject" onclick="openRejectCertModal(<?= $req['id'] ?>)">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Online Incident Reports -->
        <div class="ds-card">
            <div class="ds-card-header" style="display:flex; justify-content:space-between; align-items:center;">
                <h3><i class="fas fa-balance-scale"></i> Portal Incident Reports</h3>
                <span class="ds-badge ds-badge-warning"><?= count($blotterRequests) ?> Pending</span>
            </div>
            <div class="ds-card-body" style="padding:0">
                <div class="ds-table-wrapper">
                    <table class="ds-table">
                        <thead>
                            <tr>
                                <th>Case No.</th>
                                <th>Complainant</th>
                                <th>Incident Type</th>
                                <th>Date Filed</th>
                                <th style="text-align:right">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($blotterRequests)): ?>
                                <tr><td colspan="5" style="text-align:center;padding:24px;color:var(--ink-muted)">No pending incident reports.</td></tr>
                            <?php else: ?>
                                <?php foreach ($blotterRequests as $req): ?>
                                <tr>
                                    <td><strong><?= esc($req['case_number']) ?></strong></td>
                                    <td><?= esc($req['first_name'] . ' ' . $req['last_name']) ?></td>
                                    <td><span class="ds-badge ds-badge-primary"><?= esc($req['incident_type']) ?></span></td>
                                    <td><?= date('M d, Y', strtotime($req['created_at'])) ?></td>
                                    <td style="text-align:right">
                                        <div style="display:flex;gap:4px;justify-content:flex-end">
                                            <form method="post" action="<?= base_url('admin/online-requests/acknowledge-blotter/' . $req['id']) ?>" style="margin:0" onsubmit="return confirm('Acknowledge this report? It will be marked as Under Investigation.');">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="ds-btn ds-btn-teal ds-btn-sm" title="Acknowledge">
                                                    <i class="fas fa-check-double"></i> Ack
                                                </button>
                                            </form>
                                            <a href="<?= base_url('blotter/view/' . $req['id']) ?>" class="ds-btn ds-btn-primary ds-btn-sm" title="Review">
                                                <i class="fas fa-folder-open"></i>
                                            </a>
                                        </div>
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
</div>
</div>

<!-- Reject Certificate Modal -->
<div id="rejectCertModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
    <div class="ds-card" style="width:400px; margin:0 auto; margin-top:10vh;">
        <form id="rejectCertForm" method="post" action="">
            <?= csrf_field() ?>
            <div class="ds-card-header">
                <h3>Reject Certificate Request</h3>
            </div>
            <div class="ds-card-body">
                <div class="ds-form-group">
                    <label class="ds-form-label">Reason for Rejection</label>
                    <textarea name="rejection_note" class="ds-input" rows="3" required placeholder="e.g. Unpaid fees, Invalid request..."></textarea>
                </div>
            </div>
            <div class="ds-card-footer" style="display:flex;justify-content:flex-end;gap:10px">
                <button type="button" class="ds-btn ds-btn-light" onclick="document.getElementById('rejectCertModal').style.display='none'">Cancel</button>
                <button type="submit" class="ds-btn ds-btn-danger">Reject</button>
            </div>
        </form>
    </div>
</div>

<script>
function openRejectCertModal(id) {
    document.getElementById('rejectCertForm').action = '<?= base_url('admin/online-requests/reject-certificate/') ?>' + id;
    document.getElementById('rejectCertModal').style.display = 'flex';
}
</script>
<?= $this->endSection() ?>

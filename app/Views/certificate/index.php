<?php 
$role = session()->get('role');
$template = ($role == 'admin') ? 'theme/admin/template' : 'theme/template';
?>
<?= $this->extend($template) ?>
<?= $this->section('content') ?>

<div class="bmis-content">

    <?php if (session()->getFlashdata('success')): ?>
        <div style="background:var(--c-teal-bg);color:var(--c-teal);padding:10px 16px;border-radius:var(--r-sm);margin-bottom:14px;font-size:12px;font-weight:600;display:flex;align-items:center;gap:8px">
            <i class="fas fa-check-circle"></i> <?= esc(session()->getFlashdata('success')) ?>
        </div>
    <?php endif; ?>

    <div class="ds-card">
        <div class="ds-card-head">
            <div class="ds-card-title"><i class="fas fa-file-contract"></i> Issued Certificates</div>
            <a href="<?= base_url('certificate/create') ?>" class="ds-btn ds-btn-primary"><i class="fas fa-plus"></i> Issue New</a>
        </div>
        <div class="ds-card-body p0">
            <div style="overflow-x:auto">
                <table class="ds-table">
                    <thead><tr><th>Certificate No.</th><th>Type</th><th>Resident</th><th>Purpose</th><th>Date</th><th>Actions</th></tr></thead>
                    <tbody>
                        <?php if (empty($certificates)): ?>
                            <tr><td colspan="6" style="text-align:center;padding:32px;color:var(--ink-soft)">No certificates issued yet.</td></tr>
                        <?php else: foreach($certificates as $c):
                            $tc = 'ds-badge-violet';
                            if (stripos($c['certificate_type'],'Clearance')!==false) $tc='ds-badge-blue';
                            elseif (stripos($c['certificate_type'],'Indigency')!==false) $tc='ds-badge-amber';
                            elseif (stripos($c['certificate_type'],'Residency')!==false) $tc='ds-badge-teal';
                        ?>
                        <tr>
                            <td class="mono"><strong><?= esc($c['certificate_number'] ?? 'N/A') ?></strong></td>
                            <td><span class="ds-badge <?= $tc ?>"><?= esc($c['certificate_type']) ?></span></td>
                            <td><strong><?= esc($c['first_name'] . ' ' . $c['last_name']) ?></strong></td>
                            <td><?= esc($c['purpose']) ?></td>
                            <td><?= date('M d, Y', strtotime($c['created_at'])) ?></td>
                            <td style="white-space:nowrap">
                                <a href="<?= base_url('certificate/print/' . $c['id']) ?>" class="ds-action-btn ab-blue" title="Print"><i class="fas fa-print"></i></a>
                                <a href="<?= base_url('certificate/edit/' . $c['id']) ?>" class="ds-action-btn ab-amber" title="Edit"><i class="fas fa-edit"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
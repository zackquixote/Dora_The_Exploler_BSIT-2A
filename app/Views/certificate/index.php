<?= $this->extend('theme/template') ?>
<?= $this->section('content') ?>

<div class="bmis-content">

    <!-- Page Header -->
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px">
        <div style="display:flex;align-items:center;gap:16px">
            <div style="width:48px;height:48px;border-radius:12px;background:var(--c-violet-bg);color:var(--c-violet);display:flex;align-items:center;justify-content:center;font-size:20px">
                <i class="fas fa-file-signature"></i>
            </div>
            <div>
                <div class="ds-page-title" style="margin:0;font-size:24px;font-weight:800;color:var(--ink)">Certificate Issuance</div>
                <div style="font-size:13px;color:var(--ink-muted);margin-top:2px;font-weight:700">Manage and issue barangay certificates and clearances</div>
            </div>
        </div>
        <a href="<?= base_url('certificate/create') ?>" class="ds-btn ds-btn-primary" style="height:40px;padding:0 20px;border-radius:20px;box-shadow:0 4px 12px rgba(var(--c-blue-rgb), 0.3)">
            <i class="fas fa-plus"></i> Issue Certificate
        </a>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div style="background:var(--c-teal-bg);color:var(--c-teal);padding:14px 20px;border-radius:var(--r-md);margin-bottom:24px;font-size:13px;font-weight:600;display:flex;align-items:center;gap:10px;border:1px solid rgba(var(--c-teal-rgb), 0.2)">
            <i class="fas fa-check-circle" style="font-size:16px"></i> <?= esc(session()->getFlashdata('success')) ?>
        </div>
    <?php endif; ?>

    <div class="ds-card" style="margin-bottom:24px;border:none;box-shadow:0 10px 30px rgba(0,0,0,0.03);border-radius:var(--r-lg);overflow:hidden">
        <div class="ds-card-head" style="background:var(--white);padding:20px 24px;border-bottom:1px solid var(--border)">
            <div class="ds-card-title"><i class="fas fa-list"></i> Issued Certificates Log</div>
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
                            <td>
                                <?php $residentName = trim((string) ($c['first_name'] ?? '') . ' ' . (string) ($c['last_name'] ?? '')); ?>
                                <strong class="font-serif" style="font-size:14px;letter-spacing:-0.01em;"><?= esc($residentName !== '' ? $residentName : 'Former/Unknown Resident') ?></strong>
                            </td>
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
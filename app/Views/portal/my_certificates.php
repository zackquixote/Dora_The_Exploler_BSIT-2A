<?= $this->extend('portal/layout') ?>
<?= $this->section('content') ?>

<div class="af-container">
    <div class="af-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px; margin-bottom: 30px;">
        <div>
            <h1 class="af-title">My Certificate Requests</h1>
            <p class="af-subtitle">Track the status of your online certificate and clearance applications.</p>
        </div>
        <a href="<?= base_url('portal/certificates/request') ?>" class="af-btn" style="background: #8b5cf6; color: white; border: none; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; border-radius: 10px; padding: 10px 18px;">
            <i class="fas fa-plus-circle"></i> Request Certificate
        </a>
    </div>

    <style>
        .cert-header { display: flex; justify-content: space-between; align-items: center; padding: 16px 20px; background: rgba(15,23,42,0.01); border-bottom: 1px solid var(--border); }
        .cert-header-actions { display: flex; align-items: center; gap: 10px; }
        @media (max-width: 600px) {
            .cert-header { flex-direction: column; align-items: flex-start; gap: 12px; }
            .cert-header-actions { width: 100%; justify-content: space-between; }
        }
    </style>


    <?php if (empty($requests)): ?>
        <div class="af-card" style="text-align: center; padding: 60px 40px; border: 1.5px dashed var(--border); background: white;">
            <div style="font-size: 56px; color: rgba(139, 92, 246, 0.15); margin-bottom: 20px;">
                <i class="fas fa-file-invoice"></i>
            </div>
            <h3 style="font-weight: 800; color: var(--ink); margin-bottom: 8px;">No Certificate Requests Yet</h3>
            <p style="color: var(--ink-muted); font-size: 14px; margin-bottom: 24px;">You haven't requested any documents online yet. Submit a request to get started.</p>
            <a href="<?= base_url('portal/certificates/request') ?>" class="af-btn" style="background: #8b5cf6; color: white; text-decoration: none; padding: 10px 20px; border-radius: 10px;">
                <i class="fas fa-plus-circle"></i> Request a Document
            </a>
        </div>
    <?php else: ?>
        <!-- Summary Stats -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); gap: 12px; margin-bottom: 28px;">
            <?php
                $total = count($requests);
                $pending = count(array_filter($requests, fn($r) => $r['status'] === 'Pending'));
                $processing = count(array_filter($requests, fn($r) => $r['status'] === 'Processing'));
                $ready = count(array_filter($requests, fn($r) => $r['status'] === 'Ready for Pickup'));
                $released = count(array_filter($requests, fn($r) => $r['status'] === 'Released'));
            ?>
            <div class="af-card" style="padding: 16px; text-align: center; background: white; border: 1px solid var(--border);">
                <div style="font-size: 24px; font-weight: 900; color: var(--ink);"><?= $total ?></div>
                <div style="font-size: 11px; font-weight: 600; color: var(--ink-muted); margin-top: 4px;">Total Requests</div>
            </div>
            <div class="af-card" style="padding: 16px; text-align: center; background: white; border: 1px solid var(--border);">
                <div style="font-size: 24px; font-weight: 900; color: #f59e0b;"><?= $pending ?></div>
                <div style="font-size: 11px; font-weight: 600; color: var(--ink-muted); margin-top: 4px;">Pending</div>
            </div>
            <div class="af-card" style="padding: 16px; text-align: center; background: white; border: 1px solid var(--border);">
                <div style="font-size: 24px; font-weight: 900; color: #3b82f6;"><?= $processing ?></div>
                <div style="font-size: 11px; font-weight: 600; color: var(--ink-muted); margin-top: 4px;">Processing</div>
            </div>
            <div class="af-card" style="padding: 16px; text-align: center; background: white; border: 1px solid var(--border);">
                <div style="font-size: 24px; font-weight: 900; color: #10b981;"><?= $ready ?></div>
                <div style="font-size: 11px; font-weight: 600; color: var(--ink-muted); margin-top: 4px;">Ready for Pickup</div>
            </div>
            <div class="af-card" style="padding: 16px; text-align: center; background: white; border: 1px solid var(--border);">
                <div style="font-size: 24px; font-weight: 900; color: #64748b;"><?= $released ?></div>
                <div style="font-size: 11px; font-weight: 600; color: var(--ink-muted); margin-top: 4px;">Released</div>
            </div>
        </div>

        <!-- Requests Grid -->
        <div style="display: flex; flex-direction: column; gap: 16px;">
            <?php foreach ($requests as $req): ?>
                <?php
                    $statusColor = '#64748b'; $statusBg = '#f1f5f9';
                    if ($req['status'] === 'Pending')            { $statusColor = '#92400e'; $statusBg = '#fef3c7'; }
                    if ($req['status'] === 'Processing')         { $statusColor = '#1e40af'; $statusBg = '#dbeafe'; }
                    if ($req['status'] === 'Ready for Pickup')   { $statusColor = '#065f46'; $statusBg = '#dcfce7'; }
                    if ($req['status'] === 'Released')           { $statusColor = '#374151'; $statusBg = '#e5e7eb'; }
                    if ($req['status'] === 'Rejected')           { $statusColor = '#991b1b'; $statusBg = '#fef2f2'; }
                    if ($req['status'] === 'Cancelled')          { $statusColor = '#7f1d1d'; $statusBg = '#fee2e2'; }
                ?>
                <div class="af-card" style="padding: 0; overflow: hidden; background: white; border: 1px solid var(--border); box-shadow: var(--shadow-sm); border-radius: 12px;">
                    <!-- Card Header -->
                    <div class="cert-header">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div style="width: 38px; height: 38px; border-radius: 10px; background: rgba(139, 92, 246, 0.1); color: #8b5cf6; display: flex; align-items: center; justify-content: center; font-size: 16px;">
                                <i class="fas fa-file-signature"></i>
                            </div>
                            <div>
                                <div style="font-weight: 800; font-size: 14px; color: var(--ink);"><?= esc($req['certificate_type']) ?></div>
                                <div style="font-size: 11px; color: var(--ink-muted);">Requested <?= date('M d, Y h:i A', strtotime($req['created_at'])) ?></div>
                            </div>
                        </div>
                        <div class="cert-header-actions">
                            <span style="font-size: 11px; font-weight: 700; background: <?= $statusBg ?>; color: <?= $statusColor ?>; padding: 4px 12px; border-radius: 20px; white-space: nowrap;">
                                <?= esc($req['status']) ?>
                            </span>
                            <?php if ($req['status'] === 'Pending'): ?>
                                <form action="<?= base_url('portal/certificates/cancel/' . $req['id']) ?>" method="POST" style="margin: 0;" onsubmit="return confirm('Are you sure you want to cancel this certificate request?');">
                                    <?= csrf_field() ?>
                                    <button type="submit" style="background: none; border: 1px solid #ef4444; color: #ef4444; padding: 3px 10px; border-radius: 6px; font-size: 11px; font-weight: 600; cursor: pointer; transition: all 0.2s;">
                                        Cancel Request
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Card Body -->
                    <div style="padding: 16px 20px;">

                        <!-- Status Stepper -->
                        <?php
                            $steps = ['Pending', 'Processing', 'Ready for Pickup', 'Released'];
                            $isRejected = in_array($req['status'], ['Rejected', 'Cancelled']);
                            $currentStep = 0;
                            if (!$isRejected) {
                                foreach ($steps as $i => $s) {
                                    if ($req['status'] === $s) $currentStep = $i;
                                    if ($req['status'] === 'Approved') $currentStep = 2; // treated as Ready
                                }
                            }
                        ?>
                        <?php if (!$isRejected): ?>
                        <div style="display:flex;align-items:center;margin-bottom:16px;gap:0">
                            <?php foreach ($steps as $i => $step): ?>
                                <?php $done = $i <= $currentStep; ?>
                                <div style="display:flex;align-items:center;flex:1">
                                    <div style="display:flex;flex-direction:column;align-items:center;gap:4px;min-width:0">
                                        <div style="width:28px;height:28px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;flex-shrink:0;background:<?= $done ? '#8b5cf6' : '#e2e8f0' ?>;color:<?= $done ? 'white' : '#94a3b8' ?>">
                                            <?php if ($done): ?><i class="fas fa-check" style="font-size:10px"></i><?php else: ?><?= $i+1 ?><?php endif; ?>
                                        </div>
                                        <div style="font-size:9px;font-weight:700;color:<?= $done ? '#8b5cf6' : '#94a3b8' ?>;text-align:center;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:64px"><?= $step ?></div>
                                    </div>
                                    <?php if ($i < count($steps)-1): ?>
                                    <div style="flex:1;height:2px;background:<?= $i < $currentStep ? '#8b5cf6' : '#e2e8f0' ?>;margin:0 4px;margin-bottom:16px"></div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>

                        <div style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--ink-muted); letter-spacing: 0.5px; margin-bottom: 4px;">Purpose</div>
                        <div style="font-size: 13px; color: var(--ink); line-height: 1.5;"><?= esc($req['purpose']) ?></div>

                        <?php if ($req['remarks']): ?>
                            <div style="margin-top: 12px; font-size: 12px; color: #64748b; background: #f8fafc; padding: 10px 14px; border-radius: 8px; border: 1px solid #e2e8f0;">
                                <strong>Remarks:</strong> <?= esc($req['remarks']) ?>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($req['rejection_note']) && $req['status'] === 'Rejected'): ?>
                            <div style="margin-top: 12px; font-size: 12px; color: #ef4444; background: #fef2f2; padding: 10px 14px; border-radius: 8px; border: 1px solid #fecaca;">
                                <strong>Rejection Reason:</strong> <?= esc($req['rejection_note']) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>

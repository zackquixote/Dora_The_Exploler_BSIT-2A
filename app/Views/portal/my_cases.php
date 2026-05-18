<?= $this->extend('portal/layout') ?>
<?= $this->section('content') ?>

<div class="af-container">
    <div class="af-header">
        <h1 class="af-title">My Cases</h1>
        <p class="af-subtitle">Track the status of your filed incident reports and blotter cases.</p>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success af-alert" style="display: flex; align-items: center; gap: 10px; padding: 16px 20px; border-radius: 12px; margin-bottom: 24px; background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; font-weight: 600;">
            <i class="fas fa-check-circle"></i> <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>

    <?php if (empty($cases)): ?>
        <div class="af-card" style="text-align: center; padding: 60px 40px;">
            <div style="font-size: 56px; color: rgba(15,23,42,0.08); margin-bottom: 20px;">
                <i class="fas fa-folder-open"></i>
            </div>
            <h3 style="font-weight: 800; color: var(--ink); margin-bottom: 8px;">No Cases Filed Yet</h3>
            <p style="color: var(--ink-muted); font-size: 14px; margin-bottom: 24px;">You haven't filed any incident reports. If you need to report an incident, click below.</p>
            <a href="<?= base_url('portal/file-blotter') ?>" class="af-btn">
                <i class="fas fa-plus-circle"></i> File an Incident Report
            </a>
        </div>
    <?php else: ?>
        <!-- Summary Stats -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 12px; margin-bottom: 28px;">
            <?php
                $total = count($cases);
                $pending = count(array_filter($cases, fn($c) => $c['status'] === 'Pending'));
                $ongoing = count(array_filter($cases, fn($c) => in_array($c['status'], ['Under Investigation', 'For Hearing', 'Mediation'])));
                $resolved = count(array_filter($cases, fn($c) => in_array($c['status'], ['Resolved', 'Settled', 'Dismissed'])));
            ?>
            <div class="af-card" style="padding: 20px; text-align: center;">
                <div style="font-size: 28px; font-weight: 900; color: var(--ink);"><?= $total ?></div>
                <div style="font-size: 12px; font-weight: 600; color: var(--ink-muted); margin-top: 4px;">Total Cases</div>
            </div>
            <div class="af-card" style="padding: 20px; text-align: center;">
                <div style="font-size: 28px; font-weight: 900; color: #f59e0b;"><?= $pending ?></div>
                <div style="font-size: 12px; font-weight: 600; color: var(--ink-muted); margin-top: 4px;">Pending</div>
            </div>
            <div class="af-card" style="padding: 20px; text-align: center;">
                <div style="font-size: 28px; font-weight: 900; color: #3b82f6;"><?= $ongoing ?></div>
                <div style="font-size: 12px; font-weight: 600; color: var(--ink-muted); margin-top: 4px;">Ongoing</div>
            </div>
            <div class="af-card" style="padding: 20px; text-align: center;">
                <div style="font-size: 28px; font-weight: 900; color: #10b981;"><?= $resolved ?></div>
                <div style="font-size: 12px; font-weight: 600; color: var(--ink-muted); margin-top: 4px;">Resolved</div>
            </div>
        </div>

        <!-- Case Cards -->
        <div style="display: flex; flex-direction: column; gap: 16px;">
            <?php foreach ($cases as $case): ?>
                <?php
                    $statusColor = '#64748b'; $statusBg = '#f1f5f9';
                    if ($case['status'] === 'Pending')    { $statusColor = '#92400e'; $statusBg = '#fef3c7'; }
                    if (in_array($case['status'], ['Under Investigation', 'For Hearing', 'Mediation'])) { $statusColor = '#1e40af'; $statusBg = '#dbeafe'; }
                    if (in_array($case['status'], ['Resolved', 'Settled'])) { $statusColor = '#166534'; $statusBg = '#dcfce7'; }
                    if ($case['status'] === 'Dismissed')  { $statusColor = '#991b1b'; $statusBg = '#fef2f2'; }
                ?>
                <div class="af-card" style="padding: 0; overflow: hidden;">
                    <!-- Card Header -->
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 18px 24px; background: rgba(15,23,42,0.02); border-bottom: 1px solid var(--border);">
                        <div style="display: flex; align-items: center; gap: 14px;">
                            <div style="width: 42px; height: 42px; border-radius: 12px; background: rgba(245,158,11,0.12); color: #f59e0b; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                                <i class="fas fa-gavel"></i>
                            </div>
                            <div>
                                <div style="font-weight: 800; font-size: 15px; color: var(--ink);"><?= esc($case['case_number']) ?></div>
                                <div style="font-size: 12px; color: var(--ink-muted);">Filed <?= date('M d, Y', strtotime($case['created_at'])) ?></div>
                            </div>
                        </div>
                        <span style="font-size: 12px; font-weight: 700; background: <?= $statusBg ?>; color: <?= $statusColor ?>; padding: 5px 14px; border-radius: 20px;">
                            <?= esc($case['status']) ?>
                        </span>
                    </div>

                    <!-- Card Body -->
                    <div style="padding: 20px 24px;">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                            <div>
                                <div style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--ink-muted); letter-spacing: 0.5px; margin-bottom: 4px;">Incident Type</div>
                                <div style="font-size: 14px; font-weight: 600; color: var(--ink);"><?= esc($case['incident_type']) ?></div>
                            </div>
                            <div>
                                <div style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--ink-muted); letter-spacing: 0.5px; margin-bottom: 4px;">Respondent</div>
                                <div style="font-size: 14px; font-weight: 600; color: var(--ink);"><?= esc($case['respondent_name']) ?></div>
                            </div>
                            <div>
                                <div style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--ink-muted); letter-spacing: 0.5px; margin-bottom: 4px;">Date of Incident</div>
                                <div style="font-size: 14px; font-weight: 600; color: var(--ink);"><?= date('M d, Y h:i A', strtotime($case['incident_date'])) ?></div>
                            </div>
                            <div>
                                <div style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--ink-muted); letter-spacing: 0.5px; margin-bottom: 4px;">Location</div>
                                <div style="font-size: 14px; font-weight: 600; color: var(--ink);"><?= esc($case['incident_location']) ?></div>
                            </div>
                        </div>

                        <?php if (!empty($case['details'])): ?>
                            <div style="margin-top: 16px; padding: 14px; background: rgba(15,23,42,0.02); border-radius: 10px; border: 1px solid var(--border);">
                                <div style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--ink-muted); letter-spacing: 0.5px; margin-bottom: 6px;">Description</div>
                                <div style="font-size: 13px; color: var(--ink); line-height: 1.6;"><?= esc($case['details']) ?></div>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($case['hearing_count']) && $case['hearing_count'] > 0): ?>
                            <div style="margin-top: 14px; display: flex; align-items: center; gap: 8px; font-size: 13px; color: #3b82f6; font-weight: 600;">
                                <i class="fas fa-calendar-check"></i>
                                <?= $case['hearing_count'] ?> hearing<?= $case['hearing_count'] > 1 ? 's' : '' ?> scheduled
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>

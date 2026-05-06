<?php $this->extend('theme/admin/template') ?>
<?= $this->section('content') ?>

<div class="bmis-content">

    <!-- ROW 1 · PRIMARY STAT CARDS -->
    <div class="ds-grid-4">
        <div class="ds-stat">
            <div class="ds-stat-stripe str-blue"></div>
            <div class="ds-stat-top">
                <div class="ds-stat-icon ic-blue"><i class="fas fa-users"></i></div>
                <span class="ds-stat-trend t-neutral">—</span>
            </div>
            <div class="ds-stat-num"><?= $totalResidents ?? 0 ?></div>
            <div class="ds-stat-label">Total Residents</div>
            <a href="<?= base_url('resident') ?>" class="ds-stat-footer ft-blue">
                <i class="fas fa-arrow-right"></i> Manage Residents
            </a>
        </div>

        <div class="ds-stat">
            <div class="ds-stat-stripe str-teal"></div>
            <div class="ds-stat-top">
                <div class="ds-stat-icon ic-teal"><i class="fas fa-home"></i></div>
                <span class="ds-stat-trend t-neutral">—</span>
            </div>
            <div class="ds-stat-num"><?= $totalHouseholds ?? 0 ?></div>
            <div class="ds-stat-label">Households</div>
            <a href="<?= base_url('households') ?>" class="ds-stat-footer ft-teal">
                <i class="fas fa-arrow-right"></i> View Households
            </a>
        </div>

        <div class="ds-stat">
            <div class="ds-stat-stripe str-violet"></div>
            <div class="ds-stat-top">
                <div class="ds-stat-icon ic-violet"><i class="fas fa-file-invoice"></i></div>
                <span class="ds-stat-trend t-neutral"><?= $dailyCerts ?? 0 ?> today</span>
            </div>
            <div class="ds-stat-num"><?= $pendingCerts ?? 0 ?></div>
            <div class="ds-stat-label">Certificates</div>
            <a href="<?= base_url('certificate') ?>" class="ds-stat-footer ft-violet">
                <i class="fas fa-arrow-right"></i> View Certificates
            </a>
        </div>

        <div class="ds-stat">
            <div class="ds-stat-stripe str-rose"></div>
            <div class="ds-stat-top">
                <div class="ds-stat-icon ic-rose"><i class="fas fa-exclamation-triangle"></i></div>
                <span class="ds-stat-trend t-neutral">—</span>
            </div>
            <div class="ds-stat-num"><?= $blotterCount ?? 0 ?></div>
            <div class="ds-stat-label">Blotter Cases</div>
            <a href="<?= base_url('blotter') ?>" class="ds-stat-footer ft-rose">
                <i class="fas fa-arrow-right"></i> View Records
            </a>
        </div>
    </div>

    <!-- ROW 2 · CASE OVERVIEW -->
    <div class="ds-section-label">Case Overview</div>
    <div class="ds-grid-4">
        <div class="ds-mini">
            <div class="ds-mini-icon ic-amber"><i class="fas fa-folder-open"></i></div>
            <div><div class="ds-mini-num"><?= $openCases ?? 0 ?></div><div class="ds-mini-label">Open Cases</div></div>
        </div>
        <div class="ds-mini">
            <div class="ds-mini-icon ic-blue"><i class="fas fa-gavel"></i></div>
            <div><div class="ds-mini-num"><?= $hearingsToday ?? 0 ?></div><div class="ds-mini-label">Hearings Today</div></div>
        </div>
        <div class="ds-mini">
            <div class="ds-mini-icon ic-teal"><i class="fas fa-check-circle"></i></div>
            <div><div class="ds-mini-num"><?= $settledThisMonth ?? 0 ?></div><div class="ds-mini-label">Settled (Month)</div></div>
        </div>
        <div class="ds-mini">
            <div class="ds-mini-icon ic-rose"><i class="fas fa-file-alt"></i></div>
            <div><div class="ds-mini-num"><?= $blotterCount ?? 0 ?></div><div class="ds-mini-label">Total Cases</div></div>
        </div>
    </div>

    <!-- ROW 3 · POPULATION BREAKDOWN -->
    <div class="ds-section-label">Population Breakdown</div>
    <div class="ds-grid-4">
        <?php
        $pop = max(1, $totalResidents ?? 1);
        $popCards = [
            ['icon'=>'fa-vote-yea','cls'=>'ic-blue','val'=>$totalVoters??0,'label'=>'Voters','pct'=>round((($totalVoters??0)/$pop)*100),'bar'=>'#185FA5'],
            ['icon'=>'fa-wheelchair','cls'=>'ic-violet','val'=>$totalPwd??0,'label'=>'PWDs','pct'=>round((($totalPwd??0)/$pop)*100),'bar'=>'#534AB7'],
            ['icon'=>'fa-user-clock','cls'=>'ic-teal','val'=>$totalSenior??0,'label'=>'Seniors','pct'=>round((($totalSenior??0)/$pop)*100),'bar'=>'#1D9E75'],
            ['icon'=>'fa-chart-line','cls'=>'ic-amber','val'=>$avgPerHousehold??0,'label'=>'Avg/HH','pct'=>60,'bar'=>'#854F0B'],
        ];
        foreach ($popCards as $pc): ?>
        <div class="ds-mini">
            <div class="ds-mini-icon <?= $pc['cls'] ?>"><i class="fas <?= $pc['icon'] ?>"></i></div>
            <div class="ds-progress-wrap">
                <div class="ds-progress-top">
                    <span class="ds-mini-label"><?= $pc['label'] ?></span>
                    <span class="ds-mini-num" style="font-size:16px"><?= $pc['val'] ?></span>
                </div>
                <div class="ds-progress-bar-track">
                    <div class="ds-progress-bar-fill" style="width:<?= $pc['pct'] ?>%;background:<?= $pc['bar'] ?>"></div>
                </div>
                <div class="ds-progress-sub"><?= $pc['pct'] ?>% of population</div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- ROW 4 · USER & GENDER -->
    <div class="ds-section-label">User &amp; Gender Stats</div>
    <div class="ds-grid-4">
        <div class="ds-mini">
            <div class="ds-mini-icon ic-rose"><i class="fas fa-user-shield"></i></div>
            <div><div class="ds-mini-num"><?= $totalAdmins ?? 0 ?></div><div class="ds-mini-label">Admins</div></div>
        </div>
        <div class="ds-mini">
            <div class="ds-mini-icon" style="background:var(--bg);color:var(--ink-muted)"><i class="fas fa-user-tie"></i></div>
            <div><div class="ds-mini-num"><?= $totalStaff ?? 0 ?></div><div class="ds-mini-label">Staff</div></div>
        </div>
        <div class="ds-mini">
            <div class="ds-mini-icon ic-blue"><i class="fas fa-male"></i></div>
            <div class="ds-progress-wrap">
                <div class="ds-progress-top">
                    <span class="ds-mini-label">Males</span>
                    <span class="ds-mini-num" style="font-size:16px"><?= $totalMale ?? 0 ?></span>
                </div>
                <div class="ds-progress-bar-track">
                    <div class="ds-progress-bar-fill" style="width:<?= $pop>0?round((($totalMale??0)/$pop)*100):0 ?>%;background:#185FA5"></div>
                </div>
                <div class="ds-progress-sub"><?= $pop>0?round((($totalMale??0)/$pop)*100):0 ?>% of pop.</div>
            </div>
        </div>
        <div class="ds-mini">
            <div class="ds-mini-icon ic-rose"><i class="fas fa-female"></i></div>
            <div class="ds-progress-wrap">
                <div class="ds-progress-top">
                    <span class="ds-mini-label">Females</span>
                    <span class="ds-mini-num" style="font-size:16px"><?= $totalFemale ?? 0 ?></span>
                </div>
                <div class="ds-progress-bar-track">
                    <div class="ds-progress-bar-fill" style="width:<?= $pop>0?round((($totalFemale??0)/$pop)*100):0 ?>%;background:#A32D2D"></div>
                </div>
                <div class="ds-progress-sub"><?= $pop>0?round((($totalFemale??0)/$pop)*100):0 ?>% of pop.</div>
            </div>
        </div>
    </div>

    <!-- ROW 5 · CHARTS -->
    <div class="ds-section-label">Analytics</div>
    <div class="ds-grid-3">
        <div class="ds-card">
            <div class="ds-card-head">
                <div class="ds-card-title"><i class="fas fa-venus-mars"></i> Gender Distribution</div>
            </div>
            <div class="ds-card-body" style="text-align:center">
                <div class="ds-chart-wrap" style="height:180px"><canvas id="genderChart"></canvas></div>
                <div style="display:flex;justify-content:center;gap:16px;margin-top:10px">
                    <span style="display:flex;align-items:center;gap:5px;font-size:11px;color:var(--ink-muted)">
                        <span style="width:8px;height:8px;border-radius:2px;background:#185FA5;display:inline-block"></span>
                        Male: <strong style="color:var(--ink)"><?= $totalMale ?? 0 ?></strong>
                    </span>
                    <span style="display:flex;align-items:center;gap:5px;font-size:11px;color:var(--ink-muted)">
                        <span style="width:8px;height:8px;border-radius:2px;background:#A32D2D;display:inline-block"></span>
                        Female: <strong style="color:var(--ink)"><?= $totalFemale ?? 0 ?></strong>
                    </span>
                </div>
            </div>
        </div>

        <div class="ds-card">
            <div class="ds-card-head">
                <div class="ds-card-title"><i class="fas fa-chart-bar"></i> Population by Purok</div>
                <span class="ds-badge ds-badge-blue"><?= $totalResidents ?? 0 ?> total</span>
            </div>
            <div class="ds-card-body">
                <div class="ds-chart-wrap" style="height:190px"><canvas id="purokChart"></canvas></div>
            </div>
        </div>

        <div class="ds-card">
            <div class="ds-card-head">
                <div class="ds-card-title"><i class="fas fa-heart"></i> Civil Status</div>
            </div>
            <div class="ds-card-body" style="text-align:center">
                <div class="ds-chart-wrap" style="height:150px"><canvas id="civilStatusChart"></canvas></div>
                <div style="margin-top:10px;display:flex;flex-wrap:wrap;justify-content:center;gap:4px 10px">
                    <?php if (!empty($civilStatusData)): foreach ($civilStatusData as $cs): ?>
                    <span style="font-size:10px;color:var(--ink-muted)"><?= esc($cs['civil_status'] ?? 'N/A') ?>: <strong style="color:var(--ink)"><?= $cs['count'] ?></strong></span>
                    <?php endforeach; endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- ROW 6 · TABLES -->
    <div class="ds-section-label">Records</div>
    <div class="ds-grid-2">
        <div class="ds-card">
            <div class="ds-card-head">
                <div class="ds-card-title"><i class="fas fa-calendar-alt"></i> Upcoming Hearings</div>
                <span class="ds-badge ds-badge-amber"><?= count($upcomingHearings ?? []) ?> scheduled</span>
            </div>
            <div class="ds-card-body p0">
                <table class="ds-table">
                    <thead><tr><th>Date</th><th>Case No.</th><th>Venue</th></tr></thead>
                    <tbody>
                        <?php if (!empty($upcomingHearings)): foreach ($upcomingHearings as $h): ?>
                        <tr>
                            <td><strong><?= date('M d', strtotime($h['hearing_date'])) ?></strong></td>
                            <td class="mono"><?php $case = (new \App\Models\BlotterModel())->find($h['blotter_id']); echo esc($case['case_number'] ?? '--'); ?></td>
                            <td><?= esc($h['venue'] ?? 'Main Hall') ?></td>
                        </tr>
                        <?php endforeach; else: ?>
                        <tr><td colspan="3" style="text-align:center;padding:24px;color:var(--ink-soft)">No hearings scheduled</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="ds-card">
            <div class="ds-card-head">
                <div class="ds-card-title"><i class="fas fa-shield-alt"></i> Recent Cases</div>
                <a href="<?= base_url('blotter') ?>" style="font-size:11px;font-weight:700;color:var(--c-blue);text-decoration:none">View All</a>
            </div>
            <div class="ds-card-body p0">
                <table class="ds-table">
                    <thead><tr><th>Case No.</th><th>Type</th><th>Status</th></tr></thead>
                    <tbody>
                        <?php if (!empty($recentCases)): foreach ($recentCases as $rc):
                            $st = $rc['status'];
                            $bc = 'ds-badge-gray';
                            if ($st=='Pending') $bc='ds-badge-amber';
                            elseif (in_array($st,['Investigating','Ongoing','For Hearing'])) $bc='ds-badge-blue';
                            elseif ($st=='Settled') $bc='ds-badge-teal';
                            elseif ($st=='Dismissed') $bc='ds-badge-gray';
                            elseif ($st=='Referred') $bc='ds-badge-violet';
                            elseif ($st=='Unsettled') $bc='ds-badge-rose';
                        ?>
                        <tr>
                            <td class="mono"><strong><?= esc($rc['case_number']) ?></strong></td>
                            <td><?= esc($rc['incident_type']) ?></td>
                            <td><span class="ds-badge <?= $bc ?>"><?= esc($st) ?></span></td>
                        </tr>
                        <?php endforeach; else: ?>
                        <tr><td colspan="3" style="text-align:center;padding:24px;color:var(--ink-soft)">No cases found</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ROW 7 · ACTIVITY + QUICK ACTIONS -->
    <div class="ds-section-label">Activity</div>
    <div class="ds-grid-activity">
        <div class="ds-card">
            <div class="ds-card-head">
                <div class="ds-card-title"><i class="fas fa-history"></i> Recent Activity</div>
                <a href="<?= base_url('logs') ?>" style="font-size:11px;font-weight:700;color:var(--c-blue);text-decoration:none">View All</a>
            </div>
            <div class="ds-card-body">
                <div class="ds-activity-feed">
                    <?php if (empty($recentLogs)): ?>
                        <div style="text-align:center;padding:24px;color:var(--ink-soft)"><i class="fas fa-history" style="font-size:20px;opacity:.3;margin-bottom:8px;display:block"></i>No recent activity</div>
                    <?php else: foreach ($recentLogs as $log):
                        $a = strtolower($log['ACTION'] ?? '');
                        $ic='ds-ai-view'; $ii='fa-eye';
                        if (strpos($a,'delete')!==false||strpos($a,'remove')!==false){$ic='ds-ai-delete';$ii='fa-trash-alt';}
                        elseif (strpos($a,'edit')!==false||strpos($a,'update')!==false){$ic='ds-ai-edit';$ii='fa-edit';}
                        elseif (strpos($a,'create')!==false||strpos($a,'add')!==false){$ic='ds-ai-create';$ii='fa-plus-circle';}
                        elseif (strpos($a,'print')!==false){$ic='ds-ai-print';$ii='fa-print';}
                        elseif (strpos($a,'certif')!==false){$ic='ds-ai-cert';$ii='fa-file-alt';}
                    ?>
                    <div class="ds-activity-item">
                        <div class="ds-activity-icon <?= $ic ?>"><i class="fas <?= $ii ?>"></i></div>
                        <div>
                            <div class="ds-activity-action"><?= esc($log['ACTION']) ?></div>
                            <div class="ds-activity-meta">by <strong><?= esc($log['USER_NAME'] ?? 'System') ?></strong> · <?= time_elapsed_string($log['DATELOG'].' '.$log['TIMELOG']) ?></div>
                        </div>
                    </div>
                    <?php endforeach; endif; ?>
                </div>
            </div>
        </div>

        <div class="ds-card">
            <div class="ds-card-head">
                <div class="ds-card-title"><i class="fas fa-bolt"></i> Quick Actions</div>
            </div>
            <div class="ds-card-body">
                <div class="ds-quick-grid">
                    <a href="<?= base_url('resident/create') ?>" class="ds-quick-btn qb-blue"><i class="fas fa-user-plus"></i> Add Resident</a>
                    <a href="<?= base_url('resident') ?>" class="ds-quick-btn qb-teal"><i class="fas fa-users"></i> View Residents</a>
                    <a href="<?= base_url('households') ?>" class="ds-quick-btn qb-slate"><i class="fas fa-home"></i> Households</a>
                    <a href="<?= base_url('certificate/create') ?>" class="ds-quick-btn qb-violet"><i class="fas fa-file-alt"></i> Certificates</a>
                    <a href="<?= base_url('admin/users/create') ?>" class="ds-quick-btn qb-amber"><i class="fas fa-users-cog"></i> Manage Users</a>
                    <a href="<?= base_url('blotter/create') ?>" class="ds-quick-btn qb-rose"><i class="fas fa-gavel"></i> Blotter</a>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Chart Data -->
<script>
const DASHBOARD_DATA = {
    baseUrl:        "<?= base_url() ?>",
    csrfTokenName:  "<?= csrf_token() ?>",
    csrfTokenValue: "<?= csrf_hash() ?>",
    gender:  { male: <?= (int)($totalMale??0) ?>, female: <?= (int)($totalFemale??0) ?> },
    purokLabels: <?= json_encode(array_column($purokCounts ?? [], 'sitio')) ?>,
    purokValues: <?= json_encode(array_column($purokCounts ?? [], 'count')) ?>,
    civilLabels: <?= json_encode(array_column($civilStatusData ?? [], 'civil_status')) ?>,
    civilValues: <?= json_encode(array_column($civilStatusData ?? [], 'count')) ?>
};
</script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script src="<?= base_url('js/dashboard/admin.js') ?>"></script>

<?= $this->endSection() ?>
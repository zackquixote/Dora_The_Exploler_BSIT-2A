<?php $this->extend('theme/template') ?>
<?= $this->section('content') ?>

<div class="bmis-content">

    <!-- PREMIUM WELCOME BANNER -->
    <div class="ds-welcome-banner">
        <div>
            <h2><span id="dynamicGreeting">Welcome</span>, <strong>Staff</strong>!</h2>
            <p style="font-size: 14.5px; opacity: 0.9; margin-top: 4px;">Monitor key metrics, resolve cases, and assist the community efficiently.</p>
        </div>
        <div class="ds-datetime" style="display: flex; flex-direction: column; align-items: flex-end; gap: 6px;">
            <div style="font-size: 12px; opacity: 0.85; text-transform: uppercase; letter-spacing: 1px; font-family: var(--font); font-weight: 600;">
                <i class="fas fa-calendar-day" style="margin-right: 4px;"></i> <span id="liveDate"><?= date('l, F j, Y') ?></span>
            </div>
            <div style="font-size: 28px; font-weight: 700; color: #fff; line-height: 1; letter-spacing: -0.5px;">
                <i class="far fa-clock" style="font-size: 22px; opacity: 0.7; margin-right: 6px;"></i><span id="liveTime"><?= date('h:i:s A') ?></span>
            </div>
        </div>
    </div>

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
            <a href="<?= base_url('resident') ?>" class="ds-stat-footer ft-blue"><i class="fas fa-arrow-right"></i> Manage</a>
        </div>
        <div class="ds-stat">
            <div class="ds-stat-stripe str-teal"></div>
            <div class="ds-stat-top"><div class="ds-stat-icon ic-teal"><i class="fas fa-home"></i></div></div>
            <div class="ds-stat-num"><?= $totalHouseholds ?? 0 ?></div>
            <div class="ds-stat-label">Households</div>
            <a href="<?= base_url('households') ?>" class="ds-stat-footer ft-teal"><i class="fas fa-arrow-right"></i> View</a>
        </div>
        <div class="ds-stat">
            <div class="ds-stat-stripe str-violet"></div>
            <div class="ds-stat-top"><div class="ds-stat-icon ic-violet"><i class="fas fa-file-invoice"></i></div><span class="ds-stat-trend t-neutral"><?= $dailyCerts ?? 0 ?> today</span></div>
            <div class="ds-stat-num"><?= $pendingCerts ?? 0 ?></div>
            <div class="ds-stat-label">Certificates</div>
            <a href="<?= base_url('certificate') ?>" class="ds-stat-footer ft-violet"><i class="fas fa-arrow-right"></i> View</a>
        </div>
        <div class="ds-stat">
            <div class="ds-stat-stripe str-rose"></div>
            <div class="ds-stat-top"><div class="ds-stat-icon ic-rose"><i class="fas fa-exclamation-triangle"></i></div></div>
            <div class="ds-stat-num"><?= $blotterCount ?? 0 ?></div>
            <div class="ds-stat-label">Blotter Cases</div>
            <a href="<?= base_url('blotter') ?>" class="ds-stat-footer ft-rose"><i class="fas fa-arrow-right"></i> Records</a>
        </div>
    </div>

    <!-- ROW 2 · CASE OVERVIEW -->
    <div class="ds-section-label">Case Overview</div>
    <div class="ds-grid-4">
        <div class="ds-mini"><div class="ds-mini-icon ic-amber"><i class="fas fa-folder-open"></i></div><div><div class="ds-mini-num"><?= $openCases ?? 0 ?></div><div class="ds-mini-label">Open Cases</div></div></div>
        <div class="ds-mini"><div class="ds-mini-icon ic-blue"><i class="fas fa-gavel"></i></div><div><div class="ds-mini-num"><?= $hearingsToday ?? 0 ?></div><div class="ds-mini-label">Hearings Today</div></div></div>
        <div class="ds-mini"><div class="ds-mini-icon ic-teal"><i class="fas fa-check-circle"></i></div><div><div class="ds-mini-num"><?= $settledThisMonth ?? 0 ?></div><div class="ds-mini-label">Settled (Month)</div></div></div>
        <div class="ds-mini"><div class="ds-mini-icon ic-rose"><i class="fas fa-file-alt"></i></div><div><div class="ds-mini-num"><?= $blotterCount ?? 0 ?></div><div class="ds-mini-label">Total Cases</div></div></div>
    </div>

    <!-- ROW 3 · POPULATION -->
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
                <div class="ds-progress-top"><span class="ds-mini-label"><?= $pc['label'] ?></span><span class="ds-mini-num" style="font-size:16px"><?= $pc['val'] ?></span></div>
                <div class="ds-progress-bar-track"><div class="ds-progress-bar-fill" style="width:<?= $pc['pct'] ?>%;background:<?= $pc['bar'] ?>"></div></div>
                <div class="ds-progress-sub"><?= $pc['pct'] ?>% of population</div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- ROW 4 · CHART -->
    <div class="ds-section-label">Analytics</div>
    <div class="ds-card" style="margin-bottom:14px">
        <div class="ds-card-head">
            <div class="ds-card-title"><i class="fas fa-chart-bar"></i> Population by Purok</div>
            <span class="ds-badge ds-badge-blue"><?= $totalResidents ?? 0 ?> total</span>
        </div>
        <div class="ds-card-body"><div class="ds-chart-wrap" style="height:250px"><canvas id="purokChart"></canvas></div></div>
    </div>

    <!-- ROW 5 · TABLES -->
    <div class="ds-section-label">Records</div>
    <div class="ds-grid-2">
        <div class="ds-card">
            <div class="ds-card-head"><div class="ds-card-title"><i class="fas fa-calendar-alt"></i> Upcoming Hearings</div><span class="ds-badge ds-badge-amber"><?= count($upcomingHearings ?? []) ?></span></div>
            <div class="ds-card-body p0">
                <table class="ds-table"><thead><tr><th>Date</th><th>Case No.</th><th>Venue</th></tr></thead><tbody>
                    <?php if (!empty($upcomingHearings)): foreach ($upcomingHearings as $h): ?>
                    <tr><td><strong><?= date('M d', strtotime($h['hearing_date'])) ?></strong></td><td class="mono"><?php $case=(new \App\Models\BlotterModel())->find($h['blotter_id']); echo esc($case['case_number']??'--'); ?></td><td><?= esc($h['venue']??'Main Hall') ?></td></tr>
                    <?php endforeach; else: ?>
                    <tr><td colspan="3" style="text-align:center;padding:24px;color:var(--ink-soft)">No hearings scheduled</td></tr>
                    <?php endif; ?>
                </tbody></table>
            </div>
        </div>
        <div class="ds-card">
            <div class="ds-card-head"><div class="ds-card-title"><i class="fas fa-shield-alt"></i> Recent Cases</div><a href="<?= base_url('blotter') ?>" style="font-size:11px;font-weight:700;color:var(--c-blue);text-decoration:none">View All</a></div>
            <div class="ds-card-body p0">
                <table class="ds-table"><thead><tr><th>Case No.</th><th>Type</th><th>Status</th></tr></thead><tbody>
                    <?php if (!empty($recentCases)): foreach ($recentCases as $rc):
                        $st=$rc['status'];$bc='ds-badge-gray';
                        if($st=='Pending')$bc='ds-badge-amber';elseif(in_array($st,['Investigating','Ongoing','For Hearing']))$bc='ds-badge-blue';
                        elseif($st=='Settled')$bc='ds-badge-teal';elseif($st=='Dismissed')$bc='ds-badge-gray';
                        elseif($st=='Referred')$bc='ds-badge-violet';elseif($st=='Unsettled')$bc='ds-badge-rose'; ?>
                    <tr><td class="mono"><strong><?= esc($rc['case_number']) ?></strong></td><td><?= esc($rc['incident_type']) ?></td><td><span class="ds-badge <?= $bc ?>"><?= esc($st) ?></span></td></tr>
                    <?php endforeach; else: ?>
                    <tr><td colspan="3" style="text-align:center;padding:24px;color:var(--ink-soft)">No cases found</td></tr>
                    <?php endif; ?>
                </tbody></table>
            </div>
        </div>
    </div>

    <!-- ROW 6 · QUICK ACTIONS -->
    <div class="ds-section-label">Actions</div>
    <div class="ds-card">
        <div class="ds-card-head"><div class="ds-card-title"><i class="fas fa-bolt"></i> Quick Actions</div></div>
        <div class="ds-card-body">
            <div class="ds-quick-grid">
                <a href="<?= base_url('resident/create') ?>" class="ds-quick-btn qb-blue"><i class="fas fa-user-plus"></i> Add Resident</a>
                <a href="<?= base_url('resident') ?>" class="ds-quick-btn qb-teal"><i class="fas fa-users"></i> View Residents</a>
                <a href="<?= base_url('households') ?>" class="ds-quick-btn qb-slate"><i class="fas fa-home"></i> Households</a>
                <a href="<?= base_url('certificate/create') ?>" class="ds-quick-btn qb-violet"><i class="fas fa-file-alt"></i> Certificates</a>
                <a href="<?= base_url('blotter/create') ?>" class="ds-quick-btn qb-rose"><i class="fas fa-gavel"></i> Blotter</a>
                <a href="<?= base_url('certificate') ?>" class="ds-quick-btn qb-amber"><i class="fas fa-file-contract"></i> Issuance Log</a>
            </div>
        </div>
    </div>
</div>

<script>
const DASHBOARD_DATA = {
    baseUrl: "<?= base_url() ?>",
    csrfTokenName: "<?= csrf_token() ?>",
    csrfTokenValue: "<?= csrf_hash() ?>",
    purokLabels: <?= json_encode(array_column($purokCounts ?? [], 'sitio')) ?>,
    purokValues: <?= json_encode(array_column($purokCounts ?? [], 'count')) ?>
};

// Dynamic Greeting
var hr = new Date().getHours();
var greeting = "Good evening 🌙";
if (hr < 12) greeting = "Good morning 🌅";
else if (hr < 18) greeting = "Good afternoon ☀️";
var greetingEl = document.getElementById('dynamicGreeting');
if (greetingEl) greetingEl.innerText = greeting;

// Live Clock Logic for Welcome Banner
setInterval(function() {
    var now = new Date();
    var timeString = now.toLocaleTimeString('en-US', { hour12: true, hour: '2-digit', minute:'2-digit', second:'2-digit' });
    var timeEl = document.getElementById('liveTime');
    if (timeEl) timeEl.innerText = timeString;
}, 1000);
</script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script src="<?= base_url('js/dashboard/admin.js') ?>"></script>

<?= $this->endSection() ?>
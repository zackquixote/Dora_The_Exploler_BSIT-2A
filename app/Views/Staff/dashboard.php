<?= $this->extend('theme/template') ?>
<?= $this->section('content') ?>

<div class="bmis-content">

<style>
/* Premium Welcome Banner Upgrades */
.welcome-banner-premium {
    background: linear-gradient(135deg, #1b3a6b 0%, #0c1c38 100%) !important;
    border: 1px solid rgba(255, 255, 255, 0.08) !important;
    box-shadow: 0 10px 30px rgba(12, 28, 56, 0.15), inset 0 1px 0 rgba(255, 255, 255, 0.1) !important;
    position: relative;
    overflow: hidden;
    padding: 24px 32px !important;
    border-radius: 16px !important;
    display: flex;
    align-items: center;
    justify-content: space-between;
    transition: all 0.3s ease;
}

/* Background animated light blobs */
.welcome-banner-premium .banner-blob {
    position: absolute;
    border-radius: 50%;
    filter: blur(40px);
    opacity: 0.15;
    z-index: 1;
    pointer-events: none;
    animation: pulseBlob 8s infinite alternate;
}

.welcome-banner-premium .blob-1 {
    width: 150px;
    height: 150px;
    background: #0f766e;
    top: -50px;
    right: 150px;
}

.welcome-banner-premium .blob-2 {
    width: 200px;
    height: 200px;
    background: #3b82f6;
    bottom: -80px;
    left: 200px;
    animation-delay: 4s;
}

@keyframes pulseBlob {
    0% { transform: scale(1) translate(0, 0); opacity: 0.12; }
    100% { transform: scale(1.3) translate(20px, 10px); opacity: 0.20; }
}

/* Dynamic Greeting Icon */
.greeting-icon-wrapper {
    width: 54px;
    height: 54px;
    border-radius: 14px;
    background: rgba(255, 255, 255, 0.06);
    border: 1px solid rgba(255, 255, 255, 0.12);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: #fff;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    transition: all 0.3s ease;
    animation: floatIcon 4s ease-in-out infinite;
}

.greeting-icon-wrapper.morning {
    color: #f59e0b;
    background: rgba(245, 158, 11, 0.1);
    border-color: rgba(245, 158, 11, 0.25);
    box-shadow: 0 8px 20px rgba(245, 158, 11, 0.15);
}

.greeting-icon-wrapper.afternoon {
    color: #fb923c;
    background: rgba(251, 146, 60, 0.1);
    border-color: rgba(251, 146, 60, 0.25);
    box-shadow: 0 8px 20px rgba(251, 146, 60, 0.15);
}

.greeting-icon-wrapper.evening {
    color: #a5b4fc;
    background: rgba(165, 180, 252, 0.1);
    border-color: rgba(165, 180, 252, 0.25);
    box-shadow: 0 8px 20px rgba(165, 180, 252, 0.15);
}

@keyframes floatIcon {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-4px); }
}

/* User Role Badge */
.user-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 10px;
    border-radius: 20px;
    background: rgba(15, 118, 110, 0.15);
    border: 1px solid rgba(15, 118, 110, 0.3);
    color: var(--c-teal-light);
    font-size: 10px;
    font-weight: 800;
    letter-spacing: 1px;
    text-transform: uppercase;
}

/* Glassmorphism Datetime Card */
.glass-datetime-card {
    background: rgba(255, 255, 255, 0.03) !important;
    backdrop-filter: blur(10px) !important;
    -webkit-backdrop-filter: blur(10px) !important;
    border: 1px solid rgba(255, 255, 255, 0.08) !important;
    padding: 16px 22px !important;
    border-radius: 14px !important;
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 6px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
}

.live-clock-display {
    font-size: 26px;
    font-weight: 700;
    color: #fff;
    line-height: 1;
    letter-spacing: -0.5px;
    font-family: var(--mono);
    display: flex;
    align-items: center;
    gap: 8px;
}
</style>

<div class="bmis-content">

    <!-- PREMIUM WELCOME BANNER -->
    <div class="ds-welcome-banner welcome-banner-premium">
        <!-- Floating decorative ambient light blobs -->
        <div class="banner-blob blob-1"></div>
        <div class="banner-blob blob-2"></div>
        
        <div style="display: flex; align-items: center; gap: 20px; position: relative; z-index: 2;">
            <div class="greeting-icon-wrapper" id="greetingIcon">
                <i class="fas fa-sun"></i>
            </div>
            <div>
                <div class="user-badge">
                    <i class="fas fa-user-shield"></i> BARANGAY STAFF
                </div>
                <h2 style="font-size: 24px; font-weight: 800; color: #FFFFFF; margin: 6px 0 0; letter-spacing: -0.02em;">
                    <span id="dynamicGreeting">Welcome</span>, <strong>Staff</strong>!
                </h2>
                <p id="greetingMessage" style="font-size: 14px; opacity: 0.9; margin-top: 4px; margin-bottom: 0; color: rgba(255,255,255,0.85);">
                    Monitor key metrics, resolve cases, and assist the community efficiently.
                </p>
            </div>
        </div>
        
        <div class="ds-datetime glass-datetime-card" style="position: relative; z-index: 2;">
            <div style="font-size: 11px; opacity: 0.9; text-transform: uppercase; letter-spacing: 1.5px; font-family: var(--font); font-weight: 700; display: flex; align-items: center; gap: 6px; color: var(--accent-light);">
                <i class="fas fa-calendar-day"></i> <span id="liveDate"><?= date('l, F j, Y') ?></span>
            </div>
            <div class="live-clock-display">
                <i class="far fa-clock" style="opacity: 0.8;"></i> <span id="liveTime"><?= date('h:i:s A') ?></span>
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

    <!-- ROW 6 · ACTIVITY + QUICK ACTIONS -->
    <div class="ds-section-label">Activity</div>
    <div class="ds-grid-activity">
        <div class="ds-card">
            <div class="ds-card-head">
                <div class="ds-card-title"><i class="fas fa-history"></i> Recent Activity</div>
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
            <div class="ds-card-head"><div class="ds-card-title"><i class="fas fa-bolt"></i> Quick Actions</div></div>
            <div class="ds-quick-grid" style="padding: 16px;">
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

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
const DASHBOARD_DATA = {
    baseUrl: "<?= base_url() ?>",
    csrfTokenName: "<?= csrf_token() ?>",
    csrfTokenValue: "<?= csrf_hash() ?>",
    purokLabels: <?= json_encode(array_column($purokCounts ?? [], 'sitio')) ?>,
    purokValues: <?= json_encode(array_column($purokCounts ?? [], 'count')) ?>
};

// Dynamic Greeting & Theme Upgrade
var hr = new Date().getHours();
var greetingText = "Good evening";
var iconClass = "fas fa-moon";
var iconTheme = "evening";
var welcomeMsg = "Wrapping up the day's tasks? Here's your current overview.";

if (hr < 12) {
    greetingText = "Good morning";
    iconClass = "fas fa-sun";
    iconTheme = "morning";
    welcomeMsg = "Ready for a productive day? Here is your morning update.";
} else if (hr < 18) {
    greetingText = "Good afternoon";
    iconClass = "fas fa-cloud-sun";
    iconTheme = "afternoon";
    welcomeMsg = "Keep up the great work! Monitor your community metrics below.";
}

var greetingEl = document.getElementById('dynamicGreeting');
if (greetingEl) greetingEl.innerText = greetingText;

var msgEl = document.getElementById('greetingMessage');
if (msgEl) msgEl.innerText = welcomeMsg;

var iconEl = document.getElementById('greetingIcon');
if (iconEl) {
    iconEl.className = "greeting-icon-wrapper " + iconTheme;
    iconEl.innerHTML = '<i class="' + iconClass + '"></i>';
}

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

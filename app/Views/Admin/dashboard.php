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
    background: #c49a00;
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
    background: rgba(196, 154, 0, 0.15);
    border: 1px solid rgba(196, 154, 0, 0.3);
    color: var(--accent-light);
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
                    <i class="fas fa-shield-alt"></i> SYSTEM ADMINISTRATOR
                </div>
                <h2 style="font-size: 24px; font-weight: 800; color: #FFFFFF; margin: 6px 0 0; letter-spacing: -0.02em;">
                    <span id="dynamicGreeting">Welcome</span>, <strong>Admin</strong>!
                </h2>
                <p id="greetingMessage" style="font-size: 14px; opacity: 0.9; margin-top: 4px; margin-bottom: 0; color: rgba(255,255,255,0.85);">
                    Monitor key metrics, resolve cases, and manage your community efficiently.
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

    <!-- ── PORTAL USAGE STATS ── -->
    <div class="ds-section-label" style="margin: 20px 0 10px;">
        <i class="fas fa-globe" style="margin-right:6px;color:#4f46e5"></i> Portal Activity — This Month
    </div>
    <div class="ds-grid-4" style="margin-bottom:8px">
        <div class="ds-stat" style="border-left:4px solid #4f46e5">
            <div class="ds-stat-top">
                <div class="ds-stat-icon" style="background:rgba(79,70,229,0.1);color:#4f46e5"><i class="fas fa-users-cog"></i></div>
                <span class="ds-stat-trend" style="color:#10b981;font-size:11px">+<?= $portalPendingAccounts ?? 0 ?> pending</span>
            </div>
            <div class="ds-stat-num"><?= $portalActiveAccounts ?? 0 ?></div>
            <div class="ds-stat-label">Active Portal Accounts</div>
            <a href="<?= base_url('admin/portal-accounts') ?>" class="ds-stat-footer" style="color:#4f46e5">
                <i class="fas fa-arrow-right"></i> Manage Accounts
            </a>
        </div>
        <div class="ds-stat" style="border-left:4px solid #8b5cf6">
            <div class="ds-stat-top">
                <div class="ds-stat-icon" style="background:rgba(139,92,246,0.1);color:#8b5cf6"><i class="fas fa-file-signature"></i></div>
                <span class="ds-stat-trend" style="color:var(--ink-muted)">online</span>
            </div>
            <div class="ds-stat-num"><?= $portalCertsThisMonth ?? 0 ?></div>
            <div class="ds-stat-label">Certificate Requests</div>
            <a href="<?= base_url('admin/online-requests') ?>" class="ds-stat-footer" style="color:#8b5cf6">
                <i class="fas fa-arrow-right"></i> Review Requests
            </a>
        </div>
        <div class="ds-stat" style="border-left:4px solid #f59e0b">
            <div class="ds-stat-top">
                <div class="ds-stat-icon" style="background:rgba(245,158,11,0.1);color:#f59e0b"><i class="fas fa-balance-scale"></i></div>
                <span class="ds-stat-trend" style="color:var(--ink-muted)">online</span>
            </div>
            <div class="ds-stat-num"><?= $portalBlottersThisMonth ?? 0 ?></div>
            <div class="ds-stat-label">Online Blotters Filed</div>
            <a href="<?= base_url('admin/online-requests') ?>" class="ds-stat-footer" style="color:#f59e0b">
                <i class="fas fa-arrow-right"></i> View Reports
            </a>
        </div>
        <div class="ds-stat" style="border-left:4px solid #0ea5e9">
            <div class="ds-stat-top">
                <div class="ds-stat-icon" style="background:rgba(14,165,233,0.1);color:#0ea5e9"><i class="fas fa-building"></i></div>
                <span class="ds-stat-trend" style="color:var(--ink-muted)">bookings</span>
            </div>
            <div class="ds-stat-num"><?= $portalBookingsThisMonth ?? 0 ?></div>
            <div class="ds-stat-label">Facility Bookings</div>
            <a href="<?= base_url('admin/facility-bookings') ?>" class="ds-stat-footer" style="color:#0ea5e9">
                <i class="fas fa-arrow-right"></i> Manage Bookings
            </a>
        </div>
    </div>

    <!-- ── DETAILED STATISTICS (COMBINED) ── -->
    <div style="display: flex; align-items: center; margin: 20px 0 10px;">
        <div class="ds-section-label" style="margin: 0; flex: 1;">Detailed Statistics</div>
        <select id="detailedStatsSelector" class="ds-select" style="width: auto; height: 26px; font-size: 11px; padding: 0 24px 0 10px; text-transform: none; letter-spacing: normal; background-color: var(--white); border-color: var(--border); margin-left: 12px; cursor: pointer;">
            <option value="caseOverview">Case Overview</option>
            <option value="population">Population Breakdown</option>
            <option value="gender">User &amp; Gender Stats</option>
        </select>
        
        <!-- Case Overview Date Filter (Only visible when Case Overview is selected) -->
        <select id="caseOverviewFilter" class="ds-select" style="width: auto; height: 26px; font-size: 11px; padding: 0 24px 0 10px; text-transform: none; letter-spacing: normal; background-color: var(--white); border-color: var(--border); margin-left: 8px; cursor: pointer;">
            <option value="month">This Month</option>
            <option value="year">This Year</option>
            <option value="all">All Time</option>
        </select>
    </div>

    <!-- 1. Case Overview Grid -->
    <div class="ds-grid-4 stats-grid" id="grid-caseOverview">
        <div class="ds-mini">
            <div class="ds-mini-icon ic-amber"><i class="fas fa-folder-open"></i></div>
            <div><div class="ds-mini-num case-overview-num" id="openCasesCount" style="transition: opacity 0.2s;"><?= $openCases ?? 0 ?></div><div class="ds-mini-label">Open Cases</div></div>
        </div>
        <div class="ds-mini">
            <div class="ds-mini-icon ic-blue"><i class="fas fa-gavel"></i></div>
            <div><div class="ds-mini-num case-overview-num" id="hearingsCount" style="transition: opacity 0.2s;"><?= $hearingsToday ?? 0 ?></div><div class="ds-mini-label">Hearings Today</div></div>
        </div>
        <div class="ds-mini">
            <div class="ds-mini-icon ic-teal"><i class="fas fa-check-circle"></i></div>
            <div><div class="ds-mini-num case-overview-num" id="settledCount" style="transition: opacity 0.2s;"><?= $settledThisMonth ?? 0 ?></div><div class="ds-mini-label">Settled (Month)</div></div>
        </div>
        <div class="ds-mini">
            <div class="ds-mini-icon ic-rose"><i class="fas fa-file-alt"></i></div>
            <div><div class="ds-mini-num case-overview-num" id="blotterTotalCount" style="transition: opacity 0.2s;"><?= $blotterCount ?? 0 ?></div><div class="ds-mini-label">Total Cases</div></div>
        </div>
    </div>

    <!-- 2. Population Breakdown Grid -->
    <div class="ds-grid-4 stats-grid" id="grid-population" style="display:none;">
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

    <!-- 3. User & Gender Grid -->
    <div class="ds-grid-4 stats-grid" id="grid-gender" style="display:none;">
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

    <!-- ROW 5.5 · AGE DISTRIBUTION -->
    <div class="ds-grid-2" style="margin-top:16px">
        <div class="ds-card">
            <div class="ds-card-head">
                <div class="ds-card-title"><i class="fas fa-birthday-cake"></i> Age Distribution</div>
                <span class="ds-badge ds-badge-blue"><?= $totalResidents ?? 0 ?> residents</span>
            </div>
            <div class="ds-card-body">
                <div class="ds-chart-wrap" style="height:190px"><canvas id="ageChart"></canvas></div>
            </div>
        </div>
        <div class="ds-card">
            <div class="ds-card-head">
                <div class="ds-card-title"><i class="fas fa-chart-pie"></i> Demographics Summary</div>
            </div>
            <div class="ds-card-body">
                <?php
                $ageBrackets = [
                    ['label' => 'Minors (0-17)', 'val' => $ageDistribution['minors'] ?? 0, 'color' => '#6366f1', 'icon' => 'fa-child'],
                    ['label' => 'Young Adults (18-30)', 'val' => $ageDistribution['young_adults'] ?? 0, 'color' => '#185FA5', 'icon' => 'fa-user-graduate'],
                    ['label' => 'Adults (31-45)', 'val' => $ageDistribution['adults'] ?? 0, 'color' => '#1D9E75', 'icon' => 'fa-briefcase'],
                    ['label' => 'Middle Aged (46-59)', 'val' => $ageDistribution['middle_aged'] ?? 0, 'color' => '#854F0B', 'icon' => 'fa-user'],
                    ['label' => 'Seniors (60+)', 'val' => $ageDistribution['seniors'] ?? 0, 'color' => '#A32D2D', 'icon' => 'fa-user-clock'],
                ];
                $totalPop = max(1, $totalResidents ?? 1);
                foreach ($ageBrackets as $ab):
                    $pct = round(($ab['val'] / $totalPop) * 100);
                ?>
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px">
                    <div style="width:28px;height:28px;border-radius:50%;background:<?= $ab['color'] ?>15;color:<?= $ab['color'] ?>;display:flex;align-items:center;justify-content:center;font-size:11px"><i class="fas <?= $ab['icon'] ?>"></i></div>
                    <div style="flex:1">
                        <div style="display:flex;justify-content:space-between;font-size:11px;margin-bottom:3px">
                            <span style="font-weight:600;color:var(--ink)"><?= $ab['label'] ?></span>
                            <span style="font-weight:700;color:<?= $ab['color'] ?>"><?= $ab['val'] ?> <span style="font-weight:400;color:var(--ink-muted)">(<?= $pct ?>%)</span></span>
                        </div>
                        <div style="height:5px;background:var(--bg);border-radius:4px;overflow:hidden">
                            <div style="height:100%;width:<?= $pct ?>%;background:<?= $ab['color'] ?>;border-radius:4px;transition:width .6s ease"></div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- ROW 6 · CASE ANALYTICS -->
    <div class="ds-section-label">Case Analytics</div>
    <div class="ds-grid-2" style="margin-bottom:16px">
        <div class="ds-card">
            <div class="ds-card-head">
                <div class="ds-card-title"><i class="fas fa-chart-pie"></i> Case Status Breakdown</div>
                <a href="<?= base_url('blotter') ?>" style="font-size:11px;font-weight:700;color:var(--c-blue);text-decoration:none">View All</a>
            </div>
            <div class="ds-card-body" style="display:flex;align-items:center;gap:20px">
                <div style="width:160px;height:160px;flex-shrink:0"><canvas id="blotterStatusChart"></canvas></div>
                <div style="flex:1">
                    <?php
                    $statusGroups = [
                        ['label'=>'Pending',    'color'=>'#f59e0b'],
                        ['label'=>'For Hearing','color'=>'#3b82f6'],
                        ['label'=>'Settled',    'color'=>'#14b8a6'],
                        ['label'=>'Dismissed',  'color'=>'#9ca3af'],
                        ['label'=>'Unsettled',  'color'=>'#f43f5e'],
                        ['label'=>'Referred',   'color'=>'#8b5cf6'],
                    ];
                    $dbInner = \Config\Database::connect();
                    foreach ($statusGroups as $sg):
                        $cnt = $dbInner->table('blotter_records')->where('status', $sg['label'])->countAllResults();
                        if ($cnt == 0) continue;
                        $pct = ($blotterCount??1) > 0 ? round(($cnt/($blotterCount??1))*100) : 0;
                    ?>
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:7px">
                        <div style="width:10px;height:10px;border-radius:50%;background:<?= $sg['color'] ?>;flex-shrink:0"></div>
                        <div style="flex:1;font-size:11px;font-weight:600;color:var(--ink)"><?= $sg['label'] ?></div>
                        <div style="font-size:11px;font-weight:700;color:var(--ink)"><?= $cnt ?> <span style="font-weight:400;color:var(--ink-muted)">(<?= $pct ?>%)</span></div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
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
                            <td class="mono"><?= esc($h['case_number'] ?? '--') ?></td>
                            <td><?= esc($h['venue'] ?? 'Main Hall') ?></td>
                        </tr>
                        <?php endforeach; else: ?>
                        <tr><td colspan="3" style="text-align:center;padding:24px;color:var(--ink-soft)">No hearings scheduled.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ROW 7 · TABLES -->
    <div class="ds-section-label">Records</div>
    <div class="ds-grid-2">
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
                        <tr>
                            <td colspan="3" style="text-align:center;padding:32px 16px;color:var(--ink-soft);">
                                <i class="fas fa-folder-open" style="font-size:32px;opacity:0.25;margin-bottom:12px;display:block;"></i>
                                <div style="font-weight:600;">No cases filed</div>
                                <div style="font-size:11px;opacity:0.7;">Recent cases will appear here.</div>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="ds-card">
            <div class="ds-card-head">
                <div class="ds-card-title"><i class="fas fa-file-invoice"></i> Recent Certificates</div>
                <a href="<?= base_url('certificate') ?>" style="font-size:11px;font-weight:700;color:var(--c-blue);text-decoration:none">View All</a>
            </div>
            <div class="ds-card-body p0">
                <?php
                $db = \Config\Database::connect();
                $recentCerts = $db->table('certificates')
                    ->select('certificates.certificate_number, certificates.certificate_type, certificates.created_at, residents.first_name, residents.last_name')
                    ->join('residents', 'residents.id = certificates.resident_id', 'left')
                    ->orderBy('certificates.id', 'DESC')
                    ->limit(5)
                    ->get()->getResultArray();
                ?>
                <table class="ds-table">
                    <thead><tr><th>Cert No.</th><th>Type</th><th>Resident</th></tr></thead>
                    <tbody>
                        <?php if (!empty($recentCerts)): foreach ($recentCerts as $rc2): ?>
                        <tr>
                            <td class="mono"><strong><?= esc($rc2['certificate_number']) ?></strong></td>
                            <td style="font-size:10.5px"><?= esc($rc2['certificate_type']) ?></td>
                            <td><?= esc(trim(($rc2['first_name'] ?? '') . ' ' . ($rc2['last_name'] ?? ''))) ?: '—' ?></td>
                        </tr>
                        <?php endforeach; else: ?>
                        <tr>
                            <td colspan="3" style="text-align:center;padding:32px 16px;color:var(--ink-soft);">
                                <i class="fas fa-file-alt" style="font-size:32px;opacity:0.25;margin-bottom:12px;display:block;"></i>
                                <div style="font-weight:600;">No certificates issued</div>
                            </td>
                        </tr>
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
                            <div class="ds-activity-meta">by <strong><?= esc($log['USER_NAME'] ?? 'System') ?></strong> · <?= esc(date('M d, Y h:i A', strtotime($log['TIMELOG'] ?? $log['DATELOG']))) ?></div>
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
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
const DASHBOARD_DATA = {
    baseUrl:        "<?= base_url() ?>",
    csrfTokenName:  "<?= csrf_token() ?>",
    csrfTokenValue: "<?= csrf_hash() ?>",
    gender:  { male: <?= (int)($totalMale??0) ?>, female: <?= (int)($totalFemale??0) ?> },
    purokLabels: <?= json_encode(array_column($purokCounts ?? [], 'sitio')) ?>,
    purokValues: <?= json_encode(array_column($purokCounts ?? [], 'count')) ?>,
    civilLabels: <?= json_encode(array_column($civilStatusData ?? [], 'civil_status')) ?>,
    civilValues: <?= json_encode(array_column($civilStatusData ?? [], 'count')) ?>,
    ageLabels: ['0-17', '18-30', '31-45', '46-59', '60+'],
    ageValues: [<?= (int)($ageDistribution['minors'] ?? 0) ?>, <?= (int)($ageDistribution['young_adults'] ?? 0) ?>, <?= (int)($ageDistribution['adults'] ?? 0) ?>, <?= (int)($ageDistribution['middle_aged'] ?? 0) ?>, <?= (int)($ageDistribution['seniors'] ?? 0) ?>],
    blotterStatusLabels: ['Pending','For Hearing','Settled','Dismissed','Unsettled','Referred'],
    blotterStatusValues: [
        <?php $dbJs = \Config\Database::connect(); echo
            $dbJs->table('blotter_records')->where('status','Pending')->countAllResults() . ',' .
            $dbJs->table('blotter_records')->where('status','For Hearing')->countAllResults() . ',' .
            $dbJs->table('blotter_records')->where('status','Settled')->countAllResults() . ',' .
            $dbJs->table('blotter_records')->where('status','Dismissed')->countAllResults() . ',' .
            $dbJs->table('blotter_records')->where('status','Unsettled')->countAllResults() . ',' .
            $dbJs->table('blotter_records')->where('status','Referred')->countAllResults();
        ?>
    ],
    blotterStatusColors: ['#f59e0b','#3b82f6','#14b8a6','#9ca3af','#f43f5e','#8b5cf6']
};
</script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script src="<?= base_url('js/dashboard/admin.js') ?>"></script>

<script>
// Detailed Stats Tab Logic
const statsSelector = document.getElementById('detailedStatsSelector');
if (statsSelector) {
    statsSelector.addEventListener('change', function() {
        // Hide all grids
        document.querySelectorAll('.stats-grid').forEach(el => el.style.display = 'none');
        
        // Show selected grid
        const selectedGrid = document.getElementById('grid-' + this.value);
        if (selectedGrid) selectedGrid.style.display = 'grid'; // or block depending on responsive CSS, but ds-grid-4 forces grid when not hidden
        
        // Toggle the date filter visibility
        document.getElementById('caseOverviewFilter').style.display = (this.value === 'caseOverview') ? 'block' : 'none';
    });
}

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
<?= $this->endSection() ?>

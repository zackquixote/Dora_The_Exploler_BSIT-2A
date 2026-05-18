<?= $this->extend('theme/template') ?>
<?= $this->section('content') ?>

<div class="bmis-content">
    <!-- Page Header -->
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px">
        <div style="display:flex;align-items:center;gap:16px">
            <div style="width:48px;height:48px;border-radius:12px;background:var(--c-amber-bg);color:var(--c-amber);display:flex;align-items:center;justify-content:center;font-size:20px">
                <i class="fas fa-ambulance"></i>
            </div>
            <div>
                <div class="ds-page-title" style="margin:0;font-size:24px;font-weight:800;color:var(--ink)">Emergency Incidents</div>
                <div style="font-size:13px;color:var(--ink-muted);margin-top:2px">Live list of active emergencies (AJAX refresh)</div>
            </div>
        </div>
        <div style="display:flex;gap:10px;flex-wrap:wrap">
            <a href="<?= base_url('advanced/report-emergency') ?>" class="ds-btn ds-btn-primary" style="height:40px;padding:0 20px;border-radius:20px">
                <i class="fas fa-plus"></i> Report Incident
            </a>
            <button id="emRefreshBtn" class="ds-btn ds-btn-ghost" style="height:40px;padding:0 20px;border-radius:20px;background:var(--white)">
                <i class="fas fa-sync"></i> Refresh
            </button>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="ds-grid-4" style="margin-bottom:14px">
        <div class="ds-stat">
            <div class="ds-stat-stripe" style="background:var(--c-amber)"></div>
            <div class="ds-stat-top"><div class="ds-stat-icon ic-amber"><i class="fas fa-exclamation-triangle"></i></div></div>
            <div class="ds-stat-num" id="emActiveCount">0</div>
            <div class="ds-stat-label">Active Incidents</div>
            <div class="ds-stat-footer" style="color:var(--c-amber);cursor:default"><i class="fas fa-info-circle"></i> reported/dispatched/responding</div>
        </div>
        <div class="ds-stat">
            <div class="ds-stat-stripe str-rose"></div>
            <div class="ds-stat-top"><div class="ds-stat-icon" style="background:var(--c-rose-bg);color:var(--c-rose)"><i class="fas fa-bolt"></i></div></div>
            <div class="ds-stat-num" id="emCriticalCount">0</div>
            <div class="ds-stat-label">Critical</div>
            <div class="ds-stat-footer" style="color:var(--c-rose);cursor:default"><i class="fas fa-heartbeat"></i> high priority</div>
        </div>
        <div class="ds-stat">
            <div class="ds-stat-stripe str-blue"></div>
            <div class="ds-stat-top"><div class="ds-stat-icon ic-blue"><i class="fas fa-clock"></i></div></div>
            <div class="ds-stat-num" id="emLastUpdated">--</div>
            <div class="ds-stat-label">Last Updated</div>
            <div class="ds-stat-footer ft-blue" style="cursor:default"><i class="fas fa-sync"></i> auto refresh</div>
        </div>
        <div class="ds-stat">
            <div class="ds-stat-stripe str-teal"></div>
            <div class="ds-stat-top"><div class="ds-stat-icon ic-green"><i class="fas fa-shield-alt"></i></div></div>
            <div class="ds-stat-num"><?= esc(($active_incidents['total_active'] ?? 0)) ?></div>
            <div class="ds-stat-label">Dashboard Stat</div>
            <div class="ds-stat-footer ft-teal" style="cursor:default"><i class="fas fa-database"></i> from service</div>
        </div>
    </div>

    <!-- Active Incidents Table -->
    <div class="ds-card" style="border:none;box-shadow:0 10px 30px rgba(0,0,0,0.03);border-radius:var(--r-lg);overflow:hidden">
        <div class="ds-card-head" style="background:var(--white);padding:20px 24px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
            <div class="ds-card-title"><i class="fas fa-list"></i> Active Incidents</div>
            <div id="emResultMeta" style="font-size:12px;color:var(--ink-muted)"></div>
        </div>
        <div class="ds-card-body p0">
            <div style="overflow-x:auto">
                <table class="ds-table" id="emergencyTable">
                    <thead>
                        <tr>
                            <th>Incident #</th>
                            <th>Type</th>
                            <th>Severity</th>
                            <th>Location</th>
                            <th>Status</th>
                            <th>Reporter</th>
                            <th>Contact</th>
                            <th>Reported At</th>
                        </tr>
                    </thead>
                    <tbody id="emergencyTbody">
                        <tr>
                            <td colspan="8" style="text-align:center;color:var(--ink-muted);padding:18px">
                                Loading...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script defer src="<?= base_url('js/advanced/emergency.js') ?>?v=<?= time() ?>"></script>
<?= $this->endSection() ?>


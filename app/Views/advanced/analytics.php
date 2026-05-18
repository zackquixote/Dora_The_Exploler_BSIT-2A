<?= $this->extend('theme/template') ?>
<?= $this->section('content') ?>

<?php
    $modules = $analytics['modules'] ?? null;
?>

<div class="bmis-content">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px">
        <div style="display:flex;align-items:center;gap:16px">
            <div style="width:48px;height:48px;border-radius:12px;background:rgba(59,130,246,0.12);color:#2563eb;display:flex;align-items:center;justify-content:center;font-size:20px">
                <i class="fas fa-chart-line"></i>
            </div>
            <div>
                <div class="ds-page-title" style="margin:0;font-size:24px;font-weight:800;color:var(--ink)">Advanced Analytics</div>
                <div style="font-size:13px;color:var(--ink-muted);margin-top:2px">Dashboards/KPIs across modules</div>
            </div>
        </div>
        <div style="display:flex;gap:10px">
            <button class="ds-btn ds-btn-ghost" id="kpiRefreshBtn" style="height:36px">
                <i class="fas fa-sync"></i> Refresh
            </button>
        </div>
    </div>

    <!-- KPI cards -->
    <div class="ds-grid-4" style="margin-bottom:14px">
        <div class="ds-stat">
            <div class="ds-stat-stripe str-blue"></div>
            <div class="ds-stat-top">
                <div class="ds-stat-icon ic-blue"><i class="fas fa-briefcase"></i></div>
            </div>
            <div class="ds-stat-num" id="kpiPermitsPending"><?= esc($modules['business_permits']['counts']['pending'] ?? 0) ?></div>
            <div class="ds-stat-label">Permit Renewals (Pending)</div>
            <div class="ds-stat-footer ft-blue" id="kpiPermitsMeta">This year</div>
        </div>

        <div class="ds-stat">
            <div class="ds-stat-stripe str-teal"></div>
            <div class="ds-stat-top">
                <div class="ds-stat-icon ic-green"><i class="fas fa-calendar-check"></i></div>
            </div>
            <div class="ds-stat-num" id="kpiEventsRate"><?= esc($modules['events']['attendance_rate'] ?? 0) ?>%</div>
            <div class="ds-stat-label">Event Attendance Rate</div>
            <div class="ds-stat-footer ft-teal" id="kpiEventsMeta">Last 30 days</div>
        </div>

        <div class="ds-stat">
            <div class="ds-stat-stripe str-rose"></div>
            <div class="ds-stat-top">
                <div class="ds-stat-icon" style="background:var(--c-rose-bg);color:var(--c-rose)"><i class="fas fa-heartbeat"></i></div>
            </div>
            <div class="ds-stat-num" id="kpiHealthTotal"><?= esc($modules['health']['total_records'] ?? 0) ?></div>
            <div class="ds-stat-label">Health Records</div>
            <div class="ds-stat-footer" style="color:var(--c-rose)" id="kpiHealthMeta">Encoded</div>
        </div>

        <div class="ds-stat">
            <div class="ds-stat-stripe" style="background:var(--c-amber)"></div>
            <div class="ds-stat-top">
                <div class="ds-stat-icon ic-amber"><i class="fas fa-folder-open"></i></div>
            </div>
            <div class="ds-stat-num" id="kpiDocsActive"><?= esc($modules['documents']['active_files'] ?? 0) ?></div>
            <div class="ds-stat-label">Active Documents</div>
            <div class="ds-stat-footer" style="color:var(--c-amber)" id="kpiDocsMeta">Storage</div>
        </div>
    </div>

    <div class="ds-card" style="margin-bottom:14px">
        <div class="ds-card-body">
            <div class="ds-filter-header"><i class="fas fa-table"></i> KPI Details</div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                <div class="ds-card" style="border:1px solid var(--border);box-shadow:none">
                    <div class="ds-card-head" style="background:var(--white);padding:14px 16px;border-bottom:1px solid var(--border)">
                        <div class="ds-card-title"><i class="fas fa-briefcase"></i> Business Permits</div>
                    </div>
                    <div class="ds-card-body">
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;font-size:13px">
                            <div>Pending: <strong id="permitsPending2"><?= esc($modules['business_permits']['counts']['pending'] ?? 0) ?></strong></div>
                            <div>Paid: <strong id="permitsPaid2"><?= esc($modules['business_permits']['counts']['paid'] ?? 0) ?></strong></div>
                            <div>Approved: <strong id="permitsApproved2"><?= esc($modules['business_permits']['counts']['approved'] ?? 0) ?></strong></div>
                            <div>Printed: <strong id="permitsPrinted2"><?= esc($modules['business_permits']['counts']['printed'] ?? 0) ?></strong></div>
                            <div>Avg days to approve: <strong id="permitsAvgApprove"><?= esc($modules['business_permits']['avg_days_to_approval'] ?? 0) ?></strong></div>
                            <div>Avg days to print: <strong id="permitsAvgPrint"><?= esc($modules['business_permits']['avg_days_to_print'] ?? 0) ?></strong></div>
                        </div>
                        <div style="margin-top:10px;font-size:12px;color:var(--ink-muted)">
                            Data source: <code>permit_renewals</code> (current year)
                        </div>
                    </div>
                </div>

                <div class="ds-card" style="border:1px solid var(--border);box-shadow:none">
                    <div class="ds-card-head" style="background:var(--white);padding:14px 16px;border-bottom:1px solid var(--border)">
                        <div class="ds-card-title"><i class="fas fa-calendar-check"></i> Events</div>
                    </div>
                    <div class="ds-card-body">
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;font-size:13px">
                            <div>Upcoming events: <strong id="eventsUpcoming"><?= esc($modules['events']['upcoming_events'] ?? 0) ?></strong></div>
                            <div>Registered (30d): <strong id="eventsRegistered"><?= esc($modules['events']['last_30_days_registered'] ?? 0) ?></strong></div>
                            <div>Attended (30d): <strong id="eventsAttended"><?= esc($modules['events']['last_30_days_attended'] ?? 0) ?></strong></div>
                            <div>Attendance rate: <strong id="eventsRate2"><?= esc($modules['events']['attendance_rate'] ?? 0) ?>%</strong></div>
                        </div>
                        <div style="margin-top:10px;font-size:12px;color:var(--ink-muted)">
                            Data source: <code>event_participants</code> + <code>events</code>
                        </div>
                    </div>
                </div>

                <div class="ds-card" style="border:1px solid var(--border);box-shadow:none">
                    <div class="ds-card-head" style="background:var(--white);padding:14px 16px;border-bottom:1px solid var(--border)">
                        <div class="ds-card-title"><i class="fas fa-heartbeat"></i> Health</div>
                    </div>
                    <div class="ds-card-body">
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;font-size:13px">
                            <div>Total records: <strong id="healthTotal2"><?= esc($modules['health']['total_records'] ?? 0) ?></strong></div>
                            <div>With vaccinations: <strong id="healthWithVacc"><?= esc($modules['health']['with_vaccination_records'] ?? 0) ?></strong></div>
                            <div>Recent checkups (30d): <strong id="healthRecent"><?= esc($modules['health']['recent_checkups_30d'] ?? 0) ?></strong></div>
                        </div>
                        <div style="margin-top:10px;font-size:12px;color:var(--ink-muted)">
                            Attachments: use <code>entity_type=health_record</code> in Document Management.
                        </div>
                    </div>
                </div>

                <div class="ds-card" style="border:1px solid var(--border);box-shadow:none">
                    <div class="ds-card-head" style="background:var(--white);padding:14px 16px;border-bottom:1px solid var(--border)">
                        <div class="ds-card-title"><i class="fas fa-folder-open"></i> Documents</div>
                    </div>
                    <div class="ds-card-body">
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;font-size:13px">
                            <div>Total rows: <strong id="docsTotal"><?= esc($modules['documents']['total_files'] ?? 0) ?></strong></div>
                            <div>Active: <strong id="docsActive2"><?= esc($modules['documents']['active_files'] ?? 0) ?></strong></div>
                            <div>Uploads (7d): <strong id="docsLast7"><?= esc($modules['documents']['uploads_last_7d'] ?? 0) ?></strong></div>
                            <div>Active size: <strong id="docsSize">—</strong></div>
                        </div>
                        <div style="margin-top:10px;font-size:12px;color:var(--ink-muted)">
                            Note: document rows include versions (one row per version).
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script defer src="<?= base_url('js/advanced/analytics.js') ?>?v=<?= time() ?>"></script>
<?= $this->endSection() ?>


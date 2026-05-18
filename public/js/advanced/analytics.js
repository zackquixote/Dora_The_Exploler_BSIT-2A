/* global $, window, showToast, redirectIfSessionExpired */
(function () {
    'use strict';

    const base = (window.baseUrl || '/');
    const API = base + 'advanced/api/analytics/kpis';

    const $btn = $('#kpiRefreshBtn');

    function formatBytes(bytes) {
        const b = Number(bytes || 0);
        if (!Number.isFinite(b) || b <= 0) return '0 B';
        const units = ['B', 'KB', 'MB', 'GB', 'TB'];
        const i = Math.min(Math.floor(Math.log(b) / Math.log(1024)), units.length - 1);
        const v = b / Math.pow(1024, i);
        return `${v.toFixed(i === 0 ? 0 : 2)} ${units[i]}`;
    }

    function setText(id, value) {
        const $el = $(id);
        if ($el.length) $el.text(value);
    }

    function applyKpis(kpis) {
        const permits = (kpis.business_permits || {});
        const events = (kpis.events || {});
        const health = (kpis.health || {});
        const docs = (kpis.documents || {});

        // Cards
        setText('#kpiPermitsPending', permits.counts ? (permits.counts.pending || 0) : '—');
        setText('#kpiPermitsMeta', permits.year ? `Year ${permits.year}` : '—');

        setText('#kpiEventsRate', `${events.attendance_rate || 0}%`);
        setText('#kpiHealthTotal', health.total_records || 0);
        setText('#kpiDocsActive', docs.active_files || 0);
        setText('#kpiDocsMeta', docs.active_size_bytes != null ? formatBytes(docs.active_size_bytes) : '—');

        // Details
        setText('#permitsPending2', permits.counts ? (permits.counts.pending || 0) : '—');
        setText('#permitsPaid2', permits.counts ? (permits.counts.paid || 0) : '—');
        setText('#permitsApproved2', permits.counts ? (permits.counts.approved || 0) : '—');
        setText('#permitsPrinted2', permits.counts ? (permits.counts.printed || 0) : '—');
        setText('#permitsAvgApprove', permits.avg_days_to_approval != null ? permits.avg_days_to_approval : '—');
        setText('#permitsAvgPrint', permits.avg_days_to_print != null ? permits.avg_days_to_print : '—');

        setText('#eventsUpcoming', events.upcoming_events || 0);
        setText('#eventsRegistered', events.last_30_days_registered || 0);
        setText('#eventsAttended', events.last_30_days_attended || 0);
        setText('#eventsRate2', `${events.attendance_rate || 0}%`);

        setText('#healthTotal2', health.total_records || 0);
        setText('#healthWithVacc', health.with_vaccination_records || 0);
        setText('#healthRecent', health.recent_checkups_30d || 0);

        setText('#docsTotal', docs.total_files || 0);
        setText('#docsActive2', docs.active_files || 0);
        setText('#docsLast7', docs.uploads_last_7d || 0);
        setText('#docsSize', docs.active_size_bytes != null ? formatBytes(docs.active_size_bytes) : '—');
    }

    function load() {
        $btn.prop('disabled', true);
        return $.get(API)
            .done(function (res) {
                if (!res || !res.success) {
                    showToast && showToast('error', (res && res.message) ? res.message : 'Failed to load KPIs.');
                    return;
                }
                applyKpis(res.data || {});
            })
            .fail(function (xhr) {
                if (typeof redirectIfSessionExpired === 'function' && redirectIfSessionExpired(xhr)) return;
                showToast && showToast('error', 'Error loading KPIs.');
            })
            .always(function () {
                $btn.prop('disabled', false);
            });
    }

    $btn.on('click', load);
    load();
})();


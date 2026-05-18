/* global $, window, redirectIfSessionExpired, showToast */
(function () {
    'use strict';

    const API_URL = (window.baseUrl || '/') + 'advanced/api/emergency/active';

    const $tbody = $('#emergencyTbody');
    const $meta = $('#emResultMeta');
    const $btn = $('#emRefreshBtn');
    const $activeCount = $('#emActiveCount');
    const $criticalCount = $('#emCriticalCount');
    const $lastUpdated = $('#emLastUpdated');

    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function setLoading() {
        $tbody.html(
            '<tr><td colspan="8" style="text-align:center;color:var(--ink-muted);padding:18px">' +
            '<div class="ds-skeleton" style="width:100%;height:32px;margin-bottom:8px"></div>' +
            '<div class="ds-skeleton" style="width:100%;height:32px;margin-bottom:8px"></div>' +
            '<div class="ds-skeleton" style="width:70%;height:32px"></div>' +
            '</td></tr>'
        );
        $meta.text('Loading...');
    }

    function renderRows(rows) {
        if (!rows || rows.length === 0) {
            $tbody.html('<tr><td colspan="8" style="text-align:center;color:var(--ink-muted);padding:18px">No active incidents.</td></tr>');
            return;
        }

        const html = rows.map(r => `
            <tr>
                <td>${escapeHtml(r.incident_number || '')}</td>
                <td><strong>${escapeHtml(r.emergency_type || '')}</strong></td>
                <td>${escapeHtml(r.severity_level || '')}</td>
                <td>${escapeHtml(r.location || '')}</td>
                <td>${escapeHtml(r.status || '')}</td>
                <td>${escapeHtml(r.reporter_name || '')}</td>
                <td>${escapeHtml(r.reporter_contact || '')}</td>
                <td>${escapeHtml(r.created_at || '')}</td>
            </tr>
        `).join('');

        $tbody.html(html);
    }

    window.fetchActiveEmergencies = function fetchActiveEmergencies() {
        setLoading();
        return $.get(API_URL)
            .done(function (res) {
                if (!res || !res.success) {
                    showToast('error', (res && res.message) ? res.message : 'Failed to load active emergencies.');
                    renderRows([]);
                    $meta.text('');
                    $activeCount.text('0');
                    $criticalCount.text('0');
                    return;
                }

                const rows = res.data || [];
                renderRows(rows);
                $meta.text(`${rows.length} active incident(s)`);
                $activeCount.text(rows.length);
                $criticalCount.text(rows.filter(r => String(r.severity_level).toLowerCase() === 'critical').length);
                $lastUpdated.text(new Date().toLocaleTimeString());
            })
            .fail(function (xhr) {
                if (typeof redirectIfSessionExpired === 'function' && redirectIfSessionExpired(xhr)) return;
                showToast('error', 'Error loading emergencies.');
                renderRows([]);
                $meta.text('');
                $activeCount.text('0');
                $criticalCount.text('0');
            });
    };

    $btn.on('click', function () { window.fetchActiveEmergencies(); });

    // initial + auto refresh every 30s
    window.fetchActiveEmergencies();
    setInterval(() => window.fetchActiveEmergencies(), 30000);
})();


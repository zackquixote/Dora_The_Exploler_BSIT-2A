/* global $, window, redirectIfSessionExpired, showToast */
(function () {
    'use strict';

    const API_URL = (window.baseUrl || '/') + 'advanced/api/events/list';

    const $tbody = $('#eventsTbody');
    const $meta = $('#evtMeta');
    const $refresh = $('#evtRefreshBtn');
    const $q = $('#evtSearch');
    const $status = $('#evtStatus');
    const $clear = $('#evtClearBtn');

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
            '<tr><td colspan="7" style="text-align:center;color:var(--ink-muted);padding:18px">' +
            '<div class="ds-skeleton" style="width:100%;height:32px;margin-bottom:8px"></div>' +
            '<div class="ds-skeleton" style="width:100%;height:32px;margin-bottom:8px"></div>' +
            '<div class="ds-skeleton" style="width:75%;height:32px"></div>' +
            '</td></tr>'
        );
        $meta.text('Loading...');
    }

    function renderRows(rows) {
        if (!rows || rows.length === 0) {
            $tbody.html('<tr><td colspan="7" style="text-align:center;color:var(--ink-muted);padding:18px">No events found.</td></tr>');
            return;
        }

        const html = rows.map(r => `
            <tr>
                <td>${escapeHtml(r.event_code || '')}</td>
                <td><strong>${escapeHtml(r.title || '')}</strong></td>
                <td>${escapeHtml(r.event_type || '')}</td>
                <td>${escapeHtml(r.venue || '')}</td>
                <td>${escapeHtml(r.start_date || '')}</td>
                <td>${escapeHtml(r.end_date || '')}</td>
                <td>${escapeHtml(r.status || '')}</td>
            </tr>
        `).join('');

        $tbody.html(html);
    }

    function applyClientFilters(rows) {
        const q = ($q.val() || '').trim().toLowerCase();
        const st = ($status.val() || '').trim().toLowerCase();

        return (rows || []).filter(r => {
            const title = String(r.title || '').toLowerCase();
            const status = String(r.status || '').toLowerCase();

            if (q && !title.includes(q)) return false;
            if (st && status !== st) return false;
            return true;
        });
    }

    window.fetchEventsList = function fetchEventsList() {
        setLoading();
        return $.get(API_URL)
            .done(function (res) {
                if (!res || !res.success) {
                    showToast('error', (res && res.message) ? res.message : 'Failed to load events list.');
                    renderRows([]);
                    $meta.text('');
                    return;
                }

                const allRows = res.data || [];
                const filtered = applyClientFilters(allRows);
                renderRows(filtered);
                $meta.text(`${filtered.length} event(s)`);
            })
            .fail(function (xhr) {
                if (typeof redirectIfSessionExpired === 'function' && redirectIfSessionExpired(xhr)) return;
                showToast('error', 'Error loading events.');
                renderRows([]);
                $meta.text('');
            });
    };

    let debounce;
    function debouncedReload() {
        clearTimeout(debounce);
        debounce = setTimeout(() => window.fetchEventsList(), 250);
    }

    $refresh.on('click', function () { window.fetchEventsList(); });
    $q.on('input', debouncedReload);
    $status.on('change', debouncedReload);
    $clear.on('click', function () {
        $q.val('');
        $status.val('');
        window.fetchEventsList();
    });

    window.fetchEventsList();
})();


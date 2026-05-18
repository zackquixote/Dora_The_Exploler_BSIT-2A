/* global $, window, redirectIfSessionExpired, showToast */
(function () {
    'use strict';

    const API_URL = (window.baseUrl || '/') + 'advanced/api/business/search';

    const $q = $('#bizSearch');
    const $type = $('#bizType');
    const $btn = $('#bizSearchBtn');
    const $clear = $('#bizClearBtn');
    const $tbody = $('#businessTbody');
    const $meta = $('#bizResultMeta');

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
            '<div class="ds-skeleton" style="width:80%;height:32px"></div>' +
            '</td></tr>'
        );
        $meta.text('Loading...');
    }

    function renderRows(rows) {
        if (!rows || rows.length === 0) {
            $tbody.html('<tr><td colspan="8" style="text-align:center;color:var(--ink-muted);padding:18px">No businesses found.</td></tr>');
            return;
        }

        const html = rows.map(r => {
            const owner = [r.first_name, r.last_name].filter(Boolean).join(' ');
            return `
                <tr>
                    <td>${escapeHtml(r.business_permit_number || '')}</td>
                    <td><strong>${escapeHtml(r.business_name || '')}</strong></td>
                    <td>${escapeHtml(r.business_type || '')}</td>
                    <td>${escapeHtml(owner || '')}</td>
                    <td>${escapeHtml(r.contact_number || '')}</td>
                    <td>${escapeHtml(r.status || '')}</td>
                    <td>${escapeHtml(r.issue_date || '')}</td>
                    <td>${escapeHtml(r.expiry_date || '')}</td>
                </tr>
            `;
        }).join('');

        $tbody.html(html);
    }

    /**
     * AJAX filtering for businesses.
     */
    window.searchBusinesses = function searchBusinesses() {
        const q = ($q.val() || '').trim();
        const type = ($type.val() || '').trim();

        setLoading();

        return $.get(API_URL, { q, type })
            .done(function (res) {
                if (!res || !res.success) {
                    showToast('error', (res && res.message) ? res.message : 'Failed to load businesses.');
                    renderRows([]);
                    $meta.text('');
                    return;
                }
                renderRows(res.data || []);
                $meta.text(`${(res.data || []).length} result(s)`);
            })
            .fail(function (xhr) {
                if (typeof redirectIfSessionExpired === 'function' && redirectIfSessionExpired(xhr)) return;
                showToast('error', 'Error loading businesses.');
                renderRows([]);
                $meta.text('');
            });
    };

    let debounce;
    function debouncedSearch() {
        clearTimeout(debounce);
        debounce = setTimeout(() => window.searchBusinesses(), 300);
    }

    $btn.on('click', function () { window.searchBusinesses(); });
    $q.on('input', debouncedSearch);
    $type.on('input', debouncedSearch);
    $clear.on('click', function () {
        $q.val('');
        $type.val('');
        $meta.text('');
        $tbody.html('<tr><td colspan="8" style="text-align:center;color:var(--ink-muted);padding:18px">Use the filters above to load businesses.</td></tr>');
    });

    // Initial load
    window.searchBusinesses();
})();


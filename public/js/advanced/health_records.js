/* global $, window, redirectIfSessionExpired, showToast */
(function () {
    'use strict';

    const API_URL = (window.baseUrl || '/') + 'advanced/api/health-records/search';

    const $name = $('#hrSearchName');
    const $blood = $('#hrBloodType');
    const $btn = $('#hrSearchBtn');
    const $clear = $('#hrClearBtn');

    const $donorBlood = $('#donorBloodType');
    const $donorFind = $('#donorFindBtn');
    const $donorUse = $('#donorUseSearchBtn');

    const $tbody = $('#healthRecordsTbody');
    const $meta = $('#hrResultMeta');
    const $donorCount = $('#donorCount');

    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function renderRows(rows) {
        if (!rows || rows.length === 0) {
            $tbody.html(
                '<tr><td colspan="7" style="text-align:center;color:var(--ink-muted);padding:18px">' +
                'No records found.' +
                '</td></tr>'
            );
            return;
        }

        const html = rows.map(r => {
            const name = [r.first_name, r.last_name].filter(Boolean).join(' ');
            const conditions = (() => {
                if (Array.isArray(r.medical_conditions)) return r.medical_conditions.join(', ');
                // might be JSON string
                try {
                    const parsed = JSON.parse(r.medical_conditions);
                    if (Array.isArray(parsed)) return parsed.join(', ');
                } catch (e) { /* ignore */ }
                return r.medical_conditions ?? '';
            })();

            return `
                <tr>
                    <td><strong>${escapeHtml(name || 'N/A')}</strong></td>
                    <td>${escapeHtml(r.blood_type || 'Unknown')}</td>
                    <td>${escapeHtml(r.allergies || '')}</td>
                    <td>${escapeHtml(conditions)}</td>
                    <td>${escapeHtml(r.emergency_contact_name || '')}</td>
                    <td>${escapeHtml(r.emergency_contact_phone || r.resident_contact || '')}</td>
                    <td>${escapeHtml(r.last_checkup_date || '')}</td>
                </tr>
            `;
        }).join('');

        $tbody.html(html);
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

    /**
     * AJAX: searchHealthRecords()
     * Uses query + blood type filters.
     */
    window.searchHealthRecords = function searchHealthRecords() {
        const q = ($name.val() || '').trim();
        const bloodType = ($blood.val() || '').trim();

        setLoading();

        return $.get(API_URL, { q, blood_type: bloodType })
            .done(function (res) {
                if (!res || !res.success) {
                    showToast('error', (res && res.message) ? res.message : 'Failed to load health records.');
                    renderRows([]);
                    return;
                }
                renderRows(res.data || []);
                $meta.text(`${(res.data || []).length} result(s)`);
                $donorCount.text((bloodType && bloodType !== 'Unknown') ? (res.data || []).length : 0);
            })
            .fail(function (xhr) {
                if (typeof redirectIfSessionExpired === 'function' && redirectIfSessionExpired(xhr)) return;
                showToast('error', 'Error loading health records.');
                renderRows([]);
                $meta.text('');
            });
    };

    /**
     * AJAX: findBloodDonors()
     * Uses blood type only (required).
     */
    window.findBloodDonors = function findBloodDonors() {
        const bloodType = ($donorBlood.val() || '').trim();

        if (!bloodType) {
            showToast('error', 'Please select a blood type first.');
            return $.Deferred().reject().promise();
        }

        setLoading();

        return $.get(API_URL, { blood_type: bloodType })
            .done(function (res) {
                if (!res || !res.success) {
                    showToast('error', (res && res.message) ? res.message : 'Failed to find blood donors.');
                    renderRows([]);
                    return;
                }
                renderRows(res.data || []);
                $meta.text(`${(res.data || []).length} donor(s) found for ${bloodType}`);
                $donorCount.text((res.data || []).length);
            })
            .fail(function (xhr) {
                if (typeof redirectIfSessionExpired === 'function' && redirectIfSessionExpired(xhr)) return;
                showToast('error', 'Error finding blood donors.');
                renderRows([]);
                $meta.text('');
            });
    };

    let debounce;
    function debouncedSearch() {
        clearTimeout(debounce);
        debounce = setTimeout(() => window.searchHealthRecords(), 300);
    }

    $btn.on('click', function () { window.searchHealthRecords(); });
    $clear.on('click', function () {
        $name.val('');
        $blood.val('');
        $meta.text('');
        $donorCount.text('0');
        $tbody.html('<tr><td colspan="7" style="text-align:center;color:var(--ink-muted);padding:18px">Use the filters above to load records.</td></tr>');
    });

    $name.on('input', debouncedSearch);
    $blood.on('change', debouncedSearch);

    $donorFind.on('click', function () { window.findBloodDonors(); });
    $donorUse.on('click', function () {
        const bt = ($donorBlood.val() || '').trim();
        if (!bt) {
            showToast('error', 'Please select a blood type first.');
            return;
        }
        $blood.val(bt);
        window.searchHealthRecords();
    });

})();


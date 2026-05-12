/**
 * blotter-edit.js
 * Handles the dynamic party rows and TomSelect resident search
 * on the blotter/edit form.
 *
 * Expects window.blotterConfig = { searchUrl: '...', partyIndex: N }
 */
(function () {
    'use strict';

    let partyIndex = (window.blotterConfig && window.blotterConfig.partyIndex) || 0;
    const searchUrl = (window.blotterConfig && window.blotterConfig.searchUrl) || '';

    /* ── TomSelect initialiser ─────────────────────────────────────── */
    function initTomSelect(selectEl) {
        if (!selectEl || selectEl.tomselect) return;
        new TomSelect(selectEl, {
            valueField: 'id',
            labelField: 'text',
            searchField: ['text'],
            placeholder: 'Type to search resident…',
            maxOptions: 50,
            dropdownParent: 'body',
            load: function (query, callback) {
                const url = searchUrl + (searchUrl.includes('?') ? '&' : '?') + 'q=' + encodeURIComponent(query);
                fetch(url)
                    .then(r => r.json())
                    .then(data => callback(data))
                    .catch(() => callback());
            },
            shouldLoad: function (query) { return query.length >= 1; },
            render: {
                no_results: function () {
                    return '<div class="no-results" style="padding:8px 12px;font-size:12px;color:var(--ink-muted)">No residents found.</div>';
                }
            }
        });
    }

    /* ── Build a new party row HTML (matches the view's ds-* classes) ─ */
    function buildPartyRow(index) {
        return `
<div class="party-entry" style="padding:14px;background:var(--bg);border-radius:var(--r-sm);margin-bottom:10px;border:.5px solid var(--border)">
    <div style="display:grid;grid-template-columns:140px 140px 1fr auto;gap:10px;align-items:end">
        <div>
            <label class="ds-input-label">Role</label>
            <select name="parties[${index}][role]" class="ds-select" required>
                <option value="complainant">Complainant</option>
                <option value="respondent">Respondent</option>
                <option value="witness">Witness</option>
            </select>
        </div>
        <div>
            <label class="ds-input-label">Type</label>
            <div style="display:flex;gap:4px">
                <label class="type-toggle-label" data-value="resident"
                    style="flex:1;display:flex;align-items:center;justify-content:center;padding:6px;border-radius:var(--r-sm);font-size:10.5px;font-weight:700;cursor:pointer;border:1px solid var(--border);background:var(--white)">
                    <input type="radio" name="parties[${index}][type]" value="resident" style="display:none"> Resident
                </label>
                <label class="type-toggle-label" data-value="outsider"
                    style="flex:1;display:flex;align-items:center;justify-content:center;padding:6px;border-radius:var(--r-sm);font-size:10.5px;font-weight:700;cursor:pointer;border:1px solid var(--border);background:var(--c-amber-bg)">
                    <input type="radio" name="parties[${index}][type]" value="outsider" checked style="display:none"> Outsider
                </label>
            </div>
        </div>
        <div>
            <div class="resident-fields" style="display:none">
                <label class="ds-input-label">Search Resident</label>
                <select name="parties[${index}][resident_id]" class="resident-select ds-select" style="width:100%"></select>
            </div>
            <div class="outsider-fields">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px">
                    <div>
                        <label class="ds-input-label">Name</label>
                        <input type="text" name="parties[${index}][outsider_name]" class="ds-input" placeholder="Full name">
                    </div>
                    <div>
                        <label class="ds-input-label">Address</label>
                        <input type="text" name="parties[${index}][outsider_address]" class="ds-input" placeholder="Address">
                    </div>
                </div>
            </div>
        </div>
        <div>
            <button type="button" class="ds-action-btn ab-rose remove-party"><i class="fas fa-trash-alt"></i></button>
        </div>
    </div>
</div>`;
    }

    /* ── Toggle resident/outsider fields within a party row ─────────── */
    function applyTypeToggle(row, selectedValue) {
        const residentFields = row.querySelector('.resident-fields');
        const outsiderFields = row.querySelector('.outsider-fields');
        const labels = row.querySelectorAll('.type-toggle-label');

        labels.forEach(function (lbl) {
            const val = lbl.getAttribute('data-value');
            if (val === 'resident') {
                lbl.style.background = selectedValue === 'resident' ? 'var(--c-blue-bg)' : 'var(--white)';
            } else {
                lbl.style.background = selectedValue === 'outsider' ? 'var(--c-amber-bg)' : 'var(--white)';
            }
        });

        if (residentFields) residentFields.style.display = selectedValue === 'resident' ? '' : 'none';
        if (outsiderFields) outsiderFields.style.display = selectedValue === 'outsider' ? '' : 'none';

        if (selectedValue === 'resident') {
            const sel = row.querySelector('.resident-select');
            if (sel) initTomSelect(sel);
        }
    }

    /* ── Attach events to a party row ───────────────────────────────── */
    function bindRow(row) {
        row.querySelectorAll('input[type="radio"][name$="[type]"]').forEach(function (radio) {
            radio.addEventListener('change', function () {
                applyTypeToggle(row, this.value);
            });
        });

        row.querySelectorAll('.type-toggle-label').forEach(function (lbl) {
            lbl.addEventListener('click', function () {
                const radio = this.querySelector('input[type="radio"]');
                if (radio) {
                    radio.checked = true;
                    radio.dispatchEvent(new Event('change', { bubbles: true }));
                }
            });
        });

        const removeBtn = row.querySelector('.remove-party');
        if (removeBtn && !removeBtn.disabled) {
            removeBtn.addEventListener('click', function () {
                row.remove();
            });
        }
    }

    /* ── Init existing rows on page load ────────────────────────────── */
    document.querySelectorAll('.party-entry').forEach(function (row) {
        const checkedRadio = row.querySelector('input[type="radio"][name$="[type]"]:checked');
        const currentType = checkedRadio ? checkedRadio.value : 'outsider';
        applyTypeToggle(row, currentType);

        if (currentType === 'resident') {
            const sel = row.querySelector('.resident-select');
            if (sel) initTomSelect(sel);
        }

        bindRow(row);
    });

    /* ── Add Party button ───────────────────────────────────────────── */
    const addBtn = document.getElementById('add-party-btn');
    if (addBtn) {
        addBtn.addEventListener('click', function () {
            const container = document.getElementById('parties-container');
            const tmp = document.createElement('div');
            tmp.innerHTML = buildPartyRow(partyIndex).trim();
            const newRow = tmp.firstElementChild;
            container.appendChild(newRow);
            applyTypeToggle(newRow, 'outsider');
            bindRow(newRow);
            partyIndex++;
        });
    }

    /* ── Form submit guard ──────────────────────────────────────────── */
    const form = document.getElementById('blotter-form');
    if (form) {
        form.addEventListener('submit', function (e) {
            const rows = document.querySelectorAll('.party-entry');
            let hasComplainant = false;
            let hasRespondent  = false;
            let valid = true;

            rows.forEach(function (row) {
                const roleEl = row.querySelector('select[name$="[role]"]');
                const role   = roleEl ? roleEl.value : '';
                if (role === 'complainant') hasComplainant = true;
                if (role === 'respondent')  hasRespondent  = true;

                const checkedType = row.querySelector('input[type="radio"][name$="[type]"]:checked');
                const type = checkedType ? checkedType.value : 'outsider';

                if (type === 'outsider') {
                    const nameEl = row.querySelector('input[name$="[outsider_name]"]');
                    if (nameEl && nameEl.value.trim() === '') {
                        nameEl.style.borderColor = 'var(--c-rose)';
                        nameEl.focus();
                        valid = false;
                    } else if (nameEl) {
                        nameEl.style.borderColor = '';
                    }
                } else {
                    const resEl = row.querySelector('select[name$="[resident_id]"]');
                    if (resEl && !resEl.value) {
                        valid = false;
                        alert('Please select a resident for all "Resident" type parties.');
                    }
                }
            });

            if (!hasComplainant || !hasRespondent) {
                e.preventDefault();
                alert('You must have at least one Complainant and one Respondent.');
                return;
            }

            if (!valid) {
                e.preventDefault();
            }
        });
    }

})();

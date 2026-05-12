/**
 * blotter-create.js
 * Drives the Involved Parties section on blotter/create.
 *
 * Features:
 *  - Role-color card borders that update live when role changes
 *  - Pill-style Resident / Outsider toggle (no hidden radios)
 *  - TomSelect resident search (lazy-init on first switch to Resident)
 *  - Live party count badge
 *  - Inline field validation with error messages
 *  - Remove button (first 2 rows locked)
 *
 * Expects: window.blotterConfig = { searchUrl, partyIndex }
 */
(function () {
    'use strict';

    let partyIndex = (window.blotterConfig && window.blotterConfig.partyIndex) || 2;
    const searchUrl = (window.blotterConfig && window.blotterConfig.searchUrl) || '';

    const ROLE_ICONS = {
        complainant: 'fa-user-edit',
        respondent:  'fa-user-alt-slash',
        witness:     'fa-eye'
    };
    const ROLE_LABELS = {
        complainant: 'Complainant',
        respondent:  'Respondent',
        witness:     'Witness'
    };

    /* ─────────────────────────────────────────────────────────────────
       TomSelect
    ───────────────────────────────────────────────────────────────── */
    function initTomSelect(selectEl) {
        if (!selectEl || selectEl.tomselect) return;
        new TomSelect(selectEl, {
            valueField:  'id',
            labelField:  'text',
            searchField: ['text'],
            placeholder: 'Type a name to search…',
            maxOptions:  60,
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
                option: function (data, escape) {
                    const initials = escape(data.text).split(' ').map(w => w[0]).slice(0, 2).join('').toUpperCase();
                    return `<div class="option" style="display:flex;align-items:center;gap:10px;padding:8px 12px">
                        <div style="width:30px;height:30px;border-radius:50%;background:var(--c-blue-bg);color:var(--c-blue);display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;flex-shrink:0">${initials}</div>
                        <div style="font-size:12.5px;font-weight:600;color:var(--ink)">${escape(data.text)}</div>
                    </div>`;
                },
                no_results: function () {
                    return '<div style="padding:10px 14px;font-size:12px;color:var(--ink-muted)"><i class="fas fa-search" style="margin-right:6px;opacity:.5"></i>No residents found.</div>';
                }
            }
        });
    }

    /* ─────────────────────────────────────────────────────────────────
       Party count badge
    ───────────────────────────────────────────────────────────────── */
    function updatePartyCount() {
        const count = document.querySelectorAll('.party-entry').length;
        const el = document.getElementById('party-count-num');
        if (el) el.textContent = count;
    }

    /* ─────────────────────────────────────────────────────────────────
       Role color + badge update
    ───────────────────────────────────────────────────────────────── */
    function applyRoleStyle(row, role) {
        row.classList.remove('role-complainant', 'role-respondent', 'role-witness');
        row.classList.add('role-' + role);

        const badge = row.querySelector('.party-role-badge');
        if (badge) {
            badge.className = 'party-role-badge ' + role;
            const dot = badge.querySelector('.party-role-dot');
            if (dot) dot.className = 'party-role-dot ' + role;
            const icon = badge.querySelector('i');
            if (icon) icon.className = 'fas ' + (ROLE_ICONS[role] || 'fa-user');
            const lbl = badge.querySelector('.role-label');
            if (lbl) lbl.textContent = ROLE_LABELS[role] || role;
        }
    }

    /* ─────────────────────────────────────────────────────────────────
       Type toggle (Resident / Outsider pills)
    ───────────────────────────────────────────────────────────────── */
    function applyTypeToggle(row, type) {
        const hidden       = row.querySelector('.type-hidden');
        const resFields    = row.querySelector('.resident-fields');
        const outFields    = row.querySelector('.outsider-fields');
        const pills        = row.querySelectorAll('.type-pill');

        if (hidden) hidden.value = type;

        pills.forEach(function (pill) {
            const pt = pill.getAttribute('data-type');
            pill.classList.remove('active-resident', 'active-outsider');
            if (pt === type) {
                pill.classList.add(type === 'resident' ? 'active-resident' : 'active-outsider');
            }
        });

        if (resFields) resFields.style.display = type === 'resident' ? '' : 'none';
        if (outFields) outFields.style.display = type === 'outsider' ? '' : 'none';

        if (type === 'resident') {
            const sel = row.querySelector('.resident-select');
            if (sel) initTomSelect(sel);
        }
    }

    /* ─────────────────────────────────────────────────────────────────
       Build new party row HTML
    ───────────────────────────────────────────────────────────────── */
    function buildPartyRow(index, defaultRole) {
        const role = defaultRole || 'complainant';
        return `
<div class="party-entry role-${role}" data-index="${index}">
    <div class="party-header">
        <div style="display:flex;align-items:center;gap:10px">
            <span class="party-role-badge ${role}">
                <span class="party-role-dot ${role}"></span>
                <i class="fas ${ROLE_ICONS[role]}"></i>
                <span class="role-label">${ROLE_LABELS[role]}</span>
            </span>
            <select name="parties[${index}][role]" class="role-select ds-select" style="height:28px;font-size:11px;padding:0 24px 0 8px;width:auto;border-radius:6px" required>
                <option value="complainant" ${role === 'complainant' ? 'selected' : ''}>Complainant</option>
                <option value="respondent"  ${role === 'respondent'  ? 'selected' : ''}>Respondent</option>
                <option value="witness"     ${role === 'witness'     ? 'selected' : ''}>Witness</option>
            </select>
        </div>
        <div style="display:flex;align-items:center;gap:8px">
            <div class="type-pill-group">
                <button type="button" class="type-pill" data-type="resident">
                    <i class="fas fa-id-card" style="margin-right:4px"></i>Resident
                </button>
                <button type="button" class="type-pill active-outsider" data-type="outsider">
                    <i class="fas fa-user-slash" style="margin-right:4px"></i>Outsider
                </button>
            </div>
            <input type="hidden" name="parties[${index}][type]" class="type-hidden" value="outsider">
            <button type="button" class="ds-action-btn ab-rose remove-party" title="Remove party">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
    <div style="padding:14px">
        <div class="resident-fields" style="display:none">
            <label class="ds-input-label">Search Resident <span style="color:var(--c-rose)">*</span></label>
            <select name="parties[${index}][resident_id]" class="resident-select ds-select" style="width:100%"></select>
            <div class="field-error" id="err-resident-${index}">Please select a resident.</div>
        </div>
        <div class="outsider-fields">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                <div>
                    <label class="ds-input-label">Full Name <span style="color:var(--c-rose)">*</span></label>
                    <input type="text" name="parties[${index}][outsider_name]" class="ds-input outsider-name-input" placeholder="e.g. Juan Dela Cruz">
                    <div class="field-error" id="err-name-${index}">Name is required.</div>
                </div>
                <div>
                    <label class="ds-input-label">Address</label>
                    <input type="text" name="parties[${index}][outsider_address]" class="ds-input" placeholder="e.g. Purok Masagana">
                </div>
            </div>
        </div>
    </div>
</div>`;
    }

    /* ─────────────────────────────────────────────────────────────────
       Bind events to a row
    ───────────────────────────────────────────────────────────────── */
    function bindRow(row) {
        // Role select → update card color + badge
        const roleSelect = row.querySelector('.role-select');
        if (roleSelect) {
            roleSelect.addEventListener('change', function () {
                applyRoleStyle(row, this.value);
            });
        }

        // Type pills
        row.querySelectorAll('.type-pill').forEach(function (pill) {
            pill.addEventListener('click', function () {
                applyTypeToggle(row, this.getAttribute('data-type'));
                clearRowErrors(row);
            });
        });

        // Remove button
        const removeBtn = row.querySelector('.remove-party');
        if (removeBtn && !removeBtn.disabled) {
            removeBtn.addEventListener('click', function () {
                row.remove();
                updatePartyCount();
                clearValidationMsg();
            });
        }

        // Clear error on input
        const nameInput = row.querySelector('.outsider-name-input');
        if (nameInput) {
            nameInput.addEventListener('input', function () {
                this.style.borderColor = '';
                const errEl = row.querySelector('.field-error');
                if (errEl) errEl.classList.remove('show');
            });
        }
    }

    function clearRowErrors(row) {
        row.classList.remove('has-error');
        row.querySelectorAll('.field-error').forEach(e => e.classList.remove('show'));
        row.querySelectorAll('.ds-input').forEach(i => i.style.borderColor = '');
    }

    /* ─────────────────────────────────────────────────────────────────
       Validation message helpers
    ───────────────────────────────────────────────────────────────── */
    function showValidationMsg(text) {
        const msg = document.getElementById('party-validation-msg');
        const txt = document.getElementById('party-validation-text');
        if (msg && txt) { txt.textContent = text; msg.style.display = ''; }
    }

    function clearValidationMsg() {
        const msg = document.getElementById('party-validation-msg');
        if (msg) msg.style.display = 'none';
    }

    /* ─────────────────────────────────────────────────────────────────
       Init existing rows on page load
    ───────────────────────────────────────────────────────────────── */
    document.querySelectorAll('.party-entry').forEach(function (row) {
        const hidden = row.querySelector('.type-hidden');
        const currentType = hidden ? hidden.value : 'outsider';
        applyTypeToggle(row, currentType);

        const roleSelect = row.querySelector('.role-select');
        if (roleSelect) applyRoleStyle(row, roleSelect.value);

        if (currentType === 'resident') {
            const sel = row.querySelector('.resident-select');
            if (sel) initTomSelect(sel);
        }

        bindRow(row);
    });

    updatePartyCount();

    /* ─────────────────────────────────────────────────────────────────
       Add Party button
    ───────────────────────────────────────────────────────────────── */
    const addBtn = document.getElementById('add-party-btn');
    if (addBtn) {
        addBtn.addEventListener('click', function () {
            const container = document.getElementById('parties-container');

            // Suggest a role that's missing (prefer witness after complainant+respondent)
            const existingRoles = Array.from(document.querySelectorAll('.role-select')).map(s => s.value);
            let suggestedRole = 'witness';
            if (!existingRoles.includes('complainant')) suggestedRole = 'complainant';
            else if (!existingRoles.includes('respondent')) suggestedRole = 'respondent';

            const tmp = document.createElement('div');
            tmp.innerHTML = buildPartyRow(partyIndex, suggestedRole).trim();
            const newRow = tmp.firstElementChild;
            container.appendChild(newRow);

            applyTypeToggle(newRow, 'outsider');
            applyRoleStyle(newRow, suggestedRole);
            bindRow(newRow);
            updatePartyCount();
            clearValidationMsg();

            // Scroll new row into view smoothly
            newRow.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            partyIndex++;
        });
    }

    /* ─────────────────────────────────────────────────────────────────
       Form submit validation
    ───────────────────────────────────────────────────────────────── */
    const form = document.getElementById('blotter-form');
    if (form) {
        form.addEventListener('submit', function (e) {
            clearValidationMsg();
            document.querySelectorAll('.party-entry').forEach(r => clearRowErrors(r));

            const rows = document.querySelectorAll('.party-entry');
            let hasComplainant = false;
            let hasRespondent  = false;
            let valid = true;
            let firstErrorRow = null;

            rows.forEach(function (row) {
                const roleEl = row.querySelector('.role-select');
                const role   = roleEl ? roleEl.value : '';
                if (role === 'complainant') hasComplainant = true;
                if (role === 'respondent')  hasRespondent  = true;

                const hidden = row.querySelector('.type-hidden');
                const type   = hidden ? hidden.value : 'outsider';

                if (type === 'outsider') {
                    const nameEl = row.querySelector('.outsider-name-input');
                    if (nameEl && nameEl.value.trim() === '') {
                        nameEl.style.borderColor = 'var(--c-rose)';
                        const errEl = row.querySelector('[id^="err-name-"]');
                        if (errEl) errEl.classList.add('show');
                        row.classList.add('has-error');
                        if (!firstErrorRow) firstErrorRow = row;
                        valid = false;
                    }
                } else {
                    const resEl = row.querySelector('.resident-select');
                    if (resEl && !resEl.value) {
                        const errEl = row.querySelector('[id^="err-resident-"]');
                        if (errEl) errEl.classList.add('show');
                        row.classList.add('has-error');
                        if (!firstErrorRow) firstErrorRow = row;
                        valid = false;
                    }
                }
            });

            if (!hasComplainant || !hasRespondent) {
                e.preventDefault();
                const missing = [];
                if (!hasComplainant) missing.push('Complainant');
                if (!hasRespondent)  missing.push('Respondent');
                showValidationMsg('Missing required role(s): ' + missing.join(' and ') + '. Please add at least one of each.');
                document.getElementById('parties-container').scrollIntoView({ behavior: 'smooth', block: 'start' });
                return;
            }

            if (!valid) {
                e.preventDefault();
                showValidationMsg('Some party entries are incomplete. Please fill in all required fields.');
                if (firstErrorRow) firstErrorRow.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        });
    }

})();

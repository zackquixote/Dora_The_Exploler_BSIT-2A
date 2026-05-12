/**
 * Residents View JavaScript
 * Handles: Tab switching, Print, Certificate Modal, Activity Feed, Status Update (AJAX)
 */

(function($) {
    'use strict';

    // ---------- Global config ----------
    const config = {
        baseUrl: window.BASE_URL || '',
        csrfName: window.CSRF_TOKEN_NAME || '',
        csrfValue: window.CSRF_TOKEN_VALUE || '',
        residentId: window.RESIDENT_ID || '',
        residentName: window.RESIDENT_NAME || '',
        currentUser: window.CURRENT_USER || 'Staff',
        currentRole: window.CURRENT_ROLE || 'staff',
        statusBadges: window.STATUS_BADGES || {}
    };

    // ---------- Tab Switching ----------
    window.switchTab = function(tabId, btn) {
        $('.rv-tab-content').removeClass('active');
        $('.rv-tab-btn').removeClass('active');
        $('#' + tabId).addClass('active');
        $(btn).addClass('active');
    };

    // ---------- Print Profile ----------
    window.printProfile = function() {
        window.print();
    };

    // ---------- Certificate Modal ----------
    window.generateCertificate = function() {
        // Support the current template's custom overlay modal
        const overlay = document.getElementById('certModalOverlay');
        if (overlay) {
            overlay.classList.add('show');
            return;
        }

        // Backward compatible (if a Bootstrap modal exists)
        const $bsModal = $('#generateCertificateModal');
        if ($bsModal.length) $bsModal.modal('show');
    };

    // Log certificate generation on form submit — no-op, server logs it
    $(document).on('submit', '#certModalOverlay form, #generateCertificateModal form', function() {
        // Server-side logging handles this via LogModel
    });

    // ---------- Activity Feed (server-side logs) ----------
    function timeAgo(isoString) {
        const diff = Math.floor((Date.now() - new Date(isoString)) / 1000);
        if (diff < 60) return 'Just now';
        if (diff < 3600) return Math.floor(diff / 60) + 'm ago';
        if (diff < 86400) return Math.floor(diff / 3600) + 'h ago';
        if (diff < 604800) return Math.floor(diff / 86400) + 'd ago';
        return new Date(isoString).toLocaleDateString('en-PH', { month: 'short', day: 'numeric' });
    }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str).replace(/[&<>]/g, function(m) {
            if (m === '&') return '&amp;';
            if (m === '<') return '&lt;';
            if (m === '>') return '&gt;';
            return m;
        });
    }

    function renderActivityFeed(logs) {
        const $feed = $('#rv-activity-feed');
        const $count = $('#rv-activity-count');
        if (!$feed.length) return;

        if (!logs || !logs.length) {
            $feed.html('<div style="text-align:center;padding:24px;color:var(--ink-soft);font-size:11px"><i class="fas fa-history" style="opacity:.3;display:block;margin-bottom:6px;font-size:18px"></i>No activity recorded.</div>');
            if ($count.length) $count.hide();
            return;
        }

        if ($count.length) {
            $count.text(logs.length).show();
        }

        $feed.html(logs.map(function(log) {
            const action = log.ACTION || '';
            const user   = log.USER_NAME || 'System';
            const ts     = log.TIMELOG || log.DATELOG || '';
            let icon = 'fa-eye', cls = 'ds-ai-view';
            const a = action.toLowerCase();
            if (a.includes('delete') || a.includes('remove')) { icon = 'fa-trash-alt'; cls = 'ds-ai-delete'; }
            else if (a.includes('edit') || a.includes('update')) { icon = 'fa-edit'; cls = 'ds-ai-edit'; }
            else if (a.includes('create') || a.includes('add')) { icon = 'fa-plus-circle'; cls = 'ds-ai-create'; }
            else if (a.includes('print')) { icon = 'fa-print'; cls = 'ds-ai-print'; }
            else if (a.includes('certif')) { icon = 'fa-file-alt'; cls = 'ds-ai-cert'; }

            return `<div class="ds-activity-item">
                <div class="ds-activity-icon ${cls}"><i class="fas ${icon}"></i></div>
                <div>
                    <div class="ds-activity-action">${escapeHtml(action)}</div>
                    <div class="ds-activity-meta">by <strong>${escapeHtml(user)}</strong> · ${ts ? timeAgo(ts) : ''}</div>
                </div>
            </div>`;
        }).join(''));
    }

    function loadActivityFeed() {
        const $feed = $('#rv-activity-feed');
        if (!$feed.length || !config.residentId) return;

        $.get(config.baseUrl + 'resident/activity/' + config.residentId, function(res) {
            if (res.status === 'success') {
                renderActivityFeed(res.logs);
            } else {
                $feed.html('<div style="text-align:center;padding:24px;color:var(--ink-soft);font-size:11px">Could not load activity.</div>');
            }
        }, 'json').fail(function() {
            $feed.html('<div style="text-align:center;padding:24px;color:var(--ink-soft);font-size:11px">Could not load activity.</div>');
        });
    }

    // ---------- Status Update (AJAX) ----------
    function initStatusEditor() {
        const $display = $('#status-display');
        const $editor = $('#status-editor');
        const $badge = $('#status-badge');
        const $editIcon = $('#edit-status-icon');
        const $saveIcon = $('#save-status-icon');
        const $cancelIcon = $('#cancel-status-icon');
        const $select = $('#status-select');
        let originalStatus = $select.val();

        $editIcon.on('click', function(e) {
            e.stopPropagation();
            originalStatus = $select.val();
            $display.hide();
            $editor.show();
            $select.focus();
        });

        $cancelIcon.on('click', function(e) {
            e.stopPropagation();
            $select.val(originalStatus);
            $editor.hide();
            $display.show();
        });

        $saveIcon.on('click', function(e) {
            e.stopPropagation();
            const newStatus = $select.val();
            const payload = { status: newStatus };
            payload[config.csrfName] = config.csrfValue;

            $.post(`${config.baseUrl}resident/updateStatus/${config.residentId}`, payload, function(response) {
                if (response.status === 'success') {
                    const badgeClass = config.statusBadges[newStatus] || 'ds-badge-gray';
                    $badge
                        .text(newStatus.charAt(0).toUpperCase() + newStatus.slice(1))
                        .attr('class', 'ds-badge ' + badgeClass);
                    if (response.csrf_hash && config.csrfName) {
                        config.csrfValue = response.csrf_hash;
                        $('input[name="' + config.csrfName + '"]').val(response.csrf_hash);
                    }
                    originalStatus = newStatus;
                    $editor.hide();
                    $display.show();
                } else {
                    alert(response.message || 'Update failed');
                }
            }, 'json').fail(() => alert('Update failed. Please try again.'));
        });
    }

    // ---------- Initialize ----------
    $(document).ready(function() {
        loadActivityFeed();
        setInterval(loadActivityFeed, 60000);
        initStatusEditor();
    });

})(jQuery);
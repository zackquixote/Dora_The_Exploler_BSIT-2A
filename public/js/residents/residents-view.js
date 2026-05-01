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
        Swal.fire({
            title: 'Print Profile',
            text: `Preparing print for ${config.residentName}...`,
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#2B4FBF',
            confirmButtonText: 'Print Now',
            cancelButtonText: 'Cancel'
        }).then(result => {
            if (result.isConfirmed) {
                window.print();
                logActivity('Printed Profile', '', 'print');
            }
        });
    };

    // ---------- Certificate Modal ----------
    window.generateCertificate = function() {
        $('#generateCertificateModal').modal('show');
    };

    // Log certificate generation on form submit
    $(document).on('submit', '#generateCertificateModal form', function() {
        const certType = $('[name="certificate_type"]', this).val() || 'Certificate';
        logActivity('Generated Certificate', certType, 'cert');
    });

    // ---------- Activity Feed (localStorage) ----------
    const ACTIVITY_KEY = 'rv_activity_log';
    const MAX_ACTIVITIES = 50;

    function getActivities() {
        try {
            return JSON.parse(localStorage.getItem(ACTIVITY_KEY)) || [];
        } catch { return []; }
    }

    function saveActivities(activities) {
        localStorage.setItem(ACTIVITY_KEY, JSON.stringify(activities));
    }

    function logActivity(action, detail, type) {
        const activities = getActivities();
        activities.unshift({
            id: Date.now(),
            action: action,
            detail: detail || '',
            type: type || 'view',
            user: config.currentUser,
            role: config.currentRole,
            residentId: config.residentId,
            residentName: config.residentName,
            timestamp: new Date().toISOString()
        });
        if (activities.length > MAX_ACTIVITIES) activities.splice(MAX_ACTIVITIES);
        saveActivities(activities);
        renderActivityFeed();
    }

    function timeAgo(isoString) {
        const diff = Math.floor((Date.now() - new Date(isoString)) / 1000);
        if (diff < 60) return 'Just now';
        if (diff < 3600) return Math.floor(diff / 60) + 'm ago';
        if (diff < 86400) return Math.floor(diff / 3600) + 'h ago';
        if (diff < 604800) return Math.floor(diff / 86400) + 'd ago';
        return new Date(isoString).toLocaleDateString('en-PH', { month: 'short', day: 'numeric' });
    }

    const ICON_MAP = {
        cert: { icon: 'fas fa-file-alt', cls: 'cert' },
        print: { icon: 'fas fa-print', cls: 'print' },
        edit: { icon: 'fas fa-edit', cls: 'edit' },
        view: { icon: 'fas fa-eye', cls: 'view' }
    };

    function renderActivityFeed() {
        const $feed = $('#rv-activity-feed');
        const $count = $('#rv-activity-count');
        if (!$feed.length) return;

        const all = getActivities();
        const activities = all.filter(a => String(a.residentId) === String(config.residentId));

        if ($count.length) {
            $count.text(activities.length);
            $count.css('display', activities.length ? 'inline-flex' : 'none');
        }

        if (!activities.length) {
            $feed.html(`<div class="rv-activity-empty"><i class="fas fa-history"></i><p>No activity yet for this profile.</p></div>`);
            return;
        }

        $feed.html(activities.slice(0, 15).map(a => {
            const iconData = ICON_MAP[a.type] || ICON_MAP.view;
            return `
            <div class="rv-activity-item">
                <div class="rv-activity-icon ${iconData.cls}"><i class="${iconData.icon}"></i></div>
                <div class="rv-activity-content">
                    <div class="rv-activity-action">${escapeHtml(a.action)}${a.detail ? ` — <span style="color:var(--rv-text-muted);font-weight:400">${escapeHtml(a.detail)}</span>` : ''}</div>
                    <div class="rv-activity-user">by <strong>${escapeHtml(a.user)}</strong> <span style="text-transform:capitalize;font-size:0.68rem;background:var(--rv-gray-bg);padding:1px 5px;border-radius:4px;margin-left:2px;">${escapeHtml(a.role)}</span></div>
                    <div class="rv-activity-time"><i class="fas fa-clock" style="font-size:0.65rem;margin-right:3px;opacity:0.6"></i>${timeAgo(a.timestamp)}</div>
                </div>
            </div>`;
        }).join(''));
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
                    const badgeClass = config.statusBadges[newStatus] || 'rv-badge-secondary';
                    $badge.text(newStatus.charAt(0).toUpperCase() + newStatus.slice(1))
                          .attr('class', 'rv-badge ' + badgeClass);
                    originalStatus = newStatus;
                    $editor.hide();
                    $display.show();
                    logActivity('Changed Status', `to ${newStatus}`, 'edit');
                } else {
                    alert(response.message || 'Update failed');
                }
            }, 'json').fail(() => alert('Update failed. Please try again.'));
        });
    }

    // ---------- Initialize ----------
    $(document).ready(function() {
        // Log profile view once per session
        const sessionKey = `rv_viewed_${config.residentId}`;
        if (!sessionStorage.getItem(sessionKey)) {
            logActivity('Viewed Profile', '', 'view');
            sessionStorage.setItem(sessionKey, '1');
        }
        renderActivityFeed();
        setInterval(renderActivityFeed, 30000);
        initStatusEditor();
    });

})(jQuery);
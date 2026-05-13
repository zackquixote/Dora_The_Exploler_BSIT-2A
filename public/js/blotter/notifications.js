/**
 * Blotter Hearing Notifications
 * Fetches upcoming hearings and updates the navbar bell.
 */
(function($) {
    'use strict';

    // Use window.baseUrl set by the template (always has trailing slash)
    var APP_ROOT = (window.baseUrl || (window.location.origin + '/'));

    let NotificationCenter = {
        config: {
            refreshInterval: 60000,
            notificationDays: 3
        },

        init: function() {
            this.loadNotifications();
            setInterval(() => this.loadNotifications(), this.config.refreshInterval);
        },

        loadNotifications: function() {
            $.ajax({
                url: APP_ROOT + 'blotter/getUpcomingNotifications',
                type: 'GET',
                headers: { 'Accept': 'application/json' },
                data: { days: this.config.notificationDays },
                dataType: 'json',
                success: (res) => {
                    if (res.status === 'success') {
                        const list = Array.isArray(res.notifications) ? res.notifications : [];
                        this.renderBadge(typeof res.count === 'number' ? res.count : list.length);
                        this.renderDropdown(list);
                        if (res.csrf_hash) this.updateCsrf(res.csrf_hash);
                    }
                },
                error: (xhr) => {
                    if (xhr.status === 401) {
                        this.renderBadge(0);
                        this.renderDropdown([]);
                        let target = APP_ROOT + 'login';
                        try {
                            const body = JSON.parse(xhr.responseText || '{}');
                            if (body.redirect) target = body.redirect;
                        } catch (e) { /* use default */ }
                        window.location.href = target;
                        return;
                    }
                    console.error('Notification error:', xhr.status, xhr.statusText);
                }
            });
        },

        renderBadge: function(count) {
            let $badge = $('.notifications-badge');
            document.title = document.title.replace(/^\(\d+\)\s+/, '');
            if (count > 0) {
                $badge.text(count).show();
                document.title = '(' + count + ') ' + document.title;
            } else {
                $badge.empty().hide();
            }
        },

        renderDropdown: function(notifications) {
            let $dropdown = $('#notifications-dropdown-menu');
            if (!$dropdown.length) return;
            if (!notifications.length) {
                $dropdown.html('<li><span class="dropdown-item text-muted text-center py-3"><i class="fas fa-check-circle text-success me-2"></i>No upcoming hearings</span></li>');
                return;
            }
            let html = '';
            $.each(notifications, function(i, n) {
                let iconColor = n.type === 'danger' ? 'text-danger' : 'text-warning';
                let alertBg   = n.type === 'danger' ? 'bg-danger bg-opacity-10' : '';
                html += `<li><a class="dropdown-item ${alertBg}" href="${n.url}" style="border-bottom:1px solid #f1f5f9; white-space: normal;">
                            <div class="d-flex align-items-start py-1">
                                <div class="flex-shrink-0 mt-1"><i class="fas fa-gavel ${iconColor}"></i></div>
                                <div class="flex-grow-1 ms-3">
                                    <strong class="${n.type === 'danger' ? 'text-danger' : ''}">${n.title} – ${n.case_number}</strong><br>
                                    <small>${n.message}${n.time ? ' at ' + n.time : ''}</small>
                                    <div class="small text-muted mt-1">📍 ${n.venue || 'Venue TBA'}</div>
                                </div>
                            </div>
                         </a></li>`;
            });
            html += '<li><hr class="dropdown-divider"></li>';
            html += '<li><a class="dropdown-item text-center small" href="' + APP_ROOT + 'blotter">View all blotter cases →</a></li>';
            $dropdown.html(html);
        },

        updateCsrf: function(newHash) {
            $('input[type="hidden"][name="csrf_test_name"], input[type="hidden"][name*="csrf"]').val(newHash);
        }
    };

    $(document).ready(() => {
        if ($('#notifications-bell').length) NotificationCenter.init();
    });
})(jQuery);


/**
 * Blotter Hearing Notifications
 * Fetches upcoming hearings and updates the navbar bell.
 */
(function($) {
    'use strict';

    // Derive the application root URL from this script's own src attribute.
    // e.g. "http://localhost:8080/js/blotter/notifications.js" → "http://localhost:8080/"
    function getAppRoot() {
        var scripts = document.getElementsByTagName('script');
        for (var i = 0; i < scripts.length; i++) {
            var src = scripts[i].src || '';
            var marker = 'js/blotter/notifications.js';
            var idx = src.indexOf(marker);
            if (idx !== -1) {
                return src.substring(0, idx);
            }
        }
        // Fallback: use window.baseUrl or origin
        return window.baseUrl || (window.location.origin + '/');
    }

    var APP_ROOT = getAppRoot();

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
                data: { days: this.config.notificationDays },
                dataType: 'json',
                success: (res) => {
                    if (res.status === 'success') {
                        this.renderBadge(res.count);
                        this.renderDropdown(res.notifications);
                        if (res.csrf_hash) this.updateCsrf(res.csrf_hash);
                    }
                },
                error: (xhr, status, error) => console.error('Notification error:', error)
            });
        },

        renderBadge: function(count) {
            let $badge = $('.notifications-badge');
            if (count > 0) {
                $badge.text(count).show();
                let title = document.title;
                if (!title.match(/^\(\d+\)/)) document.title = '(' + count + ') ' + title;
            } else {
                $badge.empty().hide();
                document.title = document.title.replace(/^\(\d+\)\s/, '');
            }
        },

        renderDropdown: function(notifications) {
            let $dropdown = $('#notifications-dropdown-menu');
            if (!$dropdown.length) return;
            if (!notifications.length) {
                $dropdown.html('<div class="dropdown-item text-muted text-center">No upcoming hearings</div>');
                return;
            }
            let html = '';
            $.each(notifications, (i, n) => {
                let iconColor = n.type === 'danger' ? 'text-danger' : 'text-warning';
                let alertBg   = n.type === 'danger' ? 'bg-danger bg-opacity-10' : '';
                html += `<a class="dropdown-item ${alertBg}" href="${n.url}" style="border-bottom:1px solid #f1f5f9; white-space: normal;">
                            <div class="d-flex align-items-start py-1">
                                <div class="flex-shrink-0 mt-1"><i class="fas fa-gavel ${iconColor}"></i></div>
                                <div class="flex-grow-1 ms-3">
                                    <strong class="${n.type === 'danger' ? 'text-danger' : ''}">${n.title} – ${n.case_number}</strong><br>
                                    <small>${n.message} ${n.time ? 'at ' + n.time : ''}</small>
                                    <div class="small text-muted mt-1">📍 ${n.venue || 'Venue TBA'}</div>
                                </div>
                            </div>
                         </a>`;
            });
            html += '<div class="dropdown-divider"></div>';
            html += '<a class="dropdown-item text-center small" href="' + APP_ROOT + 'blotter?filter=upcoming">View all upcoming hearings →</a>';
            $dropdown.html(html);
        },

        updateCsrf: function(newHash) {
            $('input[name="<?= csrf_token() ?>"]').val(newHash);
        }
    };

    $(document).ready(() => {
        if ($('#notifications-bell').length) NotificationCenter.init();
    });
})(jQuery);
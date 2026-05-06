/**
 * Blotter Hearing Notifications
 * Fetches upcoming hearings and updates the navbar bell.
 */
(function($) {
    'use strict';

    let NotificationCenter = {
        config: {
            refreshInterval: 60000,
            notificationDays: 3,
            apiUrl: '/blotter/getUpcomingNotifications'
        },

        init: function() {
            this.loadNotifications();
            setInterval(() => this.loadNotifications(), this.config.refreshInterval);
        },

        loadNotifications: function() {
            $.ajax({
                url: this.config.apiUrl,
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
                html += `<a class="dropdown-item" href="${n.url}">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0"><i class="fas fa-gavel text-warning"></i></div>
                                <div class="flex-grow-1 ms-3">
                                    <strong>${n.title} – ${n.case_number}</strong><br>
                                    <small>${n.message} ${n.time ? 'at ' + n.time : ''}</small>
                                    <div class="small text-muted">📍 ${n.venue || 'Venue TBA'}</div>
                                </div>
                            </div>
                         </a>`;
            });
            html += '<div class="dropdown-divider"></div>';
            html += '<a class="dropdown-item text-center small" href="' + window.baseUrl + 'blotter?filter=upcoming">View all upcoming hearings →</a>';
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
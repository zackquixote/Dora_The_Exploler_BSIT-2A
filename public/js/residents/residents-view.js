/**
 * Residents View JavaScript
 * Handles resident profile view actions
 * File location: public/js/residents/residents-view.js
 */

(function() {
    'use strict';
    
    // ==========================================
    // CONFIGURATION & STATE
    // ==========================================
    
    // Access global variables passed by PHP View
    // We use 'window' object because inside IIFE scope
    const config = {
        baseUrl:      typeof BASE_URL !== 'undefined' ? BASE_URL : '',
        csrfName:     typeof CSRF_TOKEN_NAME !== 'undefined' ? CSRF_TOKEN_NAME : '',
        csrfValue:    typeof CSRF_TOKEN_VALUE !== 'undefined' ? CSRF_TOKEN_VALUE : '',
        residentId:   typeof RESIDENT_ID !== 'undefined' ? RESIDENT_ID : '',
        residentName: typeof RESIDENT_NAME !== 'undefined' ? RESIDENT_NAME : ''
    };
    
    // ==========================================
    // INITIALIZATION
    // ==========================================
    
    function init() {
        bindEvents();
        initializeTabs();
    }
    
    // ==========================================
    // EVENT BINDING
    // ==========================================
    
    function bindEvents() {
        // 1. Tab Switching (using class .rv-tab-btn)
        document.querySelectorAll('.rv-tab-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                const tabId = this.getAttribute('onclick')?.match(/'([^']+)'/)[1];
                if (tabId) {
                    switchTab(tabId, this);
                }
            });
        });

        // 2. Print Profile
        const printBtn = document.querySelector('a[onclick="printProfile()"]');
        if (printBtn) {
            printBtn.addEventListener('click', (e) => {
                e.preventDefault();
                window.print();
            });
        }

        // 3. Generate Certificate
        const certBtn = document.querySelector('a[onclick="generateCertificate()"]');
        if (certBtn) {
            certBtn.addEventListener('click', (e) => {
                e.preventDefault();
                openCertificateModal();
            });
        }
    }

    /**
     * Initialize tabs from URL hash (e.g., #status)
     */
    function initializeTabs() {
        const hash = window.location.hash.substring(1); // Remove # sign
        if (hash) {
            const targetBtn = document.querySelector(`.rv-tab-btn[onclick*="switchTab('${hash}', this)"]`);
            if (targetBtn) {
                switchTab(hash, targetBtn);
            }
        }
    }
    
    // ==========================================
    // CORE FUNCTIONS
    // ==========================================
    
    /**
     * Switch Tabs UI
     */
    function switchTab(tabId, btn) {
        // Hide all contents
        document.querySelectorAll('.rv-tab-content').forEach(el => el.classList.remove('active'));
        // Deactivate all buttons
        document.querySelectorAll('.rv-tab-btn').forEach(el => el.classList.remove('active'));
        
        // Activate selected
        const targetContent = document.getElementById(tabId);
        if (targetContent) {
            targetContent.classList.add('active');
        }
        if (btn) {
            btn.classList.add('active');
        }
    }

    /**
     * Open Certificate Modal (Bootstrap 4 jQuery)
     */
    function openCertificateModal() {
        // Check if jQuery is available (Bootstrap requires it)
        if (typeof jQuery !== 'undefined') {
            jQuery('#generateCertificateModal').modal('show');
        } else {
            console.error('jQuery is not loaded. Cannot open modal.');
            showNotification('Error: jQuery not found.', 'error');
        }
    }

    /**
     * Handle Delete Resident
     */
    function handleDelete() {
        const options = {
            title: 'Delete Resident?',
            text: `Are you sure you want to delete ${config.residentName}? This action cannot be undone!`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it',
            cancelButtonText: 'Cancel'
        };
        
        if (typeof Swal !== 'undefined') {
            Swal.fire(options).then((result) => {
                if (result.isConfirmed) {
                    executeDelete();
                }
            });
        } else {
            // Fallback to native confirm if SweetAlert2 fails
            if (confirm(`Are you sure you want to delete ${config.residentName}?`)) {
                executeDelete();
            }
        }
    }

    /**
     * Execute Delete Request via AJAX
     */
    function executeDelete() {
        const data = {};
        data[config.csrfName] = config.csrfValue;
        
        if (typeof jQuery === 'undefined') {
            showNotification('jQuery not loaded. Cannot delete.', 'error');
            return;
        }

        jQuery.ajax({
            url: `${config.baseUrl}resident/delete/${config.residentId}`,
            type: 'POST',
            data: data,
            dataType: 'json',
            beforeSend: function() {
                // Optional: Show loading spinner
                document.body.style.cursor = 'wait';
            },
            success: function(response) {
                if (response.status === 'success') {
                    showNotification('Resident deleted successfully', 'success');
                    setTimeout(() => {
                        window.location.href = `${config.baseUrl}resident`;
                    }, 1500);
                } else {
                    showNotification(response.message || 'Failed to delete resident', 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error('Delete Error:', status, error);
                showNotification('An error occurred while deleting', 'error');
            },
            complete: function() {
                document.body.style.cursor = 'default';
            }
        });
    }

    /**
     * Show Notification Toast
     */
    function showNotification(message, type = 'info') {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                text: message,
                icon: type,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        } else {
            alert(message);
        }
    }

    // ==========================================
    // PUBLIC API (Expose functions if needed globally)
    // ==========================================
    window.switchTab = switchTab;
    window.printProfile = () => window.print();
    window.generateCertificate = openCertificateModal;
    window.deleteResident = handleDelete;

    // Start Init
    document.addEventListener('DOMContentLoaded', init);
    /**
 * residents-view.js
 * Handles: Tab switching, Print, Certificate Modal, Activity Feed
 */

/* ============================================================
   1. TAB SWITCHING
   ============================================================ */
function switchTab(tabId, btn) {
    document.querySelectorAll('.rv-tab-content').forEach(el => el.classList.remove('active'));
    document.querySelectorAll('.rv-tab-btn').forEach(el => el.classList.remove('active'));
    document.getElementById(tabId).classList.add('active');
    btn.classList.add('active');
}

/* ============================================================
   2. ACTIVITY LOGGER — LocalStorage-backed per resident
   ============================================================ */
const ACTIVITY_KEY = 'rv_activity_log';
const MAX_ACTIVITIES = 50;

/**
 * Log an action to localStorage.
 * @param {string} action  - e.g. 'Generated Certificate', 'Printed Profile'
 * @param {string} detail  - extra detail e.g. 'Barangay Clearance'
 * @param {string} type    - icon type: 'cert' | 'print' | 'edit' | 'view'
 */
function logActivity(action, detail, type) {
    const activities = getActivities();

    const entry = {
        id: Date.now(),
        action,
        detail: detail || '',
        type: type || 'view',
        user: window.CURRENT_USER || 'Staff',
        role: window.CURRENT_ROLE || 'staff',
        residentId: window.RESIDENT_ID || '',
        residentName: window.RESIDENT_NAME || 'Resident',
        timestamp: new Date().toISOString()
    };

    activities.unshift(entry); // Add to front

    // Cap the log
    if (activities.length > MAX_ACTIVITIES) {
        activities.splice(MAX_ACTIVITIES);
    }

    localStorage.setItem(ACTIVITY_KEY, JSON.stringify(activities));
    renderActivityFeed();
}

function getActivities() {
    try {
        return JSON.parse(localStorage.getItem(ACTIVITY_KEY)) || [];
    } catch {
        return [];
    }
}

/* ============================================================
   3. ACTIVITY FEED RENDERER
   ============================================================ */
const ICON_MAP = {
    cert:  { icon: 'fas fa-file-alt',  cls: 'cert'  },
    print: { icon: 'fas fa-print',     cls: 'print' },
    edit:  { icon: 'fas fa-edit',      cls: 'edit'  },
    view:  { icon: 'fas fa-eye',       cls: 'view'  }
};

function timeAgo(isoString) {
    const diff = Math.floor((Date.now() - new Date(isoString)) / 1000);
    if (diff < 60)       return 'Just now';
    if (diff < 3600)     return Math.floor(diff / 60) + 'm ago';
    if (diff < 86400)    return Math.floor(diff / 3600) + 'h ago';
    if (diff < 604800)   return Math.floor(diff / 86400) + 'd ago';
    return new Date(isoString).toLocaleDateString('en-PH', { month: 'short', day: 'numeric' });
}

function renderActivityFeed() {
    const feed     = document.getElementById('rv-activity-feed');
    const countEl  = document.getElementById('rv-activity-count');
    if (!feed) return;

    const all        = getActivities();
    // Show only activities for this resident, or all if no residentId
    const resId      = window.RESIDENT_ID ? String(window.RESIDENT_ID) : null;
    const activities = resId
        ? all.filter(a => String(a.residentId) === resId)
        : all;

    if (countEl) {
        countEl.textContent = activities.length;
        countEl.style.display = activities.length ? 'inline-flex' : 'none';
    }

    if (!activities.length) {
        feed.innerHTML = `
            <div class="rv-activity-empty">
                <i class="fas fa-history"></i>
                <p>No activity yet for this profile.</p>
            </div>`;
        return;
    }

    feed.innerHTML = activities.slice(0, 15).map(a => {
        const iconData = ICON_MAP[a.type] || ICON_MAP.view;
        return `
        <div class="rv-activity-item">
            <div class="rv-activity-icon ${iconData.cls}">
                <i class="${iconData.icon}"></i>
            </div>
            <div class="rv-activity-content">
                <div class="rv-activity-action">${escapeHtml(a.action)}${a.detail ? ` — <span style="color:var(--rv-text-muted);font-weight:400">${escapeHtml(a.detail)}</span>` : ''}</div>
                <div class="rv-activity-user">by <strong>${escapeHtml(a.user)}</strong> <span style="text-transform:capitalize;font-size:0.68rem;background:var(--rv-gray-bg);padding:1px 5px;border-radius:4px;margin-left:2px;">${escapeHtml(a.role)}</span></div>
                <div class="rv-activity-time"><i class="fas fa-clock" style="font-size:0.65rem;margin-right:3px;opacity:0.6"></i>${timeAgo(a.timestamp)}</div>
            </div>
        </div>`;
    }).join('');
}

function escapeHtml(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

/* ============================================================
   4. PRINT PROFILE
   ============================================================ */
function printProfile() {
    logActivity('Printed Profile', '', 'print');

    Swal.fire({
        title: 'Print Profile',
        text: `Preparing print for ${window.RESIDENT_NAME}...`,
        icon: 'info',
        showCancelButton: true,
        confirmButtonColor: '#2B4FBF',
        cancelButtonColor: '#64748B',
        confirmButtonText: 'Print Now',
        cancelButtonText: 'Cancel',
        customClass: { popup: 'swal-rv' }
    }).then(result => {
        if (result.isConfirmed) {
            window.print();
        }
    });
}

/* ============================================================
   5. GENERATE CERTIFICATE MODAL
   ============================================================ */
function generateCertificate() {
    $('#generateCertificateModal').modal('show');
}

// Listen for the certificate form submit to log the action
document.addEventListener('DOMContentLoaded', function () {
    const certForm = document.querySelector('#generateCertificateModal form');
    if (certForm) {
        certForm.addEventListener('submit', function () {
            const certType = this.querySelector('[name="certificate_type"]')?.value || 'Certificate';
            logActivity('Generated Certificate', certType, 'cert');
        });
    }
});

/* ============================================================
   6. INIT — Run on page load
   ============================================================ */
document.addEventListener('DOMContentLoaded', function () {
    // Log a "viewed profile" activity on load (debounced — once per session)
    const sessionKey = `rv_viewed_${window.RESIDENT_ID}`;
    if (!sessionStorage.getItem(sessionKey)) {
        logActivity('Viewed Profile', '', 'view');
        sessionStorage.setItem(sessionKey, '1');
    }

    // Render the activity feed
    renderActivityFeed();

    // Auto-refresh every 30 seconds in case another tab logs something
    setInterval(renderActivityFeed, 30000);
});

/* ============================================================
   7. CLEAR ACTIVITY (Optional — for admin use, call from console)
   ============================================================ */
function clearResidentActivity() {
    const activities = getActivities().filter(
        a => String(a.residentId) !== String(window.RESIDENT_ID)
    );
    localStorage.setItem(ACTIVITY_KEY, JSON.stringify(activities));
    renderActivityFeed();
}
})();
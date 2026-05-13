/**
 * BMIS Core JavaScript
 * 
 * Extracted from theme/template.php and theme/admin/template.php
 * into a single cacheable, testable file.
 * 
 * Handles: mobile sidebar toggle, AJAX CSRF, double-submit guard,
 * SweetAlert confirmations, global search, dark mode, keyboard shortcuts.
 */
document.addEventListener('DOMContentLoaded', function() {
    const sendProbe = (hypothesisId, location, message, data) => {
        fetch((window.baseUrl || '/') + 'debug/probe', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ hypothesisId, location, message, data })
        }).catch(() => {});
    };

    /* ─── Mobile Sidebar Toggle ─────────────────────────────────── */
    const sidebar = document.getElementById('mainSidebar');
    const sidebarLinks = document.querySelectorAll('#mainSidebar .sb-link');
    // #region agent log
    fetch('http://127.0.0.1:7249/ingest/d1d75b02-5b55-464a-9858-e2796691b14a',{method:'POST',headers:{'Content-Type':'application/json','X-Debug-Session-Id':'0646ff'},body:JSON.stringify({sessionId:'0646ff',runId:'pre-fix',hypothesisId:'H1',location:'public/js/theme/bmis-core.js:15',message:'sidebar init state',data:{hasSidebar:!!sidebar,windowWidth:window.innerWidth,linkCount:sidebarLinks.length,sidebarClasses:sidebar?sidebar.className:''},timestamp:Date.now()})}).catch(()=>{});
    // #endregion
    if (window.innerWidth <= 768 && sidebar) {
        const toggler = document.createElement('button');
        toggler.innerHTML = '<i class="fas fa-bars"></i>';
        toggler.className = 'tb-icon-btn';
        toggler.style.cssText = 'position:fixed;top:10px;left:10px;z-index:1050;';
        toggler.onclick = () => sidebar.classList.toggle('open');
        document.body.appendChild(toggler);
    }
    if (sidebar) {
        ['resident', 'logs', 'logout'].forEach((key) => {
            const link = document.querySelector(`#mainSidebar .sb-link[href*="${key}"]`);
            if (!link) return;
            const rect = link.getBoundingClientRect();
            const probeX = Math.max(0, Math.floor(rect.left + rect.width / 2));
            const probeY = Math.max(0, Math.floor(rect.top + rect.height / 2));
            const topEl = document.elementFromPoint(probeX, probeY);
            const styles = window.getComputedStyle(link);
            sendProbe('H16', 'public/js/theme/bmis-core.js:39', 'sidebar link clickability probe (same-origin)', {
                key,
                href: link.getAttribute('href'),
                pointerEvents: styles.pointerEvents,
                zIndex: styles.zIndex,
                topElementTag: topEl ? topEl.tagName : null,
                topElementId: topEl ? topEl.id : null,
                topElementClass: topEl ? topEl.className : null
            });
        });
    }
    sidebarLinks.forEach(function(link) {
        link.addEventListener('click', function(e) {
            sendProbe('H17', 'public/js/theme/bmis-core.js:56', 'sidebar link clicked (same-origin)', {
                text: (this.textContent || '').trim(),
                href: this.getAttribute('href'),
                defaultPrevented: e.defaultPrevented,
                button: e.button,
                ctrlKey: e.ctrlKey,
                metaKey: e.metaKey
            });
        }, true);
        // Force deterministic navigation for normal left-clicks on sidebar links.
        // Some environments focus the link but do not perform anchor navigation.
        link.addEventListener('click', function(e) {
            if (e.defaultPrevented) {
                sendProbe('H21', 'public/js/theme/bmis-core.js:74', 'forced nav skipped: defaultPrevented', {
                    text: (this.textContent || '').trim(),
                    href: this.getAttribute('href')
                });
                return;
            }
            if (e.button !== 0) {
                sendProbe('H21', 'public/js/theme/bmis-core.js:81', 'forced nav skipped: non-left button', {
                    text: (this.textContent || '').trim(),
                    href: this.getAttribute('href'),
                    button: e.button
                });
                return;
            }
            if (e.ctrlKey || e.metaKey || e.shiftKey || e.altKey) {
                sendProbe('H21', 'public/js/theme/bmis-core.js:90', 'forced nav skipped: modifier key', {
                    text: (this.textContent || '').trim(),
                    href: this.getAttribute('href'),
                    ctrlKey: e.ctrlKey,
                    metaKey: e.metaKey,
                    shiftKey: e.shiftKey,
                    altKey: e.altKey
                });
                return;
            }
            const href = this.getAttribute('href');
            if (!href || href === '#') {
                sendProbe('H21', 'public/js/theme/bmis-core.js:102', 'forced nav skipped: empty href', {
                    text: (this.textContent || '').trim(),
                    href: href || null
                });
                return;
            }
            sendProbe('H19', 'public/js/theme/bmis-core.js:74', 'forced sidebar navigation', {
                text: (this.textContent || '').trim(),
                href
            });
            e.preventDefault();
            window.location.assign(href);
        }, false);
    });
    document.addEventListener('click', function(e) {
        const insideSidebar = !!e.target.closest('#mainSidebar');
        if (!insideSidebar) return;
        const topEl = document.elementFromPoint(e.clientX, e.clientY);
        sendProbe('H18', 'public/js/theme/bmis-core.js:69', 'sidebar area document click (same-origin)', {
            targetTag: e.target.tagName,
            targetId: e.target.id || null,
            targetClass: e.target.className || null,
            topElementTag: topEl ? topEl.tagName : null,
            topElementId: topEl ? topEl.id : null,
            topElementClass: topEl ? topEl.className : null
        });
    }, true);

    /* ─── Dark Mode Toggle ──────────────────────────────────────── */
    const toggleBtn = document.getElementById('darkModeToggle');
    if (toggleBtn) {
        const icon = toggleBtn.querySelector('i');
        if (localStorage.getItem('theme') === 'dark') {
            icon.classList.remove('fa-moon');
            icon.classList.add('fa-sun');
        }
        toggleBtn.addEventListener('click', function() {
            document.body.classList.toggle('dark-theme');
            if (document.body.classList.contains('dark-theme')) {
                localStorage.setItem('theme', 'dark');
                icon.classList.remove('fa-moon');
                icon.classList.add('fa-sun');
            } else {
                localStorage.setItem('theme', 'light');
                icon.classList.remove('fa-sun');
                icon.classList.add('fa-moon');
            }
        });
    }

    /* ─── Desktop Search Visibility ─────────────────────────────── */
    if (window.innerWidth > 768) {
        const searchContainer = document.getElementById('desktop-search-container');
        if (searchContainer) searchContainer.style.display = 'block';
    }

    /* ─── Bootstrap Dropdown Initialization ─────────────────────── */
    // Ensure notification dropdown works properly
    const notificationBell = document.getElementById('notifications-bell');
    if (notificationBell && typeof $ !== 'undefined') {
        // Initialize Bootstrap dropdown manually if needed
        $(notificationBell).dropdown();
        
        // Add click handler as fallback
        notificationBell.addEventListener('click', function(e) {
            e.preventDefault();
            const dropdownMenu = document.getElementById('notifications-dropdown-menu');
            if (dropdownMenu) {
                const isVisible = dropdownMenu.style.display === 'block';
                dropdownMenu.style.display = isVisible ? 'none' : 'block';
            }
        });
    }

});

/* ─── AJAX CSRF Header ─────────────────────────────────────────── */
$(document).ajaxSend(function(e, xhr, options) {
    if (options.type && options.type.toUpperCase() === 'POST') {
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    }
});

/* ─── SweetAlert Confirmations ──────────────────────────────────── */
$(document).on('submit', 'form[data-confirm]', function(e) {
    e.preventDefault();
    const $form = $(this);
    const title = $form.attr('data-confirm') || "Are you sure?";
    
    Swal.fire({
        title: title,
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: 'var(--c-rose)',
        cancelButtonColor: 'var(--ink-soft)',
        confirmButtonText: 'Yes, proceed!'
    }).then((result) => {
        if (result.isConfirmed) {
            $form.removeAttr('data-confirm');
            $form.submit();
        }
    });
});

/* ─── Global Search Logic ───────────────────────────────────────── */
(function() {
    const $searchInput = $('#globalSearchInput');
    const $searchResults = $('#globalSearchResults');
    const $searchBody = $('#globalSearchBody');

    if (!$searchInput.length) return;

    let searchTimeout;
    $searchInput.on('input', function() {
        clearTimeout(searchTimeout);
        const query = $(this).val().trim();
        
        if (query.length < 2) {
            $searchResults.hide();
            return;
        }
        
        searchTimeout = setTimeout(() => {
            $searchBody.html('<div style="text-align:center;padding:20px;color:var(--ink-soft)"><div class="ds-skeleton" style="width:100%;height:36px;margin-bottom:8px"></div><div class="ds-skeleton" style="width:100%;height:36px;margin-bottom:8px"></div><div class="ds-skeleton" style="width:80%;height:36px"></div></div>');
            $searchResults.show();
            
            $.get(window.baseUrl + 'api/search', { q: query })
             .done(function(data) {
                if (!data || data.length === 0) {
                    $searchBody.html('<div class="ds-empty-state" style="padding:24px 16px;text-align:center"><div class="ds-empty-icon" style="font-size:32px;color:var(--ink-soft);margin-bottom:12px;opacity:0.5"><i class="fas fa-search-minus"></i></div><div class="ds-empty-title" style="font-size:14px;font-weight:700;color:var(--ink)">No matching records found</div><div class="ds-empty-desc" style="font-size:11.5px;color:var(--ink-muted)">We couldn\'t find anything for "'+query+'"</div></div>');
                    return;
                }
                
                let html = '';
                data.forEach(item => {
                    html += `
                        <a href="${item.url}" style="display:flex;align-items:center;gap:12px;padding:8px;text-decoration:none;border-radius:8px;transition:background .2s;color:var(--ink)" onmouseover="this.style.background='var(--bg)'" onmouseout="this.style.background='transparent'">
                            <div style="width:32px;height:32px;border-radius:50%;background:var(--c-${item.color}-bg);color:var(--c-${item.color});display:flex;align-items:center;justify-content:center;font-size:12px">
                                <i class="fas ${item.icon}"></i>
                            </div>
                            <div>
                                <div style="font-size:12.5px;font-weight:600">${item.title}</div>
                                <div style="font-size:10.5px;color:var(--ink-muted)">${item.type} • ${item.desc}</div>
                            </div>
                        </a>
                    `;
                });
                $searchBody.html(html);
             })
             .fail(function() {
                 $searchBody.html('<div class="ds-empty-state" style="padding:24px 16px;text-align:center"><div class="ds-empty-icon" style="font-size:32px;color:var(--c-rose);margin-bottom:12px;opacity:0.8"><i class="fas fa-exclamation-triangle"></i></div><div class="ds-empty-title" style="font-size:14px;font-weight:700;color:var(--ink)">Search Error</div><div class="ds-empty-desc" style="font-size:11.5px;color:var(--ink-muted)">An error occurred while fetching results.</div></div>');
             });
        }, 300);
    });

    // Close search on click outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#desktop-search-container').length) {
            $searchResults.hide();
        }
    });
})();

/* ─── Keyboard Shortcuts ────────────────────────────────────────── */
document.addEventListener('keydown', function(e) {
    // Ctrl+K to focus search
    if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
        e.preventDefault();
        var searchInput = document.getElementById('globalSearchInput');
        if (searchInput) searchInput.focus();
    }

    // Escape to close search
    if (e.key === 'Escape') {
        $('#globalSearchResults').hide();
        var searchInput = document.getElementById('globalSearchInput');
        if (searchInput) searchInput.blur();
    }

    // Alt+Key shortcuts for navigation
    if (e.altKey && !e.ctrlKey) {
        var shortcuts = {
            'n': 'resident/create',
            'h': 'households',
            'd': 'admin/dashboard',
            'b': 'blotter',
            'c': 'certificate'
        };
        var target = shortcuts[e.key.toLowerCase()];
        if (target) {
            e.preventDefault();
            window.location.href = window.baseUrl + target;
        }
    }
});

// #region agent log
window.addEventListener('pageshow', function(e) {
    const navEntry = (performance.getEntriesByType && performance.getEntriesByType('navigation')[0]) || null;
    fetch((window.baseUrl || '/') + 'debug/probe', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            hypothesisId: 'H15',
            location: 'public/js/theme/bmis-core.js:196',
            message: 'pageshow navigation probe (same-origin)',
            data: {
                href: window.location.href,
                persisted: !!e.persisted,
                navType: navEntry ? navEntry.type : null,
                visibility: document.visibilityState
            }
        })
    }).catch(() => {});
});
// #endregion

// #region agent log
window.addEventListener('error', function(e) {
    fetch('http://127.0.0.1:7249/ingest/d1d75b02-5b55-464a-9858-e2796691b14a',{method:'POST',headers:{'Content-Type':'application/json','X-Debug-Session-Id':'0646ff'},body:JSON.stringify({sessionId:'0646ff',runId:'pre-fix',hypothesisId:'H5',location:'public/js/theme/bmis-core.js:179',message:'window error',data:{message:e.message||null,filename:e.filename||null,lineno:e.lineno||null,colno:e.colno||null},timestamp:Date.now()})}).catch(()=>{});
});
// #endregion

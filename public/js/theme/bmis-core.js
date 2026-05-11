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

    /* ─── Mobile Sidebar Toggle ─────────────────────────────────── */
    const sidebar = document.getElementById('mainSidebar');
    if (window.innerWidth <= 768 && sidebar) {
        const toggler = document.createElement('button');
        toggler.innerHTML = '<i class="fas fa-bars"></i>';
        toggler.className = 'tb-icon-btn';
        toggler.style.cssText = 'position:fixed;top:10px;left:10px;z-index:1050;';
        toggler.onclick = () => sidebar.classList.toggle('open');
        document.body.appendChild(toggler);
    }

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

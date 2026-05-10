<?php //admin topbar ?>
<header class="bmis-topbar" id="mainTopbar">
    <div class="tb-left" style="display:flex;align-items:center;gap:24px">
        <div>
            <h1><?= $title ?? 'Dashboard' ?></h1>
            <p><?= date('l, F d, Y') ?></p>
        </div>
        
        <!-- Global Search Trigger -->
        <button id="spotlightTriggerBtn" style="display:none; align-items:center; justify-content:space-between; width: 260px; height: 36px; padding: 0 16px; border-radius: 20px; border: 1px solid var(--border-input); background: var(--bg); color: var(--ink-soft); font-size: 12px; cursor: pointer; transition: border-color 0.2s;">
            <span><i class="fas fa-search" style="margin-right:8px;"></i> Search residents, cases...</span>
            <span style="font-size:10px; background:var(--border); padding:2px 6px; border-radius:4px; font-weight:600;">Ctrl+K</span>
        </button>
    </div>
    <div class="tb-right">
        <div class="dropdown" style="display:inline-block">
            <button class="tb-icon-btn" id="notifications-bell" data-toggle="dropdown" title="Notifications" style="position:relative">
                <i class="fas fa-bell"></i>
                <span class="notifications-badge" style="display:none;position:absolute;top:4px;right:4px;background:var(--c-rose);color:#fff;font-size:9px;font-weight:700;padding:2px 4px;border-radius:10px;line-height:1"></span>
            </button>
            <div class="dropdown-menu dropdown-menu-right shadow-sm border-0 mt-2" id="notifications-dropdown-menu" style="border-radius:12px;width:300px;max-height:400px;overflow-y:auto;z-index:9999;">
            </div>
        </div>
        <button class="tb-icon-btn" id="darkModeToggle" title="Toggle Dark Mode">
            <i class="fas fa-moon"></i>
        </button>
        <button class="tb-icon-btn" title="Settings" onclick="window.location='<?= base_url('admin/settings') ?>'">
            <i class="fas fa-cog"></i>
        </button>
        <div class="tb-user-pill">
            <div class="tb-avatar"><i class="fas fa-user"></i></div>
            <span><?= esc(session()->get('email')) ?></span>
        </div>
    </div>
</header>

<script>
// Dark Mode Toggle Logic
document.addEventListener('DOMContentLoaded', function() {
    const toggleBtn = document.getElementById('darkModeToggle');
    const icon = toggleBtn.querySelector('i');
    
    // Check local storage for preference
    if (localStorage.getItem('theme') === 'dark') {
        document.body.classList.add('dark-theme');
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
});
</script>
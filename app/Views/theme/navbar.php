<?php
/**
 * Unified Navbar — role-aware topbar for both Admin and Staff.
 *
 * Admin extras: dark mode toggle, settings button, always-visible search.
 * Staff: search hidden by default (shown via JS on desktop).
 */
$role = strtolower(session()->get('role') ?? 'staff');
?>
<header class="bmis-topbar" id="mainTopbar">
    <div class="tb-left" style="display:flex;align-items:center;gap:24px">
        <div>
            <h1 style="font-weight:800;color:var(--ink);margin:0"><?= $title ?? 'Dashboard' ?></h1>
            <p style="font-weight:700;color:var(--ink-soft);margin-top:2px;font-size:13px"><?= date('l, F d, Y') ?></p>
        </div>
        
        <!-- Global Search -->
        <div style="position:relative; width: 280px;<?= $role !== 'admin' ? ' display:none' : '' ?>" id="desktop-search-container">
            <i class="fas fa-search" style="position:absolute; left:14px; top:50%; transform:translateY(-50%); color:var(--ink-soft); font-size:13px"></i>
            <input type="text" id="globalSearchInput" class="ds-input" placeholder="Search residents, cases..." style="padding-left:38px; padding-right:60px; border-radius:20px; background:var(--bg); border:none; height:36px">
            <span class="ds-kbd" style="position:absolute; right:10px; top:50%; transform:translateY(-50%)">Ctrl+K</span>
            <div id="globalSearchResults" style="display:none; position:absolute; top:42px; left:0; width:100%; background:var(--white); border-radius:12px; box-shadow:var(--shadow-lg); z-index:1000; overflow:hidden; border:1px solid var(--border)">
                <div style="padding:12px; max-height:400px; overflow-y:auto" id="globalSearchBody"></div>
            </div>
        </div>
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

        <?php if ($role === 'admin'): ?>
        <button class="tb-icon-btn" id="darkModeToggle" title="Toggle Dark Mode">
            <i class="fas fa-moon"></i>
        </button>
        <button class="tb-icon-btn" title="Settings" onclick="window.location='<?= base_url('admin/settings') ?>'">
            <i class="fas fa-cog"></i>
        </button>
        <?php endif; ?>

        <div class="tb-user-pill">
            <div class="tb-avatar"><i class="fas fa-user"></i></div>
            <span><?= esc(session()->get('email')) ?></span>
        </div>
    </div>
</header>
<?php //admin topbar ?>
<header class="bmis-topbar" id="mainTopbar">
    <div class="tb-left">
        <h1><?= $title ?? 'Dashboard' ?></h1>
        <p><?= date('l, F d, Y') ?></p>
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
        <button class="tb-icon-btn" title="Settings" onclick="window.location='<?= base_url('admin/settings') ?>'">
            <i class="fas fa-cog"></i>
        </button>
        <div class="tb-user-pill">
            <div class="tb-avatar"><i class="fas fa-user"></i></div>
            <span><?= esc(session()->get('email')) ?></span>
        </div>
    </div>
</header>
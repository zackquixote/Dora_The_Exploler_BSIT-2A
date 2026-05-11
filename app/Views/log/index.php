<?= $this->extend('theme/template') ?>
<?= $this->section('content') ?>

<div class="bmis-content">

    <!-- Filter -->
    <div class="ds-card" style="margin-bottom:14px">
        <div class="ds-card-body" style="display:flex;align-items:center;gap:12px;flex-wrap:wrap">
            <div class="ds-filter-header" style="margin-bottom:0"><i class="fas fa-filter"></i> Advanced Filters</div>
            <form id="logFilterForm" method="get" style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">
                <input type="date" name="date" class="ds-input" style="width:160px;height:34px;font-size:12px" value="<?= esc($selectedDate ?? '') ?>" onchange="this.form.submit()">
                
                <select name="user" class="ds-select" style="width:160px;height:34px;font-size:12px" onchange="this.form.submit()">
                    <option value="">All Users</option>
                    <?php if(!empty($users)): foreach($users as $u): ?>
                        <option value="<?= esc($u['USER_NAME']) ?>" <?= ($selectedUser ?? '') === $u['USER_NAME'] ? 'selected' : '' ?>><?= esc($u['USER_NAME']) ?></option>
                    <?php endforeach; endif; ?>
                </select>

                <select name="action" class="ds-select" style="width:160px;height:34px;font-size:12px" onchange="this.form.submit()">
                    <option value="">All Actions</option>
                    <option value="create" <?= ($selectedAction ?? '') === 'create' ? 'selected' : '' ?>>Create/Add</option>
                    <option value="update" <?= ($selectedAction ?? '') === 'update' ? 'selected' : '' ?>>Update/Edit</option>
                    <option value="delete" <?= ($selectedAction ?? '') === 'delete' ? 'selected' : '' ?>>Delete/Remove</option>
                    <option value="login" <?= ($selectedAction ?? '') === 'login' ? 'selected' : '' ?>>Login/Logout</option>
                </select>

                <a href="<?= base_url('logs') ?>" class="ds-btn ds-btn-ghost" style="height:34px;font-size:11px"><i class="fas fa-times"></i> Clear</a>
            </form>
        </div>
    </div>

    <!-- Logs -->
    <div class="ds-card">
        <div class="ds-card-head">
            <div class="ds-card-title"><i class="fas fa-history"></i> Activity Logs</div>
            <span class="ds-badge ds-badge-gray"><?= count($logs ?? []) ?> entries</span>
        </div>
        <div class="ds-card-body">
            <?php if (!empty($logs)): ?>
            <div class="ds-activity-feed" style="max-height:none">
                <?php foreach ($logs as $log):
                    $a = strtolower($log['ACTION'] ?? '');
                    $ic='ds-ai-view'; $ii='fa-eye';
                    if (strpos($a,'delete')!==false||strpos($a,'remove')!==false){$ic='ds-ai-delete';$ii='fa-trash-alt';}
                    elseif (strpos($a,'edit')!==false||strpos($a,'update')!==false){$ic='ds-ai-edit';$ii='fa-edit';}
                    elseif (strpos($a,'create')!==false||strpos($a,'add')!==false||strpos($a,'register')!==false){$ic='ds-ai-create';$ii='fa-plus-circle';}
                    elseif (strpos($a,'print')!==false){$ic='ds-ai-print';$ii='fa-print';}
                    elseif (strpos($a,'certif')!==false){$ic='ds-ai-cert';$ii='fa-file-alt';}
                    elseif (strpos($a,'login')!==false||strpos($a,'logout')!==false){$ic='ds-ai-view';$ii='fa-sign-in-alt';}
                ?>
                <div class="ds-activity-item">
                    <div class="ds-activity-icon <?= $ic ?>"><i class="fas <?= $ii ?>"></i></div>
                    <div style="flex:1">
                        <div class="ds-activity-action"><?= esc($log['ACTION']) ?></div>
                        <div class="ds-activity-meta">
                            by <strong><?= esc($log['USER_NAME']) ?></strong> (ID: <?= esc($log['USERID']) ?>)
                            · <?= esc($log['DATELOG']) ?> at <?= esc(date('h:i A', strtotime($log['TIMELOG']))) ?>
                        </div>
                        <div style="margin-top:4px;display:flex;gap:12px;font-size:10px;color:var(--ink-soft)">
                            <span><i class="fas fa-globe" style="margin-right:3px"></i> <?= esc($log['user_ip_address']) ?></span>
                            <span><i class="fas fa-desktop" style="margin-right:3px"></i> <?= esc($log['device_used']) ?></span>
                            <?php if (!empty($log['identifier'])): ?>
                                <span class="ds-badge ds-badge-blue"><?= esc($log['identifier']) ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div style="text-align:center;padding:32px;color:var(--ink-soft)">
                <i class="fas fa-history" style="font-size:24px;opacity:.3;display:block;margin-bottom:8px"></i>
                No activity logs found.
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
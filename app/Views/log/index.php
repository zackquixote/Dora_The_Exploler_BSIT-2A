<?= $this->extend('theme/template') ?>
<?= $this->section('content') ?>

<div class="bmis-content">

    <!-- Header -->
    <div class="bmis-page-header" style="margin-bottom:14px">
        <div class="bmis-page-title">
            <h1 style="font-weight:800"><i class="fas fa-history text-primary"></i> Audit Trail</h1>
            <p>Searchable log of all system activity — who did what and when.</p>
        </div>
    </div>

    <!-- Filter bar -->
    <div class="ds-card" style="margin-bottom:14px">
        <div class="ds-card-body">
            <form id="logFilterForm" method="get" style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr auto;gap:10px;align-items:end">
                <div>
                    <label class="ds-input-label">Search Action</label>
                    <div style="position:relative">
                        <i class="fas fa-search" style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:var(--ink-soft);font-size:11px"></i>
                        <input type="text" name="keyword" class="ds-input" placeholder="e.g. Resident, Household…"
                               value="<?= esc($keyword ?? '') ?>" style="padding-left:30px">
                    </div>
                </div>
                <div>
                    <label class="ds-input-label">Filter by Date</label>
                    <input type="date" name="date" class="ds-input" value="<?= esc($selectedDate ?? '') ?>">
                </div>
                <div>
                    <label class="ds-input-label">Filter by User</label>
                    <select name="user" class="ds-select">
                        <option value="">All Users</option>
                        <?php if (!empty($users)): foreach ($users as $u): ?>
                            <option value="<?= esc($u['USER_NAME']) ?>" <?= ($selectedUser ?? '') === $u['USER_NAME'] ? 'selected' : '' ?>>
                                <?= esc($u['USER_NAME']) ?>
                            </option>
                        <?php endforeach; endif; ?>
                    </select>
                </div>
                <div>
                    <label class="ds-input-label">Action Type</label>
                    <select name="action" class="ds-select">
                        <option value="">All Actions</option>
                        <option value="create"  <?= ($selectedAction ?? '') === 'create'  ? 'selected' : '' ?>>Create / Add</option>
                        <option value="update"  <?= ($selectedAction ?? '') === 'update'  ? 'selected' : '' ?>>Update / Edit</option>
                        <option value="delete"  <?= ($selectedAction ?? '') === 'delete'  ? 'selected' : '' ?>>Delete / Remove</option>
                        <option value="login"   <?= ($selectedAction ?? '') === 'login'   ? 'selected' : '' ?>>Login / Logout</option>
                    </select>
                </div>
                <div style="display:flex;gap:6px">
                    <button type="submit" class="ds-btn ds-btn-primary" style="height:36px"><i class="fas fa-search"></i> Search</button>
                    <?php if (!empty($keyword) || !empty($selectedDate) || !empty($selectedUser) || !empty($selectedAction)): ?>
                        <a href="<?= base_url('logs') ?>" class="ds-btn ds-btn-ghost" style="height:36px" title="Clear filters"><i class="fas fa-times"></i></a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <!-- Results -->
    <div class="ds-card">
        <div class="ds-card-head">
            <div class="ds-card-title"><i class="fas fa-list-alt"></i> Activity Logs</div>
            <div style="display:flex;align-items:center;gap:8px">
                <span class="ds-badge ds-badge-gray"><?= number_format($totalCount ?? 0) ?> total</span>
                <?php if (($totalPages ?? 1) > 1): ?>
                    <span style="font-size:11px;color:var(--ink-soft)">Page <?= $currentPage ?> of <?= $totalPages ?></span>
                <?php endif; ?>
            </div>
        </div>
        <div class="ds-card-body" style="padding:0">
            <?php if (!empty($logs)): ?>
            <div class="ds-activity-feed" style="max-height:none;padding:0">
                <?php foreach ($logs as $log):
                    $a  = strtolower($log['ACTION'] ?? '');
                    $ic = 'ds-ai-view'; $ii = 'fa-eye';
                    if      (strpos($a,'delete')!==false||strpos($a,'remove')!==false) { $ic='ds-ai-delete'; $ii='fa-trash-alt'; }
                    elseif  (strpos($a,'edit')!==false||strpos($a,'update')!==false)   { $ic='ds-ai-edit';   $ii='fa-edit'; }
                    elseif  (strpos($a,'create')!==false||strpos($a,'add')!==false||strpos($a,'register')!==false||strpos($a,'assign')!==false) { $ic='ds-ai-create'; $ii='fa-plus-circle'; }
                    elseif  (strpos($a,'print')!==false)   { $ic='ds-ai-print'; $ii='fa-print'; }
                    elseif  (strpos($a,'certif')!==false)  { $ic='ds-ai-cert';  $ii='fa-file-alt'; }
                    elseif  (strpos($a,'login')!==false||strpos($a,'logout')!==false) { $ic='ds-ai-view'; $ii='fa-sign-in-alt'; }
                ?>
                <div class="ds-activity-item" style="padding:12px 18px;border-bottom:.5px solid var(--border)">
                    <div class="ds-activity-icon <?= $ic ?>"><i class="fas <?= $ii ?>"></i></div>
                    <div style="flex:1;min-width:0">
                        <div class="ds-activity-action" style="font-size:12.5px"><?= esc($log['ACTION']) ?></div>
                        <div class="ds-activity-meta" style="margin-top:3px">
                            by <strong><?= esc($log['USER_NAME'] ?? '—') ?></strong>
                            <?php if (!empty($log['USERID'])): ?>
                                <span style="color:var(--ink-soft)">(ID: <?= esc($log['USERID']) ?>)</span>
                            <?php endif; ?>
                            · <i class="fas fa-calendar" style="margin-right:2px"></i><?= esc($log['DATELOG']) ?>
                            · <i class="fas fa-clock" style="margin-right:2px"></i><?= esc(date('h:i A', strtotime($log['TIMELOG']))) ?>
                        </div>
                        <div style="margin-top:4px;display:flex;gap:12px;flex-wrap:wrap;font-size:10px;color:var(--ink-soft)">
                            <?php if (!empty($log['user_ip_address'])): ?>
                                <span><i class="fas fa-globe" style="margin-right:3px"></i><?= esc($log['user_ip_address']) ?></span>
                            <?php endif; ?>
                            <?php if (!empty($log['device_used'])): ?>
                                <span><i class="fas fa-desktop" style="margin-right:3px"></i><?= esc($log['device_used']) ?></span>
                            <?php endif; ?>
                            <?php if (!empty($log['identifier'])): ?>
                                <span class="ds-badge ds-badge-blue" style="font-size:9px"><?= esc($log['identifier']) ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php if (($totalPages ?? 1) > 1): ?>
            <div style="padding:12px 18px;border-top:.5px solid var(--border);display:flex;align-items:center;justify-content:space-between">
                <span style="font-size:11px;color:var(--ink-soft)">
                    Showing <?= number_format(($currentPage - 1) * $perPage + 1) ?>–<?= number_format(min($currentPage * $perPage, $totalCount)) ?> of <?= number_format($totalCount) ?>
                </span>
                <div style="display:flex;gap:4px">
                    <?php
                    $qp = array_filter(['date'=>$selectedDate,'user'=>$selectedUser,'action'=>$selectedAction,'keyword'=>$keyword]);
                    $qs = $qp ? '&' . http_build_query($qp) : '';
                    $start = max(1, $currentPage - 2);
                    $end   = min($totalPages, $currentPage + 2);
                    ?>
                    <?php if ($currentPage > 1): ?>
                        <a href="?page=<?= $currentPage - 1 ?><?= $qs ?>" class="ds-btn ds-btn-ghost" style="height:30px;font-size:11px;padding:0 10px"><i class="fas fa-chevron-left"></i></a>
                    <?php endif; ?>
                    <?php for ($p = $start; $p <= $end; $p++): ?>
                        <a href="?page=<?= $p ?><?= $qs ?>"
                           class="ds-btn <?= $p === $currentPage ? 'ds-btn-primary' : 'ds-btn-ghost' ?>"
                           style="height:30px;font-size:11px;padding:0 10px;min-width:30px;text-align:center"><?= $p ?></a>
                    <?php endfor; ?>
                    <?php if ($currentPage < $totalPages): ?>
                        <a href="?page=<?= $currentPage + 1 ?><?= $qs ?>" class="ds-btn ds-btn-ghost" style="height:30px;font-size:11px;padding:0 10px"><i class="fas fa-chevron-right"></i></a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <?php else: ?>
            <div style="text-align:center;padding:48px 24px;color:var(--ink-soft)">
                <i class="fas fa-history" style="font-size:28px;opacity:.25;display:block;margin-bottom:12px"></i>
                <div style="font-size:13px;font-weight:700;color:var(--ink);margin-bottom:4px">No logs found</div>
                <div style="font-size:11px">Try adjusting your filters or search term.</div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

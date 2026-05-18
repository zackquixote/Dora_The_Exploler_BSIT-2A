<?= $this->extend('theme/template') ?>
<?= $this->section('content') ?>

<div class="bmis-content">
    <!-- Page Header -->
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;gap:12px;flex-wrap:wrap">
        <div style="display:flex;align-items:center;gap:16px">
            <div style="width:48px;height:48px;border-radius:12px;background:var(--c-blue-bg);color:var(--c-blue);display:flex;align-items:center;justify-content:center;font-size:20px">
                <i class="fas fa-history"></i>
            </div>
            <div>
                <div class="ds-page-title" style="margin:0;font-size:24px;font-weight:800;color:var(--ink)">Audit Logs</div>
                <div style="font-size:13px;color:var(--ink-muted);margin-top:2px">System-wide audit trail (login/logout + future CRUD)</div>
            </div>
        </div>
        <div style="font-size:12px;color:var(--ink-muted)">
            Total: <strong style="color:var(--ink)"><?= esc($totalCount ?? 0) ?></strong>
        </div>
    </div>

    <!-- Filters -->
    <div class="ds-card" style="margin-bottom:14px">
        <div class="ds-card-body">
            <div class="ds-filter-header"><i class="fas fa-filter"></i> Filters</div>
            <form method="get" action="<?= current_url() ?>" style="display:grid;grid-template-columns:1fr 1.2fr 1fr 1fr 1.5fr auto;gap:10px;align-items:end">
                <div>
                    <label class="ds-input-label">Date</label>
                    <input type="date" name="date" class="ds-input" value="<?= esc($selectedDate ?? '') ?>">
                </div>
                <div>
                    <label class="ds-input-label">User</label>
                    <select name="user_id" class="ds-select">
                        <option value="">All</option>
                        <?php foreach (($users ?? []) as $u): ?>
                            <option value="<?= (int) $u['id'] ?>" <?= ((string)($selectedUser ?? '') === (string)$u['id']) ? 'selected' : '' ?>>
                                <?= esc($u['name']) ?> (<?= esc($u['role']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="ds-input-label">Entity</label>
                    <input type="text" name="entity" class="ds-input" placeholder="e.g., user" value="<?= esc($selectedEntity ?? '') ?>">
                </div>
                <div>
                    <label class="ds-input-label">Action</label>
                    <input type="text" name="action" class="ds-input" placeholder="e.g., login" value="<?= esc($selectedAction ?? '') ?>">
                </div>
                <div>
                    <label class="ds-input-label">Keyword</label>
                    <input type="text" name="keyword" class="ds-input" placeholder="Search entity/action/ip..." value="<?= esc($keyword ?? '') ?>">
                </div>
                <div style="display:flex;gap:8px">
                    <button class="ds-btn ds-btn-primary" style="height:36px"><i class="fas fa-search"></i> Apply</button>
                    <a class="ds-btn ds-btn-ghost" style="height:36px;background:var(--white)" href="<?= current_url() ?>"><i class="fas fa-eraser"></i> Clear</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="ds-card" style="border:none;box-shadow:0 10px 30px rgba(0,0,0,0.03);border-radius:var(--r-lg);overflow:hidden">
        <div class="ds-card-body p0">
            <div style="overflow-x:auto">
                <table class="ds-table">
                    <thead>
                        <tr>
                            <th>Date/Time</th>
                            <th>User ID</th>
                            <th>Action</th>
                            <th>Entity</th>
                            <th>Entity ID</th>
                            <th>IP</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($logs)): ?>
                            <tr><td colspan="6" style="text-align:center;color:var(--ink-muted);padding:18px">No audit logs found.</td></tr>
                        <?php else: ?>
                            <?php foreach ($logs as $row): ?>
                                <tr>
                                    <td><?= esc($row['created_at'] ?? '') ?></td>
                                    <td><?= esc($row['user_id'] ?? '') ?></td>
                                    <td><strong><?= esc($row['action'] ?? '') ?></strong></td>
                                    <td><?= esc($row['entity'] ?? '') ?></td>
                                    <td><?= esc($row['entity_id'] ?? '') ?></td>
                                    <td><?= esc($row['ip_address'] ?? '') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <?php
        $currentPage = (int)($currentPage ?? 1);
        $totalPages  = (int)($totalPages ?? 1);
        $qs = $_GET ?? [];
    ?>
    <?php if ($totalPages > 1): ?>
        <div style="display:flex;gap:8px;justify-content:flex-end;align-items:center;margin-top:14px;flex-wrap:wrap">
            <?php
                $prev = max(1, $currentPage - 1);
                $next = min($totalPages, $currentPage + 1);
            ?>
            <?php $qs['page'] = $prev; ?>
            <a class="ds-btn ds-btn-ghost" style="height:34px;background:var(--white)" href="?<?= http_build_query($qs) ?>">Prev</a>
            <div style="font-size:12px;color:var(--ink-muted)">Page <strong style="color:var(--ink)"><?= $currentPage ?></strong> of <strong style="color:var(--ink)"><?= $totalPages ?></strong></div>
            <?php $qs['page'] = $next; ?>
            <a class="ds-btn ds-btn-ghost" style="height:34px;background:var(--white)" href="?<?= http_build_query($qs) ?>">Next</a>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>


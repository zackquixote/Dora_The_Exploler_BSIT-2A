<?php
$role = session()->get('role');
$template = ($role == 'admin') ? 'theme/admin/template' : 'theme/template';
?>
<?= $this->extend($template) ?>
<?= $this->section('content') ?>

<div class="bmis-content">

    <?php if (session()->getFlashdata('success')): ?>
        <div style="background:var(--c-teal-bg);color:var(--c-teal);padding:10px 16px;border-radius:var(--r-sm);margin-bottom:14px;font-size:12px;font-weight:600;display:flex;align-items:center;gap:8px">
            <i class="fas fa-check-circle"></i> <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>

    <!-- FILTER BAR -->
    <div class="ds-card" style="margin-bottom:14px">
        <div class="ds-card-body">
            <div class="ds-filter-header"><i class="fas fa-filter"></i> Filter Cases</div>
            <div style="display:grid;grid-template-columns:2fr 1.5fr 1.5fr auto;gap:12px;align-items:end">
                <div><label class="ds-input-label">Search</label><input type="text" id="searchCase" class="ds-input" placeholder="Case #, party name..."></div>
                <div><label class="ds-input-label">Status</label>
                    <select id="filterStatus" class="ds-select">
                        <option value="">All Status</option>
                        <?php foreach(['Pending','Investigating','Ongoing','For Hearing','Settled','Dismissed','Referred','Unsettled'] as $s): ?>
                            <option value="<?= $s ?>"><?= $s ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div><label class="ds-input-label">Purok</label>
                    <select id="filterPurok" class="ds-select">
                        <option value="">All Puroks</option>
                        <?php foreach(['Purok Malipayon','Purok Masagana','Purok Cory','Purok Kawayan','Purok Pagla-um'] as $p): ?>
                            <option value="<?= $p ?>"><?= $p ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div><button id="clearFilters" class="ds-btn ds-btn-ghost">Clear</button></div>
            </div>
        </div>
    </div>

    <!-- TABLE -->
    <div class="ds-card">
        <div class="ds-card-head">
            <div class="ds-card-title"><i class="fas fa-gavel"></i> Case List</div>
            <div style="display:flex;gap:8px">
                <a href="<?= base_url('blotter/exportCsv') ?>" class="ds-btn ds-btn-ghost"><i class="fas fa-file-csv"></i> Export CSV</a>
                <a href="<?= base_url('blotter/create') ?>" class="ds-btn ds-btn-primary"><i class="fas fa-plus"></i> New Case</a>
            </div>
        </div>
        <div class="ds-card-body p0">
            <div style="overflow-x:auto">
                <table class="ds-table" id="blotterTable">
                    <thead><tr><th>Case No.</th><th>Date</th><th>Purok</th><th>Complainant</th><th>Respondent</th><th>Type</th><th>Status</th><th>Actions</th></tr></thead>
                    <tbody>
                        <?php if (empty($blotters)): ?>
                            <tr><td colspan="8" style="text-align:center;padding:32px;color:var(--ink-soft)">No blotter records found.</td></tr>
                        <?php else: foreach($blotters as $b):
                            $sc = match($b['status']) {
                                'Pending'=>'ds-badge-amber','Settled'=>'ds-badge-teal','Dismissed'=>'ds-badge-gray',
                                'For Hearing'=>'ds-badge-blue','Unsettled'=>'ds-badge-rose','Referred'=>'ds-badge-violet',
                                default=>'ds-badge-blue'
                            };
                        ?>
                        <tr data-status="<?= $b['status'] ?>" data-purok="<?= $b['purok'] ?? '' ?>">
                            <td class="mono"><strong><?= esc($b['case_number']) ?></strong></td>
                            <td><?= date('Y-m-d', strtotime($b['incident_date'])) ?></td>
                            <td style="font-size:10.5px;font-weight:700;text-transform:uppercase;color:var(--ink-muted)"><?= esc($b['purok'] ?? '-') ?></td>
                            <td><?= esc($b['complainant_name'] ?? 'N/A') ?></td>
                            <td><?= esc($b['respondent_name'] ?? 'N/A') ?></td>
                            <td><?= esc($b['incident_type']) ?></td>
                            <td><span class="ds-badge <?= $sc ?>"><?= esc($b['status']) ?></span></td>
                            <td style="white-space:nowrap">
                                <a href="<?= base_url('blotter/view/'.$b['id']) ?>" class="ds-action-btn ab-blue" title="View"><i class="fas fa-folder-open"></i></a>
                                <a href="<?= base_url('blotter/edit/'.$b['id']) ?>" class="ds-action-btn ab-amber" title="Edit"><i class="fas fa-edit"></i></a>
                                <button class="ds-action-btn ab-rose delete-btn" data-id="<?= $b['id'] ?>" data-case="<?= esc($b['case_number']) ?>" title="Delete"><i class="fas fa-trash-alt"></i></button>
                            </td>
                        </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="ds-modal-overlay" id="deleteModalOverlay">
    <div class="ds-modal">
        <div class="ds-modal-icon" style="background:var(--c-rose-bg);color:var(--c-rose)"><i class="fas fa-trash-alt"></i></div>
        <h3>Confirm Delete</h3>
        <div class="subtitle">This will also remove all associated parties and hearings</div>
        <p>Are you sure you want to delete <strong id="delete-case-ref"></strong>?</p>
        <div class="ds-modal-actions">
            <button class="ds-btn ds-btn-ghost" onclick="document.getElementById('deleteModalOverlay').classList.remove('show')">Cancel</button>
            <form id="delete-form" method="POST" action="">
                <?= csrf_field() ?>
                <input type="hidden" name="_method" value="DELETE">
                <button type="submit" class="ds-btn ds-btn-rose">Delete Case</button>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    window.blotterConfig = { deleteUrl: '<?= base_url('blotter/delete') ?>' };
</script>
<script src="<?= base_url('js/blotter/blotter-index.js') ?>"></script>
<?= $this->endSection() ?>
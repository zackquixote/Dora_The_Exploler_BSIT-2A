<?= $this->extend('theme/template') ?>
<?= $this->section('content') ?>

<div class="bmis-content">

    <!-- Page Header -->
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px">
        <div style="display:flex;align-items:center;gap:16px">
            <div style="width:48px;height:48px;border-radius:12px;background:var(--c-rose-bg);color:var(--c-rose);display:flex;align-items:center;justify-content:center;font-size:20px">
                <i class="fas fa-gavel"></i>
            </div>
            <div>
                <div class="ds-page-title" style="margin:0;font-size:24px;font-weight:800;color:var(--ink)">Blotter Records</div>
                <div style="font-size:13px;color:var(--ink-muted);margin-top:2px;font-weight:700">Manage incident reports and barangay justice cases</div>
            </div>
        </div>
        <div style="display:flex;gap:10px">
            <a href="<?= base_url('blotter/exportCsv') ?>" class="ds-btn ds-btn-ghost" style="height:40px;padding:0 20px;border-radius:20px;background:var(--white)"><i class="fas fa-file-csv"></i> Export CSV</a>
            <a href="<?= base_url('blotter/create') ?>" class="ds-btn ds-btn-primary" style="height:40px;padding:0 20px;border-radius:20px;box-shadow:0 4px 12px rgba(var(--c-blue-rgb), 0.3)">
                <i class="fas fa-plus"></i> New Case
            </a>
        </div>
    </div>



    <!-- FILTER BAR -->
    <div class="ds-card" style="margin-bottom:14px">
        <div class="ds-card-body">
            <div class="ds-filter-header"><i class="fas fa-filter"></i> Filter Cases</div>
            <div style="display:grid;grid-template-columns:2fr 1.5fr 1.5fr auto;gap:12px;align-items:end">
                <div>
                    <label class="ds-input-label">Search</label>
                    <div style="position:relative">
                        <i class="fas fa-search" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--ink-soft);font-size:12px"></i>
                        <input type="text" id="searchCase" class="ds-input" placeholder="Case #, party name..." style="padding-left:32px">
                    </div>
                </div>
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
                        <?php foreach($purokList as $p): ?>
                            <option value="<?= $p ?>"><?= $p ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div><button id="clearFilters" class="ds-btn ds-btn-ghost">Clear</button></div>
            </div>
        </div>
    </div>

    <!-- TABLE -->
    <div class="ds-card" style="margin-bottom:24px;border:none;box-shadow:0 10px 30px rgba(0,0,0,0.03);border-radius:var(--r-lg);overflow:hidden">
        <div class="ds-card-head" style="background:var(--white);padding:20px 24px;border-bottom:1px solid var(--border)">
            <div class="ds-card-title"><i class="fas fa-list"></i> Official Case Roster</div>
        </div>
        <div class="ds-card-body p0">
            <?php if (empty($blotters)): ?>
                <div class="ds-empty-state" style="border:none; margin:0; padding:80px 20px;">
                    <i class="fas fa-gavel ds-empty-icon" style="color:var(--c-rose-soft); font-size:64px;"></i>
                    <h4 class="ds-empty-title">No Cases Found</h4>
                    <p class="ds-empty-text">There are no blotter records or incidents matching your current search criteria.</p>
                    <a href="<?= base_url('blotter/create') ?>" class="ds-btn ds-btn-primary"><i class="fas fa-plus"></i> Add New Case</a>
                </div>
            <?php else: ?>
            <div style="overflow-x:auto">
                <table class="ds-table" id="blotterTable">
                    <thead><tr><th>Case No.</th><th>Date</th><th>Purok</th><th>Complainant</th><th>Respondent</th><th>Type</th><th>Status</th><th>Actions</th></tr></thead>
                    <tbody>
                        <?php foreach($blotters as $b):
                            $sc = match($b['status']) {
                                'Pending'=>'ds-badge-amber','Settled'=>'ds-badge-teal','Dismissed'=>'ds-badge-gray',
                                'For Hearing'=>'ds-badge-blue','Unsettled'=>'ds-badge-rose','Referred'=>'ds-badge-violet',
                                default=>'ds-badge-blue'
                            };
                        ?>
                        <tr data-status="<?= $b['status'] ?>" data-purok="<?= $b['purok'] ?? '' ?>">
                            <td data-label="Case No" class="mono"><strong><?= esc($b['case_number']) ?></strong></td>
                            <td data-label="Date"><?= date('Y-m-d', strtotime($b['incident_date'])) ?></td>
                            <td data-label="Purok" style="font-size:10.5px;font-weight:700;text-transform:uppercase;color:var(--ink-muted)"><?= esc($b['purok'] ?? '-') ?></td>
                            <td data-label="Complainant"><?= esc($b['complainant_name'] ?? 'N/A') ?></td>
                            <td data-label="Respondent"><?= esc($b['respondent_name'] ?? 'N/A') ?></td>
                            <td data-label="Type"><?= esc($b['incident_type']) ?></td>
                            <td data-label="Status"><span class="ds-badge <?= $sc ?>"><?= esc($b['status']) ?></span></td>
                            <td data-label="Actions" style="white-space:nowrap">
                                <a href="<?= base_url('blotter/view/'.$b['id']) ?>" class="ds-action-btn ab-blue" title="View"><i class="fas fa-folder-open"></i></a>
                                <a href="<?= base_url('blotter/edit/'.$b['id']) ?>" class="ds-action-btn ab-amber" title="Edit"><i class="fas fa-edit"></i></a>
                                <form action="<?= base_url('blotter/delete/'.$b['id']) ?>" method="POST" style="display:inline-block" data-confirm="Delete Case <?= esc($b['case_number']) ?>?">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="ds-action-btn ab-rose" title="Delete"><i class="fas fa-trash-alt"></i></button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
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
<?= $this->extend('theme/template') ?>
<?= $this->section('content') ?>

<style>
@import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=DM+Mono:wght@400;500&display=swap');

:root {
    --navy:      #03213b;
    --navy-mid:  #0a3259;
    --accent:    #2563eb;
    --accent-lt: #eff6ff;
    --success:   #16a34a;
    --warn:      #d97706;
    --danger:    #dc2626;
    --muted:     #6b7280;
    --border:    #e5e7eb;
    --bg:        #f9fafb;
    --card:      #ffffff;
    --text:      #111827;
    --text-sm:   #374151;
    --radius:    12px;
    --shadow:    0 1px 3px rgba(0,0,0,.08), 0 4px 16px rgba(0,0,0,.05);
}
body, .content-wrapper { background: var(--bg) !important; font-family: 'DM Sans', sans-serif; }

/* PAGE HEADER */
.hh-page-header { padding: 28px 32px 0; }
.breadcrumb-bar { font-size: 12px; color: var(--muted); margin-bottom: 4px; }
.breadcrumb-bar a { color: var(--muted); text-decoration: none; }
.breadcrumb-bar a:hover { color: var(--accent); }
.hh-page-header h1 { font-size: 28px; font-weight: 700; color: var(--text); margin: 0 0 4px; letter-spacing: -.4px; }
.hh-page-header p  { font-size: 14px; color: var(--muted); margin: 0; max-width: 600px; line-height: 1.5; }

/* STAT CARDS */
.stat-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 16px; padding: 24px 32px 0; }
.stat-card { background: var(--card); border-radius: var(--radius); padding: 22px 24px; box-shadow: var(--shadow); display: flex; align-items: center; justify-content: space-between; }
.stat-body h2 { font-size: 30px; font-weight: 700; color: var(--text); margin: 0; line-height: 1; }
.stat-body .stat-label { font-size: 13px; font-weight: 600; color: var(--text-sm); margin-top: 4px; }
.stat-body .stat-sub   { font-size: 12px; color: var(--muted); margin-top: 4px; }
.stat-body .stat-delta { font-size: 12px; color: var(--success); font-weight: 600; margin-bottom: 4px; }
.stat-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0; }
.si-blue   { background:#eff6ff; color:#2563eb; }
.si-green  { background:#f0fdf4; color:#16a34a; }
.si-orange { background:#fff7ed; color:#d97706; }

/* TOOLBAR */
.hh-toolbar { display:flex; align-items:center; gap:10px; padding:20px 32px 0; flex-wrap:wrap; }
.search-wrap { position:relative; flex:1; min-width:220px; }
.search-wrap i { position:absolute; left:13px; top:50%; transform:translateY(-50%); color:var(--muted); font-size:13px; }
.search-wrap input { width:100%; padding:10px 14px 10px 36px; border:1px solid var(--border); border-radius:8px; font-size:14px; background:var(--card); font-family:'DM Sans',sans-serif; color:var(--text); }
.search-wrap input:focus { outline:none; border-color:var(--accent); box-shadow:0 0 0 3px rgba(37,99,235,.1); }
.filter-select { padding:10px 14px; border:1px solid var(--border); border-radius:8px; font-size:14px; background:var(--card); font-family:'DM Sans',sans-serif; color:var(--text); cursor:pointer; }
.filter-select:focus { outline:none; border-color:var(--accent); }
.btn-ghost { display:inline-flex; align-items:center; gap:6px; padding:10px 16px; border:1px solid var(--border); border-radius:8px; background:var(--card); font-size:14px; font-family:'DM Sans',sans-serif; color:var(--text-sm); cursor:pointer; text-decoration:none; font-weight:500; transition:all .15s; }
.btn-ghost:hover { border-color:var(--accent); color:var(--accent); text-decoration:none; }
.btn-add { display:inline-flex; align-items:center; gap:6px; padding:10px 18px; border:none; border-radius:8px; background:var(--navy); color:#fff; font-size:14px; font-family:'DM Sans',sans-serif; font-weight:600; cursor:pointer; text-decoration:none; transition:background .15s; white-space:nowrap; }
.btn-add:hover { background:var(--navy-mid); color:#fff; text-decoration:none; }

/* TABLE CARD */
.hh-card { background:var(--card); border-radius:var(--radius); box-shadow:var(--shadow); margin:20px 32px 0; overflow:hidden; }
.hh-card-header { padding:20px 24px 16px; border-bottom:1px solid var(--border); display:flex; align-items:flex-start; justify-content:space-between; }
.hh-card-header h3 { font-size:16px; font-weight:700; color:var(--text); margin:0 0 3px; }
.hh-card-header p  { font-size:13px; color:var(--muted); margin:0; }
.term-badge { font-size:12px; color:var(--muted); font-weight:500; white-space:nowrap; padding-top:4px; }

.hh-table { width:100%; border-collapse:collapse; }
.hh-table thead th { padding:11px 20px; font-size:11px; font-weight:600; color:var(--muted); text-transform:uppercase; letter-spacing:.6px; border-bottom:1px solid var(--border); background:#fafafa; white-space:nowrap; }
.hh-table tbody tr { border-bottom:1px solid var(--border); transition:background .1s; }
.hh-table tbody tr:last-child { border-bottom:none; }
.hh-table tbody tr:hover { background:#f8faff; }
.hh-table tbody td { padding:14px 20px; font-size:14px; color:var(--text-sm); vertical-align:middle; }

.hh-id-link { font-family:'DM Mono',monospace; font-size:13px; font-weight:500; color:var(--accent); text-decoration:none; }
.hh-id-link:hover { text-decoration:underline; }

.head-cell { display:flex; align-items:center; gap:10px; }
.avatar-initials { width:36px; height:36px; border-radius:8px; color:#fff; font-size:11px; font-weight:700; display:flex; align-items:center; justify-content:center; flex-shrink:0; letter-spacing:.5px; }
.head-name { font-size:14px; font-weight:600; color:var(--text); }
.head-sub  { font-size:12px; color:var(--muted); }

.loc-cell { display:flex; align-items:flex-start; gap:6px; font-size:13px; }
.loc-cell i { color:var(--muted); margin-top:2px; font-size:12px; flex-shrink:0; }

.badge-members { display:inline-flex; align-items:center; padding:4px 10px; border-radius:999px; font-size:12px; font-weight:600; }
.bm-low  { background:#f0fdf4; color:var(--success); }
.bm-mid  { background:#fef3c7; color:var(--warn); }
.bm-high { background:#fee2e2; color:var(--danger); }
.badge-purok { display:inline-block; padding:2px 8px; border-radius:6px; font-size:11px; font-weight:500; background:var(--accent-lt); color:var(--accent); margin-left:4px; }

.action-icons { display:flex; align-items:center; gap:4px; }
.action-btn { width:32px; height:32px; border-radius:7px; border:1px solid var(--border); background:var(--card); color:var(--muted); display:inline-flex; align-items:center; justify-content:center; font-size:13px; cursor:pointer; text-decoration:none; transition:all .15s; }
.action-btn.view:hover   { border-color:var(--accent);  background:var(--accent-lt); color:var(--accent); }
.action-btn.edit:hover   { border-color:#d97706; background:#fff7ed; color:#d97706; }
.action-btn.del:hover    { border-color:var(--danger);  background:#fee2e2; color:var(--danger); }

.hh-pagination { display:flex; align-items:center; justify-content:space-between; padding:14px 24px; border-top:1px solid var(--border); }
.pag-info { font-size:13px; color:var(--muted); }
.pag-btns { display:flex; gap:6px; }
.pag-btn { padding:7px 14px; border:1px solid var(--border); border-radius:7px; background:var(--card); font-size:13px; font-family:'DM Sans',sans-serif; color:var(--text-sm); cursor:pointer; font-weight:500; }
.pag-btn:disabled { opacity:.4; cursor:default; }

/* BOTTOM */
.hh-bottom { display:grid; grid-template-columns:1fr 1fr; gap:20px; padding:20px 32px 32px; }
.bottom-card { background:var(--card); border-radius:var(--radius); box-shadow:var(--shadow); padding:24px; }
.bottom-card h4 { font-size:15px; font-weight:700; color:var(--text); margin:0 0 8px; display:flex; align-items:center; gap:8px; }
.bottom-card p  { font-size:13px; color:var(--muted); line-height:1.6; margin:0 0 16px; }
.growth-card    { background:var(--navy); }
.growth-card h4 { color:#fff; }
.growth-card p  { color:rgba(255,255,255,.65); }
.growth-icon { width:40px; height:40px; border-radius:10px; background:rgba(255,255,255,.12); display:flex; align-items:center; justify-content:center; margin-bottom:14px; }
.purok-chips { display:flex; flex-wrap:wrap; gap:8px; }
.purok-chip  { display:inline-flex; align-items:center; gap:8px; padding:6px 12px; background:var(--bg); border:1px solid var(--border); border-radius:99px; font-size:12px; color:var(--text-sm); text-decoration:none; font-weight:500; transition:all .15s; }
.purok-chip:hover { border-color:var(--accent); color:var(--accent); background:var(--accent-lt); text-decoration:none; }
.chip-count { background:var(--navy); color:#fff; border-radius:99px; padding:1px 7px; font-size:11px; font-weight:700; }

/* FLASH */
.flash-zone { padding:16px 32px 0; }
.flash-zone .alert { border-radius:var(--radius); border:none; font-size:14px; }
</style>

<div class="content-wrapper">

    <!-- PAGE HEADER -->
    <div class="hh-page-header">
        <div class="breadcrumb-bar">
            <a href="<?= base_url('staff/dashboard') ?>">Records</a>
            <span class="mx-1">›</span>
            <span>Households</span>
        </div>
        <h1>Household Clusters</h1>
        <p>Manage and monitor residential groupings within the barangay. Each household record links individual residents to a primary address and family head.</p>
    </div>

    <!-- FLASH MESSAGES -->
    <?php if (session()->getFlashdata('success') || session()->getFlashdata('error')): ?>
    <div class="flash-zone">
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle mr-2"></i><?= session()->getFlashdata('success') ?>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-circle mr-2"></i><?= session()->getFlashdata('error') ?>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    
    <!-- TOOLBAR -->
    <div class="hh-toolbar">
        <div class="search-wrap">
            <i class="fas fa-search"></i>
            <input type="text" id="hhSearch" placeholder="Search by ID, Head of Family, or Address…">
        </div>
        <select class="filter-select" onchange="location.href='<?= base_url('households') ?>?purok='+encodeURIComponent(this.value)">
            <option value="all"             <?= ($selectedPurok ?? 'all') == 'all'            ? 'selected' : '' ?>>All Puroks</option>
            <option value="Purok Malipayon" <?= ($selectedPurok ?? '') == 'Purok Malipayon'   ? 'selected' : '' ?>>Purok Malipayon</option>
            <option value="Purok Masagana"  <?= ($selectedPurok ?? '') == 'Purok Masagana'    ? 'selected' : '' ?>>Purok Masagana</option>
            <option value="Purok Cory"      <?= ($selectedPurok ?? '') == 'Purok Cory'        ? 'selected' : '' ?>>Purok Cory</option>
            <option value="Purok Kawayan"   <?= ($selectedPurok ?? '') == 'Purok Kawayan'     ? 'selected' : '' ?>>Purok Kawayan</option>
            <option value="Purok Pagla-um"  <?= ($selectedPurok ?? '') == 'Purok Pagla-um'    ? 'selected' : '' ?>>Purok Pagla-um</option>
        </select>
        <a href="#" class="btn-ghost"><i class="fas fa-download"></i> Export CSV</a>
        <a href="<?= base_url('households/create') ?>" class="btn-add">
            <i class="fas fa-plus"></i> Add Household
        </a>
    </div>

    <!-- TABLE CARD -->
    <div class="hh-card">
        <div class="hh-card-header">
            <div>
                <h3>Household Registry</h3>
                <p>
                    Displaying <?= count($households) ?> household(s)
                    <?php if (($selectedPurok ?? 'all') !== 'all'): ?>
                        — filtered by <strong><?= esc($selectedPurok) ?></strong>
                        <a href="<?= base_url('households') ?>" style="color:var(--muted);margin-left:6px;font-size:12px;">(clear)</a>
                    <?php endif; ?>
                </p>
            </div>
            <span class="term-badge">Current Term: 2023–2025</span>
        </div>

        <div style="overflow-x:auto;">
            <table class="hh-table" id="hhTable">
                <thead>
                    <tr>
                        <th>Household ID</th>
                        <th>Head of Family</th>
                        <th>Address / Location</th>
                        <th>Members</th>
                        <th>House Type</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($households)): ?>
                        <tr>
                            <td colspan="6" style="text-align:center;padding:56px;color:var(--muted);">
                                <i class="fas fa-home" style="font-size:36px;display:block;margin-bottom:12px;opacity:.25;"></i>
                                No households found.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php
                        $bgPalette = ['#03213b','#0a3259','#1e4d8c','#1a3a6b','#0d2e5a','#163a6e','#164e63','#065f46'];
                        foreach ($households as $h):
                            $parts    = explode(' ', trim($h['head_name']));
                            $initials = implode('', array_map(fn($w) => strtoupper(substr($w,0,1)), $parts));
                            $initials = substr($initials, 0, 3);
                            $bgColor  = $bgPalette[abs(crc32($h['head_name'])) % count($bgPalette)];
                            $cnt      = (int)$h['resident_count'];
                            $bmClass  = $cnt >= 7 ? 'bm-high' : ($cnt >= 4 ? 'bm-mid' : 'bm-low');
                        ?>
                        <tr>
                            <td>
                                <a href="<?= base_url('households/view/'.$h['id']) ?>" class="hh-id-link">
                                    <?= esc($h['household_no']) ?>
                                </a>
                            </td>
                            <td>
                                <?php if ($h['head_name'] !== 'Not assigned'): ?>
                                <div class="head-cell">
                                    <div class="avatar-initials" style="background:<?= $bgColor ?>;"><?= esc($initials) ?></div>
                                    <div>
                                        <div class="head-name"><?= esc($h['head_name']) ?></div>
                                        <div class="head-sub"><?= esc($h['sitio']) ?></div>
                                    </div>
                                </div>
                                <?php else: ?>
                                    <span style="color:var(--muted);font-size:13px;font-style:italic;">Not assigned</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="loc-cell">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span>
                                        <?= esc($h['street_address'] ?: ($h['address'] ?: '—')) ?>
                                        <span class="badge-purok"><?= esc($h['sitio']) ?></span>
                                    </span>
                                </div>
                            </td>
                            <td><span class="badge-members <?= $bmClass ?>"><?= $cnt ?> Member<?= $cnt !== 1 ? 's' : '' ?></span></td>
                            <td style="color:var(--muted);font-size:13px;"><?= esc($h['house_type'] ?? '—') ?></td>
                            <td>
                                <div class="action-icons">
                                    <a href="<?= base_url('households/view/'.$h['id']) ?>" class="action-btn view" title="View"><i class="fas fa-eye"></i></a>
                                    <a href="<?= base_url('households/edit/'.$h['id']) ?>" class="action-btn edit" title="Edit"><i class="fas fa-pencil-alt"></i></a>
                                    <button class="action-btn del delete-household" data-id="<?= $h['id'] ?>" data-no="<?= esc($h['household_no']) ?>" title="Delete"><i class="fas fa-trash-alt"></i></button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="hh-pagination">
            <span class="pag-info">
                <i class="fas fa-lightbulb mr-1" style="color:var(--warn);"></i>
                Tip: Click "Household ID" to quickly view details.
            </span>
            <div class="pag-btns">
                <button class="pag-btn" disabled>← Previous</button>
                <button class="pag-btn">Next →</button>
            </div>
        </div>
    </div>

    <!-- BOTTOM SECTION -->
    <div class="hh-bottom">
        <div class="bottom-card">
            <h4><i class="fas fa-users" style="color:var(--accent);font-size:16px;"></i> Demographic Reporting</h4>
            <p>The household clusters facilitate targeted government assistance programs. Use the "Export CSV" feature to generate reports required by municipal social welfare departments for rice distribution or emergency cash grants.</p>
            <?php if (!empty($purokCounts)): ?>
            <div class="purok-chips">
                <?php foreach ($purokCounts as $purok => $count): ?>
                    <a href="<?= base_url('households?purok='.urlencode($purok)) ?>" class="purok-chip">
                        <?= esc($purok) ?><span class="chip-count"><?= $count ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
        <div class="bottom-card growth-card">
            <div class="growth-icon"><i class="fas fa-chart-line" style="color:#fff;font-size:18px;"></i></div>
            <h4>Growth Statistics</h4>
            <p>Barangay household growth has increased by 4% in the last quarter, primarily in Purok 1. Ensure all new structures are registered within 15 days of occupancy.</p>
        </div>
    </div>

</div>

<script>
$(document).ready(function () {
    /* Live search */
    $('#hhSearch').on('input', function () {
        var q = $(this).val().toLowerCase();
        $('#hhTable tbody tr').each(function () {
            $(this).toggle($(this).text().toLowerCase().includes(q));
        });
    });

    /* Delete */
    $(document).on('click', '.delete-household', function () {
        var id = $(this).data('id'), no = $(this).data('no'), row = $(this).closest('tr');
        if (!confirm('Delete Household ' + no + '?\nThis action cannot be undone.')) return;
        $.ajax({
            url: '<?= base_url('households/delete') ?>/' + id,
            type: 'POST',
            data: { '<?= csrf_token() ?>': '<?= csrf_hash() ?>' },
            dataType: 'json',
            success: function (res) {
                if (res.status === 'success') {
                    row.fadeOut(350, function () { $(this).remove(); });
                    toast('success', res.message);
                } else { toast('danger', res.message); }
            },
            error: function () { toast('danger', 'Server error. Please try again.'); }
        });
    });

    function toast(type, msg) {
        var t = $('<div>').css({
            position:'fixed', bottom:'24px', right:'24px', zIndex:9999,
            background: type === 'success' ? '#f0fdf4' : '#fee2e2',
            border: '1px solid ' + (type === 'success' ? '#16a34a' : '#dc2626'),
            borderRadius:'10px', padding:'14px 20px', fontSize:'14px',
            color: type === 'success' ? '#16a34a' : '#dc2626',
            boxShadow:'0 4px 20px rgba(0,0,0,.12)',
            fontFamily:'DM Sans,sans-serif', fontWeight:'600', maxWidth:'340px'
        }).text(msg).appendTo('body');
        setTimeout(function () { t.fadeOut(400, function () { $(this).remove(); }); }, 3500);
    }
});
</script>

<?= $this->endSection() ?>
<?= $this->extend('theme/template') ?>
<?= $this->section('content') ?>

<div class="bmis-content">

    <!-- Page Header -->
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px">
        <div style="display:flex;align-items:center;gap:16px">
            <div style="width:48px;height:48px;border-radius:12px;background:var(--c-rose-bg);color:var(--c-rose);display:flex;align-items:center;justify-content:center;font-size:20px;box-shadow:0 4px 12px rgba(var(--c-rose-rgb), 0.15)">
                <i class="fas fa-trash-restore"></i>
            </div>
            <div>
                <div class="ds-page-title" style="margin:0;font-size:24px;font-weight:800;color:var(--ink)">Archive</div>
                <div style="font-size:13px;color:var(--ink-muted);margin-top:2px">View and restore recently deleted records</div>
            </div>
        </div>
    </div>

    <!-- Flash messages -->
    <?php if (session()->getFlashdata('success')): ?>
        <div style="background:var(--c-teal-bg);color:var(--c-teal);padding:14px 20px;border-radius:var(--r-md);margin-bottom:24px;font-size:13px;font-weight:600;display:flex;align-items:center;gap:10px;border:1px solid rgba(var(--c-teal-rgb), 0.2)">
            <i class="fas fa-check-circle" style="font-size:16px"></i> <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div style="background:var(--c-rose-bg);color:var(--c-rose);padding:14px 20px;border-radius:var(--r-md);margin-bottom:24px;font-size:13px;font-weight:600;display:flex;align-items:center;gap:10px;border:1px solid rgba(var(--c-rose-rgb), 0.2)">
            <i class="fas fa-exclamation-circle" style="font-size:16px"></i> <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <!-- TABS NAV -->
    <div style="display:flex;gap:10px;margin-bottom:20px;border-bottom:2px solid var(--border);padding-bottom:12px;">
        <button class="archive-tab active" data-target="residents-tab" style="background:none;border:none;font-weight:700;font-size:14px;color:var(--c-blue);cursor:pointer;padding:8px 16px;border-radius:8px;background:var(--c-blue-bg);">
            <i class="fas fa-users" style="margin-right:6px"></i> Residents (<?= count($archivedResidents) ?>)
        </button>
        <button class="archive-tab" data-target="households-tab" style="background:none;border:none;font-weight:700;font-size:14px;color:var(--ink-muted);cursor:pointer;padding:8px 16px;border-radius:8px;transition:0.2s;">
            <i class="fas fa-house-user" style="margin-right:6px"></i> Households (<?= count($archivedHouseholds) ?>)
        </button>
    </div>

    <!-- TABS CONTENT -->
    <div class="ds-card" style="border:none;box-shadow:0 10px 30px rgba(0,0,0,0.03);border-radius:var(--r-lg);overflow:hidden">
        
        <!-- RESIDENTS TAB -->
        <div id="residents-tab" class="archive-content" style="display:block;">
            <div class="ds-card-head" style="background:var(--white);padding:20px 24px;border-bottom:1px solid var(--border)">
                <div class="ds-card-title" style="color:var(--ink)"><i class="fas fa-users" style="color:var(--c-blue)"></i> Archived Residents</div>
            </div>
            <div class="ds-card-body p0">
                <div style="overflow-x:auto">
                    <table class="ds-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Full Name</th>
                                <th>Purok / Sitio</th>
                                <th>Deleted At</th>
                                <th style="text-align:right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($archivedResidents)): ?>
                            <tr>
                                <td colspan="5" style="text-align:center;padding:60px 20px;">
                                    <div style="color:var(--border);font-size:48px;margin-bottom:16px"><i class="fas fa-folder-open"></i></div>
                                    <div style="color:var(--ink-muted);font-weight:600;font-size:15px">No archived residents found.</div>
                                    <div style="color:var(--ink-muted);font-size:13px;margin-top:4px">Deleted residents will appear here.</div>
                                </td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($archivedResidents as $r): ?>
                            <tr style="transition:0.2s">
                                <td class="mono" style="color:var(--c-blue)"><?= $r['id'] ?></td>
                                <td>
                                    <strong class="font-serif" style="font-size:14px;letter-spacing:-0.01em;color:var(--ink)"><?= esc($r['first_name']) ?> <?= esc($r['last_name']) ?></strong>
                                </td>
                                <td>
                                    <span style="background:var(--c-slate-bg);color:var(--c-slate);padding:4px 10px;border-radius:12px;font-size:11px;font-weight:700;text-transform:uppercase;">
                                        <?= esc($r['sitio'] ?? 'Unassigned') ?>
                                    </span>
                                </td>
                                <td>
                                    <div style="font-size:13px;color:var(--ink);font-weight:600"><i class="far fa-calendar-alt" style="color:var(--ink-muted);margin-right:6px"></i><?= date('M d, Y', strtotime($r['deleted_at'])) ?></div>
                                    <div style="font-size:11px;color:var(--ink-muted);margin-top:2px"><i class="far fa-clock" style="margin-right:6px"></i><?= date('h:i A', strtotime($r['deleted_at'])) ?></div>
                                </td>
                                <td style="text-align:right;white-space:nowrap">
                                    <div style="display:flex;gap:8px;justify-content:flex-end">
                                        <a href="<?= base_url('archive/restoreResident/'.$r['id']) ?>" 
                                           style="display:inline-flex;align-items:center;gap:6px;padding:6px 14px;border-radius:20px;font-size:12px;font-weight:700;text-decoration:none;background:#E1F5EE;color:#1D9E75;transition:all 0.2s;" 
                                           onmouseover="this.style.background='#1D9E75';this.style.color='#fff';" 
                                           onmouseout="this.style.background='#E1F5EE';this.style.color='#1D9E75';"
                                           onclick="return confirm('Restore this resident to active status?');">
                                            <i class="fas fa-undo"></i> Restore
                                        </a>
                                        <a href="<?= base_url('archive/forceDeleteResident/'.$r['id']) ?>" 
                                           style="display:inline-flex;align-items:center;gap:6px;padding:6px 14px;border-radius:20px;font-size:12px;font-weight:700;text-decoration:none;background:#FCEBEB;color:#A32D2D;transition:all 0.2s;" 
                                           onmouseover="this.style.background='#A32D2D';this.style.color='#fff';" 
                                           onmouseout="this.style.background='#FCEBEB';this.style.color='#A32D2D';"
                                           onclick="return confirm('Permanently delete this resident? This CANNOT be undone.');">
                                            <i class="fas fa-trash"></i> Delete
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- HOUSEHOLDS TAB -->
        <div id="households-tab" class="archive-content" style="display:none;">
            <div class="ds-card-head" style="background:var(--white);padding:20px 24px;border-bottom:1px solid var(--border)">
                <div class="ds-card-title" style="color:var(--ink)"><i class="fas fa-house-user" style="color:var(--c-blue)"></i> Archived Households</div>
            </div>
            <div class="ds-card-body p0">
                <div style="overflow-x:auto">
                    <table class="ds-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Household No.</th>
                                <th>Purok / Sitio</th>
                                <th>Deleted At</th>
                                <th style="text-align:right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($archivedHouseholds)): ?>
                            <tr>
                                <td colspan="5" style="text-align:center;padding:60px 20px;">
                                    <div style="color:var(--border);font-size:48px;margin-bottom:16px"><i class="fas fa-folder-open"></i></div>
                                    <div style="color:var(--ink-muted);font-weight:600;font-size:15px">No archived households found.</div>
                                    <div style="color:var(--ink-muted);font-size:13px;margin-top:4px">Deleted households will appear here.</div>
                                </td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($archivedHouseholds as $h): ?>
                            <tr style="transition:0.2s">
                                <td class="mono" style="color:var(--c-blue)"><?= $h['id'] ?></td>
                                <td>
                                    <strong class="font-serif" style="font-size:14px;letter-spacing:-0.01em;color:var(--ink)"><?= esc($h['household_no']) ?></strong>
                                </td>
                                <td>
                                    <span style="background:var(--c-slate-bg);color:var(--c-slate);padding:4px 10px;border-radius:12px;font-size:11px;font-weight:700;text-transform:uppercase;">
                                        <?= esc($h['sitio'] ?? 'Unassigned') ?>
                                    </span>
                                </td>
                                <td>
                                    <div style="font-size:13px;color:var(--ink);font-weight:600"><i class="far fa-calendar-alt" style="color:var(--ink-muted);margin-right:6px"></i><?= date('M d, Y', strtotime($h['deleted_at'])) ?></div>
                                    <div style="font-size:11px;color:var(--ink-muted);margin-top:2px"><i class="far fa-clock" style="margin-right:6px"></i><?= date('h:i A', strtotime($h['deleted_at'])) ?></div>
                                </td>
                                <td style="text-align:right;white-space:nowrap">
                                    <div style="display:flex;gap:8px;justify-content:flex-end">
                                        <a href="<?= base_url('archive/restoreHousehold/'.$h['id']) ?>" 
                                           style="display:inline-flex;align-items:center;gap:6px;padding:6px 14px;border-radius:20px;font-size:12px;font-weight:700;text-decoration:none;background:#E1F5EE;color:#1D9E75;transition:all 0.2s;" 
                                           onmouseover="this.style.background='#1D9E75';this.style.color='#fff';" 
                                           onmouseout="this.style.background='#E1F5EE';this.style.color='#1D9E75';"
                                           onclick="return confirm('Restore this household?');">
                                            <i class="fas fa-undo"></i> Restore
                                        </a>
                                        <a href="<?= base_url('archive/forceDeleteHousehold/'.$h['id']) ?>" 
                                           style="display:inline-flex;align-items:center;gap:6px;padding:6px 14px;border-radius:20px;font-size:12px;font-weight:700;text-decoration:none;background:#FCEBEB;color:#A32D2D;transition:all 0.2s;" 
                                           onmouseover="this.style.background='#A32D2D';this.style.color='#fff';" 
                                           onmouseout="this.style.background='#FCEBEB';this.style.color='#A32D2D';"
                                           onclick="return confirm('Permanently delete this household? This CANNOT be undone.');">
                                            <i class="fas fa-trash"></i> Delete
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tabs = document.querySelectorAll('.archive-tab');
    const contents = document.querySelectorAll('.archive-content');

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            // Reset all tabs
            tabs.forEach(t => {
                t.classList.remove('active');
                t.style.color = 'var(--ink-muted)';
                t.style.background = 'none';
            });
            // Reset all contents
            contents.forEach(c => c.style.display = 'none');

            // Activate clicked tab
            tab.classList.add('active');
            tab.style.color = 'var(--c-blue)';
            tab.style.background = 'var(--c-blue-bg)';
            
            // Show corresponding content
            document.getElementById(tab.getAttribute('data-target')).style.display = 'block';
        });
    });
});
</script>

<?= $this->endSection() ?>

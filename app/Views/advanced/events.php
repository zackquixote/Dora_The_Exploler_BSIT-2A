<?= $this->extend(session()->get('role') === 'resident' ? 'portal/layout' : 'theme/template') ?>
<?= $this->section('content') ?>

<div class="bmis-content">
    <!-- Page Header -->
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px">
        <div style="display:flex;align-items:center;gap:16px">
            <div style="width:48px;height:48px;border-radius:12px;background:var(--c-teal-bg);color:var(--c-teal);display:flex;align-items:center;justify-content:center;font-size:20px">
                <i class="fas fa-calendar-alt"></i>
            </div>
            <div>
                <div class="ds-page-title" style="margin:0;font-size:24px;font-weight:800;color:var(--ink)">Events</div>
                <div style="font-size:13px;color:var(--ink-muted);margin-top:2px">AJAX fetching for events list</div>
            </div>
        </div>
        <div style="display:flex;gap:10px;flex-wrap:wrap">
            <a href="<?= base_url('advanced/create-event') ?>" class="ds-btn ds-btn-primary" style="height:40px;padding:0 20px;border-radius:20px">
                <i class="fas fa-plus"></i> Create Event
            </a>
            <button id="evtRefreshBtn" class="ds-btn ds-btn-ghost" style="height:40px;padding:0 20px;border-radius:20px;background:var(--white)">
                <i class="fas fa-sync"></i> Refresh
            </button>
        </div>
    </div>

    <!-- Filter -->
    <div class="ds-card" style="margin-bottom:14px">
        <div class="ds-card-body">
            <div class="ds-filter-header"><i class="fas fa-filter"></i> Filter Events</div>
            <div style="display:grid;grid-template-columns:2fr 1fr auto;gap:12px;align-items:end">
                <div>
                    <label class="ds-input-label">Search title</label>
                    <input type="text" id="evtSearch" class="ds-input" placeholder="e.g., Cleanup Drive...">
                </div>
                <div>
                    <label class="ds-input-label">Status</label>
                    <select id="evtStatus" class="ds-select">
                        <option value="">All</option>
                        <?php foreach (['planning','open','ongoing','completed','cancelled'] as $st): ?>
                            <option value="<?= $st ?>"><?= ucfirst($st) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div style="display:flex;gap:10px">
                    <button id="evtClearBtn" class="ds-btn ds-btn-ghost" style="height:36px"><i class="fas fa-eraser"></i> Clear</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Results -->
    <div class="ds-card" style="border:none;box-shadow:0 10px 30px rgba(0,0,0,0.03);border-radius:var(--r-lg);overflow:hidden">
        <div class="ds-card-head" style="background:var(--white);padding:20px 24px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
            <div class="ds-card-title"><i class="fas fa-list"></i> Events List</div>
            <div id="evtMeta" style="font-size:12px;color:var(--ink-muted)"></div>
        </div>
        <div class="ds-card-body p0">
            <div style="overflow-x:auto">
                <table class="ds-table" id="eventsTable">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Title</th>
                            <th>Type</th>
                            <th>Venue</th>
                            <th>Start</th>
                            <th>End</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="eventsTbody">
                        <tr>
                            <td colspan="7" style="text-align:center;color:var(--ink-muted);padding:18px">Loading...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script defer src="<?= base_url('js/advanced/events.js') ?>?v=<?= time() ?>"></script>
<?= $this->endSection() ?>


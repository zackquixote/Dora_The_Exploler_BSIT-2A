<?= $this->extend('theme/template') ?>
<?= $this->section('content') ?>

<div class="bmis-content">
    <!-- Page Header -->
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px">
        <div style="display:flex;align-items:center;gap:16px">
            <div style="width:48px;height:48px;border-radius:12px;background:var(--c-rose-bg);color:var(--c-rose);display:flex;align-items:center;justify-content:center;font-size:20px">
                <i class="fas fa-heartbeat"></i>
            </div>
            <div>
                <div class="ds-page-title" style="margin:0;font-size:24px;font-weight:800;color:var(--ink)">Health Records</div>
                <div style="font-size:13px;color:var(--ink-muted);margin-top:2px">Search health profiles & find blood donors quickly</div>
            </div>
        </div>
    </div>

    <!-- Stat Cards -->
    <?php $stats = $statistics ?? []; ?>
    <div class="ds-grid-4" style="margin-bottom:14px">
        <div class="ds-stat">
            <div class="ds-stat-stripe str-rose"></div>
            <div class="ds-stat-top"><div class="ds-stat-icon" style="background:var(--c-rose-bg);color:var(--c-rose)"><i class="fas fa-notes-medical"></i></div></div>
            <div class="ds-stat-num"><?= esc($stats['total_records'] ?? 0) ?></div>
            <div class="ds-stat-label">Total Records</div>
            <div class="ds-stat-footer" style="color:var(--c-rose);cursor:default"><i class="fas fa-info-circle"></i> Encoded in system</div>
        </div>
        <div class="ds-stat">
            <div class="ds-stat-stripe str-teal"></div>
            <div class="ds-stat-top"><div class="ds-stat-icon ic-green"><i class="fas fa-phone-alt"></i></div></div>
            <div class="ds-stat-num"><?= esc($stats['emergency_contacts_percentage'] ?? 0) ?>%</div>
            <div class="ds-stat-label">With Emergency Contact</div>
            <div class="ds-stat-footer ft-teal" style="cursor:default"><i class="fas fa-shield-alt"></i> Safety readiness</div>
        </div>
        <div class="ds-stat">
            <div class="ds-stat-stripe str-blue"></div>
            <div class="ds-stat-top"><div class="ds-stat-icon ic-blue"><i class="fas fa-id-card"></i></div></div>
            <div class="ds-stat-num"><?= esc($stats['insurance_coverage_percentage'] ?? 0) ?>%</div>
            <div class="ds-stat-label">With Insurance Info</div>
            <div class="ds-stat-footer ft-blue" style="cursor:default"><i class="fas fa-info-circle"></i> Coverage tracking</div>
        </div>
        <div class="ds-stat">
            <div class="ds-stat-stripe" style="background:var(--c-amber)"></div>
            <div class="ds-stat-top"><div class="ds-stat-icon ic-amber"><i class="fas fa-tint"></i></div></div>
            <div class="ds-stat-num" id="donorCount">0</div>
            <div class="ds-stat-label">Blood Donors (Result)</div>
            <div class="ds-stat-footer" style="color:var(--c-amber);cursor:default"><i class="fas fa-filter"></i> Based on filter</div>
        </div>
    </div>

    <!-- Search / Filter -->
    <div class="ds-card" style="margin-bottom:14px">
        <div class="ds-card-body">
            <div class="ds-filter-header"><i class="fas fa-filter"></i> Search Health Records</div>
            <div style="display:grid;grid-template-columns:2fr 1fr auto;gap:12px;align-items:end">
                <div>
                    <label class="ds-input-label">Search by resident name</label>
                    <input type="text" id="hrSearchName" class="ds-input" placeholder="First name / last name...">
                </div>
                <div>
                    <label class="ds-input-label">Blood Type</label>
                    <select id="hrBloodType" class="ds-select">
                        <option value="">All</option>
                        <?php foreach (['A+','A-','B+','B-','AB+','AB-','O+','O-','Unknown'] as $bt): ?>
                            <option value="<?= $bt ?>"><?= $bt ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div style="display:flex;gap:10px">
                    <button id="hrSearchBtn" class="ds-btn ds-btn-primary" style="height:36px"><i class="fas fa-search"></i> Search</button>
                    <button id="hrClearBtn" class="ds-btn ds-btn-ghost" style="height:36px"><i class="fas fa-eraser"></i> Clear</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Donor Finder -->
    <div class="ds-card" style="margin-bottom:14px">
        <div class="ds-card-body">
            <div class="ds-filter-header"><i class="fas fa-tint"></i> Find Blood Donors</div>
            <div style="display:grid;grid-template-columns:1fr auto;gap:12px;align-items:end">
                <div>
                    <label class="ds-input-label">Select blood type</label>
                    <select id="donorBloodType" class="ds-select">
                        <option value="">-- Select --</option>
                        <?php foreach (['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bt): ?>
                            <option value="<?= $bt ?>"><?= $bt ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div style="display:flex;gap:10px">
                    <button id="donorFindBtn" class="ds-btn ds-btn-primary" style="height:36px"><i class="fas fa-search"></i> Find Donors</button>
                    <button id="donorUseSearchBtn" class="ds-btn ds-btn-ghost" style="height:36px"><i class="fas fa-sync"></i> Use as Filter</button>
                </div>
            </div>
            <div style="margin-top:10px;font-size:12px;color:var(--ink-muted)">
                Tip: “Use as Filter” will also update the Search filters above (same endpoint).
            </div>
        </div>
    </div>

    <!-- Results -->
    <div class="ds-card" style="border:none;box-shadow:0 10px 30px rgba(0,0,0,0.03);border-radius:var(--r-lg);overflow:hidden">
        <div class="ds-card-head" style="background:var(--white);padding:20px 24px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
            <div class="ds-card-title"><i class="fas fa-list"></i> Results</div>
            <div id="hrResultMeta" style="font-size:12px;color:var(--ink-muted)"></div>
        </div>
        <div class="ds-card-body p0">
            <div style="overflow-x:auto">
                <table class="ds-table" id="healthRecordsTable">
                    <thead>
                        <tr>
                            <th>Resident</th>
                            <th>Blood Type</th>
                            <th>Allergies</th>
                            <th>Medical Conditions</th>
                            <th>Emergency Contact</th>
                            <th>Emergency Phone</th>
                            <th>Last Checkup</th>
                        </tr>
                    </thead>
                    <tbody id="healthRecordsTbody">
                        <tr>
                            <td colspan="7" style="text-align:center;color:var(--ink-muted);padding:18px">
                                Use the filters above to load records.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script defer src="<?= base_url('js/advanced/health_records.js') ?>?v=<?= time() ?>"></script>
<?= $this->endSection() ?>


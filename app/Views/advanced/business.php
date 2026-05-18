<?= $this->extend('theme/template') ?>
<?= $this->section('content') ?>

<div class="bmis-content">
    <!-- Page Header -->
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px">
        <div style="display:flex;align-items:center;gap:16px">
            <div style="width:48px;height:48px;border-radius:12px;background:var(--c-blue-bg);color:var(--c-blue);display:flex;align-items:center;justify-content:center;font-size:20px">
                <i class="fas fa-store"></i>
            </div>
            <div>
                <div class="ds-page-title" style="margin:0;font-size:24px;font-weight:800;color:var(--ink)">Business Permits</div>
                <div style="font-size:13px;color:var(--ink-muted);margin-top:2px">AJAX filtering for registered businesses</div>
            </div>
        </div>
        <div style="display:flex;gap:10px;flex-wrap:wrap">
            <a href="<?= base_url('advanced/register-business') ?>" class="ds-btn ds-btn-primary" style="height:40px;padding:0 20px;border-radius:20px">
                <i class="fas fa-plus"></i> Register Business
            </a>
        </div>
    </div>

    <!-- Search / Filter -->
    <div class="ds-card" style="margin-bottom:14px">
        <div class="ds-card-body">
            <div class="ds-filter-header"><i class="fas fa-filter"></i> Filter Businesses</div>
            <div style="display:grid;grid-template-columns:2fr 1.2fr auto;gap:12px;align-items:end">
                <div>
                    <label class="ds-input-label">Search by business name</label>
                    <input type="text" id="bizSearch" class="ds-input" placeholder="e.g., Sari-sari Store...">
                </div>
                <div>
                    <label class="ds-input-label">Business Type</label>
                    <input type="text" id="bizType" class="ds-input" placeholder="e.g., Retail, Food...">
                </div>
                <div style="display:flex;gap:10px">
                    <button id="bizSearchBtn" class="ds-btn ds-btn-primary" style="height:36px"><i class="fas fa-search"></i> Search</button>
                    <button id="bizClearBtn" class="ds-btn ds-btn-ghost" style="height:36px"><i class="fas fa-eraser"></i> Clear</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Results -->
    <div class="ds-card" style="border:none;box-shadow:0 10px 30px rgba(0,0,0,0.03);border-radius:var(--r-lg);overflow:hidden">
        <div class="ds-card-head" style="background:var(--white);padding:20px 24px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
            <div class="ds-card-title"><i class="fas fa-list"></i> Businesses</div>
            <div id="bizResultMeta" style="font-size:12px;color:var(--ink-muted)"></div>
        </div>
        <div class="ds-card-body p0">
            <div style="overflow-x:auto">
                <table class="ds-table" id="businessTable">
                    <thead>
                        <tr>
                            <th>Permit #</th>
                            <th>Business Name</th>
                            <th>Type</th>
                            <th>Owner</th>
                            <th>Contact</th>
                            <th>Status</th>
                            <th>Issue</th>
                            <th>Expiry</th>
                        </tr>
                    </thead>
                    <tbody id="businessTbody">
                        <tr>
                            <td colspan="8" style="text-align:center;color:var(--ink-muted);padding:18px">
                                Use the filters above to load businesses.
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
<script defer src="<?= base_url('js/advanced/business.js') ?>?v=<?= time() ?>"></script>
<?= $this->endSection() ?>


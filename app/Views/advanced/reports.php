<?= $this->extend('theme/template') ?>
<?= $this->section('content') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/advanced-features.css') ?>">

<div class="bmis-content af-container">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px">
        <div style="display:flex;align-items:center;gap:16px">
            <div style="width:56px;height:56px;border-radius:16px;background:rgba(245,158,11,0.12);color:#f59e0b;display:flex;align-items:center;justify-content:center;font-size:24px">
                <i class="fas fa-chart-pie"></i>
            </div>
            <div>
                <h1 class="ds-page-title" style="margin:0;font-size:28px;font-weight:800;color:var(--ink)">Advanced Reports</h1>
                <p style="font-size:14px;color:var(--ink-muted);margin-top:2px">Detailed data analysis and exports</p>
            </div>
        </div>
        <div>
            <a href="<?= base_url('advanced/export?type=' . esc($report_type)) ?>" class="af-btn-primary" style="background: linear-gradient(135deg, #F59E0B, #D97706); box-shadow: 0 4px 15px rgba(245,158,11,0.3)">
                <i class="fas fa-file-export"></i> Export CSV Data
            </a>
        </div>
    </div>
    
    <div class="af-card">
        <div class="af-card-header" style="display:flex; justify-content:space-between; align-items:center">
            <div class="ds-card-title"><i class="fas fa-sliders-h"></i> Report Configuration</div>
            
            <form method="GET" style="display:flex; gap:12px; align-items:center;">
                <select name="type" class="af-input" style="width:250px; padding: 10px 16px;">
                    <option value="population" <?= $report_type == 'population' ? 'selected' : '' ?>>Population Demographics</option>
                    <option value="certificates" <?= $report_type == 'certificates' ? 'selected' : '' ?>>Certificate Issuance</option>
                    <option value="blotter" <?= $report_type == 'blotter' ? 'selected' : '' ?>>Blotter & Incidents</option>
                    <option value="revenue" <?= $report_type == 'revenue' ? 'selected' : '' ?>>Financial Revenue</option>
                </select>
                <button type="submit" class="af-btn-primary" style="padding: 10px 20px;">Load</button>
            </form>
        </div>
        
        <div class="af-card-body" style="text-align:center; padding: 60px 20px">
            <div style="font-size: 64px; color: rgba(15,23,42,0.05); margin-bottom: 20px;">
                <i class="fas fa-chart-bar"></i>
            </div>
            <h3 style="font-weight: 800; color: var(--ink); margin-bottom: 12px">
                <?= esc(ucwords(str_replace('_', ' ', $report_type))) ?> Report Data Loaded
            </h3>
            <p style="color: var(--ink-muted); max-width: 500px; margin: 0 auto 30px;">
                The data visualization modules are currently being set up. Please use the Export CSV feature at the top right to download and view the raw dataset for this module.
            </p>
            <div style="display:inline-block; background: var(--bg); padding: 12px 24px; border-radius: 99px; font-size: 12px; font-weight: 700; color: var(--ink-soft); border: 1px solid var(--border)">
                <i class="fas fa-clock"></i> Generated At: <?= esc($report['generated_at'] ?? date('Y-m-d H:i:s')) ?>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

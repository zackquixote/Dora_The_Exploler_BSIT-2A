<?= $this->extend(session()->get('role') === 'resident' ? 'portal/layout' : 'theme/template') ?>
<?= $this->section('content') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/advanced-features.css') ?>">

<div class="bmis-content af-container theme-emergency">
    <div style="display:flex;align-items:center;margin-bottom:24px">
        <div class="pulse-icon" style="width:56px;height:56px;border-radius:16px;background:rgba(239,68,68,0.12);color:#ef4444;display:flex;align-items:center;justify-content:center;font-size:24px;margin-right:18px">
            <i class="fas fa-ambulance"></i>
        </div>
        <div>
            <h1 class="ds-page-title" style="margin:0;font-size:28px;font-weight:800;color:var(--ink)">Report Emergency</h1>
            <p style="font-size:14px;color:var(--ink-muted);margin-top:2px">Dispatch response teams immediately to an active incident</p>
        </div>
    </div>
    
    <div class="af-card" style="max-width: 800px; border: 1px solid rgba(239,68,68,0.3)">
        <div class="af-card-header" style="background: linear-gradient(to right, rgba(239,68,68,0.1), transparent)">
            <div class="ds-card-title" style="color:#ef4444"><i class="fas fa-exclamation-triangle"></i> Incident Details</div>
        </div>
        <div class="af-card-body">
            <form action="<?= base_url('advanced/report-emergency') ?>" method="POST" id="emergencyForm">
                
                <div class="ds-grid-2">
                    <div class="af-form-group">
                        <label class="af-label">Emergency Type</label>
                        <select name="emergency_type" class="af-input" required>
                            <option value="Fire">Fire</option>
                            <option value="Medical Emergency">Medical Emergency</option>
                            <option value="Crime">Crime</option>
                            <option value="Flood">Flood</option>
                            <option value="Earthquake">Earthquake</option>
                        </select>
                    </div>
                    
                    <div class="af-form-group">
                        <label class="af-label">Severity Level</label>
                        <select name="severity_level" class="af-input" required>
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                            <option value="critical">Critical (Auto-dispatch)</option>
                        </select>
                    </div>
                </div>

                <div class="af-form-group">
                    <label class="af-label">Location</label>
                    <input type="text" name="location" class="af-input has-icon" required placeholder="e.g. Purok 1, Main Street">
                    <i class="fas fa-map-marker-alt af-input-icon"></i>
                </div>

                <div class="af-form-group">
                    <label class="af-label">Description</label>
                    <textarea name="description" class="af-input" rows="4" required placeholder="Describe the situation..."></textarea>
                </div>

                <div class="ds-grid-2">
                    <div class="af-form-group">
                        <label class="af-label">Reporter Name</label>
                        <input type="text" name="reporter_name" class="af-input has-icon" required>
                        <i class="fas fa-user af-input-icon"></i>
                    </div>
                    <div class="af-form-group">
                        <label class="af-label">Contact Number</label>
                        <input type="text" name="reporter_contact" class="af-input has-icon" required>
                        <i class="fas fa-phone af-input-icon"></i>
                    </div>
                </div>

                <div class="af-form-group" style="margin-top: 32px">
                    <button type="submit" class="af-btn-primary" style="width:100%; justify-content:center; padding:18px">
                        <i class="fas fa-exclamation-circle"></i> REPORT EMERGENCY NOW
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
document.getElementById('emergencyForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const res = await fetch(e.target.action, { method: 'POST', body: formData });
    const data = await res.json();
    if (data.success) {
        alert('Emergency Reported! Incident #' + data.incident.incident_number);
        window.location.href = '<?= base_url('advanced/emergency') ?>';
    } else {
        alert('Error: ' + data.message);
    }
});
</script>
<?= $this->endSection() ?>

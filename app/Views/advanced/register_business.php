<?= $this->extend(session()->get('role') === 'resident' ? 'portal/layout' : 'theme/template') ?>
<?= $this->section('content') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/advanced-features.css') ?>">

<div class="bmis-content af-container">
    <div style="display:flex;align-items:center;margin-bottom:24px">
        <div style="width:56px;height:56px;border-radius:16px;background:rgba(59,130,246,0.12);color:#2563eb;display:flex;align-items:center;justify-content:center;font-size:24px;margin-right:18px">
            <i class="fas fa-store"></i>
        </div>
        <div>
            <h1 class="ds-page-title" style="margin:0;font-size:28px;font-weight:800;color:var(--ink)">Register Business</h1>
            <p style="font-size:14px;color:var(--ink-muted);margin-top:2px">Add a new business to the official barangay directory</p>
        </div>
    </div>
    
    <div class="af-card" style="max-width: 800px">
        <div class="af-card-header">
            <div class="ds-card-title"><i class="fas fa-file-contract"></i> Business Permit Application</div>
        </div>
        <div class="af-card-body">
            <form action="<?= base_url('advanced/register-business') ?>" method="POST" id="businessForm">
                
                <div class="ds-grid-2">
                    <div class="af-form-group">
                        <label class="af-label">Owner Resident ID</label>
                        <input type="number" name="owner_resident_id" class="af-input has-icon" required placeholder="Enter Resident ID">
                        <i class="fas fa-id-card af-input-icon"></i>
                    </div>
                    
                    <div class="af-form-group">
                        <label class="af-label">Business Name</label>
                        <input type="text" name="business_name" class="af-input has-icon" required placeholder="Trade Name">
                        <i class="fas fa-store-alt af-input-icon"></i>
                    </div>
                </div>

                <div class="af-form-group">
                    <label class="af-label">Business Address</label>
                    <input type="text" name="business_address" class="af-input has-icon" required placeholder="Exact Location">
                    <i class="fas fa-map-marker-alt af-input-icon"></i>
                </div>

                <div class="ds-grid-3">
                    <div class="af-form-group">
                        <label class="af-label">Type/Category</label>
                        <input type="text" name="business_type" class="af-input" required placeholder="e.g. Retail">
                    </div>
                    <div class="af-form-group">
                        <label class="af-label">Contact Number</label>
                        <input type="text" name="contact_number" class="af-input" placeholder="Phone">
                    </div>
                    <div class="af-form-group">
                        <label class="af-label">Email</label>
                        <input type="email" name="email" class="af-input" placeholder="Optional">
                    </div>
                </div>

                <div class="ds-grid-2">
                    <div class="af-form-group">
                        <label class="af-label">Declared Capital (PHP)</label>
                        <input type="number" step="0.01" name="capital_amount" class="af-input has-icon" placeholder="0.00">
                        <i class="fas fa-coins af-input-icon"></i>
                    </div>
                    <div class="af-form-group">
                        <label class="af-label">Number of Employees</label>
                        <input type="number" name="employees_count" class="af-input has-icon" placeholder="1">
                        <i class="fas fa-users af-input-icon"></i>
                    </div>
                </div>

                <div class="af-form-group" style="margin-top: 32px">
                    <button type="submit" class="af-btn-primary">
                        <i class="fas fa-save"></i> Submit Registration
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
document.getElementById('businessForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const res = await fetch(e.target.action, { method: 'POST', body: formData });
    const data = await res.json();
    if (data.success) {
        alert('Business Registered Successfully!');
        window.location.href = '<?= base_url('advanced/business') ?>';
    } else {
        alert('Error: ' + data.message);
    }
});
</script>
<?= $this->endSection() ?>

<?= $this->extend(session()->get('role') === 'resident' ? 'portal/layout' : 'theme/template') ?>
<?= $this->section('content') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/advanced-features.css') ?>">

<div class="bmis-content af-container">
    <div style="display:flex;align-items:center;margin-bottom:24px">
        <div style="width:56px;height:56px;border-radius:16px;background:rgba(139,92,246,0.12);color:#8b5cf6;display:flex;align-items:center;justify-content:center;font-size:24px;margin-right:18px">
            <i class="fas fa-qrcode"></i>
        </div>
        <div>
            <h1 class="ds-page-title" style="margin:0;font-size:28px;font-weight:800;color:var(--ink)">QR Code Generator</h1>
            <p style="font-size:14px;color:var(--ink-muted);margin-top:2px">Generate secure, verifiable QR codes for entities</p>
        </div>
    </div>
    
    <div class="ds-grid-2">
        <div class="af-card">
            <div class="af-card-header">
                <div class="ds-card-title"><i class="fas fa-cogs"></i> Generation Settings</div>
            </div>
            <div class="af-card-body">
                <form action="<?= base_url('advanced/qr-generator') ?>" method="POST" id="qrForm">
                    <div class="af-form-group">
                        <label class="af-label">QR Type</label>
                        <select name="type" class="af-input" required>
                            <option value="resident">Resident ID Profile</option>
                            <option value="certificate">Document / Certificate</option>
                        </select>
                    </div>
                    <div class="af-form-group">
                        <label class="af-label">Entity ID (Number)</label>
                        <input type="number" name="id" class="af-input has-icon" required placeholder="Enter ID...">
                        <i class="fas fa-fingerprint af-input-icon"></i>
                    </div>
                    <div class="af-form-group" style="margin-top: 32px">
                        <button type="submit" class="af-btn-primary" style="width:100%; justify-content:center; background: linear-gradient(135deg, #8B5CF6, #6366F1); box-shadow: 0 4px 15px rgba(139,92,246,0.3)">
                            <i class="fas fa-magic"></i> Generate Code
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="af-card" id="qrResultCard" style="opacity:0.5; transition: all 0.5s;">
            <div class="af-card-body" style="height:100%; display:flex; flex-direction:column; align-items:center; justify-content:center; min-height:300px">
                <div id="qrPlaceholder" style="color:var(--ink-soft); text-align:center">
                    <i class="fas fa-qrcode" style="font-size:64px; margin-bottom:16px; opacity:0.3"></i>
                    <p>Generated QR will appear here</p>
                </div>
                <div id="qrResult" style="display:none; text-align:center;">
                    <h4 style="margin-bottom:20px; font-weight:800; color:var(--ink)">Scan to Verify</h4>
                    <div style="background:white; padding:16px; border-radius:16px; display:inline-block; box-shadow:0 10px 25px rgba(0,0,0,0.1)">
                        <img id="qrImage" src="" alt="QR Code" style="max-width: 200px; display:block">
                    </div>
                    <div style="margin-top:24px">
                        <button type="button" class="ds-btn ds-btn-primary" onclick="window.print()">
                            <i class="fas fa-print"></i> Print QR
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
document.getElementById('qrForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = e.target.querySelector('button');
    btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Generating...';
    
    const formData = new FormData(e.target);
    const res = await fetch(e.target.action, { method: 'POST', body: formData });
    const data = await res.json();
    
    btn.innerHTML = '<i class="fas fa-magic"></i> Generate Code';

    if (data.success) {
        document.getElementById('qrPlaceholder').style.display = 'none';
        document.getElementById('qrResult').style.display = 'block';
        document.getElementById('qrImage').src = data.qr_image;
        document.getElementById('qrResultCard').style.opacity = '1';
    } else {
        alert('Error: ' + data.message);
    }
});
</script>
<?= $this->endSection() ?>

<?= $this->extend('portal/layout') ?>
<?= $this->section('content') ?>

<div class="af-container">
    <div class="af-header">
        <h1 class="af-title">File an Incident Report</h1>
        <p class="af-subtitle">Formally file a complaint or report an incident to the Barangay Peace and Order Committee (Lupon).</p>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success af-alert">
            <i class="fas fa-check-circle"></i> <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger af-alert">
            <i class="fas fa-exclamation-circle"></i> <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <div class="af-card-grid" style="grid-template-columns: 1fr; max-width: 800px; margin: 0 auto;">
        <div class="af-stat-card" style="align-items: stretch; text-align: left; padding: 30px;">
            <form action="<?= base_url('portal/blotter/submit') ?>" method="POST" id="blotterForm">
                <?= csrf_field() ?>
                
                <h3 style="font-size: 1.2rem; margin-bottom: 20px; color: var(--c-navy); border-bottom: 1px solid #e2e8f0; padding-bottom: 10px;">Incident Details</h3>

                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="af-form-group">
                            <label class="af-label">Incident Type *</label>
                            <select name="incident_type" class="af-input" required>
                                <option value="">Select Type</option>
                                <option value="Theft / Robbery">Theft / Robbery</option>
                                <option value="Physical Injury">Physical Injury</option>
                                <option value="Vandalism">Vandalism</option>
                                <option value="Disturbance of Peace">Disturbance of Peace</option>
                                <option value="Domestic Dispute">Domestic Dispute</option>
                                <option value="Property Dispute">Property Dispute</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="af-form-group">
                            <label class="af-label">Date & Time of Incident *</label>
                            <input type="datetime-local" name="incident_date" class="af-input" required max="<?= date('Y-m-d\TH:i') ?>">
                        </div>
                    </div>
                </div>

                <div class="af-form-group" style="margin-top: 15px;">
                    <label class="af-label">Exact Location of Incident *</label>
                    <input type="text" name="incident_location" class="af-input" placeholder="e.g. Near the basketball court, Purok 2" required>
                </div>

                <div class="af-form-group" style="margin-top: 15px;">
                    <label class="af-label">Detailed Description *</label>
                    <textarea name="details" class="af-input" rows="5" placeholder="Please describe exactly what happened in detail..." required></textarea>
                </div>

                <h3 style="font-size: 1.2rem; margin-top: 30px; margin-bottom: 20px; color: var(--c-navy); border-bottom: 1px solid #e2e8f0; padding-bottom: 10px;">Respondent Information (Person Being Complained)</h3>
                
                <div class="row g-3">
                    <div class="col-md-12">
                        <div class="af-form-group">
                            <label class="af-label">Name of Respondent *</label>
                            <input type="text" name="respondent_name" class="af-input" placeholder="Full name of the person you are complaining against" required>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="af-form-group" style="margin-top: 15px;">
                            <label class="af-label">Address of Respondent (if known)</label>
                            <input type="text" name="respondent_address" class="af-input" placeholder="Address of the respondent">
                        </div>
                    </div>
                </div>

                <div style="margin-top: 30px; display: flex; gap: 15px; justify-content: flex-end;">
                    <a href="<?= base_url('portal/home') ?>" class="af-btn" style="background: #e2e8f0; color: var(--c-navy);">Cancel</a>
                    <button type="submit" class="af-btn" id="submitBtn">
                        <i class="fas fa-paper-plane"></i> Submit Report
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById('blotterForm').addEventListener('submit', function() {
        const btn = document.getElementById('submitBtn');
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
        btn.style.opacity = '0.7';
        btn.style.pointerEvents = 'none';
    });
</script>

<?= $this->endSection() ?>

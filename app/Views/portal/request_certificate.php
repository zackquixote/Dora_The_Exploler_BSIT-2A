<?= $this->extend('portal/layout') ?>
<?= $this->section('content') ?>

<div class="af-container">
    <div class="af-header" style="margin-bottom: 30px;">
        <h1 class="af-title">Request Certificate</h1>
        <p class="af-subtitle">Request official barangay clearances, certifications, and documentation online.</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="af-card" style="padding: 32px; background: white; border: 1px solid var(--border); box-shadow: var(--shadow-md); border-radius: 16px;">
                <h3 style="font-size: 1.3rem; font-weight: 800; color: var(--ink); margin-bottom: 24px; border-bottom: 1px solid var(--border); padding-bottom: 12px; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-file-alt" style="color: #8b5cf6;"></i> New Request details
                </h3>

                <form action="<?= base_url('portal/certificates/request') ?>" method="POST" id="requestForm" aria-label="Certificate Request Form">
                    <?= csrf_field() ?>
                    
                    <div class="af-form-group" style="margin-bottom: 20px;">
                        <label for="certificate_type" class="af-label" style="font-weight: 700; font-size: 13px; color: var(--ink); margin-bottom: 8px; display: block;">Certificate Type *</label>
                        <select id="certificate_type" name="certificate_type" class="af-input" style="width: 100%; height: 44px; padding: 0 14px; border: 1.5px solid var(--border); border-radius: 10px; font-size: 14px; color: var(--ink); transition: border-color 0.2s;" required>
                            <option value="">Choose a certificate...</option>
                            <?php foreach ($types as $t): ?>
                                <option value="<?= esc($t) ?>"><?= esc($t) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="af-form-group" style="margin-bottom: 24px;">
                        <label for="purpose" class="af-label" style="font-weight: 700; font-size: 13px; color: var(--ink); margin-bottom: 8px; display: block;">Purpose *</label>
                        <textarea id="purpose" name="purpose" class="af-input" rows="4" style="width: 100%; padding: 12px 14px; border: 1.5px solid var(--border); border-radius: 10px; font-size: 14px; color: var(--ink); transition: border-color 0.2s; resize: vertical;" placeholder="e.g. For employment requirements, local scholarship application, etc." required><?= old('purpose') ?></textarea>
                    </div>

                    <div style="display: flex; gap: 12px;">
                        <a href="<?= base_url('portal/certificates') ?>" class="ds-btn ds-btn-ghost" style="flex: 1; height: 44px; display: flex; align-items: center; justify-content: center; font-weight: 700; text-decoration: none; border-radius: 10px;">
                            Cancel
                        </a>
                        <button type="submit" class="af-btn" style="flex: 2; height: 44px; font-weight: 700; border-radius: 10px; border: none; background: #8b5cf6; color: white;" id="submitBtn">
                            <i class="fas fa-paper-plane" style="margin-right: 6px;"></i> Submit Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('requestForm').addEventListener('submit', function() {
        const btn = document.getElementById('submitBtn');
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
        btn.style.opacity = '0.7';
        btn.style.pointerEvents = 'none';
    });
</script>

<?= $this->endSection() ?>

<?= $this->extend('theme/template') ?>

<?= $this->section('content') ?>
<div class="bmis-header">
    <div class="bmis-title">
        <div class="icon"><i class="fas fa-id-card"></i></div>
        <div>
            <h2>Verification Review</h2>
            <p>Review submitted ID images, link the account to a resident, and decide the next step.</p>
        </div>
    </div>
</div>

<div class="ds-container">
    <?php if (session()->getFlashdata('success')): ?>
        <div class="ds-alert ds-alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="ds-alert ds-alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <div class="ds-grid-2" style="display:grid;grid-template-columns:1.15fr .85fr;gap:18px">
        <div class="ds-card">
            <div class="ds-card-header">
                <h3>Submitted Details</h3>
            </div>
            <div class="ds-card-body">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
                    <div>
                        <div class="ds-form-label">Full Name</div>
                        <div><strong><?= esc(trim($verification['first_name'] . ' ' . ($verification['middle_name'] ? $verification['middle_name'] . ' ' : '') . $verification['last_name'])) ?></strong></div>
                    </div>
                    <div>
                        <div class="ds-form-label">Birthdate</div>
                        <div><?= esc($verification['birthdate'] ?: '—') ?></div>
                    </div>
                    <div>
                        <div class="ds-form-label">Email</div>
                        <div><?= esc($verification['contact_email_submitted'] ?: $verification['email'] ?: '—') ?></div>
                    </div>
                    <div>
                        <div class="ds-form-label">Phone</div>
                        <div><?= esc($verification['contact_phone_submitted'] ?: $verification['phone'] ?: '—') ?></div>
                    </div>
                    <div>
                        <div class="ds-form-label">National ID Number</div>
                        <div><?= esc($verification['national_id_number']) ?></div>
                    </div>
                    <div>
                        <div class="ds-form-label">Current Status</div>
                        <div>
                            <span class="ds-badge <?= $verification['status'] === 'pending_admin_review' ? 'ds-badge-blue' : ($verification['status'] === 'needs_resubmission' ? 'ds-badge-amber' : ($verification['status'] === 'pending_otp' ? 'ds-badge-teal' : 'ds-badge-rose')) ?>">
                                <?= esc(str_replace('_', ' ', $verification['status'])) ?>
                            </span>
                        </div>
                    </div>
                </div>

                <div style="margin-top:16px">
                    <div class="ds-form-label">Submitted Address</div>
                    <div style="padding:12px;border:1px solid var(--border);border-radius:12px;background:#fcfcfd">
                        <?= esc($verification['address_submitted']) ?>
                    </div>
                </div>

                <div style="margin-top:18px">
                    <div class="ds-form-label">Uploaded Files</div>
                    <?php if (empty($files)): ?>
                        <div style="padding:14px;border:1px solid var(--border);border-radius:12px;color:var(--ink-muted)">No uploaded files found.</div>
                    <?php else: ?>
                        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:12px">
                            <?php foreach ($files as $file): ?>
                                <div style="border:1px solid var(--border);border-radius:14px;padding:12px;background:#fcfcfd">
                                    <div style="font-weight:800;font-size:13px;margin-bottom:8px;text-transform:capitalize">
                                        <?= esc(str_replace('_', ' ', $file['file_type'])) ?>
                                    </div>
                                    <?php if (str_contains((string) ($file['mime_type'] ?? ''), 'image/')): ?>
                                        <a href="<?= base_url('admin/portal-accounts/file/' . $file['id']) ?>" target="_blank">
                                            <img src="<?= base_url('admin/portal-accounts/file/' . $file['id']) ?>" alt="Verification file" style="width:100%;height:180px;object-fit:cover;border-radius:12px;border:1px solid var(--border)">
                                        </a>
                                    <?php else: ?>
                                        <div style="height:180px;display:flex;align-items:center;justify-content:center;border:1px solid var(--border);border-radius:12px;background:var(--white)">
                                            <i class="fas fa-file-pdf" style="font-size:40px;color:#ef4444"></i>
                                        </div>
                                    <?php endif; ?>
                                    <div style="margin-top:10px;font-size:12px;color:var(--ink-muted)"><?= esc($file['original_name']) ?></div>
                                    <a href="<?= base_url('admin/portal-accounts/file/' . $file['id']) ?>" target="_blank" class="ds-btn ds-btn-ghost ds-btn-sm" style="margin-top:10px;background:var(--white)">
                                        <i class="fas fa-eye"></i> Open File
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div style="display:flex;flex-direction:column;gap:18px">
            <div class="ds-card">
                <div class="ds-card-header">
                    <h3>Resident Link</h3>
                </div>
                <div class="ds-card-body">
                    <div style="font-size:13px;color:var(--ink-muted);margin-bottom:10px">
                        Link this verification to the correct resident record before approval.
                    </div>
                    <?php if (!empty($verification['resident_id'])): ?>
                        <div style="background:#ecfdf5;border:1px solid rgba(16,185,129,.2);padding:12px;border-radius:12px;margin-bottom:12px">
                            <strong>Currently linked:</strong>
                            <?= esc(trim(($verification['resident_first_name'] ?? '') . ' ' . ($verification['resident_last_name'] ?? ''))) ?>
                            <?php if (!empty($verification['resident_sitio'])): ?>
                                <div style="font-size:12px;color:var(--ink-muted)">Purok / Sitio: <?= esc($verification['resident_sitio']) ?></div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    <div style="font-size:12px;color:var(--ink-muted);margin-bottom:8px">Matching resident suggestions</div>
                    <div style="max-height:220px;overflow:auto;border:1px solid var(--border);border-radius:12px;background:#fcfcfd">
                        <?php if (empty($residentCandidates)): ?>
                            <div style="padding:12px;color:var(--ink-muted)">No likely resident matches found. Enter the resident ID manually below.</div>
                        <?php else: ?>
                            <?php foreach ($residentCandidates as $resident): ?>
                                <label style="display:flex;align-items:center;gap:10px;padding:12px;border-bottom:1px solid var(--border)">
                                    <input type="radio" name="resident_id_selector" value="<?= esc($resident['id']) ?>" onclick="document.getElementById('resident_id').value=this.value" <?= (int) ($verification['resident_id'] ?? 0) === (int) $resident['id'] ? 'checked' : '' ?>>
                                    <span>
                                        <strong><?= esc($resident['first_name'] . ' ' . $resident['last_name']) ?></strong>
                                        <span style="display:block;font-size:12px;color:var(--ink-muted)">Resident ID #<?= esc($resident['id']) ?><?= !empty($resident['sitio']) ? ' • ' . esc($resident['sitio']) : '' ?></span>
                                    </span>
                                </label>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="ds-card">
                <div class="ds-card-header">
                    <h3>Admin Actions</h3>
                </div>
                <div class="ds-card-body">
                    <form method="post" action="<?= base_url('admin/portal-accounts/approve/' . $verification['id']) ?>" style="margin-bottom:14px">
                        <?= csrf_field() ?>
                        <label class="ds-form-label">Resident ID to activate</label>
                        <input id="resident_id" class="ds-input" type="number" name="resident_id" value="<?= esc((string) ($verification['resident_id'] ?? $verification['account_resident_id'] ?? '')) ?>" required>

                        <div style="height:10px"></div>
                        <label class="ds-form-label">Review Notes</label>
                        <textarea class="ds-input" name="review_notes" rows="3" placeholder="Optional admin notes"></textarea>

                        <div style="height:10px"></div>
                        <label class="ds-form-label">OTP Requirement</label>
                        <select class="ds-select" name="otp_required" id="otp_required" onchange="toggleOtpChannel()">
                            <option value="0">Activate account immediately after approval</option>
                            <option value="1" <?= ($verification['otp_required'] ?? 0) ? 'selected' : '' ?>>Require OTP after approval</option>
                        </select>

                        <div id="otp_channel_wrap" style="margin-top:10px;display:none">
                            <label class="ds-form-label">OTP Channel</label>
                            <select class="ds-select" name="otp_channel">
                                <option value="sms">SMS</option>
                                <option value="email">Email</option>
                            </select>
                        </div>

                        <button type="submit" class="ds-btn ds-btn-teal" style="margin-top:14px;width:100%">
                            <i class="fas fa-check"></i> Approve Verification
                        </button>
                    </form>

                    <form method="post" action="<?= base_url('admin/portal-accounts/request-resubmission/' . $verification['id']) ?>" style="margin-bottom:14px">
                        <?= csrf_field() ?>
                        <label class="ds-form-label">Request New Documents</label>
                        <textarea class="ds-input" name="resubmission_reason" rows="3" required placeholder="Explain what needs to be re-uploaded or corrected."></textarea>
                        <button type="submit" class="ds-btn ds-btn-amber" style="margin-top:12px;width:100%">
                            <i class="fas fa-upload"></i> Request Resubmission
                        </button>
                    </form>

                    <form method="post" action="<?= base_url('admin/portal-accounts/reject/' . $verification['id']) ?>">
                        <?= csrf_field() ?>
                        <label class="ds-form-label">Reject Verification</label>
                        <textarea class="ds-input" name="rejection_reason" rows="3" required placeholder="Explain why this verification is being rejected."></textarea>
                        <button type="submit" class="ds-btn ds-btn-danger" style="margin-top:12px;width:100%">
                            <i class="fas fa-times"></i> Reject Verification
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div style="margin-top:16px">
        <a href="<?= base_url('admin/portal-accounts') ?>" class="ds-btn ds-btn-ghost" style="background:var(--white)">
            <i class="fas fa-arrow-left"></i> Back to Verification Queue
        </a>
    </div>
</div>

<script>
function toggleOtpChannel() {
    const select = document.getElementById('otp_required');
    const wrap = document.getElementById('otp_channel_wrap');
    wrap.style.display = select.value === '1' ? 'block' : 'none';
}
toggleOtpChannel();
</script>
<?= $this->endSection() ?>

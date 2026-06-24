<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= ($mode ?? 'register') === 'resubmit' ? 'Resubmit Verification' : 'Resident Registration' ?></title>
    <link rel="stylesheet" href="<?= base_url('assets/adminlte/plugins/fontawesome-free/css/all.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/bmis-design-system.css') ?>">
    <style>
        body { background: var(--bg); margin:0; font-family: var(--font); color: var(--ink); }
        .wrap { min-height: 100vh; display:flex; align-items:center; justify-content:center; padding: 24px; }
        .card { width: 100%; max-width: 760px; background: var(--white); border: .5px solid var(--border); border-radius: var(--r-lg); box-shadow: var(--shadow); overflow:hidden; }
        .head { padding: 18px 20px; border-bottom: .5px solid var(--border); display:flex; align-items:center; gap:10px; }
        .icon { width:40px; height:40px; border-radius: 12px; background: var(--c-teal-bg); color: var(--c-teal); display:flex; align-items:center; justify-content:center; }
        .body { padding: 18px 20px; }
        .grid { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
        .grid-3 { display:grid; grid-template-columns:1fr 1fr 1fr; gap:12px; }
        .section { padding: 16px; border: 1px solid var(--border); border-radius: 16px; margin-bottom: 14px; background: #fcfcfd; }
        .section-title { display:flex; align-items:center; gap:8px; margin-bottom:12px; font-weight:800; color: var(--ink); }
        .hint { font-size:12px; color: var(--ink-muted); margin-top:6px; }
        .file-box { border:1px dashed var(--border); border-radius: 14px; padding:12px; background: var(--white); }
        @media (max-width:560px){ .grid { grid-template-columns:1fr; } }
        @media (max-width:760px){ .grid-3 { grid-template-columns:1fr; } }
    </style>
</head>
<body>
<?php
$verification = $verification ?? [];
$account = $account ?? [];
$firstName = old('first_name', $verification['first_name'] ?? '');
$middleName = old('middle_name', $verification['middle_name'] ?? '');
$lastName = old('last_name', $verification['last_name'] ?? '');
$birthdate = old('birthdate', $verification['birthdate'] ?? '');
$address = old('address', $verification['address_submitted'] ?? '');
$email = old('email', $verification['contact_email_submitted'] ?? ($account['email'] ?? ''));
$phone = old('phone', $verification['contact_phone_submitted'] ?? ($account['phone'] ?? ''));
$nationalIdNumber = old('national_id_number', $verification['national_id_number'] ?? '');
$otpChannel = old('otp_channel', $verification['otp_channel'] ?? (!empty($phone) ? 'sms' : 'email'));
?>
<div class="wrap">
    <div class="card">
        <div class="head">
            <div class="icon"><i class="fas fa-user-plus"></i></div>
            <div>
                <div style="font-weight:800;font-size:16px">
                    <?= ($mode ?? 'register') === 'resubmit' ? 'Resubmit Resident Verification' : 'Resident Registration & ID Verification' ?>
                </div>
                <div style="font-size:12px;color:var(--ink-muted)">
                    Upload a valid national ID so the barangay can review and approve your portal access.
                </div>
            </div>
        </div>
        <div class="body">
            <?php if (session()->getFlashdata('error')): ?>
                <div style="background:var(--c-rose-bg);color:var(--c-rose);padding:12px 14px;border-radius:12px;margin-bottom:12px;border:1px solid rgba(var(--c-rose-rgb),.2)">
                    <?= esc(session()->getFlashdata('error')) ?>
                </div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('success')): ?>
                <div style="background:var(--c-green-bg);color:var(--c-green);padding:12px 14px;border-radius:12px;margin-bottom:12px;border:1px solid rgba(var(--c-green-rgb),.2)">
                    <?= esc(session()->getFlashdata('success')) ?>
                </div>
            <?php endif; ?>

            <form method="post" action="<?= esc($formAction ?? base_url('portal/register')) ?>" enctype="multipart/form-data">
                <?= csrf_field() ?>

                <div class="section">
                    <div class="section-title"><i class="fas fa-id-card"></i> Identity Details</div>
                    <div class="grid-3">
                        <div>
                            <label class="ds-input-label">First Name</label>
                            <input class="ds-input" name="first_name" type="text" value="<?= esc($firstName) ?>" placeholder="e.g., Juan" required>
                        </div>
                        <div>
                            <label class="ds-input-label">Middle Name</label>
                            <input class="ds-input" name="middle_name" type="text" value="<?= esc($middleName) ?>" placeholder="Optional">
                        </div>
                        <div>
                            <label class="ds-input-label">Last Name</label>
                            <input class="ds-input" name="last_name" type="text" value="<?= esc($lastName) ?>" placeholder="e.g., Dela Cruz" required>
                        </div>
                    </div>

                    <div style="height:10px"></div>
                    <div class="grid">
                        <div>
                            <label class="ds-input-label">Birthdate</label>
                            <input class="ds-input" name="birthdate" type="date" value="<?= esc($birthdate) ?>" required>
                        </div>
                        <div>
                            <label class="ds-input-label">National ID Number</label>
                            <input class="ds-input" name="national_id_number" type="text" value="<?= esc($nationalIdNumber) ?>" placeholder="Enter your valid government ID number" required>
                        </div>
                    </div>
                </div>

                <div class="section">
                    <div class="section-title"><i class="fas fa-map-marker-alt"></i> Address & Contact</div>
                    <label class="ds-input-label">Current Address</label>
                    <textarea class="ds-input" name="address" rows="3" placeholder="House no., street, purok / sitio, barangay" required><?= esc($address) ?></textarea>

                    <div style="height:10px"></div>
                    <div class="grid">
                        <div>
                            <label class="ds-input-label">Email</label>
                            <input class="ds-input" name="email" type="email" value="<?= esc($email) ?>" placeholder="you@email.com">
                        </div>
                        <div>
                            <label class="ds-input-label">Phone</label>
                            <input class="ds-input" name="phone" value="<?= esc($phone) ?>" placeholder="09xxxxxxxxx">
                        </div>
                    </div>

                    <div style="height:10px"></div>
                    <label class="ds-input-label">Preferred OTP Channel</label>
                    <select class="ds-select" name="otp_channel">
                        <option value="sms" <?= $otpChannel === 'sms' ? 'selected' : '' ?>>SMS</option>
                        <option value="email" <?= $otpChannel === 'email' ? 'selected' : '' ?>>Email</option>
                    </select>
                    <div class="hint">At least one contact method is required. OTP is used only when admin enables second confirmation.</div>
                </div>

                <?php if (($mode ?? 'register') === 'register'): ?>
                <div class="section">
                    <div class="section-title"><i class="fas fa-lock"></i> Portal Credentials</div>
                    <div class="grid">
                        <div>
                            <label class="ds-input-label">Password</label>
                            <input class="ds-input" type="password" name="password" required minlength="8">
                        </div>
                        <div>
                            <label class="ds-input-label">Confirm Password</label>
                            <input class="ds-input" type="password" name="confirm_password" required minlength="8">
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <div class="section">
                    <div class="section-title"><i class="fas fa-file-upload"></i> National ID Upload</div>
                    <?php if (($mode ?? 'register') === 'resubmit' && !empty($verification['resubmission_reason'])): ?>
                        <div style="background:var(--c-amber-bg);color:#9a6700;padding:12px 14px;border-radius:12px;margin-bottom:12px;border:1px solid rgba(245,158,11,.25)">
                            <strong>Admin note:</strong> <?= esc($verification['resubmission_reason']) ?>
                        </div>
                    <?php endif; ?>

                    <div class="grid">
                        <div class="file-box">
                            <label class="ds-input-label">National ID Front</label>
                            <input class="ds-input" name="national_id_front" type="file" accept=".jpg,.jpeg,.png,.webp" required>
                            <div class="hint">Required. Upload a clear photo of the front side.</div>
                        </div>
                        <div class="file-box">
                            <label class="ds-input-label">National ID Back</label>
                            <input class="ds-input" name="national_id_back" type="file" accept=".jpg,.jpeg,.png,.webp">
                            <div class="hint">Optional but recommended.</div>
                        </div>
                    </div>

                    <div style="height:10px"></div>
                    <div class="file-box">
                        <label class="ds-input-label">Supporting Document</label>
                        <input class="ds-input" name="supporting_document" type="file" accept=".jpg,.jpeg,.png,.webp,.pdf">
                        <div class="hint">Optional. Barangay certificate, proof of address, or another supporting document.</div>
                    </div>
                </div>

                <div class="section" style="background:#f8fafc">
                    <div class="section-title"><i class="fas fa-shield-alt"></i> Privacy Notice</div>
                    <div style="font-size:13px;color:var(--ink-muted);line-height:1.6">
                        Your uploaded ID images are used only for resident verification and can only be viewed by authorized admin reviewers.
                        Please make sure the information is accurate and the uploaded files are readable.
                    </div>
                </div>

                <div style="display:flex;gap:10px;margin-top:14px">
                    <button class="ds-btn ds-btn-teal" style="flex:1;height:44px">
                        <i class="fas fa-paper-plane"></i>
                        <?= ($mode ?? 'register') === 'resubmit' ? 'Resubmit Verification' : 'Submit Verification' ?>
                    </button>
                    <a class="ds-btn ds-btn-ghost" style="height:40px;background:var(--white)" href="<?= base_url('portal/login') ?>"><i class="fas fa-arrow-left"></i></a>
                </div>

                <div style="margin-top:14px;font-size:12px;color:var(--ink-muted)">
                    After submission, your account stays pending until the barangay verifies your ID. If required, you will also be asked to confirm your contact method with OTP.
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>

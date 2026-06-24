<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verification Status</title>
    <link rel="stylesheet" href="<?= base_url('assets/adminlte/plugins/fontawesome-free/css/all.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/bmis-design-system.css') ?>">
    <style>
        body { background: var(--bg); margin:0; font-family: var(--font); color: var(--ink); }
        .wrap { min-height: 100vh; display:flex; align-items:center; justify-content:center; padding: 24px; }
        .card { width:100%; max-width: 760px; background: var(--white); border: .5px solid var(--border); border-radius: var(--r-lg); box-shadow: var(--shadow); overflow:hidden; }
        .head { padding: 18px 20px; border-bottom: .5px solid var(--border); display:flex; align-items:center; gap:12px; }
        .icon { width:44px; height:44px; border-radius: 14px; background: var(--c-blue-bg); color: var(--c-blue); display:flex; align-items:center; justify-content:center; }
        .body { padding: 20px; }
        .grid { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
        .box { border:1px solid var(--border); border-radius: 16px; padding: 16px; background:#fcfcfd; }
        .status { display:inline-flex; align-items:center; gap:8px; padding:8px 12px; border-radius:999px; font-size:12px; font-weight:800; text-transform:uppercase; letter-spacing:.03em; }
        .status.pending { background: #eff6ff; color: #1d4ed8; }
        .status.resubmit { background: #fff7ed; color: #c2410c; }
        .status.otp { background: #ecfeff; color: #0f766e; }
        .status.verified { background: #ecfdf5; color: #047857; }
        .status.rejected { background: #fff1f2; color: #be123c; }
        @media (max-width:720px){ .grid { grid-template-columns:1fr; } }
    </style>
</head>
<body>
<?php
$status = $verification['status'] ?? 'pending_admin_review';
$statusClass = match ($status) {
    'needs_resubmission' => 'resubmit',
    'pending_otp' => 'otp',
    'verified' => 'verified',
    'rejected' => 'rejected',
    default => 'pending',
};
$statusLabel = str_replace('_', ' ', $status);
?>
<div class="wrap">
    <div class="card">
        <div class="head">
            <div class="icon"><i class="fas fa-user-check"></i></div>
            <div>
                <div style="font-weight:800;font-size:18px">Resident Verification Status</div>
                <div style="font-size:12px;color:var(--ink-muted)">Track your ID review progress before portal activation.</div>
            </div>
        </div>

        <div class="body">
            <?php if (session()->getFlashdata('success')): ?>
                <div style="background:var(--c-green-bg);color:var(--c-green);padding:12px 14px;border-radius:12px;margin-bottom:14px;border:1px solid rgba(var(--c-green-rgb),.2)">
                    <?= esc(session()->getFlashdata('success')) ?>
                </div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('error')): ?>
                <div style="background:var(--c-rose-bg);color:var(--c-rose);padding:12px 14px;border-radius:12px;margin-bottom:14px;border:1px solid rgba(var(--c-rose-rgb),.2)">
                    <?= esc(session()->getFlashdata('error')) ?>
                </div>
            <?php endif; ?>

            <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-bottom:16px">
                <div>
                    <div style="font-size:13px;color:var(--ink-muted)">Current status</div>
                    <div class="status <?= $statusClass ?>"><?= esc($statusLabel) ?></div>
                </div>
                <div style="font-size:12px;color:var(--ink-muted)">
                    Submitted:
                    <strong><?= !empty($verification['submitted_at']) ? esc(date('M d, Y h:i A', strtotime($verification['submitted_at']))) : '—' ?></strong>
                </div>
            </div>

            <div class="grid">
                <div class="box">
                    <div style="font-size:12px;color:var(--ink-muted);text-transform:uppercase;font-weight:700;margin-bottom:8px">Submitted Identity</div>
                    <div style="font-weight:800;font-size:18px"><?= esc(trim(($verification['first_name'] ?? '') . ' ' . ($verification['last_name'] ?? ''))) ?></div>
                    <div style="font-size:13px;color:var(--ink-muted);margin-top:8px"><?= esc($verification['address_submitted'] ?? 'No address submitted') ?></div>
                    <div style="margin-top:10px;font-size:13px">
                        <div><strong>Email:</strong> <?= esc($verification['contact_email_submitted'] ?? $account['email'] ?? '—') ?></div>
                        <div><strong>Phone:</strong> <?= esc($verification['contact_phone_submitted'] ?? $account['phone'] ?? '—') ?></div>
                        <div><strong>National ID:</strong> <?= esc($verification['national_id_number'] ?? '—') ?></div>
                    </div>
                </div>

                <div class="box">
                    <div style="font-size:12px;color:var(--ink-muted);text-transform:uppercase;font-weight:700;margin-bottom:8px">What Happens Next</div>
                    <?php if ($status === 'pending_admin_review'): ?>
                        <p style="margin:0 0 10px">Your uploaded documents are waiting for admin review. You cannot access resident-only features yet.</p>
                    <?php elseif ($status === 'needs_resubmission'): ?>
                        <p style="margin:0 0 10px">The admin requested new files or clearer information.</p>
                        <div style="background:#fff7ed;padding:12px;border-radius:12px;font-size:13px;color:#9a6700">
                            <?= esc($verification['resubmission_reason'] ?? 'Please upload a clearer ID image.') ?>
                        </div>
                    <?php elseif ($status === 'pending_otp'): ?>
                        <p style="margin:0 0 10px">Your ID was approved. Finish the last step by entering the OTP sent to your selected contact method.</p>
                    <?php elseif ($status === 'verified'): ?>
                        <p style="margin:0 0 10px">Your verification is complete and your account is active.</p>
                    <?php elseif ($status === 'rejected'): ?>
                        <p style="margin:0 0 10px">Your verification was rejected.</p>
                        <div style="background:#fff1f2;padding:12px;border-radius:12px;font-size:13px;color:#be123c">
                            <?= esc($verification['rejection_reason'] ?? 'No rejection reason was provided.') ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div style="display:flex;gap:10px;flex-wrap:wrap;margin-top:18px">
                <?php if ($status === 'needs_resubmission'): ?>
                    <a href="<?= base_url('portal/resubmit-verification') ?>" class="ds-btn ds-btn-teal" style="height:42px">
                        <i class="fas fa-upload"></i> Upload New Documents
                    </a>
                <?php endif; ?>

                <?php if ($status === 'pending_otp'): ?>
                    <a href="<?= base_url('portal/verify-otp') ?>" class="ds-btn ds-btn-teal" style="height:42px">
                        <i class="fas fa-key"></i> Enter OTP
                    </a>
                <?php endif; ?>

                <?php if ($status === 'verified'): ?>
                    <a href="<?= base_url('portal/login') ?>" class="ds-btn ds-btn-teal" style="height:42px">
                        <i class="fas fa-sign-in-alt"></i> Log In
                    </a>
                <?php endif; ?>

                <a href="<?= base_url('portal/login') ?>" class="ds-btn ds-btn-ghost" style="height:42px;background:var(--white)">
                    <i class="fas fa-arrow-left"></i> Back to Login
                </a>
            </div>
        </div>
    </div>
</div>
</body>
</html>

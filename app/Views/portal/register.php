<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resident Registration</title>
    <link rel="stylesheet" href="<?= base_url('assets/adminlte/plugins/fontawesome-free/css/all.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/bmis-design-system.css') ?>">
    <style>
        body { background: var(--bg); margin:0; font-family: var(--font); color: var(--ink); }
        .wrap { min-height: 100vh; display:flex; align-items:center; justify-content:center; padding: 24px; }
        .card { width: 100%; max-width: 520px; background: var(--white); border: .5px solid var(--border); border-radius: var(--r-lg); box-shadow: var(--shadow); overflow:hidden; }
        .head { padding: 18px 20px; border-bottom: .5px solid var(--border); display:flex; align-items:center; gap:10px; }
        .icon { width:40px; height:40px; border-radius: 12px; background: var(--c-teal-bg); color: var(--c-teal); display:flex; align-items:center; justify-content:center; }
        .body { padding: 18px 20px; }
        .grid { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
        @media (max-width:560px){ .grid { grid-template-columns:1fr; } }
    </style>
</head>
<body>
<div class="wrap">
    <div class="card">
        <div class="head">
            <div class="icon"><i class="fas fa-user-plus"></i></div>
            <div>
                <div style="font-weight:800;font-size:16px">Resident Registration</div>
                <div style="font-size:12px;color:var(--ink-muted)">Phase 1A scaffold — activation flow will be added</div>
            </div>
        </div>
        <div class="body">
            <?php if (session()->getFlashdata('error')): ?>
                <div style="background:var(--c-rose-bg);color:var(--c-rose);padding:12px 14px;border-radius:12px;margin-bottom:12px;border:1px solid rgba(var(--c-rose-rgb),.2)">
                    <?= esc(session()->getFlashdata('error')) ?>
                </div>
            <?php endif; ?>

            <form method="post" action="<?= base_url('portal/register') ?>">
                <?= csrf_field() ?>
                <div class="grid">
                    <div>
                        <label class="ds-input-label">Resident ID (from BMIS)</label>
                        <input class="ds-input" name="resident_id" type="number" min="1" placeholder="e.g., 123" required>
                    </div>
                    <div>
                        <label class="ds-input-label">Phone (optional)</label>
                        <input class="ds-input" name="phone" placeholder="09xxxxxxxxx">
                    </div>
                </div>
                <div style="height:10px"></div>
                <label class="ds-input-label">Email (optional)</label>
                <input class="ds-input" name="email" placeholder="you@email.com">

                <div style="height:10px"></div>
                <label class="ds-input-label">Password</label>
                <input class="ds-input" type="password" name="password" required>

                <div style="display:flex;gap:10px;margin-top:14px">
                    <button class="ds-btn ds-btn-teal" style="flex:1;height:40px"><i class="fas fa-paper-plane"></i> Submit</button>
                    <a class="ds-btn ds-btn-ghost" style="height:40px;background:var(--white)" href="<?= base_url('portal/login') ?>"><i class="fas fa-arrow-left"></i></a>
                </div>
            </form>

            <div style="margin-top:14px;font-size:12px;color:var(--ink-muted)">
                Note: For Phase 1C, we’ll add verification (OTP/activation) and resident lookup instead of manual Resident ID.
            </div>
        </div>
    </div>
</div>
</body>
</html>


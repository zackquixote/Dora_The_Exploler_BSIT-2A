<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Event Check-in</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f6f7fb; margin: 0; padding: 0; }
        .wrap { max-width: 720px; margin: 40px auto; padding: 18px; }
        .card { background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; padding: 18px; }
        .ok { color: #065f46; }
        .bad { color: #991b1b; }
        .muted { color: #6b7280; font-size: 13px; }
    </style>
</head>
<body>
<div class="wrap">
    <div class="card">
        <?php if (!empty($success)): ?>
            <h2 class="ok">✅ <?= esc($message ?? 'Success') ?></h2>
            <p class="muted">You may close this page after confirming the details.</p>

            <hr>
            <p><strong>Event:</strong> <?= esc($event['title'] ?? '—') ?></p>
            <p><strong>Participant:</strong>
                <?= esc(($resident['first_name'] ?? '') . ' ' . ($resident['last_name'] ?? '')) ?>
            </p>
            <p><strong>Checked-in at:</strong> <?= esc($participant['check_in_time'] ?? '—') ?></p>
        <?php else: ?>
            <h2 class="bad">❌ <?= esc($message ?? 'Failed') ?></h2>
            <p class="muted">If this QR keeps failing, regenerate the participant QR and try again.</p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>


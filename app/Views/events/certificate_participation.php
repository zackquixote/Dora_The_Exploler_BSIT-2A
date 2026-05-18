<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; margin: 0; padding: 0; }
        .page { width: 100%; padding: 40px; }
        .border { border: 6px double #111; padding: 40px; height: 520px; }
        .title { text-align: center; font-size: 34px; font-weight: 700; letter-spacing: 1px; }
        .subtitle { text-align: center; margin-top: 6px; font-size: 16px; }
        .name { text-align: center; margin-top: 48px; font-size: 36px; font-weight: 700; }
        .body { text-align: center; margin-top: 20px; font-size: 16px; line-height: 1.6; }
        .footer { display: flex; justify-content: space-between; margin-top: 70px; }
        .sig { width: 45%; text-align: center; }
        .line { border-top: 1px solid #111; margin-top: 36px; }
        .small { font-size: 12px; color: #333; margin-top: 6px; }
    </style>
</head>
<body>
<div class="page">
    <div class="border">
        <div class="title">CERTIFICATE OF PARTICIPATION</div>
        <div class="subtitle">This certificate is proudly presented to</div>

        <div class="name">
            <?= esc(($resident['first_name'] ?? '') . ' ' . ($resident['last_name'] ?? '')) ?>
        </div>

        <div class="body">
            for participating in the event<br>
            <strong><?= esc($event['title'] ?? '—') ?></strong><br>
            held at <strong><?= esc($event['venue'] ?? '—') ?></strong><br>
            on <strong><?= esc(date('F j, Y', strtotime($event['start_date'] ?? 'now'))) ?></strong>.
        </div>

        <div class="footer">
            <div class="sig">
                <div class="line"></div>
                <div>Event Organizer</div>
                <div class="small">Signature over printed name</div>
            </div>
            <div class="sig">
                <div class="line"></div>
                <div>Barangay Official</div>
                <div class="small">Signature over printed name</div>
            </div>
        </div>

        <div class="small" style="text-align:center;margin-top:22px;">
            Certificate generated on <?= esc(date('F j, Y')) ?> • Participant ID: <?= esc($participant['id'] ?? '') ?>
        </div>
    </div>
</div>
</body>
</html>


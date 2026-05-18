<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'Permit Renewal') ?></title>
    <style>
        body { font-family: Arial, sans-serif; color: #111; }
        .wrap { width: 800px; margin: 0 auto; padding: 24px; }
        .header { text-align: center; margin-bottom: 18px; }
        .row { display: flex; justify-content: space-between; gap: 16px; }
        .box { border: 1px solid #333; padding: 12px; margin-top: 12px; }
        .label { font-weight: bold; }
        @media print { .no-print { display: none; } .wrap { width: auto; } }
    </style>
</head>
<body>
<div class="wrap">
    <div class="header">
        <h2>Business Permit Renewal</h2>
        <div>Renewal Year: <strong><?= esc($renewal['renewal_year'] ?? '') ?></strong></div>
        <div>Status: <strong><?= esc($renewal['status'] ?? '') ?></strong></div>
    </div>

    <div class="box">
        <div class="row">
            <div>
                <div><span class="label">Business Name:</span> <?= esc($businessPermit['business_name'] ?? '—') ?></div>
                <div><span class="label">Permit #:</span> <?= esc($businessPermit['business_permit_number'] ?? '—') ?></div>
                <div><span class="label">Business Type:</span> <?= esc($businessPermit['business_type'] ?? '—') ?></div>
            </div>
            <div>
                <div><span class="label">Amount Due:</span> <?= esc($renewal['amount_due'] ?? '0.00') ?></div>
                <div><span class="label">Paid At:</span> <?= esc($renewal['paid_at'] ?? '—') ?></div>
                <div><span class="label">Approved At:</span> <?= esc($renewal['approved_at'] ?? '—') ?></div>
            </div>
        </div>
        <div style="margin-top:10px;">
            <div><span class="label">Business Address:</span> <?= esc($businessPermit['business_address'] ?? '—') ?></div>
            <div><span class="label">Remarks:</span> <?= esc($renewal['remarks'] ?? '—') ?></div>
        </div>
    </div>

    <div class="box">
        <div class="label">Attachments</div>
        <div style="font-size: 13px;">
            Upload requirements/receipts here using Document Management API:<br>
            <code>entity_type=permit_renewal</code>, <code>entity_id=<?= (int) ($renewal['id'] ?? 0) ?></code>
        </div>
    </div>

    <div class="no-print" style="margin-top: 16px;">
        <button onclick="window.print()">Print</button>
        <p style="font-size:12px;color:#555;">
            Note: to mark this renewal as printed in the system, call:
            <code>POST /api/permit-renewals/<?= (int) ($renewal['id'] ?? 0) ?>/mark-printed</code>
        </p>
    </div>
</div>
</body>
</html>


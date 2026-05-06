<?php
$barangayName = $barangay['barangay_name'] ?? 'Barangay';
$munProv = trim(($barangay['municipality'] ?? '') . ', ' . ($barangay['province'] ?? ''), ', ');
$caseNumber = esc($case['case_number']);
$incidentDate = date('F d, Y', strtotime($case['incident_date']));
$status = esc($case['status']);
$type = esc($case['incident_type']);
$location = esc($case['incident_location'] ?? '');
$purok = esc($case['purok'] ?? '');
$details = esc($case['details']);
$action = esc($case['action_taken'] ?? '');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= $barangayName ?> – Case <?= $caseNumber ?></title>
    <style>
        body {
            font-family: 'Times New Roman', serif;
            margin: 0;
            padding: 1.5cm;
            font-size: 12pt;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        .header h2 {
            margin: 0;
        }
        .section-title {
            font-size: 14pt;
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 10px;
            border-left: 4px solid #333;
            padding-left: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            vertical-align: top;
        }
        th {
            background-color: #f2f2f2;
        }
        .signature-block {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }
        .signature {
            text-align: center;
            width: 45%;
        }
        .signature p {
            margin-bottom: 40px;
        }
        @media print {
            .no-print { display: none; }
            body { margin: 0; padding: 0; }
        }
    </style>
</head>
<body>
<div class="header">
    <h2><?= strtoupper($barangayName) ?></h2>
    <p><?= $munProv ?></p>
    <p><strong>Case Number:</strong> <?= $caseNumber ?></p>
    <p><strong>Date of Incident:</strong> <?= $incidentDate ?></p>
</div>

<div class="section-title">Incident Report</div>
<table>
    <tr><th>Type</th><td><?= $type ?></td></tr>
    <tr><th>Location</th><td><?= $location ?: 'Not specified' ?></td></tr>
    <tr><th>Purok</th><td><?= $purok ?: 'Not specified' ?></td></tr>
    <tr><th>Status</th><td><strong><?= $status ?></strong></td></tr>
    <tr><th>Action Taken</th><td><?= nl2br($action ?: 'None recorded') ?></td></tr>
    <tr><th>Narrative</th><td><?= nl2br($details) ?></td></tr>
</table>

<div class="section-title">Parties Involved</div>
<?php foreach (['complainant','respondent','witness'] as $role): ?>
    <?php if (!empty($parties[$role])): ?>
        <p><strong><?= ucfirst($role) ?>s:</strong></p>
        <ul>
            <?php foreach ($parties[$role] as $p): ?>
                <li>
                    <?php if (!empty($p['resident_id'])): ?>
                        <?= esc($p['resident_name']) ?> (Resident #<?= $p['resident_id'] ?>)
                    <?php else: ?>
                        <?= esc($p['outsider_name']) ?><?= !empty($p['outsider_address']) ? ' — ' . esc($p['outsider_address']) : '' ?>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
<?php endforeach; ?>

<?php if (!empty($hearings)): ?>
<div class="section-title">Hearings / Proceedings</div>
<table>
    <thead>
        <tr><th>Date</th><th>Time</th><th>Venue</th><th>Officer</th><th>Status</th>
    </thead>
    <tbody>
        <?php foreach ($hearings as $h): ?>
        <tr>
            <td><?= date('M d, Y', strtotime($h['hearing_date'])) ?></td>
            <td><?= $h['hearing_time'] ? date('h:i A', strtotime($h['hearing_time'])) : '—' ?></td>
            <td><?= esc($h['venue']) ?></td>
            <td><?= esc($h['presiding_officer']) ?></td>
            <td><?= esc($h['status']) ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>

<?php if (!empty($timeline)): ?>
<div class="section-title">Status History</div>
<ul>
    <?php foreach ($timeline as $entry): ?>
        <li><strong><?= date('M d, Y H:i', strtotime($entry['created_at'])) ?></strong> – 
            <?= $entry['old_status'] ? "Changed from “{$entry['old_status']}” to “{$entry['new_status']}”" : "Initial status: “{$entry['new_status']}”" ?>
            <?= !empty($entry['remarks']) ? ' – ' . esc($entry['remarks']) : '' ?>
        </li>
    <?php endforeach; ?>
</ul>
<?php endif; ?>

<div class="signature-block">
    <div class="signature">
        <p><?= $case['created_by_name'] ?? 'Recording Officer' ?></p>
        <small>Recorded by</small>
    </div>
    <div class="signature">
        <p><?= $barangayName ?></p>
        <small>Barangay</small>
    </div>
</div>

<div class="no-print" style="text-align:center; margin-top:30px;">
    <button onclick="window.print()">🖨 Print</button>
    <a href="<?= base_url('blotter/view/'.$case['id']) ?>">Back to Case</a>
</div>
</body>
</html>
<?php
// Official Barangay Blotter – Case Summary for Printing
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $barangayName ?> – Case <?= $caseNumber ?></title>
    <link rel="stylesheet" href="<?= base_url('assets/css/blotter/print.css') ?>">
</head>
<body>

<div class="header">
    <h2><?= strtoupper($barangayName) ?></h2>
    <p><?= $munProv ?></p>
    <p><strong>Case Number:</strong> <?= $caseNumber ?></p>
    <p><strong>Date of Incident:</strong> <?= $incidentDate ?></p>
</div>

<h3>Incident Details</h3>
<table>
    <tr><th>Type</th><td><?= $type ?></td></tr>
    <tr><th>Location</th><td><?= $location ?: 'Not specified' ?></td></tr>
    <tr><th>Purok</th><td><?= $purok ?: 'Not specified' ?></td></tr>
    <tr><th>Status</th><td><strong class="status-<?= strtolower($status) ?>"><?= $status ?></strong></td></tr>
    <tr><th>Action Taken</th><td><?= nl2br($action ?: 'None recorded') ?></td></tr>
    <tr><th>Narrative</th><td><?= nl2br($details) ?></td></tr>
</table>

<h3>Parties Involved</h3>
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
<h3>Hearings / Proceedings</h3>
<table>
    <thead>
        <tr><th>Date</th><th>Time</th><th>Venue</th><th>Officer</th><th>Status</th><th>Outcome</th></tr>
    </thead>
    <tbody>
        <?php foreach ($hearings as $h): ?>
        <tr>
            <td><?= date('M d, Y', strtotime($h['hearing_date'])) ?></td>
            <td><?= $h['hearing_time'] ? date('h:i A', strtotime($h['hearing_time'])) : '' ?></td>
            <td><?= esc($h['venue']) ?></td>
            <td><?= esc($h['presiding_officer']) ?></td>
            <td><?= esc($h['status']) ?></td>
            <td><?= esc($h['outcome'] ?? '') ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>

<?php if (!empty($timeline)): ?>
<h3>Status History</h3>
<ul>
    <?php foreach ($timeline as $entry): ?>
        <li>
            <strong><?= date('M d, Y H:i', strtotime($entry['created_at'])) ?></strong> –
            <?php if ($entry['old_status']): ?>
                Changed from “<?= esc($entry['old_status']) ?>” to “<?= esc($entry['new_status']) ?>”
            <?php else: ?>
                Initial status: “<?= esc($entry['new_status']) ?>”
            <?php endif; ?>
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
    <button onclick="window.print()" style="padding:8px 20px; font-size:14px;">🖨 Print this page</button>
    <a href="<?= base_url('blotter/view/' . $case['id']) ?>" style="margin-left:10px;">Back to Case</a>
</div>

</body>
</html>
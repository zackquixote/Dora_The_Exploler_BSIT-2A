<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Amicable Settlement - <?= esc($case['case_number']) ?></title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            margin: 0;
            padding: 0;
            background: #fff;
            color: #000;
        }
        .page {
            width: 21cm;
            min-height: 29.7cm;
            padding: 2cm 2cm;
            margin: 0 auto;
            box-sizing: border-box;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header p {
            margin: 2px 0;
            font-size: 14pt;
        }
        .title {
            text-align: center;
            font-size: 18pt;
            font-weight: bold;
            margin: 40px 0 30px;
            text-transform: uppercase;
            text-decoration: underline;
        }
        .case-info {
            text-align: right;
            font-size: 12pt;
            margin-bottom: 20px;
        }
        .content {
            font-size: 12pt;
            line-height: 1.8;
            text-align: justify;
        }
        .parties {
            display: grid;
            grid-template-columns: 1fr 1fr;
            margin-bottom: 30px;
        }
        .signatures {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-top: 60px;
        }
        .sig-box {
            text-align: center;
        }
        .sig-line {
            border-bottom: 1px solid #000;
            margin-bottom: 5px;
            height: 40px;
        }
        .captain {
            margin-top: 80px;
            text-align: center;
            width: 50%;
            margin-left: auto;
            margin-right: auto;
        }
        @media print {
            body { background: none; }
            .page { margin: 0; padding: 1.5cm; }
            @page { size: A4 portrait; margin: 0; }
        }
    </style>
</head>
<body onload="window.print()">

<div class="page">
    <div class="header">
        <p>Republic of the Philippines</p>
        <p>Province of <?= esc($barangay['province'] ?? '____________________') ?></p>
        <p>Municipality of <?= esc($barangay['municipality'] ?? '____________________') ?></p>
        <p><strong>BARANGAY <?= esc($barangay['barangay_name'] ?? '____________________') ?></strong></p>
        <p>OFFICE OF THE LUPONG TAGAPAMAYAPA</p>
    </div>

    <div class="case-info">
        Case No: <strong><?= esc($case['case_number']) ?></strong><br>
        For: <strong><?= esc($case['incident_type']) ?></strong>
    </div>

    <div class="parties">
        <div>
            <u>Complainant/s:</u><br>
            <?php if (!empty($parties['complainant'])): ?>
                <?php foreach($parties['complainant'] as $p): ?>
                    <strong><?= esc($p['resident_name'] ?? $p['outsider_name']) ?></strong><br>
                <?php endforeach; ?>
            <?php else: ?>
                ________________________<br>
            <?php endif; ?>
        </div>
        <div>
            <u>Respondent/s:</u><br>
            <?php if (!empty($parties['respondent'])): ?>
                <?php foreach($parties['respondent'] as $p): ?>
                    <strong><?= esc($p['resident_name'] ?? $p['outsider_name']) ?></strong><br>
                <?php endforeach; ?>
            <?php else: ?>
                ________________________<br>
            <?php endif; ?>
        </div>
    </div>

    <div class="title">Amicable Settlement</div>

    <div class="content">
        <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;WE, the complainant(s) and respondent(s) in the above-captioned case, do hereby agree to settle our dispute(s) amicably and voluntarily bind ourselves to the following settlement:</p>
        
        <p style="padding-left: 20px; font-style: italic;">
            <?= nl2br(esc($case['action_taken'] ?? '_________________________________________________________________________________________________________________________________________________________________________________________________________________')) ?>
        </p>

        <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;WE FURTHER AGREE that we will strictly comply with the terms of this settlement. Failure to comply with the terms and conditions hereof shall be a ground for the immediate execution of this agreement.</p>
        
        <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Entered into this <strong><?= date('jS') ?></strong> day of <strong><?= date('F, Y') ?></strong> at Barangay <?= esc($barangay['barangay_name'] ?? '______________') ?>, <?= esc($barangay['municipality'] ?? '______________') ?>, <?= esc($barangay['province'] ?? '______________') ?>.</p>
    </div>

    <div class="signatures">
        <div class="sig-box">
            <div class="sig-line"></div>
            <strong>COMPLAINANT/S</strong><br>
            (Signature over printed name)
        </div>
        <div class="sig-box">
            <div class="sig-line"></div>
            <strong>RESPONDENT/S</strong><br>
            (Signature over printed name)
        </div>
    </div>

    <div class="captain">
        <div style="font-size: 11pt; text-align: left; margin-bottom: 40px;">ATTESTED BY:</div>
        <div class="sig-line"></div>
        <strong>PUNONG BARANGAY / LUPONG CHAIRMAN</strong>
    </div>

</div>

</body>
</html>

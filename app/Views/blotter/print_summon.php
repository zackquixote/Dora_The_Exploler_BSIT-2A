<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Summon - <?= esc($case['case_number']) ?></title>
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
            padding: 2cm 2.5cm;
            margin: 0 auto;
            box-sizing: border-box;
        }
        .header {
            text-align: center;
            margin-bottom: 40px;
        }
        .header p {
            margin: 2px 0;
            font-size: 14pt;
        }
        .title {
            text-align: center;
            font-size: 24pt;
            font-weight: bold;
            margin: 30px 0;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .case-info {
            text-align: right;
            font-size: 12pt;
            margin-bottom: 30px;
        }
        .content {
            font-size: 12pt;
            line-height: 2;
            text-align: justify;
        }
        .parties {
            display: grid;
            grid-template-columns: 1fr 1fr;
            margin-bottom: 20px;
        }
        .to-section {
            margin: 40px 0;
        }
        .captain {
            margin-top: 60px;
            text-align: right;
            width: 50%;
            margin-left: auto;
        }
        .sig-line {
            border-bottom: 1px solid #000;
            margin-bottom: 5px;
            height: 40px;
        }
        @media print {
            body { background: none; }
            .page { margin: 0; padding: 2cm; }
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
        <div style="text-align: right">
            Case No: <strong><?= esc($case['case_number']) ?></strong><br>
            For: <strong><?= esc($case['incident_type']) ?></strong>
        </div>
    </div>

    <div class="title">S U M M O N S</div>

    <div class="to-section">
        <strong>TO: Respondent/s</strong><br>
        <?php if (!empty($parties['respondent'])): ?>
            <?php foreach($parties['respondent'] as $p): ?>
                <span style="font-size: 14pt; border-bottom: 1px solid #000; padding: 0 10px;"><?= esc($p['resident_name'] ?? $p['outsider_name']) ?></span><br>
                <span style="font-size: 10pt; color: #555;"><?= esc($p['outsider_address'] ?? 'Barangay ' . ($barangay['barangay_name'] ?? '')) ?></span><br>
            <?php endforeach; ?>
        <?php else: ?>
            ________________________<br>
        <?php endif; ?>
    </div>

    <div class="content">
        <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;You are hereby summoned to appear before me in person, together with your witnesses, on the <strong><?= date('jS', strtotime($hearing['hearing_date'])) ?></strong> day of <strong><?= date('F Y', strtotime($hearing['hearing_date'])) ?></strong> at <strong><?= $hearing['hearing_time'] ? date('h:i A', strtotime($hearing['hearing_time'])) : '________' ?></strong>, at the <strong><?= esc($hearing['venue'] ?? 'Barangay Hall') ?></strong>, then and there to answer to a complaint made before me, copy of which is attached hereto, for mediation/conciliation of your dispute with complainant/s.</p>

        <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;You are hereby warned that if you refuse or willfully fail to appear in obedience to this summons, you may be barred from filing any counterclaim arising from said complaint.</p>
        
        <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;FAIL NOT or else face punishment as for contempt of court.</p>

        <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;This <strong><?= date('jS') ?></strong> day of <strong><?= date('F, Y') ?></strong>.</p>
    </div>

    <div class="captain">
        <div class="sig-line"></div>
        <strong style="text-align: center; display: block;">PUNONG BARANGAY / LUPONG CHAIRMAN</strong>
    </div>
</div>

</body>
</html>

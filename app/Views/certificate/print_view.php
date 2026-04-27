<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Barangay Certificate</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Old+Standard+TT:ital,wght@0,400;0,700;1,400&display=swap');

        body {
            background-color: #ccc; 
            font-family: "Times New Roman", Times, serif;
            padding: 20px;
            display: flex;
            justify-content: center;
        }

        .paper {
            background: white;
            width: 216mm; /* Legal width usually, or 210mm A4 */
            min-height: 330mm; /* Adjusted height for content */
            padding: 25mm;
            box-shadow: 0 0 15px rgba(0,0,0,0.2);
            position: relative;
        }

        /* HEADER STYLING - Updated for Logo */
        .header {
            display: flex; /* Aligns Logo and Text side by side */
            align-items: center;
            border-bottom: 3px double #000;
            padding-bottom: 20px;
            margin-bottom: 40px;
        }

        .header img {
            height: 90px; /* Size of the Logo */
            width: auto;
            margin-right: 20px; /* Space between logo and text */
        }

        .header-text {
            text-align: center;
            flex-grow: 1; /* Takes up remaining space */
        }

        .header-text h5 {
            font-family: sans-serif;
            font-size: 11pt;
            margin: 0;
            text-transform: uppercase;
            font-weight: bold;
            letter-spacing: 1px;
        }
        
        .header-text h4 {
            font-family: sans-serif;
            font-size: 13pt;
            font-weight: bold;
            margin: 5px 0;
            text-transform: uppercase;
        }

        .header-text h3 {
            font-family: "Times New Roman", serif;
            font-weight: 900;
            margin: 5px 0;
            text-transform: uppercase;
            font-size: 20pt;
        }

        .header-text small {
            font-family: sans-serif;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 10pt;
        }

        /* CONTENT STYLING */
        .cert-title {
            text-align: center;
            font-weight: bold;
            font-size: 18pt;
            margin-bottom: 30px;
            text-decoration: underline;
            text-transform: uppercase;
        }

        .body-text {
            text-align: justify;
            font-size: 13pt;
            line-height: 2.2;
            text-indent: 50px;
        }

        .name-text {
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: underline;
        }

        /* Indigency Specific (Optional) */
        .indigency-box {
            border: 1px solid #999;
            padding: 15px;
            margin: 20px 50px;
            text-align: center;
            font-style: italic;
            font-size: 12pt;
        }

        /* FOOTER STYLING */
        .footer {
            margin-top: 60px;
            text-align: center;
        }
        
        .sign-box {
            display: inline-block;
            width: 60%;
        }
        
        .sign-name {
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 60px;
            border-top: 1px solid #000;
            padding-top: 5px;
        }

        /* CONTROLS */
        .controls {
            position: fixed;
            top: 20px;
            right: 20px;
            background: white;
            padding: 10px;
            border-radius: 5px;
        }

        /* PRINT */
        @media print {
            body { background: none; padding: 0; -webkit-print-color-adjust: exact; }
            .paper { box-shadow: none; margin: 0; width: 100%; padding: 0; }
            .controls { display: none; }
            @page { margin: 10mm; }
        }
    </style>
</head>
<body>

    <!-- Controls -->
    <div class="controls no-print">
        <button onclick="window.print()" class="btn btn-primary">
            <i class="fas fa-print"></i> Print
        </button>
        <button onclick="window.close()" class="btn btn-secondary">
            <i class="fas fa-times"></i> Close
        </button>
    </div>

    <!-- The Paper -->
    <div class="paper">
        <!-- Header with Logo -->
        <div class="header">
            <!-- Logo -->
            <img src="<?= esc($settings->logo_url) ?>" alt="Barangay Logo">
            
            <!-- Text -->
            <div class="header-text">
                <h5>Republic of the Philippines</h5>
                <h5>Province of <?= esc($settings->province) ?></h5>
                <h4>MUNICIPALITY OF <?= esc($settings->municipality) ?></h4>
                <h3>BARANGAY <?= esc($settings->barangay_name) ?></h3>
                <small>Office of the Punong Barangay</small>
            </div>
        </div>

        <!-- Title -->
        <div class="cert-title">
            <?= esc($cert['certificate_type']) ?>
        </div>

        <!-- Body Content -->
        <div class="body-text">
            <p>TO WHOM IT MAY CONCERN:</p>
            
            <p>
                This is to certify that <span class="name-text">
                    <?= esc($cert['first_name']) ?> 
                    <?= esc($cert['middle_name'] ?? '') ?> 
                    <?= esc($cert['last_name']) ?>
                </span>, 
                <?= esc(ucfirst($cert['sex'])) ?>, 
                <?= esc(ucfirst($cert['civil_status'])) ?>, 
                is a bonafide resident of Purok <?= esc($cert['sitio'] ?? 'N/A') ?>, Barangay <?= esc($settings->barangay_name) ?>, Municipality of <?= esc($settings->municipality) ?>.
            </p>

            <!-- Conditional Indigency Text -->
            <?php if ($cert['certificate_type'] == 'Certificate of Indigency'): ?>
                <p>
                    This certification is issued to certify that the above-named person belongs to the indigent family/sector of this barangay.
                </p>
            <?php endif; ?>

            <p>
                This certification is being issued upon the request of the interested party for 
                <strong><?= esc($cert['purpose']) ?></strong>.
            </p>

            <p>
                Issued this <?= date('d') ?> day of <?= date('F') ?>, <?= date('Y') ?> at Barangay <?= esc($settings->barangay_name) ?>.
            </p>
        </div>

        <!-- Signature -->
        <div class="footer">
            <div class="sign-box">
                <div class="sign-name"><?= esc($settings->captain_name) ?></div>
                <div>Punong Barangay</div>
            </div>
        </div>
    </div>

</body>
</html>
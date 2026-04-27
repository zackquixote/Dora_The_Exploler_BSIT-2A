<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Barangay Certificate</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600;700&family=EB+Garamond:ital,wght@0,400;0,600;1,400&display=swap');

        /* ============================================================
           PAGE / BODY
        ============================================================ */
        body {
            background-color: #7a7a7a;
            font-family: "EB Garamond", "Times New Roman", Times, serif;
            padding: 30px 20px;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
        }

        /* ============================================================
           PAPER
        ============================================================ */
        .paper {
            background: #fffef9;
            width: 216mm;
            min-height: 330mm;
            padding: 22mm 25mm;
            box-shadow: 0 6px 40px rgba(0,0,0,0.45);
            position: relative;
            overflow: hidden;
        }

        /* Outer decorative border */
        .paper::before {
            content: '';
            position: absolute;
            inset: 10px;
            border: 2.5px double #1a3a6c;
            pointer-events: none;
            z-index: 0;
        }

        /* Inner hairline border */
        .paper::after {
            content: '';
            position: absolute;
            inset: 15px;
            border: 1px solid #c5a84b;
            pointer-events: none;
            z-index: 0;
        }

        /* Subtle diagonal watermark */
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-35deg);
            font-family: 'Cinzel', serif;
            font-size: 72pt;
            font-weight: 700;
            color: rgba(26, 58, 108, 0.035);
            letter-spacing: 8px;
            text-transform: uppercase;
            white-space: nowrap;
            pointer-events: none;
            z-index: 0;
            user-select: none;
        }

        /* Ensure content is above decorative elements */
        .content-wrapper { position: relative; z-index: 1; }

        /* ============================================================
           HEADER — Logo | Text | Logo
        ============================================================ */
        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding-bottom: 14px;
            margin-bottom: 24px;
            border-bottom: 3px double #1a3a6c;
        }

        /* Left and Right logos */
        .header-logo {
            width: 88px;
            height: 88px;
            object-fit: contain;
            flex-shrink: 0;
            filter: drop-shadow(0 1px 3px rgba(0,0,0,0.15));
        }

        /* Center text block */
        .header-text {
            text-align: center;
            flex: 1;
            line-height: 1.35;
        }

        .header-text .line-republic {
            font-family: Arial Narrow, Arial, sans-serif;
            font-size: 9.5pt;
            font-weight: bold;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: #222;
            margin: 0 0 2px;
        }

        .header-text .line-province {
            font-family: Arial Narrow, Arial, sans-serif;
            font-size: 9.5pt;
            font-weight: bold;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: #333;
            margin: 0 0 3px;
        }

        .header-text .line-municipality {
            font-family: Arial Narrow, Arial, sans-serif;
            font-size: 11.5pt;
            font-weight: bold;
            letter-spacing: 2.5px;
            text-transform: uppercase;
            color: #1a3a6c;
            margin: 0 0 3px;
        }

        .header-text .line-barangay {
            font-family: 'Cinzel', serif;
            font-size: 18pt;
            font-weight: 700;
            text-transform: uppercase;
            color: #1a3a6c;
            letter-spacing: 3px;
            margin: 2px 0 4px;
        }

        .header-text .line-office {
            font-family: Arial Narrow, Arial, sans-serif;
            font-size: 8.5pt;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #666;
            margin: 0;
            font-style: italic;
        }

        /* Thin gold rule under header text */
        .header-text hr.gold-rule {
            border: none;
            border-top: 1px solid #c5a84b;
            margin: 5px auto 5px;
            width: 80%;
        }

        /* ============================================================
           DOCUMENT NUMBER
        ============================================================ */
        .doc-meta {
            display: flex;
            justify-content: space-between;
            font-size: 9pt;
            color: #666;
            font-style: italic;
            margin-bottom: 10px;
        }

        /* ============================================================
           CERTIFICATE TITLE
        ============================================================ */
        .cert-title-wrap {
            text-align: center;
            margin: 20px 0 26px;
        }

        .cert-title {
            display: inline-block;
            font-family: 'Cinzel', serif;
            font-size: 14pt;
            font-weight: 700;
            letter-spacing: 5px;
            text-transform: uppercase;
            color: #1a3a6c;
            padding: 9px 36px;
            border-top: 2px solid #1a3a6c;
            border-bottom: 2px solid #1a3a6c;
            position: relative;
        }

        .cert-title::before,
        .cert-title::after {
            content: '✦';
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            color: #c5a84b;
            font-size: 11pt;
        }
        .cert-title::before { left: 10px; }
        .cert-title::after  { right: 10px; }

        /* ============================================================
           BODY TEXT
        ============================================================ */
        .body-text {
            text-align: justify;
            font-size: 12.5pt;
            line-height: 2.1;
            color: #111;
        }

        .body-text p {
            margin-bottom: 0.4em;
        }

        .body-text .greeting {
            text-indent: 0;
            font-weight: bold;
            font-size: 11pt;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: #1a3a6c;
        }

        .body-text .indent {
            text-indent: 60px;
        }

        .name-text {
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: underline;
            text-underline-offset: 4px;
            text-decoration-color: #c5a84b;
            color: #1a3a6c;
        }

        /* Indigency special box */
        .indigency-notice {
            border: 1px solid #c5a84b;
            background: #fffbf0;
            padding: 10px 18px;
            margin: 14px 0;
            font-style: italic;
            font-size: 11.5pt;
            color: #444;
            border-radius: 2px;
        }

        /* ============================================================
           OFFICIAL SEAL AREA (left, near footer)
        ============================================================ */
        .seal-area {
            position: absolute;
            left: 25mm;
            bottom: 45mm;
        }

        .seal-label {
            font-size: 8pt;
            color: #aaa;
            text-align: center;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-top: 4px;
        }

        /* ============================================================
           FOOTER / SIGNATURE
        ============================================================ */
        .footer {
            margin-top: 55px;
            display: flex;
            justify-content: flex-end;
        }

        .sign-block {
            text-align: center;
            width: 58%;
        }

        .sign-name {
            font-family: 'Cinzel', serif;
            font-size: 11pt;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            border-top: 1.5px solid #1a3a6c;
            padding-top: 7px;
            color: #1a3a6c;
            margin-top: 70px;
        }

        .sign-title {
            font-size: 10pt;
            color: #555;
            font-style: italic;
            letter-spacing: 0.5px;
            margin-top: 2px;
        }

        /* ============================================================
           CONTROLS (screen only)
        ============================================================ */
        .controls {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        /* ============================================================
           PRINT STYLES
        ============================================================ */
        @media print {
            body {
                background: none;
                padding: 0;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .paper {
                box-shadow: none;
                margin: 0;
                width: 100%;
                padding: 15mm 18mm;
                min-height: unset;
            }
            .controls { display: none !important; }
            @page { margin: 8mm; size: legal portrait; }
        }
    </style>
</head>
<body>

    <!-- ================================
         CONTROLS (no-print)
    ================================ -->
    <div class="controls no-print">
        <button onclick="window.print()" class="btn btn-primary btn-sm">
            <i class="fas fa-print mr-1"></i> Print
        </button>
        <button onclick="window.close()" class="btn btn-secondary btn-sm">
            <i class="fas fa-times mr-1"></i> Close
        </button>
    </div>

    <!-- ================================
         THE PAPER
    ================================ -->
    <div class="paper">

        <!-- Watermark -->
        <div class="watermark">Official</div>

        <div class="content-wrapper">

            <!-- ========================
                 HEADER
            ======================== -->
            <div class="header">

                <!-- LEFT LOGO: Municipal / Province Seal (ilog.png) -->
                <img
                    src="/assets/img/ilog.png"
                    alt="Municipal Seal"
                    class="header-logo">

                <!-- CENTER TEXT -->
                <div class="header-text">
                    <hr class="gold-rule">
                    <p class="line-republic">Republic of the Philippines</p>
                    <p class="line-province">Province of <?= esc($settings->province) ?></p>
                    <p class="line-municipality">Municipality of <?= esc($settings->municipality) ?></p>
                    <p class="line-barangay">Barangay <?= esc($settings->barangay_name) ?></p>
                    <p class="line-office">Office of the Punong Barangay</p>
                    <hr class="gold-rule">
                </div>

                <!-- RIGHT LOGO: Barangay Seal (tabu.jpg) -->
                <img
                    src="/assets/img/tabu.jpg"
                    alt="Barangay Seal"
                    class="header-logo">

            </div>
            <!-- END HEADER -->

            <!-- Document number + date issued -->
            <div class="doc-meta">
                <span>Cert. No.: _____________</span>
                <span>Date Issued: <?= date('F d, Y') ?></span>
            </div>

            <!-- ========================
                 CERTIFICATE TITLE
            ======================== -->
            <div class="cert-title-wrap">
                <div class="cert-title">
                    <?= esc($cert['certificate_type']) ?>
                </div>
            </div>

            <!-- ========================
                 BODY
            ======================== -->
            <div class="body-text">

                <p class="greeting">To Whom It May Concern:</p>
                <br>

                <p class="indent">
                    This is to certify that
                    <span class="name-text">
                        <?= esc($cert['first_name']) ?>
                        <?= esc($cert['middle_name'] ?? '') ?>
                        <?= esc($cert['last_name']) ?>
                    </span>,
                    <?= esc(ucfirst($cert['sex'])) ?>,
                    <?= esc(ucfirst($cert['civil_status'])) ?>,
                    of legal age, is a <em>bonafide</em> resident of
                    Purok <?= esc($cert['sitio'] ?? 'N/A') ?>,
                    Barangay <?= esc($settings->barangay_name) ?>,
                    Municipality of <?= esc($settings->municipality) ?>,
                    Province of <?= esc($settings->province) ?>.
                </p>
                <br>

                <?php if ($cert['certificate_type'] === 'Certificate of Indigency'): ?>
                <div class="indigency-notice">
                    This certification further attests that the above-named person
                    belongs to the indigent family/sector of this barangay and is
                    entitled to assistance under applicable government programs.
                </div>
                <?php endif; ?>

                <p class="indent">
                    This certification is being issued upon the request of the
                    interested party for
                    <strong><?= esc($cert['purpose']) ?></strong>
                    and for whatever legal purpose it may serve.
                </p>
                <br>

                <p class="indent">
                    Issued this <strong><?= date('jS') ?></strong> day of
                    <strong><?= date('F') ?></strong>,
                    <strong><?= date('Y') ?></strong>
                    at Barangay <?= esc($settings->barangay_name) ?>,
                    Municipality of <?= esc($settings->municipality) ?>.
                </p>

            </div>
            <!-- END BODY -->

            <!-- ========================
                 SIGNATURE BLOCK
            ======================== -->
            <div class="footer">
                <div class="sign-block">
                    <div class="sign-name"><?= esc($settings->captain_name) ?></div>
                    <div class="sign-title">Punong Barangay</div>
                </div>
            </div>

        </div>
        <!-- END content-wrapper -->

    </div>
    <!-- END PAPER -->

</body>
</html>
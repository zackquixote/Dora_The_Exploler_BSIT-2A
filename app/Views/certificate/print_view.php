<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Certificate Print</title>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=Cinzel:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --gold: #b8962e;
            --gold-light: #d4af37;
            --gold-dark: #8b6914;
            --gold-pale: #f5e9c4;
            --ink: #1a1209;
            --ink-soft: #2c1f0e;
            --parchment: #fdf8ef;
            --parchment-dark: #f5edd8;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Cormorant Garamond', 'Times New Roman', serif;
            background: #2a2318;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
            padding: 24px;
            background-image: repeating-linear-gradient(
                45deg, transparent, transparent 35px,
                rgba(184,150,46,0.03) 35px, rgba(184,150,46,0.03) 70px
            );
        }

        /* ─── Paper ─── */
        .paper {
            background: var(--parchment);
            width: 210mm;
            min-height: 297mm;
            position: relative;
            overflow: hidden;
            box-shadow:
                0 0 0 1px rgba(139,105,20,0.3),
                0 4px 6px rgba(0,0,0,0.3),
                0 20px 60px rgba(0,0,0,0.5),
                inset 0 0 80px rgba(184,150,46,0.06);
        }

        /* Parchment noise texture */
        .paper::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='300' height='300'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.75' numOctaves='4' stitchTiles='stitch'/%3E%3CfeColorMatrix type='saturate' values='0'/%3E%3C/filter%3E%3Crect width='300' height='300' filter='url(%23n)' opacity='0.04'/%3E%3C/svg%3E");
            pointer-events: none;
            z-index: 1;
        }

        /* ─── Decorative Borders ─── */
        .border-outer  { position: absolute; inset: 10px; border: 2px solid var(--gold); z-index: 2; pointer-events: none; }
        .border-inner  { position: absolute; inset: 16px; border: 1px solid var(--gold-dark); z-index: 2; pointer-events: none; }
        .border-pattern{ position: absolute; inset: 19px; border: 3px double var(--gold); z-index: 2; pointer-events: none; }

        .side-ornament { position: absolute; z-index: 2; pointer-events: none; }
        .side-ornament.left  { left: 26px;  top: 80px; bottom: 80px; width: 2px; background: linear-gradient(to bottom, transparent, var(--gold) 10%, var(--gold) 90%, transparent); }
        .side-ornament.right { right: 26px; top: 80px; bottom: 80px; width: 2px; background: linear-gradient(to bottom, transparent, var(--gold) 10%, var(--gold) 90%, transparent); }

        /* ─── Corner SVGs ─── */
        .corner { position: absolute; width: 70px; height: 70px; z-index: 3; }
        .corner svg { width: 100%; height: 100%; }
        .corner-tl { top: 10px; left: 10px; }
        .corner-tr { top: 10px; right: 10px; transform: scaleX(-1); }
        .corner-bl { bottom: 10px; left: 10px; transform: scaleY(-1); }
        .corner-br { bottom: 10px; right: 10px; transform: scale(-1); }

        /* ─── Watermark ─── */
        .watermark {
            position: absolute;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            width: 280px; height: 280px;
            opacity: 0.04;
            z-index: 1;
            pointer-events: none;
        }

        /* ─── Content Wrapper ─── */
        .content-wrap {
            position: relative;
            z-index: 4;
            padding: 34px 46px;
            display: flex;
            flex-direction: column;
            min-height: 297mm;
        }

        /* ─── Header Logos ─── */
        .header-logos {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 16px;
            margin-bottom: 10px;
            position: relative;
        }
        .header-logos::after {
            content: '';
            position: absolute;
            bottom: 0; left: 0; right: 0;
            height: 1px;
            background: linear-gradient(to right, transparent, var(--gold) 20%, var(--gold) 80%, transparent);
        }

        .logo-img {
            width: 82px;
            height: 82px;
            border-radius: 50%;
            border: 2px solid var(--gold);
            object-fit: cover;
            box-shadow: 0 0 0 1px var(--gold-dark), 0 0 0 4px var(--gold-pale), 0 0 0 5px var(--gold-dark);
            flex-shrink: 0;
        }

        .logo-center-text {
            text-align: center;
            flex: 1;
            padding: 0 16px;
        }
        .logo-center-text .republic {
            font-family: 'Cinzel', serif;
            font-size: 8.5pt;
            letter-spacing: 0.25em;
            color: var(--ink-soft);
            text-transform: uppercase;
            display: block;
            margin-bottom: 3px;
        }
        .logo-center-text .province {
            font-family: 'Cinzel', serif;
            font-size: 7.5pt;
            letter-spacing: 0.15em;
            color: var(--gold-dark);
            display: block;
            margin-bottom: 2px;
        }
        .logo-center-text .municipality {
            font-family: 'Cinzel', serif;
            font-size: 13pt;
            font-weight: 700;
            color: var(--ink);
            display: block;
            line-height: 1.1;
        }
        .logo-center-text .barangay {
            font-family: 'Cinzel', serif;
            font-size: 10pt;
            font-weight: 600;
            color: var(--gold-dark);
            display: block;
            margin-top: 2px;
            letter-spacing: 0.08em;
        }
        .logo-center-text .office {
            font-family: 'Cormorant Garamond', serif;
            font-size: 8pt;
            color: var(--ink-soft);
            display: block;
            margin-top: 4px;
            font-style: italic;
            opacity: 0.75;
        }

        /* ─── Gold Divider ─── */
        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 14px 0 10px;
        }
        .divider-line     { flex: 1; height: 1px; background: linear-gradient(to right, transparent, var(--gold)); }
        .divider-line.rev { background: linear-gradient(to left, transparent, var(--gold)); }
        .divider-diamond  { width: 8px; height: 8px; background: var(--gold); transform: rotate(45deg); flex-shrink: 0; }
        .divider-diamond.sm { width: 5px; height: 5px; background: transparent; border: 1px solid var(--gold); }

        /* ─── Certificate Title ─── */
        .cert-title-band { text-align: center; padding: 10px 0 8px; }
        .cert-title-band .cert-type-label {
            font-family: 'Cinzel', serif;
            font-size: 8pt;
            letter-spacing: 0.4em;
            color: var(--gold-dark);
            text-transform: uppercase;
            display: block;
            margin-bottom: 4px;
        }
        .cert-title-band h1 {
            font-family: 'Cinzel', serif;
            font-size: 26pt;
            font-weight: 700;
            color: var(--ink);
            letter-spacing: 0.06em;
            line-height: 1;
            text-shadow: 1px 1px 0 rgba(184,150,46,0.15);
        }
        .cert-title-band h2 {
            font-family: 'Cinzel', serif;
            font-size: 14pt;
            font-weight: 400;
            color: var(--gold-dark);
            letter-spacing: 0.2em;
            margin-top: 3px;
        }

        /* ─── Certificate Body (your $content goes here) ─── */
        .cert-body {
            flex: 1;
            padding: 16px 20px;
            font-family: 'Cormorant Garamond', serif;
            font-size: 12.5pt;
            color: var(--ink);
            line-height: 1.85;
            text-align: justify;
        }

        /* ─── Official Seal ─── */
        .official-seal { display: flex; justify-content: center; margin: 14px 0 6px; }
        .seal-ring {
            width: 90px; height: 90px;
            border-radius: 50%;
            border: 3px solid var(--gold);
            box-shadow: 0 0 0 1px var(--gold-dark), 0 0 0 4px var(--gold-pale), 0 0 0 5px var(--gold-dark);
            display: flex;
            align-items: center;
            justify-content: center;
            background: radial-gradient(circle, var(--gold-pale), var(--parchment));
            font-family: 'Cinzel', serif;
            font-size: 6.5pt;
            color: var(--gold-dark);
            text-align: center;
            letter-spacing: 0.08em;
            line-height: 1.5;
            text-transform: uppercase;
        }

        /* ─── Signatories ─── */
        .signatories {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
            padding: 0 10px;
            gap: 20px;
        }
        .signatory { text-align: center; flex: 1; }
        .sig-line  { width: 100%; height: 1px; background: var(--ink); margin-bottom: 6px; }
        .sig-name  { font-family: 'Cinzel', serif; font-size: 9.5pt; font-weight: 600; color: var(--ink); display: block; line-height: 1.3; }
        .sig-title { font-family: 'Cormorant Garamond', serif; font-size: 8.5pt; color: var(--gold-dark); font-style: italic; display: block; }

        /* ─── Footer ─── */
        .footer-band {
            margin-top: 18px;
            padding-top: 12px;
            border-top: 1px solid var(--gold);
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            font-family: 'Cormorant Garamond', serif;
            font-size: 8pt;
            color: var(--gold-dark);
            font-style: italic;
        }
        .doc-no { font-family: 'Cinzel', serif; font-size: 7.5pt; letter-spacing: 0.1em; font-style: normal; }

        /* ─── Print ─── */
        @media print {
            body { background: white; padding: 0; }
            .paper { box-shadow: none; margin: 0; width: 100%; }
        }
    </style>
</head>
<body>
<div class="paper">

    <!-- Borders -->
    <div class="border-outer"></div>
    <div class="border-inner"></div>
    <div class="border-pattern"></div>
    <div class="side-ornament left"></div>
    <div class="side-ornament right"></div>

    <!-- Corner Ornaments -->
    <?php
    $corner_svg = '<svg viewBox="0 0 70 70" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M5 5 L30 5 L5 30 Z" fill="none" stroke="#b8962e" stroke-width="1"/>
        <path d="M5 5 L60 5" stroke="#b8962e" stroke-width="1.5"/>
        <path d="M5 5 L5 60" stroke="#b8962e" stroke-width="1.5"/>
        <circle cx="5" cy="5" r="3" fill="#b8962e"/>
        <path d="M12 5 L5 12" stroke="#b8962e" stroke-width="0.5" opacity="0.5"/>
        <path d="M20 5 L5 20" stroke="#b8962e" stroke-width="0.5" opacity="0.4"/>
        <path d="M28 5 L5 28" stroke="#b8962e" stroke-width="0.5" opacity="0.3"/>
        <rect x="3" y="3" width="4" height="4" fill="none" stroke="#b8962e" stroke-width="0.5" transform="rotate(45 5 5)"/>
    </svg>';
    ?>
    <div class="corner corner-tl"><?= $corner_svg ?></div>
    <div class="corner corner-tr"><?= $corner_svg ?></div>
    <div class="corner corner-bl"><?= $corner_svg ?></div>
    <div class="corner corner-br"><?= $corner_svg ?></div>

    <!-- Watermark -->
    <svg class="watermark" viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
        <circle cx="100" cy="100" r="95" stroke="#b8962e" stroke-width="2"/>
        <circle cx="100" cy="100" r="80" stroke="#b8962e" stroke-width="1"/>
        <circle cx="100" cy="100" r="65" stroke="#b8962e" stroke-width="1.5"/>
        <path d="M100 30 L115 70 L158 70 L124 94 L136 134 L100 110 L64 134 L76 94 L42 70 L85 70 Z" stroke="#b8962e" stroke-width="1.5"/>
    </svg>

    <!-- Main Content -->
    <div class="content-wrap">

        <!-- Header: Logos + Title Text -->
        <div class="header-logos">
            <img src="<?= base_url('assets/img/ilog.png') ?>" alt="Barangay Logo" class="logo-img">

            <div class="logo-center-text">
                <span class="republic">Republic of the Philippines</span>
                <span class="province">Province of Negros Occidental</span>
                <span class="municipality">Municipality of Ilog</span>
                <span class="barangay">Barangay Tabu</span>
                <span class="office">Office of the Barangay Council</span>
            </div>

            <img src="<?= base_url('assets/img/tabu.jpg') ?>" alt="Municipal Logo" class="logo-img">
        </div>

        <!-- Top Divider -->
        <div class="divider">
            <div class="divider-line"></div>
            <div class="divider-diamond sm"></div>
            <div class="divider-diamond"></div>
            <div class="divider-diamond sm"></div>
            <div class="divider-line rev"></div>
        </div>

        <!-- Certificate Title (customize cert type label / title / subtitle as needed) -->
        <div class="cert-title-band">
            <span class="cert-type-label">Official Document</span>
            <h1>Certificate</h1>
            <h2>of Residency</h2>
        </div>

        <!-- Bottom title divider -->
        <div class="divider" style="margin: 10px 0 0;">
            <div class="divider-line"></div>
            <div class="divider-diamond sm"></div>
            <div class="divider-diamond"></div>
            <div class="divider-diamond sm"></div>
            <div class="divider-line rev"></div>
        </div>

        <!-- Certificate Content (your $content variable) -->
        <div class="cert-body">
            <?php if (empty($content)): ?>
                <div style="text-align:center; padding:50px; color:red; border:1px solid red;">
                    <h1>Content is Empty</h1>
                    <p>Template not found in database.</p>
                </div>
            <?php else: ?>
                <?= $content ?>
            <?php endif; ?>
        </div>

        <!-- Official Seal -->
        <div class="official-seal">
            <div class="seal-ring">
                Official<br>Seal<br>————<br>Barangay<br>Ilog
            </div>
        </div>

        <!-- Signatories -->
        <div class="signatories">
            <div class="signatory">
                <div class="sig-line"></div>
                <span class="sig-name">Punong Barangay</span>
                <span class="sig-title">Barangay Captain</span>
            </div>
            <div class="signatory" style="max-width:160px;">
                <div style="height:36px; display:flex; align-items:flex-end; justify-content:center; margin-bottom:6px;">
                    <span style="font-family:'Cormorant Garamond',serif; font-size:8pt; color:var(--gold-dark); font-style:italic;">Date: ________________</span>
                </div>
                <div style="font-family:'Cinzel',serif; font-size:7pt; color:var(--gold-dark); text-align:center; letter-spacing:0.08em; line-height:1.6;">
                    Doc. No. _____ Page No. _____<br>
                    Book No. _____ Series of <?= date('Y') ?>
                </div>
            </div>
            <div class="signatory">
                <div class="sig-line"></div>
                <span class="sig-name">Barangay Secretary</span>
                <span class="sig-title">Secretary</span>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer-band">
            <span class="doc-no">OR No.: __________</span>
            <span>Not valid without official dry seal</span>
            <span class="doc-no">CTC No.: __________</span>
        </div>

    </div><!-- /content-wrap -->
</div><!-- /paper -->
</body>
</html>
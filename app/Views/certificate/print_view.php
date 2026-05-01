<?php
// This view expects:
// $cert     – the result from CertificateModel::getCertificateForPrint()
// $content  – the template HTML with placeholders already replaced
// $settings – barangay settings array (or we can use $cert fields)

// Use values from $cert if available (fallback to $settings)
$province     = $cert['province']      ?? $settings['province']      ?? '';
$municipality = $cert['municipality']  ?? $settings['municipality']  ?? '';
$barangay     = $cert['barangay_name'] ?? $settings['barangay_name'] ?? '';
$captainName  = $cert['captain_name']   ?? 'Punong Barangay';
$secretaryName= $cert['secretary_name'] ?? '';
$certNumber   = $cert['certificate_number'] ?? '';

// Remove the placeholder HTML that was inside $content if it's embedded;
// we rely on the controller having done the replacements.
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate – <?= esc($certNumber ?: 'Print') ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=Cinzel:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/certificate/print_view.css') ?>">
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

        <!-- Header -->
        <div class="header-logos">
            <img src="<?= base_url('assets/img/ilog.png') ?>" alt="Barangay Logo" class="logo-img">

            <div class="logo-center-text">
                <span class="republic">Republic of the Philippines</span>
                <span class="province">Province of <?= esc($province) ?></span>
                <span class="municipality">Municipality of <?= esc($municipality) ?></span>
                <span class="barangay">Barangay <?= esc($barangay) ?></span>
                <span class="office">Office of the Barangay Council</span>
            </div>

            <img src="<?= base_url('assets/img/tabu.jpg') ?>" alt="Municipal Logo" class="logo-img">
        </div>

        <div class="divider">
            <div class="divider-line"></div>
            <div class="divider-diamond sm"></div>
            <div class="divider-diamond"></div>
            <div class="divider-diamond sm"></div>
            <div class="divider-line rev"></div>
        </div>

        <!-- Certificate Title -->
        <div class="cert-title-band">
            <span class="cert-type-label">Official Document</span>
            <h1>Certificate</h1>
            <h2><?= esc($cert['certificate_type'] ?? '') ?></h2>
        </div>

        <div class="divider" style="margin: 10px 0 0;">
            <div class="divider-line"></div>
            <div class="divider-diamond sm"></div>
            <div class="divider-diamond"></div>
            <div class="divider-diamond sm"></div>
            <div class="divider-line rev"></div>
        </div>

        <!-- Certificate Content -->
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

        <!-- Signatories – now dynamic -->
        <div class="signatories">
            <div class="signatory">
                <div class="sig-line"></div>
                <span class="sig-name"><?= esc($captainName ?: 'Punong Barangay') ?></span>
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
                <span class="sig-name"><?= esc($secretaryName ?: 'Barangay Secretary') ?></span>
                <span class="sig-title">Secretary</span>
            </div>
        </div>

        <!-- Footer with Certificate Number -->
        <div class="footer-band">
            <span class="doc-no">Cert No.: <?= esc($certNumber ?: '_______') ?></span>
            <span>Not valid without official dry seal</span>
            <span class="doc-no">CTC No.: __________</span>
        </div>

    </div><!-- /content-wrap -->
</div><!-- /paper -->
</body>
</html>
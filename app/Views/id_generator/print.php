<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?> - <?= esc($resident['first_name'] . ' ' . $resident['last_name']) ?></title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=DM+Mono:wght@500&display=swap" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --c-blue: #4F46E5;
            --c-teal: #10B981;
            --c-navy: #0f172a;
            --c-gray: #475569;
            --font: 'Plus Jakarta Sans', sans-serif;
            --mono: 'DM Mono', monospace;
        }
        
        body {
            font-family: var(--font);
            background: #f1f5f9;
            margin: 0;
            padding: 40px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 40px;
        }

        .controls {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
            display: flex;
            gap: 16px;
        }

        .btn {
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn-primary { background: var(--c-blue); color: white; }
        .btn-secondary { background: #e2e8f0; color: var(--c-navy); }

        .id-container {
            display: flex;
            gap: 40px;
            flex-wrap: wrap;
            justify-content: center;
        }

        /* CR80 standard size (3.375" x 2.125"). We use a multiplier for high-res viewing/printing */
        .id-card {
            width: 3.375in;
            height: 2.125in;
            background: white;
            border-radius: 0.125in;
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
            position: relative;
            overflow: hidden;
            border: 1px solid #e2e8f0;
            /* Scale up for preview, print will scale to fit */
            transform: scale(1.5);
            transform-origin: top center;
            margin-bottom: 1.5in; /* Account for the scale */
        }

        .id-card.back {
            background: #f8fafc;
        }

        /* Front Side Design */
        .id-header {
            background: linear-gradient(135deg, var(--c-blue), var(--c-teal));
            color: white;
            padding: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
            height: 50px;
        }

        .id-logo {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            border: 1px solid rgba(255,255,255,0.5);
        }

        .id-header-text { line-height: 1.1; }
        .id-header-text h3 { margin: 0; font-size: 10px; font-weight: 800; letter-spacing: 0.5px; }
        .id-header-text p { margin: 0; font-size: 7px; opacity: 0.9; }

        .id-body {
            padding: 12px;
            display: flex;
            gap: 12px;
        }

        .id-photo {
            width: 75px;
            height: 95px;
            background: #e2e8f0;
            border-radius: 6px;
            object-fit: cover;
            border: 2px solid white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .id-details { flex: 1; display: flex; flex-direction: column; gap: 6px; }
        
        .id-name { margin-bottom: 4px; }
        .id-name h2 { margin: 0; font-size: 14px; font-weight: 800; color: var(--c-navy); line-height: 1.1; }
        .id-name p { margin: 0; font-size: 8px; font-weight: 600; color: var(--c-teal); text-transform: uppercase; }

        .id-info-row { display: flex; flex-direction: column; line-height: 1.2; }
        .id-info-label { font-size: 6.5px; color: var(--c-gray); text-transform: uppercase; font-weight: 700; }
        .id-info-val { font-size: 9px; color: var(--c-navy); font-weight: 600; }

        .id-footer {
            position: absolute;
            bottom: 0; left: 0; right: 0;
            background: var(--c-navy);
            color: white;
            padding: 6px 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .id-number { font-family: var(--mono); font-size: 10px; font-weight: 500; }
        .id-type { font-size: 8px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: var(--c-teal); }

        /* Back Side Design */
        .id-back-header {
            background: var(--c-navy);
            color: white;
            padding: 8px;
            text-align: center;
            font-size: 8px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .id-back-body {
            padding: 12px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            height: calc(100% - 50px);
        }

        .id-emergency {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 8px;
        }
        .id-emergency .title { font-size: 7px; color: var(--c-gray); font-weight: 700; text-transform: uppercase; margin-bottom: 4px; border-bottom: 1px solid #e2e8f0; padding-bottom: 2px;}
        .id-emergency .val { font-size: 9px; font-weight: 600; color: var(--c-navy); }

        .id-qr-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            flex: 1;
        }

        .id-qr-code {
            width: 70px;
            height: 70px;
            background: white;
            padding: 4px;
            border-radius: 4px;
            border: 1px solid #e2e8f0;
        }

        .id-disclaimer {
            font-size: 6px;
            color: var(--c-gray);
            line-height: 1.3;
            flex: 1;
        }

        .id-signature {
            border-top: 1px solid #cbd5e1;
            text-align: center;
            padding-top: 4px;
            margin-top: 5px;
        }
        .id-signature-val { font-size: 7px; font-weight: 700; color: var(--c-navy); }
        .id-signature-title { font-size: 6px; color: var(--c-gray); }

        /* Print styles */
        @media print {
            body { background: white; padding: 0; margin: 0; align-items: flex-start; }
            .controls { display: none; }
            .id-container { flex-direction: column; gap: 0.5in; margin: 0; align-items: flex-start;}
            .id-card {
                transform: none; /* Return to actual physical size */
                margin-bottom: 0;
                box-shadow: none;
                border: 1px dashed #cbd5e1; /* Cut guide */
            }
        }
    </style>
</head>
<body>

    <div class="controls">
        <button onclick="window.print()" class="btn btn-primary"><i class="fas fa-print"></i> Print ID Card</button>
        <a href="<?= base_url('resident/view/' . $resident['id']) ?>" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back to Profile</a>
    </div>

    <div class="id-container">
        <!-- Front -->
        <div class="id-card">
            <div class="id-header">
                <img src="<?= base_url('assets/img/tabu.jpg') ?>" class="id-logo" alt="Logo">
                <div class="id-header-text">
                    <h3>BARANGAY TABU</h3>
                    <p>Ilog, Negros Occidental</p>
                </div>
            </div>
            
            <div class="id-body">
                <?php $pic = !empty($resident['profile_picture']) ? base_url('uploads/' . $resident['profile_picture']) : base_url('assets/img/avatar.png'); ?>
                <img src="<?= $pic ?>" class="id-photo" alt="Photo">
                
                <div class="id-details">
                    <div class="id-name">
                        <h2><?= esc(strtoupper($resident['last_name'])) ?>,</h2>
                        <h2><?= esc($resident['first_name'] . (!empty($resident['middle_name']) ? ' ' . substr($resident['middle_name'],0,1) . '.' : '')) ?></h2>
                        <p>Resident</p>
                    </div>
                    
                    <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 6px;">
                        <div class="id-info-row">
                            <span class="id-info-label">Date of Birth</span>
                            <span class="id-info-val"><?= date('M d, Y', strtotime($resident['birthdate'])) ?></span>
                        </div>
                        <div class="id-info-row">
                            <span class="id-info-label">Blood Type</span>
                            <span class="id-info-val">N/A</span>
                        </div>
                    </div>
                    <div class="id-info-row">
                        <span class="id-info-label">Address</span>
                        <span class="id-info-val"><?= esc($resident['sitio'] . (!empty($resident['household_no']) ? ' (' . $resident['household_no'] . ')' : '')) ?></span>
                    </div>
                </div>
            </div>

            <div class="id-footer">
                <div class="id-number">ID: <?= str_pad($resident['id'], 6, '0', STR_PAD_LEFT) ?></div>
                <div class="id-type">VALID ID</div>
            </div>
        </div>

        <!-- Back -->
        <div class="id-card back">
            <div class="id-back-header">In Case of Emergency</div>
            
            <div class="id-back-body">
                <div class="id-emergency">
                    <div class="title">Please Notify</div>
                    <div class="val">Head: <?= !empty($resident['household_no']) ? esc($resident['household_no']) : 'N/A' ?></div>
                    <div style="margin-top:2px;" class="val">Contact: <?= !empty($resident['contact_number']) ? esc($resident['contact_number']) : 'N/A' ?></div>
                </div>

                <div class="id-qr-container">
                    <div class="id-disclaimer">
                        This card is non-transferable and remains the property of Barangay Tabu. 
                        If found, please return to the Barangay Hall. 
                        <br><br>
                        Scan the QR code to verify the authenticity of this ID.
                    </div>
                    <div id="qrcode" class="id-qr-code"></div>
                </div>
                
                <div class="id-signature">
                    <div class="id-signature-val">HON. BARANGAY CAPTAIN</div>
                    <div class="id-signature-title">Punong Barangay</div>
                </div>
            </div>
        </div>
    </div>

    <!-- QR Code Generator Library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script>
        // Generate QR Code containing the verification link
        var verifyUrl = "<?= base_url('verify/' . $resident['id']) ?>";
        new QRCode(document.getElementById("qrcode"), {
            text: verifyUrl,
            width: 62,  // 70px container minus 8px padding
            height: 62,
            colorDark : "#0f172a",
            colorLight : "#ffffff",
            correctLevel : QRCode.CorrectLevel.M
        });
    </script>
</body>
</html>

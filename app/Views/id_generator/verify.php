<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?></title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --c-blue: #4F46E5;
            --c-green: #10B981;
            --c-rose: #F43F5E;
            --c-navy: #0f172a;
            --bg: #f8fafc;
            --font: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            font-family: var(--font);
            background: var(--bg);
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }

        .verify-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px -10px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
            overflow: hidden;
            text-align: center;
            border: 1px solid rgba(15, 23, 42, 0.05);
        }

        .header {
            padding: 30px 20px;
            color: white;
        }

        .header.success { background: linear-gradient(135deg, var(--c-green), #059669); }
        .header.error { background: linear-gradient(135deg, var(--c-rose), #e11d48); }

        .header-icon {
            font-size: 48px;
            margin-bottom: 15px;
            display: inline-block;
            background: rgba(255,255,255,0.2);
            width: 80px; height: 80px;
            line-height: 80px;
            border-radius: 50%;
        }

        .header h1 {
            margin: 0;
            font-size: 22px;
            font-weight: 800;
        }

        .body {
            padding: 30px;
        }

        .photo {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid white;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            margin-top: -50px;
            background: #e2e8f0;
        }

        .details {
            margin-top: 20px;
            text-align: left;
        }

        .detail-row {
            padding: 12px 0;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .detail-row:last-child { border: none; }

        .label {
            font-size: 11px;
            color: #64748b;
            text-transform: uppercase;
            font-weight: 700;
        }

        .val {
            font-size: 14px;
            color: var(--c-navy);
            font-weight: 700;
            text-align: right;
        }

        .msg {
            color: #475569;
            font-size: 15px;
            margin-top: 10px;
        }

        .footer {
            background: #f8fafc;
            padding: 15px;
            font-size: 12px;
            color: #94a3b8;
            border-top: 1px solid #f1f5f9;
        }

        .btn {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 24px;
            background: var(--c-navy);
            color: white;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 600;
            font-size: 14px;
            transition: opacity 0.2s;
        }
        .btn:hover { opacity: 0.9; }

    </style>
</head>
<body>

    <div class="verify-card">
        <?php if ($status === 'success'): ?>
            <div class="header success">
                <div class="header-icon"><i class="fas fa-check"></i></div>
                <h1>Verified Record</h1>
            </div>
            
            <div class="body" style="padding-top: 0;">
                <?php $pic = !empty($resident['profile_picture']) ? base_url('uploads/' . $resident['profile_picture']) : base_url('assets/img/avatar.png'); ?>
                <img src="<?= $pic ?>" class="photo" alt="Photo">
                
                <h2 style="margin: 15px 0 5px; color: var(--c-navy); font-size: 20px; font-weight: 800;">
                    <?= esc($resident['first_name'] . ' ' . $resident['last_name']) ?>
                </h2>
                <div style="font-size: 12px; font-weight: 600; color: var(--c-green); text-transform: uppercase; letter-spacing: 1px;">Active Resident</div>

                <div class="details">
                    <div class="detail-row">
                        <span class="label">ID Number</span>
                        <span class="val">#<?= str_pad($resident['id'], 6, '0', STR_PAD_LEFT) ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="label">Gender</span>
                        <span class="val" style="text-transform: capitalize;"><?= esc($resident['sex']) ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="label">Sitio / Address</span>
                        <span class="val"><?= esc($resident['sitio']) ?></span>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="header error">
                <div class="header-icon"><i class="fas fa-times"></i></div>
                <h1>Invalid ID</h1>
            </div>
            <div class="body">
                <div class="msg"><?= esc($message) ?></div>
                <a href="<?= base_url() ?>" class="btn">Return to Portal</a>
            </div>
        <?php endif; ?>

        <div class="footer">
            Official Verification System &bull; Barangay Tabu
        </div>
    </div>

</body>
</html>

<?php
$barangay     = $settings['barangay_name'] ?? 'Barangay';
$municipality = $settings['municipality']  ?? 'Municipality';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($barangay) ?> - Public Portal</title>
    <!-- Fonts loaded via bmis-design-system.css -->
    <!-- Icons -->
    <link rel="stylesheet" href="<?= base_url('assets/adminlte/plugins/fontawesome-free/css/all.min.css') ?>">
    <!-- Design System -->
    <link rel="stylesheet" href="<?= base_url('assets/css/bmis-design-system.css') ?>">
    <style>
        body { font-family: var(--font); background: var(--bg); margin: 0; color: var(--ink); }
        .portal-nav { display: flex; align-items: center; justify-content: space-between; padding: 20px 40px; background: var(--white); border-bottom: .5px solid var(--border); position: sticky; top: 0; z-index: 100; box-shadow: 0 4px 12px rgba(0,0,0,0.02); }
        .portal-nav .logo { display: flex; align-items: center; gap: 12px; font-weight: 700; font-size: 16px; color: var(--c-navy); text-decoration: none; }
        .portal-nav .logo img { height: 40px; width: 40px; border-radius: 50%; object-fit: cover; }
        .hero { text-align: center; padding: 80px 20px; background: radial-gradient(circle at center, var(--c-blue-bg) 0%, var(--bg) 100%); border-bottom: .5px solid var(--border); }
        .hero h1 { font-size: 36px; font-weight: 700; color: var(--c-navy); margin-bottom: 12px; }
        .hero p { font-size: 16px; color: var(--ink-muted); max-width: 600px; margin: 0 auto 30px; line-height: 1.6; }
        .services-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px; max-width: 1000px; margin: -40px auto 60px; padding: 0 20px; }
        .service-card { background: var(--white); border-radius: var(--r); padding: 30px 24px; text-align: center; border: .5px solid var(--border); box-shadow: var(--shadow); transition: transform 0.2s, box-shadow 0.2s; text-decoration: none; display: block; color: var(--ink); }
        .service-card:hover { transform: translateY(-5px); box-shadow: 0 12px 24px rgba(0,0,0,0.06); }
        .service-icon { width: 64px; height: 64px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px; margin: 0 auto 16px; }
        .portal-footer { text-align: center; padding: 40px 20px; color: var(--ink-soft); font-size: 12px; border-top: .5px solid var(--border); }
    </style>
</head>
<body>

    <nav class="portal-nav">
        <a href="<?= base_url() ?>" class="logo">
            <img src="<?= base_url('assets/img/tabu.jpg') ?>" alt="Logo">
            <span>Barangay <?= esc($barangay) ?></span>
        </a>
        <div>
            <a href="<?= base_url('login') ?>" class="ds-btn ds-btn-teal"><i class="fas fa-sign-in-alt"></i> Staff Login</a>
            <a href="<?= base_url('portal/login') ?>" class="ds-btn ds-btn-blue"><i class="fas fa-user"></i> Resident Login</a>
        </div>
    </nav>

    <header class="hero">
        <h1>Welcome to Barangay <?= esc($barangay) ?></h1>
        <p>Your unified digital public service portal for <?= esc($municipality) ?>. Request documents, report incidents, and stay updated with the latest community announcements from the comfort of your home.</p>
        <div style="display:flex;justify-content:center;gap:12px">
            <a href="#services" class="ds-btn ds-btn-blue" style="height:44px;padding:0 24px;font-size:14px">Explore Services</a>
            <a href="#" class="ds-btn ds-btn-ghost" style="height:44px;padding:0 24px;font-size:14px">View Announcements</a>
        </div>
    </header>

    <main id="services">
        <div class="services-grid">
            <a href="#" class="service-card" onclick="alert('Online requesting is coming in the next update!')">
                <div class="service-icon" style="background:var(--c-violet-bg);color:var(--c-violet)"><i class="fas fa-file-signature"></i></div>
                <h3 style="margin:0 0 8px;font-size:16px;font-weight:700">Request Certificates</h3>
                <p style="margin:0;font-size:13px;color:var(--ink-muted)">Request Barangay Clearance, Indigency, and other certifications online.</p>
            </a>
            
            <a href="#" class="service-card" onclick="alert('Incident reporting is coming in the next update!')">
                <div class="service-icon" style="background:var(--c-rose-bg);color:var(--c-rose)"><i class="fas fa-exclamation-triangle"></i></div>
                <h3 style="margin:0 0 8px;font-size:16px;font-weight:700">Report an Incident</h3>
                <p style="margin:0;font-size:13px;color:var(--ink-muted)">Submit an initial report for Blotter recording directly to the barangay desk.</p>
            </a>

        </div>
    </main>

    <footer class="portal-footer">
        &copy; <?= date('Y') ?> &mdash; Barangay <?= esc($barangay) ?>, <?= esc($municipality) ?>. All rights reserved.<br>
        <div style="margin-top:8px">Powered by BMIS</div>
    </footer>

</body>
</html>

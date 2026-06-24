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
    <!-- Icons -->
    <link rel="stylesheet" href="<?= base_url('assets/adminlte/plugins/fontawesome-free/css/all.min.css') ?>">
    <!-- Design System -->
    <link rel="stylesheet" href="<?= base_url('assets/css/bmis-design-system.css') ?>">
    <style>
        .portal-nav { display: flex; align-items: center; justify-content: space-between; padding: 20px 40px; background: var(--bg-elevated); border-bottom: 1px solid var(--border); position: sticky; top: 0; z-index: 100; backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); }
        .portal-nav .logo { display: flex; align-items: center; gap: 12px; font-weight: 800; font-size: 18px; color: var(--ink); text-decoration: none; }
        .portal-nav .logo img { height: 44px; width: 44px; border-radius: var(--r-md); object-fit: cover; border: 2px solid var(--c-teal-soft); }
        .hero { text-align: center; padding: 100px 20px; background: radial-gradient(circle at center, var(--c-blue-soft) 0%, var(--bg) 100%); border-bottom: 1px solid var(--border); }
        .hero h1 { font-size: 42px; font-weight: 800; color: var(--ink); margin-bottom: 16px; letter-spacing: -0.04em; }
        .hero p { font-size: 18px; color: var(--ink-muted); max-width: 600px; margin: 0 auto 32px; line-height: 1.6; }
        .portal-footer { text-align: center; padding: 40px 20px; color: var(--ink-soft); font-size: 13px; border-top: 1px solid var(--border); background: var(--bg-tertiary); margin-top: 80px; }
    </style>
</head>
<body>

    <nav class="portal-nav">
        <a href="<?= base_url() ?>" class="logo">
            <img src="<?= base_url('assets/img/tabu.jpg') ?>" alt="Logo">
            <span>Barangay <?= esc($barangay) ?></span>
        </a>
        <div style="display:flex;gap:12px;">
            <a href="<?= base_url('login') ?>" class="ds-btn ds-btn-secondary"><i class="fas fa-sign-in-alt"></i> Staff Login</a>
            <a href="<?= base_url('portal/login') ?>" class="ds-btn ds-btn-primary"><i class="fas fa-user"></i> Resident Login</a>
        </div>
    </nav>

    <header class="hero">
        <div class="bmis-content animate-slide-up">
            <h1>Welcome to Barangay <?= esc($barangay) ?></h1>
            <p>Your unified digital public service portal for <?= esc($municipality) ?>. Request documents, report incidents, and stay updated with the latest community announcements from the comfort of your home.</p>
            <div style="display:flex;justify-content:center;gap:16px;">
                <a href="#services" class="ds-btn ds-btn-primary ds-btn-lg"><i class="fas fa-compass"></i> Explore Services</a>
            </div>
        </div>
    </header>

    <main class="bmis-content" id="services" style="margin-top: -40px; position: relative; z-index: 10;">
        <div class="ds-grid-3 animate-fade-in" style="animation-delay: 0.2s;">
            <a href="<?= base_url('portal/certificates') ?>" class="ds-card" style="text-decoration:none; display:block; text-align:center;">
                <div class="ds-card-body">
                    <div style="width:64px;height:64px;border-radius:50%;background:var(--c-blue-bg);color:var(--c-blue);font-size:28px;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
                        <i class="fas fa-file-contract"></i>
                    </div>
                    <h3 class="ds-card-title" style="justify-content:center;margin-bottom:8px;">Certificates</h3>
                    <p style="color:var(--ink-muted);font-size:14px;margin:0;">Request clearances and certifications online.</p>
                </div>
            </a>
            <a href="<?= base_url('portal/blotter') ?>" class="ds-card" style="text-decoration:none; display:block; text-align:center;">
                <div class="ds-card-body">
                    <div style="width:64px;height:64px;border-radius:50%;background:var(--c-rose-bg);color:var(--c-rose);font-size:28px;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
                        <i class="fas fa-gavel"></i>
                    </div>
                    <h3 class="ds-card-title" style="justify-content:center;margin-bottom:8px;">Report Incident</h3>
                    <p style="color:var(--ink-muted);font-size:14px;margin:0;">File a blotter or report an issue securely.</p>
                </div>
            </a>
            <div class="ds-card" style="text-decoration:none; display:block; text-align:center;">
                <div class="ds-card-body">
                    <div style="width:64px;height:64px;border-radius:50%;background:var(--c-teal-bg);color:var(--c-teal);font-size:28px;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3 class="ds-card-title" style="justify-content:center;margin-bottom:8px;">Community</h3>
                    <div style="display:flex;justify-content:center;gap:12px;margin-top:12px;">
                        <div style="text-align:center">
                            <div style="font-weight:800;font-size:18px;color:var(--ink)"><?= number_format($stats['residents']) ?></div>
                            <div style="font-size:11px;color:var(--ink-muted);text-transform:uppercase">Residents</div>
                        </div>
                        <div style="width:1px;background:var(--border);"></div>
                        <div style="text-align:center">
                            <div style="font-weight:800;font-size:18px;color:var(--ink)"><?= number_format($stats['accounts']) ?></div>
                            <div style="font-size:11px;color:var(--ink-muted);text-transform:uppercase">Online</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div style="margin-top: 60px; display: grid; grid-template-columns: 2fr 1fr; gap: 32px;" class="animate-fade-in" style="animation-delay: 0.4s;">
            
            <!-- Announcements Column -->
            <div>
                <h2 style="font-size: 24px; font-weight: 800; color: var(--ink); margin-bottom: 24px;">
                    <i class="fas fa-bullhorn" style="color: #f59e0b; margin-right: 8px;"></i> Latest Announcements
                </h2>
                <?php if (empty($announcements)): ?>
                    <div class="ds-card"><div class="ds-card-body" style="text-align:center;color:var(--ink-muted);padding:40px;">No recent announcements.</div></div>
                <?php else: ?>
                    <div style="display:flex;flex-direction:column;gap:16px;">
                        <?php foreach ($announcements as $ann): ?>
                            <div class="ds-card">
                                <div class="ds-card-body">
                                    <div style="display:flex;align-items:flex-start;gap:16px;">
                                        <?php if (!empty($ann['image_url'])): ?>
                                            <div style="width:80px;height:80px;border-radius:8px;overflow:hidden;flex-shrink:0;">
                                                <img src="<?= base_url(esc($ann['image_url'])) ?>" alt="Image" style="width:100%;height:100%;object-fit:cover;">
                                            </div>
                                        <?php endif; ?>
                                        <div style="flex:1;">
                                            <h4 style="margin:0;font-size:16px;font-weight:700;color:var(--ink);"><?= esc($ann['title']) ?></h4>
                                            <div style="font-size:12px;color:var(--ink-muted);margin:4px 0 8px;">
                                                <?= date('F d, Y \a\t h:i A', strtotime($ann['created_at'])) ?>
                                                <?php if ($ann['is_pinned']): ?>
                                                    <span class="ds-badge ds-badge-amber" style="margin-left:8px;font-size:10px;"><i class="fas fa-thumbtack"></i> Pinned</span>
                                                <?php endif; ?>
                                            </div>
                                            <p style="margin:0;font-size:13px;color:var(--ink-soft);line-height:1.5;display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden;"><?= esc($ann['body']) ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Events Column -->
            <div>
                <h2 style="font-size: 24px; font-weight: 800; color: var(--ink); margin-bottom: 24px;">
                    <i class="fas fa-calendar-alt" style="color: #4f46e5; margin-right: 8px;"></i> Upcoming Events
                </h2>
                <?php if (empty($upcomingEvents)): ?>
                    <div class="ds-card"><div class="ds-card-body" style="text-align:center;color:var(--ink-muted);padding:40px;">No upcoming events.</div></div>
                <?php else: ?>
                    <div style="display:flex;flex-direction:column;gap:16px;">
                        <?php foreach ($upcomingEvents as $evt): ?>
                            <div class="ds-card">
                                <div class="ds-card-body" style="padding:16px;">
                                    <div style="font-size:11px;font-weight:700;color:#4f46e5;text-transform:uppercase;margin-bottom:4px;"><?= date('M d, Y', strtotime($evt['event_date'])) ?></div>
                                    <h4 style="margin:0;font-size:15px;font-weight:700;color:var(--ink);"><?= esc($evt['title']) ?></h4>
                                    <div style="font-size:12px;color:var(--ink-muted);margin-top:4px;"><i class="fas fa-map-marker-alt" style="margin-right:4px;"></i> <?= esc($evt['location'] ?? 'TBA') ?></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            
        </div>
    </main>

    <footer class="portal-footer">
        &copy; <?= date('Y') ?> &mdash; Barangay <?= esc($barangay) ?>, <?= esc($municipality) ?>. All rights reserved.<br>
        <div style="margin-top:8px;font-weight:600;">Powered by BMIS Design System</div>
    </footer>

</body>
</html>

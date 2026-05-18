<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resident Portal | Barangay Information System</title>
    <!-- Core CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Use local FontAwesome to prevent loading issues -->
    <link rel="stylesheet" href="<?= base_url('assets/adminlte/plugins/fontawesome-free/css/all.min.css') ?>">
    <!-- Theme CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/css/bmis-design-system.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/advanced-features.css') ?>">
    <style>
        body {
            background-color: var(--bg);
            background-image: radial-gradient(at 0% 0%, rgba(79, 70, 229, 0.08) 0px, transparent 50%),
                              radial-gradient(at 100% 100%, rgba(16, 185, 129, 0.08) 0px, transparent 50%);
            background-attachment: fixed;
            font-family: var(--font);
            margin: 0;
            padding: 0;
        }

        /* ── Navbar ────────────────────────────────── */
        .portal-navbar {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(15, 23, 42, 0.05);
            padding: 12px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        .portal-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: var(--ink);
            flex-shrink: 0;
        }
        .portal-brand img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
        }
        .portal-brand-text h1 {
            font-size: 16px;
            font-weight: 800;
            margin: 0;
            line-height: 1.2;
        }
        .portal-brand-text span {
            font-size: 12px;
            color: var(--ink-muted);
        }

        /* ── Nav Links ─────────────────────────────── */
        .portal-nav {
            display: flex;
            align-items: center;
            gap: 4px;
            list-style: none;
            margin: 0;
            padding: 0;
        }
        .portal-nav a {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 8px 14px;
            border-radius: 10px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            color: var(--ink-muted);
            transition: all 0.2s ease;
            white-space: nowrap;
        }
        .portal-nav a:hover {
            background: rgba(79, 70, 229, 0.08);
            color: var(--ink);
        }
        .portal-nav a.active {
            background: rgba(79, 70, 229, 0.12);
            color: #4f46e5;
        }
        .portal-nav a i {
            font-size: 14px;
            width: 16px;
            text-align: center;
        }

        /* ── Right Actions ─────────────────────────── */
        .portal-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-shrink: 0;
        }
        .portal-logout {
            color: var(--c-rose);
            text-decoration: none;
            font-weight: 700;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 8px 14px;
            border-radius: 12px;
            background: rgba(244, 63, 94, 0.1);
            transition: all 0.2s ease;
        }
        .portal-logout:hover {
            background: rgba(244, 63, 94, 0.2);
            color: #e11d48;
        }

        /* ── Mobile Hamburger ──────────────────────── */
        .portal-hamburger {
            display: none;
            background: none;
            border: none;
            font-size: 22px;
            color: var(--ink);
            cursor: pointer;
            padding: 6px;
            border-radius: 8px;
            transition: background 0.2s;
        }
        .portal-hamburger:hover {
            background: rgba(15, 23, 42, 0.06);
        }

        .portal-main {
            padding: 40px 24px;
            max-width: 1200px;
            margin: 0 auto;
        }

        /* ── Responsive ────────────────────────────── */
        @media (max-width: 900px) {
            .portal-hamburger {
                display: block;
            }
            .portal-nav {
                display: none;
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                flex-direction: column;
                background: rgba(255, 255, 255, 0.97);
                backdrop-filter: blur(16px);
                -webkit-backdrop-filter: blur(16px);
                padding: 12px 16px;
                border-bottom: 1px solid rgba(15, 23, 42, 0.08);
                box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
                gap: 2px;
            }
            .portal-nav.open {
                display: flex;
            }
            .portal-nav a {
                padding: 12px 16px;
                border-radius: 10px;
                font-size: 14px;
            }
            .portal-brand-text {
                display: none;
            }
        }
        @media (max-width: 480px) {
            .portal-navbar {
                padding: 10px 16px;
            }
            .portal-main {
                padding: 24px 16px;
            }
            .portal-logout span {
                display: none;
            }
        }
    </style>
</head>
<body>

    <?php
        // Determine active nav item from the current URL path
        $currentPath = uri_string();
    ?>

    <nav class="portal-navbar">
        <a href="<?= base_url('portal/home') ?>" class="portal-brand">
            <div style="width: 40px; height: 40px; border-radius: 50%; background: var(--c-blue-bg); color: var(--c-blue); display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fas fa-city"></i>
            </div>
            <div class="portal-brand-text">
                <h1>Barangay Portal</h1>
                <span>Resident Services</span>
            </div>
        </a>

        <button class="portal-hamburger" id="portalMenuToggle" aria-label="Toggle navigation">
            <i class="fas fa-bars"></i>
        </button>

        <ul class="portal-nav" id="portalNav">
            <li><a href="<?= base_url('portal/home') ?>" class="<?= ($currentPath === 'portal/home') ? 'active' : '' ?>">
                <i class="fas fa-home"></i> Home
            </a></li>
            <li><a href="<?= base_url('portal/file-blotter') ?>" class="<?= (strpos($currentPath, 'portal/file-blotter') !== false || strpos($currentPath, 'portal/blotter') !== false) ? 'active' : '' ?>">
                <i class="fas fa-balance-scale"></i> File Incident
            </a></li>
            <li><a href="<?= base_url('portal/my-cases') ?>" class="<?= (strpos($currentPath, 'portal/my-cases') !== false) ? 'active' : '' ?>">
                <i class="fas fa-folder-open"></i> My Cases
            </a></li>
            <li><a href="<?= base_url('portal/facilities') ?>" class="<?= (strpos($currentPath, 'portal/facilities') !== false) ? 'active' : '' ?>">
                <i class="fas fa-building"></i> Facilities
            </a></li>
            <li><a href="<?= base_url('portal/my-id') ?>" class="<?= (strpos($currentPath, 'portal/my-id') !== false) ? 'active' : '' ?>">
                <i class="fas fa-id-card"></i> My ID
            </a></li>
            <li><a href="<?= base_url('advanced/events') ?>" class="<?= (strpos($currentPath, 'advanced/events') !== false) ? 'active' : '' ?>">
                <i class="fas fa-calendar-alt"></i> Events
            </a></li>
            <li><a href="<?= base_url('portal/profile') ?>" class="<?= (strpos($currentPath, 'portal/profile') !== false) ? 'active' : '' ?>">
                <i class="fas fa-user-cog"></i> Profile
            </a></li>
            <li><a href="<?= base_url('advanced/report-emergency') ?>" class="<?= (strpos($currentPath, 'report-emergency') !== false) ? 'active' : '' ?>" style="color: #ef4444;">
                <i class="fas fa-ambulance"></i> Emergency
            </a></li>
        </ul>

        <div class="portal-actions">
            <a href="<?= base_url('portal/logout') ?>" class="portal-logout">
                <i class="fas fa-sign-out-alt"></i> <span>Sign Out</span>
            </a>
        </div>
    </nav>

    <main class="portal-main">
        <?= $this->renderSection('content') ?>
    </main>

    <script>
        // Mobile menu toggle
        document.getElementById('portalMenuToggle').addEventListener('click', function() {
            const nav = document.getElementById('portalNav');
            nav.classList.toggle('open');
            const icon = this.querySelector('i');
            icon.className = nav.classList.contains('open') ? 'fas fa-times' : 'fas fa-bars';
        });
        // Close menu when clicking a link (mobile)
        document.querySelectorAll('#portalNav a').forEach(link => {
            link.addEventListener('click', () => {
                document.getElementById('portalNav').classList.remove('open');
                document.querySelector('#portalMenuToggle i').className = 'fas fa-bars';
            });
        });
    </script>

</body>
</html>

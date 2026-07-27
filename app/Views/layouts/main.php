<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'ASOG TBI Dataset Repository') ?></title>
    <link rel="icon" type="image/x-icon" href="<?= base_url('favicon/favicon.ico') ?>">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= base_url('favicon/favicon-32x32.png') ?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= base_url('favicon/favicon-16x16.png') ?>">
    <link rel="icon" type="image/png" sizes="192x192" href="<?= base_url('favicon/android-chrome-192x192.png') ?>">
    <link rel="icon" type="image/png" sizes="512x512" href="<?= base_url('favicon/android-chrome-512x512.png') ?>">
    <link rel="apple-touch-icon" sizes="180x180" href="<?= base_url('favicon/apple-touch-icon.png') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,700;0,9..40,800;0,9..40,900;1,9..40,400&family=DM+Serif+Display:ital@0;1&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/app/detail-responsive.css') ?>">
    <meta name="theme-color" content="#03558b">
    <script>
        (function () {
            try {
                var stored = localStorage.getItem('asog-theme');
                if (stored === 'dark' || stored === 'light') {
                    document.documentElement.setAttribute('data-theme', stored);
                    document.documentElement.classList.toggle('dark', stored === 'dark');
                } else if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                    document.documentElement.setAttribute('data-theme', 'dark');
                    document.documentElement.classList.add('dark');
                }
            } catch (e) {}
        })();
    </script>
</head>
<body>
<?php $isAuthenticated = (bool) session()->get('user_id'); ?>
<?php $roles = (array) session()->get('roles'); ?>
<?php $flashInfo = session()->getFlashdata('info'); ?>
<?php $flashError = session()->getFlashdata('error'); ?>
<?php
    $headerNotifications = [];
    $unreadNotificationCount = 0;
    $latestNotificationId = 0;
    if ($isAuthenticated) {
        $notificationModel = model(\App\Models\NotificationModel::class);
        $headerNotifications = $notificationModel->where('user_id', (int) session()->get('user_id'))->orderBy('created_at', 'DESC')->findAll(6);
        $unreadNotificationCount = $notificationModel->where('user_id', (int) session()->get('user_id'))->where('read_at', null)->countAllResults();
        $latestUnreadNotification = $notificationModel->where('user_id', (int) session()->get('user_id'))->where('read_at', null)->orderBy('id', 'DESC')->first();
        $latestNotificationId = is_array($latestUnreadNotification) ? (int) $latestUnreadNotification['id'] : 0;
    }
    $currentPath = trim(uri_string(), '/');
    $isHome = $currentPath === '';
    $isBrowse = $currentPath === 'datasets' || str_starts_with($currentPath, 'datasets/');
    $isBrowseCatalog = $currentPath === 'datasets';
    $isAboutPlatform = $currentPath === 'about/platform';
    $isAboutPartners = $currentPath === 'about/partners';
    $isAbout = $isAboutPlatform || $isAboutPartners;
?>
<?= view('components/logout_modal') ?>

<button type="button" class="theme-toggle" id="theme-toggle" aria-label="Switch to dark mode" title="Switch to dark mode">
    <span class="material-symbols-rounded" aria-hidden="true" id="theme-toggle-icon">dark_mode</span>
</button>

<header class="site-header<?= $isHome ? ' site-header--home' : '' ?><?= $isBrowseCatalog ? ' site-header--browse' : '' ?><?= $isAbout ? ' site-header--about' : '' ?>" id="site-header">
    <div class="wide-shell header-inner">
        <div class="nav-left">
            <a class="nav-link<?= $isHome ? ' is-active' : '' ?>" href="<?= site_url('/') ?>">Home</a>
            <a class="nav-link<?= $isBrowse ? ' is-active' : '' ?>" href="<?= site_url('datasets') ?>">Browse</a>

            <details class="nav-about">
                <summary class="nav-link<?= ($isAboutPlatform || $isAboutPartners) ? ' is-active' : '' ?>">About Us</summary>
                <div class="nav-about-dropdown">
                    <a class="nav-about-item<?= $isAboutPlatform ? ' is-active' : '' ?>" href="<?= site_url('about/platform') ?>">
                        <strong>About the Platform</strong>
                        <span>Mission, features &amp; workflow</span>
                    </a>
                    <a class="nav-about-item<?= $isAboutPartners ? ' is-active' : '' ?>" href="<?= site_url('about/partners') ?>">
                        <strong>Partners &amp; Team</strong>
                        <span>Partners, developers &amp; origins</span>
                    </a>
                </div>
            </details>
        </div>

        <a class="brand<?= $isHome ? ' brand--hero-sync' : '' ?>" href="<?= site_url('/') ?>" aria-label="ASOG TBI Dataset Repository home">
            <img class="brand-logo" src="<?= base_url('assets/img/asog-data-repo-logo.png') ?>" alt="ASOG TBI Dataset Repository logo">
        </a>

        <div class="nav-right">
            <div class="nav-right-links">
                    <?php if ($isAuthenticated): ?>
                    <a class="nav-link<?= $currentPath === 'dashboard' ? ' is-active' : '' ?>" href="<?= site_url('dashboard') ?>">My Datasets</a>
                    <?= view('components/notification_menu', [
                        'headerNotifications' => $headerNotifications,
                        'unreadNotificationCount' => $unreadNotificationCount,
                        'latestNotificationId' => $latestNotificationId,
                        'notificationLabel' => 'Notifications',
                        'notificationMenuClass' => 'notification-menu--public',
                    ]) ?>
                    <details class="account-menu">
                        <summary class="account-trigger" aria-label="Open account menu">
                            <span class="material-symbols-rounded" aria-hidden="true">account_circle</span>
                        </summary>
                        <div class="account-popover">
                            <p class="account-name"><?= esc((string) session()->get('user_name')) ?></p>
                            <a href="<?= site_url('account/settings') ?>"><span class="material-symbols-rounded" aria-hidden="true">manage_accounts</span> Profile settings</a>
                            <a href="<?= site_url('dashboard') ?>"><span class="material-symbols-rounded" aria-hidden="true">database</span> My datasets</a>
                            <?php if (in_array('ethics_reviewer', $roles, true) || in_array('technical_reviewer', $roles, true) || in_array('repository_administrator', $roles, true)): ?>
                                <a href="<?= site_url('portal/dashboard') ?>"><span class="material-symbols-rounded" aria-hidden="true">folder_managed</span> Portal records</a>
                            <?php endif; ?>
                            <?php if (in_array('ethics_reviewer', $roles, true)): ?><a href="<?= site_url('review/ethics') ?>"><span class="material-symbols-rounded" aria-hidden="true">verified_user</span> Ethics reviews</a><?php endif; ?>
                            <?php if (in_array('technical_reviewer', $roles, true)): ?><a href="<?= site_url('review/technical') ?>"><span class="material-symbols-rounded" aria-hidden="true">sdk</span> Technical reviews</a><?php endif; ?>
                            <?php if (in_array('repository_administrator', $roles, true)): ?><a href="<?= site_url('admin') ?>"><span class="material-symbols-rounded" aria-hidden="true">dashboard</span> Admin dashboard</a><?php endif; ?>
                            <form class="nav-form" method="post" action="<?= site_url('logout') ?>">
                                <?= csrf_field() ?>
                                <button type="submit"><span class="material-symbols-rounded" aria-hidden="true">logout</span> Logout</button>
                            </form>
                        </div>
                    </details>
                <?php else: ?>
                    <a class="nav-cta" href="<?= site_url('login') ?>">Login</a>
                <?php endif; ?>
            </div>

            <button class="menu-toggle" type="button" aria-label="Toggle navigation" aria-controls="mobile-nav" aria-expanded="false">
                <span class="material-symbols-rounded" aria-hidden="true">menu</span>
            </button>
        </div>
    </div>

    <nav class="wide-shell mobile-nav" id="mobile-nav" aria-label="Mobile navigation">
        <a class="<?= $isHome ? 'is-active' : '' ?>" href="<?= site_url('/') ?>">Home</a>
        <a class="<?= $isBrowse ? 'is-active' : '' ?>" href="<?= site_url('datasets') ?>">Browse</a>
        <div style="border-bottom: 1px solid var(--nav-border);">
            <div class="mobile-nav-group-label">About Us</div>
            <a class="mobile-nav-sublink<?= $isAboutPlatform ? ' is-active' : '' ?>" href="<?= site_url('about/platform') ?>">About the Platform</a>
            <a class="mobile-nav-sublink<?= $isAboutPartners ? ' is-active' : '' ?>" href="<?= site_url('about/partners') ?>">Partners &amp; Team</a>
        </div>
        <?php if ($isAuthenticated): ?>
            <a href="<?= site_url('upload') ?>">Upload</a>
            <div class="mobile-account-panel">
                <p><?= esc((string) session()->get('user_name')) ?></p>
                <?= view('components/notification_menu', [
                    'headerNotifications' => $headerNotifications,
                    'unreadNotificationCount' => $unreadNotificationCount,
                    'latestNotificationId' => $latestNotificationId,
                    'notificationLabel' => 'Notifications',
                    'notificationMenuClass' => 'notification-menu--mobile',
                ]) ?>
                <a href="<?= site_url('account/settings') ?>"><span class="material-symbols-rounded" aria-hidden="true">manage_accounts</span> Profile settings</a>
                <a href="<?= site_url('dashboard') ?>"><span class="material-symbols-rounded" aria-hidden="true">database</span> My datasets</a>
                <?php if (in_array('ethics_reviewer', $roles, true) || in_array('technical_reviewer', $roles, true) || in_array('repository_administrator', $roles, true)): ?>
                    <a href="<?= site_url('portal/dashboard') ?>"><span class="material-symbols-rounded" aria-hidden="true">folder_managed</span> Portal records</a>
                <?php endif; ?>
                <?php if (in_array('ethics_reviewer', $roles, true)): ?><a href="<?= site_url('review/ethics') ?>"><span class="material-symbols-rounded" aria-hidden="true">verified_user</span> Ethics reviews</a><?php endif; ?>
                <?php if (in_array('technical_reviewer', $roles, true)): ?><a href="<?= site_url('review/technical') ?>"><span class="material-symbols-rounded" aria-hidden="true">sdk</span> Technical reviews</a><?php endif; ?>
                <?php if (in_array('repository_administrator', $roles, true)): ?><a href="<?= site_url('admin') ?>"><span class="material-symbols-rounded" aria-hidden="true">dashboard</span> Admin dashboard</a><?php endif; ?>
            </div>
            <form class="nav-form" method="post" action="<?= site_url('logout') ?>">
                <?= csrf_field() ?>
                <button class="nav-cta" type="submit"><span class="material-symbols-rounded" aria-hidden="true">logout</span> Logout</button>
            </form>
        <?php else: ?>
            <a href="<?= site_url('register') ?>">Request access</a>
            <a class="nav-cta" href="<?= site_url('login') ?>">Login</a>
        <?php endif; ?>
    </nav>
</header>

<main class="page-shell">
    <?php if ($isAuthenticated): ?>
        <div class="portal-live-toast" data-live-toast hidden>
            <strong data-live-toast-title>New activity</strong>
            <span data-live-toast-message>Open notifications for details.</span>
        </div>
    <?php endif; ?>
    <div class="flash-stack" aria-live="polite" aria-atomic="true">
        <?php if ($flashInfo): ?>
            <div class="flash-toast flash-toast--success" role="status" data-flash-toast>
                <span class="material-symbols-rounded" aria-hidden="true">check_circle</span>
                <div>
                    <strong>Changes saved</strong>
                    <p><?= esc($flashInfo) ?></p>
                </div>
                <button type="button" data-flash-dismiss aria-label="Dismiss notification"><span class="material-symbols-rounded" aria-hidden="true">close</span></button>
            </div>
        <?php endif; ?>
        <?php if ($flashError): ?>
            <div class="flash-toast flash-toast--error" role="alert" data-flash-toast>
                <span class="material-symbols-rounded" aria-hidden="true">error</span>
                <div>
                    <strong>Needs attention</strong>
                    <p><?= esc($flashError) ?></p>
                </div>
                <button type="button" data-flash-dismiss aria-label="Dismiss notification"><span class="material-symbols-rounded" aria-hidden="true">close</span></button>
            </div>
        <?php endif; ?>
    </div>
    <?= $this->renderSection('content') ?>
</main>

<footer class="site-footer">
    <div class="wide-shell">
        <div class="ft-grid">
            <section>
                <div class="ft-brand-row">
                    <img class="ft-logo-icon" src="<?= base_url('assets/img/asog-data-repo-logo.png') ?>" alt="ASOG TBI">
                    <div>
                        <p class="ft-brand-name">Dataset Repository</p>
                        <p class="ft-brand-sub">ASOG TBI &middot; CSPC</p>
                    </div>
                </div>
                <p class="ft-tagline">A centralized repository for institutional research datasets from the College of Computer Studies at CSPC, in partnership with ASOG Technology Business Incubator.</p>
            </section>

            <section>
                <h3 class="ft-heading">Repository</h3>
                <ul class="ft-links">
                    <li><a href="<?= site_url('datasets') ?>">Browse Datasets</a></li>
                    <li><a href="<?= site_url('upload') ?>">Submit Dataset</a></li>
                    <li><a href="<?= site_url('dashboard') ?>">My Datasets</a></li>
                    <li><a href="<?= site_url('login') ?>">Contributor Sign In</a></li>
                </ul>
            </section>

            <section>
                <h3 class="ft-heading">About Us</h3>
                <ul class="ft-links">
                    <li><a href="<?= site_url('about/platform') ?>">About the Platform</a></li>
                    <li><a href="<?= site_url('about/partners') ?>">Partners &amp; Team</a></li>
                    <li><a href="<?= site_url('register') ?>">Request Credentials</a></li>
                </ul>
            </section>

            <section>
                <h3 class="ft-heading">Contact</h3>
                <ul class="ft-contact-list">
                    <li>Camarines Sur Polytechnic Colleges, Nabua, Camarines Sur</li>
                    <li><a href="mailto:repository@cspc.edu.ph">repository@cspc.edu.ph</a></li>
                    <li><a href="https://asogtbi.com/" target="_blank" rel="noopener noreferrer">ASOG Technology Business Incubator</a></li>
                </ul>
            </section>
        </div>

        <div class="ft-bottom">
            <div class="ft-bottom-left">
                <img class="ft-ccs-logo" src="<?= base_url('assets/img/powered by ccs/[for navy blue] powered_by_ccs.webp') ?>" alt="Powered by CCS">
                <span class="ft-copyright">&copy; <?= date('Y') ?> ASOG Technology Business Incubator &middot; CSPC</span>
            </div>
            <div class="ft-bottom-right">
                <a href="#">Privacy Policy</a>
                <a href="#">Terms of Use</a>
            </div>
        </div>
    </div>
</footer>

<script src="<?= base_url('assets/js/theme-toggle.js') ?>"></script>
<script>
    const header = document.getElementById('site-header');
    const toggle = document.querySelector('.menu-toggle');
    const heroLogo = document.querySelector('.home-hero-logo');

    // Scroll threshold: 20px (matches React prototype)
    const SCROLL_THRESHOLD = 20;
    window.syncHeader = () => header?.classList.toggle('site-header--scrolled', window.scrollY > SCROLL_THRESHOLD);

    window.syncHeader();
    window.addEventListener('scroll', window.syncHeader, { passive: true });

    // Hero logo visibility for brand fade effect (continuous observation)
    if (heroLogo && 'IntersectionObserver' in window) {
        new IntersectionObserver(
            ([entry]) => header?.classList.toggle('site-header--hero-logo-visible', entry.isIntersecting),
            { threshold: 0.1, rootMargin: '-64px 0px 0px 0px' }
        ).observe(heroLogo);
    }

    // Mobile menu toggle
    toggle?.addEventListener('click', () => {
        const isOpen = header.classList.toggle('is-menu-open');
        toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        const icon = toggle.querySelector('.material-symbols-rounded');
        if (icon) icon.textContent = isOpen ? 'close' : 'menu';
    });

    // Close mobile menu on navigation
    header?.querySelectorAll('.mobile-nav a').forEach(link => {
        link.addEventListener('click', () => {
            header.classList.remove('is-menu-open');
            toggle?.setAttribute('aria-expanded', 'false');
            const icon = toggle?.querySelector('.material-symbols-rounded');
            if (icon) icon.textContent = 'menu';
        });
    });

    // About Us dropdown: close on outside click (enhances <details> behavior)
    const aboutDetails = document.querySelector('.nav-about');
    if (aboutDetails) {
        document.addEventListener('click', (e) => {
            if (!aboutDetails.contains(e.target)) {
                aboutDetails.removeAttribute('open');
            }
        });
    }

    // Flash toast auto-dismiss
    document.querySelectorAll('[data-flash-toast]').forEach((toast) => {
        const close = () => {
            toast.classList.add('is-leaving');
            window.setTimeout(() => toast.remove(), 220);
        };
        toast.querySelector('[data-flash-dismiss]')?.addEventListener('click', close);
        window.setTimeout(close, 5000);
    });
</script>
<?= view('components/select_enhancer_script') ?>
<?php if ($isAuthenticated): ?><?= view('components/notification_script') ?><?php endif; ?>
</body>
</html>

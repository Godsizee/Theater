<?php
$settings = \App\Core\Utils::getSettings();
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? htmlspecialchars($settings['site_title']); ?></title>
    
    <script>
        (function() {
            try {
                const theme = localStorage.getItem('theme');
                if (theme === 'dark') {
                    document.documentElement.classList.add('dark-mode');
                }
            } catch (e) { /* ignore */ }
        })();
    </script>
    
    <script>
        window.APP_CONFIG = {
            baseUrl: '<?php echo $config['base_url']; ?>',
            userRole: '<?php echo $_SESSION['user_role'] ?? ''; ?>',
            settings: <?php echo json_encode($settings); ?>
        };
    </script>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>

    <?php 
    \App\Core\Utils::viteAssets($config); 
    ?>
</head>
<body class="<?php echo $body_class ?? ''; ?>">
    <div class="background-shape shape-1"></div>
    <div class="background-shape shape-2"></div>
    <div class="background-shape shape-3"></div>

    <header class="page-header">
        <a href="<?php echo $config['base_url']; ?>/" class="site-logo" data-spa-link>
            <img src="<?php echo $config['base_url']; ?>/public/assets/images/Banner.png" alt="<?php echo htmlspecialchars($settings['site_title']); ?>">
        </a>

        <nav class="header-nav" id="header-nav">
            <div class="nav-left">
                <a href="<?php echo $config['base_url']; ?>/select" class="header-link" data-spa-link>Filme</a>
                <a href="<?php echo $config['base_url']; ?>/series" class="header-link" data-spa-link>Serien</a>
                <a href="<?php echo $config['base_url']; ?>/merchandise" class="header-link" data-spa-link>Merch</a>
            </div>
            <div class="nav-right">
                <button id="theme-toggle" class="theme-toggle" title="Theme umschalten">
                    <img class="sun-icon" src="<?php echo $config['base_url']; ?>/public/assets/images/sun.png" alt="Light Mode">
                    <img class="moon-icon" src="<?php echo $config['base_url']; ?>/public/assets/images/moon.png" alt="Dark Mode">
                </button>
                <span class="nav-separator"></span>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="<?php echo $config['base_url']; ?>/cart" class="cart-widget" aria-label="Warenkorb" data-spa-link>
                        <svg viewBox="0 0 24 24"><path d="M17,18A2,2 0 0,1 19,20A2,2 0 0,1 17,22A2,2 0 0,1 15,20A2,2 0 0,1 17,18M1,2H4.27L5.21,4H20A1,1 0 0,1 21,5C21,5.17 20.95,5.34 20.88,5.5L17.3,11.97C16.96,12.58 16.3,13 15.55,13H8.1L7.2,14.63L7.17,14.75A0.25,0.25 0 0,0 7.42,15H19V17H7C5.89,17 5,16.1 5,15C5,14.65 5.09,14.32 5.24,14.04L6.6,11.59L3,4H1V2M7,18A2,2 0 0,1 9,20A2,2 0 0,1 7,22A2,2 0 0,1 5,20A2,2 0 0,1 7,18M16,11L18.78,6H6.14L8.5,11H16Z" /></svg>
                        <span id="cart-item-count" class="cart-item-count">0</span>
                    </a>
                    <div class="user-menu">
                        <button class="user-menu-toggle" aria-haspopup="true" aria-expanded="false">
                            <span>Hallo, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                            <svg class="chevron-down" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                        </button>
                        <div class="user-menu-dropdown">
                            <a href="<?php echo $config['base_url']; ?>/profil" data-spa-link>Mein Profil</a>
                            <?php if (in_array($_SESSION['user_role'], ['admin', 'co-admin', 'kundendienst'])): ?>
                                <a href="<?php echo $config['base_url']; ?>/support">Kundendienst</a>
                            <?php endif; ?>
                            <?php if (in_array($_SESSION['user_role'], ['admin', 'co-admin'])): ?>
                                <a href="<?php echo $config['base_url']; ?>/admin">Admin Dashboard</a>
                            <?php endif; ?>
                            <div class="dropdown-divider"></div>
                            <a href="<?php echo $config['base_url']; ?>/login?logout=1">Abmelden</a>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="<?php echo $config['base_url']; ?>/login" class="header-link" data-spa-link>Anmelden</a>
                    <?php if ($settings['allow_registrations']): ?>
                        <a href="<?php echo $config['base_url']; ?>/registry" class="btn btn-primary btn-small header-cta" data-spa-link>Registrieren</a>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </nav>
        <button class="mobile-menu-toggle" id="mobile-menu-toggle" aria-controls="header-nav" aria-expanded="false">
            <span class="visually-hidden">Menü</span>
            <span class="hamburger-box"><span class="hamburger-inner"></span></span>
        </button>
    </header>

    <main class="page-wrapper">
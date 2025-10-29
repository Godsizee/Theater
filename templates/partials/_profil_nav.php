<!-- templates/partials/_profil_nav.php -->
<?php
// KORREKTUR: Diese Logik zur Bestimmung der aktiven Seite ist für einen Router besser geeignet.
$request_path_profil = trim($_GET['url'] ?? '', '/');
?>
<nav class="profile-nav">
    <a href="<?php echo $config['base_url']; ?>/profil" class="profile-nav-item <?php echo ($request_path_profil === 'profil' ? 'active' : ''); ?>" data-spa-link>Mein Konto</a>
    <a href="<?php echo $config['base_url']; ?>/bestellungen" class="profile-nav-item <?php echo ($request_path_profil === 'bestellungen' ? 'active' : ''); ?>" data-spa-link>Bestellungen</a>
    <a href="<?php echo $config['base_url']; ?>/rechnungen" class="profile-nav-item <?php echo ($request_path_profil === 'rechnungen' ? 'active' : ''); ?>" data-spa-link>Rechnungen</a>
    <a href="<?php echo $config['base_url']; ?>/profil_daten" class="profile-nav-item <?php echo ($request_path_profil === 'profil_daten' ? 'active' : ''); ?>" data-spa-link>Persönliche Daten</a>
    <a href="<?php echo $config['base_url']; ?>/login?logout=1" class="profile-nav-item">Abmelden</a>
</nav>
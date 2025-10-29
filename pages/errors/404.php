<?php
// pages/errors/404.php
// Dies ist die Inhaltsvorlage für die 404-Seite.
// Header und Footer werden von der aufrufenden Funktion eingebunden.
global $config;
?>
<div class="container text-center">
    <h1 class="main-title" style="font-size: 5rem; margin-bottom: 0;">404</h1>
    <p style="font-size: 1.5rem; color: var(--color-text-muted); margin-top: 0;">Filmriss im System!</p>
    <p>Ups! Diese Seite scheint im Schneideraum verloren gegangen zu sein. Keine Sorge, wir spulen für Sie zurück.</p>
    <a href="<?php echo htmlspecialchars($config['base_url']); ?>/" class="btn btn-primary" style="width: auto; margin-top: 20px;" data-spa-link>Zurück zur Startseite</a>
</div>
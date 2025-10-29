<?php
// Die gesamte PHP-Logik wurde in den ProfileController->showProfile() verschoben.
$is_ajax = isset($is_ajax_request);

if (!$is_ajax) {
    include dirname(__DIR__, 2) . '/templates/header.php';
} else {
    echo "<script>document.title = '" . addslashes($page_title) . "'; document.body.className = '$body_class';</script>";
}
?>

<div class="profile-layout">
    <aside class="profile-sidebar">
        <?php include dirname(__DIR__, 2) . '/templates/partials/_profil_nav.php'; ?>
    </aside>
    
    <main class="profile-content">
        <div class="profile-content-header">
            <h1>Herzlich Willkommen, <?php echo htmlspecialchars($user_data['Vorname'] ?? $user_data['Username']); ?>!</h1>
            <p class="meta-info">Kundennummer: <?php echo isset($user_data['KundeId']) ? str_pad($user_data['KundeId'], 10, '0', STR_PAD_LEFT) : 'N/A'; ?></p>
        </div>
        
        <div class="dashboard-grid-links">
            <a href="<?php echo \App\Core\Utils::url('profil_daten'); ?>" class="dashboard-link-card" data-spa-link>
                <span class="icon">&#128100;</span>
                <span>
                    <strong>Meine persönlichen Daten</strong>
                    <small>Name, E-Mail und Passwort ändern</small>
                </span>
                <span class="arrow">&rsaquo;</span>
            </a>
            <a href="<?php echo \App\Core\Utils::url('bestellungen'); ?>" class="dashboard-link-card" data-spa-link>
                <span class="icon">&#128722;</span>
                <span>
                    <strong>Meine Bestellungen</strong>
                    <small>Ihre bisherigen Ausleihvorgänge ansehen</small>
                </span>
                <span class="arrow">&rsaquo;</span>
            </a>
            <a href="<?php echo \App\Core\Utils::url('rechnungen'); ?>" class="dashboard-link-card" data-spa-link>
                 <span class="icon">&#128196;</span>
                <span>
                    <strong>Meine Rechnungen</strong>
                    <small>Alle Rechnungen herunterladen</small>
                </span>
                <span class="arrow">&rsaquo;</span>
            </a>
        </div>

        <?php if (!empty($recommendations)): ?>
        <section class="profile-section-card" style="margin-top: 40px;">
            <h2>Für Sie empfohlen</h2>
            <p class="form-hint" style="margin-top: -15px;">Basierend auf Filmen, die Ihnen gefallen haben.</p>
            <div class="recommendations-grid">
                <?php foreach ($recommendations as $movie): ?>
                    <a href="<?php echo \App\Core\Utils::url('movie/' . ($movie['slug'] ?? $movie['id'])); ?>" class="recommendation-card" data-spa-link>
                        <img loading="lazy" decoding="async" crossorigin="anonymous" src="<?php echo $config['base_url'] . '/' . htmlspecialchars($movie['PosterPath']); ?>" alt="Poster von <?php echo htmlspecialchars($movie['title']); ?>">
                        <div class="recommendation-info">
                            <strong><?php echo htmlspecialchars($movie['title']); ?></strong>
                            <small><?php echo htmlspecialchars($movie['Genre']); ?></small>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>
    
    </main>
</div>

<?php
if (!$is_ajax) {
    include dirname(__DIR__, 2) . '/templates/footer.php';
}
?>
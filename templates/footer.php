<?php
$settings = \App\Core\Utils::getSettings();
?>
    </main>

    <footer class="site-footer">
        <div class="projector-light-effect"></div>
        <div class="footer-container">
            <div class="footer-content">
                <div class="footer-col about">
                    <h4 class="footer-title"><?php echo htmlspecialchars($settings['site_title']); ?></h4>
                    <p>Ihr Portal für cineastische Meisterwerke. Leihen Sie die besten Filme einfach und bequem von zu Hause aus. Tauchen Sie ein in die Welt des Kinos.</p>
                </div>
                <div class="footer-col links">
                    <h4 class="footer-title">Top-Genres</h4>
                    <nav>
                        <?php
                            // KORREKTUR: $pdo und $config werden hier global verfügbar gemacht.
                            global $pdo, $config;
                            try {
                                $mediaRepository = new App\Repositories\MediaRepository($pdo, $config);
                                $genres_data = $mediaRepository->getAllUniqueGenres();

                                $movie_genres = $genres_data['movie_genres'] ?? [];
                                $series_genres = $genres_data['series_genres'] ?? [];

                                // Erstellt eine kombinierte Liste, um den richtigen Pfad pro Genre zu finden.
                                $genre_links = [];
                                foreach ($movie_genres as $genre) {
                                    $genre_links[trim($genre)] = 'select'; // 'select' ist die Route für Filme
                                }
                                foreach ($series_genres as $genre) {
                                    $trimmed_genre = trim($genre);
                                    if (!isset($genre_links[$trimmed_genre])) {
                                        $genre_links[$trimmed_genre] = 'series'; // 'series' ist die Route für Serien
                                    }
                                }
                                ksort($genre_links); // Sortiert Genres alphabetisch

                                if (!empty($genre_links)) {
                                    $display_genres = array_slice($genre_links, 0, 5, true);
                                    foreach ($display_genres as $genre_name => $path) {
                                        $url = $config['base_url'] . '/' . $path . '?genre=' . urlencode($genre_name);
                                        echo '<a href="' . $url . '" data-spa-link>' . htmlspecialchars($genre_name) . '</a>';
                                    }
                                } else {
                                    echo '<p style="font-size:0.9rem; color:rgba(255,255,255,0.7);">Keine Genres gefunden.</p>';
                                }
                            } catch (Exception $e) {
                                 error_log("Footer Genre Fehler: " . $e->getMessage());
                                 echo '<p style="font-size:0.9rem; color:rgba(255,255,255,0.7);">Genres laden fehlgeschlagen.</p>';
                            }
                        ?>
                    </nav>
                </div>
                <div class="footer-col links">
                    <h4 class="footer-title">Service</h4>
                    <nav>
                        <a href="<?php echo $config['base_url']; ?>/profil" data-spa-link>Mein Konto</a>
                        <a href="<?php echo $config['base_url']; ?>/hilfe" data-spa-link>Hilfe & FAQ</a>
                        <a href="<?php echo $config['base_url']; ?>/kontakt" data-spa-link>Kontakt</a>
                        <a href="<?php echo $config['base_url']; ?>/ueber-uns" data-spa-link>Über Uns</a>
                        <a href="<?php echo $config['base_url']; ?>/public/Browsergame/index.html">Abschnitt 11</a>
                        <!-- NEU: Link zur Demonstration der 404-Seite -->
                        <a href="<?php echo $config['base_url']; ?>/seite-die-es-nicht-gibt" data-spa-link>404-Demo</a>
                    </nav>
                </div>
                <div class="footer-col action">
                    <h4 class="footer-title">Bleiben Sie dran</h4>
                    <p>Die neusten Film-Highlights direkt in Ihr Postfach!</p>
                    <form class="newsletter-form">
                        <input type="email" name="email" placeholder="Ihre E-Mail-Adresse" required>
                        <button type="submit" aria-label="Newsletter abonnieren">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <nav class="legal-nav">
                <a href="<?php echo $config['base_url']; ?>/agb" data-spa-link>AGB</a>
                <a href="<?php echo $config['base_url']; ?>/impressum" data-spa-link>Impressum</a>
                <a href="<?php echo $config['base_url']; ?>/datenschutz" data-spa-link>Datenschutz</a>
            </nav>
            <p class="footer-copyright">
                &copy; <?php echo date("Y"); ?> <?php echo htmlspecialchars($settings['site_title']); ?>. Alle Rechte vorbehalten.
            </p>
        </div>
    </footer>

    <script>
        window.APP_CONFIG = {
            baseUrl: '<?php echo $config['base_url']; ?>',
            userRole: '<?php echo $_SESSION['user_role'] ?? ''; ?>',
            settings: <?php echo json_encode($settings); ?>
        };
    </script>
    <?php
    // Lädt die von Vite kompilierten und versionierten JS/CSS-Dateien.
    \App\Core\Utils::viteAssets($config);
    ?>
</body>
</html>

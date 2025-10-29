<?php
// Die Logik befindet sich jetzt im MediaController@showGenres.
// Variablen werden vom Controller bereitgestellt.
$is_ajax = isset($is_ajax_request);

if (!$is_ajax) {
    include dirname(__DIR__, 2) . '/templates/header.php';
} else {
    echo "<script>document.title = '".addslashes($page_title)."'; document.body.className = '';</script>";
}
?>
<div class="page-wrapper">
    <div class="page-title-bar">
        <h1 class="main-title">Nach Genre stöbern</h1>
        <a href="<?php echo $config['base_url']; ?>/index.php" class="btn btn-secondary" style="width: auto;" data-spa-link>Zurück zum Menü</a>
    </div>

    <?php if (!empty($message)): ?>
        <div class="message error"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <?php if (empty($movie_genres) && empty($series_genres)): ?>
        <div class="container text-center">
            <p>Es wurden keine Genres in der Datenbank gefunden.</p>
        </div>
    <?php else: ?>
        
        <?php if (!empty($movie_genres)): ?>
            <h2 class="genre-section-title">Film-Genres</h2>
            <div class="genre-grid">
                <?php foreach ($movie_genres as $genre): ?>
                    <a href="select?genre=<?php echo urlencode($genre); ?>" class="genre-card" data-spa-link>
                        <?php echo htmlspecialchars($genre); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($series_genres)): ?>
            <h2 class="genre-section-title">Serien-Genres</h2>
            <div class="genre-grid">
                <?php foreach ($series_genres as $genre): ?>
                    <a href="series?genre=<?php echo urlencode($genre); ?>" class="genre-card" data-spa-link>
                        <?php echo htmlspecialchars($genre); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    <?php endif; ?>
</div>
<?php
if (!$is_ajax) {
    include dirname(__DIR__, 2) . '/templates/footer.php';
}
?>

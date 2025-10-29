<?php
// Die gesamte Logik befindet sich jetzt im HomeController.
// Alle Variablen ($page_title, $featured_movie etc.) werden vom Controller bereitgestellt.
include dirname(__DIR__) . '/templates/header.php';
?>

<section class="hero" style="--hero-bg-image: url('<?php echo $hero_image_path; ?>');">
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <?php if ($featured_movie): ?>
            <p class="hero-badge">Neu im Verleih</p>
            <h1 class="hero-title"><?php echo htmlspecialchars($featured_movie['Moviename']); ?></h1>
            <p class="hero-subtitle">
                <?php echo htmlspecialchars(substr($featured_movie['Beschreibung'], 0, 150)); ?>...
            </p>
            <div class="hero-actions">
                <a href="<?php echo $config['base_url']; ?>/movie/<?php echo htmlspecialchars($featured_movie['slug']); ?>" class="btn btn-primary btn-large" data-spa-link>Details ansehen</a>
                <a href="<?php echo $config['base_url']; ?>/select" class="btn btn-secondary btn-large" data-spa-link>Alle Filme entdecken</a>
            </div>
        <?php else: ?>
            <h1 class="hero-title">Dein Kino für Zuhause.</h1>
            <p class="hero-subtitle">Entdecke hunderte von Meisterwerken und die neuesten Blockbuster. Einfach ausleihen und sofort streamen.</p>
            <div class="hero-actions">
                <a href="<?php echo $config['base_url']; ?>/select" class="btn btn-primary btn-large" data-spa-link>Alle Filme entdecken</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php if (!empty($top_rated_movies)): ?>
<section class="home-category-section">
    <h2 class="category-title">Top-Bewertet</h2>
    <div class="carousel-wrapper">
        <div class="movie-carousel">
            <?php foreach ($top_rated_movies as $movie): ?>
                <a href="<?php echo $config['base_url']; ?>/movie/<?php echo htmlspecialchars($movie['slug']); ?>" class="carousel-movie-card" data-spa-link>
                    <img loading="lazy" src="<?php echo $config['base_url'] . '/' . htmlspecialchars($movie['PosterPath']); ?>" alt="<?php echo htmlspecialchars($movie['Moviename']); ?>">
                    <div class="carousel-card-overlay">
                        <h3><?php echo htmlspecialchars($movie['Moviename']); ?></h3>
                        <p><?php echo htmlspecialchars($movie['Genre']); ?></p>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if (!empty($classic_movies)): ?>
<section class="home-category-section">
    <h2 class="category-title">Klassiker der Filmgeschichte</h2>
    <div class="carousel-wrapper">
        <div class="movie-carousel">
            <?php foreach ($classic_movies as $movie): ?>
                <a href="<?php echo $config['base_url']; ?>/movie/<?php echo htmlspecialchars($movie['slug']); ?>" class="carousel-movie-card" data-spa-link>
                    <img loading="lazy" src="<?php echo $config['base_url'] . '/' . htmlspecialchars($movie['PosterPath']); ?>" alt="<?php echo htmlspecialchars($movie['Moviename']); ?>">
                    <div class="carousel-card-overlay">
                        <h3><?php echo htmlspecialchars($movie['Moviename']); ?></h3>
                        <p><?php echo htmlspecialchars($movie['Genre']); ?></p>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if ($flash_message_for_script): ?>
<script>
    window.addEventListener('load', function() {
        if (window.showToast) {
            window.showToast('<?php echo addslashes($flash_message_for_script); ?>', 'success');
        }
    });
</script>
<?php endif; ?>

<?php
include dirname(__DIR__) . '/templates/footer.php';
?>

<?php
// Dies ist jetzt die "View" für den MediaController::index()
// Alle Variablen ($page_title, $media_type etc.) werden vom Controller bereitgestellt.

// Die `require_once init.php` und die Security-Prüfung sind nicht mehr nötig,
// da der Controller dies bereits erledigt hat.

include_once dirname(__DIR__, 2) . '/templates/header.php';
?>

<h1 class="main-title"><?php echo htmlspecialchars($page_title); ?></h1>
<div class="dashboard-grid">
    <?php include 'partials/_sidebar_nav.php'; ?>
    <main class="dashboard-content">
        <div class="dashboard-section">
            <?php include dirname(__DIR__, 2) . '/templates/search_filter_form.php'; ?>

            <?php if (!empty($message)): ?>
                <div class="message <?php echo htmlspecialchars($message_type); ?>"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>

            <div id="<?php echo htmlspecialchars($media_type); ?>-grid-container" class="movie-grid" data-type="<?php echo htmlspecialchars($media_type); ?>">
                <div class="container text-center" style="grid-column: 1 / -1;"><p>Lade Inhalte...</p></div>
            </div>
            
            <nav id="pagination-container" class="pagination-nav" aria-label="Seitennavigation"></nav>
        </div>
    </main>
</div>

<?php
include_once dirname(__DIR__, 2) . '/templates/footer.php';
?>
<?php
// templates/_media_grid_layout.php

// Lade den Header
include __DIR__ . '/header.php';

// Stelle sicher, dass $page_type einen Fallback hat, falls er nicht gesetzt ist
$page_type = $page_type ?? 'movies'; 
?>

<div class="page-wrapper">
    <div class="page-title-bar">
        <h1 class="main-title"><?php echo htmlspecialchars($page_title ?? 'Übersicht'); ?></h1>
    </div>

    <?php
    // Übergebe $page_type an search_filter_form.php
    include __DIR__ . '/search_filter_form.php';
    ?>

    <div id="<?php echo htmlspecialchars($page_type); ?>-grid-container" class="movie-grid" data-type="<?php echo htmlspecialchars($page_type); ?>">
        <p class="loading-placeholder">Lade Inhalte...</p>
    </div>
    
    <nav id="pagination-container" class="pagination-nav">
        </nav>
</div>

<?php
// Lade den Footer
include __DIR__ . '/footer.php';
?>
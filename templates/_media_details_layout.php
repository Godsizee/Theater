<?php
$seasons = $seasons ?? [];
$is_ajax = isset($is_ajax_request);

if (!$is_ajax) {
    include __DIR__ . '/header.php';
} else {
    echo "<script>document.title = '".addslashes($page_title)."'; document.body.className = '';</script>";
}
?>

<div class="page-wrapper">
    <div class="movie-detail-container">
        <div class="movie-detail-poster">
            <img src="<?php echo $config['base_url'] . '/' . htmlspecialchars($media['PosterPath']); ?>" alt="Poster von <?php echo htmlspecialchars($media['title']); ?>">
        </div>
        <div class="movie-detail-info">
            <h1><?php echo htmlspecialchars($media['title']); ?></h1>

            <div class="movie-detail-meta">
                <?php if (isset($media['USK'])): ?>
                    <span><span class="meta-tag usk-<?php echo htmlspecialchars($media['USK']); ?>">FSK <?php echo htmlspecialchars($media['USK']); ?></span></span>
                <?php endif; ?>
                
                <?php if ($media_type === 'movie'): ?>
                    <span><?php echo htmlspecialchars($media['Erscheinungsjahr'] ?? 'N/A'); ?></span>
                    <span><?php echo htmlspecialchars($media['Laufzeit'] ?? 'N/A'); ?> Min.</span>
                <?php else: ?>
                    <span><?php echo htmlspecialchars($media['StartYear'] ?? 'N/A'); ?> - <?php echo htmlspecialchars($media['EndYear'] ?? 'heute'); ?></span>
                    <span><?php echo htmlspecialchars($media['NumberOfSeasons'] ?? 'N/A'); ?> Staffeln</span>
                <?php endif; ?>
            </div>
            
            <div class="movie-tags">
                <?php 
                    $genres = explode(',', $media['Genre'] ?? ''); 
                    foreach($genres as $genre) {
                        if (trim($genre)) {
                            $genre_url = $config['base_url'] . '/' . ($media_type === 'movie' ? 'select' : 'series') . '?genre=' . urlencode(trim($genre));
                            echo '<a href="' . $genre_url . '" class="btn btn-secondary btn-small" data-spa-link>' . htmlspecialchars(trim($genre)) . '</a>'; 
                        }
                    }
                ?>
            </div>

            <h2 class="detail-section-title">Beschreibung</h2>
            <p><?php echo nl2br(htmlspecialchars($media['Beschreibung'] ?? 'Keine Beschreibung vorhanden.')); ?></p>

            <h2 class="detail-section-title">Details</h2>
            <div class="detail-list">
                <?php if ($media_type === 'movie'): ?>
                    <div class="detail-item"><span class="data-label">Regisseur:</span><span class="data-value"><?php echo htmlspecialchars($media['Regisseur'] ?? 'N/A'); ?></span></div>
                <?php else: ?>
                    <div class="detail-item"><span class="data-label">Serienschöpfer:</span><span class="data-value"><?php echo htmlspecialchars($media['Creator'] ?? 'N/A'); ?></span></div>
                <?php endif; ?>
                <div class="detail-item"><span class="data-label">Genre:</span><span class="data-value"><?php echo htmlspecialchars($media['Genre'] ?? 'N/A'); ?></span></div>
            </div>

            <div class="form-actions" style="margin-top: 30px;">
                <a href="#" 
                   class="btn btn-primary add-to-cart" 
                   data-id="<?php echo htmlspecialchars($media['id']); ?>" 
                   data-type="<?php echo htmlspecialchars($media_type); ?>"
                   data-title="<?php echo htmlspecialchars($media['title']); ?>"
                   data-price="<?php echo htmlspecialchars($media['price']); ?>">
                    Jetzt ausleihen für <?php echo number_format($media['price'], 2, ',', '.'); ?> € <?php echo $media_type === 'series' ? '/ Staffel' : ''; ?>
                </a>
            </div>
        </div>
    </div>

    <?php if ($media_type === 'series' && !empty($seasons)): ?>
    <div class="series-episode-section">
        <h2 class="detail-section-title">Episodenübersicht</h2>
        <?php foreach ($seasons as $season_number => $episodes): ?>
            <div class="collapsible-section">
                <button class="collapsible-header" aria-expanded="false">
                    <span>Staffel <?php echo htmlspecialchars($season_number); ?> (<?php echo count($episodes); ?> Episoden)</span>
                    <span class="collapsible-chevron">&rsaquo;</span>
                </button>
                <div class="collapsible-content">
                    <ul class="episode-list">
                        <?php foreach ($episodes as $episode): ?>
                            <li>
                                <span class="episode-number"><?php echo htmlspecialchars($episode['EpisodeNumber']); ?>.</span>
                                <span class="episode-title"><?php echo htmlspecialchars($episode['Title']); ?></span>
                                <span class="episode-runtime"><?php echo htmlspecialchars($episode['Laufzeit'] ?? 'N/A'); ?> Min.</span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<?php
if (!$is_ajax) {
    include __DIR__ . '/footer.php';
}
?>
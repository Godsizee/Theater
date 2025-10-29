<?php
// admin/partials/_missing_data_section.php
?>
<div class="dashboard-section" style="margin-top: 40px;">
    <h3>Status der Daten (Filme & Serien)</h3> <h4 style="margin-top: 30px; border-bottom: 1px solid var(--color-border); padding-bottom: 10px;">Filmdaten-Übersicht</h4>
    <?php $total_missing_movies_count = array_sum($missing_movie_data_counts); ?>
    <?php if ($total_missing_movies_count > 0): ?>
        <p class="message error">Achtung: Es gibt Filme mit unvollständigen Informationen. Bitte überprüfen Sie diese.</p>
        <div class="dashboard-widget-grid">
            <a href="?filter=genre_missing" class="dashboard-widget <?php echo ($current_filter === 'genre_missing' && $active_filter_type === 'movie' ? 'active-filter' : ''); ?>">
                <h4>Fehlendes Genre</h4>
                <p class="stat-value <?php echo ($missing_movie_data_counts['missing_genre_count'] > 0) ? 'error-stat' : ''; ?>"><?php echo htmlspecialchars($missing_movie_data_counts['missing_genre_count']); ?></p>
            </a>
            <a href="?filter=description_missing" class="dashboard-widget <?php echo ($current_filter === 'description_missing' && $active_filter_type === 'movie' ? 'active-filter' : ''); ?>">
                <h4>Fehlende Beschreibung</h4>
                <p class="stat-value <?php echo ($missing_movie_data_counts['missing_description_count'] > 0) ? 'error-stat' : ''; ?>"><?php echo htmlspecialchars($missing_movie_data_counts['missing_description_count']); ?></p>
            </a>
            <a href="?filter=price_missing" class="dashboard-widget <?php echo ($current_filter === 'price_missing' && $active_filter_type === 'movie' ? 'active-filter' : ''); ?>">
                <h4>Fehlender Preis</h4>
                <p class="stat-value <?php echo ($missing_movie_data_counts['missing_price_count'] > 0) ? 'error-stat' : ''; ?>"><?php echo htmlspecialchars($missing_movie_data_counts['missing_price_count']); ?></p>
            </a>
            <a href="?filter=poster_missing" class="dashboard-widget <?php echo ($current_filter === 'poster_missing' && $active_filter_type === 'movie' ? 'active-filter' : ''); ?>">
                <h4>Fehlendes Poster</h4>
                <p class="stat-value <?php echo ($missing_movie_data_counts['missing_poster_count'] > 0) ? 'error-stat' : ''; ?>"><?php echo htmlspecialchars($missing_movie_data_counts['missing_poster_count']); ?></p>
            </a>
            <a href="?filter=year_missing" class="dashboard-widget <?php echo ($current_filter === 'year_missing' && $active_filter_type === 'movie' ? 'active-filter' : ''); ?>">
                <h4>Fehlendes Jahr</h4>
                <p class="stat-value <?php echo ($missing_movie_data_counts['missing_year_count'] > 0) ? 'error-stat' : ''; ?>"><?php echo htmlspecialchars($missing_movie_data_counts['missing_year_count']); ?></p>
            </a>
            <a href="?filter=runtime_missing" class="dashboard-widget <?php echo ($current_filter === 'runtime_missing' && $active_filter_type === 'movie' ? 'active-filter' : ''); ?>">
                <h4>Fehlende Laufzeit</h4>
                <p class="stat-value <?php echo ($missing_movie_data_counts['missing_runtime_count'] > 0) ? 'error-stat' : ''; ?>"><?php echo htmlspecialchars($missing_movie_data_counts['missing_runtime_count']); ?></p>
            </a>
            <a href="?filter=director_missing" class="dashboard-widget <?php echo ($current_filter === 'director_missing' && $active_filter_type === 'movie' ? 'active-filter' : ''); ?>">
                <h4>Fehlender Regisseur</h4>
                <p class="stat-value <?php echo ($missing_movie_data_counts['missing_director_count'] > 0) ? 'error-stat' : ''; ?>"><?php echo htmlspecialchars($missing_movie_data_counts['missing_director_count']); ?></p>
            </a>
             <a href="?filter=all_movies_missing" class="dashboard-widget <?php echo ($active_filter_type === 'any_movie' ? 'active-filter' : ''); ?>">
                <h4>Gesamt Filme</h4>
                <p class="stat-value <?php echo ($total_movies_with_any_missing_data > 0) ? 'error-stat' : ''; ?>"><?php echo htmlspecialchars($total_movies_with_any_missing_data); ?></p>
            </a>
        </div>
        <div style="text-align: center; margin-top: 20px;">
            <a href="index.php?filter=all_movies_missing" class="btn btn-secondary small-btn">Film-Filter anzeigen / zurücksetzen</a>
        </div>

        <?php if (!empty($movies_with_missing_data) && ($active_filter_type === 'movie' || $active_filter_type === 'any_movie')): ?>
            <h4 style="margin-top: 30px; border-bottom: 1px solid var(--color-border); padding-bottom: 10px;">
                Betroffene Filme (<?php echo htmlspecialchars($total_movies_with_any_missing_data); ?>):
            </h4>
            <ul class="missing-data-list">
                <?php foreach ($movies_with_missing_data as $movie): ?>
                    <li>
                        <span><?php echo htmlspecialchars($movie['Moviename']); ?></span>
                        <span class="missing-labels">
                            <?php if ($movie['missing_genre']) echo '<span class="label label-danger">Genre</span>'; ?>
                            <?php if ($movie['missing_description']) echo '<span class="label label-danger">Beschreibung</span>'; ?>
                            <?php if ($movie['missing_price']) echo '<span class="label label-danger">Preis</span>'; ?>
                            <?php if ($movie['missing_poster']) echo '<span class="label label-danger">Poster</span>'; ?>
                            <?php if ($movie['missing_year']) echo '<span class="label label-danger">Jahr</span>'; ?>
                            <?php if ($movie['missing_runtime']) echo '<span class="label label-danger">Laufzeit</span>'; ?>
                            <?php if ($movie['missing_director']) echo '<span class="label label-danger">Regisseur</span>'; ?>
                        </span>
                        <a href="<?php echo $config['base_url']; ?>/admin/update/movie/<?php echo htmlspecialchars($movie['MovieId']); ?>" class="action-btn edit small-btn">Bearbeiten</a>
                        <a href="#" class="action-btn delete small-btn" data-id="<?php echo htmlspecialchars($movie['MovieId']); ?>" data-type="movie">Löschen</a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php elseif ($active_filter_type === 'movie' || $active_filter_type === 'any_movie'): ?>
            <p class="message success">Es sind keine Filme gefunden worden, die dem aktuellen Film-Filter entsprechen.</p>
        <?php endif; ?>

    <?php else: // Keine Filme mit fehlenden Daten ?>
        <p class="message success">Alle Filme haben vollständige Daten! Gut gemacht!</p>
    <?php endif; ?>

    <h4 style="margin-top: 60px; border-bottom: 1px solid var(--color-border); padding-bottom: 10px;">Seriendaten-Übersicht</h4>
    <?php $total_missing_series_count = array_sum($missing_series_data_counts); ?>
    <?php if ($total_missing_series_count > 0): ?>
        <p class="message error">Achtung: Es gibt Serien mit unvollständigen Informationen. Bitte überprüfen Sie diese.</p>
        <div class="dashboard-widget-grid">
            <a href="?filter=series_genre_missing" class="dashboard-widget <?php echo ($current_filter === 'series_genre_missing' && $active_filter_type === 'series' ? 'active-filter' : ''); ?>">
                <h4>Fehlendes Genre</h4>
                <p class="stat-value <?php echo ($missing_series_data_counts['missing_genre_count'] > 0) ? 'error-stat' : ''; ?>"><?php echo htmlspecialchars($missing_series_data_counts['missing_genre_count']); ?></p>
            </a>
            <a href="?filter=series_description_missing" class="dashboard-widget <?php echo ($current_filter === 'series_description_missing' && $active_filter_type === 'series' ? 'active-filter' : ''); ?>">
                <h4>Fehlende Beschreibung</h4>
                <p class="stat-value <?php echo ($missing_series_data_counts['missing_description_count'] > 0) ? 'error-stat' : ''; ?>"><?php echo htmlspecialchars($missing_series_data_counts['missing_description_count']); ?></p>
            </a>
            <a href="?filter=series_price_missing" class="dashboard-widget <?php echo ($current_filter === 'series_price_missing' && $active_filter_type === 'series' ? 'active-filter' : ''); ?>">
                <h4>Fehlender Preis</h4>
                <p class="stat-value <?php echo ($missing_series_data_counts['missing_price_count'] > 0) ? 'error-stat' : ''; ?>"><?php echo htmlspecialchars($missing_series_data_counts['missing_price_count']); ?></p>
            </a>
            <a href="?filter=series_poster_missing" class="dashboard-widget <?php echo ($current_filter === 'series_poster_missing' && $active_filter_type === 'series' ? 'active-filter' : ''); ?>">
                <h4>Fehlendes Poster</h4>
                <p class="stat-value <?php echo ($missing_series_data_counts['missing_poster_count'] > 0) ? 'error-stat' : ''; ?>"><?php echo htmlspecialchars($missing_series_data_counts['missing_poster_count']); ?></p>
            </a>
            <a href="?filter=series_start_year_missing" class="dashboard-widget <?php echo ($current_filter === 'series_start_year_missing' && $active_filter_type === 'series' ? 'active-filter' : ''); ?>">
                <h4>Fehlendes Startjahr</h4>
                <p class="stat-value <?php echo ($missing_series_data_counts['missing_start_year_count'] > 0) ? 'error-stat' : ''; ?>"><?php echo htmlspecialchars($missing_series_data_counts['missing_start_year_count']); ?></p>
            </a>
            <a href="?filter=series_end_year_missing" class="dashboard-widget <?php echo ($current_filter === 'series_end_year_missing' && $active_filter_type === 'series' ? 'active-filter' : ''); ?>">
                <h4>Fehlendes Endjahr</h4>
                <p class="stat-value <?php echo ($missing_series_data_counts['missing_end_year_count'] > 0) ? 'error-stat' : ''; ?>"><?php echo htmlspecialchars($missing_series_data_counts['missing_end_year_count']); ?></p>
            </a>
            <a href="?filter=series_seasons_missing" class="dashboard-widget <?php echo ($current_filter === 'series_seasons_missing' && $active_filter_type === 'series' ? 'active-filter' : ''); ?>">
                <h4>Fehlende Staffeln</h4>
                <p class="stat-value <?php echo ($missing_series_data_counts['missing_seasons_count'] > 0) ? 'error-stat' : ''; ?>"><?php echo htmlspecialchars($missing_series_data_counts['missing_seasons_count']); ?></p>
            </a>
            <a href="?filter=series_creator_missing" class="dashboard-widget <?php echo ($current_filter === 'series_creator_missing' && $active_filter_type === 'series' ? 'active-filter' : ''); ?>">
                <h4>Fehlender Serienschöpfer</h4>
                <p class="stat-value <?php echo ($missing_series_data_counts['missing_creator_count'] > 0) ? 'error-stat' : ''; ?>"><?php echo htmlspecialchars($missing_series_data_counts['missing_creator_count']); ?></p>
            </a>
             <a href="?filter=all_series_missing" class="dashboard-widget <?php echo ($active_filter_type === 'any_series' ? 'active-filter' : ''); ?>">
                <h4>Gesamt Serien</h4>
                <p class="stat-value <?php echo ($total_series_with_any_missing_data > 0) ? 'error-stat' : ''; ?>"><?php echo htmlspecialchars($total_series_with_any_missing_data); ?></p>
            </a>
        </div>
        <div style="text-align: center; margin-top: 20px;">
            <a href="index.php?filter=all_series_missing" class="btn btn-secondary small-btn">Serien-Filter anzeigen / zurücksetzen</a>
        </div>

        <?php if (!empty($series_with_missing_data) && ($active_filter_type === 'series' || $active_filter_type === 'any_series')): ?>
            <h4 style="margin-top: 30px; border-bottom: 1px solid var(--color-border); padding-bottom: 10px;">
                Betroffene Serien (<?php echo htmlspecialchars($total_series_with_any_missing_data); ?>):
            </h4>
            <ul class="missing-data-list">
                <?php foreach ($series_with_missing_data as $serie): ?>
                    <li>
                        <span><?php echo htmlspecialchars($serie['SeriesTitle']); ?></span>
                        <span class="missing-labels">
                            <?php if ($serie['missing_genre']) echo '<span class="label label-danger">Genre</span>'; ?>
                            <?php if ($serie['missing_description']) echo '<span class="label label-danger">Beschreibung</span>'; ?>
                            <?php if ($serie['missing_price']) echo '<span class="label label-danger">Preis</span>'; ?>
                            <?php if ($serie['missing_poster']) echo '<span class="label label-danger">Poster</span>'; ?>
                            <?php if ($serie['missing_start_year']) echo '<span class="label label-danger">Startjahr</span>'; ?>
                            <?php if ($serie['missing_end_year']) echo '<span class="label label-danger">Endjahr</span>'; ?>
                            <?php if ($serie['missing_seasons']) echo '<span class="label label-danger">Staffeln</span>'; ?>
                            <?php if ($serie['missing_creator']) echo '<span class="label label-danger">Serienschöpfer</span>'; ?>
                        </span>
                        <a href="<?php echo $config['base_url']; ?>/admin/update/series/<?php echo htmlspecialchars($serie['SeriesId']); ?>" class="action-btn edit small-btn">Bearbeiten</a>
                        <a href="#" class="action-btn delete small-btn" data-id="<?php echo htmlspecialchars($serie['SeriesId']); ?>" data-type="series">Löschen</a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php elseif ($active_filter_type === 'series' || $active_filter_type === 'any_series'): ?>
            <p class="message success">Es sind keine Serien gefunden worden, die dem aktuellen Serien-Filter entsprechen.</p>
        <?php endif; ?>
    <?php else: ?>
        <p class="message success">Alle Serien haben vollständige Daten! Gut gemacht!</p>
    <?php endif; ?>
</div>
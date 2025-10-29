<?php
// Aktuelle Werte aus der URL holen, um das Formular vorauszufüllen
$current_search = isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '';
$current_usk = isset($_GET['usk']) ? $_GET['usk'] : '';
$current_sort = isset($_GET['sort']) ? $_GET['sort'] : ($settings['default_sort_order'] ?? 'name_asc');

$form_id = ($page_type ?? 'movies') === 'series' ? 'series-filter-form' : 'movie-filter-form';
?>

<div class="search-filter-container">
    <form id="<?php echo htmlspecialchars($form_id); ?>" action="<?php echo htmlspecialchars($form_action); ?>" method="get" class="search-filter-form">
        
        <?php
        // KORREKTUR: Fügt ein verstecktes Feld hinzu, wenn wir uns in der Admin-Medienübersicht befinden,
        // um den Typ (movies/series) bei der Formular-Absendung beizubehalten.
        if (str_contains($form_action, 'admin/media_overview')) {
            $current_type = $_GET['type'] ?? 'movies';
            echo '<input type="hidden" name="type" value="' . htmlspecialchars($current_type) . '">';
        }
        ?>
        
        <div class="form-group search-group" style="position: relative;">
            <label for="search" class="visually-hidden">Suche nach Filmnamen</label>
            <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                <path d="M10 18a8 8 0 1 1 8-8a8.009 8.009 0 0 1-8 8zm0-14a6 6 0 1 0 6 6a6.007 6.007 0 0 0-6-6z"/>
                <path d="M20.707 19.293l-4-4a1 1 0 0 0-1.414 1.414l4 4a1 1 0 0 0 1.414-1.414z"/>
            </svg>
            <input type="search" id="search" name="search" class="form-control" placeholder="Film suchen..." value="<?php echo $current_search; ?>">
        </div>

        <div class="form-group filter-group">
            <label for="usk" class="visually-hidden">Filter nach FSK</label>
            <select name="usk" id="usk" class="form-control">
                <option value="">Alle FSK</option>
                <option value="0"  <?php if ($current_usk === '0') echo 'selected'; ?>>FSK 0</option>
                <option value="6"  <?php if ($current_usk === '6') echo 'selected'; ?>>FSK 6</option>
                <option value="12" <?php if ($current_usk === '12') echo 'selected'; ?>>FSK 12</option>
                <option value="16" <?php if ($current_usk === '16') echo 'selected'; ?>>FSK 16</option>
                <option value="18" <?php if ($current_usk === '18') echo 'selected'; ?>>FSK 18</option>
            </select>
        </div>

        <div class="form-group sort-group">
            <label for="sort" class="visually-hidden">Sortieren nach</label>
            <select name="sort" id="sort" class="form-control">
                <option value="name_asc" <?php if ($current_sort === 'name_asc') echo 'selected'; ?>>Name (A-Z)</option>
                <option value="name_desc" <?php if ($current_sort === 'name_desc') echo 'selected'; ?>>Name (Z-A)</option>
                <option value="year_desc" <?php if ($current_sort === 'year_desc') echo 'selected'; ?>>Neueste zuerst</option>
                <option value="year_asc" <?php if ($current_sort === 'year_asc') echo 'selected'; ?>>Älteste zuerst</option>
            </select>
        </div>

        <div class="form-group button-group">
            <button type="submit" class="btn btn-primary">Suchen</button>
            <a href="<?php echo htmlspecialchars($form_action); ?>" class="btn btn-secondary">Reset</a>
        </div>

    </form>
</div>
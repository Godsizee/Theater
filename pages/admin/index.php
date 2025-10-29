<?php
// DIESE ZEILE IST DIE KORREKTUR:
// Sie geht vom aktuellen Verzeichnis (.../pages/admin) zwei Ebenen nach oben.
require_once dirname(__DIR__, 2) . '/init.php';

\App\Core\Security::requireAdmin();

$dashboardService = new \App\Services\DashboardService($pdo);

$globalStats = $dashboardService->getGlobalStats();
$missingMovieStats = $dashboardService->getMissingMovieDataStats();
$missingSeriesStats = $dashboardService->getMissingSeriesDataStats();

$current_filter = $_GET['filter'] ?? 'all_movies_missing';
$active_filter_type = str_contains($current_filter, 'series') ? 'series' : 'movie';
$movies_with_missing_data = [];
$series_with_missing_data = [];

if ($active_filter_type === 'movie') {
    $movies_with_missing_data = $dashboardService->getFilteredMediaWithMissingData($current_filter);
} else {
    $series_with_missing_data = $dashboardService->getFilteredMediaWithMissingData($current_filter);
}

$page_title = 'Admin Dashboard';
$body_class = 'admin-dashboard-body';
include dirname(__DIR__, 2) .'/templates/header.php';
?>

<h1 class="main-title">Admin Dashboard</h1>
<div class="dashboard-grid">
    <?php include 'partials/_sidebar_nav.php'; ?>
    <main class="dashboard-content">
        <?php
            $total_movies = $globalStats['total_movies'];
            $total_series = $globalStats['total_series'];
            $total_users = $globalStats['total_users'];
            
            $missing_movie_data_counts = $missingMovieStats;
            $total_movies_with_any_missing_data = $missingMovieStats['total_movies_with_any_missing_data'];
            
            $missing_series_data_counts = $missingSeriesStats;
            $total_series_with_any_missing_data = $missingSeriesStats['total_series_with_any_missing_data'];

            include 'partials/_overview_stats.php';
            include 'partials/_missing_data_section.php';
        ?>
    </main>
</div>

<?php
include dirname(__DIR__, 2) .'/templates/footer.php';
?>
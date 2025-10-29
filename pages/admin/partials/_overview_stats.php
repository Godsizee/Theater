<?php
// admin/partials/_overview_stats.php
?>
<div class="dashboard-section">
    <h3>Übersicht</h3>
    <p>Willkommen im Admin-Bereich. Hier können Sie Inhalte verwalten und Einstellungen vornehmen.</p>

    <div class="dashboard-widget-grid">
        <a href="<?php echo $config['base_url']; ?>/admin/media_overview.php?type=movies">
            <div class="dashboard-widget">
                <h4>Anzahl Filme</h4>
                <p class="stat-value"><?php echo htmlspecialchars($total_movies); ?></p>
            </div>
        </a>

        <a href="<?php echo $config['base_url']; ?>/admin/media_overview.php?type=series">
            <div class="dashboard-widget">
                <h4>Anzahl Serien</h4>
                <p class="stat-value"><?php echo htmlspecialchars($total_series); ?></p>
            </div>
        </a>

        <a href="<?php echo $config['base_url']; ?>/admin/users.php">
            <div class="dashboard-widget">
                <h4>Registrierte Benutzer</h4>
                <p class="stat-value"><?php echo htmlspecialchars($total_users); ?></p>
            </div>
        </a>
    </div>
</div>
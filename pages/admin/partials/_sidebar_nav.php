<?php
$request_path = trim($_GET['url'] ?? '', '/');
?>
<aside class="dashboard-sidebar">
    <h2>Verwaltung</h2>
    <nav class="dashboard-nav">
        <a href="<?php echo $config['base_url']; ?>/admin" class="dashboard-nav-item <?php echo ($request_path === 'admin' ? 'active' : ''); ?>">
            Dashboard Übersicht
        </a>
        <a href="<?php echo $config['base_url']; ?>/admin/media_overview?type=movies" class="dashboard-nav-item <?php echo (strpos($request_path, 'admin/media_overview') !== false && ($_GET['type'] ?? '') === 'movies' ? 'active' : ''); ?>">
            Filme verwalten
        </a>
        <a href="<?php echo $config['base_url']; ?>/admin/media_overview?type=series" class="dashboard-nav-item <?php echo (strpos($request_path, 'admin/media_overview') !== false && ($_GET['type'] ?? '') === 'series' ? 'active' : ''); ?>">
            Serien verwalten
        </a>
        <a href="<?php echo $config['base_url']; ?>/admin/insert" class="dashboard-nav-item <?php echo ($request_path === 'admin/insert' ? 'active' : ''); ?>">
            Neuen Inhalt hinzufügen
        </a>
        <a href="<?php echo $config['base_url']; ?>/admin/users" class="dashboard-nav-item <?php echo ($request_path === 'admin/users' || strpos($request_path, 'admin/edit_user') !== false ? 'active' : ''); ?>">
            Benutzer verwalten
        </a>
        <a href="<?php echo $config['base_url']; ?>/admin/create_user" class="dashboard-nav-item <?php echo ($request_path === 'admin/create_user' ? 'active' : ''); ?>">
            Benutzer anlegen
        </a>
        <a href="<?php echo $config['base_url']; ?>/admin/logs" class="dashboard-nav-item <?php echo ($request_path === 'admin/logs' ? 'active' : ''); ?>">
            Aktivitätsprotokoll
        </a>
        <a href="<?php echo $config['base_url']; ?>/admin/settings" class="dashboard-nav-item <?php echo ($request_path === 'admin/settings' ? 'active' : ''); ?>">
            Einstellungen
        </a>
    </nav>
</aside>
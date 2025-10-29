<?php

// Die Logik befindet sich im SettingsController.
?>
<?php include_once dirname(__DIR__, 2) .'/templates/header.php'; ?>

<h1 class="main-title">Einstellungen</h1>
<div class="dashboard-grid">
    <?php include 'partials/_sidebar_nav.php'; ?>
    <main class="dashboard-content">
        <div class="dashboard-section">
            <h3>Allgemeine Seiteneinstellungen</h3>

            <?php if (!empty($message)): ?>
                <p class="message <?php echo htmlspecialchars($message_type); ?>"><?php echo htmlspecialchars($message); ?></p>
            <?php endif; ?>

            <form action="<?php echo $config['base_url']; ?>/admin/settings/update" method="post">
                <input type="hidden" name="csrf_token" value="<?php echo \App\Core\Security::generateCsrfToken(); ?>">
                
                <p>
                    Seitentitel (wird im Browser-Tab angezeigt)
                    <input type="text" name="site_title" value="<?php echo htmlspecialchars($settings['site_title']); ?>" required>
                </p>
                <p>
                    Filme pro Seite (in der Übersicht)
                    <input type="number" name="items_per_page" value="<?php echo htmlspecialchars($settings['items_per_page']); ?>" min="1" required>
                </p>
                <p>
                    Standard-Sortierung der Filmliste
                    <select name="default_sort_order">
                        <option value="name_asc" <?php echo ($settings['default_sort_order'] === 'name_asc' ? 'selected' : ''); ?>>Name (A-Z)</option>
                        <option value="name_desc" <?php echo ($settings['default_sort_order'] === 'name_desc' ? 'selected' : ''); ?>>Name (Z-A)</option>
                        <option value="year_desc" <?php echo ($settings['default_sort_order'] === 'year_desc' ? 'selected' : ''); ?>>Neueste zuerst</option>
                        <option value="year_asc" <?php echo ($settings['default_sort_order'] === 'year_asc' ? 'selected' : ''); ?>>Älteste zuerst</option>
                    </select>
                </p>
                <p>
                    Neue Benutzer-Registrierungen erlauben?
                    <label><input type="radio" name="allow_registrations" value="1" <?php echo ($settings['allow_registrations'] ? 'checked' : ''); ?>> Ja</label>
                    <label style="margin-left: 15px;"><input type="radio" name="allow_registrations" value="0" <?php echo (!$settings['allow_registrations'] ? 'checked' : ''); ?>> Nein</label>
                </p>

                <div class="form-actions" style="justify-content: flex-start;">
                    <input type="submit" value="Einstellungen speichern" class="btn btn-primary" style="width: auto;">
                </div>
            </form>
        </div>
    </main>
</div>

<?php include_once dirname(__DIR__, 2) .'/templates/footer.php'; ?>
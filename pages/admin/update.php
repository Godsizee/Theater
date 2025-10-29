<?php
// Dies ist die "View" für den MediaController::edit()
include_once dirname(__DIR__, 2) . '/templates/header.php';
?>

<div class="page-wrapper admin-dashboard-wrapper">
    <h1 class="main-title">Bearbeiten: <?php echo htmlspecialchars($media['title'] ?? 'Unbekannt'); ?></h1>
    <div class="dashboard-grid">
        <?php include 'partials/_sidebar_nav.php'; ?>
        <main class="dashboard-content">
            <div class="dashboard-section">
                <form action="<?php echo $config['base_url']; ?>/admin/update/<?php echo htmlspecialchars($media_type); ?>/<?php echo htmlspecialchars($id); ?>/update" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?php echo \App\Core\Security::generateCsrfToken(); ?>">
                    
                    <?php if (!empty($message)): ?>
                        <p class="message <?php echo htmlspecialchars($message_type); ?>"><?php echo htmlspecialchars($message); ?></p>
                    <?php endif; ?>

                    <div class="form-grid">
                        <div class="form-column">
                            <h4>Basisdaten</h4>
                            <p><label>Titel</label><input type="text" name="title" value="<?php echo htmlspecialchars($media['title'] ?? ''); ?>" required></p>
                            <?php if ($media_type === 'movie'): ?>
                                <p><label>USK</label>
                                    <select name="usk" required>
                                        <option value="0" <?php if(isset($media['USK']) && $media['USK'] == 0) echo 'selected'; ?>>FSK 0</option>
                                        <option value="6" <?php if(isset($media['USK']) && $media['USK'] == 6) echo 'selected'; ?>>FSK 6</option>
                                        <option value="12" <?php if(isset($media['USK']) && $media['USK'] == 12) echo 'selected'; ?>>FSK 12</option>
                                        <option value="16" <?php if(isset($media['USK']) && $media['USK'] == 16) echo 'selected'; ?>>FSK 16</option>
                                        <option value="18" <?php if(isset($media['USK']) && $media['USK'] == 18) echo 'selected'; ?>>FSK 18</option>
                                    </select>
                                </p>
                            <?php endif; ?>
                            <p><label>Preis <?php echo $media_type === 'series' ? 'pro Staffel' : ''; ?></label><input type="text" name="price" value="<?php echo htmlspecialchars(str_replace('.', ',', $media['price'] ?? '0')); ?>" pattern="^\d+([,.])?\d{1,2})?$" required></p>
                            <p><label>Beschreibung</label><textarea name="beschreibung" rows="6"><?php echo htmlspecialchars($media['Beschreibung'] ?? ''); ?></textarea></p>
                        </div>
                        <div class="form-column">
                            <h4>Zusatzinformationen</h4>
                            <?php if ($media_type === 'movie'): ?>
                                <p><label>Erscheinungsjahr</label><input type="number" name="erscheinungsjahr" value="<?php echo htmlspecialchars($media['Erscheinungsjahr'] ?? ''); ?>"></p>
                                <p><label>Laufzeit (in Minuten)</label><input type="number" name="laufzeit" value="<?php echo htmlspecialchars($media['Laufzeit'] ?? ''); ?>"></p>
                                <p><label>Regisseur</label><input type="text" name="regisseur" value="<?php echo htmlspecialchars($media['Regisseur'] ?? ''); ?>"></p>
                            <?php else: // Serie ?>
                                <p><label>Startjahr</label><input type="number" name="start_year" value="<?php echo htmlspecialchars($media['Erscheinungsjahr'] ?? ''); ?>"></p>
                                <p><label>Endjahr</label><input type="number" name="end_year" value="<?php echo htmlspecialchars($media['Endjahr'] ?? ''); ?>"></p>
                                <p><label>Staffeln</label><input type="number" name="seasons" value="<?php echo htmlspecialchars($media['Staffeln'] ?? ''); ?>"></p>
                                <p><label>Schöpfer</label><input type="text" name="creator" value="<?php echo htmlspecialchars($media['Creator'] ?? ''); ?>"></p>
                            <?php endif; ?>
                            <p><label>Genre</label><input type="text" name="genre" value="<?php echo htmlspecialchars($media['Genre'] ?? ''); ?>"></p>
                        </div>
                    </div>
                    
                    <hr style="margin: 30px 0; border-color: var(--color-border);">
                    <h4>Poster Management</h4>
                    <div class="poster-management-grid">
                        <div class="poster-upload-area">
                            <p style="margin-top:0; font-weight:600;">Aktuelles Poster:</p>
                            <div class="current-poster-container">
                                <img src="<?php echo $config['base_url'] . '/' . htmlspecialchars($media['PosterPath'] ?? ''); ?>" alt="Aktuelles Poster" class="current-poster-preview">
                            </div>
                            <p style="margin-top: 20px;"><label for="posterUpload"><strong>Neues Poster hochladen:</strong></label></p>
                            <div class="file-upload-wrapper">
                                <input type="file" name="poster" id="posterUpload" class="file-upload-input" accept="image/*">
                                <label for="posterUpload" class="btn btn-secondary file-upload-label">Datei auswählen...</label>
                                <span class="file-upload-filename">Keine Datei ausgewählt</span>
                            </div>
                        </div>
                        <div class="ai-generator-area">
                            <div class="ai-generator-box">
                                <div class="ai-generator-box-inner">
                                    <h4>KI-Poster-Generator</h4>
                                    <p>Kein Poster zur Hand? Generieren Sie hier eine Idee.</p>
                                    <label for="ai-prompt">Prompt für die Bilderzeugung:</label>
                                    <textarea id="ai-prompt" rows="4"><?php
                                        $prompt_type = ($media_type === 'series') ? 'für die Serie' : 'für den Film';
                                        $main_genre = !empty($media['Genre']) ? explode(',', $media['Genre'])[0] : '';
                                        $prompt = 'Ein dramatisches, hochwertiges Filmplakat ' . $prompt_type . ' \'' . htmlspecialchars($media['title'] ?? '') . '\'. ';
                                        if (!empty($main_genre)) { $prompt .= 'Genre: ' . htmlspecialchars($main_genre) . '. '; }
                                        $prompt .= 'Stil: filmisch, episch, hochauflösend, meisterwerk.';
                                        echo $prompt;
                                    ?></textarea>
                                    <button type="button" class="btn btn-small" id="copy-prompt-btn">Prompt kopieren</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions" style="margin-top: 40px;">
                        <input type="submit" value="Änderungen speichern" class="btn btn-warning">
                        <a href="<?php echo $config['base_url']; ?>/admin/media_overview?type=<?php echo ($media_type === 'movie' ? 'movies' : 'series'); ?>" class="btn btn-secondary">Abbrechen</a>
                    </div>
                </form>
            </div>
        </main>
    </div>
</div>

<?php include_once dirname(__DIR__, 2) . '/templates/footer.php'; ?>
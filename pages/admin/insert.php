<?php
// Dies ist die "View" für den MediaController::create()
require_once dirname(__DIR__, 2) .'/templates/header.php';
?>

<h1 class="main-title">Admin Dashboard</h1>
<div class="dashboard-grid">
    <?php include 'partials/_sidebar_nav.php'; ?>
    <main class="dashboard-content">
        <div class="dashboard-section">
            <h2>Neuen Inhalt hinzufügen</h2>

            <?php if (!empty($message)): ?>
                <div class="message <?php echo htmlspecialchars($message_type); ?>"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>

            <form action="<?php echo $config['base_url']; ?>/admin/insert/store" method="post" enctype="multipart/form-data" class="modern-form">
                <input type="hidden" name="csrf_token" value="<?php echo \App\Core\Security::generateCsrfToken(); ?>">
                
                <div class="form-group" style="background-color: #f1f5f9; padding: 25px; border-radius: 8px; margin-bottom: 30px; border: 1px solid #e2e8f0;">
                    <label for="media-type-select" style="font-weight: 700; font-size: 1.2rem; color: #334155; display: block; margin-bottom: 10px;">Schritt 1: Was möchten Sie hinzufügen?</label>
                    <select name="media_type" id="media-type-select" class="form-control" style="max-width: 300px;">
                        <option value="movie" selected>Film</option>
                        <option value="series">Serie</option>
                    </select>
                </div>

                <div class="form-grid">
                    <div class="form-column">
                        <h4>Basisdaten</h4>
                        <p><label for="title-input">Titel*</label><input type="text" id="title-input" name="title" required></p>
                        <p><label for="genre-input">Genre</label><input type="text" id="genre-input" name="genre" placeholder="z.B. Action, Komödie"></p>
                        <p><label for="beschreibung-text">Beschreibung</label><textarea id="beschreibung-text" name="beschreibung" rows="8"></textarea></p>
                    </div>
                    <div class="form-column">
                        <h4>Details & Preis</h4>
                        
                        <div class="media-fields" data-type="movie">
                            <p><label for="erscheinungsjahr-input">Erscheinungsjahr</label><input type="number" id="erscheinungsjahr-input" name="erscheinungsjahr" min="1888" max="<?php echo date('Y'); ?>"></p>
                            <p><label for="laufzeit-input">Laufzeit (Minuten)</label><input type="number" id="laufzeit-input" name="laufzeit"></p>
                            <p><label for="regisseur-input">Regisseur</label><input type="text" id="regisseur-input" name="regisseur"></p>
                            <p><label for="usk-select">FSK*</label><input type="number" id="usk-select" name="usk" min="0" max="18" step="1" required></p>
                        </div>
                        
                        <div class="media-fields" data-type="series" style="display: none;">
                            <p><label for="start_year-input">Startjahr</label><input type="number" id="start_year-input" name="start_year" min="1940" max="<?php echo date('Y'); ?>"></p>
                            <p><label for="end_year-input">Endjahr</label><input type="number" id="end_year-input" name="end_year" min="1940" max="<?php echo date('Y'); ?>"></p>
                            <p><label for="seasons-input">Anzahl Staffeln</label><input type="number" id="seasons-input" name="seasons"></p>
                            <p><label for="creator-input">Schöpfer</label><input type="text" id="creator-input" name="creator"></p>
                        </div>

                        <p><label for="price-input" id="price-label">Preis*</label><input type="text" id="price-input" name="price" pattern="[0-9]+([,.][0-9]{1,2})?" required></p>
                        
                        <div style="margin-top: 1rem;">
                            <label>Poster</label>
                            <div class="file-upload-wrapper">
                                <input type="file" name="poster" id="posterUpload" class="file-upload-input" accept="image/*">
                                <label for="posterUpload" class="btn btn-secondary file-upload-label">Datei auswählen...</label>
                                <span class="file-upload-filename">Keine Datei ausgewählt</span>
                            </div>
                        </div>
                    </div>
                </div>

                <hr style="margin: 30px 0; border-color: var(--color-border);">
                <div class="ai-generator-area">
                    <h4>Poster-Hilfe</h4>
                    <div class="ai-generator-box" style="margin-top: 15px;">
                        <div class="ai-generator-box-inner">
                            <h4>KI-Poster-Generator</h4>
                            <p>Generiert eine Prompt-Idee basierend auf Ihren Eingaben oben.</p>
                            <label for="ai-prompt">Generierter Prompt:</label>
                            <textarea id="ai-prompt" rows="4" readonly style="background-color: #f8f9fa; cursor: not-allowed;">Bitte geben Sie einen Titel und ein Genre ein...</textarea>
                            <button type="button" class="btn btn-small" id="copy-prompt-btn" disabled>Prompt kopieren</button>
                        </div>
                    </div>
                </div>

                <div class="form-actions" style="margin-top: 30px;">
                    <input type="submit" value="Eintrag hinzufügen" class="btn btn-primary">
                </div>
            </form>
        </div>
    </main>
</div>

<?php
include dirname(__DIR__, 2) .'/templates/footer.php';
?>
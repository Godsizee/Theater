<div class="profile-data-section">
    <div class="section-header">
        <h2>Passwort</h2>
        <button class="edit-icon" aria-label="Bearbeiten">&#9998;</button>
    </div>
    <div class="data-display-view">
        <div class="data-row">
            <span class="data-label">Passwort</span>
            <span class="data-value">********</span>
        </div>
    </div>
    <div class="data-edit-view">
        <form class="profile-edit-form" method="post">
            <input type="hidden" name="form_type" value="update_password">
            <input type="hidden" name="csrf_token" value="<?php echo \App\Core\Security::generateCsrfToken(); ?>">
            <p><label>Neues Passwort</label><input type="password" name="new_password" required minlength="8"></p>
            <p><label>Neues Passwort bestätigen</label><input type="password" name="confirm_password" required minlength="8"></p>
            <div class="form-actions"><button type="submit" class="btn btn-primary">Speichern</button></div>
        </form>
    </div>
</div>
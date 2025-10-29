<div class="profile-data-section">
    <div class="section-header">
        <h2>E-Mail-Adresse</h2>
        <button class="edit-icon" aria-label="Bearbeiten">&#9998;</button>
    </div>
    <div class="data-display-view">
        <div class="data-row">
            <span class="data-label">E-Mail</span>
            <span class="data-value"><?php echo htmlspecialchars($user['EMail'] ?? ''); ?></span>
        </div>
    </div>
    <div class="data-edit-view">
        <form class="profile-edit-form" method="post">
            <input type="hidden" name="form_type" value="update_email">
            <input type="hidden" name="csrf_token" value="<?php echo \App\Core\Security::generateCsrfToken(); ?>">
            <p><label>Neue E-Mail-Adresse</label><input type="email" name="email" value="<?php echo htmlspecialchars($user['EMail'] ?? ''); ?>" required></p>
            <div class="form-actions"><button type="submit" class="btn btn-primary">Speichern</button></div>
        </form>
    </div>
</div>
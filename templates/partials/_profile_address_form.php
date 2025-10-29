<div class="profile-data-section">
    <div class="section-header">
        <h2>Persönliche Angaben</h2>
        <button class="edit-icon" aria-label="Bearbeiten">&#9998;</button>
    </div>
    <div class="data-display-view">
        <div class="data-row">
            <span class="data-label">Name</span>
            <span class="data-value"><?php echo htmlspecialchars(($user['Vorname'] ?? '') . ' ' . ($user['Nachname'] ?? '')); ?></span>
        </div>
        <div class="data-row">
            <span class="data-label">Anschrift</span>
            <span class="data-value"><?php echo htmlspecialchars(($user['Strasse'] ?? '') . ' ' . ($user['Hausnummer'] ?? '')); ?><br><?php echo htmlspecialchars(($user['PLZ'] ?? '') . ' ' . ($user['Ort'] ?? '')); ?></span>
        </div>
        <div class="data-row">
            <span class="data-label">Telefon</span>
            <span class="data-value"><?php echo htmlspecialchars($user['Telefon'] ?? 'Nicht angegeben'); ?></span>
        </div>
        <div class="data-row">
            <span class="data-label">Geburtsdatum</span>
            <span class="data-value"><?php echo !empty($user['Birthday']) ? htmlspecialchars(date('d.m.Y', strtotime($user['Birthday']))) : 'Nicht angegeben'; ?></span>
        </div>
    </div>
    <div class="data-edit-view">
        <form class="profile-edit-form" method="post">
            <input type="hidden" name="form_type" value="update_address">
            <input type="hidden" name="csrf_token" value="<?php echo \App\Core\Security::generateCsrfToken(); ?>">

            <p>
                <label>Vorname</label>
                <?php if (isset($user['VornameChanged']) && $user['VornameChanged']): ?>
                    <span class="form-static-value"><?php echo htmlspecialchars($user['Vorname'] ?? ''); ?></span>
                    <small class="form-hint">Dieses Feld kann nur einmalig geändert werden.</small>
                <?php else: ?>
                    <input type="text" name="vorname" value="<?php echo htmlspecialchars($user['Vorname'] ?? ''); ?>">
                    <small class="form-hint">Dieses Feld kann nur einmalig festgelegt oder geändert werden.</small>
                <?php endif; ?>
            </p>

            <p><label>Nachname</label><input type="text" name="nachname" value="<?php echo htmlspecialchars($user['Nachname'] ?? ''); ?>"></p>
            <p><label>Straße</label><input type="text" name="strasse" value="<?php echo htmlspecialchars($user['Strasse'] ?? ''); ?>"></p>
            <p><label>Hausnummer</label><input type="text" name="hausnummer" value="<?php echo htmlspecialchars($user['Hausnummer'] ?? ''); ?>"></p>
            <p><label>Postleitzahl</label><input type="text" name="plz" value="<?php echo htmlspecialchars($user['PLZ'] ?? ''); ?>"></p>
            <p><label>Ort</label><input type="text" name="ort" value="<?php echo htmlspecialchars($user['Ort'] ?? ''); ?>"></p>
            <p><label>Telefon</label><input type="tel" name="telefon" value="<?php echo htmlspecialchars($user['Telefon'] ?? ''); ?>"></p>

            <p>
                <label>Geburtstag</label>
                <?php if (isset($user['BirthdayChanged']) && $user['BirthdayChanged']): ?>
                    <span class="form-static-value"><?php echo !empty($user['Birthday']) ? htmlspecialchars(date('d.m.Y', strtotime($user['Birthday']))) : 'Nicht angegeben'; ?></span>
                    <small class="form-hint">Dieses Feld kann nur einmalig geändert werden.</small>
                <?php else: ?>
                    <input type="date" name="birthday" value="<?php echo htmlspecialchars($user['Birthday'] ?? ''); ?>">
                    <small class="form-hint">Dieses Feld kann nur einmalig festgelegt oder geändert werden.</small>
                <?php endif; ?>
            </p>

            <div class="form-actions"><button type="submit" class="btn btn-primary">Speichern</button></div>
        </form>
    </div>
</div>
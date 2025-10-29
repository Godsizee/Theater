<?php
// Dies ist jetzt die "View" für den UserController::create()
// Alle Variablen ($page_title, $body_class, $valid_roles etc.) werden vom Controller bereitgestellt.
require_once dirname(__DIR__, 2) .'/templates/header.php';
?>

<h1 class="main-title">Neuen Benutzer anlegen</h1>
<div class="dashboard-grid">
    <?php include 'partials/_sidebar_nav.php'; ?>
    <main class="dashboard-content">
        <div class="dashboard-section">
            <h3>Benutzerdaten eingeben</h3>
            
            <?php if (!empty($message)): ?>
                <p class="message <?php echo htmlspecialchars($message_type); ?>"><?php echo htmlspecialchars($message); ?></p>
            <?php endif; ?>
            
            <form action="<?php echo $config['base_url']; ?>/admin/create_user/store" method="post">
                <input type="hidden" name="csrf_token" value="<?php echo \App\Core\Security::generateCsrfToken(); ?>">

                <p>
                    Benutzername*
                    <input type="text" name="username" value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" required>
                </p>
                <p>
                    E-Mail-Adresse*
                    <input type="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                </p>
                <p>
                    Rolle*
                    <select name="role" required>
                        <?php foreach ($valid_roles as $role): ?>
                            <option value="<?php echo htmlspecialchars($role); ?>" <?php echo (($_POST['role'] ?? '') === $role) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars(ucfirst(str_replace('-', ' ', $role))); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </p>
                <p>
                    Passwort* (mind. 8 Zeichen)
                    <input type="password" name="password" minlength="8" required>
                </p>
                <p>
                    Passwort bestätigen*
                    <input type="password" name="confirm_password" minlength="8" required>
                </p>
                <hr style="margin: 20px 0;">
                <h4>Optionale Informationen</h4>
                 <p>
                    Vorname
                    <input type="text" name="vorname" value="<?php echo htmlspecialchars($_POST['vorname'] ?? ''); ?>">
                </p>
                 <p>
                    Nachname
                    <input type="text" name="nachname" value="<?php echo htmlspecialchars($_POST['nachname'] ?? ''); ?>">
                </p>
                <p>
                    Geburtsdatum
                    <input type="date" name="birthday" value="<?php echo htmlspecialchars($_POST['birthday'] ?? ''); ?>">
                </p>

                <div class="form-actions">
                    <input type="submit" value="Benutzer erstellen" class="btn btn-success">
                    <a href="<?php echo $config['base_url']; ?>/admin/users" class="btn btn-secondary">Abbrechen</a>
                </div>
            </form>
        </div>
    </main>
</div>

<?php
include_once dirname(__DIR__, 2) .'/templates/footer.php';
?>
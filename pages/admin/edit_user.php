<?php
// Dies ist die "View" für den UserController::edit()
require_once dirname(__DIR__, 2) . '/templates/header.php';
?>

<h1 class="main-title">Benutzer bearbeiten</h1>
<div class="dashboard-grid">
    <?php include 'partials/_sidebar_nav.php'; ?>
    <main class="dashboard-content">
        <div class="dashboard-section">
            <h3>Benutzerdaten für "<?php echo htmlspecialchars($user['Username']); ?>"</h3>
            
            <?php if (!empty($message)): ?>
                <p class="message <?php echo htmlspecialchars($message_type); ?>"><?php echo htmlspecialchars($message); ?></p>
            <?php endif; ?>

            <form action="<?php echo $config['base_url']; ?>/admin/edit_user/<?php echo htmlspecialchars($user['UserId']); ?>/update" method="post">
                <input type="hidden" name="csrf_token" value="<?php echo \App\Core\Security::generateCsrfToken(); ?>">

                <p>
                    Benutzername*
                    <input type="text" name="username" value="<?php echo htmlspecialchars($user['Username']); ?>" required>
                </p>
                <p>
                    E-Mail-Adresse*
                    <input type="email" name="email" value="<?php echo htmlspecialchars($user['EMail']); ?>" required>
                </p>
                <p>
                    Rolle*
                    <select name="role" required>
                        <?php foreach ($valid_roles as $role): ?>
                            <option value="<?php echo htmlspecialchars($role); ?>" <?php echo ($user['Rolle'] === $role) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars(ucfirst(str_replace('-', ' ', $role))); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </p>
                <p>
                    Geburtsdatum
                    <input type="date" name="birthday" value="<?php echo htmlspecialchars($user['Birthday']); ?>">
                </p>
                <hr style="margin: 20px 0;">
                <p>
                    Neues Passwort
                    <input type="password" name="password" minlength="8" placeholder="Leer lassen, um nicht zu ändern">
                    <small class="form-hint">Geben Sie hier nur ein neues Passwort ein, wenn Sie es ändern möchten.</small>
                </p>

                <div class="form-actions">
                    <input type="submit" value="Änderungen speichern" class="btn btn-warning">
                    <a href="<?php echo $config['base_url']; ?>/admin/users" class="btn btn-secondary">Zurück zur Übersicht</a>
                </div>
            </form>
        </div>
    </main>
</div>

<?php
include_once dirname(__DIR__, 2) . '/templates/footer.php';
?>
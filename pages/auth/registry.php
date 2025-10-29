<?php
// Die gesamte Logik wurde in den AuthController verschoben.
// Die Variablen $page_title, $message und $message_type werden vom Controller bereitgestellt.

$is_ajax = isset($is_ajax_request);

if (!$is_ajax) {
    include dirname(__DIR__, 2) . '/templates/header.php';
} else {
    echo "<script>document.title = '" . addslashes($page_title) . "'; document.body.className = '';</script>";
}
?>

<div class="container" style="margin-top: 100px;">
    <h1 class="main-title text-center">Neues Konto erstellen</h1>
    <?php if (!empty($message)): ?>
        <p class="message <?php echo htmlspecialchars($message_type); ?>"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>
    
    <!-- GEÄNDERT: Die action des Formulars zeigt auf die neue Verarbeitungs-Route -->
    <form action="<?php echo \App\Core\Utils::url('registry/process'); ?>" method="post" id="registry-form">
        <p>
            <label for="username-input">Benutzername*</label>
            <input type="text" id="username-input" name="username" required value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
            <span class="validation-status"></span>
        </p>
        <p>
            <label for="email-input">E-Mail-Adresse*</label>
            <input type="email" id="email-input" name="email" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
            <span class="validation-status"></span>
        </p>
        <p>
            <label for="password">Passwort* (mind. 8 Zeichen)</label>
            <input type="password" id="password" name="password" required minlength="8">
        </p>
        <p>
            <label for="confirm_password">Passwort bestätigen*</label>
            <input type="password" id="confirm_password" name="confirm_password" required minlength="8">
        </p>
        <hr style="margin: 20px 0;">
        <p>
            <label for="vorname">Vorname</label>
            <input type="text" id="vorname" name="vorname" value="<?php echo htmlspecialchars($_POST['vorname'] ?? ''); ?>">
        </p>
        <p>
            <label for="nachname">Nachname</label>
            <input type="text" id="nachname" name="nachname" value="<?php echo htmlspecialchars($_POST['nachname'] ?? ''); ?>">
        </p>
        <p>
            <input type="submit" value="Konto erstellen" class="btn btn-primary" style="width: 100%;">
        </p>
    </form>
    <p class="text-center" style="margin-top: 20px;">
        Bereits ein Konto? <a href="<?php echo \App\Core\Utils::url('login'); ?>" data-spa-link>Zum Login</a>
    </p>
</div>

<?php
if (!$is_ajax) {
    include dirname(__DIR__, 2) . '/templates/footer.php';
}
?>

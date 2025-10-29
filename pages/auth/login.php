<?php
// Die gesamte Logik wurde in den AuthController verschoben.

$is_ajax = isset($is_ajax_request);

if (!$is_ajax) {
    include dirname(__DIR__, 2) . '/templates/header.php';
} else {
    echo "<script>document.title = '" . addslashes($page_title) . "'; document.body.className = '';</script>";
}
?>

<div class="container" style="margin-top: 100px;">
    <h1 class="main-title text-center">Login</h1>
    <?php if (!empty($message)): ?>
        <p class="message error"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>
    <form action="<?php echo \App\Core\Utils::url('login/process'); ?>" method="post">
        <p>
            <label for="identifier">Benutzername oder E-Mail</label>
            <input type="text" id="identifier" name="identifier" required>
        </p>
        <p>
            <label for="password">Passwort</label>
            <input type="password" id="password" name="password" required>
        </p>
        <p>
            <input type="submit" value="Anmelden" class="btn btn-primary" style="width: 100%;">
        </p>
    </form>
    <p class="text-center" style="margin-top: 20px;">
        Noch kein Konto? <a href="<?php echo \App\Core\Utils::url('registry'); ?>" data-spa-link>Jetzt registrieren</a>
    </p>
</div>

<?php
if (!$is_ajax) {
    include dirname(__DIR__, 2) . '/templates/footer.php';
}
?>
<?php
// Die gesamte PHP-Logik wurde in den ProfileController verschoben.

$is_ajax = isset($is_ajax_request);

if (!$is_ajax) {
    include dirname(__DIR__, 2) . '/templates/header.php';
} else {
    echo "<script>document.title = '" . addslashes($page_title) . "'; document.body.className = '$body_class';</script>";
}
?>

<div class="profile-layout">
    <aside class="profile-sidebar">
        <?php include dirname(__DIR__, 2) . '/templates/partials/_profil_nav.php'; ?>
    </aside>
    <main class="profile-content">
        <div class="profile-content-header">
            <h1>Meine Daten</h1>
            <p>Hier können Sie Ihre persönlichen Daten, Ihre E-Mail-Adresse und Ihr Passwort verwalten.</p>
        </div>
        
        <?php include dirname(__DIR__, 2) . '/templates/partials/_profile_address_form.php'; ?>
        <?php include dirname(__DIR__, 2) . '/templates/partials/_profile_email_form.php'; ?>
        <?php include dirname(__DIR__, 2) . '/templates/partials/_profile_password_form.php'; ?>

    </main>
</div>

<?php
if (!$is_ajax) {
    include dirname(__DIR__, 2) . '/templates/footer.php';
}
?>
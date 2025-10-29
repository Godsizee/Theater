<?php
// Die gesamte Logik wurde in ProfileController->showOrderDetails() verschoben.

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
        <?php if (!$orderViewModel): ?>
            <div class="message error">Die angeforderte Bestellung konnte nicht gefunden werden oder Sie haben keine Berechtigung, sie anzuzeigen.</div>
        <?php else: ?>
            <?php 
                $order = $orderViewModel;
                include dirname(__DIR__, 2) . '/templates/partials/_order_details_content.php'; 
            ?>
        <?php endif; ?>
    </main>
</div>

<?php
if (!$is_ajax) {
    include dirname(__DIR__, 2) . '/templates/footer.php';
}
?>
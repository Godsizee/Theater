<?php
// Die gesamte PHP-Logik wurde in den ProfileController->showOrders() verschoben.

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
            <h1>Meine Bestellungen</h1>
        </div>
        <div class="order-list-container">
            <?php if (empty($orderViewModels)): ?>
                <div class="message info">Sie haben bisher noch keine Filme ausgeliehen.</div>
            <?php else: ?>
                <?php foreach ($orderViewModels as $order): ?>
                    <?php include dirname(__DIR__, 2) . '/templates/partials/_order_list_item.php'; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>
</div>

<?php
if (!$is_ajax) {
    include dirname(__DIR__, 2) . '/templates/footer.php';
}
?>
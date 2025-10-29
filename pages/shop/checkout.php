<?php
// Die Logik befindet sich jetzt im ShopController@showCheckout.
// Die Variablen $page_title, $body_class und $user_data werden vom Controller bereitgestellt.

$is_ajax = isset($is_ajax_request);
if (!$is_ajax) {
    include dirname(__DIR__, 2) . '/templates/header.php';
} else {
    echo "<script>document.title = '".addslashes($page_title)."'; document.body.className = '$body_class';</script>";
}
?>

<script>
    // Stelle die Benutzerdaten für das Checkout-Skript global zur Verfügung
    window.CHECKOUT_USER_DATA = <?php echo json_encode($user_data); ?>;
</script>

<div class="checkout-header">
    <h1>Überprüfen & Bestellen</h1>
    <p>Herzlich willkommen, <?php echo htmlspecialchars(($user_data['Vorname'] ?? '') . ' ' . ($user_data['Nachname'] ?? '')); ?>!</p>
</div>

<div id="checkout-container">
    <p>Bestellübersicht wird geladen...</p>
</div>

<?php
if (!$is_ajax) {
    include dirname(__DIR__, 2) . '/templates/footer.php';
}
?>
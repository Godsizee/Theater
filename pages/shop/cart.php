<?php
// Die Logik befindet sich jetzt im ShopController@showCart.
// Die Variablen $page_title und $body_class werden vom Controller bereitgestellt.

$is_ajax = isset($is_ajax_request);
if (!$is_ajax) {
    include dirname(__DIR__, 2) . '/templates/header.php';
} else {
    echo "<script>document.title = '".addslashes($page_title)."'; document.body.className = '$body_class';</script>";
}
?>

<div class="profile-content-header">
    <h1>Warenkorb</h1>
</div>

<div id="cart-container">
    <p>Warenkorb wird geladen...</p>
</div>

<?php
if (!$is_ajax) {
    include dirname(__DIR__, 2) . '/templates/footer.php';
}
?>
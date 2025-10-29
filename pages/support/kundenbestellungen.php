<?php
// Logik befindet sich jetzt in SupportController@showCustomerOrders
// Variablen werden vom Controller bereitgestellt.
include_once dirname(__DIR__, 2) .'/templates/header.php';
?>

<h1 class="main-title">Kundencenter</h1>
<div class="dashboard-grid">
    <?php include 'partials/_sidebar.php'; ?>
    <main class="dashboard-content">
        <?php include 'partials/_kunden_nav.php'; ?>
        <div class="dashboard-section">
            <h3>Bestellungen von <?php echo htmlspecialchars($customer['Vorname'] . ' ' . $customer['Nachname']); ?></h3>
             <div class="order-list-container">
                <?php if (empty($orderViewModels)): ?>
                    <div class="message info">Dieser Kunde hat bisher noch keine Bestellungen getätigt.</div>
                <?php else: ?>
                    <?php foreach ($orderViewModels as $order): ?>
                        <?php include dirname(__DIR__, 2) . '/templates/partials/_order_list_item.php'; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>

<?php include_once dirname(__DIR__, 2) .'/templates/footer.php'; ?>
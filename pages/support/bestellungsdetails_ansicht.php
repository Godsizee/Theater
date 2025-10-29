<?php
// Logik befindet sich jetzt in SupportController@showOrderDetails
// Variablen werden vom Controller bereitgestellt.
include_once dirname(__DIR__, 2) .'/templates/header.php';
?>

<div class="page-wrapper admin-dashboard-wrapper">
    <h1 class="main-title">Kundencenter</h1>
    <div class="dashboard-grid">
        <?php include 'partials/_sidebar.php'; ?>
        <main class="dashboard-content">
            <?php include 'partials/_kunden_nav.php'; ?>
            <div class="dashboard-section" style="margin-top: 20px;">
                <?php
                    // Das ViewModel-Objekt für das Partial vorbereiten
                    $order = $orderViewModel; 
                    include dirname(__DIR__, 2) . '/templates/partials/_order_details_content.php'; 
                ?>
            </div>
        </main>
    </div>
</div>

<?php include dirname(__DIR__, 2) . '/templates/footer.php'; ?>
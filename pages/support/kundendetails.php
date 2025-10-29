<?php
// Logik befindet sich jetzt in SupportController@showCustomerDetails
// Variablen werden vom Controller bereitgestellt.
include_once dirname(__DIR__, 2) .'/templates/header.php';
?>

<h1 class="main-title">Kundencenter</h1>
<div class="dashboard-grid">
    <?php include 'partials/_sidebar.php'; ?>
    <main class="dashboard-content">
        <?php include 'partials/_kunden_nav.php'; ?>
        <div class="dashboard-section customer-details-container">
            <h3>Kundenübersicht</h3>
            <div class="profile-data-section">
                <div class="data-display-view">
                    <div class="data-row"><span class="data-label">Kunden-Nr.:</span><span class="data-value"><?php echo htmlspecialchars($customer['KundeId']); ?></span></div>
                    <div class="data-row"><span class="data-label">Name:</span><span class="data-value"><?php echo htmlspecialchars($customer['Vorname'] . ' ' . $customer['Nachname']); ?></span></div>
                    <div class="data-row"><span class="data-label">E-Mail:</span><span class="data-value"><?php echo htmlspecialchars($customer['EMail']); ?></span></div>
                    <div class="data-row"><span class="data-label">Telefon:</span><span class="data-value"><?php echo htmlspecialchars($customer['Telefon'] ?? '---'); ?></span></div>
                    <div class="data-row"><span class="data-label">Anschrift:</span><span class="data-value"><?php echo htmlspecialchars($customer['Strasse'] . ' ' . $customer['Hausnummer']); ?><br><?php echo htmlspecialchars($customer['PLZ'] . ' ' . $customer['Ort']); ?></span></div>
                    <div class="data-row"><span class="data-label">Geburtstag:</span><span class="data-value"><?php echo !empty($customer['Birthday']) ? htmlspecialchars(date('d.m.Y', strtotime($customer['Birthday']))) : '---'; ?></span></div>
                    <div class="data-row"><span class="data-label">Benutzerrolle:</span><span class="data-value"><?php echo htmlspecialchars(ucfirst($customer['Rolle'])); ?></span></div>
                </div>
            </div>
        </div>
    </main>
</div>

<?php include_once dirname(__DIR__, 2) .'/templates/footer.php'; ?>
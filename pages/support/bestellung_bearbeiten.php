<?php
// Logik befindet sich jetzt in SupportController@editOrder und updateOrder
// Variablen werden vom Controller bereitgestellt.
include_once dirname(__DIR__, 2) . '/templates/header.php';
?>

<h1 class="main-title">Kundencenter</h1>
<div class="dashboard-grid">
    <?php include 'partials/_sidebar.php'; ?>
    <main class="dashboard-content">
        <?php include 'partials/_kunden_nav.php'; ?>
        <div class="dashboard-section" style="margin-top: 20px;">
            <h3>Bestellung #<?php echo htmlspecialchars($order['TicketId']); ?> bearbeiten</h3>
            <p><strong>Film:</strong> <?php echo htmlspecialchars($order['Titel']); ?></p>

            <?php if ($message): ?>
                <p class="message <?php echo htmlspecialchars($message_type); ?>"><?php echo htmlspecialchars($message); ?></p>
            <?php endif; ?>

            <form action="<?php echo $config['base_url']; ?>/support/bestellung_bearbeiten/<?php echo $ticket_id; ?>/update" method="post">
                <input type="hidden" name="csrf_token" value="<?php echo \App\Core\Security::generateCsrfToken(); ?>">

                <p>
                    <label for="end_date">Gültig bis (End-Datum):</label>
                    <input type="datetime-local" id="end_date" name="end_date" class="form-control" 
                           value="<?php echo date('Y-m-d\TH:i:s', strtotime($order['EndDatum'])); ?>">
                </p>

                <p>
                    <label for="status">Zahlungsstatus:</label>
                    <select name="status" id="status" class="form-control">
                        <option value="Offen" <?php if($order['Zahlungsstatus'] === 'Offen') echo 'selected'; ?>>Offen</option>
                        <option value="Beglichen" <?php if($order['Zahlungsstatus'] === 'Beglichen') echo 'selected'; ?>>Beglichen</option>
                        <option value="Storniert" <?php if($order['Zahlungsstatus'] === 'Storniert') echo 'selected'; ?>>Storniert</option>
                    </select>
                </p>

                <div class="form-actions" style="margin-top: 20px;">
                    <input type="submit" value="Änderungen speichern" class="btn btn-warning">
                    <a href="<?php echo $config['base_url']; ?>/support/kundenbestellungen/<?php echo $customer['UserId']; ?>" class="btn btn-secondary">Abbrechen</a>
                </div>
            </form>
        </div>
    </main>
</div>

<?php include_once dirname(__DIR__, 2) .'/templates/footer.php'; ?>
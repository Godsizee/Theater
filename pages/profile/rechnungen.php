<?php
// Die gesamte Logik wurde in den ProfileController->showInvoices() verschoben.

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
            <h1>Meine Rechnungen</h1>
        </div>
        <div class="invoice-summary">
            <div class="summary-item"><span class="summary-label">Offener Gesamtbetrag:</span><span class="summary-value"><?php echo number_format($open_total, 2, ',', '.'); ?> €</span></div>
            <div class="summary-item"><span class="summary-label">Sofort zu zahlen:</span><span class="summary-value"><?php echo number_format($open_total, 2, ',', '.'); ?> €</span></div>
        </div>
        <div class="collapsible-section">
            <button class="collapsible-header" aria-expanded="true">
                <span>Offene Rechnungen</span><span class="collapsible-chevron">&circ;</span>
            </button>
            <div class="collapsible-content">
                <?php if (empty($open_invoices)): ?>
                    <div class="message info" style="text-align: left; margin-left: 0;">Keine offenen Zahlungen</div>
                <?php else: ?>
                    <?php foreach($open_invoices as $invoice): ?>
                        <a href="<?php echo $config['base_url']; ?>/rechnung_pdf.php?id=<?php echo $invoice['TicketId']; ?>" class="invoice-row" target="_blank">
                            <span class="invoice-date">Rechnung vom <?php echo date('d.m.Y', strtotime($invoice['Bestelldatum'])); ?></span>
                            <span class="invoice-amount"><?php echo number_format($invoice['Total'] ?? 0.00, 2, ',', '.'); ?> €</span>
                            <span class="invoice-chevron">&rsaquo;</span>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        <div class="collapsible-section">
            <button class="collapsible-header" aria-expanded="true">
                <span>Beglichene Rechnungen</span><span class="collapsible-chevron">&circ;</span>
            </button>
            <div class="collapsible-content">
                <?php if (empty($paid_invoices)): ?>
                    <div class="message info" style="text-align: left; margin-left: 0;">Keine beglichenen Rechnungen</div>
                <?php else: ?>
                    <?php foreach($paid_invoices as $invoice): ?>
                         <a href="<?php echo $config['base_url']; ?>/rechnung_pdf.php?id=<?php echo $invoice['TicketId']; ?>" class="invoice-row" target="_blank">
                            <span class="invoice-date">Rechnung vom <?php echo date('d.m.Y', strtotime($invoice['Bestelldatum'])); ?></span>
                            <span class="invoice-amount"><?php echo number_format($invoice['Total'] ?? 0.00, 2, ',', '.'); ?> €</span>
                            <span class="invoice-chevron">&rsaquo;</span>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>

<?php
if (!$is_ajax) {
    include dirname(__DIR__, 2) . '/templates/footer.php';
}
?>
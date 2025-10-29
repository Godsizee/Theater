<?php
/**
 * @var \App\ViewModels\OrderListItemViewModel $order Das ViewModel-Objekt, das vom Controller übergeben wird.
 */
?>
<<?php echo $order->isUserContext ? 'a href="' . htmlspecialchars($order->detailUrl) . '"' : 'div'; ?> 
    class="order-list-row <?php echo !$order->isUserContext ? 'support-order-row' : ''; ?>" 
    <?php if ($order->isUserContext) echo 'data-spa-link'; ?>
    id="<?php echo htmlspecialchars($order->rowId); ?>">

    <div class="order-list-main">
        <div class="order-list-date"><?php echo htmlspecialchars($order->bestellDatum); ?></div>
        <div class="order-list-previews">
            <img src="<?php echo htmlspecialchars($order->posterPath); ?>" alt="Poster von <?php echo htmlspecialchars($order->movieName); ?>">
        </div>
        <div class="order-list-status">
            <strong>Status: <span class="status-text"><?php echo htmlspecialchars($order->statusText); ?></span></strong>
            <span class="status-info-text"><?php echo htmlspecialchars($order->statusInfo); ?></span>
        </div>
    </div>
    <div class="order-list-aside">
        <span class="order-list-price"><?php echo htmlspecialchars($order->total); ?> €</span>
        
        <?php if ($order->isUserContext): ?>
            <span class="order-list-chevron">&rsaquo;</span>
        <?php else: // Support-Kontext ?>
            <div class="order-row-actions">
                <!-- KORREKTUR: Der Link für "Bearbeiten" wurde auf die neue Route umgestellt -->
                <a href="<?php echo $config['base_url']; ?>/support/bestellung_bearbeiten/<?php echo htmlspecialchars($order->ticketId); ?>" class="btn btn-warning btn-small">Bearbeiten</a>
                <a href="<?php echo htmlspecialchars($order->detailUrl); ?>" class="btn btn-secondary btn-small">Details</a>
                <?php if ($order->canBeCancelled): ?>
                    <button class="btn btn-danger btn-small action-cancel-order" data-ticket-id="<?php echo htmlspecialchars($order->ticketId); ?>">Stornieren</button>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</<?php echo $order->isUserContext ? 'a' : 'div'; ?>>
<?php
/**
 * @var \App\ViewModels\OrderDetailViewModel $order Das ViewModel-Objekt.
 */
?>
<div class="order-details-grid" style="margin-top: 2rem;">
    <section class="order-detail-section">
        <h2>Bestelldetails</h2>
        <div class="detail-list">
            <div class="detail-item"><span class="data-label">Bestellnummer:</span><span class="data-value">#<?php echo htmlspecialchars($order->ticketId); ?></span></div>
            <div class="detail-item"><span class="data-label">Zahlungsstatus:</span><span class="data-value"><?php echo htmlspecialchars($order->zahlungsStatus); ?></span></div>
            <div class="detail-item total-sum" style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--color-border);"><span class="data-label">Gesamtsumme:</span><span class="data-value"><?php echo htmlspecialchars($order->price); ?> €</span></div>
            <small>inkl. MwSt.</small>
        </div>
        <a href="<?php echo htmlspecialchars($order->rechnungUrl); ?>" class="detail-link" target="_blank">Rechnung herunterladen &rsaquo;</a>
    </section>

    <section class="order-detail-section">
        <h2>Leihfrist</h2>
        <p><strong><?php echo htmlspecialchars($order->statusInfo); ?></strong></p>
        <div class="delivery-status-tracker">
            <div class="status-progress" style="width: <?php echo $order->statusProzent; ?>%;"></div>
            <div class="status-point active"><span>Bestellt</span></div>
            <div class="status-point <?php if($order->statusProzent >= 66) echo 'active'; ?>"><span>Aktiv</span></div>
            <div class="status-point <?php if($order->statusProzent >= 100) echo 'active'; ?>"><span>Abgelaufen</span></div>
        </div>
        <?php if ($order->mediaDetailUrl): ?>
            <a href="<?php echo htmlspecialchars($order->mediaDetailUrl); ?>" class="detail-link" data-spa-link>Film erneut ansehen &rsaquo;</a>
        <?php endif; ?>
    </section>
</div>

<section class="order-detail-section">
    <h2>Ausgeliehener Artikel</h2>
    <div class="order-item-list">
        <div class="product-item-row">
            <img src="<?php echo htmlspecialchars($order->posterPath); ?>" alt="Poster" class="product-item-img">
            <div class="product-item-details">
                <p class="product-title"><?php echo htmlspecialchars($order->titel); ?></p>
                <p class="product-meta">Genre: <?php echo htmlspecialchars($order->genre); ?></p>
                <p class="product-meta">Erscheinungsjahr: <?php echo htmlspecialchars($order->erscheinungsjahr); ?></p>
                <p class="product-meta">Laufzeit: <?php echo htmlspecialchars($order->laufzeit); ?> Min.</p>
            </div>
            <div class="product-item-price"><?php echo htmlspecialchars($order->price); ?> €</div>
        </div>
    </div>
</section>
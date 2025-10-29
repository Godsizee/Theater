import { getCart, removeFromCart } from './cart.js';

export function initializeCartPage() {
    const container = document.getElementById('cart-container');
    if (!container) return;

    function render() {
        const cart = getCart();
        
        if (cart.length === 0) {
            container.innerHTML = `
                <div class="empty-cart-message">
                    <h3>Ihr Warenkorb ist leer</h3>
                    <p>Fügen Sie Filme oder Serien hinzu, um sie hier zu sehen.</p>
                    <a href="${window.APP_CONFIG.baseUrl}/select" class="btn btn-primary" style="width: auto;" data-spa-link>Jetzt stöbern</a>
                </div>`;
            return;
        }

        let itemsHTML = cart.map(item => `
            <div class="cart-item" data-id="${item.id}" data-type="${item.type}">
                <img src="${item.poster}" alt="${item.title}" class="cart-item-poster">
                <div class="cart-item-details">
                    <h4>${item.title}</h4>
                    <div class="cart-item-price">${(item.price || 0).toFixed(2)} €</div>
                </div>
                <div class="cart-item-actions">
                    <button class="remove-from-cart-btn" title="Entfernen">&times;</button>
                </div>
            </div>
        `).join('');

        const subtotal = cart.reduce((sum, item) => sum + (item.price || 0), 0);
        
        container.innerHTML = `
            <div class="cart-layout">
                <div class="cart-items-list-container">
                    <h3>Ihre Auswahl</h3>
                    <div class="cart-items-list">
                        ${itemsHTML}
                    </div>
                </div>
                <div class="order-summary-card">
                    <h3>Zusammenfassung</h3>
                    <div class="summary-line">
                        <span>Zwischensumme:</span>
                        <span>${subtotal.toFixed(2)} €</span>
                    </div>
                    <div class="summary-line">
                        <small>Versandkosten werden an der Kasse berechnet.</small>
                    </div>
                    <a href="${window.APP_CONFIG.baseUrl}/checkout" class="btn btn-success" data-spa-link style="width: 100%; margin-top: 20px;">Zur Kasse</a>
                </div>
            </div>`;
    }

    container.addEventListener('click', (e) => {
        if (e.target.closest('.remove-from-cart-btn')) {
            const itemElement = e.target.closest('.cart-item');
            removeFromCart(itemElement.dataset.id, itemElement.dataset.type);
            render();
        }
    });

    render();
}

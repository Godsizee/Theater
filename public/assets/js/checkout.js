import { getCart, clearCart } from './cart.js';
import { apiFetch } from './api-client.js';

export function initializeCheckoutPage() {
    const container = document.getElementById('checkout-container');
    if (!container) return;
    
    const userData = window.CHECKOUT_USER_DATA || {};

    function render() {
        const cart = getCart();
        
        if (cart.length === 0) {
            container.innerHTML = `<div class="empty-cart-message"><h3>Ihr Warenkorb ist leer</h3><p>Fügen Sie Filme oder Serien hinzu, um sie hier zu sehen.</p><a href="${window.APP_CONFIG.baseUrl}/select" class="btn btn-primary" style="width: auto;" data-spa-link>Jetzt stöbern</a></div>`;
            return;
        }

        let itemsHTML = cart.map(item => `
            <div class="order-item" data-id="${item.id}" data-type="${item.type}">
                <img src="${item.poster}" alt="${item.title}" class="order-item-poster">
                <div class="order-item-details">
                    <h5>${item.title}</h5>
                    <p class="availability">Sofort lieferbar</p>
                </div>
                <div class="order-item-price">${(item.price || 0).toFixed(2)} €</div>
            </div>`).join('');

        const subtotal = cart.reduce((sum, item) => sum + (item.price || 0), 0);
        const shipping = 0.00;
        const total = subtotal + shipping;

        container.innerHTML = `
            <div class="checkout-layout">
                <div class="customer-info-col">
                    <div class="info-box">
                        <div class="info-box-header">
                            <h4>Rechnungsanschrift</h4>
                            <a href="${window.APP_CONFIG.baseUrl}/profil_daten" class="edit-link" data-spa-link>Bearbeiten</a>
                        </div>
                        <address>
                            ${userData.Vorname || ''} ${userData.Nachname || ''}<br>
                            ${userData.Strasse || ''} ${userData.Hausnummer || ''}<br>
                            ${userData.PLZ || ''} ${userData.Ort || ''}
                        </address>
                    </div>
                    <div class="info-box">
                        <div class="info-box-header"><h4>Zahlungsarten</h4></div>
                        <div class="payment-options">
                            <div class="payment-option selected" data-payment="paypal">
                                <img src="${window.APP_CONFIG.baseUrl}/public/assets/icons/paypalblack.png" alt="PayPal" class="payment-icon">
                                <span class="payment-label">PayPal</span>
                            </div>
                            <div class="payment-option" data-payment="klarna">
                                <img src="${window.APP_CONFIG.baseUrl}/public/assets/icons/klarnablack.png" alt="Klarna" class="payment-icon">
                                <span class="payment-label">Klarna</span>
                            </div>
                            <div class="payment-option" data-payment="visa">
                                <img src="${window.APP_CONFIG.baseUrl}/public/assets/icons/visablack.png" alt="Visa" class="payment-icon">
                                <span class="payment-label">Kreditkarte</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="order-details-col">
                    <h4>Ihre Bestellung</h4>
                    <div class="order-items-container">${itemsHTML}</div>
                    <div class="order-summary">
                        <div class="summary-line"><span>Versandkosten</span><span>${shipping.toFixed(2)} €</span></div>
                        <div class="summary-line total"><span>Gesamtsumme (inkl. MwSt.)</span><span>${total.toFixed(2)} €</span></div>
                    </div>
                    <div class="final-confirmation">
                        <div class="terms-agreement">
                           <label>
                               <input type="checkbox" id="terms-checkbox">
                               <span>Ich habe die <a href="${window.APP_CONFIG.baseUrl}/agb" target="_blank" data-spa-link>AGB</a> gelesen und stimme ihnen zu.</span>
                           </label>
                        </div>
                        <button id="submit-order-btn" class="btn btn-success">Kaufen</button>
                    </div>
                </div>
            </div>`;
    }

    container.addEventListener('click', async (e) => {
        if (e.target.matches('#submit-order-btn')) {
            const termsCheckbox = document.getElementById('terms-checkbox');
            if (!termsCheckbox || !termsCheckbox.checked) {
                window.showToast('Bitte stimmen Sie den AGB zu, um fortzufahren.', 'error');
                return;
            }

            const submitBtn = e.target;
            submitBtn.disabled = true;
            submitBtn.textContent = 'Bestellung wird verarbeitet...';

            try {
                const apiUrl = `${window.APP_CONFIG.baseUrl}/api/create_order.php`;
                const options = {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(getCart())
                };
                
                const result = await apiFetch(apiUrl, options);
                
                if (result.success) {
                    clearCart();
                    window.showToast(result.message, 'success');
                    // KORREKTUR: Die Weiterleitung nutzt jetzt die saubere Route ohne .php
                    setTimeout(() => window.navigateTo(`${window.APP_CONFIG.baseUrl}/bestellungen`), 1500);
                }
                
            } catch (error) {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Kaufen';
            }
        }
        
        const paymentOption = e.target.closest('.payment-option');
        if (paymentOption) {
            container.querySelectorAll('.payment-option').forEach(el => el.classList.remove('selected'));
            paymentOption.classList.add('selected');
        }
    });

    render();
}
import { addToCart } from './cart.js';

export function initializeRippleEffect() {
    function createRipple(event) {
        const button = event.target.closest('.btn:not([disabled])');
        if (!button) {
            return;
        }
        const circle = document.createElement('span');
        const diameter = Math.max(button.clientWidth, button.clientHeight);
        const radius = diameter / 2;
        const rect = button.getBoundingClientRect();
        circle.style.left = `${event.clientX - rect.left - radius}px`;
        circle.style.top = `${event.clientY - rect.top - radius}px`;
        circle.classList.add('ripple');
        const ripple = button.getElementsByClassName('ripple')[0];
        if (ripple) {
            ripple.remove();
        }
        button.appendChild(circle);
        circle.addEventListener('animationend', () => {
            circle.remove();
        });
    }
    document.removeEventListener('click', createRipple);
    document.addEventListener('click', createRipple);
}

export function initializeCartActions() {
    function handleAddToCart(event) {
        const cartButton = event.target.closest('.add-to-cart');
        if (!cartButton) return;
        event.preventDefault();

        const card = cartButton.closest('.movie-card, .movie-detail-container');
        if (!card) return;

        // KORREKTUR: Wir lesen die Daten jetzt direkt aus den data-* Attributen am Button.
        // Das ist robust und unanfällig für Text- oder Layout-Änderungen.
        const price = parseFloat(cartButton.dataset.price);

        if (isNaN(price)) {
            console.error('Konnte keinen gültigen Preis extrahieren. Das data-price Attribut fehlt oder ist ungültig.', cartButton);
            window.showToast('Fehler: Der Preis für diesen Artikel konnte nicht ermittelt werden.', 'error');
            return;
        }

        const item = {
            id: cartButton.dataset.id,
            type: cartButton.dataset.type,
            title: cartButton.dataset.title, // Wir lesen auch den Titel direkt vom Button.
            price: price,
            poster: card.querySelector('img').src
        };
        
        const added = addToCart(item);
        if (added) {
            window.showToast(`"${item.title}" wurde zum Warenkorb hinzugefügt.`, 'success');
        } else {
            window.showToast(`"${item.title}" ist bereits im Warenkorb.`, 'error');
        }
    }
    document.body.removeEventListener('click', handleAddToCart);
    document.body.addEventListener('click', handleAddToCart);
}
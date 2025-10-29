// assets/js/cart.js

const CART_KEY = 'movieRentalCart';

// Lädt den Warenkorb aus dem sessionStorage
export function getCart() {
    const cart = sessionStorage.getItem(CART_KEY);
    return cart ? JSON.parse(cart) : [];
}

// Speichert den Warenkorb
function saveCart(cart) {
    sessionStorage.setItem(CART_KEY, JSON.stringify(cart));
}

// Fügt einen Artikel hinzu
export function addToCart(item) {
    const cart = getCart();
    const existingItem = cart.find(cartItem => cartItem.id === item.id && cartItem.type === item.type);

    if (!existingItem) {
        cart.push(item);
        saveCart(cart);
        updateCartIcon();
        return true;
    }
    return false;
}

// Entfernt einen Artikel
export function removeFromCart(itemId, itemType) {
    let cart = getCart();
    cart = cart.filter(item => !(item.id === itemId && item.type === itemType));
    saveCart(cart);
    updateCartIcon();
}

// NEU: Leert den gesamten Warenkorb
export function clearCart() {
    sessionStorage.removeItem(CART_KEY);
    updateCartIcon();
}

// Aktualisiert den Zähler im Header
export function updateCartIcon() {
    const cart = getCart();
    const countElement = document.getElementById('cart-item-count');
    if (countElement) {
        countElement.textContent = cart.length;
        countElement.classList.toggle('visible', cart.length > 0);
    }
}

// Initialisiere den Zähler beim ersten Laden der Seite
updateCartIcon();

// assets/js/main.js

import { initializeNotificationSystem } from './notifications.js';
import { initializeSpaRouting, updateActiveNavLink } from './spa.js';
import { initializeUi, initializeHeaderUi } from './ui.js';
import { initializeFrontendMediaLoader } from './frontend-media-loader.js';
import { initializeAdminMediaManager } from './admin-media-manager.js';
import { initializeRealtimeValidation } from './validation.js';
import { initializeAjaxActions } from './ajax-actions.js';
import { initializeProfileActions } from './profile-actions.js';
import { initializeAdminInteractions } from './admin-interactions.js';
import { initializeSpotlightSearch } from './spotlight-search.js';
import { initializeFilmLook } from './film-look.js';
import { initializeDynamicColors } from './dynamic-colors.js';
import { initializeRippleEffect, initializeCartActions } from './interactions.js';
import { updateCartIcon } from './cart.js';
import { initializeScrollAnimations } from './advanced-animations.js';
import { initializeCheckoutPage } from './checkout.js';
import { initializeCartPage } from './cart-page.js';
import { initializeTheme } from './theme.js'; // Import für den Theme-Schalter

/**
 * Führt alle Initialisierungsfunktionen aus, die für den dynamisch
 * geladenen Inhalt (via SPA-Routing) notwendig sind.
 */
function runContentInitializers() {
    console.log("Führe Inhalts-Initialisierer aus...");

    const initializers = [
        initializeUi,
        initializeRealtimeValidation,
        initializeAjaxActions,
        initializeProfileActions,
        initializeAdminInteractions,
        updateActiveNavLink,
        initializeDynamicColors,
        initializeScrollAnimations
    ];

    initializers.forEach(initFunc => {
        if (typeof initFunc === 'function') {
            try { initFunc(); } catch (error) { console.error(`Fehler bei der Ausführung von ${initFunc.name}:`, error); }
        }
    });

    if (document.getElementById('movies-grid-container') || document.getElementById('series-grid-container')) {
        initializeFrontendMediaLoader();
    } else if (document.querySelector('.admin-dashboard-body #movies-grid-container') || document.querySelector('.admin-dashboard-body #series-grid-container')) {
        initializeAdminMediaManager();
    } else if (document.getElementById('checkout-container')) {
        initializeCheckoutPage();
    } else if (document.getElementById('cart-container')) {
        initializeCartPage();
    }
}

/**
 * Führt die Initialisierungen aus, die nur einmal pro Seitenaufbau
 * stattfinden müssen und nicht bei jedem SPA-Navigationswechsel.
 */
function runGlobalInitializers() {
    console.log("Führe globale Initialisierer aus...");
    initializeTheme(); // Initialisiert den Dark Mode Schalter
    initializeNotificationSystem();
    initializeSpotlightSearch();
    initializeFilmLook();
    initializeHeaderUi();
    initializeRippleEffect();
    initializeCartActions();
    updateCartIcon();
    initializeSpaRouting(runContentInitializers); 
}

// Haupt-Event-Listener, der beim ersten Laden der Seite alles startet.
document.addEventListener('DOMContentLoaded', () => {
    runGlobalInitializers();
    runContentInitializers();
});
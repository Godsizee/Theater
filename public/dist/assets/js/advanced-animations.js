// assets/js/advanced-animations.js

/**
 * Initialisiert fortgeschrittene Scroll-Animationen mit GSAP und ScrollTrigger.
 */
export function initializeScrollAnimations() {
    // Sicherstellen, dass GSAP und ScrollTrigger geladen sind
    if (typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') {
        console.error("GSAP oder ScrollTrigger nicht geladen.");
        return;
    }

    // Das Plugin bei GSAP registrieren
    gsap.registerPlugin(ScrollTrigger);

    // Alle Filmkarten auf der Seite auswählen
    const cards = gsap.utils.toArray('.movie-card');

    if (cards.length === 0) {
        return;
    }

    // Die Animation für die Karten definieren
    gsap.from(cards, {
        // Startzustand: Unsichtbar und nach unten verschoben
        opacity: 0,
        y: 50,
        
        // Dauer der Animation für jede einzelne Karte
        duration: 0.6,
        
        // Versetzt den Start jeder Kartenanimation um 0.1 Sekunden
        stagger: 0.1,
        
        // Weicher "ease"-Effekt für die Animation
        ease: "power3.out",

        // ScrollTrigger-Konfiguration
        scrollTrigger: {
            trigger: ".movie-grid", // Der Container, der die Animation auslöst
            start: "top 80%",       // Startet, wenn 80% des Grids im Viewport sind
            end: "bottom 20%",
            // 'markers: true' kann zur Fehlersuche hinzugefügt werden
        }
    });
}

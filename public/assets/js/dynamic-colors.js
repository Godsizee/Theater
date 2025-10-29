/**
 * dynamic-colors.js
 */
// KORREKTUR: Wir importieren ColorThief als richtiges ES-Modul von einem CDN.
// Das ist die saubere, moderne Methode, die ohne Build-Tool im Browser funktioniert.
import ColorThief from 'https://cdn.jsdelivr.net/npm/colorthief@2.3.2/dist/color-thief.mjs';

export function initializeDynamicColors() {
    const cards = document.querySelectorAll('.movie-card, .recommendation-card');
    
    if (cards.length === 0) {
        return;
    }

    const colorThief = new ColorThief();

    cards.forEach(card => {
        const poster = card.querySelector('img');
        if (!poster) return;

        // Dieses Attribut ist wichtig, damit das Skript die Bilddaten lesen darf.
        poster.crossOrigin = 'Anonymous';

        const applyColor = (image, cardElement) => {
            try {
                const dominantColor = colorThief.getColor(image);
                const rgbString = `rgb(${dominantColor.join(',')})`;
                cardElement.style.setProperty('--accent-color', rgbString);
            } catch (e) {
                console.error("Fehler bei der Farbanalyse für Bild:", image.src, e);
            }
        };

        // Prüfen, ob das Bild bereits geladen ist, sonst auf das 'load'-Event warten.
        if (poster.complete) {
            applyColor(poster, card);
        } else {
            poster.addEventListener('load', function () {
                applyColor(this, card);
            });
            // Fehlerbehandlung, falls ein Bild nicht geladen werden kann.
            poster.addEventListener('error', function() {
                console.error('Bild für Farbanalyse konnte nicht geladen werden:', this.src);
            });
        }
    });
}

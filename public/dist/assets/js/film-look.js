// assets/js/film-look.js

export function initializeFilmLook() {
    if (document.body.dataset.filmLookInitialized) {
        return;
    }
    document.body.dataset.filmLookInitialized = 'true';

    const toggleButton = document.getElementById('film-look-toggle');
    const body = document.body;
    
    // Funktion zum Umschalten des Effekts
    const toggleFilmLook = () => {
        const isActive = body.classList.toggle('film-look-active');
        // Speichere die Einstellung im lokalen Speicher des Browsers
        localStorage.setItem('filmLookActive', isActive);
    };

    // Prüfe beim Laden der Seite die gespeicherte Einstellung
    if (localStorage.getItem('filmLookActive') === 'true') {
        body.classList.add('film-look-active');
    }

    // Event-Listener für den Klick auf den Schalter
    if (toggleButton) {
        toggleButton.addEventListener('click', toggleFilmLook);
    }

    // Erstelle und füge die Overlay-Elemente dem DOM hinzu
    const vignette = document.createElement('div');
    vignette.className = 'film-look-vignette';
    body.appendChild(vignette);

    const grain = document.createElement('div');
    grain.className = 'film-look-grain';
    body.appendChild(grain);
    
    console.log("Interaktiver Film-Look initialisiert.");
}
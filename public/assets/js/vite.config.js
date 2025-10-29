// vite.config.js

import { defineConfig } from 'vite';

// Dies ist die Konfigurationsdatei für Vite.
// Sie teilt dem Build-Tool mit, wo unsere Quelldateien sind
// und wohin die optimierten Dateien gespeichert werden sollen.

export default defineConfig({
    plugins: [],
    build: {
        // Der Ordner, in den die fertigen Dateien kommen.
        outDir: 'dist',
        // Erstellt eine "manifest.json", die PHP mitteilt, welche Dateien es laden soll.
        manifest: true,
        rollupOptions: {
            // Definiert die Einstiegspunkte unserer Anwendung.
            // Vite wird diesen Dateien folgen und alles importieren, was sie benötigen.
            input: {
                main: 'assets/js/main.js',
                styles: 'assets/css/main.css'
            }
        }
    },
    server: {
        // Konfiguration für den Vite-Entwicklungsserver (optional für dieses Szenario).
        // Ermöglicht es, während der Entwicklung direkt mit dem PHP-Backend zu arbeiten.
        origin: 'http://localhost:5173',
        cors: true,
        strictPort: true
    }
});
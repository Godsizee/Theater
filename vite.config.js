import { resolve } from 'path';
import { defineConfig } from 'vite';
import { fileURLToPath } from 'url';
import { dirname } from 'path';

// ES Module-sicherer Weg, um den Pfad zum aktuellen Verzeichnis zu erhalten
const __filename = fileURLToPath(import.meta.url);
const __dirname = dirname(__filename);

export default defineConfig({
    plugins: [],
    build: {
        // KORREKTUR: Das Ausgabe-Verzeichnis ist jetzt public/dist
        outDir: resolve(__dirname, 'public/dist'),
        emptyOutDir: true,
        manifest: true,
        rollupOptions: {
            // KORREKTUR: Die Eingabepfade zeigen jetzt korrekt in den public-Ordner
            input: {
                main: resolve(__dirname, 'public/assets/js/main.js'),
                styles: resolve(__dirname, 'public/assets/css/main.css')
            }
        }
    },
    server: {
        origin: 'http://localhost:5173',
        cors: true,
        strictPort: true
    }
});
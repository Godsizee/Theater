// assets/js/api-client.js

/**
 * Zentraler API-Client zum Abwickeln aller Fetch-Anfragen.
 * Kümmert sich um die JSON-Verarbeitung und eine einheitliche Fehlerbehandlung.
 * @param {string} url Die URL, die aufgerufen werden soll.
 * @param {object} options Die Standard-Fetch-Optionen (method, headers, body, etc.).
 * @returns {Promise<any>} Gibt ein Promise zurück, das bei Erfolg die JSON-Daten enthält.
 * @throws {Error} Wirft einen Fehler bei Netzwerk- oder Serverproblemen.
 */
export async function apiFetch(url, options = {}) {
    try {
        const response = await fetch(url, options);
        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.message || `Ein Serverfehler ist aufgetreten (Status: ${response.status}).`);
        }
        
        // `data.success` wird hier nicht mehr geprüft, da wir davon ausgehen,
        // dass eine nicht-ok-Antwort bereits einen Fehler wirft. Die aufrufende Funktion
        // kann sich auf die `data.success`-Prüfung konzentrieren, falls nötig.
        return data;

    } catch (error) {
        console.error('API Client Fehler:', error.message);
        window.showToast(error.message || 'Ein unbekannter Netzwerkfehler ist aufgetreten.', 'error');
        throw error;
    }
}
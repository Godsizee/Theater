// public/assets/js/profile-actions.js

import { apiFetch } from './api-client.js';

export function initializeProfileActions() {
    const mainContent = document.querySelector('.profile-content');
    if (!mainContent) return;

    // Funktion, um Event-Listener zu entfernen, bevor neue hinzugefügt werden
    const removeListeners = () => {
        mainContent.removeEventListener('click', handleProfileClick);
        mainContent.removeEventListener('submit', handleProfileSubmit);
    };

    // Funktion zum Hinzufügen der Event-Listener
    const addListeners = () => {
        mainContent.addEventListener('click', handleProfileClick);
        mainContent.addEventListener('submit', handleProfileSubmit);
    };

    function handleProfileClick(e) {
        const editButton = e.target.closest('.edit-icon');
        if (editButton) {
            const section = editButton.closest('.profile-data-section');
            if (section) {
                section.classList.toggle('is-editing');
            }
        }
    }

    async function handleProfileSubmit(e) {
        const form = e.target.closest('.profile-edit-form');
        if (!form) return;

        e.preventDefault();
        
        const password = await window.showPasswordConfirm('Änderungen bestätigen');
        if (password === null) return; // Benutzer hat abgebrochen

        if (password === '') {
            window.showToast('Das Passwort darf nicht leer sein.', 'error');
            return;
        }

        const formData = new FormData(form);
        formData.append('current_password', password);
        
        // KORREKTUR: Die URL zeigt jetzt auf die neue Controller-Route
        const apiUrl = `${window.APP_CONFIG.baseUrl}/profil_daten/update`;

        try {
            const data = await apiFetch(apiUrl, { method: 'POST', body: formData });

            if (data.success) {
                window.showToast(data.message, 'success');
                // Lade die Seite neu, um die aktualisierten Daten anzuzeigen
                window.navigateTo(window.location.href, false); 
            }
            // Die apiFetch-Funktion zeigt bei `success: false` bereits einen Fehler-Toast an
        } catch (error) {
            // Die apiFetch-Funktion kümmert sich um die Fehleranzeige bei Netzwerkfehlern etc.
            console.error('Fehler beim Aktualisieren des Profils:', error);
        }
    }

    // Initiales Setup
    removeListeners(); // Sicherstellen, dass keine alten Listener vorhanden sind
    addListeners();
}
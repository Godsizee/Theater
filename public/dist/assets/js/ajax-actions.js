// assets/js/ajax-actions.js

import { apiFetch } from './api-client.js';

export function initializeAjaxActions() {
    const handleClick = async (e) => {
        // --- Generic Delete Action ---
        const deleteButton = e.target.closest('.action-btn.delete');
        if (deleteButton) {
            e.preventDefault();
            const confirmed = await window.showConfirm('Löschen bestätigen', 'Sind Sie sicher, dass dieses Element endgültig gelöscht werden soll?');
            if (!confirmed) return;

            const itemToRemove = deleteButton.closest('.movie-card, .missing-data-list > li, .user-table tbody tr');
            const id = deleteButton.dataset.id;
            const type = deleteButton.dataset.type;

            if (!id || !itemToRemove || !type) {
                console.error("Delete action aborted: Missing id, item to remove, or data-type.");
                return;
            }

            try {
                const formData = new FormData();
                formData.append('id', id);
                const apiUrl = `${window.APP_CONFIG.baseUrl}/api/delete_${type}.php`;
                
                const data = await apiFetch(apiUrl, { method: 'POST', body: formData });

                if (data.success) {
                    window.showToast(data.message || 'Erfolgreich gelöscht.', 'success');
                    itemToRemove.classList.add('is-deleting');
                    itemToRemove.addEventListener('transitionend', () => itemToRemove.remove());
                } else {
                    window.showToast(data.message || 'Aktion konnte nicht ausgeführt werden.', 'error');
                }
            } catch (error) {
                // Fehlerbehandlung erfolgt zentral in apiFetch.
                // Hier muss nichts weiter getan werden.
            }
        }

        // --- Order Cancellation Action (Support) ---
        const cancelButton = e.target.closest('.action-cancel-order');
        if (cancelButton) {
            e.preventDefault();
            const ticketId = cancelButton.dataset.ticketId;
            const confirmed = await window.showConfirm('Bestellung stornieren', 'Möchten Sie diese Ausleihe wirklich stornieren?');
            if (!confirmed) return;

            const formData = new FormData();
            formData.append('ticket_id', ticketId);

            try {
                const apiUrl = `${window.APP_CONFIG.baseUrl}/support/stornieren.php`;
                const data = await apiFetch(apiUrl, { method: 'POST', body: formData });

                if (data.success) {
                    window.showToast(data.message, 'success');
                    const row = document.getElementById(`ticket-row-${ticketId}`);
                    if (row) {
                        const statusTextElem = row.querySelector('.status-text');
                        if(statusTextElem) statusTextElem.textContent = 'Storniert';
                        
                        const statusInfoElem = row.querySelector('.status-info-text');
                        if(statusInfoElem) statusInfoElem.textContent = 'Soeben storniert';
                        
                        cancelButton.remove();
                    }
                } else {
                     window.showToast(data.message, 'error');
                }
            } catch(error) {
                // Fehlerbehandlung erfolgt zentral in apiFetch.
            }
        }
    };

    document.body.removeEventListener('click', handleClick);
    document.body.addEventListener('click', handleClick);
}
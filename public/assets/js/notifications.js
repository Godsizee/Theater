/**
 * notifications.js - Manages the creation of toast notifications and confirmation modals.
 */
export function initializeNotificationSystem() {
    const body = document.querySelector('body');
    if (document.querySelector('.toast-container')) return; // Already initialized

    // --- Toast Container ---
    const toastContainer = document.createElement('div');
    toastContainer.className = 'toast-container';
    body.appendChild(toastContainer);

    // --- window.showToast ---
    window.showToast = (message, type = 'success', duration = 4000) => {
        const toast = document.createElement('div');
        toast.className = `toast ${type}`;
        const icon = type === 'success' ? '✓' : '×';
        toast.innerHTML = `<div class="toast-icon">${icon}</div><div class="toast-message"><p>${message}</p></div>`;
        toastContainer.appendChild(toast);
        setTimeout(() => toast.classList.add('visible'), 10);
        setTimeout(() => {
            toast.classList.remove('visible');
            toast.addEventListener('transitionend', () => toast.remove());
        }, duration);
    };

    // --- window.showConfirm ---
    window.showConfirm = (title, message) => {
        return new Promise((resolve) => {
            const existingModal = document.querySelector('.modal-overlay');
            if(existingModal) existingModal.remove();

            const overlay = document.createElement('div');
            overlay.className = 'modal-overlay';
            overlay.innerHTML = `
                <div class="modal-box">
                    <h2>${title}</h2>
                    <p>${message}</p>
                    <div class="modal-actions">
                        <button class="btn btn-secondary" data-resolve="false">Abbrechen</button>
                        <button class="btn btn-danger" data-resolve="true">Ja, bestätigen</button>
                    </div>
                </div>`;
            const handleResolve = (e) => {
                const button = e.target.closest('button[data-resolve]');
                if (!button) return;
                overlay.classList.remove('visible');
                overlay.addEventListener('transitionend', () => {
                    overlay.remove();
                    resolve(button.dataset.resolve === 'true');
                });
            };
            overlay.addEventListener('click', handleResolve);
            body.appendChild(overlay);
            setTimeout(() => overlay.classList.add('visible'), 10);
        });
    };
    
    // --- window.showPasswordConfirm ---
    window.showPasswordConfirm = (title) => {
        return new Promise((resolve) => {
            const existingModal = document.querySelector('.modal-overlay');
            if(existingModal) existingModal.remove();
            
            const overlay = document.createElement('div');
            overlay.className = 'modal-overlay';
            overlay.innerHTML = `
                <div class="modal-box">
                    <h2>${title}</h2>
                    <p>Bitte geben Sie Ihr aktuelles Passwort ein, um diese Aktion zu bestätigen.</p>
                    <form id="modal-password-form">
                        <input type="password" id="modal-password-input" placeholder="Aktuelles Passwort" required style="margin-bottom: 20px;">
                        <div class="modal-actions">
                            <button type="button" class="btn btn-secondary" data-resolve="cancel">Abbrechen</button>
                            <button type="submit" class="btn btn-primary">Bestätigen</button>
                        </div>
                    </form>
                </div>`;
            
            const form = overlay.querySelector('#modal-password-form');
            const passwordInput = overlay.querySelector('#modal-password-input');
            const cancelButton = overlay.querySelector('[data-resolve="cancel"]');

            const closeModal = (value) => {
                overlay.classList.remove('visible');
                overlay.addEventListener('transitionend', () => {
                    overlay.remove();
                    resolve(value);
                });
            }
            
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                closeModal(passwordInput.value);
            });

            cancelButton.addEventListener('click', () => {
                closeModal(null);
            });

            body.appendChild(overlay);
            setTimeout(() => {
                overlay.classList.add('visible');
                passwordInput.focus();
            }, 10);
        });
    };
}
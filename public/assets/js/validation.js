// C:\xampp\htdocs\files\Theater\assets\js\validation.js

/**
 * validation.js - Handles real-time form field validation (e.g., for registration).
 */
export function initializeRealtimeValidation() {
    let debounceTimer;

    const validateField = async (field, fieldName) => {
        const statusElement = field.parentElement.querySelector('.validation-status');
        if(!statusElement) return;

        const value = field.value.trim();
        field.classList.remove('invalid-input');

        if (value.length < 3) {
            statusElement.className = 'validation-status';
            statusElement.textContent = '';
            return;
        }

        statusElement.textContent = 'Prüfe...';
        statusElement.className = 'validation-status checking visible';

        try {
            const response = await fetch(`${window.APP_CONFIG.baseUrl}/api/validate_user.php?${fieldName}=${encodeURIComponent(value)}`);
            const data = await response.json();
            
            statusElement.textContent = data.message;
            statusElement.className = `validation-status ${data.available ? 'valid' : 'invalid'} visible`;
            
            if (!data.available) {
                field.classList.add('invalid-input');
            }
        } catch (error) {
            statusElement.textContent = 'Prüfung fehlgeschlagen.';
            statusElement.className = 'validation-status invalid visible';
            console.error("Validation error:", error);
        }
    };

    const handleInput = (e) => {
        const target = e.target;
        clearTimeout(debounceTimer);

        debounceTimer = setTimeout(() => {
            if (target.matches('#username-input')) {
                validateField(target, 'username');
            }
            if (target.matches('#email-input')) {
                validateField(target, 'email');
            }
        }, 500);
    };

    document.body.removeEventListener('input', handleInput);
    document.body.addEventListener('input', handleInput);
}
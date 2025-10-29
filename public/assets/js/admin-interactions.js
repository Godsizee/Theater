/**
 * Initialisiert interaktive Elemente speziell für den Admin-Bereich.
 */
export function initializeAdminInteractions() {
    // Bestehende Logik für den Kopieren-Button
    initializeCopyPromptButton();
    
    // Neue Logik für den dynamischen Generator auf der insert-Seite
    initializeDynamicPromptGenerator();
}

/**
 * Logik für den "Prompt kopieren" Button. Funktioniert auf update.php und insert.php.
 */
function initializeCopyPromptButton() {
    const copyBtn = document.getElementById('copy-prompt-btn');
    const promptText = document.getElementById('ai-prompt');

    if (copyBtn && promptText) {
        copyBtn.addEventListener('click', function() {
            if (this.disabled) return;

            navigator.clipboard.writeText(promptText.value).then(() => {
                const originalText = this.textContent;
                this.textContent = 'Kopiert!';
                this.style.backgroundColor = 'var(--color-success)';
                
                setTimeout(() => {
                    this.textContent = originalText;
                    this.style.backgroundColor = ''; // Setzt den Stil zurück
                }, 2000);
            }).catch(err => {
                console.error('Fehler beim Kopieren: ', err);
            });
        });
    }
}

/**
 * Erstellt dynamisch einen KI-Prompt auf der insert.php Seite basierend auf Benutzereingaben.
 */
function initializeDynamicPromptGenerator() {
    // Diese Funktion soll nur auf der "Neuen Inhalt hinzufügen"-Seite laufen
    const insertForm = document.querySelector('form[action="insert.php"]');
    if (!insertForm) {
        return;
    }

    const mediaTypeSelect = document.getElementById('media-type-select');
    const titleInput = document.getElementById('title-input');
    const genreInput = document.getElementById('genre-input');
    const promptTextarea = document.getElementById('ai-prompt');
    const copyBtn = document.getElementById('copy-prompt-btn');

    const updatePrompt = () => {
        const mediaType = mediaTypeSelect.value === 'movie' ? 'für den Film' : 'für die Serie';
        const title = titleInput.value.trim();
        const genre = genreInput.value.trim().split(',')[0]; // Nur das erste Genre nehmen

        if (!title) {
            promptTextarea.value = 'Bitte geben Sie einen Titel und ein Genre ein...';
            copyBtn.disabled = true;
            return;
        }

        let prompt = `Ein dramatisches, hochwertiges Filmplakat ${mediaType} '${title}'. `;
        if (genre) {
            prompt += `Genre: ${genre}. `;
        }
        prompt += 'Stil: filmisch, episch, hochauflösend, meisterwerk.';

        promptTextarea.value = prompt;
        copyBtn.disabled = false;
    };

    // Event Listener für die relevanten Felder hinzufügen
    mediaTypeSelect.addEventListener('change', updatePrompt);
    titleInput.addEventListener('input', updatePrompt);
    genreInput.addEventListener('input', updatePrompt);
}
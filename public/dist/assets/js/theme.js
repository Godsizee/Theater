export function initializeTheme() {
    const themeToggle = document.getElementById('theme-toggle');
    // KORREKTUR: Wir zielen jetzt auf das <html>-Element statt auf den <body>
    const rootElement = document.documentElement;

    // Funktion zum Anwenden des Themes
    const applyTheme = (theme) => {
        if (theme === 'dark') {
            rootElement.classList.add('dark-mode');
        } else {
            rootElement.classList.remove('dark-mode');
        }
    };

    // Funktion zum Umschalten und Speichern
    const toggleTheme = () => {
        const newTheme = rootElement.classList.contains('dark-mode') ? 'light' : 'dark';
        localStorage.setItem('theme', newTheme);
        applyTheme(newTheme);
    };

    if (themeToggle) {
        themeToggle.addEventListener('click', toggleTheme);
    }

}

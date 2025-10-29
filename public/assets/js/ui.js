export function initializeUi() {
    const menuBox = document.querySelector('.menu-box');
    if (menuBox) {
        menuBox.addEventListener('mousemove', (e) => {
            const { left, top, width, height } = menuBox.getBoundingClientRect();
            const x = e.clientX - left;
            const y = e.clientY - top;
            const rotateX = (y - height / 2) / (height / 2) * -5;
            const rotateY = (x - width / 2) / (width / 2) * 5;
            menuBox.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale(1.02)`;
        });
        menuBox.addEventListener('mouseleave', () => {
            menuBox.style.transform = 'perspective(1000px) rotateX(0) rotateY(0) scale(1)';
        });
    }

    document.body.addEventListener('change', e => {
        if (e.target.matches('.file-upload-input')) {
            const fileInput = e.target;
            const fileNameDisplay = fileInput.closest('.file-upload-wrapper')?.querySelector('.file-upload-filename');
            if (fileNameDisplay) {
                fileNameDisplay.textContent = fileInput.files.length > 0 ? fileInput.files[0].name : 'Keine Datei ausgewählt';
            }
        }
    });

    document.body.addEventListener('click', e => {
        const header = e.target.closest('.collapsible-header');
        if (!header) return;
        
        const content = header.nextElementSibling;
        if (!content || !content.classList.contains('collapsible-content')) return;

        const isExpanded = header.getAttribute('aria-expanded') === 'true';
        
        header.setAttribute('aria-expanded', String(!isExpanded));
        content.classList.toggle('is-open', !isExpanded);
    });
}

export function initializeHeaderUi() {
    const header = document.querySelector('.page-header');
    if (!header) return;

    const mobileToggle = document.getElementById('mobile-menu-toggle');
    const headerNav = document.getElementById('header-nav');

    if (mobileToggle && headerNav) {
        
        // KORREKTUR: Eigene Funktion zum sauberen Schließen des Menüs
        const closeMobileMenu = () => {
            mobileToggle.classList.remove('is-open');
            headerNav.classList.remove('is-open');
            document.body.classList.remove('menu-open');
            mobileToggle.setAttribute('aria-expanded', 'false');
        };

        mobileToggle.addEventListener('click', () => {
            const isOpen = headerNav.classList.contains('is-open');
            if (isOpen) {
                closeMobileMenu();
            } else {
                mobileToggle.classList.add('is-open');
                headerNav.classList.add('is-open');
                document.body.classList.add('menu-open');
                mobileToggle.setAttribute('aria-expanded', 'true');
            }
        });

        // KORREKTUR: Neuer Event-Listener, der das Menü bei Klick auf einen Link schließt.
        headerNav.addEventListener('click', (e) => {
            if (e.target.closest('a')) {
                closeMobileMenu();
            }
        });
    }

    const userMenu = header.querySelector('.user-menu');
    if (userMenu && !userMenu.dataset.menuInitialized) {
        const toggleButton = userMenu.querySelector('.user-menu-toggle');
        const dropdown = userMenu.querySelector('.user-menu-dropdown');

        if (toggleButton && dropdown) {
            toggleButton.addEventListener('click', (e) => {
                e.stopPropagation();
                const isOpen = dropdown.classList.toggle('is-open');
                toggleButton.setAttribute('aria-expanded', isOpen);
            });

            document.addEventListener('click', (e) => {
                if (!userMenu.contains(e.target) && dropdown.classList.contains('is-open')) {
                    dropdown.classList.remove('is-open');
                    toggleButton.setAttribute('aria-expanded', 'false');
                }
            });
            userMenu.dataset.menuInitialized = 'true';
        }
    }
}
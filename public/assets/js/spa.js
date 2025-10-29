export function updateActiveNavLink() {
    const currentPathname = window.location.pathname;
    const navLinks = document.querySelectorAll('.header-nav .header-link');

    navLinks.forEach(link => {
        const linkPathname = new URL(link.href).pathname;
        link.classList.remove('active');
        if (linkPathname === currentPathname) {
            link.classList.add('active');
        }
    });
}

export function initializeSpaRouting(reinitializeCallback = () => {}) {
    async function navigateTo(url, addToHistory = true) {
        const mainContentWrapper = document.querySelector('main.page-wrapper');
        if (!mainContentWrapper) {
            console.error("SPA: .page-wrapper not found. Full page reload.");
            window.location.href = url;
            return;
        }

        mainContentWrapper.classList.add('is-exiting');
        await new Promise(resolve => setTimeout(resolve, 300));

        try {
            const fullPath = new URL(url).pathname;
            let relativePath = fullPath;
            if (fullPath.startsWith(window.APP_CONFIG.baseUrl)) {
                relativePath = fullPath.substring(window.APP_CONFIG.baseUrl.length);
            }
            if (relativePath.startsWith('/')) {
                relativePath = relativePath.substring(1);
            }
            
            const apiPath = `${window.APP_CONFIG.baseUrl}/api/get_page_content.php?path=${encodeURIComponent(relativePath)}`;
            
            const response = await fetch(apiPath);
            const data = await response.json();

            if (data.success) {
                // Den alten Inhalt ersetzen
                mainContentWrapper.innerHTML = data.html;
                document.title = data.page_title || document.title;
                
                // NEU: Die Klasse des Body-Tags aktualisieren
                document.body.className = data.body_class || '';

                if (addToHistory) {
                    history.pushState({ path: url }, data.page_title, url);
                }
                window.scrollTo(0, 0);

                mainContentWrapper.classList.remove('is-exiting');
                mainContentWrapper.classList.add('is-entering');
                setTimeout(() => mainContentWrapper.classList.remove('is-entering'), 300);

                if (typeof reinitializeCallback === 'function') {
                    reinitializeCallback();
                }

            } else {
                window.location.href = url;
            }
        } catch (error) {
            console.error("SPA: Fetch error during navigation:", error);
            window.location.href = url;
        }
    }

    document.body.addEventListener('click', e => {
        const link = e.target.closest('a[data-spa-link]');
        if (link) {
            e.preventDefault();
            navigateTo(link.href);
        }
    });

    window.addEventListener('popstate', e => {
        if (e.state && e.state.path) {
            navigateTo(e.state.path, false); 
        }
    });

    if (!history.state || history.state.path !== window.location.href) {
        history.replaceState({ path: window.location.href }, document.title, window.location.href);
    }

    window.navigateTo = navigateTo;
}
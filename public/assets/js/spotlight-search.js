export function initializeSpotlightSearch() {
    if (document.body.dataset.spotlightInitialized) {
        return;
    }
    document.body.dataset.spotlightInitialized = 'true';

    const overlay = document.createElement('div');
    overlay.className = 'spotlight-overlay';
    document.body.appendChild(overlay);
    
    const searchInput = document.getElementById('search');

    document.body.addEventListener('focusin', (e) => {
        if (e.target.matches('#search')) {
            document.body.classList.add('search-is-active');
        }
    });

    document.body.addEventListener('focusout', (e) => {
        if (e.target.matches('#search')) {
            document.body.classList.remove('search-is-active');
        }
    });
    
    document.addEventListener('mousedown', (e) => {
        if (!document.body.classList.contains('search-is-active')) {
            return;
        }

        const searchContainer = document.querySelector('.search-filter-container');

        if (searchContainer && !searchContainer.contains(e.target)) {
            searchInput?.blur();
        }
    });
    
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && document.body.classList.contains('search-is-active')) {
            searchInput?.blur();
        }
    });
}
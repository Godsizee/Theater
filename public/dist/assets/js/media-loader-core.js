import { initializeDynamicColors } from './dynamic-colors.js';
import { apiFetch } from './api-client.js';

function showSkeletonLoader(gridElement, count) {
    gridElement.innerHTML = ''; 
    let skeletonHTML = '';
    for (let i = 0; i < count; i++) {
        skeletonHTML += `
            <div class="skeleton-card">
                <div class="skeleton-poster"></div>
                <div class="skeleton-content">
                    <div class="skeleton-title"></div>
                    <div class="skeleton-meta">
                        <div class="skeleton-meta-tag"></div>
                        <div class="skeleton-meta-tag"></div>
                    </div>
                </div>
            </div>
        `;
    }
    gridElement.innerHTML = skeletonHTML;
}

function createMediaCard(item, mediaType) {
    const config = window.APP_CONFIG;
    const price = new Intl.NumberFormat('de-DE', { style: 'currency', currency: 'EUR' }).format(item.price);
    const priceSuffix = mediaType === 'series' ? ' / Staffel' : '';
    const detailsUrl = `${config.baseUrl}/${mediaType === 'movies' ? 'movie' : 'series'}/${item.slug}`;
    const posterPath = item.PosterPath ? `${config.baseUrl}/${item.PosterPath}` : `${config.baseUrl}/img/movieImg/placeholder.png`;
    const isAdmin = document.body.classList.contains('admin-dashboard-body');

    let metaTags = `<span class="meta-tag price">${price}${priceSuffix}</span>`;
    if (mediaType === 'movies' && item.USK !== null) {
        metaTags = `<span class="meta-tag usk-${item.USK}">FSK ${item.USK}</span>` + metaTags;
    }

    let actionButtons;
    if (isAdmin) {
        actionButtons = `
            <a href="${config.baseUrl}/admin/update/${mediaType === 'movies' ? 'movie' : 'series'}/${item.id}" class="action-btn edit">Bearbeiten</a>
            <a href="#" class="action-btn delete" data-id="${item.id}" data-type="${mediaType === 'movies' ? 'movie' : 'series'}">Löschen</a>
        `;
    } else {
        actionButtons = `
            <a href="#" class="action-btn add-to-cart" 
               data-id="${item.id}" 
               data-type="${mediaType === 'movies' ? 'movie' : 'series'}" 
               data-title="${item.title}" 
               data-price="${item.price}">In den Warenkorb</a>
        `;
    }

    return `
        <div class="movie-card">
            <a href="${detailsUrl}" class="movie-card-link" data-spa-link>
                <div class="movie-card-image-container">
                    <img loading="lazy" decoding="async" src="${posterPath}" alt="Poster von ${item.title}" class="movie-card-poster">
                </div>
                <div class="movie-card-content">
                    <h3>${item.title}</h3>
                    <div class="movie-meta">
                        ${metaTags}
                    </div>
                </div>
            </a>
            <div class="movie-card-actions">
                ${actionButtons}
            </div>
        </div>
    `;
}

export function setupMediaLoader(mediaGrid, paginationContainer, filterForm, mediaType) {
    if (!mediaGrid || !paginationContainer || !filterForm) {
        return;
    }
    
    let isLoading = false;
    let debounceTimer;

    const fetchMedia = async (params, append = false) => {
        if (isLoading) return;
        isLoading = true;

        if (!append) {
            const itemsPerPage = parseInt(params.get('items_per_page')) || 12;
            showSkeletonLoader(mediaGrid, itemsPerPage);
        }

        const loadMoreBtn = paginationContainer.querySelector('#load-more-btn');
        if (loadMoreBtn) {
            loadMoreBtn.disabled = true;
            loadMoreBtn.textContent = 'Lade...';
        }

        try {
            const apiUrl = `${window.APP_CONFIG.baseUrl}/api/${mediaType}.php?${params.toString()}`;
            const result = await apiFetch(apiUrl);
            if (!result.success) throw new Error(result.message);
            const newCardsHTML = result.data.items.map(item => createMediaCard(item, mediaType)).join('');
            
            await new Promise(resolve => setTimeout(resolve, 300));

            if (append) {
                mediaGrid.querySelector('.skeleton-card')?.remove();
                mediaGrid.insertAdjacentHTML('beforeend', newCardsHTML);
            } else {
                mediaGrid.innerHTML = newCardsHTML || '<div class="container text-center" style="grid-column: 1 / -1;"><p>Ihre Suche ergab leider keine Treffer.</p></div>';
            }
            
            paginationContainer.innerHTML = '';
            if (result.data.pagination.currentPage < result.data.pagination.totalPages) {
                const nextPage = result.data.pagination.currentPage + 1;
                paginationContainer.innerHTML = `<button id='load-more-btn' class='btn btn-primary' data-next-page='${nextPage}' style='width: auto;'>Weitere laden</button>`;
            }
            
            initializeDynamicColors();
            
            // KORREKTUR: Die Zeile, die den Fokus entfernt, wurde gelöscht.
            // document.getElementById('search')?.blur();

        } catch (error) {
            mediaGrid.innerHTML = '<p class="message error" style="grid-column: 1 / -1;">Die Inhalte konnten nicht geladen werden.</p>';
        } finally {
            isLoading = false;
        }
    };

    const handleFilterChange = (isInitialLoad = false) => {
        const formData = new FormData(filterForm);
        const settings = window.APP_CONFIG.settings || { items_per_page: 24 };
        const params = new URLSearchParams();
        
        for (const [key, value] of formData.entries()) {
            if (value) { 
                params.append(key, value);
            }
        }
        
        if (!isInitialLoad) {
             const urlParams = new URLSearchParams(window.location.search);
             if (!params.has('genre') && urlParams.has('genre')) {
                params.set('genre', urlParams.get('genre'));
             }
        }
       
        params.set('items_per_page', settings.items_per_page);
        if (!params.has('page')) {
            params.set('page', '1');
        }
        
        fetchMedia(params, false);

        const currentUrl = new URL(window.location.href);
        currentUrl.search = params.toString();
        if (currentUrl.href !== window.location.href) {
            history.pushState({ path: currentUrl.href }, '', currentUrl.href);
        }
    };
    
    const syncFormWithUrl = () => {
        const urlParams = new URLSearchParams(window.location.search);
        for (const [key, value] of urlParams.entries()) {
            const input = filterForm.querySelector(`[name="${key}"]`);
            if (input) {
                input.value = value;
            }
        }
    };

    const handleLoadMore = (e) => {
        const button = e.target.closest('#load-more-btn');
        if (button && !isLoading) {
            const nextPage = button.dataset.nextPage;
            if (nextPage) {
                const params = new URLSearchParams(new FormData(filterForm));
                params.set('page', nextPage);
                fetchMedia(params, true);
            }
        }
    };

    const debouncedFilter = () => {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => handleFilterChange(false), 400);
    };
    
    syncFormWithUrl(); 
    handleFilterChange(true); 

    filterForm.addEventListener('submit', (e) => {
        e.preventDefault();
        handleFilterChange(false);
    });

    const searchInput = filterForm.querySelector('#search');
    if (searchInput) {
        searchInput.addEventListener('input', debouncedFilter);
        searchInput.addEventListener('keyup', (e) => {
            if (e.key === 'Backspace' || e.key === 'Delete') {
                debouncedFilter();
            }
        });
    }

    const selects = filterForm.querySelectorAll('select');
    selects.forEach(select => {
        select.addEventListener('change', () => handleFilterChange(false));
    });

    paginationContainer.addEventListener('click', handleLoadMore);
}
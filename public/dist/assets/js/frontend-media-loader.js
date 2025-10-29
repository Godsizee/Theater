// C:\xampp\htdocs\files\Theater\assets\js\frontend-media-loader.js
import { setupMediaLoader } from './media-loader-core.js';

/**
 * Initialisiert den Medien-Loader für Frontend-Seiten (select.php, series.php).
 */
export function initializeFrontendMediaLoader() {
    console.log("initializeFrontendMediaLoader wurde aufgerufen!");

    // Initialisierung für Filmseiten
    const moviesGrid = document.querySelector('#movies-grid-container');
    const moviePagination = document.querySelector('#pagination-container');
    const movieFilterForm = document.querySelector('#movie-filter-form');

    if (moviesGrid && moviePagination && movieFilterForm) {
        console.log("Frontend Film-Loader wird eingerichtet.");
        setupMediaLoader(moviesGrid, moviePagination, movieFilterForm, 'movies');
    }

    // Initialisierung für Serienseiten
    const seriesGrid = document.querySelector('#series-grid-container');
    const seriesPagination = document.querySelector('#pagination-container');
    const seriesFilterForm = document.querySelector('#series-filter-form');

    if (seriesGrid && seriesPagination && seriesFilterForm) {
        console.log("Frontend Serien-Loader wird eingerichtet.");
        setupMediaLoader(seriesGrid, seriesPagination, seriesFilterForm, 'series');
    }

    if (!moviesGrid && !seriesGrid) {
        console.log("initializeFrontendMediaLoader: Keine Frontend-Medien-Grids gefunden. Überspringe.");
    }
}
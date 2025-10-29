// C:\xampp\htdocs\files\Theater\assets\js\admin-media-manager.js
import { setupMediaLoader } from './media-loader-core.js';

/**
 * Initialisiert den Medien-Manager für Admin-Seiten (admin/movies.php, admin/series.php).
 * Stellt sicher, dass die entsprechenden Grid-Container und Formulare gefunden
 * und die Medienlade-Logik angewendet wird.
 */
export function initializeAdminMediaManager() {
    console.log("initializeAdminMediaManager wurde aufgerufen!");

    // Initialisierung für Admin Filmseiten
    const adminMoviesGrid = document.querySelector('#movies-grid-container'); // Admin-Filme nutzen auch #movies-grid-container
    const adminMoviePagination = document.querySelector('#pagination-container'); // Pagination ist im Admin auch die gleiche ID
    const adminMovieFilterForm = document.querySelector('#movie-filter-form');

    if (adminMoviesGrid && adminMoviePagination && adminMovieFilterForm) {
        console.log("Admin Film-Manager wird eingerichtet.");
        setupMediaLoader(adminMoviesGrid, adminMoviePagination, adminMovieFilterForm, 'movies');
    }

    // Initialisierung für Admin Serienseiten
    const adminSeriesGrid = document.querySelector('#series-grid-container'); // Admin-Serien nutzen auch #series-grid-container
    const adminSeriesPagination = document.querySelector('#pagination-container'); // Pagination ist im Admin auch die gleiche ID
    const adminSeriesFilterForm = document.querySelector('#series-filter-form');

    if (adminSeriesGrid && adminSeriesPagination && adminSeriesFilterForm) {
        console.log("Admin Serien-Manager wird eingerichtet.");
        setupMediaLoader(adminSeriesGrid, adminSeriesPagination, adminSeriesFilterForm, 'series');
    }

    // Wenn keine der spezifischen Grids gefunden wird, tue nichts.
    if (!adminMoviesGrid && !adminSeriesGrid) {
        console.log("initializeAdminMediaManager: Keine Admin-Medien-Grids gefunden. Überspringe.");
    }
}

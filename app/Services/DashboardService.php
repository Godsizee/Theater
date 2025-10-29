<?php
// app/Services/DashboardService.php

namespace App\Services;

use PDO;

class DashboardService
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Ruft die globalen Zähler für die Seite ab.
     */
    public function getGlobalStats(): array
    {
        $total_movies = $this->pdo->query("SELECT COUNT(MovieId) FROM Movie")->fetchColumn();
        $total_users = $this->pdo->query("SELECT COUNT(UserId) FROM User")->fetchColumn();
        $total_series = $this->pdo->query("SELECT COUNT(SeriesId) FROM Series")->fetchColumn();

        return [
            'total_movies' => $total_movies,
            'total_users' => $total_users,
            'total_series' => $total_series,
        ];
    }

    /**
     * Ruft die Zähler für fehlende Daten bei Filmen ab.
     */
    public function getMissingMovieDataStats(): array
    {
        $stmt = $this->pdo->query("
            SELECT
                COUNT(CASE WHEN Genre IS NULL OR Genre = '' THEN 1 END) AS missing_genre_count,
                COUNT(CASE WHEN Beschreibung IS NULL OR Beschreibung = '' THEN 1 END) AS missing_description_count,
                COUNT(CASE WHEN Price IS NULL OR Price <= 0 THEN 1 END) AS missing_price_count,
                COUNT(CASE WHEN PosterPath IS NULL OR PosterPath = '' OR PosterPath LIKE '%placeholder.png' THEN 1 END) AS missing_poster_count,
                COUNT(CASE WHEN Erscheinungsjahr IS NULL OR Erscheinungsjahr = 0 THEN 1 END) AS missing_year_count,
                COUNT(CASE WHEN Laufzeit IS NULL OR Laufzeit <= 0 THEN 1 END) AS missing_runtime_count,
                COUNT(CASE WHEN Regisseur IS NULL OR Regisseur = '' THEN 1 END) AS missing_director_count
            FROM Movie
        ");
        $counts = $stmt->fetch(PDO::FETCH_ASSOC);

        // Gesamtanzahl der betroffenen Filme
        $totalMissingStmt = $this->pdo->query("SELECT COUNT(DISTINCT MovieId) FROM Movie WHERE (Genre IS NULL OR Genre = '') OR (Beschreibung IS NULL OR Beschreibung = '') OR (Price IS NULL OR Price <= 0) OR (PosterPath IS NULL OR PosterPath = '' OR PosterPath LIKE '%placeholder.png') OR (Erscheinungsjahr IS NULL OR Erscheinungsjahr = 0) OR (Laufzeit IS NULL OR Laufzeit <= 0) OR (Regisseur IS NULL OR Regisseur = '')");
        $counts['total_movies_with_any_missing_data'] = $totalMissingStmt->fetchColumn();

        return $counts;
    }

    /**
     * Ruft die Zähler für fehlende Daten bei Serien ab.
     */
    public function getMissingSeriesDataStats(): array
    {
        $stmt = $this->pdo->query("
            SELECT
                COUNT(CASE WHEN Genre IS NULL OR Genre = '' THEN 1 END) AS missing_genre_count,
                COUNT(CASE WHEN Beschreibung IS NULL OR Beschreibung = '' THEN 1 END) AS missing_description_count,
                COUNT(CASE WHEN Price IS NULL OR Price <= 0 THEN 1 END) AS missing_price_count,
                COUNT(CASE WHEN PosterPath IS NULL OR PosterPath = '' OR PosterPath LIKE '%placeholder.png' THEN 1 END) AS missing_poster_count,
                COUNT(CASE WHEN Erscheinungsjahr IS NULL OR Erscheinungsjahr = 0 THEN 1 END) AS missing_start_year_count,
                COUNT(CASE WHEN Endjahr IS NULL THEN 1 END) AS missing_end_year_count,
                COUNT(CASE WHEN Staffeln IS NULL OR Staffeln <= 0 THEN 1 END) AS missing_seasons_count,
                COUNT(CASE WHEN Creator IS NULL OR Creator = '' THEN 1 END) AS missing_creator_count
            FROM Series
        ");
        $counts = $stmt->fetch(PDO::FETCH_ASSOC);

        $totalMissingStmt = $this->pdo->query("
            SELECT COUNT(DISTINCT SeriesId) FROM Series WHERE 
            (Genre IS NULL OR Genre = '') OR 
            (Beschreibung IS NULL OR Beschreibung = '') OR 
            (Price IS NULL OR Price <= 0) OR 
            (PosterPath IS NULL OR PosterPath = '' OR PosterPath LIKE '%placeholder.png') OR 
            (Erscheinungsjahr IS NULL OR Erscheinungsjahr = 0) OR 
            (Endjahr IS NULL) OR 
            (Staffeln IS NULL OR Staffeln <= 0) OR 
            (Creator IS NULL OR Creator = '')
        ");
        $counts['total_series_with_any_missing_data'] = $totalMissingStmt->fetchColumn();

        return $counts;
    }
    
    /**
     * Holt eine gefilterte Liste von Medien mit fehlenden Daten.
     */
    public function getFilteredMediaWithMissingData(string $filter): array
    {
        if (str_contains($filter, 'series')) {
            $where_clauses = [];
            $base_query = "SELECT SeriesId, Title as SeriesTitle, 
                (Genre IS NULL OR Genre = '') AS missing_genre,
                (Beschreibung IS NULL OR Beschreibung = '') AS missing_description,
                (Price IS NULL OR Price <= 0) AS missing_price,
                (PosterPath IS NULL OR PosterPath = '' OR PosterPath LIKE '%placeholder.png') AS missing_poster,
                (Erscheinungsjahr IS NULL OR Erscheinungsjahr = 0) AS missing_start_year,
                (Endjahr IS NULL) AS missing_end_year,
                (Staffeln IS NULL OR Staffeln <= 0) AS missing_seasons,
                (Creator IS NULL OR Creator = '') AS missing_creator
                FROM Series";

            switch ($filter) {
                case 'series_genre_missing': $where_clauses[] = "(Genre IS NULL OR Genre = '')"; break;
                case 'series_description_missing': $where_clauses[] = "(Beschreibung IS NULL OR Beschreibung = '')"; break;
                case 'series_price_missing': $where_clauses[] = "(Price IS NULL OR Price <= 0)"; break;
                case 'series_poster_missing': $where_clauses[] = "(PosterPath IS NULL OR PosterPath = '' OR PosterPath LIKE '%placeholder.png')"; break;
                case 'series_start_year_missing': $where_clauses[] = "(Erscheinungsjahr IS NULL OR Erscheinungsjahr = 0)"; break;
                case 'series_end_year_missing': $where_clauses[] = "(Endjahr IS NULL)"; break;
                case 'series_seasons_missing': $where_clauses[] = "(Staffeln IS NULL OR Staffeln <= 0)"; break;
                case 'series_creator_missing': $where_clauses[] = "(Creator IS NULL OR Creator = '')"; break;
                default:
                    $where_clauses[] = "((Genre IS NULL OR Genre = '') OR (Beschreibung IS NULL OR Beschreibung = '') OR (Price IS NULL OR Price <= 0) OR (PosterPath IS NULL OR PosterPath = '' OR PosterPath LIKE '%placeholder.png') OR (Erscheinungsjahr IS NULL OR Erscheinungsjahr = 0) OR (Endjahr IS NULL) OR (Staffeln IS NULL OR Staffeln <= 0) OR (Creator IS NULL OR Creator = ''))";
                    break;
            }

            $sql = $base_query . " WHERE " . implode(' AND ', $where_clauses) . " ORDER BY Title ASC";
            return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

        } else {
            $where_clauses = [];
            $base_query = "SELECT MovieId, Moviename, 
                (Genre IS NULL OR Genre = '') AS missing_genre,
                (Beschreibung IS NULL OR Beschreibung = '') AS missing_description,
                (Price IS NULL OR Price <= 0) AS missing_price,
                (PosterPath IS NULL OR PosterPath = '' OR PosterPath LIKE '%placeholder.png') AS missing_poster,
                (Erscheinungsjahr IS NULL OR Erscheinungsjahr = 0) AS missing_year,
                (Laufzeit IS NULL OR Laufzeit <= 0) AS missing_runtime,
                (Regisseur IS NULL OR Regisseur = '') AS missing_director
                FROM Movie";

            switch ($filter) {
                case 'genre_missing': $where_clauses[] = "(Genre IS NULL OR Genre = '')"; break;
                case 'description_missing': $where_clauses[] = "(Beschreibung IS NULL OR Beschreibung = '')"; break;
                case 'price_missing': $where_clauses[] = "(Price IS NULL OR Price <= 0)"; break;
                case 'poster_missing': $where_clauses[] = "(PosterPath IS NULL OR PosterPath = '' OR PosterPath LIKE '%placeholder.png')"; break;
                case 'year_missing': $where_clauses[] = "(Erscheinungsjahr IS NULL OR Erscheinungsjahr = 0)"; break;
                case 'runtime_missing': $where_clauses[] = "(Laufzeit IS NULL OR Laufzeit <= 0)"; break;
                case 'director_missing': $where_clauses[] = "(Regisseur IS NULL OR Regisseur = '')"; break;
                default:
                     $where_clauses[] = "((Genre IS NULL OR Genre = '') OR (Beschreibung IS NULL OR Beschreibung = '') OR (Price IS NULL OR Price <= 0) OR (PosterPath IS NULL OR PosterPath = '' OR PosterPath LIKE '%placeholder.png') OR (Erscheinungsjahr IS NULL OR Erscheinungsjahr = 0) OR (Laufzeit IS NULL OR Laufzeit <= 0) OR (Regisseur IS NULL OR Regisseur = ''))";
                    break;
            }

            $sql = $base_query . " WHERE " . implode(' AND ', $where_clauses) . " ORDER BY Moviename ASC";
            return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        }
    }
}

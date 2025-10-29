<?php
namespace App\Repositories;

use PDO;
use PDOException;
use Exception;

class MediaRepository
{
    private PDO $pdo;
    private array $config;

    public function __construct(PDO $pdo, array $config)
    {
        $this->pdo = $pdo;
        if (!isset($config['document_root'])) {
            $config['document_root'] = dirname(__DIR__, 2);
        }
        $this->config = $config;
    }

    private function generateSlug(string $title, string $type, ?int $excludeId = null): string
    {
        $slug = strtolower($title);
        $slug = str_replace(['ä', 'ö', 'ü', 'ß'], ['ae', 'oe', 'ue', 'ss'], $slug);
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
        $slug = trim($slug, '-');

        $table = ($type === 'movie') ? 'Movie' : 'Series';
        $idColumn = ($type === 'movie') ? 'MovieId' : 'SeriesId';
        
        $baseSlug = $slug;
        $counter = 2;
        
        while (true) {
            $sql = "SELECT {$idColumn} FROM {$table} WHERE slug = :slug";
            $params = [':slug' => $slug];
            
            if ($excludeId !== null) {
                $sql .= " AND {$idColumn} != :exclude_id";
                $params[':exclude_id'] = $excludeId;
            }
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);

            if ($stmt->fetch() === false) {
                break;
            }
            
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
    
    public function createMovie(array $data, string $posterPath): int
    {
        $slug = $this->generateSlug($data['moviename'], 'movie');

        $sql = "INSERT INTO Movie (Moviename, slug, Genre, Beschreibung, Erscheinungsjahr, Laufzeit, Regisseur, USK, Price, PosterPath) 
                VALUES (:name, :slug, :genre, :description, :year, :runtime, :director, :usk, :price, :poster)";
        
        $params = [
            ':name' => $data['moviename'],
            ':slug' => $slug,
            ':genre' => $data['genre'] ?? null,
            ':description' => $data['beschreibung'] ?? null,
            ':year' => !empty($data['erscheinungsjahr']) ? (int)$data['erscheinungsjahr'] : null,
            ':runtime' => !empty($data['laufzeit']) ? (int)$data['laufzeit'] : null,
            ':director' => $data['regisseur'] ?? null,
            ':usk' => (int)$data['usk'],
            ':price' => (float)str_replace(',', '.', $data['price']),
            ':poster' => $posterPath
        ];

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return (int)$this->pdo->lastInsertId();
        } catch (PDOException $e) {
            error_log("Fehler beim Erstellen des Films: " . $e->getMessage());
            throw new Exception("Der Film konnte nicht in der Datenbank gespeichert werden.");
        }
    }

    public function createSeries(array $data, string $posterPath): int
    {
        $slug = $this->generateSlug($data['title'], 'series');

        $sql = "INSERT INTO Series (Title, slug, Genre, Beschreibung, Erscheinungsjahr, Endjahr, Staffeln, Creator, Price, PosterPath) 
                VALUES (:title, :slug, :genre, :description, :start_year, :end_year, :seasons, :creator, :price, :poster)";
        
        $params = [
            ':title' => $data['title'],
            ':slug' => $slug,
            ':genre' => $data['genre'] ?? null,
            ':description' => $data['beschreibung'] ?? null,
            ':start_year' => !empty($data['start_year']) ? (int)$data['start_year'] : null,
            ':end_year' => !empty($data['end_year']) ? (int)$data['end_year'] : null,
            ':seasons' => !empty($data['seasons']) ? (int)$data['seasons'] : null,
            ':creator' => $data['creator'] ?? null,
            ':price' => (float)str_replace(',', '.', $data['price_per_season']),
            ':poster' => $posterPath
        ];

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return (int)$this->pdo->lastInsertId();
        } catch (PDOException $e) {
            error_log("Fehler beim Erstellen der Serie: " . $e->getMessage());
            throw new Exception("Die Serie konnte nicht in der Datenbank gespeichert werden.");
        }
    }

    public function updateMovie(int $movieId, array $data, ?string $newPosterPath = null): bool
    {
        $fields = [];
        
        // KORREKTUR: Prüft auf 'title' statt 'moviename', um mit dem Formular übereinzustimmen.
        if (isset($data['title'])) {
            $fields['Moviename'] = $data['title'];
            $fields['slug'] = $this->generateSlug($data['title'], 'movie', $movieId);
        }
        if (isset($data['usk'])) $fields['USK'] = $data['usk'];
        if (isset($data['price'])) $fields['Price'] = (float)str_replace(',', '.', $data['price']);
        if (isset($data['beschreibung'])) $fields['Beschreibung'] = $data['beschreibung'];
        if (isset($data['erscheinungsjahr'])) $fields['Erscheinungsjahr'] = $data['erscheinungsjahr'];
        if (isset($data['laufzeit'])) $fields['Laufzeit'] = $data['laufzeit'];
        if (isset($data['genre'])) $fields['Genre'] = $data['genre'];
        if (isset($data['regisseur'])) $fields['Regisseur'] = $data['regisseur'];

        if ($newPosterPath !== null) {
            $oldData = $this->findMovieOrFail($movieId);
            $oldPosterPath = $oldData['PosterPath'] ?? '';
            
            $fields['PosterPath'] = $newPosterPath;
            
            if ($oldPosterPath && !str_contains($oldPosterPath, 'placeholder.png')) {
                $fullOldPath = $this->config['document_root'] . '/' . $oldPosterPath;
                if (file_exists($fullOldPath)) {
                    @unlink($fullOldPath);
                }
            }
        }
        
        if (empty($fields)) return true;

        $updateParts = [];
        $params = [':id' => $movieId];
        foreach ($fields as $key => $value) {
            $updateParts[] = "$key = :$key";
            $params[":$key"] = $value;
        }
        
        $sql = "UPDATE Movie SET " . implode(', ', $updateParts) . " WHERE MovieId = :id";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    public function updateSeries(int $seriesId, array $data, ?string $newPosterPath = null): bool
    {
        $fields = [];

        if (isset($data['title'])) {
            $fields['Title'] = $data['title'];
            $fields['slug'] = $this->generateSlug($data['title'], 'series', $seriesId);
        }
        if (isset($data['price'])) $fields['Price'] = (float)str_replace(',', '.', $data['price']);
        if (isset($data['beschreibung'])) $fields['Beschreibung'] = $data['beschreibung'];
        if (isset($data['start_year'])) $fields['Erscheinungsjahr'] = $data['start_year'];
        if (isset($data['end_year'])) $fields['Endjahr'] = $data['end_year'];
        if (isset($data['seasons'])) $fields['Staffeln'] = $data['seasons'];
        if (isset($data['genre'])) $fields['Genre'] = $data['genre'];
        if (isset($data['creator'])) $fields['Creator'] = $data['creator'];

        if ($newPosterPath !== null) {
            $oldData = $this->findSeriesOrFail($seriesId);
            $oldPosterPath = $oldData['PosterPath'] ?? '';
            
            $fields['PosterPath'] = $newPosterPath;
            
            if ($oldPosterPath && !str_contains($oldPosterPath, 'placeholder.png')) {
                $fullOldPath = $this->config['document_root'] . '/' . $oldPosterPath;
                if (file_exists($fullOldPath)) {
                    @unlink($fullOldPath);
                }
            }
        }
        
        if (empty($fields)) return true;
        
        $updateParts = [];
        $params = [':id' => $seriesId];
        foreach ($fields as $key => $value) {
            $updateParts[] = "$key = :$key";
            $params[":$key"] = $value;
        }
        
        $sql = "UPDATE Series SET " . implode(', ', $updateParts) . " WHERE SeriesId = :id";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    public function findMovieBySlug(string $slug): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM Movie WHERE slug = :slug");
        $stmt->execute([':slug' => $slug]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function findSeriesBySlug(string $slug): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM Series WHERE slug = :slug");
        $stmt->execute([':slug' => $slug]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function deleteMovie(int $movieId): bool
    {
        $movieData = $this->findMovieOrFail($movieId);
        if (!$movieData) {
            return false;
        }

        $stmt = $this->pdo->prepare("DELETE FROM Movie WHERE MovieId = :id");
        $success = $stmt->execute([':id' => $movieId]);

        if ($success && !empty($movieData['PosterPath']) && !str_contains($movieData['PosterPath'], 'placeholder.png')) {
            $fullPosterPath = $this->config['document_root'] . '/' . $movieData['PosterPath'];
            if (file_exists($fullPosterPath)) {
                @unlink($fullPosterPath);
            }
        }
        return $success;
    }

    public function deleteSeries(int $seriesId): bool
    {
        $seriesData = $this->findSeriesOrFail($seriesId);
        if (!$seriesData) {
            return false;
        }

        $stmt = $this->pdo->prepare("DELETE FROM Series WHERE SeriesId = :id");
        $success = $stmt->execute([':id' => $seriesId]);

        if ($success && !empty($seriesData['PosterPath']) && !str_contains($seriesData['PosterPath'], 'placeholder.png')) {
            $fullPosterPath = $this->config['document_root'] . '/' . $seriesData['PosterPath'];
            if (file_exists($fullPosterPath)) {
                @unlink($fullPosterPath);
            }
        }
        return $success;
    }
    
    public function getFiltered(string $type, array $options): array
    {
        $page = $options['page'] ?? 1;
        $search_term = $options['search'] ?? '';
        $usk_filter = $options['usk'] ?? null;
        $sort_key = $options['sort'] ?? 'name_asc';
        $items_per_page = $options['items_per_page'] ?? 24;
        $genre_filter = $options['genre'] ?? '';
        $offset = ($page - 1) * $items_per_page;
        $where_clauses = [];
        $params = [];
        $table_config = $this->getTableConfig($type);
        if (!$table_config) return ['items' => [], 'total_items' => 0];
        extract($table_config);

        if (!empty($search_term)) {
            $where_clauses[] = "{$title_col} LIKE :search_term";
            $params[':search_term'] = '%' . $search_term . '%';
        }
        if ($type === 'movies' && $usk_filter !== null) {
            $where_clauses[] = "USK = :usk_filter";
            $params[':usk_filter'] = $usk_filter;
        }
        if (!empty($genre_filter)) {
            $where_clauses[] = "Genre LIKE :genre_filter";
            $params[':genre_filter'] = '%' . $genre_filter . '%';
        }

        $sql_where = !empty($where_clauses) ? ' WHERE ' . implode(' AND ', $where_clauses) : '';
        
        $order_by = "ORDER BY {$title_col} ASC";
        switch ($sort_key) {
            case 'name_desc': 
                $order_by = "ORDER BY {$title_col} DESC"; 
                break;
            case 'year_desc': 
                $order_by = "ORDER BY {$id_col} DESC"; 
                break;
            case 'year_asc': 
                $order_by = "ORDER BY {$year_col} ASC, {$title_col} ASC"; 
                break;
        }

        $stmt_count = $this->pdo->prepare("SELECT COUNT({$id_col}) FROM {$table_name}{$sql_where}");
        $stmt_count->execute($params);
        $total_items = $stmt_count->fetchColumn();

        $sql_select = "SELECT {$id_col} as id, slug, {$title_col} as title, PosterPath, {$price_col} as price, Genre";
        if ($type === 'movies') {
            $sql_select .= ", USK, Erscheinungsjahr";
        } elseif ($type === 'series') {
            $sql_select .= ", NULL AS USK, Erscheinungsjahr as start_year, Endjahr as end_year";
        }

        $stmt_items = $this->pdo->prepare("{$sql_select} FROM {$table_name}{$sql_where} {$order_by} LIMIT :limit OFFSET :offset");
        $stmt_items->bindParam(':limit', $items_per_page, PDO::PARAM_INT);
        $stmt_items->bindParam(':offset', $offset, PDO::PARAM_INT);
        foreach ($params as $key => $value) {
            $stmt_items->bindValue($key, $value);
        }
        $stmt_items->execute();
        $items = $stmt_items->fetchAll(PDO::FETCH_ASSOC);

        return ['items' => $items, 'total_items' => $total_items];
    }
    
    public function findMovieOrFail(int $movieId): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM Movie WHERE MovieId = :id");
        $stmt->execute([':id' => $movieId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function findSeriesOrFail(int $seriesId): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM Series WHERE SeriesId = :id");
        $stmt->execute([':id' => $seriesId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
    
    public function getRandomMovieId(): ?int
    {
        $stmt = $this->pdo->query("SELECT MovieId FROM Movie ORDER BY RAND() LIMIT 1");
        $result = $stmt->fetchColumn();
        return $result ? (int)$result : null;
    }
    
    public function getRecommendationsForUser(int $userId, int $limit = 4): array
    {
        $stmt = $this->pdo->prepare("SELECT MovieId as id, Moviename as title, PosterPath, Genre, slug FROM Movie ORDER BY RAND() LIMIT :limit");
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    private function getTableConfig(string $type): ?array
    {
        $config = [
            'movies' => ['table_name' => 'Movie', 'id_col' => 'MovieId', 'title_col'  => 'Moviename', 'year_col' => 'Erscheinungsjahr', 'price_col' => 'Price'],
            'series' => ['table_name' => 'Series', 'id_col' => 'SeriesId', 'title_col' => 'Title', 'year_col' => 'Erscheinungsjahr', 'price_col' => 'Price']
        ];
        return $config[$type] ?? null;
    }

    public function getAllUniqueGenres(): array
    {
        $movie_genres = [];
        $series_genres = [];

        $stmt_movies = $this->pdo->query("SELECT Genre FROM Movie WHERE Genre IS NOT NULL AND Genre != ''");
        $movie_genre_strings = $stmt_movies->fetchAll(PDO::FETCH_COLUMN);
        foreach ($movie_genre_strings as $genre_string) {
            $genres = explode(',', $genre_string);
            foreach ($genres as $genre) {
                $cleaned_genre = trim($genre);
                if (!empty($cleaned_genre)) {
                    $movie_genres[$cleaned_genre] = true;
                }
            }
        }

        $stmt_series = $this->pdo->query("SELECT Genre FROM Series WHERE Genre IS NOT NULL AND Genre != ''");
        $series_genre_strings = $stmt_series->fetchAll(PDO::FETCH_COLUMN);
        foreach ($series_genre_strings as $genre_string) {
            $genres = explode(',', $genre_string);
            foreach ($genres as $genre) {
                $cleaned_genre = trim($genre);
                if (!empty($cleaned_genre)) {
                    $series_genres[$cleaned_genre] = true;
                }
            }
        }
        
        $unique_movie_genres = array_keys($movie_genres);
        sort($unique_movie_genres);

        $unique_series_genres = array_keys($series_genres);
        sort($unique_series_genres);

        return [
            'movie_genres' => $unique_movie_genres,
            'series_genres' => $unique_series_genres,
        ];
    }
    
    public function getEpisodesForSeries(int $seriesId): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT SeasonNumber, EpisodeNumber, Title, Description, Laufzeit
             FROM Episode
             WHERE SeriesId = :series_id
             ORDER BY SeasonNumber, EpisodeNumber"
        );
        $stmt->execute([':series_id' => $seriesId]);
        $episodes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $seasons = [];
        foreach ($episodes as $episode) {
            $seasons[$episode['SeasonNumber']][] = $episode;
        }
        return $seasons;
    }

    public function getHomepageSections(): array
    {
        $sections = [
            'featured' => null,
            'top_rated' => [],
            'classics' => []
        ];

        $stmt_featured = $this->pdo->query("SELECT MovieId, slug, Moviename, PosterPath, Beschreibung FROM Movie WHERE slug IS NOT NULL ORDER BY MovieId DESC LIMIT 1");
        if ($stmt_featured) {
            $sections['featured'] = $stmt_featured->fetch(PDO::FETCH_ASSOC);
        }

        $stmt_top_rated = $this->pdo->query("SELECT MovieId, slug, Moviename, PosterPath, Genre FROM Movie ORDER BY Price DESC LIMIT 5");
        if ($stmt_top_rated) {
            $sections['top_rated'] = $stmt_top_rated->fetchAll(PDO::FETCH_ASSOC);
        }

        $stmt_classics = $this->pdo->query("SELECT MovieId, slug, Moviename, PosterPath, Genre FROM Movie WHERE Erscheinungsjahr < 2015 ORDER BY RAND() LIMIT 5");
        if ($stmt_classics) {
            $sections['classics'] = $stmt_classics->fetchAll(PDO::FETCH_ASSOC);
        }

        return $sections;
    }
}
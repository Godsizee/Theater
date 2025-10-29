<?php

namespace App\Http\Controllers;

use App\Repositories\MediaRepository;
use Exception;
use PDO;

class MediaController
{
    private PDO $pdo;
    private array $config;
    private MediaRepository $mediaRepository;

    public function __construct()
    {
        $this->pdo = \App\Core\Database::getInstance();
        $this->config = \App\Core\Database::getConfig();
        $this->mediaRepository = new MediaRepository($this->pdo, $this->config);
    }

    /**
     * Zeigt die Film-Übersichtsseite an.
     */
    public function showMovies()
    {
        global $config;
        $config = $this->config;

        $page_title = 'Alle Filme';
        $body_class = ''; 
        $page_type = 'movies';
        $form_action = $this->config['base_url'] . '/select';

        require_once dirname(__DIR__, 3) . '/templates/_media_grid_layout.php';
    }

    /**
     * Zeigt die Serien-Übersichtsseite an.
     */
    public function showSeries()
    {
        global $config;
        $config = $this->config;

        $page_title = 'Alle Serien';
        $body_class = ''; 
        $page_type = 'series';
        $form_action = $this->config['base_url'] . '/series';

        require_once dirname(__DIR__, 3) . '/templates/_media_grid_layout.php';
    }

    /**
     * Zeigt die Genre-Übersichtsseite an.
     */
    public function showGenres()
    {
        global $config, $pdo;
        $config = $this->config;
        $pdo = $this->pdo;

        $page_title = 'Nach Genre stöbern';
        $movie_genres = [];
        $series_genres = [];
        $message = '';

        try {
            $genres_data = $this->mediaRepository->getAllUniqueGenres(); 
            $movie_genres = $genres_data['movie_genres'] ?? [];
            $series_genres = $genres_data['series_genres'] ?? [];
        } catch (Exception $e) {
            error_log("Fehler beim Abrufen der Genres: " . $e->getMessage());
            $message = "Die Genres konnten nicht geladen werden. Bitte versuchen Sie es später erneut.";
        }

        require_once dirname(__DIR__, 3) . '/pages/media/genres.php';
    }

    /**
     * NEU: Leitet zu einem zufälligen Film weiter.
     */
    public function showRandom()
    {
        try {
            $random_movie_id = $this->mediaRepository->getRandomMovieId();
            
            if ($random_movie_id) {
                $movieData = $this->mediaRepository->findMovieOrFail($random_movie_id);
                
                if ($movieData && !empty($movieData['slug'])) {
                     header("Location: " . $this->config['base_url'] . "/movie/" . $movieData['slug']);
                } else {
                     // Fallback, falls kein Slug vorhanden ist.
                     header("Location: " . $this->config['base_url'] . "/select");
                }
                exit(); 
            } else {
                $_SESSION['message'] = "Es sind keine Filme vorhanden, um einen zufälligen auszuwählen.";
                header("Location: " . $this->config['base_url'] . "/select");
                exit();
            }

        } catch (Exception $e) {
            error_log("Fehler bei der Zufallsfilm-Abfrage: " . $e->getMessage());
            $_SESSION['message'] = "Ein Fehler ist aufgetreten. Bitte versuchen Sie es später erneut.";
            header("Location: " . $this->config['base_url'] . "/select");
            exit();
        }
    }
}

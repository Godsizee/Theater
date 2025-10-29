<?php

namespace App\Http\Controllers;

use App\Repositories\MediaRepository;
use Exception;
use PDO;

class HomeController
{
    private PDO $pdo;
    private array $config;

    public function __construct()
    {
        $this->pdo = \App\Core\Database::getInstance();
        $this->config = \App\Core\Database::getConfig();
    }

    /**
     * Zeigt die Startseite an.
     */
    public function show()
    {
        // Globale Variablen für die Views verfügbar machen
        global $config, $pdo;
        $config = $this->config;
        $pdo = $this->pdo;

        $flash_message_for_script = null;
        if (isset($_SESSION['flash_message'])) {
            $flash_message_for_script = $_SESSION['flash_message'];
            unset($_SESSION['flash_message']);
        }

        $settings = \App\Core\Utils::getSettings();
        $featured_movie = null;
        $top_rated_movies = [];
        $classic_movies = [];

        try {
            $mediaRepository = new MediaRepository($this->pdo, $this->config);
            $homepageSections = $mediaRepository->getHomepageSections();
            
            $featured_movie = $homepageSections['featured'];
            $top_rated_movies = $homepageSections['top_rated'];
            $classic_movies = $homepageSections['classics'];

        } catch (Exception $e) {
            error_log("Fehler beim Laden der Filme für die Startseite: " . $e->getMessage());
        }

        $hero_image_path = $this->config['base_url'] . '/public/assets/images/default-hero.jpg';
        if ($featured_movie && !empty($featured_movie['PosterPath'])) {
            $hero_image_path = $this->config['base_url'] . '/' . htmlspecialchars($featured_movie['PosterPath']);
        }

        $page_title = 'Willkommen im ' . htmlspecialchars($settings['site_title']);
        $body_class = 'home-page';

        require_once dirname(__DIR__, 3) . '/pages/home.php';
    }
}

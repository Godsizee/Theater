<?php
// random.php (Refactored)

// Die zentrale init.php startet die Session und stellt die DB-Verbindung her.
require_once dirname(__DIR__, 2) . '/init.php';

try {
    $mediaRepository = new \App\Repositories\MediaRepository($pdo, $config);
    $random_movie_id = $mediaRepository->getRandomMovieId();
    
    if ($random_movie_id) {
        // Hole die kompletten Filmdaten, um den Slug für die saubere URL zu bekommen.
        $movieData = $mediaRepository->findMovieOrFail($random_movie_id);
        
        // Leite zur neuen Slug-basierten URL um, falls vorhanden.
        if ($movieData && !empty($movieData['slug'])) {
             header("Location: " . $config['base_url'] . "/movie/" . $movieData['slug']);
        } else {
             // Fallback, falls aus irgendeinem Grund kein Slug vorhanden ist.
             header("Location: movie_details.php?id=" . $random_movie_id);
        }
        exit(); 
    } else {
        // Fall, dass keine Filme in der Datenbank existieren.
        $_SESSION['message'] = "Es sind keine Filme vorhanden, um einen zufälligen auszuwählen.";
        header("Location: " . $config['base_url'] . "/select");
        exit();
    }

} catch (Exception $e) {
    error_log("Fehler bei der Zufallsfilm-Abfrage: " . $e->getMessage());
    $_SESSION['message'] = "Ein Fehler ist aufgetreten. Bitte versuchen Sie es später erneut.";
    header("Location: " . $config['base_url'] . "/select");
    exit();
}
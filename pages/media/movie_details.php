<?php
// pages/media/movie_details.php

require_once dirname(__DIR__, 2) . '/init.php';

$slug = $matches[0] ?? null;

if (!$slug) {
    header("Location: " . $config['base_url'] . "/select");
    exit();
}

$mediaRepository = new App\Repositories\MediaRepository($pdo, $config);
$movie = $mediaRepository->findMovieBySlug($slug);

if (!$movie) {
    header("Location: " . $config['base_url'] . "/select");
    exit();
}

$page_title = $movie['Moviename'];
$media_type = 'movie';

// Daten für das Template vereinheitlichen
$media = [
    'id' => $movie['MovieId'],
    'title' => $movie['Moviename'],
    'USK' => $movie['USK'],
    'price' => $movie['Price'],
    'PosterPath' => $movie['PosterPath'],
    'Beschreibung' => $movie['Beschreibung'],
    'Erscheinungsjahr' => $movie['Erscheinungsjahr'],
    'Laufzeit' => $movie['Laufzeit'],
    'Genre' => $movie['Genre'],
    'Regisseur' => $movie['Regisseur']
];

// KORREKTUR: Das korrekte Layout für die Detailansicht wird hier eingebunden.
include dirname(__DIR__, 2) . '/templates/_media_details_layout.php';
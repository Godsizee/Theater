<?php
// pages/media/series_details.php

require_once dirname(__DIR__, 2) . '/init.php';

$slug = $matches[0] ?? null;

if (!$slug) {
    header("Location: " . $config['base_url'] . "/series");
    exit();
}

$mediaRepository = new App\Repositories\MediaRepository($pdo, $config);
$series_data = $mediaRepository->findSeriesBySlug($slug);

if (!$series_data) {
    header("Location: " . $config['base_url'] . "/series");
    exit();
}

$seasons = $mediaRepository->getEpisodesForSeries((int)$series_data['SeriesId']);

$page_title = $series_data['Title'];
$media_type = 'series';

// Daten für das Template vereinheitlichen
$media = [
    'id' => $series_data['SeriesId'],
    'title' => $series_data['Title'],
    'USK' => null,
    'price' => $series_data['Price'],
    'PosterPath' => $series_data['PosterPath'],
    'Beschreibung' => $series_data['Beschreibung'],
    'StartYear' => $series_data['Erscheinungsjahr'],
    'EndYear' => $series_data['Endjahr'],
    'NumberOfSeasons' => $series_data['Staffeln'],
    'Genre' => $series_data['Genre'],
    'Creator' => $series_data['Creator'] ?? 'N/A' // Fallback für Serienschöpfer hinzugefügt
];

// KORREKTUR: Das korrekte Layout für die Detailansicht wird hier eingebunden.
include dirname(__DIR__, 2) . '/templates/_media_details_layout.php';
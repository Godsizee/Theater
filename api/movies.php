<?php
// api/movies.php (Fixed)

require_once __DIR__ . '/../init.php';

// Setzt den Content-Type Header, damit der Browser weiß, dass er JSON erwartet.
header('Content-Type: application/json');

try {
    $mediaRepository = new App\Repositories\MediaRepository($pdo, $config);
    // KORREKTUR: Die Funktion wird jetzt über die Utils-Klasse aufgerufen.
    $settings = \App\Core\Utils::getSettings();
    
    $options = [
        'page' => isset($_GET['page']) ? (int)$_GET['page'] : 1,
        'search' => trim($_GET['search'] ?? ''),
        'usk' => isset($_GET['usk']) && $_GET['usk'] !== '' ? (int)$_GET['usk'] : null,
        'sort' => $_GET['sort'] ?? $settings['default_sort_order'],
        'items_per_page' => $settings['items_per_page'] ?? 24,
        'genre' => trim($_GET['genre'] ?? '')
    ];
    
    // Die Methode getFiltered() gibt bereits ein Array mit 'items' und 'total_items' zurück.
    $result = $mediaRepository->getFiltered('movies', $options);
    
    // Berechne die Gesamtanzahl der Seiten für die Paginierung
    $total_pages = ($options['items_per_page'] > 0) ? ceil($result['total_items'] / $options['items_per_page']) : 0;

    // Erstelle ein sauberes Antwort-Array
    $response = [
        'success' => true,
        'data' => [
            'items' => $result['items'],
            'pagination' => [
                'currentPage' => $options['page'],
                'totalPages' => $total_pages,
                'totalItems' => $result['total_items']
            ]
        ]
    ];

} catch (Exception $e) {
    // Bei einem Fehler eine standardisierte Fehlermeldung senden
    http_response_code(500);
    $response = [
        'success' => false,
        'message' => 'Ein interner Serverfehler ist aufgetreten.',
        'error' => $e->getMessage() // Nur für die Entwicklung nützlich
    ];
}

// Gib das Array als JSON-String aus und beende das Skript.
echo json_encode($response);
exit();

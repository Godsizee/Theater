<?php
// api/series.php (Fixed)

require_once __DIR__ . '/../init.php';

header('Content-Type: application/json');
$response = ['success' => false, 'message' => 'Ein Fehler ist aufgetreten.'];

try {
    // Das $config-Array wird für den Poster-Pfad benötigt
    $mediaRepository = new App\Repositories\MediaRepository($pdo, $config); 
    // KORREKTUR: Die Funktion wird jetzt über die Utils-Klasse aufgerufen.
    $settings = \App\Core\Utils::getSettings();
    
    $options = [
        'page' => isset($_GET['page']) ? (int)$_GET['page'] : 1,
        'search' => trim($_GET['search'] ?? ''),
        'usk' => null, // USK wird für Serien ignoriert
        'sort' => $_GET['sort'] ?? $settings['default_sort_order'],
        'items_per_page' => $settings['items_per_page'] ?? 24,
        'genre' => trim($_GET['genre'] ?? '')
    ];
    
    $result = $mediaRepository->getFiltered('series', $options);
    
    $total_pages = ($options['items_per_page'] > 0) ? ceil($result['total_items'] / $options['items_per_page']) : 0;

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
    http_response_code(500);
    $response['message'] = 'Ein interner Serverfehler ist aufgetreten.';
    // Im produktiven Betrieb sollte die folgende Zeile auskommentiert werden
    $response['error_details'] = $e->getMessage();
}

echo json_encode($response);
exit();
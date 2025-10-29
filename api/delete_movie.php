<?php
// api/delete_movie.php (CORRECTED)

header('Content-Type: application/json');
require_once __DIR__ . '/../init.php';

\App\Core\Security::requireAdmin();

$response = ['success' => false, 'message' => 'Ein unbekannter Fehler ist aufgetreten.'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = 'Ungültige Anfrage-Methode.';
    http_response_code(405);
    echo json_encode($response);
    exit();
}

$movie_id = $_POST['id'] ?? null;
if (!$movie_id || !filter_var($movie_id, FILTER_VALIDATE_INT)) {
    $response['message'] = 'Ungültige oder fehlende Film-ID.';
    http_response_code(400);
    echo json_encode($response);
    exit();
}

try {
    // Services und Repositories instanziieren
    $mediaRepository = new \App\Repositories\MediaRepository($pdo, $config);
    $auditLogRepository = new \App\Repositories\AuditLogRepository($pdo);
    // NEU: Der ImageUploadService wird ebenfalls benötigt
    $imageUploadService = new \App\Services\ImageUploadService($config); 
    // KORREKTUR: Der Service wird jetzt mit allen drei benötigten Argumenten instanziiert
    $mediaService = new \App\Services\MediaManagementService($mediaRepository, $auditLogRepository, $imageUploadService);

    // Ein einziger Aufruf an den Service
    $deletedTitle = $mediaService->deleteMedia('movie', (int)$movie_id, $_SESSION['user_id']);

    $response['success'] = true;
    $response['message'] = 'Film "' . htmlspecialchars($deletedTitle) . '" erfolgreich gelöscht.';

} catch (Exception $e) {
    error_log("Fehler beim Löschen des Films (API): " . $e->getMessage());
    $response['message'] = 'Ein interner Fehler ist aufgetreten: ' . $e->getMessage();
    http_response_code(500);
}

echo json_encode($response);
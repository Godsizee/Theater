<?php
// api/delete_user.php (CORRECTED)
require_once __DIR__ . '/../init.php';
header('Content-Type: application/json');

// KORREKTUR: Veralteten Include entfernt und neue Security-Klasse verwendet
// require_once __DIR__ . '/../src/auth_check.php'; // ALT & FEHLERHAFT
\App\Core\Security::requireAdmin(); // NEU & KORREKT

$response = ['success' => false, 'message' => 'Ein unbekannter Fehler ist aufgetreten.'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = 'Ungültige Anfrage-Methode.';
    http_response_code(405); // Method Not Allowed
    echo json_encode($response);
    exit();
}

$user_id = $_POST['id'] ?? null;
if (!$user_id || !filter_var($user_id, FILTER_VALIDATE_INT)) {
    $response['message'] = 'Ungültige oder fehlende User-ID.';
    http_response_code(400); // Bad Request
    echo json_encode($response);
    exit();
}

try {
    // Repositories instanziieren
    $userRepository = new \App\Repositories\UserRepository($pdo);
    $logRepository = new \App\Repositories\AuditLogRepository($pdo);

    // Benutzerdaten für das Logging VOR dem Löschen holen
    $user_data = $userRepository->find((int)$user_id);

    if (!$user_data) {
        throw new Exception("Benutzer nicht gefunden.");
    }
    
    // Repository-Methode zum Löschen verwenden
    $delete_success = $userRepository->delete((int)$user_id);

    if ($delete_success) {
        // Logging erfolgt nach erfolgreichem Löschen
        $logRepository->log($_SESSION['user_id'], 'BENUTZER_GELOESCHT', ['UserId' => $user_id, 'Username' => $user_data['Username']]);
        
        $response['success'] = true;
        $response['message'] = 'Benutzer erfolgreich gelöscht.';
    } else {
        // Dieser Fall sollte durch die Prüfung oben eigentlich nicht eintreten
        $response['message'] = 'Benutzer konnte nicht gelöscht werden.';
        http_response_code(500);
    }

} catch (Exception $e) {
    error_log("Fehler beim Löschen des Benutzers (API): " . $e->getMessage());
    $response['message'] = 'Ein Datenbankfehler ist aufgetreten: ' . $e->getMessage();
    http_response_code(500); // Internal Server Error
}

echo json_encode($response);
?>
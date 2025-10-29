<?php
require_once __DIR__ . '/../init.php';
header('Content-Type: application/json');

$response = ['success' => false, 'message' => 'Ein unbekannter Fehler ist aufgetreten.'];

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    $response['message'] = 'Bitte melden Sie sich an, um eine Bestellung aufzugeben.';
    echo json_encode($response);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    $response['message'] = 'Ungültige Anfrage-Methode.';
    echo json_encode($response);
    exit();
}

$cart_items = json_decode(file_get_contents('php://input'), true);

if (empty($cart_items)) {
    http_response_code(400);
    $response['message'] = 'Der Warenkorb ist leer.';
    echo json_encode($response);
    exit();
}

try {
    // KORREKTUR: Beide Repositories werden jetzt korrekt instanziiert.
    $auditLogRepository = new \App\Repositories\AuditLogRepository($pdo);
    $ticketRepository = new \App\Repositories\TicketRepository($pdo, $auditLogRepository);
    
    $success = $ticketRepository->createOrderForUser($_SESSION['user_id'], $cart_items);

    if ($success) {
        $response = ['success' => true, 'message' => 'Bestellung erfolgreich! Sie werden weitergeleitet.'];
    } else {
        throw new Exception("Die Bestellung konnte nicht erstellt werden, eventuell war der Warenkorb leer oder enthielt ungültige Artikel.");
    }

} catch (Exception $e) {
    http_response_code(500);
    $response['message'] = 'Ihre Bestellung konnte nicht verarbeitet werden: ' . $e->getMessage();
}

echo json_encode($response);
exit();
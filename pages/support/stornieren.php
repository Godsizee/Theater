<?php
// Output-Buffering starten. Alle Ausgaben werden "gesammelt" statt direkt gesendet.
ob_start();

require_once dirname(__DIR__, 2) . '/init.php';

// WICHTIG: Die header()-Funktion muss vor jeglicher Ausgabe stehen.
header('Content-Type: application/json');

$response = ['success' => false, 'message' => 'Ein unbekannter Fehler ist aufgetreten.'];

try {
    // API-sichere Authentifizierung
    \App\Core\Security::requireApiSupport();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Ungültige Anfrage-Methode.', 405);
    }

    $ticket_id = filter_input(INPUT_POST, 'ticket_id', FILTER_VALIDATE_INT);
    if (!$ticket_id) {
        throw new Exception('Ungültige oder fehlende Ticket-ID.', 400);
    }

    $auditLogRepository = new \App\Repositories\AuditLogRepository($pdo);
    $ticketRepository = new \App\Repositories\TicketRepository($pdo, $auditLogRepository);

    $success = $ticketRepository->cancel($ticket_id, $_SESSION['user_id']);

    if ($success) {
        $response['success'] = true;
        $response['message'] = 'Bestellung wurde erfolgreich storniert.';
    } else {
        // Wir werfen eine Exception, um die Fehlerbehandlung zu zentralisieren
        throw new Exception('Bestellung konnte nicht storniert werden (evtl. bereits bezahlt oder schon storniert).', 400);
    }

} catch (Exception $e) {
    // Setzt den HTTP-Statuscode aus der Exception oder standardmäßig 500
    $statusCode = ($e->getCode() >= 400 && $e->getCode() < 600) ? $e->getCode() : 500;
    http_response_code($statusCode);
    
    $response['message'] = $e->getMessage();
    error_log('Fehler bei der Stornierung (Ticket: ' . ($ticket_id ?? 'N/A') . '): ' . $e->getMessage());
}

// Alle bisherigen (unerwünschten) Ausgaben aus dem Buffer löschen
ob_end_clean();

// Jetzt wird garantiert nur noch unsere JSON-Antwort gesendet
echo json_encode($response);
exit();
<?php
// support/logic/kundendetails_logic.php (Refactored)

// Die Datenbankverbindung wird bereits von der aufrufenden Seite 
// (z.B. kundendetails.php) über init.php hergestellt. 
// Ein erneutes Einbinden ist nicht nötig und kann zu Fehlern führen.

// Authentifizierung prüfen: Hat der Nutzer die nötigen Rechte?
include_once dirname(__DIR__, 2) .'/../../src/support_auth_check.php';

// Die User-ID aus der URL holen und validieren
$user_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

// Wenn keine gültige ID vorhanden ist, zur Kundenübersicht umleiten
if (!$user_id) {
    header("Location: kunden.php");
    exit();
}

try {
    // Die kompletten Daten des Kunden aus der Datenbank abrufen
    $stmt = $pdo->prepare(
       "SELECT u.UserId, u.Username, u.EMail, u.Rolle, u.Birthday, k.*
        FROM User u
        LEFT JOIN Kunde k ON u.UserId = k.UserId
        WHERE u.UserId = :user_id"
    );
    $stmt->execute([':user_id' => $user_id]);
    $customer = $stmt->fetch(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Bei einem Datenbankfehler eine Nachricht loggen und den Nutzer umleiten
    error_log("Fehler beim Laden der Kundendetails: " . $e->getMessage());
    $customer = null;
}

// Wenn kein Kunde für die ID gefunden wurde, ebenfalls umleiten
if (!$customer) {
    $_SESSION['message'] = "Kunde nicht gefunden.";
    $_SESSION['message_type'] = "error";
    header("Location: kunden.php");
    exit();
}
<?php
require_once dirname(__DIR__, 2) . '/init.php';

// Zentrale Sicherheitsprüfung
\App\Core\Security::requireAdmin();

// Repository instanziieren
$auditLogRepository = new \App\Repositories\AuditLogRepository($pdo);

// Dieselben Filter wie auf der Anzeigeseite verwenden
$filters = [
    'user_id'     => filter_input(INPUT_GET, 'user', FILTER_VALIDATE_INT),
    'action_type' => trim($_GET['action'] ?? '')
];

// Hole alle gefilterten Logs (ohne Limit, daher 'null')
$logs_stmt = $auditLogRepository->getFilteredLogs(array_filter($filters), null);
$logs = $logs_stmt->fetchAll(PDO::FETCH_ASSOC);

// Bereite den Text-Output für die Datei vor
$output = "Aktivitaetsprotokoll - Export vom " . date('d.m.Y H:i:s') . "\n";
$output .= str_repeat("=", 70) . "\n\n";

if (empty($logs)) {
    $output .= "Keine Eintraege fuer die ausgewaehlten Filter gefunden.\n";
} else {
    foreach ($logs as $log) {
        $output .= "Zeitstempel: " . $log['Timestamp'] . "\n";
        $output .= "Mitarbeiter:  " . $log['Username'] . "\n";
        $output .= "Aktion:       " . $log['Aktionstyp'] . "\n";
        
        $details_text = "Rohdaten: " . $log['Details'];
        $details_json = json_decode($log['Details'], true);
        
        // Versuche, die JSON-Details lesbar zu formatieren
        if (json_last_error() === JSON_ERROR_NONE) {
            $formatted_details = [];
            foreach ($details_json as $key => $value) {
                $formatted_details[] = "$key: $value";
            }
            $details_text = implode(", ", $formatted_details);
        }
        
        $output .= "Details:      " . $details_text . "\n";
        $output .= str_repeat("-", 70) . "\n";
    }
}

// Setzt die Header für den Datei-Download
header('Content-Type: text/plain; charset=utf-8');
header('Content-Disposition: attachment; filename="audit_log_' . date('Y-m-d') . '.txt"');

echo $output;
exit();
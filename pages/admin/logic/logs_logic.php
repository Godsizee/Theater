<?php
// logic/logs_logic.php

// Holt die Mitarbeiter und Aktionstypen für die Filter-Dropdowns
$staff_users = $pdo->query("SELECT UserId, Username FROM User WHERE Rolle IN ('admin', 'co-admin', 'kundendienst') ORDER BY Username")->fetchAll();
$action_types = $pdo->query("SELECT DISTINCT Aktionstyp FROM AuditLog ORDER BY Aktionstyp")->fetchAll(PDO::FETCH_COLUMN);

// Verarbeitet die Filter-Parameter aus der URL
$filter_user_id = filter_input(INPUT_GET, 'user', FILTER_VALIDATE_INT);
$submitted_action = trim($_GET['action'] ?? '');
$filter_action_type = '';
if (in_array($submitted_action, $action_types, true)) {
    $filter_action_type = $submitted_action;
}

// Baut die SQL-Abfrage dynamisch zusammen
$where_clauses = [];
$params = [];

if ($filter_user_id) {
    $where_clauses[] = "l.AusfuehrenderUserId = :user_id";
    $params[':user_id'] = $filter_user_id;
}
if ($filter_action_type) {
    $where_clauses[] = "l.Aktionstyp = :action_type";
    $params[':action_type'] = $filter_action_type;
}

$sql = "SELECT l.Timestamp, l.Aktionstyp, l.Details, u.Username 
        FROM AuditLog l
        JOIN User u ON l.AusfuehrenderUserId = u.UserId";

if (!empty($where_clauses)) {
    $sql .= " WHERE " . implode(" AND ", $where_clauses);
}
$sql .= " ORDER BY l.Timestamp DESC LIMIT 200";

// Führt die Abfrage aus und speichert das Ergebnis in $logs
$logs = $pdo->prepare($sql);
$logs->execute($params);
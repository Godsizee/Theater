<?php
namespace App\Repositories;

use PDO;
use PDOException;
use PDOStatement;

class AuditLogRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function log(int $userId, string $actionType, array $details = []): bool
    {
        try {
            $stmt = $this->pdo->prepare(
                "INSERT INTO AuditLog (AusfuehrenderUserId, Aktionstyp, Details) VALUES (:user_id, :action_type, :details)"
            );
            
            return $stmt->execute([
                ':user_id' => $userId,
                ':action_type' => $actionType,
                ':details' => json_encode($details)
            ]);
        } catch (PDOException $e) {
            error_log("Fehler beim Erstellen des Audit-Logs: " . $e->getMessage());
            return false;
        }
    }

    public function getFilteredLogs(array $filters = [], ?int $limit = 200): PDOStatement
    {
        $where_clauses = [];
        $params = [];

        if (!empty($filters['user_id'])) {
            $where_clauses[] = "l.AusfuehrenderUserId = :user_id";
            $params[':user_id'] = $filters['user_id'];
        }
        if (!empty($filters['action_type'])) {
            $where_clauses[] = "l.Aktionstyp = :action_type";
            $params[':action_type'] = $filters['action_type'];
        }

        $sql = "SELECT l.Timestamp, l.Aktionstyp, l.Details, u.Username 
                FROM AuditLog l
                JOIN User u ON l.AusfuehrenderUserId = u.UserId";

        if (!empty($where_clauses)) {
            $sql .= " WHERE " . implode(" AND ", $where_clauses);
        }

        $sql .= " ORDER BY l.Timestamp DESC";

        if ($limit !== null) {
            $sql .= " LIMIT :limit";
            // PDO kann keine Zahlen an LIMIT binden, daher muss es direkt in den String
            // Aber wir stellen sicher, dass es ein Integer ist.
            $sql = str_replace(':limit', (int)$limit, $sql);
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    // NEU: Holt alle einzigartigen Aktionstypen für das Filter-Dropdown.
    public function getDistinctActionTypes(): array
    {
        $stmt = $this->pdo->query("SELECT DISTINCT Aktionstyp FROM AuditLog ORDER BY Aktionstyp");
        return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    }
}
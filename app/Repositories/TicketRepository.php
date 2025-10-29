<?php
// app/Repositories/TicketRepository.php

namespace App\Repositories;

use PDO;
use PDOException;
use Exception;

/**
 * Verantwortlich für alle Datenbankabfragen, die Tickets/Bestellungen betreffen.
 * Angepasst für eine polymorphe Beziehung zu Filmen und Serien.
 */
class TicketRepository
{
    private PDO $pdo;
    private AuditLogRepository $auditLogRepository;

    public function __construct(PDO $pdo, AuditLogRepository $auditLogRepository)
    {
        $this->pdo = $pdo;
        $this->auditLogRepository = $auditLogRepository;
    }

    /**
     * Findet ein einzelnes Ticket anhand seiner ID und gibt die zugehörigen
     * Medien- und Kundendaten zurück.
     */
    public function find(int $ticketId): ?array
    {
        $stmt = $this->pdo->prepare(
           "SELECT 
                t.*, t.Zeitstempel AS Bestelldatum,
                k.UserId, k.Vorname, k.Nachname, k.Strasse, k.Hausnummer, k.PLZ, k.Ort, u.Username,
                COALESCE(m.Moviename, s.Title) AS Titel,
                COALESCE(m.PosterPath, s.PosterPath) AS PosterPath,
                COALESCE(m.Price, s.Price) AS Price,
                COALESCE(m.Genre, s.Genre) AS Genre,
                m.Erscheinungsjahr, m.Laufzeit, m.Regisseur,
                s.Erscheinungsjahr AS Startjahr, s.Endjahr
            FROM Ticket t
            JOIN Kunde k ON t.KundeId = k.KundeId
            LEFT JOIN User u ON k.UserId = u.UserId
            LEFT JOIN Movie m ON t.ProduktId = m.MovieId AND t.ProduktTyp = 'movie'
            LEFT JOIN Series s ON t.ProduktId = s.SeriesId AND t.ProduktTyp = 'series'
            WHERE t.TicketId = :ticket_id"
        );
        $stmt->execute([':ticket_id' => $ticketId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Findet ein einzelnes Ticket für einen bestimmten Benutzer.
     */
    public function findForUser(int $ticketId, int $userId): ?array
    {
        $stmt = $this->pdo->prepare(
           "SELECT 
                t.*, t.Zeitstempel AS Bestelldatum,
                k.UserId, k.Vorname, k.Nachname, k.Strasse, k.Hausnummer, k.PLZ, k.Ort,
                COALESCE(m.MovieId, s.SeriesId) AS MediaId,
                -- KORREKTUR: Der Alias wurde von Moviename auf Titel geändert, um konsistent zu sein.
                COALESCE(m.Moviename, s.Title) AS Titel, 
                COALESCE(m.PosterPath, s.PosterPath) AS PosterPath,
                COALESCE(m.Price, s.Price) AS Price,
                COALESCE(m.Genre, s.Genre) AS Genre,
                m.Erscheinungsjahr, m.Laufzeit, m.Regisseur
            FROM Ticket t
            JOIN Kunde k ON t.KundeId = k.KundeId
            LEFT JOIN Movie m ON t.ProduktId = m.MovieId AND t.ProduktTyp = 'movie'
            LEFT JOIN Series s ON t.ProduktId = s.SeriesId AND t.ProduktTyp = 'series'
            WHERE t.TicketId = :ticket_id AND k.UserId = :user_id"
        );
        $stmt->execute([':ticket_id' => $ticketId, ':user_id' => $userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Ruft alle Tickets/Bestellungen für eine bestimmte Kunden-ID ab.
     */
    public function findByCustomerId(int $kundeId): array
    {
        $stmt = $this->pdo->prepare(
           "SELECT 
                t.TicketId, t.Zeitstempel AS Bestelldatum, t.EndDatum, t.Zahlungsstatus,
                COALESCE(m.Moviename, s.Title) AS Moviename,
                COALESCE(m.PosterPath, s.PosterPath) AS PosterPath,
                COALESCE(m.Price, s.Price) AS Total
            FROM Ticket t
            LEFT JOIN Movie m ON t.ProduktId = m.MovieId AND t.ProduktTyp = 'movie'
            LEFT JOIN Series s ON t.ProduktId = s.SeriesId AND t.ProduktTyp = 'series'
            WHERE t.KundeId = :kunde_id
            ORDER BY t.Zeitstempel DESC"
        );
        $stmt->execute([':kunde_id' => $kundeId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Aktualisiert die Daten einer Bestellung.
     */
    public function update(int $ticketId, string $endDate, string $status): bool
    {
        $stmt = $this->pdo->prepare(
            "UPDATE Ticket SET EndDatum = :end_datum, Zahlungsstatus = :status WHERE TicketId = :ticket_id"
        );
        return $stmt->execute([
            ':end_datum' => $endDate,
            ':status' => $status,
            ':ticket_id' => $ticketId
        ]);
    }

    /**
     * Storniert eine Bestellung und loggt die Aktion.
     */
    public function cancel(int $ticketId, int $performingUserId): bool
    {
        $this->pdo->beginTransaction();
        try {
            $stmt = $this->pdo->prepare("UPDATE Ticket SET Zahlungsstatus = 'Storniert' WHERE TicketId = :ticket_id AND Zahlungsstatus = 'Offen'");
            $stmt->execute([':ticket_id' => $ticketId]);
            $affectedRows = $stmt->rowCount();

            if ($affectedRows > 0) {
                $this->auditLogRepository->log($performingUserId, 'BESTELLUNG_STORNIERT', ['TicketId' => $ticketId]);
                $this->pdo->commit();
                return true;
            } else {
                $this->pdo->rollBack();
                return false;
            }
        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("Fehler bei der Stornierung für Ticket {$ticketId}: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Erstellt eine neue Bestellung für einen Benutzer aus den Warenkorb-Artikeln.
     */
    public function createOrderForUser(int $userId, array $cartItems): bool
    {
        if (empty($cartItems)) {
            throw new Exception("Der Warenkorb ist leer.");
        }

        $this->pdo->beginTransaction();

        try {
            $stmt_kunde = $this->pdo->prepare("SELECT KundeId FROM Kunde WHERE UserId = :user_id");
            $stmt_kunde->execute([':user_id' => $userId]);
            $kunde_id = $stmt_kunde->fetchColumn();

            if (!$kunde_id) {
                throw new Exception("Keine Kundendaten für diesen Benutzer gefunden.");
            }

            $stmt_insert = $this->pdo->prepare(
                "INSERT INTO Ticket (KundeId, ProduktId, ProduktTyp, BeginnDatum, EndDatum, Zahlungsstatus) 
                 VALUES (:kunde_id, :produkt_id, :produkt_typ, NOW(), DATE_ADD(NOW(), INTERVAL 30 DAY), 'Beglichen')"
            );

            foreach ($cartItems as $item) {
                if (isset($item['type']) && isset($item['id']) && in_array($item['type'], ['movie', 'series'])) {
                    $stmt_insert->execute([
                        ':kunde_id' => $kunde_id,
                        ':produkt_id' => (int)$item['id'],
                        ':produkt_typ' => $item['type']
                    ]);
                }
            }

            $this->pdo->commit();
            return true;

        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("Fehler bei der Bestellungserstellung für User {$userId}: " . $e->getMessage());
            throw $e;
        }
    }
}

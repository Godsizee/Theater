<?php

namespace App\Repositories;

use PDO;

class LoginAttemptRepository
{
    private PDO $pdo;
    private const MAX_ATTEMPTS = 5;
    private const LOCKOUT_MINUTES = 15;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Prüft, ob weitere Login-Versuche für eine IP oder einen Benutzernamen erlaubt sind.
     * @param string $ip
     * @param string $username
     * @return bool
     */
    public function isAllowed(string $ip, string $username): bool
    {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM login_attempts WHERE (ip_address = :ip OR username = :username) AND timestamp > (NOW() - INTERVAL :minutes MINUTE)"
        );
        $stmt->execute([
            ':ip' => $ip, 
            ':username' => $username,
            ':minutes' => self::LOCKOUT_MINUTES
        ]);
        return $stmt->fetchColumn() < self::MAX_ATTEMPTS;
    }

    /**
     * Zeichnet einen fehlgeschlagenen Login-Versuch auf.
     * @param string $ip
     * @param string $username
     */
    public function recordFailure(string $ip, string $username): void
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO login_attempts (ip_address, username) VALUES (:ip, :username)"
        );
        $stmt->execute([':ip' => $ip, ':username' => $username]);
    }

    /**
     * Löscht alle Login-Versuche für eine IP und einen Benutzernamen (nach einem erfolgreichen Login).
     * @param string $ip
     * @param string $username
     */
    public function clearAttempts(string $ip, string $username): void
    {
        $stmt = $this->pdo->prepare(
            "DELETE FROM login_attempts WHERE ip_address = :ip OR username = :username"
        );
        $stmt->execute([':ip' => $ip, ':username' => $username]);
    }
}
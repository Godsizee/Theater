<?php
namespace App\Core;

class Security
{
    public static function generateCsrfToken(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function validateCsrfToken(): bool
    {
        if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token'])) {
            return false;
        }
        if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            return false;
        }
        unset($_SESSION['csrf_token']);
        return true;
    }

    public static function requireAdmin(?string $requiredRole = null): void
    {
        $user_role = strtolower(trim($_SESSION['user_role'] ?? ''));
        $isLoggedIn = isset($_SESSION['user_id']);
        $allowedRoles = ['admin', 'co-admin'];
        $hasAccess = false;

        if ($isLoggedIn) {
            if ($requiredRole !== null) {
                if ($user_role === $requiredRole) {
                    $hasAccess = true;
                }
            } else {
                if (in_array($user_role, $allowedRoles)) {
                    $hasAccess = true;
                }
            }
        }

        if (!$hasAccess) {
            header("Location: " . \App\Core\Database::getConfig()['base_url'] . '/');
            exit();
        }
    }

    public static function requireSupport(): void
    {
        $user_role = strtolower(trim($_SESSION['user_role'] ?? ''));
        if (!isset($_SESSION['user_id']) || !in_array($user_role, ['admin', 'co-admin', 'kundendienst'])) {
            header("Location: " . \App\Core\Database::getConfig()['base_url'] . '/');
            exit();
        }
    }
    
    /**
     * NEUE METHODE: Stellt sicher, dass ein Support-Mitarbeiter angemeldet ist,
     * und gibt im Fehlerfall eine JSON-Antwort zurück.
     */
    public static function requireApiSupport(): void
    {
        $user_role = strtolower(trim($_SESSION['user_role'] ?? ''));
        $isLoggedIn = isset($_SESSION['user_id']);
        $allowedRoles = ['admin', 'co-admin', 'kundendienst'];

        if (!$isLoggedIn || !in_array($user_role, $allowedRoles)) {
            http_response_code(403); // Forbidden
            echo json_encode(['success' => false, 'message' => 'Zugriff verweigert.']);
            exit();
        }
    }
}
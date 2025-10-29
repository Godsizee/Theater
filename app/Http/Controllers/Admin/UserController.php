<?php

namespace App\Http\Controllers\Admin;

use App\Core\Security;
use App\Repositories\UserRepository;
use Exception;
use PDO;

class UserController
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = \App\Core\Database::getInstance();
    }

    public function index()
    {
        Security::requireAdmin();
        $userRepository = new UserRepository($this->pdo);
        global $config;
        $page_title = 'Benutzer verwalten';
        $body_class = 'admin-dashboard-body';
        $sort_column = $_GET['sort'] ?? 'Username';
        $sort_order = $_GET['order'] ?? 'asc';
        $next_order = $sort_order === 'asc' ? 'desc' : 'asc';
        $message = $_SESSION['message'] ?? '';
        unset($_SESSION['message']);
        $message_type = $_SESSION['message_type'] ?? 'success';
        unset($_SESSION['message_type']);
        $users = [];
        try {
            $users = $userRepository->getAll($sort_column, $sort_order);
        } catch (Exception $e) {
            error_log("Fehler beim Abrufen der Benutzerliste: " . $e->getMessage());
            $message = "Fehler beim Laden der Benutzerliste.";
            $message_type = "error";
        }
        require_once dirname(__DIR__, 4) . '/pages/admin/users.php';
    }

    /**
     * Zeigt das Formular zum Erstellen eines neuen Benutzers an.
     */
    public function create()
    {
        Security::requireAdmin('admin');

        $userRepository = new UserRepository($this->pdo);
        
        global $config;
        
        $page_title = 'Neuen Benutzer anlegen';
        $body_class = 'admin-dashboard-body';
        
        $promoter_role = $_SESSION['user_role'] ?? 'user';
        $valid_roles = $userRepository->getValidRoles($promoter_role);
        
        $message = $_SESSION['message'] ?? '';
        $message_type = $_SESSION['message_type'] ?? '';
        unset($_SESSION['message'], $_SESSION['message_type']);

        // KORREKTUR: Hier wurde fälschlicherweise users.php geladen.
        require_once dirname(__DIR__, 4) . '/pages/admin/create_user.php';
    }

    /**
     * Speichert einen neuen Benutzer in der Datenbank.
     */
    public function store()
    {
        Security::requireAdmin('admin');
        
        if (!Security::validateCsrfToken()) {
            $_SESSION['message'] = "Ungültige Anfrage. Bitte versuchen Sie es erneut.";
            $_SESSION['message_type'] = "error";
        } else {
            try {
                if ($_POST['password'] !== $_POST['confirm_password']) {
                    throw new Exception("Die Passwörter stimmen nicht überein.");
                }

                $userRepository = new UserRepository($this->pdo);
                $promoter_role = $_SESSION['user_role'] ?? 'user';

                $registrationData = [
                    'username' => trim($_POST['username']),
                    'email' => trim($_POST['email']),
                    'password' => $_POST['password'],
                    'role' => $_POST['role'],
                    'birthday' => !empty($_POST['birthday']) ? trim($_POST['birthday']) : null,
                    'vorname' => trim($_POST['vorname']) ?: null,
                    'nachname' => trim($_POST['nachname']) ?: null,
                ];
                
                if (!$userRepository->isRolePromotionAllowed($promoter_role, $registrationData['role'])) {
                    throw new Exception("Sie haben keine Berechtigung, die Rolle '" . htmlspecialchars($registrationData['role']) . "' zuzuweisen.");
                }

                $newUserId = $userRepository->register($registrationData, true);

                if ($newUserId) {
                    $_SESSION['message'] = "Benutzer '" . htmlspecialchars($registrationData['username']) . "' wurde erfolgreich erstellt.";
                    $_SESSION['message_type'] = "success";
                    header("Location: " . $GLOBALS['config']['base_url'] . "/admin/users");
                    exit();
                } else {
                    throw new Exception("Registrierung fehlgeschlagen. Bitte versuchen Sie es erneut.");
                }
            } catch (Exception $e) {
                $_SESSION['message'] = $e->getMessage();
                $_SESSION['message_type'] = "error";
            }
        }
        
        header("Location: " . $GLOBALS['config']['base_url'] . "/admin/create_user");
        exit();
    }
    
    /**
     * Zeigt das Formular zum Bearbeiten eines Benutzers an.
     */
    public function edit(int $id)
    {
        Security::requireAdmin();
        $userRepository = new UserRepository($this->pdo);
        global $config;

        $user = $userRepository->find($id);

        if (!$user) {
            $_SESSION['message'] = "Benutzer nicht gefunden.";
            $_SESSION['message_type'] = "error";
            header("Location: " . $config['base_url'] . "/admin/users");
            exit();
        }

        $page_title = 'Benutzer bearbeiten: ' . htmlspecialchars($user['Username']);
        $body_class = 'admin-dashboard-body';
        
        $promoter_role = $_SESSION['user_role'] ?? 'user';
        $valid_roles = $userRepository->getValidRoles($promoter_role);

        $message = $_SESSION['message'] ?? '';
        $message_type = $_SESSION['message_type'] ?? '';
        unset($_SESSION['message'], $_SESSION['message_type']);

        require_once dirname(__DIR__, 4) . '/pages/admin/edit_user.php';
    }

    /**
     * Aktualisiert die Daten eines Benutzers in der Datenbank.
     */
    public function update(int $id)
    {
        Security::requireAdmin();

        if (!Security::validateCsrfToken()) {
            $_SESSION['message'] = "Ungültige Anfrage.";
            $_SESSION['message_type'] = "error";
        } else {
            try {
                $userRepository = new UserRepository($this->pdo);
                $promoter_role = $_SESSION['user_role'];

                $userData = [
                    'username' => trim($_POST['username']),
                    'email' => trim($_POST['email']),
                    'role' => $_POST['role'],
                    'birthday' => !empty($_POST['birthday']) ? trim($_POST['birthday']) : null,
                ];

                if (!$userRepository->isRolePromotionAllowed($promoter_role, $userData['role'])) {
                    throw new Exception("Sie haben keine Berechtigung, die Rolle '" . htmlspecialchars($userData['role']) . "' zuzuweisen.");
                }

                if ($userRepository->exists($userData['username'], $userData['email'], $id)) {
                    throw new Exception("Benutzername oder E-Mail-Adresse ist bereits an einen anderen Benutzer vergeben.");
                }

                $newPassword = !empty($_POST['password']) ? $_POST['password'] : null;
                if ($newPassword && strlen($newPassword) < 8) {
                    throw new Exception("Das neue Passwort muss mindestens 8 Zeichen lang sein.");
                }

                $userRepository->update($id, $userData, $newPassword);

                $_SESSION['message'] = "Benutzer erfolgreich aktualisiert.";
                $_SESSION['message_type'] = "success";

            } catch (Exception $e) {
                $_SESSION['message'] = $e->getMessage();
                $_SESSION['message_type'] = "error";
            }
        }
        
        header("Location: " . $GLOBALS['config']['base_url'] . "/admin/edit_user/" . $id);
        exit();
    }
}
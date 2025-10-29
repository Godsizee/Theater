<?php


namespace App\Http\Controllers\Auth;

use App\Repositories\UserRepository;
use App\Repositories\LoginAttemptRepository;
use App\Services\AuthenticationService;
use Exception;
use PDO;

class AuthController
{
    private PDO $pdo;
    private array $config;

    public function __construct()
    {
        $this->pdo = \App\Core\Database::getInstance();
        $this->config = \App\Core\Database::getConfig();
    }

    /**
     * Zeigt das Login-Formular an oder führt einen Logout durch.
     */
    public function showLogin()
    {
        if (isset($_GET['logout'])) {
            session_unset();
            session_destroy();
            header("Location: " . \App\Core\Utils::url('login'));
            exit();
        }

        if (isset($_SESSION['user_id'])) {
            header("Location: " . \App\Core\Utils::url('profil'));
            exit();
        }

        $page_title = 'Login';
        $message = $_SESSION['flash_message'] ?? '';
        unset($_SESSION['flash_message']);
        $config = $this->config;

        require_once dirname(__DIR__, 4) . '/pages/auth/login.php';
    }

    /**
     * Verarbeitet die POST-Anfrage vom Login-Formular.
     */
    public function handleLogin()
    {
        $identifier = $_POST['identifier'] ?? '';
        $password = $_POST['password'] ?? '';
        $message = '';
        
        try {
            $userRepository = new UserRepository($this->pdo);
            $loginAttemptRepository = new LoginAttemptRepository($this->pdo);
            $authService = new AuthenticationService($userRepository, $loginAttemptRepository);
            
            $user_data = $authService->login($identifier, $password, $_SERVER['REMOTE_ADDR']);

            if ($user_data) {
                session_regenerate_id(true); 
                $_SESSION['user_id'] = $user_data['UserId'];
                $_SESSION['username'] = $user_data['Username'];
                $_SESSION['user_role'] = $user_data['Rolle'];

                header("Location: " . \App\Core\Utils::url('profil'));
                exit();
            } else {
                $message = "Benutzername oder Passwort ist falsch.";
            }
        } catch (Exception $e) {
            $message = $e->getMessage();
        }

        $page_title = 'Login';
        $config = $this->config;
        require_once dirname(__DIR__, 4) . '/pages/auth/login.php';
    }

    /**
     * NEU: Zeigt das Registrierungsformular an.
     */
    public function showRegistry()
    {
        $settings = \App\Core\Utils::getSettings();
        if (!$settings['allow_registrations']) {
            header("Location: " . \App\Core\Utils::url('login'));
            exit();
        }
        
        $page_title = 'Registrieren';
        $message = $_SESSION['flash_message'] ?? '';
        $message_type = $_SESSION['flash_message_type'] ?? 'error';
        unset($_SESSION['flash_message'], $_SESSION['flash_message_type']);
        
        // Globale $config-Variable für die View verfügbar machen
        global $config;

        require_once dirname(__DIR__, 4) . '/pages/auth/registry.php';
    }

    /**
     * NEU: Verarbeitet die POST-Anfrage vom Registrierungsformular.
     */
    public function handleRegistry()
    {
        if ($_POST['password'] !== $_POST['confirm_password']) {
            $_SESSION['flash_message'] = "Die Passwörter stimmen nicht überein.";
            $_SESSION['flash_message_type'] = 'error';
            header("Location: " . \App\Core\Utils::url('registry'));
            exit();
        }

        try {
            $userRepository = new \App\Repositories\UserRepository($this->pdo);
            $new_user_id = $userRepository->register([
                'username' => trim($_POST['username']),
                'email' => trim($_POST['email']),
                'password' => $_POST['password'],
                'vorname' => trim($_POST['vorname']),
                'nachname' => trim($_POST['nachname']),
            ]);

            if ($new_user_id) {
                $_SESSION['flash_message'] = "Registrierung erfolgreich! Sie können sich jetzt anmelden.";
                header("Location: " . \App\Core\Utils::url('login'));
                exit();
            }
        } catch (Exception $e) {
            $_SESSION['flash_message'] = $e->getMessage();
            $_SESSION['flash_message_type'] = 'error';
            header("Location: " . \App\Core\Utils::url('registry'));
            exit();
        }
    }
}

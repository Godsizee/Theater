<?php

namespace App\Http\Controllers;

use App\Core\Security;
use App\Repositories\AuditLogRepository;
use App\Repositories\MediaRepository;
use App\Repositories\TicketRepository;
use App\Repositories\UserRepository;
use App\ViewModels\OrderDetailViewModel;
use App\ViewModels\OrderListItemViewModel;
use Exception;
use PDO;

class ProfileController
{
    private PDO $pdo;
    private array $config;

    public function __construct()
    {
        $this->pdo = \App\Core\Database::getInstance();
        $this->config = \App\Core\Database::getConfig();
    }

    public function showProfile()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . \App\Core\Utils::url('login'));
            exit();
        }

        $user_id = $_SESSION['user_id'];
        $user_data = [];
        $recommendations = [];

        try {
            $userRepository = new UserRepository($this->pdo);
            $mediaRepository = new MediaRepository($this->pdo, $this->config);
            $user_data = $userRepository->find($user_id);
            if ($user_data) {
                $recommendations = $mediaRepository->getRecommendationsForUser($user_id, 4);
            }
        } catch (Exception $e) {
            error_log("Fehler auf der Profilseite: " . $e->getMessage());
        }

        if (empty($user_data)) {
            session_unset();
            session_destroy();
            header("Location: " . \App\Core\Utils::url('login') . '&error=invalid_session');
            exit();
        }

        $page_title = 'Mein Konto';
        $body_class = 'profile-page';
        $config = $this->config;

        require_once dirname(__DIR__, 3) . '/pages/profile/profil.php';
    }

    public function showOrders()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . \App\Core\Utils::url('login'));
            exit();
        }

        $user_id = $_SESSION['user_id'];
        $orderViewModels = [];

        try {
            $userRepository = new UserRepository($this->pdo);
            $auditLogRepository = new AuditLogRepository($this->pdo);
            $ticketRepository = new TicketRepository($this->pdo, $auditLogRepository);
            $user = $userRepository->find($user_id);

            if ($user && isset($user['KundeId'])) {
                $rawOrders = $ticketRepository->findByCustomerId($user['KundeId']);
                foreach ($rawOrders as $rawOrder) {
                    $orderViewModels[] = new OrderListItemViewModel($rawOrder, $this->config, 'user');
                }
            }
        } catch (Exception $e) {
            error_log("Fehler beim Laden der Bestellungen: " . $e->getMessage());
        }

        $page_title = 'Meine Bestellungen';
        $body_class = 'profile-page';
        $config = $this->config;

        require_once dirname(__DIR__, 3) . '/pages/profile/bestellungen.php';
    }

    public function showOrderDetails(int $ticket_id)
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . \App\Core\Utils::url('login'));
            exit();
        }
        
        $user_id = $_SESSION['user_id'];
        $orderViewModel = null;

        try {
            $auditLogRepository = new AuditLogRepository($this->pdo);
            $ticketRepository = new TicketRepository($this->pdo, $auditLogRepository);
            $order = $ticketRepository->findForUser($ticket_id, $user_id);

            if ($order) {
                $orderViewModel = new OrderDetailViewModel($order, $this->config);
            }
        } catch (Exception $e) {
            error_log("Fehler beim Laden der Bestelldetails: " . $e->getMessage());
        }
        
        $page_title = 'Bestelldetails';
        $body_class = 'profile-page';
        $config = $this->config;
        $matches = [$ticket_id];

        require_once dirname(__DIR__, 3) . '/pages/profile/bestellungsdetails.php';
    }

    public function showInvoices()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . \App\Core\Utils::url('login'));
            exit();
        }

        $user_id = $_SESSION['user_id'];
        $open_invoices = [];
        $paid_invoices = [];
        $open_total = 0.00;

        try {
            $userRepository = new UserRepository($this->pdo);
            $auditLogRepository = new AuditLogRepository($this->pdo);
            $ticketRepository = new TicketRepository($this->pdo, $auditLogRepository);
            
            $user = $userRepository->find($user_id);
            if ($user && isset($user['KundeId'])) {
                $all_invoices = $ticketRepository->findByCustomerId($user['KundeId']);
                
                foreach ($all_invoices as $invoice) {
                    if ($invoice['Zahlungsstatus'] === 'Offen') {
                        $open_invoices[] = $invoice;
                        $open_total += $invoice['Total'] ?? 0.00;
                    } else {
                        $paid_invoices[] = $invoice;
                    }
                }
            }
        } catch (Exception $e) {
            error_log("Fehler beim Laden der Rechnungen: " . $e->getMessage());
        }

        $page_title = 'Meine Rechnungen';
        $body_class = 'profile-page';
        $config = $this->config;

        require_once dirname(__DIR__, 3) . '/pages/profile/rechnungen.php';
    }

    public function showProfileData()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . \App\Core\Utils::url('login'));
            exit();
        }

        $user_id = $_SESSION['user_id'];
        $user = [];

        try {
            $userRepository = new UserRepository($this->pdo);
            $user = $userRepository->find($user_id);
        } catch (Exception $e) {
            error_log("Fehler beim Laden der Profildaten: " . $e->getMessage());
        }

        $page_title = 'Persönliche Daten';
        $body_class = 'profile-page';
        $config = $this->config;

        require_once dirname(__DIR__, 3) . '/pages/profile/profil_daten.php';
    }

    public function handleProfileDataUpdate()
    {
        header('Content-Type: application/json');
        $response = ['success' => false, 'message' => 'Ein unbekannter Fehler ist aufgetreten.'];

        try {
            if (!Security::validateCsrfToken()) {
                throw new Exception("Ungültige oder abgelaufene Sitzung. Bitte laden Sie die Seite neu.", 403);
            }

            if (!isset($_SESSION['user_id'])) {
                throw new Exception('Sitzung abgelaufen. Bitte neu anmelden.', 401);
            }

            $user_id = $_SESSION['user_id'];
            $current_password = $_POST['current_password'] ?? '';
            if (empty($current_password)) {
                throw new Exception('Bitte geben Sie Ihr aktuelles Passwort zur Bestätigung ein.');
            }

            $userRepository = new UserRepository($this->pdo);

            if (!$userRepository->verifyPassword($user_id, $current_password)) {
                throw new Exception('Das aktuelle Passwort ist nicht korrekt.');
            }

            $form_type = $_POST['form_type'] ?? '';
            $updateData = [];

            if ($form_type === 'update_address') {
                $user = $userRepository->find($user_id);
                if (!empty($_POST['vorname']) && $_POST['vorname'] !== $user['Vorname']) {
                    if ($user['VornameChanged']) {
                        throw new Exception("Der Vorname wurde bereits einmal geändert und kann nicht erneut bearbeitet werden.");
                    }
                    $updateData['VornameChanged'] = 1;
                }

                if (!empty($_POST['birthday']) && $_POST['birthday'] !== $user['Birthday']) {
                    if ($user['BirthdayChanged']) {
                        throw new Exception("Das Geburtsdatum wurde bereits einmal geändert und kann nicht erneut bearbeitet werden.");
                    }
                    $updateData['BirthdayChanged'] = 1;
                }

                $updateData += [
                    'Vorname' => trim($_POST['vorname']) ?: null,
                    'Nachname' => trim($_POST['nachname']) ?: null,
                    'Strasse' => trim($_POST['strasse']) ?: null,
                    'Hausnummer' => trim($_POST['hausnummer']) ?: null,
                    'PLZ' => trim($_POST['plz']) ?: null,
                    'Ort' => trim($_POST['ort']) ?: null,
                    'Telefon' => trim($_POST['telefon']) ?: null,
                    'Birthday' => !empty($_POST['birthday']) ? trim($_POST['birthday']) : null,
                ];
                $response['message'] = 'Ihre Angaben wurden erfolgreich aktualisiert.';
            } elseif ($form_type === 'update_email') {
                $updateData['EMail'] = trim($_POST['email']);
                if (!filter_var($updateData['EMail'], FILTER_VALIDATE_EMAIL)) {
                    throw new Exception("Ungültige E-Mail-Adresse.");
                }
                if ($userRepository->exists('', $updateData['EMail'], $user_id)) {
                    throw new Exception("E-Mail ist bereits vergeben.");
                }
                $response['message'] = 'Ihre E-Mail-Adresse wurde erfolgreich geändert.';
            } elseif ($form_type === 'update_password') {
                $newPassword = $_POST['new_password'] ?? '';
                $confirmPassword = $_POST['confirm_password'] ?? '';

                if (empty($newPassword) || strlen($newPassword) < 8) {
                    throw new Exception("Das neue Passwort muss mindestens 8 Zeichen lang sein.");
                }
                if ($newPassword !== $confirmPassword) {
                    throw new Exception("Die neuen Passwörter stimmen nicht überein.");
                }

                $updateData['Password'] = $newPassword;
                $response['message'] = 'Ihr Passwort wurde erfolgreich geändert.';
            }

            if (!empty($updateData)) {
                if ($userRepository->updateUserProfile($user_id, $updateData)) {
                    $response['success'] = true;
                } else {
                    throw new Exception('Die Daten konnten nicht in der Datenbank gespeichert werden.');
                }
            }
        } catch (Exception $e) {
            http_response_code(isset($e->getCode) && is_int($e->getCode()) ? $e->getCode() : 400);
            $response['message'] = $e->getMessage();
        }

        echo json_encode($response);
        exit();
    }
}
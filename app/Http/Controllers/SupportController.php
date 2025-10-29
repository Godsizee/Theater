<?php

namespace App\Http\Controllers;

use App\Core\Security;
use App\Repositories\UserRepository;
use App\Repositories\TicketRepository;
use App\Repositories\AuditLogRepository;
use App\Services\CustomerManagementService;
use App\ViewModels\OrderDetailViewModel;
use App\ViewModels\OrderListItemViewModel;
use Exception;
use PDO;

class SupportController
{
    private PDO $pdo;
    private array $config;
    private UserRepository $userRepository;
    private TicketRepository $ticketRepository;
    private AuditLogRepository $auditLogRepository;

    public function __construct()
    {
        $this->pdo = \App\Core\Database::getInstance();
        $this->config = \App\Core\Database::getConfig();
        $this->userRepository = new UserRepository($this->pdo);
        $this->auditLogRepository = new AuditLogRepository($this->pdo);
        $this->ticketRepository = new TicketRepository($this->pdo, $this->auditLogRepository);
    }

    /**
     * Zeigt die Kundensuchseite an.
     */
    public function index()
    {
        Security::requireSupport();
        $page_title = 'Kundendienst Dashboard';
        $body_class = 'admin-dashboard-body';
        global $config;
        require_once dirname(__DIR__, 3) . '/pages/support/index.php';
    }

    /**
     * Listet alle Kunden auf oder zeigt Suchergebnisse an.
     */
    public function listCustomers()
    {
        Security::requireSupport();
        $page_title = 'Kundenübersicht';
        $body_class = 'admin-dashboard-body';
        $search_term = trim($_GET['search'] ?? '');
        $customers = [];

        try {
            $customers = $this->userRepository->searchCustomers($search_term);
        } catch (Exception $e) {
            error_log("Fehler bei der Kundensuche: " . $e->getMessage());
        }
        
        global $config;
        require_once dirname(__DIR__, 3) . '/pages/support/kunden.php';
    }

    /**
     * Zeigt die Detailübersicht eines Kunden an.
     */
    public function showCustomerDetails(int $id)
    {
        Security::requireSupport();
        $page_title = 'Kundendetails';
        $body_class = 'admin-dashboard-body';
        
        try {
            $customer = $this->userRepository->find($id);
            if (!$customer) {
                throw new Exception("Kunde nicht gefunden.");
            }
        } catch (Exception $e) {
            $_SESSION['message'] = "Fehler: " . $e->getMessage();
            $_SESSION['message_type'] = "error";
            header("Location: " . $this->config['base_url'] . "/support/kunden");
            exit();
        }
        
        global $config;
        $matches = [$id];
        require_once dirname(__DIR__, 3) . '/pages/support/kundendetails.php';
    }

    /**
     * Zeigt das Formular zum Bearbeiten von Kundendaten an.
     */
    public function editCustomerData(int $id)
    {
        Security::requireSupport();
        $page_title = 'Kundendaten bearbeiten';
        $body_class = 'admin-dashboard-body';
        $message = $_SESSION['message'] ?? '';
        $message_type = $_SESSION['message_type'] ?? '';
        unset($_SESSION['message'], $_SESSION['message_type']);

        try {
            $customer = $this->userRepository->find($id);
            if (!$customer) throw new Exception("Kunde nicht gefunden.");
            $valid_roles = $this->userRepository->getValidRoles($_SESSION['user_role']);
        } catch (Exception $e) {
            $_SESSION['message'] = "Fehler: " . $e->getMessage();
            header("Location: " . $this->config['base_url'] . "/support/kunden");
            exit();
        }

        global $config;
        $matches = [$id];
        require_once dirname(__DIR__, 3) . '/pages/support/kundendaten_bearbeiten.php';
    }

    /**
     * Verarbeitet die Aktualisierung von Kundendaten.
     */
    public function updateCustomerData(int $id)
    {
        Security::requireSupport();
        
        if (!Security::validateCsrfToken()) {
            $_SESSION['message'] = "Ungültige Anfrage.";
            $_SESSION['message_type'] = "error";
        } else {
            try {
                $customerService = new CustomerManagementService($this->pdo, $this->userRepository, $this->auditLogRepository);
                $customerService->updateCustomer($_SESSION['user_id'], $_SESSION['user_role'], $id, $_POST);
                $_SESSION['message'] = "Kundendaten erfolgreich aktualisiert!";
                $_SESSION['message_type'] = "success";
                header("Location: " . $this->config['base_url'] . "/support/kundendetails/" . $id);
                exit();
            } catch (Exception $e) {
                $_SESSION['message'] = "Fehler: " . $e->getMessage();
                $_SESSION['message_type'] = "error";
            }
        }
        header("Location: " . $this->config['base_url'] . "/support/kundendaten_bearbeiten/" . $id);
        exit();
    }

    /**
     * Zeigt die Bestellungen eines Kunden an.
     */
    public function showCustomerOrders(int $id)
    {
        Security::requireSupport();
        $page_title = 'Kundenbestellungen';
        $body_class = 'admin-dashboard-body';

        try {
            $customer = $this->userRepository->find($id);
            if (!$customer) throw new Exception("Kunde nicht gefunden.");

            $orders = $this->ticketRepository->findByCustomerId($customer['KundeId']);
            $orderViewModels = [];
            foreach ($orders as $order) {
                $orderViewModels[] = new OrderListItemViewModel($order, $this->config, 'support');
            }
        } catch (Exception $e) {
            $_SESSION['message'] = "Fehler: " . $e->getMessage();
            $_SESSION['message_type'] = "error";
            header("Location: " . $this->config['base_url'] . "/support/kunden");
            exit();
        }

        global $config;
        $matches = [$id];
        require_once dirname(__DIR__, 3) . '/pages/support/kundenbestellungen.php';
    }

    /**
     * Zeigt die Detailansicht einer Bestellung an.
     */
    public function showOrderDetails(int $id)
    {
        Security::requireSupport();
        $page_title = 'Bestelldetails (Support-Ansicht)';
        $body_class = 'admin-dashboard-body';

        try {
            $order = $this->ticketRepository->find($id);
            if (!$order) {
                throw new Exception("Bestellung nicht gefunden.");
            }
            $orderViewModel = new OrderDetailViewModel($order, $this->config);
            $customer = $order; // for nav partial
        } catch (Exception $e) {
            $_SESSION['message'] = "Fehler: " . $e->getMessage();
            header("Location: " . $this->config['base_url'] . "/support/kunden");
            exit();
        }

        global $config;
        $matches = [$id];
        require_once dirname(__DIR__, 3) . '/pages/support/bestellungsdetails_ansicht.php';
    }

    /**
     * Zeigt das Formular zum Bearbeiten einer Bestellung an.
     */
    public function editOrder(int $id)
    {
        Security::requireSupport();
        $message = $_SESSION['message'] ?? '';
        $message_type = $_SESSION['message_type'] ?? '';
        unset($_SESSION['message'], $_SESSION['message_type']);

        try {
            $order = $this->ticketRepository->find($id);
            if (!$order) throw new Exception("Bestellung nicht gefunden.");
            $customer = $order;
        } catch (Exception $e) {
            $_SESSION['message'] = "Fehler: " . $e->getMessage();
            header("Location: " . $this->config['base_url'] . "/support/kunden");
            exit();
        }

        $page_title = 'Bestellung #' . $order['TicketId'] . ' bearbeiten';
        $body_class = 'admin-dashboard-body';
        global $config;
        $matches = [$id];
        $ticket_id = $id;
        require_once dirname(__DIR__, 3) . '/pages/support/bestellung_bearbeiten.php';
    }

    /**
     * Verarbeitet die Aktualisierung einer Bestellung.
     */
    public function updateOrder(int $id)
    {
        Security::requireSupport();

        if (!Security::validateCsrfToken()) {
            $_SESSION['message'] = "Ungültige Anfrage.";
            $_SESSION['message_type'] = "error";
        } else {
            try {
                $end_date = $_POST['end_date'] ?? date('Y-m-d H:i:s');
                $status = $_POST['status'] ?? 'Offen';
                
                if ($this->ticketRepository->update($id, $end_date, $status)) {
                    $this->auditLogRepository->log($_SESSION['user_id'], 'BESTELLUNG_GEAENDERT', ['TicketId' => $id, 'NeueDaten' => ['EndDatum' => $end_date, 'Status' => $status]]);
                    $_SESSION['message'] = "Bestellung erfolgreich aktualisiert.";
                    $_SESSION['message_type'] = "success";
                } else {
                    throw new Exception("Bestellung konnte nicht aktualisiert werden.");
                }
            } catch (Exception $e) {
                $_SESSION['message'] = "Fehler: " . $e->getMessage();
                $_SESSION['message_type'] = "error";
            }
        }
        
        $order = $this->ticketRepository->find($id);
        header("Location: " . $this->config['base_url'] . "/support/kundenbestellungen/" . $order['UserId']);
        exit();
    }
}

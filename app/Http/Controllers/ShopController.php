<?php

namespace App\Http\Controllers;

use App\Repositories\UserRepository;
use Exception;
use PDO;

class ShopController
{
    private PDO $pdo;
    private array $config;

    public function __construct()
    {
        $this->pdo = \App\Core\Database::getInstance();
        $this->config = \App\Core\Database::getConfig();
    }

    /**
     * Zeigt den Warenkorb an.
     */
    public function showCart()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . \App\Core\Utils::url('login'));
            exit();
        }

        $page_title = 'Warenkorb';
        $body_class = 'cart-page';
        
        global $config; 
        $config = $this->config;
        require_once dirname(__DIR__, 3) . '/pages/shop/cart.php';
    }

    /**
     * Zeigt die Checkout-Seite an.
     */
    public function showCheckout()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . \App\Core\Utils::url('login'));
            exit();
        }

        $user_data = [];
        try {
            $userRepository = new UserRepository($this->pdo);
            $user_data = $userRepository->find($_SESSION['user_id']);
        } catch (Exception $e) {
            error_log("Fehler beim Laden der Benutzerdaten für den Checkout: " . $e->getMessage());
            header("Location: " . \App\Core\Utils::url('cart'));
            exit();
        }

        $page_title = 'Überprüfen & Bestellen';
        $body_class = 'checkout-page';
        
        global $config;
        $config = $this->config;
        require_once dirname(__DIR__, 3) . '/pages/shop/checkout.php';
    }

    /**
     * NEU: Zeigt die Merchandise-Seite an.
     */
    public function showMerchandise()
    {
        $page_title = 'Merchandise - Demnächst verfügbar';
        $body_class = '';
        
        global $config;
        $config = $this->config;
        require_once dirname(__DIR__, 3) . '/pages/shop/merchandise.php';
    }
}

<?php

namespace App\Http\Controllers;

use Exception;

class StaticPageController
{
    private array $config;

    public function __construct()
    {
        $this->config = \App\Core\Database::getConfig();
    }

    /**
     * Zeigt eine generische statische Seite an.
     * @param string $pageName Der Name der anzuzeigenden Seite (z.B. 'impressum').
     */
    public function show(string $pageName)
    {
        // Wandelt URL-freundliche Namen (mit Bindestrich) in Dateinamen (mit Unterstrich) um.
        $fileName = str_replace('-', '_', $pageName);
        $filePath = dirname(__DIR__, 3) . "/pages/static/{$fileName}.php";

        // Die 404-Logik wurde entfernt, da sie jetzt zentral in index.php gehandhabt wird.
        // Wenn die Datei nicht existiert, wird der Router-Fallback in index.php greifen.
        if (!file_exists($filePath)) {
             // Diese Bedingung sollte durch den Router in index.php bereits abgefangen werden,
             // aber als Sicherheitsnetz leiten wir einfach weiter.
            header("Location: " . $this->config['base_url'] . "/");
            exit();
        }

        // Setzt einen sinnvollen Seitentitel basierend auf dem Dateinamen
        $page_title = ucfirst(str_replace('_', ' ', $fileName));
        if ($fileName === 'agb') {
            $page_title = 'Allgemeine Geschäftsbedingungen';
        }
        
        $body_class = 'static-page';
        global $config;
        $config = $this->config;

        require_once $filePath;
    }

    /**
     * Zeigt die Kontaktseite mit dem Formular an.
     */
    public function showKontakt()
    {
        $page_title = 'Kontakt';
        $body_class = 'static-page';
        $message_sent = $_SESSION['message_sent'] ?? false;
        $error_message = $_SESSION['error_message'] ?? '';
        unset($_SESSION['message_sent'], $_SESSION['error_message']);

        global $config;
        $config = $this->config;

        require_once dirname(__DIR__, 3) . '/pages/static/kontakt.php';
    }

    /**
     * Verarbeitet die Einsendung des Kontaktformulars.
     */
    public function handleKontakt()
    {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $subject = trim($_POST['subject'] ?? '');
        $body = trim($_POST['message'] ?? '');

        if (empty($name) || empty($email) || empty($subject) || empty($body)) {
            $_SESSION['error_message'] = "Bitte füllen Sie alle Felder aus.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error_message'] = "Bitte geben Sie eine gültige E-Mail-Adresse ein.";
        } else {
            // Hier würde die Logik zum Senden der E-Mail stehen.
            // Wir simulieren den Erfolg.
            $_SESSION['message_sent'] = true;
        }

        header("Location: " . $this->config['base_url'] . "/kontakt");
        exit();
    }
}

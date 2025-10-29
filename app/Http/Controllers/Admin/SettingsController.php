<?php

namespace App\Http\Controllers\Admin;

use App\Core\Security;
use App\Core\Utils;

class SettingsController
{
    public function show()
    {
        // KORREKTUR: Die globale $config-Variable für die View verfügbar machen
        global $config;

        $page_title = 'Einstellungen';
        $body_class = 'admin-dashboard-body';
        $message = $_SESSION['message'] ?? '';
        $message_type = $_SESSION['message_type'] ?? '';
        unset($_SESSION['message'], $_SESSION['message_type']);

        $settings = Utils::getSettings();

        require_once dirname(__DIR__, 4) . '/pages/admin/settings.php';
    }

    public function update()
    {
        if (!Security::validateCsrfToken()) {
            $_SESSION['message'] = "Ungültige Anfrage. Bitte versuchen Sie es erneut.";
            $_SESSION['message_type'] = "error";
        } else {
            $new_title = trim($_POST['site_title']);
            $new_items_per_page = filter_input(INPUT_POST, 'items_per_page', FILTER_VALIDATE_INT);
            $new_sort_order = trim($_POST['default_sort_order']);
            $new_allow_registrations = isset($_POST['allow_registrations']) ? (bool)$_POST['allow_registrations'] : false;

            $valid_sort_orders = ['name_asc', 'name_desc', 'year_desc', 'year_asc'];

            if (empty($new_title) || $new_items_per_page === false || $new_items_per_page < 1 || !in_array($new_sort_order, $valid_sort_orders)) {
                $_SESSION['message'] = "Bitte füllen Sie alle Felder korrekt aus.";
                $_SESSION['message_type'] = "error";
            } else {
                $current_settings = Utils::getSettings();
                $current_settings['site_title'] = $new_title;
                $current_settings['items_per_page'] = $new_items_per_page;
                $current_settings['default_sort_order'] = $new_sort_order;
                $current_settings['allow_registrations'] = $new_allow_registrations;

                if (Utils::saveSettings($current_settings)) {
                    $_SESSION['message'] = "Einstellungen erfolgreich gespeichert!";
                    $_SESSION['message_type'] = "success";
                } else {
                    $_SESSION['message'] = "Fehler: Einstellungen konnten nicht gespeichert werden.";
                    $_SESSION['message_type'] = "error";
                }
            }
        }

        header("Location: " . $GLOBALS['config']['base_url'] . "/admin/settings");
        exit();
    }
}
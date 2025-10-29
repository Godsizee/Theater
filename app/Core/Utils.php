<?php
// app/Core/Utils.php

namespace App\Core;

use App\Core\Cache;

class Utils
{
    /**
     * Generiert eine portable URL, die ohne mod_rewrite funktioniert.
     * @param string $path Der interne Pfad (z.B. 'profil' oder 'admin/users').
     * @return string Die vollständige, funktionierende URL.
     */
    public static function url(string $path): string
    {
        $base_url = Database::getConfig()['base_url'];
        // Wenn der Pfad leer ist oder nur aus einem / besteht, verlinke zur Startseite
        if (empty($path) || $path === '/') {
            return rtrim($base_url, '/') . '/';
        }
        return rtrim($base_url, '/') . '/index.php?url=' . ltrim($path, '/');
    }

    /**
     * Holt die Einstellungen. Prüft zuerst den Cache.
     * @return array
     */
    public static function getSettings(): array
    {
        $cache = new Cache();
        $cacheKey = 'app_settings';
        $settings = $cache->get($cacheKey);

        if ($settings === null) {
            $settings_file = dirname(__DIR__, 2) . '/config/settings.json';
            if (file_exists($settings_file)) {
                $settings_data = json_decode(file_get_contents($settings_file), true);
                $settings = (json_last_error() === JSON_ERROR_NONE) ? $settings_data : [];
            } else {
                $settings = [];
            }
            
            $settings = array_merge([
                'site_title' => 'Filmverleih',
                'items_per_page' => 24,
                'default_sort_order' => 'name_asc',
                'allow_registrations' => true
            ], $settings);

            $cache->set($cacheKey, $settings, 3600);
        }

        return $settings;
    }

    /**
     * Speichert die Einstellungen und löscht den Cache.
     * @param array $settings
     * @return bool
     */
    public static function saveSettings(array $settings): bool
    {
        $settings_file = dirname(__DIR__, 2) . '/config/settings.json';
        if (!is_dir(dirname($settings_file))) {
            mkdir(dirname($settings_file), 0755, true);
        }
        
        $json_content = json_encode($settings, JSON_PRETTY_PRINT);
        if ($json_content !== false && file_put_contents($settings_file, $json_content) !== false) {
            $cache = new Cache();
            $cache->set('app_settings', [], -1); // Invalidate cache
            return true;
        }
        return false;
    }

    /**
     * Fügt die von Vite generierten Assets ein.
     * @param array $config
     */
    public static function viteAssets(array $config): void
    {
        $isDev = ($_ENV['APP_ENV'] ?? 'production') === 'development';
        $devServerUrl = 'http://localhost:5173';

        if ($isDev) {
            echo '<script type="module" src="' . $devServerUrl . '/@vite/client"></script>';
            echo '<script type="module" src="' . $devServerUrl . '/public/assets/js/main.js"></script>';
        } else {
            $manifest_path = dirname(__DIR__, 2) . '/public/dist/.vite/manifest.json';

            if (!file_exists($manifest_path)) {
                return;
            }

            $manifest = json_decode(file_get_contents($manifest_path), true);
            
            $css_key = 'public/assets/css/main.css';
            $js_key = 'public/assets/js/main.js';

            if (isset($manifest[$css_key]['file'])) {
                $css_file = $manifest[$css_key]['file'];
                echo '<link rel="stylesheet" href="' . $config['base_url'] . '/public/dist/' . $css_file . '">';
            }

            if (isset($manifest[$js_key]['file'])) {
                $js_file = $manifest[$js_key]['file'];
                echo '<script type="module" src="' . $config['base_url'] . '/public/dist/' . $js_file . '"></script>';
            }
        }
    }
}
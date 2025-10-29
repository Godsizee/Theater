<?php

// init.php (Final & Bereinigt)

// 1. Session starten (MUSS vor JEDER anderen Ausgabe erfolgen)
session_start();

// 2. Autoloader für alle Klassen im 'App' Namespace
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/app/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

// 3. Konfiguration und Datenbankverbindung laden
$config = App\Core\Database::getConfig();
$pdo = App\Core\Database::getInstance();
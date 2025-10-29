<?php
// app/Core/Database.php

namespace App\Core;

use PDO;
use PDOException;
use RuntimeException;

/**
 * Eine Singleton-Klasse für die Datenbankverbindung.
 * Stellt sicher, dass nur eine einzige PDO-Instanz pro Anfrage erstellt wird.
 */
class Database
{
    private static ?PDO $instance = null;
    private static array $config = [];

    /**
     * Der Konstruktor ist privat, um eine direkte Instanziierung zu verhindern.
     */
    private function __construct() {}

    /**
     * Klonen ist nicht erlaubt, um die Einzigartigkeit der Instanz zu wahren.
     */
    private function __clone() {}

    /**
     * Holt die Konfiguration aus der verschobenen Datei.
     */
    public static function getConfig(): array
    {
        if (empty(self::$config)) {
            // HIER IST DIE ÄNDERUNG: Der Pfad wurde aktualisiert.
            self::$config = require __DIR__ . '/../../config/database_access.php';
        }
        return self::$config;
    }

    /**
     * Gibt die einzige PDO-Instanz zurück oder erstellt sie bei Bedarf.
     *
     * @return PDO Die PDO-Instanz.
     * @throws RuntimeException Wenn die Verbindung fehlschlägt.
     */
    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $config = self::getConfig();
            
            $dsn = "mysql:host={$config['db_host']};port={$config['db_port']};dbname={$config['db_name']};charset={$config['db_charset']}";

            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            try {
                self::$instance = new PDO($dsn, $config['db_user'], $config['db_pass'], $options);
            } catch (PDOException $e) {
                // In einer realen Anwendung würde hier ein Logger verwendet.
                error_log("Datenbankverbindungsfehler: " . $e->getMessage());
                throw new RuntimeException("Datenbankverbindung konnte nicht hergestellt werden.");
            }
        }

        return self::$instance;
    }
}

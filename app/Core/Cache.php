<?php
// app/Core/Cache.php

namespace App\Core;

/**
 * Eine einfache, dateibasierte Caching-Klasse.
 * Speichert Daten für eine bestimmte Zeit, um wiederholte, langsame Operationen zu vermeiden.
 */
class Cache
{
    private string $cacheDir;

    public function __construct()
    {
        // Definiert das Verzeichnis, in dem die Cache-Dateien gespeichert werden.
        $this->cacheDir = dirname(__DIR__, 2) . '/cache/';
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0755, true);
        }
    }

    /**
     * Holt einen Wert aus dem Cache.
     *
     * @param string $key Der eindeutige Schlüssel für den Cache-Eintrag.
     * @return mixed|null Die Daten aus dem Cache oder null, wenn der Cache abgelaufen oder nicht vorhanden ist.
     */
    public function get(string $key)
    {
        $file = $this->getFilePath($key);

        if (!file_exists($file)) {
            return null;
        }

        $content = file_get_contents($file);
        $data = unserialize($content);

        // Prüfen, ob der Cache abgelaufen ist
        if (time() > $data['expires']) {
            unlink($file); // Abgelaufene Cache-Datei löschen
            return null;
        }

        return $data['value'];
    }

    /**
     * Speichert einen Wert im Cache.
     *
     * @param string $key Der eindeutige Schlüssel.
     * @param mixed $value Die zu speichernden Daten.
     * @param int $duration Die Gültigkeitsdauer des Caches in Sekunden.
     */
    public function set(string $key, $value, int $duration = 3600): void
    {
        $data = [
            'value'   => $value,
            'expires' => time() + $duration,
        ];

        $file = $this->getFilePath($key);
        file_put_contents($file, serialize($data));
    }

    /**
     * Generiert den vollständigen Dateipfad für einen Cache-Schlüssel.
     */
    private function getFilePath(string $key): string
    {
        // Erstellt einen sicheren Dateinamen aus dem Schlüssel
        return $this->cacheDir . md5($key) . '.cache';
    }
}

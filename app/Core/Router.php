<?php
// app/Core/Router.php

namespace App\Core;

class Router
{
    private array $routes = [];

    /**
     * Fügt eine neue Route zur Routing-Tabelle hinzu.
     *
     * @param string $pattern Ein Regex-Pattern für die URL.
     * @param mixed $handler Der Handler (Dateipfad als String oder Controller-Array).
     */
    public function add(string $pattern, $handler): void
    {
        $this->routes[$pattern] = $handler;
    }

    /**
     * Findet die passende Route für eine gegebene URI.
     *
     * @param string $uri Die angefragte URL (z.B. 'admin/users').
     * @return array|null Gibt ein Array mit Handler und Parametern zurück oder null.
     */
    public function resolve(string $uri): ?array
    {
        foreach ($this->routes as $pattern => $handler) {
            if (preg_match($pattern, $uri, $matches)) {
                // Entfernt den kompletten Match, damit nur die Capturing-Groups übrig bleiben.
                array_shift($matches);

                // Prüft, ob der Handler Query-Parameter enthält (z.B. 'update.php?type=movie')
                // Dies betrifft nur die alten, String-basierten Routen.
                if (is_string($handler)) {
                    $queryString = parse_url($handler, PHP_URL_QUERY);
                    if ($queryString) {
                        parse_str($queryString, $_GET); // Fügt die Parameter zu $_GET hinzu
                        $handler = parse_url($handler, PHP_URL_PATH);
                    }
                }
                
                return [
                    'handler' => $handler,
                    'matches' => $matches
                ];
            }
        }
        return null;
    }
}
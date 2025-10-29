<?php
// index.php (Final & Bereinigt)

// WICHTIG: Diese Zeile muss die ALLERERSTE Anweisung in der Datei sein.
require_once __DIR__ . '/init.php';

// Router einrichten und ausführen
$router = new \App\Core\Router();
$routes = require __DIR__ . '/config/routes.php';

foreach ($routes as $pattern => $handler) {
    $router->add($pattern, $handler);
}

$request_uri = $_GET['url'] ?? '/';
$request_path = parse_url($request_uri, PHP_URL_PATH);
$request_path = trim($request_path, '/');

$routeInfo = $router->resolve($request_path);

if ($routeInfo) {
    $handler = $routeInfo['handler'];
    $matches = $routeInfo['matches'];

    if (is_array($handler) && class_exists($handler[0]) && method_exists($handler[0], $handler[1])) {
        $controllerClass = $handler[0];
        $method = $handler[1];
        $controller = new $controllerClass();
        call_user_func_array([$controller, $method], $matches);
    } elseif (is_string($handler) && file_exists(__DIR__ . '/' . $handler)) {
        include __DIR__ . '/' . $handler;
    } else {
        // Fallback, wenn der Handler ungültig ist
        http_response_code(404);
        $page_title = '404 - Seite nicht gefunden';
        $body_class = 'error-page body-centered-content';
        include_once __DIR__ . '/templates/header.php';
        include_once __DIR__ . '/pages/errors/404.php';
        include_once __DIR__ . '/templates/footer.php';
    }
} else {
    // KORREKTUR: Zeigt jetzt die gestaltete 404-Seite für jede nicht gefundene Route an
    http_response_code(404);
    $page_title = '404 - Seite nicht gefunden';
    $body_class = 'error-page body-centered-content';
    include_once __DIR__ . '/templates/header.php';
    include_once __DIR__ . '/pages/errors/404.php';
    include_once __DIR__ . '/templates/footer.php';
}

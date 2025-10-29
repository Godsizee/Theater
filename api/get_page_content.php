<?php
require_once __DIR__ . '/../init.php';

$router = new \App\Core\Router();
$routes = require __DIR__ . '/../config/routes.php';
foreach ($routes as $pattern => $handler) {
    $router->add($pattern, $handler);
}

$request_uri = $_GET['path'] ?? '/';
$request_path = trim(parse_url($request_uri, PHP_URL_PATH), '/');

$routeInfo = $router->resolve($request_path);

$response_data = [
    'success'    => false,
    'html'       => '<div class="page-wrapper" style="text-align:center; padding:120px 20px;"><h1>404</h1><p>Inhalt konnte nicht geladen werden.</p></div>',
    'page_title' => 'Fehler',
    'body_class' => 'error-page' // Standard-Body-Klasse
];

if ($routeInfo) {
    $matches = $routeInfo['matches'];
    $file_to_load = __DIR__ . '/../' . $routeInfo['handler'];

    if (file_exists($file_to_load)) {
        $is_ajax_request = true;

        ob_start();
        // Die PHP-Datei wird ausgeführt. Wenn sie $body_class definiert, ist die Variable hier verfügbar.
        include $file_to_load;
        $full_html = ob_get_clean();

        preg_match("/<script>document\.title = '(.*?)';/", $full_html, $title_matches);
        if (empty($title_matches)) { 
            preg_match("/<title>(.*?)<\/title>/", $full_html, $title_matches);
        }
        $page_title = $title_matches[1] ?? 'Theater';

        // Wir extrahieren nur den Inhalt des Wrappers, nicht den Wrapper selbst
        preg_match('/<main class="page-wrapper.*?">(.*?)<\/main>/s', $full_html, $content_matches);
        $main_content = $content_matches[1] ?? $full_html; 

        $response_data['success']    = true;
        $response_data['html']       = $main_content;
        $response_data['page_title'] = html_entity_decode($page_title);
        // NEU: Füge die $body_class zur Antwort hinzu. Wenn sie nicht gesetzt ist, bleibt sie leer.
        $response_data['body_class'] = $body_class ?? '';
    }
}

header('Content-Type: application/json');
echo json_encode($response_data);
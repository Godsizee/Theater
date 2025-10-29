<?php
require_once __DIR__ . '/../init.php';

header('Content-Type: application/json');

$response = [
    'available' => false,
    'message' => 'Ungültige Anfrage'
];

try {
    $userRepository = new \App\Repositories\UserRepository($pdo);

    if (isset($_GET['username'])) {
        $username = trim($_GET['username']);
        if ($userRepository->isUsernameAvailable($username)) {
            $response['available'] = true;
            $response['message'] = 'Benutzername ist verfügbar.';
        } else {
            $response['message'] = 'Benutzername ist bereits vergeben.';
        }
    }
    elseif (isset($_GET['email'])) {
        $email = trim($_GET['email']);
        if ($userRepository->isEmailAvailable($email)) {
            $response['available'] = true;
            $response['message'] = 'E-Mail ist verfügbar.';
        } else {
            $response['message'] = 'E-Mail ist bereits registriert.';
        }
    }

} catch (PDOException $e) {
    http_response_code(500);
    error_log("Validation API error: " . $e->getMessage());
    $response['message'] = 'Bei der Prüfung ist ein Datenbankfehler aufgetreten.';
}

echo json_encode($response);
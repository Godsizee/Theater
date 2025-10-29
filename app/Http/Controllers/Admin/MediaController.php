<?php

namespace App\Http\Controllers\Admin;

use App\Core\Security;
use App\Repositories\AuditLogRepository;
use App\Repositories\MediaRepository;
use App\Services\ImageUploadService;
use App\Services\MediaManagementService;
use Exception;
use PDO;

class MediaController
{
    private PDO $pdo;
    private MediaRepository $mediaRepository;
    private MediaManagementService $mediaService;

    public function __construct()
    {
        $this->pdo = \App\Core\Database::getInstance();
        
        // Services und Repositories, die in mehreren Methoden gebraucht werden,
        // können hier zentral instanziiert werden.
        $repoConfig = ['document_root' => dirname(__DIR__, 4)];
        $this->mediaRepository = new MediaRepository($this->pdo, $repoConfig);
        $auditLogRepository = new AuditLogRepository($this->pdo);
        $imageUploadService = new ImageUploadService($repoConfig);
        $this->mediaService = new MediaManagementService($this->mediaRepository, $auditLogRepository, $imageUploadService);
    }

    public function index()
    {
        // ... (bestehende index-Methode, keine Änderung)
        Security::requireAdmin();
        global $config;
        $type_from_url = $_GET['type'] ?? 'movies';
        if ($type_from_url === 'series') {
            $page_title = 'Serien verwalten';
            $media_type = 'series';
        } else {
            $page_title = 'Filme verwalten';
            $media_type = 'movies';
        }
        $body_class = 'admin-dashboard-body';
        $page_type = $media_type;
        $form_action = $config['base_url'] . '/admin/media_overview';
        $message = $_SESSION['message'] ?? '';
        unset($_SESSION['message']);
        $message_type = $_SESSION['message_type'] ?? 'success';
        unset($_SESSION['message_type']);
        require_once dirname(__DIR__, 4) . '/pages/admin/media_overview.php';
    }

    public function create()
    {
        // ... (bestehende create-Methode, keine Änderung)
        Security::requireAdmin();
        global $config;
        $page_title = 'Neuen Inhalt hinzufügen';
        $body_class = 'admin-dashboard-body';
        $message = $_SESSION['message'] ?? '';
        $message_type = $_SESSION['message_type'] ?? '';
        unset($_SESSION['message'], $_SESSION['message_type']);
        require_once dirname(__DIR__, 4) . '/pages/admin/insert.php';
    }

    public function store()
    {
        // ... (bestehende store-Methode, keine Änderung)
        Security::requireAdmin();
        if (!Security::validateCsrfToken()) {
            $_SESSION['message'] = "Ungültige Anfrage. Bitte versuchen Sie es erneut.";
            $_SESSION['message_type'] = "error";
        } else {
            try {
                $mediaType = $_POST['media_type'] ?? '';
                $this->mediaService->createMedia($mediaType, $_POST, $_FILES['poster'] ?? [], $_SESSION['user_id']);
                $_SESSION['message'] = ucfirst($mediaType) . " erfolgreich hinzugefügt!";
                $_SESSION['message_type'] = 'success';
                header("Location: " . $GLOBALS['config']['base_url'] . "/admin/insert");
                exit();
            } catch (Exception $e) {
                $_SESSION['message'] = "Fehler: " . $e->getMessage();
                $_SESSION['message_type'] = 'error';
            }
        }
        header("Location: " . $GLOBALS['config']['base_url'] . "/admin/insert");
        exit();
    }
    
    /**
     * NEU: Zeigt das Formular zum Bearbeiten eines Mediums an.
     */
    public function edit(string $media_type, int $id)
    {
        Security::requireAdmin();
        global $config;
        
        try {
            $media = ($media_type === 'movie')
                ? $this->mediaRepository->findMovieOrFail($id)
                : $this->mediaRepository->findSeriesOrFail($id);

            if (!$media) throw new Exception("Inhalt nicht gefunden.");

            if ($media_type === 'movie') {
                $media['title'] = $media['Moviename'] ?? '';
                $media['price'] = $media['Price'] ?? 0;
            } else {
                $media['title'] = $media['Title'] ?? '';
                $media['price'] = $media['Price'] ?? 0;
            }
        } catch (Exception $e) {
            $_SESSION['message'] = "Fehler beim Laden der Daten: " . $e->getMessage();
            header("Location: " . $config['base_url'] . "/admin");
            exit();
        }

        $page_title = 'Bearbeiten: ' . htmlspecialchars($media['title'] ?? 'Unbekannt');
        $body_class = 'admin-dashboard-body';
        $message = $_SESSION['message'] ?? '';
        $message_type = $_SESSION['message_type'] ?? '';
        unset($_SESSION['message'], $_SESSION['message_type']);
        
        require_once dirname(__DIR__, 4) . '/pages/admin/update.php';
    }

    /**
     * NEU: Aktualisiert ein Medium in der Datenbank.
     */
    public function update(string $media_type, int $id)
    {
        Security::requireAdmin();
        
        if (!Security::validateCsrfToken()) {
            $_SESSION['message'] = "Ungültige Anfrage. Bitte versuchen Sie es erneut.";
            $_SESSION['message_type'] = "error";
        } else {
            try {
                $this->mediaService->updateMedia($media_type, $id, $_POST, $_FILES['poster'] ?? [], $_SESSION['user_id']);
                $_SESSION['message'] = "Inhalt erfolgreich aktualisiert!";
                $_SESSION['message_type'] = "success";
                $redirect_url = $GLOBALS['config']['base_url'] . '/admin/media_overview?type=' . ($media_type === 'movie' ? 'movies' : 'series');
                header("Location: " . $redirect_url);
                exit();
            } catch (Exception $e) {
                $_SESSION['message'] = "Fehler beim Aktualisieren: " . $e->getMessage();
                $_SESSION['message_type'] = "error";
            }
        }
        
        header("Location: " . $GLOBALS['config']['base_url'] . "/admin/update/{$media_type}/{$id}");
        exit();
    }

    /**
     * NEU: Zeigt die Bestätigungsseite zum Löschen an.
     */
    public function confirmDelete(string $media_type, int $id)
    {
        Security::requireAdmin();
        global $config;

        $media = ($media_type === 'movie') ? $this->mediaRepository->findMovieOrFail($id) : $this->mediaRepository->findSeriesOrFail($id);
        
        if (!$media) {
            header("Location: " . $config['base_url'] . "/admin/media_overview");
            exit();
        }
        $media['title'] = $media['Moviename'] ?? ($media['Title'] ?? 'Unbekannt');

        $page_title = 'Löschen bestätigen';
        $body_class = 'admin-dashboard-body';
        $redirect_url = $config['base_url'] . '/admin/media_overview?type=' . ($media_type === 'movie' ? 'movies' : 'series');

        require_once dirname(__DIR__, 4) . '/pages/admin/delete.php';
    }

    /**
     * NEU: Löscht ein Medium aus der Datenbank.
     */
    public function destroy(string $media_type, int $id)
    {
        Security::requireAdmin();
        $redirect_url = $GLOBALS['config']['base_url'] . '/admin/media_overview?type=' . ($media_type === 'movie' ? 'movies' : 'series');

        if (!Security::validateCsrfToken()) {
            $_SESSION['message'] = "Ungültige Anfrage. Bitte versuchen Sie es erneut.";
        } else {
            try {
                $deletedTitle = $this->mediaService->deleteMedia($media_type, $id, $_SESSION['user_id']);
                $_SESSION['message'] = "Der Inhalt '" . htmlspecialchars($deletedTitle) . "' wurde erfolgreich gelöscht.";
            } catch (Exception $e) {
                $_SESSION['message'] = "Fehler: " . $e->getMessage();
            }
        }
        header("Location: " . $redirect_url);
        exit();
    }
}
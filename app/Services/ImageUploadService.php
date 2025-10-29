<?php

namespace App\Services;

use Exception;

class ImageUploadService
{
    private array $config;

    public function __construct(array $config)
    {
        // Stellt sicher, dass der Basispfad gesetzt ist
        if (!isset($config['document_root'])) {
            $config['document_root'] = dirname(__DIR__, 2);
        }
        $this->config = $config;
    }

    /**
     * Verarbeitet den Upload eines Posters, validiert, verkleinert und speichert es.
     *
     * @param array $file Das $_FILES-Array für das hochgeladene Bild.
     * @param string $title Der Titel des Films/der Serie zur Benennung der Datei.
     * @param string $type 'movie' oder 'series', um den Speicherort zu bestimmen.
     * @return string Der relative Pfad zum gespeicherten Poster.
     * @throws Exception Wenn beim Upload ein Fehler auftritt.
     */
    public function handlePosterUpload(array $file, string $title, string $type = 'movie'): string
    {
        $basePath = ($type === 'movie') ? 'img/movieImg/' : 'img/seriesImg/';
        $uploadDir = $this->config['document_root'] . '/' . $basePath;

        if (!isset($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
            return $basePath . 'placeholder.png';
        }
        
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception("Fehler beim Datei-Upload. Code: " . $file['error']);
        }

        if ($file['size'] > 10 * 1024 * 1024) { // 10 MB
            throw new Exception("Die Datei ist zu groß. Maximal 10 MB erlaubt.");
        }
        
        $sourcePath = $file['tmp_name'];
        $imageInfo = getimagesize($sourcePath);
        if ($imageInfo === false) {
            throw new Exception("Die hochgeladene Datei ist kein gültiges Bild.");
        }

        $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($imageInfo['mime'], $allowedMimeTypes)) {
            throw new Exception("Ungültiger Dateityp. Nur JPG, PNG, GIF und WebP sind erlaubt.");
        }

        if (!is_dir($uploadDir)) {
            if (!mkdir($uploadDir, 0755, true)) {
                throw new Exception("Zielordner konnte nicht erstellt werden.");
            }
        }

        $slug = preg_replace('/[^a-z0-9-]/', '', strtolower(str_replace(' ', '-', $title)));
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $newFilename = $slug . '-' . uniqid() . '.' . $extension;
        $destination = $uploadDir . $newFilename;

        list($originalWidth, $originalHeight, $imageType) = $imageInfo;
        $maxWidth = 800;

        if ($originalWidth > $maxWidth) {
            switch ($imageType) {
                case IMAGETYPE_JPEG: $sourceImage = imagecreatefromjpeg($sourcePath); break;
                case IMAGETYPE_PNG:  $sourceImage = imagecreatefrompng($sourcePath); break;
                case IMAGETYPE_GIF:  $sourceImage = imagecreatefromgif($sourcePath); break;
                case IMAGETYPE_WEBP: $sourceImage = imagecreatefromwebp($sourcePath); break;
                default: throw new Exception("Nicht unterstützter Bildtyp für die Verarbeitung.");
            }

            $ratio = $originalHeight / $originalWidth;
            $newWidth = $maxWidth;
            $newHeight = $maxWidth * $ratio;

            $newImage = imagecreatetruecolor($newWidth, $newHeight);

            if ($imageType == IMAGETYPE_PNG || $imageType == IMAGETYPE_GIF) {
                imagealphablending($newImage, false);
                imagesavealpha($newImage, true);
                $transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
                imagefilledrectangle($newImage, 0, 0, $newWidth, $newHeight, $transparent);
            }
            
            imagecopyresampled($newImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);
            
            $saveSuccess = false;
            switch ($imageType) {
                case IMAGETYPE_JPEG: $saveSuccess = imagejpeg($newImage, $destination, 85); break;
                case IMAGETYPE_PNG:  $saveSuccess = imagepng($newImage, $destination, 6); break;
                case IMAGETYPE_GIF:  $saveSuccess = imagegif($newImage, $destination); break;
                case IMAGETYPE_WEBP: $saveSuccess = imagewebp($newImage, $destination, 85); break;
            }

            imagedestroy($sourceImage);
            imagedestroy($newImage);

            if (!$saveSuccess) {
                throw new Exception("Das verkleinerte Bild konnte nicht gespeichert werden.");
            }

        } else {
            if (!move_uploaded_file($sourcePath, $destination)) {
                throw new Exception("Datei konnte nicht in das Zielverzeichnis verschoben werden.");
            }
        }

        return $basePath . $newFilename;
    }
}
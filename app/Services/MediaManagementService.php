<?php
// app/Services/MediaManagementService.php

namespace App\Services;

use App\Repositories\MediaRepository;
use App\Repositories\AuditLogRepository;
use App\Services\ImageUploadService;
use Exception;

class MediaManagementService
{
    private MediaRepository $mediaRepository;
    private AuditLogRepository $auditLogRepository;
    private ImageUploadService $imageUploadService;

    public function __construct(MediaRepository $mediaRepository, AuditLogRepository $auditLogRepository, ImageUploadService $imageUploadService)
    {
        $this->mediaRepository = $mediaRepository;
        $this->auditLogRepository = $auditLogRepository;
        $this->imageUploadService = $imageUploadService;
    }

    /**
     * Erstellt einen neuen Medieneintrag (Film oder Serie) und loggt die Aktion.
     */
    public function createMedia(string $mediaType, array $postData, array $fileData, int $creatorUserId): int
    {
        // 1. Poster hochladen
        $posterPath = $this->imageUploadService->handlePosterUpload($fileData, $postData['title'] ?? 'media', $mediaType);

        // 2. Medieneintrag in DB erstellen
        try {
            if ($mediaType === 'movie') {
                $movieData = [
                    'moviename' => $postData['title'], 'genre' => $postData['genre'], 
                    'beschreibung' => $postData['beschreibung'], 'erscheinungsjahr' => $postData['erscheinungsjahr'], 
                    'laufzeit' => $postData['laufzeit'], 'regisseur' => $postData['regisseur'],
                    'usk' => $postData['usk'], 'price' => $postData['price']
                ];
                $newId = $this->mediaRepository->createMovie($movieData, $posterPath);
                $this->auditLogRepository->log($creatorUserId, 'FILM_ERSTELLT', ['MovieId' => $newId, 'Moviename' => $movieData['moviename']]);
                return $newId;
            } elseif ($mediaType === 'series') {
                $seriesData = [
                    'title' => $postData['title'], 'genre' => $postData['genre'], 
                    'beschreibung' => $postData['beschreibung'], 'start_year' => $postData['start_year'], 
                    'end_year' => $postData['end_year'], 'seasons' => $postData['seasons'],
                    'creator' => $postData['creator'], 'price_per_season' => $postData['price']
                ];
                $newId = $this->mediaRepository->createSeries($seriesData, $posterPath);
                $this->auditLogRepository->log($creatorUserId, 'SERIE_ERSTELLT', ['SeriesId' => $newId, 'SeriesTitle' => $seriesData['title']]);
                return $newId;
            }
        } catch (Exception $e) {
            // Wenn der DB-Eintrag fehlschlägt, lösche das gerade hochgeladene Poster wieder.
            if ($posterPath && !str_contains($posterPath, 'placeholder.png')) {
                @unlink(dirname(__DIR__, 2) . '/' . $posterPath);
            }
            // Werfe die Exception weiter, damit der Controller sie behandeln kann.
            throw $e;
        }

        throw new Exception("Ungültiger Medientyp angegeben.");
    }

    /**
     * Aktualisiert einen vorhandenen Medieneintrag.
     */
    public function updateMedia(string $mediaType, int $mediaId, array $postData, array $fileData, int $editorUserId): void
    {
        $newPosterPath = null;
        if (isset($fileData['error']) && $fileData['error'] === UPLOAD_ERR_OK) {
            $newPosterPath = $this->imageUploadService->handlePosterUpload($fileData, $postData['title'] ?? 'media', $mediaType);
        }

        if ($mediaType === 'movie') {
            $this->mediaRepository->updateMovie($mediaId, $postData, $newPosterPath);
            $this->auditLogRepository->log($editorUserId, 'FILM_GEAENDERT', ['MovieId' => $mediaId, 'Title' => $postData['title']]);
        } elseif ($mediaType === 'series') {
            $this->mediaRepository->updateSeries($mediaId, $postData, $newPosterPath);
            $this->auditLogRepository->log($editorUserId, 'SERIE_GEAENDERT', ['SeriesId' => $mediaId, 'Title' => $postData['title']]);
        } else {
            throw new Exception("Ungültiger Medientyp für Update.");
        }
    }

    /**
     * Löscht einen Medieneintrag.
     */
    public function deleteMedia(string $mediaType, int $mediaId, int $editorUserId): string
    {
        if ($mediaType === 'movie') {
            $media = $this->mediaRepository->findMovieOrFail($mediaId);
            if (!$media) throw new Exception("Film nicht gefunden.");
            
            $title = $media['Moviename'];
            $this->mediaRepository->deleteMovie($mediaId);
            $this->auditLogRepository->log($editorUserId, 'FILM_GELOESCHT', ['MovieId' => $mediaId, 'Moviename' => $title]);
            return $title;

        } elseif ($mediaType === 'series') {
            $media = $this->mediaRepository->findSeriesOrFail($mediaId);
            if (!$media) throw new Exception("Serie nicht gefunden.");
            
            $title = $media['Title'];
            $this->mediaRepository->deleteSeries($mediaId);
            $this->auditLogRepository->log($editorUserId, 'SERIE_GELOESCHT', ['SeriesId' => $mediaId, 'SeriesTitle' => $title]);
            return $title;
        }
        throw new Exception("Ungültiger Medientyp für Löschung.");
    }
}
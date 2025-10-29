<?php
// app/ViewModels/OrderDetailViewModel.php

namespace App\ViewModels;

class OrderDetailViewModel
{
    public string $ticketId;
    public string $bestellDatum;
    public string $zahlungsStatus;
    public string $price;
    public string $titel;
    public string $posterPath;
    public string $genre;
    public string $erscheinungsjahr;
    public string $laufzeit;
    public ?int $mediaId;
    public int $statusProzent;
    public string $statusInfo;
    public string $rechnungUrl;
    public ?string $mediaDetailUrl = null;

    public function __construct(array $rawOrder, array $config)
    {
        // === Daten zuweisen & formatieren ===
        $this->ticketId = $rawOrder['TicketId'] ?? 0;
        $this->bestellDatum = date('d.m.Y', strtotime($rawOrder['Bestelldatum'] ?? 'now'));
        $this->zahlungsStatus = $rawOrder['Zahlungsstatus'] ?? 'Unbekannt';
        $this->price = number_format($rawOrder['Price'] ?? 0.00, 2, ',', '.');
        $this->titel = $rawOrder['Titel'] ?? 'Titel nicht verfügbar';
        $this->posterPath = $config['base_url'] . '/' . ($rawOrder['PosterPath'] ?? 'img/movieImg/placeholder.png');
        $this->genre = $rawOrder['Genre'] ?? 'N/A';
        $this->erscheinungsjahr = $rawOrder['Erscheinungsjahr'] ?? 'N/A';
        $this->laufzeit = $rawOrder['Laufzeit'] ?? 'N/A';
        $this->mediaId = $rawOrder['MediaId'] ?? null;

        // KORREKTUR: Der Link zur PDF-Rechnung wird jetzt mit der base_url erstellt.
        $this->rechnungUrl = $config['base_url'] . '/rechnung_pdf.php?id=' . $this->ticketId;

        if ($this->mediaId) {
            $mediaTypeSlug = ($rawOrder['ProduktTyp'] ?? 'movie') === 'series' ? 'series' : 'movie';
            $this->mediaDetailUrl = $config['base_url'] . '/' . $mediaTypeSlug . '/' . ($rawOrder['slug'] ?? $this->mediaId);
        }

        // === Status-Logik ===
        $endDatum = $rawOrder['EndDatum'] ?? null;
        $isActive = $endDatum ? strtotime($endDatum) > time() : false;

        if ($this->zahlungsStatus === 'Storniert') {
            $this->statusProzent = 0;
            $this->statusInfo = 'Am ' . $this->bestellDatum . ' storniert';
        } elseif ($this->zahlungsStatus === 'Offen') {
            $this->statusProzent = 33;
            $this->statusInfo = 'Rechnung ist noch nicht beglichen.';
        } elseif ($isActive) {
            $this->statusProzent = 66;
            $this->statusInfo = 'Gültig bis ' . date('d.m.Y', strtotime($endDatum));
        } else {
            $this->statusProzent = 100;
            $this->statusInfo = 'Ausleihe abgelaufen am ' . date('d.m.Y', strtotime($endDatum));
        }
    }
}
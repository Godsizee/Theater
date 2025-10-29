<?php
namespace App\ViewModels;

class OrderListItemViewModel
{
    public string $ticketId;
    public string $bestellDatum;
    public string $total;
    public string $movieName;
    public string $posterPath;
    public string $statusText;
    public string $statusInfo;
    public string $detailUrl;
    public string $rowId;
    public bool $isUserContext;
    public bool $canBeCancelled;

    public function __construct(array $rawOrder, array $config, string $context = 'user')
    {
        $this->ticketId = $rawOrder['TicketId'] ?? 0;
        $this->bestellDatum = date('d.m.Y', strtotime($rawOrder['Bestelldatum'] ?? 'now'));
        $this->total = number_format($rawOrder['Total'] ?? 0.00, 2, ',', '.');
        $this->movieName = $rawOrder['Moviename'] ?? 'Titel nicht verfügbar';
        $this->posterPath = $config['base_url'] . '/' . ($rawOrder['PosterPath'] ?? 'img/movieImg/placeholder.png');
        $this->rowId = 'ticket-row-' . $this->ticketId;
        
        $this->isUserContext = ($context === 'user');
        
        // KORREKTUR: Die URL-Generierung für den Support-Kontext wurde angepasst
        if ($this->isUserContext) {
            $this->detailUrl = $config['base_url'] . '/bestellung/' . $this->ticketId;
        } else {
            $this->detailUrl = $config['base_url'] . '/support/bestellungsdetails_ansicht/' . $this->ticketId;
        }

        $endDatum = $rawOrder['EndDatum'] ?? null;
        $zahlungsStatus = $rawOrder['Zahlungsstatus'] ?? 'Unbekannt';
        $isActive = $endDatum ? strtotime($endDatum) > time() : false;

        $this->canBeCancelled = ($zahlungsStatus === 'Offen');

        if ($zahlungsStatus === 'Storniert') {
            $this->statusText = 'Storniert';
            $this->statusInfo = 'Am ' . $this->bestellDatum . ' storniert';
        } elseif ($zahlungsStatus === 'Offen') {
            $this->statusText = 'Offen';
            $this->statusInfo = 'Rechnung ist noch nicht beglichen.';
        } elseif ($isActive) {
            $this->statusText = 'Aktiv';
            $this->statusInfo = 'Gültig bis ' . date('d.m.Y', strtotime($endDatum));
        } else {
            $this->statusText = 'Abgelaufen';
            $this->statusInfo = 'Ausleihe abgelaufen am ' . date('d.m.Y', strtotime($endDatum));
        }
    }
}
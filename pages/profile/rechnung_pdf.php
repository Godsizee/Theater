<?php
// pages/profile/rechnung_pdf.php

require_once dirname(__DIR__, 2) . '/init.php';

// Pfad zum Font-Verzeichnis der PDF-Bibliothek
define('FPDF_FONTPATH', dirname(__DIR__, 2) . '/src/lib/tfpdf/font/');

// Die externe PDF-Bibliothek laden
require_once dirname(__DIR__, 2) . '/src/lib/tfpdf/tfpdf.php';

// Zugriffskontrolle
if (!isset($_SESSION['user_id'])) {
    die("Zugriff verweigert.");
}

$ticket_id = $matches[0] ?? ($_GET['id'] ?? null);
if (!$ticket_id) {
    die("Keine Rechnungs-ID angegeben.");
}

$user_id = $_SESSION['user_id'];
$invoice_data = null;

try {
    // Daten über das Repository abrufen
    $auditLogRepository = new \App\Repositories\AuditLogRepository($pdo);
    $ticketRepository = new \App\Repositories\TicketRepository($pdo, $auditLogRepository);
    
    $invoice_data = $ticketRepository->findForUser((int)$ticket_id, $user_id);

    if (!$invoice_data) {
        die("Rechnung nicht gefunden oder Zugriff verweigert.");
    }
} catch (PDOException $e) {
    error_log("PDF-Erstellungsfehler: " . $e->getMessage());
    die("Datenbankfehler: " . $e->getMessage());
}

// PDF-Generierung
class PDF extends tFPDF
{
    function Header()
    {
        // KORREKTUR: Pfad zeigt jetzt korrekt auf /Theater/public/assets/images/
        $projectRoot = dirname(__DIR__, 2); // ergibt C:\xampp\htdocs\files\Theater
        $this->Image($projectRoot . '/public/assets/images/Bannerpdf.png', 10, 6, 60);
        $this->Image($projectRoot . '/public/assets/images/pingu.png', 180, 6, 20);

        $this->SetDrawColor(220, 220, 220);
        $this->Line(10, 32, 200, 32);
        $this->SetY(35);
        $this->SetFont('Oswald', '', 9);
        $this->SetTextColor(0, 0, 0);
        $this->Cell(0, 4, 'Kundenservice: 040-6462321  |  E-Mail: service@filmverleih.net  |  Bestellservice: 0180-5 10 20', 0, 2, 'C');
        $this->Ln(5);
    }

    function Footer()
    {
        $this->SetY(-30);
        $this->SetFont('Oswald', '', 8);
        $this->SetTextColor(128, 128, 128);
        $this->Cell(0, 4, 'Filmverleih GmbH - Haldesdorfer Strasse 61 - 22179 Hamburg', 0, 2, 'L');
        $this->Cell(0, 4, 'Handelsregister: AG Hamburg HRB 36455 - USt-Id-Nr. DE 811 14 69 64', 0, 2, 'L');
        $this->Ln(2);
        $this->Cell(0, 4, 'Bankverbindung: Hanseatic Bank Hamburg - IBAN: DE68 20120700 3100 7555 55 - BIC: HSTBDEHHXXX', 0, 2, 'L');
    }
}

$pdf = new PDF('P', 'mm', 'A4');
$pdf->AddFont('Oswald', 'B', 'Oswald-Bold.ttf', true);
$pdf->AddFont('Oswald', '', 'Oswald-Regular.ttf', true);
$pdf->AliasNbPages();
$pdf->AddPage();

// Der Rest der PDF-Logik bleibt unverändert
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('Oswald', '', 9);
$pdf->SetXY(20, 50);
$pdf->Cell(80, 4, ($invoice_data['Vorname'] ?? '') . ' ' . ($invoice_data['Nachname'] ?? ''), 0, 2);
$pdf->Cell(80, 4, ($invoice_data['Strasse'] ?? '') . ' ' . ($invoice_data['Hausnummer'] ?? ''), 0, 2);
$pdf->Cell(80, 4, ($invoice_data['PLZ'] ?? '') . ' ' . ($invoice_data['Ort'] ?? ''), 0, 2);
$pdf->SetFont('Oswald', 'B', 10);
$pdf->SetXY(110, 50);
$pdf->Cell(40, 5, 'Lieferadresse', 0, 2);
$pdf->SetFont('Oswald', '', 9);
$pdf->Cell(40, 5, ($invoice_data['Vorname'] ?? '') . ' ' . ($invoice_data['Nachname'] ?? ''), 0, 2);
$pdf->Cell(40, 5, ($invoice_data['Strasse'] ?? '') . ' ' . ($invoice_data['Hausnummer'] ?? ''), 0, 2);
$pdf->Cell(40, 5, ($invoice_data['PLZ'] ?? '') . ' ' . ($invoice_data['Ort'] ?? ''), 0, 2);
$pdf->SetXY(130, 65);
$pdf->SetFont('Oswald', 'B', 16);
$pdf->Cell(70, 8, 'Ihre Rechnung', 0, 2, 'R');
$pdf->SetFont('Oswald', '', 10);
$pdf->SetXY(140, 75);
$pdf->Cell(30, 5, 'Kunden-Nr.:', 0, 0, 'L');
$pdf->Cell(30, 5, $invoice_data['UserId'] ?? '', 0, 1, 'R');
$pdf->SetXY(140, 80);
$pdf->Cell(30, 5, 'Rechnungs-Nr.:', 0, 0, 'L');
$pdf->Cell(30, 5, $invoice_data['TicketId'] ?? '', 0, 1, 'R');
$pdf->SetXY(140, 85);
$pdf->Cell(30, 5, 'Rechnungs-Datum:', 0, 0, 'L');
$pdf->Cell(30, 5, date('d.m.Y', strtotime($invoice_data['Bestelldatum'] ?? 'now')), 0, 1, 'R');
$pdf->Ln(10);
$pdf->SetFont('Oswald', 'B', 10);
$pdf->Cell(0, 6, 'Hallo ' . ($invoice_data['Vorname'] ?? '') . ' ' . ($invoice_data['Nachname'] ?? '') . ',', 0, 1);
$pdf->SetFont('Oswald', '', 10);
$pdf->MultiCell(0, 5, 'vielen Dank fuer Ihre Bestellung. Wir hoffen, dass Sie viel Freude an der Ware haben und alles zu Ihrer Zufriedenheit ausgefallen ist.', 0, 1);
$pdf->Ln(10);
$pdf->SetFont('Oswald', 'B', 9);
$pdf->SetFillColor(245, 245, 245);
$pdf->SetTextColor(80, 80, 80);
$pdf->Cell(88, 7, 'Artikelbezeichnung', 0, 0, 'L', true);
$pdf->Cell(30, 7, 'Bestell-Nr.', 0, 0, 'L', true);
$pdf->Cell(20, 7, 'Menge', 0, 0, 'C', true);
$pdf->Cell(26, 7, 'Preis', 0, 0, 'R', true);
$pdf->Cell(26, 7, 'Betrag', 0, 1, 'R', true);
$pdf->SetDrawColor(220, 220, 220);
$pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('Oswald', '', 9);
$pdf->Ln(2);
$price = $invoice_data['Price'] ?? 0.00;
$pdf->Cell(88, 6, $invoice_data['Titel'] ?? 'Unbekannter Artikel', 0, 0, 'L');
$pdf->Cell(30, 6, $invoice_data['TicketId'] ?? '', 0, 0, 'L');
$pdf->Cell(20, 6, '1 ST', 0, 0, 'C');
$pdf->Cell(26, 6, number_format($price, 2, ',', '.') . ' EUR', 0, 0, 'R');
$pdf->Cell(26, 6, number_format($price, 2, ',', '.') . ' EUR', 0, 1, 'R');
$pdf->Ln(5);
$pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
$pdf->Ln(2);
$pdf->Cell(134, 6, '', 0, 0);
$pdf->Cell(30, 6, 'Summe netto', 0, 0, 'L');
$pdf->Cell(26, 6, number_format($price / 1.19, 2, ',', '.') . ' EUR', 0, 1, 'R');
$pdf->Cell(134, 6, '', 0, 0);
$pdf->Cell(30, 6, 'Versandkosten', 0, 0, 'L');
$pdf->Cell(26, 6, '0,00 EUR', 0, 1, 'R'); // Angenommene Versandkosten
$pdf->Cell(134, 8, '', 0, 0);
$pdf->Cell(56, 8, 'Rechnungsbetrag', 'T', 1, 'L');
$pdf->SetY($pdf->GetY() - 8);
$pdf->SetX(174);
$pdf->SetFont('Oswald', 'B', 10);
$pdf->Cell(26, 8, number_format($price, 2, ',', '.') . ' EUR', 'T', 1, 'R');
$pdf->Ln(5);
$pdf->SetFont('Oswald', '', 9);
$pdf->Cell(0, 5, 'Lieferdatum entspricht Rechnungsdatum.', 0, 1);
$pdf->Cell(0, 5, 'Die Rechnung wurde per ' . htmlspecialchars($invoice_data['Zahlungsstatus'] ?? '') . ' bezahlt.', 0, 1);
$pdf->Ln(5);
$pdf->SetFont('Oswald', 'B', 9);
$pdf->Cell(47, 6, 'Steuersatz %', 'B', 0, 'L');
$pdf->Cell(47, 6, 'MwSt EUR', 'B', 0, 'R');
$pdf->Cell(47, 6, 'Netto EUR', 'B', 0, 'R');
$pdf->Cell(49, 6, 'Brutto EUR', 'B', 1, 'R');
$pdf->SetFont('Oswald', '', 9);
$mwst_total = $price - ($price / 1.19);
$pdf->Cell(47, 6, '19', 0, 0, 'L');
$pdf->Cell(47, 6, number_format($mwst_total, 2, ',', '.'), 0, 0, 'R');
$pdf->Cell(47, 6, number_format($price / 1.19, 2, ',', '.'), 0, 0, 'R');
$pdf->Cell(49, 6, number_format($price, 2, ',', '.'), 0, 1, 'R');
$pdf->Ln(10);
$pdf->SetFont('Oswald', 'B', 10);
$pdf->Cell(0, 6, 'Herzliche Grüße', 0, 1);
$pdf->Cell(0, 6, 'Ihr Filmverleih-Team', 0, 1);

$pdf->Output('I', 'rechnung-' . ($invoice_data['TicketId'] ?? '0') . '.pdf');
exit();
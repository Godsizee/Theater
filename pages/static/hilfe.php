<?php
// Logik befindet sich jetzt in StaticPageController@show
// Variablen werden vom Controller bereitgestellt.
include dirname(__DIR__, 2) . '/templates/header.php';
?>

<div class="page-wrapper">
    <h1 class="main-title">Hilfe & FAQ</h1>
    <p>Hier finden Sie Antworten auf häufig gestellte Fragen.</p>

    <div class="collapsible-section" style="margin-top: 2rem;">
        <button class="collapsible-header" aria-expanded="false">
            <span>Wie kann ich einen Film ausleihen?</span>
            <span class="collapsible-chevron">&rsaquo;</span>
        </button>
        <div class="collapsible-content">
            <p>
                Suchen Sie sich einen Film oder eine Serie aus unserer Auswahl aus, klicken Sie auf "In den Warenkorb" und gehen Sie anschließend zur Kasse. Nach erfolgreicher Bezahlung finden Sie Ihre ausgeliehenen Inhalte sofort in Ihrem Profil unter "Meine Bestellungen".
            </p>
        </div>
    </div>

    <div class="collapsible-section">
        <button class="collapsible-header" aria-expanded="false">
            <span>Wie lange kann ich einen ausgeliehenen Inhalt ansehen?</span>
            <span class="collapsible-chevron">&rsaquo;</span>
        </button>
        <div class="collapsible-content">
            <p>
                Nach dem Kauf haben Sie 30 Tage Zeit, die Wiedergabe zu starten. Sobald Sie die Wiedergabe begonnen haben, können Sie den Inhalt für 48 Stunden beliebig oft ansehen.
            </p>
        </div>
    </div>

     <div class="collapsible-section">
        <button class="collapsible-header" aria-expanded="false">
            <span>Kann ich meine persönlichen Daten ändern?</span>
            <span class="collapsible-chevron">&rsaquo;</span>
        </button>
        <div class="collapsible-content">
            <p>
                Ja, in Ihrem Profil unter "Persönliche Daten" können Sie Ihre Anschrift, E-Mail-Adresse und Ihr Passwort jederzeit ändern. Beachten Sie, dass Ihr Vorname und Geburtsdatum aus Sicherheitsgründen nur einmalig festgelegt werden können.
            </p>
        </div>
    </div>

</div>

<?php
include dirname(__DIR__, 2) . '/templates/footer.php';
?>
<?php
// Logik befindet sich jetzt in StaticPageController@show
// Variablen werden vom Controller bereitgestellt.
$is_ajax = isset($is_ajax_request);

if (!$is_ajax) {
    include dirname(__DIR__, 2) . '/templates/header.php';
} else {
    echo "<script>document.title = '" . addslashes($page_title) . "'; document.body.className = '$body_class';</script>";
}
?>

<div class="page-wrapper">
    <div class="container container-lg">

        <h1 class="main-title text-center">Datenschutz: Die geheimen Akten</h1>
        <p class="text-center page-intro">
            In der Welt des Films ist das Drehbuch heilig. Auf dieser Webseite ist es dein Skript – deine Daten.<br>
            Hier legen wir offen, wie wir mit deinen persönlichen Informationen umgehen, damit du die Hauptrolle unbesorgt genießen kannst.
        </p>

        <div class="content-box">
            <h2>Akt 1: Die Studioleitung (Verantwortlicher)</h2>
            <p>
                Verantwortlich für die Regie bei der Datenverarbeitung ist der im <a href="<?php echo $config['base_url']; ?>/impressum" data-spa-link>Impressum</a> genannte Produzent:
            </p>
            <p>
                <strong>Silas Lumière</strong><br>
                Am Zelluloid-Hain 16<br>
                42113 Kulissenstadt<br>
                E-Mail: kontakt@zelluloid-hain.com
            </p>
        </div>

        <div class="content-box content-box--spaced">
            <h2>Akt 2: Das Logbuch der Technik (Server-Log-Dateien)</h2>
            <p>
                Jedes Mal, wenn du unsere digitale Bühne betrittst, zeichnet unser Server (der Aufnahmeleiter) automatisch Informationen auf. Diese sogenannten Server-Log-Dateien sind technisch notwendig, um den reibungslosen Ablauf der Vorstellung zu garantieren.
            </p>
            <p>Aufgezeichnet werden unter anderem:</p>
            <ul>
                <li>Browsertyp und Browserversion</li>
                <li>Verwendetes Betriebssystem</li>
                <li>Die zuvor besuchte Seite (Referrer URL)</li>
                <li>Hostname des zugreifenden Rechners (IP-Adresse)</li>
                <li>Uhrzeit der Serveranfrage</li>
            </ul>
            <p>
                Diese Daten werden nicht mit anderen Datenquellen zusammengeführt und dienen lediglich der technischen Überwachung und Sicherheit des Sets. Die IP-Adresse wird als notwendig für die Auslieferung der Show betrachtet.
            </p>
        </div>
        
        <div class="content-box content-box--spaced">
            <h2>Akt 3: Dein Casting (Benutzerkonto & Registrierung)</h2>
            <p>
                Wenn du dich entscheidest, bei unserer Produktion eine feste Rolle zu übernehmen (dich zu registrieren), benötigen wir ein paar Angaben für deine persönliche Rollenkarte:
            </p>
            <ul>
                <li><strong>Benutzername:</strong> Dein Künstlername auf unserer Plattform.</li>
                <li><strong>E-Mail-Adresse:</strong> Unser direkter Draht zu dir für wichtige Mitteilungen.</li>
                <li><strong>Passwort:</strong> Wird sicher verschlüsselt (gehasht) gespeichert und ist selbst für uns nicht lesbar.</li>
                <li><strong>Geburtsdatum (optional):</strong> Eine freiwillige Angabe für dein Charakterprofil.</li>
            </ul>
            <p>
                Diese Daten sind dein Schlüssel zur Welt hinter den Kulissen (z.B. als Admin) und werden ausschließlich zur Verwaltung deines Kontos verwendet. Rechtsgrundlage für diesen Vertrag ist Art. 6 Abs. 1 lit. b DSGVO. Deine Daten bleiben so lange gespeichert, wie du Teil unserer Besetzung bist.
            </p>
        </div>

        <div class="content-box content-box--spaced">
            <h2>Akt 4: Das Popcorn der Technik (Cookies)</h2>
            <p>
                Unsere Webseite verwendet "Session-Cookies". Das sind kleine Textdateien, die wie eine Eintrittskarte funktionieren. Sie merken sich, dass du eingeloggt bist, während du dich durch die verschiedenen Szenen (Seiten) bewegst. Diese Cookies sind technisch notwendig und werden automatisch gelöscht, wenn du die Vorstellung verlässt (den Browser schließt).
            </p>
        </div>

        <div class="disclaimer-section">
            <h2 class="text-center">Finaler Akt: Deine Rechte als Hauptdarsteller</h2>
            <p class="text-center">Als Star unserer Produktion hast du jederzeit die volle Kontrolle über dein Skript. Dir stehen folgende Rechte zu:</p>
            <ul>
                <li><strong>Recht auf Auskunft:</strong> Du kannst jederzeit eine Kopie der Daten verlangen, die wir über dich gespeichert haben.</li>
                <li><strong>Recht auf Berichtigung:</strong> Sollte in deinem Skript ein Fehler sein, korrigieren wir ihn auf deinen Wunsch.</li>
                <li><strong>Recht auf Löschung ("Recht auf Vergessenwerden"):</strong> Du kannst verlangen, dass wir deine Rolle aus dem Film schneiden und deine Daten löschen, solange keine gesetzlichen Aufbewahrungspflichten (z.B. für Rechnungen) dem entgegenstehen.</li>
                <li><strong>Recht auf Einschränkung der Verarbeitung:</strong> Du kannst verlangen, dass wir deine Daten zwar aufbewahren, aber nicht weiter verwenden.</li>
                <li><strong>Recht auf Widerspruch:</strong> Du kannst der Verarbeitung deiner Daten widersprechen.</li>
                <li><strong>Recht auf Datenübertragbarkeit:</strong> Du kannst verlangen, dass wir dir deine Daten in einem gängigen, maschinenlesbaren Format aushändigen.</li>
                <li><strong>Beschwerderecht:</strong> Wenn du glaubst, dass wir uns nicht an das Drehbuch halten, kannst du dich bei der zuständigen Aufsichtsbehörde beschweren.</li>
            </ul>
             <p class="text-center" style="margin-top: 20px;">Um deine Rechte geltend zu machen, genügt eine E-Mail an die im Impressum genannte Adresse.</p>
        </div>

        <p class="text-center page-signature">
            Wir hoffen, dieses "Making-of" war aufschlussreich. Vorhang auf für den Film!
        </p>

    </div>
</div>

<?php
if (!$is_ajax) {
    include dirname(__DIR__, 2) . '/templates/footer.php';
}
?>

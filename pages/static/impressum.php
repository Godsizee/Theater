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

        <h1 class="main-title text-center">Impressum: The Legal Cut</h1>
        <p class="text-center page-intro">Hinter den Kulissen dieses digitalen Filmtheaters. Hier ist das Team, das für die Produktion verantwortlich ist.</p>

        <div class="content-box">
            <h2>Eine Produktion von:</h2>
            
            <p>
                <strong>Regie & Drehbuch (Verantwortlich für den Inhalt nach § 5 TMG):</strong><br>
                Silas Lumière
            </p>
            
            <p>
                <strong>Produktionsstudio (Anschrift):</strong><br>
                Am Zelluloid-Hain 16<br>
                42113 Kulissenstadt
            </p>
        </div>

        <div class="content-box content-box--spaced">
            <h2>Kommunikation & Funk (Kontakt):</h2>
            <p>
                <strong>E-Mail:</strong> kontakt@zelluloid-hain.com<br>
                <strong>Telefon (optional):</strong> 04242 / 19 28 357
            </p>
            <p>
                Bei Fragen, Wünschen oder Popcorn-Bestellungen bitte über die oben genannten Kanäle melden.
            </p>
        </div>

        <div class="disclaimer-section">
            <h2 class="text-center">Das juristische Bonusmaterial (Haftungsausschluss)</h2>

            <div class="form-grid" style="gap: 40px;">
                <div class="form-column">
                    <h4>Szene 1: Haftung für Inhalte</h4>
                    <p>Als Hauptdarsteller auf dieser Bühne sind wir gemäß § 7 Abs.1 TMG für eigene Inhalte auf diesen Seiten nach den allgemeinen Gesetzen verantwortlich. Nach §§ 8 bis 10 TMG sind wir als Diensteanbieter jedoch nicht verpflichtet, übermittelte oder gespeicherte fremde Informationen zu überwachen oder nach Umständen zu forschen, die auf eine rechtswidrige Tätigkeit hinweisen. Das Skript für unsere eigenen Szenen schreiben wir mit größter Sorgfalt, eine Garantie für die Richtigkeit können wir aber nicht geben.</p>
                </div>
                <div class="form-column">
                    <h4>Szene 2: Haftung für Links</h4>
                    <p>Unsere Produktion enthält Links zu externen Websites Dritter (andere Filmsets), auf deren Inhalte wir keinen Einfluss haben. Deshalb können wir für diese fremden Inhalte auch keine Gewähr übernehmen. Für die Inhalte der verlinkten Seiten ist stets der jeweilige Anbieter oder Betreiber der Seiten verantwortlich. Sollte uns eine Szene mit Rechtsverletzung bekannt werden, wird der Link sofort aus dem Drehbuch gestrichen.</p>
                </div>
            </div>
            <div class="text-center" style="margin-top: 30px;">
                <h4>Szene 3: Urheberrecht</h4>
                <p>Die durch die Seitenbetreiber erstellten Inhalte und Werke auf diesen Seiten (Drehbuch, Set-Design, Spezialeffekte) unterliegen dem deutschen Urheberrecht. Die Vervielfältigung, Bearbeitung, Verbreitung und jede Art der Verwertung außerhalb der Grenzen des Urheberrechtes bedürfen der schriftlichen Zustimmung des jeweiligen Autors bzw. Erstellers. Downloads und Kopien dieser Seite sind nur für den privaten, nicht kommerziellen Gebrauch gestattet.</p>
            </div>
        </div>

        <p class="text-center page-signature">
            Und... Schnitt!
        </p>

    </div>
</div>

<?php
if (!$is_ajax) {
    include dirname(__DIR__, 2) . '/templates/footer.php';
}
?>
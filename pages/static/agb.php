<?php
// Logik befindet sich jetzt in StaticPageController@show
// Variablen werden vom Controller bereitgestellt.
$is_ajax = isset($is_ajax_request);

if (!$is_ajax) {
    include dirname(__DIR__, 2) . '/templates/header.php';
} else {
    echo "<script>document.title = '".addslashes($page_title)."'; document.body.className = '$body_class';</script>";
}
?>

<div class="page-wrapper" style="padding-top: 100px;">
    <div class="container container-lg">

        <h1 class="main-title text-center" style="font-size: 3rem; margin-bottom: 20px;">Allgemeine Geschäftsbedingungen (AGB)</h1>
        <p class="text-center" style="margin-bottom: 50px;">
            Das Kleingedruckte für unser großes Kino. Hier sind die Spielregeln für unsere digitale Bühne.
        </p>

        <div class="dashboard-section" style="background-color: #f8f9fa; padding: 30px; border-radius: 8px;">
            <h2 style="border-bottom: 1px solid var(--color-border); padding-bottom: 10px; margin-bottom: 20px;">§ 1 Geltungsbereich</h2>
            <p>
                Diese AGB gelten für alle Bestellungen und Ausleihvorgänge, die über die Webseite des Filmverleihs getätigt werden. Mit einer Bestellung erkennen Sie diese Bedingungen an. Abweichende Bedingungen des Kunden werden nicht anerkannt, es sei denn, wir stimmen ihrer Geltung ausdrücklich schriftlich zu.
            </p>

            <h2 style="border-bottom: 1px solid var(--color-border); padding-bottom: 10px; margin-bottom: 20px; margin-top: 30px;">§ 2 Vertragsabschluss</h2>
            <p>
                Die Darstellung der Produkte im Online-Shop stellt kein rechtlich bindendes Angebot, sondern eine Aufforderung zur Bestellung dar. Durch Anklicken des Buttons "Kaufen" geben Sie eine verbindliche Bestellung der im Warenkorb enthaltenen Waren ab. Der Vertrag kommt zustande, sobald Sie von uns eine Bestellbestätigung erhalten.
            </p>
            
            <h2 style="border-bottom: 1px solid var(--color-border); padding-bottom: 10px; margin-bottom: 20px; margin-top: 30px;">§ 3 Leihfrist und Nutzung</h2>
            <p>
                Die Leihfrist für digitale Medien beträgt 30 Tage ab dem Zeitpunkt des Kaufs. Innerhalb dieser Frist können Sie das Medium so oft ansehen, wie Sie möchten. Eine Weitergabe, Vervielfältigung oder öffentliche Vorführung der geliehenen Inhalte ist strengstens untersagt.
            </p>

            <p class="text-center" style="margin-top: 50px; font-family: var(--font-display); font-size: 1.5rem; color: var(--color-secondary);">
                Weitere Paragraphen folgen in Kürze...
            </p>
        </div>
    </div>
</div>

<?php
if (!$is_ajax) {
    include dirname(__DIR__, 2) . '/templates/footer.php';
}
?>
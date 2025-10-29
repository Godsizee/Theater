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

        <h1 class="main-title text-center" style="font-size: 3rem; margin-bottom: 20px;">Unsere Geschichte: Ein Drehbuch voller Leidenschaft</h1>
        <p class="text-center" style="margin-bottom: 50px; font-size: 1.2rem; color: #6c757d;">
            Jeder große Film beginnt mit einer Idee. Unsere war einfach: Das Gefühl eines magischen Kinoabends für jeden zugänglich zu machen – direkt im eigenen Wohnzimmer.
        </p>

        <div class="dashboard-section" style="background-color: #f8f9fa; padding: 40px; border-radius: 8px; margin-bottom: 40px;">
            <h2 style="text-align: center; border-bottom: 1px solid var(--color-border); padding-bottom: 15px; margin-bottom: 30px;">Akt 1: Die Vision</h2>
            <p style="font-size: 1.1rem; line-height: 1.7;">
                Es war einmal in einer Welt voller verpasster Anfangszeiten und überteuertem Popcorn. Ein kleines Team von Film-Enthusiasten – unsere "Gründungs-Crew" – träumte von einer besseren Art, Filme zu erleben. Wir wollten keinen einfachen Streaming-Dienst erschaffen, sondern ein digitales Filmtheater. Ein Ort, an dem Klassiker die Anerkennung bekommen, die sie verdienen, und neue Meisterwerke ein würdiges Zuhause finden. Zelluloid-Hain war geboren – nicht aus einem Geschäftsplan, sondern aus reiner Liebe zur Magie des Kinos.
            </p>
        </div>

        <div class="dashboard-section" style="background-color: #f8f9fa; padding: 40px; border-radius: 8px; margin-bottom: 40px;">
            <h2 style="text-align: center; border-bottom: 1px solid var(--color-border); padding-bottom: 15px; margin-bottom: 30px;">Akt 2: Das Casting</h2>
            <p style="font-size: 1.1rem; line-height: 1.7;">
                Unser Ensemble besteht nicht aus Schauspielern, sondern aus Kuratoren, Technik-Zauberern und Kundenservice-Helden. Jeder Film in unserer Sammlung wird von Hand ausgewählt – wir nennen es "Location Scouting für die Seele". Unser Tech-Team arbeitet unermüdlich hinter den Kulissen, um sicherzustellen, dass jeder Stream so reibungslos läuft wie eine perfekt choreografierte Kampfszene. Und unser Support? Das sind die freundlichen Platzanweiser, die sicherstellen, dass Sie immer den besten Platz im Haus haben.
            </p>
        </div>

        <div class="dashboard-section" style="background-color: #f8f9fa; padding: 40px; border-radius: 8px;">
            <h2 style="text-align: center; border-bottom: 1px solid var(--color-border); padding-bottom: 15px; margin-bottom: 30px;">Akt 3: Die Premiere – Und die Zukunft</h2>
            <p style="font-size: 1.1rem; line-height: 1.7;">
                Was Sie heute sehen, ist unsere Premiere. Aber wie bei jeder guten Filmreihe ist das erst der Anfang. Wir planen bereits die Fortsetzungen: interaktive Filmabende, exklusive Interviews mit "Regisseuren" (unseren Entwicklern) und eine Merchandise-Ecke, die selbst den größten Fan zum Schmunzeln bringt. Unsere Mission ist es, Ihre erste Wahl zu sein, wenn die Worte "Lass uns einen Film schauen" fallen.
            </p>
        </div>

        <p class="text-center" style="margin-top: 60px; font-family: var(--font-display); font-size: 1.8rem; color: var(--color-primary);">
            Vorhang auf für Ihr nächstes Filmabenteuer.
        </p>
        <p class="text-center" style="font-size: 1.5rem; color: var(--color-secondary);">
            Und... Schnitt!
        </p>

    </div>
</div>

<?php
if (!$is_ajax) {
    include dirname(__DIR__, 2) . '/templates/footer.php';
}
?>
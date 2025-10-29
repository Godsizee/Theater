<?php
// Logik befindet sich jetzt in StaticPageController@show
// Die Variablen $page_title und $body_class werden vom Controller bereitgestellt.
include dirname(__DIR__, 2) . '/templates/header.php';
?>

<div class="page-wrapper" style="padding-top: 120px; text-align: center;">
    <div class="container container-lg">
        
        <h1 class="main-title" style="font-size: 3rem; margin-bottom: 1rem;">Hier entsteht Großes!</h1>
        
        <p style="font-size: 1.2rem; line-height: 1.6; color: #666; margin-bottom: 40px;">
            Unsere Einkaufsabteilung kuratiert gerade eine unvergessliche Kollektion, die so unvorhersehbar ist wie ein guter Film-Twist.
        </p>

        <div style="text-align: left; max-width: 600px; margin: 0 auto 40px auto; border-top: 1px solid var(--color-border); padding-top: 30px;">
            <h2 style="text-align: center; margin-bottom: 20px;">Demnächst im Sortiment:</h2>
            <ul style="list-style: none; padding: 0;">
                <li style="background-color: #f8f9fa; padding: 15px; border-radius: 6px; margin-bottom: 15px;">
                    <strong style="font-size: 1.1rem; color: var(--color-primary);">Der "Spiel mir das Lied vom Tod"-Mundharmonika-Halter</strong>
                    <p style="margin: 5px 0 0 0; color: #666;">(Auch für Zahnbürsten geeignet. Hygiene nicht garantiert.)</p>
                </li>
                <li style="background-color: #f8f9fa; padding: 15px; border-radius: 6px;">
                    <strong style="font-size: 1.1rem; color: var(--color-primary);">Ein Set taktischer Hornhauthobel im "John Wick"-Design</strong>
                    <p style="margin: 5px 0 0 0; color: #666;">(Für die besonders harten Fälle. Bleistift nicht enthalten.)</p>
                </li>
            </ul>
        </div>


        <a href="<?php echo $config['base_url']; ?>/" class="btn btn-primary" style="width: auto; padding: 12px 30px;" data-spa-link>Zurück zur Startseite</a>

    </div>
</div>

<?php
include dirname(__DIR__, 2) . '/templates/footer.php';
?>

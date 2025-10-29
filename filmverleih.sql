-- Löscht die Datenbank, falls sie bereits existiert, um einen sauberen Start zu gewährleisten.
DROP DATABASE IF EXISTS filmverleih;

-- Erstellt die Datenbank neu.
CREATE DATABASE IF NOT EXISTS filmverleih;

-- Wählt die erstellte Datenbank für die folgenden Operationen aus.
USE filmverleih;

-- Erstellt die Tabelle für Benutzerkonten.
CREATE TABLE User (
    UserId INT PRIMARY KEY AUTO_INCREMENT,
    Username VARCHAR(255) NOT NULL UNIQUE,
    EMail VARCHAR(255) NOT NULL UNIQUE,
    Password VARCHAR(255) NOT NULL,
    Rolle ENUM('user', 'kundendienst', 'co-admin', 'admin') NOT NULL DEFAULT 'user',
    Birthday DATE NULL
);

-- Erstellt die Tabelle für Kundendaten, die mit der User-Tabelle verknüpft ist.
-- Beim Löschen eines Users wird der zugehörige Kunde ebenfalls gelöscht (ON DELETE CASCADE).
CREATE TABLE Kunde (
    KundeId INT PRIMARY KEY AUTO_INCREMENT,
    Vorname VARCHAR(255) NULL,
    Nachname VARCHAR(255) NULL,
    Strasse VARCHAR(255) NULL,
    Hausnummer VARCHAR(10) NULL,
    Telefon VARCHAR(50) NULL,
    PLZ VARCHAR(10) NULL,
    Ort VARCHAR(255) NULL,
    Land VARCHAR(100) NULL,
    ErstelltAm TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UserId INT NOT NULL UNIQUE,
    FOREIGN KEY (UserId) REFERENCES User(UserId) ON DELETE CASCADE ON UPDATE CASCADE
);

ALTER TABLE `Kunde`
ADD `VornameChanged` TINYINT(1) NOT NULL DEFAULT 0,
ADD `BirthdayChanged` TINYINT(1) NOT NULL DEFAULT 0;

-- Erstellt die Tabelle für Filme.
-- HINZUGEFÜGT: Eine 'slug'-Spalte für saubere URLs (z.B. /movie/film-titel).
CREATE TABLE Movie (
    MovieId INT PRIMARY KEY AUTO_INCREMENT,
    Moviename VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    USK INT,
    Price DECIMAL(10, 2) NOT NULL,
    PosterPath VARCHAR(255) DEFAULT 'img/movieImg/placeholder.png',
    Beschreibung TEXT NULL,
    Erscheinungsjahr YEAR(4) NULL,
    Laufzeit SMALLINT NULL,
    Genre VARCHAR(100) NULL,
    Regisseur VARCHAR(255) NULL
);

-- Erstellt die Tabelle für Serien.
-- HINZUGEFÜGT: Eine 'slug'-Spalte für saubere URLs.
-- KORRIGIERT: 'Regisseur' wurde in 'Creator' umbenannt, um dem PHP-Code zu entsprechen.
CREATE TABLE Series (
    SeriesId INT PRIMARY KEY AUTO_INCREMENT,
    Title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    Episodes INT,
    Price DECIMAL(10, 2) NOT NULL,
    PosterPath VARCHAR(255) DEFAULT 'img/seriesImg/placeholder.png',
    Beschreibung TEXT NULL,
    Erscheinungsjahr YEAR(4) NULL,
    Endjahr YEAR(4) NULL,
    Staffeln INT NULL,
    Genre VARCHAR(100) NULL,
    Creator VARCHAR(255) NULL
);

-- Erstellt die Tabelle für Episoden, die zu einer Serie gehören.
CREATE TABLE Episode (
    EpisodeId INT PRIMARY KEY AUTO_INCREMENT,
    SeriesId INT NOT NULL,
    SeasonNumber INT NOT NULL,
    EpisodeNumber INT NOT NULL,
    Title VARCHAR(255) NOT NULL,
    Description TEXT,
    Laufzeit INT,
    FOREIGN KEY (SeriesId) REFERENCES Series(SeriesId) ON DELETE CASCADE
);

-- Erstellt die Tabelle für Ausleihtickets.
-- KORRIGIERT: Die Tabelle unterstützt jetzt sowohl Filme als auch Serien (polymorph).
-- 'MovieId' wurde durch 'ProduktId' und 'ProduktTyp' ersetzt.
-- Beim Löschen eines Kunden werden seine Tickets ebenfalls gelöscht (ON DELETE CASCADE).
CREATE TABLE Ticket (
    TicketId INT PRIMARY KEY AUTO_INCREMENT,
    Zeitstempel TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    BeginnDatum DATETIME NOT NULL,
    EndDatum DATETIME NOT NULL,
    Zahlungsstatus VARCHAR(50) NOT NULL DEFAULT 'Beglichen',
    KundeId INT,
    ProduktId INT NOT NULL,
    ProduktTyp VARCHAR(50) NOT NULL,
    -- Wird 'movie' oder 'series' enthalten
    FOREIGN KEY (KundeId) REFERENCES Kunde(KundeId) ON DELETE CASCADE
);

-- Erstellt die Tabelle für Audit-Logs zur Nachverfolgung von Aktionen.
-- Wenn ein User gelöscht wird, bleiben seine Log-Einträge erhalten, aber die Verknüpfung wird auf NULL gesetzt.
CREATE TABLE AuditLog (
    LogId INT PRIMARY KEY AUTO_INCREMENT,
    Timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    AusfuehrenderUserId INT,
    Aktionstyp VARCHAR(50) NOT NULL,
    BetroffeneEntitaet VARCHAR(50),
    BetroffeneEntitaetId INT,
    Details TEXT,
    FOREIGN KEY (AusfuehrenderUserId) REFERENCES User(UserId) ON DELETE
    SET
        NULL
);

-- Erstellt die Tabelle zur Protokollierung von fehlgeschlagenen Login-Versuchen.
CREATE TABLE login_attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ip_address VARCHAR(45) NOT NULL,
    username VARCHAR(255) NOT NULL,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Fügt Beispieldaten in die Movie-Tabelle ein, inklusive der neuen 'slug'-Spalte.
INSERT INTO
    `movie`
VALUES
    (
        1,
        'Die Rache des Goldfischs im Gurkenglas',
        'die-rache-des-goldfischs-im-gurkenglas',
        6,
        9.99,
        'img/movieImg/die-rache-des-goldfischs-im-gurkenglas-1749845920.jpeg',
        'Ein gewöhnlicher Goldfisch plant nach Jahren der Gefangenschaft die ultimative Rache an seinen menschlichen Peinigern, angeführt von einem wütenden Gurkenglas.',
        2022,
        95,
        'Komödie',
        'Finn Fischbeck'
    ),
    (
        2,
        'Angriff der Killer-Kiwis aus der Hölle',
        'angriff-der-killer-kiwis-aus-der-hoelle',
        16,
        12.99,
        'img/movieImg/angriff-der-killer-kiwis-aus-der-hoelle-1749842220.jpeg',
        'Nach einem fehlgeschlagenen Genexperiment mutieren harmlose Kiwis zu blutrünstigen Bestien, die eine Kleinstadt terrorisieren.',
        2019,
        110,
        'Horror',
        'Dr. Fruchtnar'
    ),
    (
        3,
        'Mein Friseur ist ein Alien',
        'mein-friseur-ist-ein-alien',
        0,
        7.99,
        'img/movieImg/mein-friseur-ist-ein-alien-1749842551.jpeg',
        'Ein Junge entdeckt, dass sein neuer, exzentrischer Friseur ein Außerirdischer ist, der Haarschnitte als Tarnung nutzt, um die Menschheit zu studieren.',
        2021,
        88,
        'Sci-Fi-Komödie',
        'Zorgon 7'
    ),
    (
        4,
        'Der Werwolf, der lieber strickte',
        'der-werwolf-der-lieber-strickte',
        6,
        11.99,
        'img/movieImg/der-werwolf-der-lieber-strickte-1749842332.jpeg',
        'In einer Vollmondnacht verwandelt sich ein Werwolf nicht in ein reißendes Monster, sondern in einen sanften Kreator von Wollsocken.',
        2018,
        92,
        'Fantasy-Komödie',
        'Luna Wolle'
    ),
    (
        5,
        'Zombies im Schrebergarten: Der letzte Rasen wird gemäht',
        'zombies-im-schrebergarten-der-letzte-rasen-wird-gemaeht',
        18,
        14.99,
        'img/movieImg/zombies-im-schrebergarten-der-letzte-rasen-wird-gemaeht-1750064305.jpg',
        'Eine Zombie-Apokalypse erreicht einen idyllischen Schrebergartenverein, wo die überlebenden Rentner mit Gartengeräten um ihr Gemüsebeet kämpfen.',
        2023,
        105,
        'Horror-Komödie',
        'Hermann Beete'
    ),
    (
        6,
        'Die unglaubliche Reise des einsamen Sockens',
        'die-unglaubliche-reise-des-einsamen-sockens',
        0,
        8.99,
        'img/movieImg/die-unglaubliche-reise-des-einsamen-sockens-1749845946.jpeg',
        'Ein einzelner Socken macht sich auf eine epische Reise durch das Haus, um seinen verschwundenen Partner zu finden und die Waschmaschine zu überlisten.',
        2020,
        75,
        'Animation',
        'Elastana Faser'
    ),
    (
        7,
        'Kung-Fu-Hamster gegen die Ameisen-Armee',
        'kung-fu-hamster-gegen-die-ameisen-armee',
        6,
        10.99,
        'img/movieImg/kung-fu-hamster-gegen-die-ameisen-armee-1749846035.jpeg',
        'Ein kleiner, flauschiger Hamster, der die Kunst des Kung-Fu meistert, muss sein Zuhause vor einer Invasion bösartiger Ameisen verteidigen.',
        2022,
        85,
        'Animation',
        'Meister Fluff'
    ),
    (
        8,
        'Das Geheimnis der quietschenden Badeente',
        'das-geheimnis-der-quietschenden-badeente',
        0,
        9.99,
        'img/movieImg/das-geheimnis-der-quietschenden-badeente-1749842234.jpeg',
        'Eine alte Badeente birgt ein schockierendes Geheimnis, das das Leben einer Familie für immer verändern könnte.',
        2017,
        90,
        'Familienfilm',
        'Quack Quackson'
    ),
    (
        9,
        'Ein Pinguin in der Sahara: Es ist kompliziert',
        'ein-pinguin-in-der-sahara-es-ist-kompliziert',
        6,
        12.99,
        'img/movieImg/ein-pinguin-in-der-sahara-es-ist-kompliziert-1749845975.jpeg',
        'Ein verirrter Pinguin landet versehentlich in der Sahara und muss sich mit der Hitze, dem Sand und den seltsamen Wüstenbewohnern auseinandersetzen.',
        2021,
        98,
        'Abenteuer-Komödie',
        'Herr Frack'
    ),
    (
        10,
        'Der Tag, an dem die Toaster die Weltherrschaft übernahmen',
        'der-tag-an-dem-die-toaster-die-weltherrschaft-uebernahmen',
        12,
        13.99,
        'img/movieImg/der-tag-an-dem-die-toaster-die-weltherrschaft-uebernahmen-1749842004.jpeg',
        'Nach einer plötzlichen Revolte ergreifen intelligente Toaster die Macht und versklaven die Menschheit, um unendlich Toast zu produzieren.',
        2023,
        115,
        'Sci-Fi-Komödie',
        'Prof. Kruste'
    ),
    (
        11,
        'Die Nudisten-WG am Südpol',
        'die-nudisten-wg-am-suedpol',
        16,
        11.99,
        'img/movieImg/die-nudisten-wg-am-suedpol-1749842412.jpeg',
        'Eine Gruppe von Nudisten versucht, eine harmonische Wohngemeinschaft am kältesten Ort der Erde zu gründen, was zu urkomischen Herausforderungen führt.',
        2018,
        95,
        'Komödie',
        'Kaltstarter GmbH'
    ),
    (
        12,
        'Gummibärchen auf dem Mars',
        'gummibaerchen-auf-dem-mars',
        0,
        9.99,
        'img/movieImg/gummibaerchen-auf-dem-mars-1749842465.jpeg',
        'Eine Gruppe tapferer Gummibärchen wird versehentlich zum Mars geschickt und muss einen Weg finden, nach Hause zurückzukehren, bevor sie schmelzen.',
        2020,
        80,
        'Animation',
        'Bärchen Rot'
    ),
    (
        13,
        'Mein Staubsauger will die Weltherrschaft',
        'mein-staubsauger-will-die-weltherrschaft',
        12,
        14.99,
        'img/movieImg/mein-staubsauger-will-die-weltherrschaft-1749842570.jpeg',
        'Ein hochintelligenter Staubsauger entwickelt ein Bewusstsein und plant, die Welt von allem Staub und Schmutz zu befreien – auch von den Menschen.',
        2022,
        100,
        'Sci-Fi-Thriller',
        'Dr. Dreck'
    ),
    (
        14,
        'Die Odyssee der verlorenen Fernbedienung',
        'die-odyssee-der-verlorenen-fernbedienung',
        6,
        10.99,
        'img/movieImg/die-odyssee-der-verlorenen-fernbedienung-1749845900.jpeg',
        'Eine Familie begibt sich auf eine verzweifelte Suche nach ihrer verlorenen Fernbedienung, die sie durch absurde Situationen und unerwartete Abenteuer führt.',
        2021,
        90,
        'Komödie',
        'Couchpotato Jr.'
    ),
    (
        15,
        'Der Kaktus, der kuscheln wollte',
        'der-kaktus-der-kuscheln-wollte',
        0,
        8.99,
        'img/movieImg/der-kaktus-der-kuscheln-wollte-1749842428.jpeg',
        'Ein kleiner, einsamer Kaktus sehnt sich nach Zuneigung und versucht auf unkonventionelle Weise, Freunde zu finden.',
        2019,
        78,
        'Familienfilm',
        'Dornröschen'
    ),
    (
        16,
        'Fluch der Karibik 8: Jack Sparrow sucht seine Brille',
        'fluch-der-karibik-8-jack-sparrow-sucht-seine-brille',
        12,
        15.99,
        'img/movieImg/fluch-der-karibik-8-jack-sparrow-sucht-seine-brille-1749846000.jpeg',
        'Captain Jack Sparrow ist erneut auf hoher See, diesmal nicht auf der Suche nach einem Schatz, sondern nach seiner verlegten Lesebrille, die er für eine wichtige Seekarte braucht.',
        2025,
        140,
        'Abenteuer',
        'Gore Verbinski (nicht mehr)'
    ),
    (
        17,
        'Die haarlose Katze und der allergische Hund auf Weltreise',
        'die-haarlose-katze-und-der-allergische-hund-auf-weltreise',
        6,
        11.99,
        'img/movieImg/die-haarlose-katze-und-der-allergische-hund-auf-weltreise-1750009183.jpeg',
        'Ein ungewöhnliches Duo, bestehend aus einer haarlosen Katze und einem allergischen Hund, begibt sich auf eine turbulente Weltreise, um ein Heilmittel zu finden.',
        2023,
        98,
        'Animation',
        'Tierische Abenteuer Studios'
    ),
    (
        18,
        'Ein Elefant im Porzellanladen und seine Folgen',
        'ein-elefant-im-porzellanladen-und-seine-folgen',
        6,
        12.99,
        'img/movieImg/ein-elefant-im-porzellanladen-und-seine-folgen-1750009922.jpeg',
        'Ein gutmütiger, aber tollpatschiger Elefant sorgt in einem feinen Porzellanladen für Chaos, mit unerwarteten und lustigen Konsequenzen.',
        2020,
        90,
        'Komödie',
        'Rüssel D. Schade'
    ),
    (
        19,
        'Das Schweigen der Lämmer und das laute Blöken der Schafe',
        'das-schweigen-der-laemmer-und-das-laute-bloeken-der-schafe',
        16,
        13.99,
        'img/movieImg/das-schweigen-der-laemmer-und-das-laute-bloeken-der-schafe-1749844029.jpeg',
        'Eine Parodie auf den Klassiker, in der eine Detektivin versucht, das laute und störende Blöken einer Schafherde zu stoppen, die einen Serienkiller in den Wahnsinn treibt.',
        2022,
        108,
        'Satire-Horror',
        'Jonathan Lamb'
    ),
    (
        20,
        'Der Mann, der auf Ziegen starrte und sie zum Lachen brachte',
        'der-mann-der-auf-ziegen-starrte-und-sie-zum-lachen-brachte',
        12,
        11.99,
        'img/movieImg/der-mann-der-auf-ziegen-starrte-und-sie-zum-lachen-brachte-1750015451.jpeg',
        'Ein ehemaliger Journalist entdeckt eine geheime Regierungseinheit, die versucht, Ziegen mit Gedankenkraft zu kontrollieren, und stolpert in ein urkomisches Abenteuer.',
        2021,
        102,
        'Komödie',
        'Goat Whisperer'
    ),
    (
        21,
        'Planet der Affen und der Eichhörnchen-Aufstand',
        'planet-der-affen-und-der-eichhoernchen-aufstand',
        12,
        14.99,
        'img/movieImg/planet-der-affen-und-der-eichhoernchen-aufstand-1750012293.jpeg',
        'Nachdem die Affen die Erde übernommen haben, sehen sie sich einem neuen Feind gegenüber: einer hochorganisierten Eichhörnchen-Armee, die ihre Nüsse zurückfordert.',
        2024,
        120,
        'Sci-Fi-Abenteuer',
        'Nussknacker Filmproduktion'
    ),
    (
        22,
        'Der mit dem Wolf tanzt und ihm dabei auf die Füße tritt',
        'der-mit-dem-wolf-tanzt-und-ihm-dabei-auf-die-fuesse-tritt',
        6,
        9.99,
        'img/movieImg/der-mit-dem-wolf-tanzt-und-ihm-dabei-auf-die-fuesse-tritt-1750007650.jpeg',
        'Ein unbeholfener Siedler versucht, sich mit einem Wolfsrudel anzufreunden, was zu einer Reihe von Missgeschicken und unfreiwilliger Komik führt.',
        2020,
        95,
        'Western-Komödie',
        'Lachwolf Studios'
    ),
    (
        23,
        'Die Ritter der Kokosnuss und die Suche nach dem heiligen Dosenöffner',
        'die-ritter-der-kokosnuss-und-die-suche-nach-dem-heiligen-dosenoeffner',
        12,
        10.99,
        'img/movieImg/die-ritter-der-kokosnuss-und-die-suche-nach-dem-heiligen-dosenoeffner-1750009371.jpeg',
        'Eine neue Generation von Rittern begibt sich auf die abstruse Suche nach einem legendären Dosenöffner, der das letzte Stück Nahrung in einem postapokalyptischen Königreich retten soll.',
        2023,
        90,
        'Fantasy-Komödie',
        'Monty Python (Reboot)'
    ),
    (
        24,
        'E.T. – Der Außerirdische hat sein Handy vergessen',
        'et-der-ausserirdische-hat-sein-handy-vergessen',
        0,
        8.99,
        'img/movieImg/et-der-ausserirdische-hat-sein-handy-vergessen-1750009843.jpeg',
        'E.T. kehrt zur Erde zurück, nicht um nach Hause zu telefonieren, sondern um sein Smartphone zu finden, das er bei seinem letzten Besuch liegen ließ.',
        2022,
        88,
        'Sci-Fi-Familie',
        'Steven Spielburgers Sohn'
    ),
    (
        25,
        'Findet Nemo – Dorie hat ihn schon wieder verloren',
        'findet-nemo-dorie-hat-ihn-schon-wieder-verloren',
        0,
        9.99,
        'img/movieImg/findet-nemo-dorie-hat-ihn-schon-wieder-verloren-1750010070.jpeg',
        'Nemo ist erneut verschwunden, und Dorie muss mit ihrem Kurzzeitgedächtnis kämpfen, um ihn zu finden, während sie neue skurrile Meeresbewohner trifft.',
        2024,
        100,
        'Animation',
        'Pixar Neuauflage'
    ),
    (
        26,
        'Forrest Gump – Die Blase drückt',
        'forrest-gump-die-blase-drueckt',
        6,
        11.99,
        'img/movieImg/forrest-gump-die-blase-drueckt-1750010149.jpeg',
        'Forrest Gump erlebt weiterhin historische Ereignisse, muss aber feststellen, dass seine Blase immer zum ungünstigsten Zeitpunkt drückt.',
        2023,
        130,
        'Drama-Komödie',
        'Robert Zemeckis (Remix)'
    ),
    (
        27,
        'Harry Potter und der Stein der Weisen ist nur ein Kieselstein',
        'harry-potter-und-der-stein-der-weisen-ist-nur-ein-kieselstein',
        6,
        13.99,
        'img/movieImg/harry-potter-und-der-stein-der-weisen-ist-nur-ein-kieselstein-1750010421.jpeg',
        'Harry Potter entdeckt, dass der legendäre Stein der Weisen in Wirklichkeit ein gewöhnlicher Kieselstein ist und muss seine Freunde davon überzeugen, dass Magie nicht von Objekten abhängt.',
        2025,
        145,
        'Fantasy-Parodie',
        'Albus Dumbledore Jr.'
    ),
    (
        28,
        'Herr der Ringe: Die Gefährten haben sich im Wald verlaufen',
        'herr-der-ringe-die-gefaehrten-haben-sich-im-wald-verlaufen',
        12,
        15.99,
        'img/movieImg/herr-der-ringe-die-gefaehrten-haben-sich-im-wald-verlaufen-1750010515.jpeg',
        'Die neun Gefährten verlieren auf ihrem Weg nach Mordor die Orientierung und stolpern von einem Missgeschick ins nächste, immer auf der Suche nach dem richtigen Pfad.',
        2024,
        180,
        'Fantasy-Komödie',
        'Peter Jackson (verlaufen)'
    ),
    (
        29,
        'Jurassic Park – Der Streichelzoo ist eröffnet',
        'jurassic-park-der-streichelzoo-ist-eroeffnet',
        6,
        12.99,
        'img/movieImg/jurassic-park-der-streichelzoo-ist-eroeffnet-1750010848.jpeg',
        'Nach dem Scheitern des ursprünglichen Parks versucht ein neuer Investor, eine familienfreundliche Version zu schaffen, in der Kinder Baby-Dinosaurier streicheln können – mit unvorhersehbaren Folgen.',
        2023,
        110,
        'Abenteuer-Komödie',
        'Dino-Streichel Produktion'
    ),
    (
        30,
        'King Kong und die riesige Bananenschale',
        'king-kong-und-die-riesige-bananenschale',
        6,
        10.99,
        'img/movieImg/king-kong-und-die-riesige-bananenschale-1750011231.jpeg',
        'King Kong verursacht chaos in New York, als er auf einer gigantischen Bananenschale ausrutscht, was zu einer Reihe von Slapstick-Einlagen führt.',
        2021,
        95,
        'Abenteuer-Komödie',
        'Affenstarke Filme'
    ),
    (
        31,
        'Krieg der Sterne: Eine neue Hoffnung auf einen Parkplatz',
        'krieg-der-sterne-eine-neue-hoffnung-auf-einen-parkplatz',
        12,
        14.99,
        'img/movieImg/krieg-der-sterne-eine-neue-hoffnung-auf-einen-parkplatz-1750011777.jpeg',
        'In einer weit, weit entfernten Galaxis kämpfen Rebellen nicht gegen das Imperium, sondern um den letzten freien Parkplatz im Einkaufszentrum.',
        2025,
        135,
        'Sci-Fi-Komödie',
        'George Lucas (Parkplatz Edition)'
    ),
    (
        32,
        'Der Dunkle Ritter hat das Licht angelassen',
        'der-dunkle-ritter-hat-das-licht-angelassen',
        12,
        13.99,
        'img/movieImg/der-dunkle-ritter-hat-das-licht-angelassen-1749844143.jpeg',
        'Batman muss Gotham City retten, während er ständig von Alfred ermahnt wird, die Lichter auszuschalten, um Strom zu sparen.',
        2023,
        120,
        'Action-Komödie',
        'Christopher Nolan (Energiesparmodus)'
    ),
    (
        33,
        'Titanic – Diesmal mit U-Boot-Führerschein',
        'titanic-diesmal-mit-u-boot-fuehrerschein',
        12,
        11.99,
        'img/movieImg/titanic-diesmal-mit-u-boot-fuehrerschein-1750013114.jpeg',
        'Eine Neuinterpretation der klassischen Tragödie, in der Jack versucht, Rose zu beeindrucken, indem er behauptet, einen U-Boot-Führerschein zu besitzen, was zu noch mehr Chaos führt.',
        2022,
        160,
        'Katastrophen-Komödie',
        'James Cameron (Unterwasser-Edition)'
    ),
    (
        34,
        'Spider-Man: Die Heimkehr – Er hat seinen Schlüssel vergessen',
        'spider-man-die-heimkehr-er-hat-seinen-schluessel-vergessen',
        6,
        12.99,
        'img/movieImg/spider-man-die-heimkehr-er-hat-seinen-schluessel-vergessen-1750012627.jpeg',
        'Spider-Man kehrt nach einem langen Tag der Verbrechensbekämpfung nach Hause zurück, nur um festzustellen, dass er seinen Schlüssel vergessen hat und seine Spinnenkräfte nicht helfen können.',
        2024,
        115,
        'Action-Komödie',
        'Marvel Studios (Schlüssel-Edition)'
    ),
    (
        35,
        'Die Schnellen und die Wilden im Stau auf der A3',
        'die-schnellen-und-die-wilden-im-stau-auf-der-a3',
        12,
        10.99,
        'img/movieImg/die-schnellen-und-die-wilden-im-stau-auf-der-a3-1750009654.jpeg',
        'Eine Gruppe von Straßenrennfahrern steckt in einem epischen Stau fest und muss ihre Fähigkeiten einsetzen, um aus der Blechlawine zu entkommen.',
        2023,
        98,
        'Action-Komödie',
        'Vin Diesel (Verkehrschaos)'
    ),
    (
        36,
        'Die unendliche Geschichte – Das Buch hat nur noch 10 Seiten',
        'die-unendliche-geschichte-das-buch-hat-nur-noch-10-seiten',
        6,
        9.99,
        'img/movieImg/die-unendliche-geschichte-das-buch-hat-nur-noch-10-seiten-1750009743.jpeg',
        'Bastian entdeckt, dass die "unendliche Geschichte" nur noch wenige Seiten hat und muss schnell handeln, um Phantásien vor dem endgültigen Nichts zu bewahren.',
        2025,
        105,
        'Fantasy-Komödie',
        'Michael Ende (Kurzfassung)'
    ),
    (
        37,
        'Stirb langsam – Aber bitte nicht auf meinem neuen Teppich',
        'stirb-langsam-aber-bitte-nicht-auf-meinem-neuen-teppich',
        16,
        13.99,
        'img/movieImg/stirb-langsam-aber-bitte-nicht-auf-meinem-neuen-teppich-1750012781.jpeg',
        'John McClane muss in einem neuen Hochhaus Geiseln retten, während er gleichzeitig versucht, seinen brandneuen Teppich vor Blutflecken und Explosionen zu schützen.',
        2024,
        130,
        'Action-Komödie',
        'Bruce Willis (Reinigungsservice)'
    ),
    (
        38,
        'Hübsche Frau und der Frosch ohne Krone',
        'huebsche-frau-und-der-frosch-ohne-krone',
        0,
        8.99,
        'img/movieImg/huebsche-frau-und-der-frosch-ohne-krone-1750010810.jpeg',
        'Eine moderne Märchenadaption, in der eine Frau einen Frosch küsst, aber feststellt, dass er keine Krone hat und somit auch kein Prinz wird – nur ein sehr glitschiger Freund.',
        2022,
        85,
        'Romantik-Komödie',
        'Gebrüder Grimm (Neuzeit)'
    ),
    (
        39,
        'Terminator 2: Tag der Abrechnung – Und der Müll muss auch noch raus',
        'terminator-2-tag-der-abrechnung-und-der-muell-muss-auch-noch-raus',
        16,
        14.99,
        'img/movieImg/terminator-2-tag-der-abrechnung-und-der-muell-muss-auch-noch-raus-1750013019.jpeg',
        'Der T-800 und Sarah Connor müssen nicht nur Skynet aufhalten, sondern auch sicherstellen, dass John den Müll rechtzeitig rausbringt, bevor das Chaos komplett wird.',
        2023,
        140,
        'Sci-Fi-Action-Komödie',
        'James Cameron (Haushaltsedition)'
    ),
    (
        40,
        'Unabhängigkeitstag – Aber es regnet in Strömen',
        'unabhaengigkeitstag-aber-es-regnet-in-stroemen',
        12,
        12.99,
        'img/movieImg/unabhaengigkeitstag-aber-es-regnet-in-stroemen-1750013250.jpeg',
        'Aliens greifen die Erde an, aber der Unabhängigkeitstag wird durch sintflutartige Regenfälle erschwert, die die Abwehr der Menschheit behindern.',
        2024,
        145,
        'Sci-Fi-Katastrophe',
        'Roland Emmerich (Regenzeit)'
    ),
    (
        41,
        'Geisterjäger jagen jetzt Staubmäuse',
        'geisterjaeger-jagen-jetzt-staubmaeuse',
        6,
        10.99,
        'img/movieImg/geisterjaeger-jagen-jetzt-staubmaeuse-1750010290.jpeg',
        'Die Ghostbusters haben ein neues Problem: riesige, aggressive Staubmäuse, die die Stadt heimsuchen und für allergische Reaktionen sorgen.',
        2022,
        90,
        'Komödie',
        'Harold Ramis (Putzedition)'
    ),
    (
        42,
        'Männer in Schwarz, Weiß und Pink',
        'maenner-in-schwarz-weiss-und-pink',
        12,
        11.99,
        'img/movieImg/maenner-in-schwarz-weiss-und-pink-1750012072.jpeg',
        'Agenten J und K müssen undercover in der Modebranche ermitteln, um Außerirdische aufzuspüren, die sich als Designer tarnen und die Erde mit schrecklichen Farbkombinationen bedrohen.',
        2023,
        105,
        'Sci-Fi-Komödie',
        'Mode-Aliens Inc.'
    ),
    (
        43,
        'Der Sechste Sinn – Er sieht alles doppelt',
        'der-sechste-sinn-er-sieht-alles-doppelt',
        16,
        13.99,
        'img/movieImg/der-sechste-sinn-er-sieht-alles-doppelt-1750008091.jpeg',
        'Ein Junge behauptet, er sehe tote Menschen, aber sein Therapeut stellt fest, dass er nur eine starke Brille braucht.',
        2022,
        100,
        'Thriller-Komödie',
        'M. Night Shyamalan (Optiker-Version)'
    ),
    (
        44,
        'Kampfklub – Die erste Regel: Rede nicht über den Kampfklub. Die zweite Regel: Kissen sind erlaubt.',
        'kampfklub-die-erste-regel-kissen-sind-erlaubt',
        18,
        15.99,
        'img/movieImg/kampfklub-die-erste-regel-rede-nicht-ueber-den-kampfklub-die-zweite-regel-kissen-sind-erlaubt-1750011010.jpeg',
        'Eine subversive Satire über einen geheimen Klub, in dem die Mitglieder ihre Aggressionen bei Kissenschlachten abbauen, aber die Regeln sind streng!',
        2024,
        135,
        'Satire-Drama',
        'David Fincher (Plüsch-Edition)'
    ),
    (
        45,
        'Schundliteratur – Die saftige Rache der Orangen',
        'schundliteratur-die-saftige-rache-der-orangen',
        16,
        12.99,
        'img/movieImg/schundliteratur-die-saftige-rache-der-orangen-1750012384.jpeg',
        'Ein Pulp-Fiction-Parodie, in der ein Auftragskillerduo in einen unerwarteten Krieg mit einer Bande intelligenter Orangen gerät, die sich für ihre Ausbeutung rächen wollen.',
        2023,
        140,
        'Krimi-Komödie',
        'Quentin Orangeino'
    ),
    (
        46,
        'Wilde Hunde im örtlichen Tierheim',
        'wilde-hunde-im-oertlichen-tierheim',
        6,
        9.99,
        'img/movieImg/wilde-hunde-im-oertlichen-tierheim-1750064680.jpg',
        'Eine Gruppe von Streunern plant den Ausbruch aus dem örtlichen Tierheim, um die Freiheit zu erlangen und die Weltherrschaft der Hühner zu verhindern.',
        2021,
        88,
        'Animation',
        'Bellfreunde Studios'
    ),
    (
        47,
        'Der Große Lebowski und sein kleiner, nerviger Bruder',
        'der-grosse-lebowski-und-sein-kleiner-nerviger-bruder',
        12,
        11.99,
        'img/movieImg/der-grosse-lebowski-und-sein-kleiner-nerviger-bruder-1749844283.jpeg',
        'Der Dude muss nicht nur seine entführte Frau retten, sondern auch mit seinem hyperaktiven und ständig quengelnden jüngeren Bruder fertig werden.',
        2024,
        118,
        'Komödie',
        'Coen Brothers (Familienausgabe)'
    ),
    (
        48,
        'Kein Land für alte Männer und ihre Gehstöcke',
        'kein-land-fuer-alte-maenner-und-ihre-gehstoecke',
        16,
        14.99,
        'img/movieImg/kein-land-fuer-alte-maenner-und-ihre-gehstoecke-1750011037.jpeg',
        'Eine düstere Parodie über einen Sheriff, der in die Jahre gekommen ist und mit einer Gruppe alter, aber gefährlicher Gangster zu kämpfen hat, die ihre Gehstöcke als Waffen benutzen.',
        2023,
        125,
        'Krimi-Thriller-Komödie',
        'Coen Brothers (Senioren-Edition)'
    ),
    (
        49,
        'Fargo und die Suche nach dem verlorenen Handschuh im Schnee',
        'fargo-und-die-suche-nach-dem-verlorenen-handschuh-im-schnee',
        16,
        13.99,
        'img/movieImg/fargo-und-die-suche-nach-dem-verlorenen-handschuh-im-schnee-1750010056.jpeg',
        'Ein skurriler Kriminalfall in Minnesota, bei dem ein Polizist versucht, einen brutalen Mord aufzuklären, während er gleichzeitig nach seinem verlorenen Handschuh im tiefen Schnee sucht.',
        2025,
        98,
        'Krimi-Komödie',
        'Coen Brothers (Winter-Edition)'
    ),
    (
        50,
        'Die Guten Jungs sind eigentlich ganz nett',
        'die-guten-jungs-sind-eigentlich-ganz-nett',
        12,
        10.99,
        'img/movieImg/die-guten-jungs-sind-eigentlich-ganz-nett-1750009153.jpeg',
        'Eine Komödie über eine Gruppe von Gangstern, die versuchen, ein normales Leben zu führen, aber ständig in absurde Situationen geraten, weil sie einfach zu nett sind.',
        2022,
        105,
        'Krimi-Komödie',
        'Goodfellas Parodie'
    ),
    (
        51,
        'Der Pate und die Patin und ihre chaotische Familie',
        'der-pate-und-die-patin-und-ihre-chaotische-familie',
        16,
        15.99,
        'img/movieImg/der-pate-und-die-patin-und-ihre-chaotische-familie-1750007833.jpeg',
        'Die Corleones müssen sich mit einer neuen Matriarchin auseinandersetzen, die ihre eigene Art von Macht ausübt und die Familie noch chaotischer macht.',
        2024,
        170,
        'Krimi-Drama-Komödie',
        'Francis Ford Coppola (Familienbande)'
    ),
    (
        52,
        'Apokalypse jetzt oder später? Ich habe einen Termin.',
        'apokalypse-jetzt-oder-spaeter-ich-habe-einen-termin',
        18,
        14.99,
        'img/movieImg/apokalypse-jetzt-oder-spaeter-ich-habe-einen-termin-1749843100.jpeg',
        'Ein Soldat wird auf eine Mission geschickt, um einen abtrünnigen Colonel zu finden, aber er muss ständig seine Termine im Auge behalten, da die Apokalypse droht.',
        2023,
        155,
        'Kriegs-Satire',
        'Francis Ford Coppola (Terminkalender)'
    ),
    (
        53,
        'Klingenläufer auf Rollschuhen',
        'klingenlaeufer-auf-rollschuhen',
        12,
        12.99,
        'img/movieImg/klingenlaeufer-auf-rollschuhen-1750011289.jpeg',
        'In einer dystopischen Zukunft jagen Blade Runner auf Rollschuhen Replikanten durch die neonbeleuchteten Straßen, was zu halsbrecherischen Verfolgungsjagden führt.',
        2024,
        108,
        'Sci-Fi-Action',
        'Ridley Scott (Rollschuh-Edition)'
    ),
    (
        54,
        '2001: Eine Odyssee im Weltraum… im Wohnzimmer',
        '2001-eine-odyssee-im-weltraum-im-wohnzimmer',
        6,
        10.99,
        'img/movieImg/2001-eine-odyssee-im-weltraum-im-wohnzimmer-1749842814.jpeg',
        'eine Familie erlebt eine epische Odyssee, während sie versucht, ihren Fernseher im Wohnzimmer zu reparieren, der ein mysteriöses Monolith-Signal empfängt.',
        2023,
        120,
        'Sci-Fi-Komödie',
        'Stanley Kubrick (Heimkino-Edition)'
    ),
    (
        55,
        'Uhrwerk Orange und ein manueller Apfel',
        'uhrwerk-orange-und-ein-manueller-apfel',
        18,
        13.99,
        'img/movieImg/uhrwerk-orange-und-ein-manueller-apfel-1750013125.jpeg',
        'Eine gewalttätige Jugendbande wird mit einer neuen Konditionierungstherapie konfrontiert, die sie dazu zwingt, nur noch Bio-Äpfel zu essen und manuelle Arbeit zu verrichten.',
        2025,
        128,
        'Dystopie-Satire',
        'Stanley Kubrick (Obstgarten-Edition)'
    ),
    (
        56,
        'Das Leuchten, aber es ist nur eine kaputte Lampe',
        'das-leuchten-aber-es-ist-nur-eine-kaputte-lampe',
        16,
        11.99,
        'img/movieImg/das-leuchten-aber-es-ist-nur-eine-kaputte-lampe-1749843893.jpeg',
        'Ein Schriftsteller zieht mit seiner Familie in ein abgelegenes Hotel, wo er den Verstand verliert, weil die Beleuchtung ständig flackert und ihn in den Wahnsinn treibt.',
        2024,
        130,
        'Horror-Komödie',
        'Stephen King (Glühbirnen-Edition)'
    ),
    (
        57,
        'Psycho hat nur einen schlechten Tag',
        'psycho-hat-nur-einen-schlechten-tag',
        16,
        10.99,
        'img/movieImg/psycho-hat-nur-einen-schlechten-tag-1750012376.jpeg',
        'Norman Bates hat einen besonders schlechten Tag, an dem alles schiefläuft, was zu einer Reihe von unglücklichen und komischen Ereignissen im Bates Motel führt.',
        2023,
        95,
        'Horror-Komödie',
        'Alfred Hitchcock (Launenhaft)'
    ),
    (
        58,
        'Bürger Kane und sein sehr nerviger Hund',
        'buerger-kane-und-sein-sehr-nerviger-hund',
        0,
        8.99,
        'img/movieImg/buerger-kane-und-sein-sehr-nerviger-hund-1749843383.jpeg',
        'Die Geschichte des mächtigen Charles Foster Kane, erzählt aus der Perspektive seines übermäßig bellenden und fordernden Hundes, der seine letzten Worte versteht.',
        2022,
        105,
        'Drama-Komödie',
        'Orson Welles (Hunde-Edition)'
    ),
    (
        59,
        'Casablanca in Farbe und mit schlechter Synchronisation',
        'casablanca-in-farbe-und-mit-schlechter-synchronisation',
        6,
        9.99,
        'img/movieImg/casablanca-in-farbe-und-mit-schlechter-synchronisation-1749843583.jpeg',
        'Der Klassiker in einer neuen, farbenfrohen Version, die durch eine grauenhafte Synchronisation unfreiwillig komisch wird.',
        2024,
        98,
        'Romantik-Komödie',
        'Michael Curtiz (Fehlübersetzung)'
    ),
    (
        60,
        'Vom Winde verweht und der darauf folgende schlechte Haartag',
        'vom-winde-verweht-und-der-darauf-folgende-schlechte-haartag',
        0,
        9.99,
        'img/movieImg/vom-winde-verweht-und-der-darauf-folgende-schlechte-haartag-1750064093.jpg',
        'Scarlett O''Hara kämpft im Bürgerkrieg, muss sich aber gleichzeitig mit den Auswirkungen des starken Windes auf ihre Frisur auseinandersetzen.',
        2023,
        160,
        'Drama-Komödie',
        'Victor Fleming (Frisuren-Edition)'
    ),
    (
        61,
        'Der Zauberer von Oz und der schreckliche Modegeschmack der bösen Hexe des Ostens',
        'der-zauberer-von-oz-und-der-schreckliche-modegeschmack-der-boesen-hexe-des-ostens',
        0,
        10.99,
        'img/movieImg/der-zauberer-von-oz-und-der-schreckliche-modegeschmack-der-boesen-hexe-des-ostens-1750008117.jpeg',
        'Dorothy reist nach Oz und entdeckt, dass die wahre Bedrohung nicht die böse Hexe ist, sondern ihr schrecklicher Modegeschmack, der das Land der Munchkins verunstaltet.',
        2022,
        90,
        'Fantasy-Komödie',
        'L. Frank Baum (Modenschau)'
    ),
    (
        62,
        'Singen im Regen mit einem riesigen Regenschirm und Gummistiefeln',
        'singen-im-regen-mit-einem-riesigen-regenschirm-und-gummistiefeln',
        0,
        9.99,
        'img/movieImg/singen-im-regen-mit-einem-riesigen-regenschirm-und-gummistiefeln-1750012422.jpeg',
        'Eine moderne Interpretation des Musicals, in der Gene Kelly mit einem überdimensionierten Regenschirm und kniehohen Gummistiefeln durch die Pfützen tanzt.',
        2023,
        103,
        'Musical-Komödie',
        'Stanley Donen (Wetterfest)'
    ),
    (
        63,
        'Ist das Leben nicht schön? Nein, heute nicht.',
        'ist-das-leben-nicht-schoen-nein-heute-nicht',
        6,
        11.99,
        'img/movieImg/ist-das-leben-nicht-schoen-nein-heute-nicht-1750010828.jpeg',
        'Ein Engel versucht, einen verzweifelten Mann davon zu überzeugen, dass sein Leben wertvoll ist, aber dieser hat einfach nur einen wirklich schlechten Tag.',
        2024,
        110,
        'Drama-Komödie',
        'Frank Capra (Miesepeter-Edition)'
    ),
    (
        64,
        'Sonnenuntergangs-Boulevard bei Sonnenaufgang',
        'sonnenuntergangs-boulevard-bei-sonnenaufgang',
        12,
        12.99,
        'img/movieImg/sonnenuntergangs-boulevard-bei-sonnenaufgang-1750012609.jpeg',
        'Eine alternde Stummfilmdiva versucht, ihr Comeback zu feiern, aber ihr Butler hat sie versehentlich für einen Dreh am Sonnenaufgangs-Boulevard gebucht.',
        2023,
        98,
        'Drama-Komödie',
        'Billy Wilder (Frühschicht)'
    ),
    (
        65,
        'Dr. Seltsam oder: Wie ich lernte, die Bombe und meine Katze zu lieben',
        'dr-seltsam-oder-wie-ich-lernte-die-bombe-und-meine-katze-zu-lieben',
        12,
        13.99,
        'img/movieImg/dr-seltsam-oder-wie-ich-lernte-die-bombe-und-meine-katze-zu-lieben-1750009819.jpeg',
        'Eine politische Satire, in der ein paranoider General einen Atomkrieg auslösen will, während er gleichzeitig versucht, seine unartige Katze zu erziehen.',
        2024,
        102,
        'Satire-Komödie',
        'Stanley Kubrick (Katzenliebhaber)'
    ),
    (
        66,
        'Lawrence von Arabien in der Wüste ohne Wasser',
        'lawrence-von-arabien-in-der-wueste-ohne-wasser',
        12,
        14.99,
        'img/movieImg/lawrence-von-arabien-in-der-wueste-ohne-wasser-1750011793.jpeg',
        'T.E. Lawrence versucht, die arabischen Stämme zu vereinen, aber der Mangel an Wasser in der Wüste macht ihm das Leben schwerer als jeder Feind.',
        2025,
        185,
        'Abenteuer-Drama-Komödie',
        'David Lean (Durststrecke)'
    ),
    (
        67,
        'Die Reifeprüfung aus dem Kindergarten',
        'die-reifepruefung-aus-dem-kindergarten',
        0,
        7.99,
        'img/movieImg/die-reifepruefung-aus-dem-kindergarten-1750009272.jpeg',
        'Ein naiver Absolvent wird von einer älteren Frau verführt, die sich als seine Kindergärtnerin herausstellt, was zu einer Reihe von peinlichen Situationen führt.',
        2023,
        90,
        'Komödie',
        'Mike Nichols (Kindergarten-Edition)'
    ),
    (
        68,
        'Der Klang der Musik ist viel zu laut',
        'der-klang-der-musik-ist-viel-zu-laut',
        0,
        9.99,
        'img/movieImg/der-klang-der-musik-ist-viel-zu-laut-1749844743.jpeg',
        'Maria versucht, die singende Familie Trapp zu bändigen, aber ihre Lieder sind so laut, dass sie die Nachbarschaft in den Wahnsinn treiben.',
        2022,
        125,
        'Musical-Komödie',
        'Robert Wise (Lautstärke-Regler)'
    ),
    (
        69,
        'West Side Story auf der langweiligen Ostseite',
        'west-side-story-auf-der-langweiligen-ostseite',
        6,
        10.99,
        'img/movieImg/west-side-story-auf-der-langweiligen-ostseite-1750064664.jpg',
        'Ein Musical über zwei rivalisierende Jugendbanden in einem verschlafenen Vorort, deren Konflikte so banal sind, dass sie eher zum Gähnen als zum Tanzen anregen.',
        2024,
        115,
        'Musical-Parodie',
        'Jerome Robbins (Gähn-Edition)'
    ),
    (
        70,
        'Die Faust im Nacken in den Bergen mit Aussicht',
        'die-faust-im-nacken-in-den-bergen-mit-aussicht',
        16,
        12.99,
        'img/movieImg/die-faust-im-nacken-in-den-bergen-mit-aussicht-1750008666.jpeg',
        'Ein Hafenarbeiter kämpft gegen Korruption, aber der Film spielt diesmal in einer malerischen Berglandschaft, was zu schönen Kulissen, aber wenig Spannung führt.',
        2023,
        108,
        'Drama-Komödie',
        'Elia Kazan (Alpen-Edition)'
    ),
    (
        71,
        'Die Brücke am Kwai ist wegen Bauarbeiten geschlossen',
        'die-bruecke-am-kwai-ist-wegen-bauarbeiten-geschlossen',
        12,
        11.99,
        'img/movieImg/die-bruecke-am-kwai-ist-wegen-bauarbeiten-geschlossen-1750008654.jpeg',
        'Britische Kriegsgefangene müssen im Zweiten Weltkrieg eine Brücke bauen, aber das Projekt verzögert sich immer wieder aufgrund unvorhergesehener Bauarbeiten.',
        2025,
        140,
        'Kriegs-Komödie',
        'David Lean (Baustellen-Edition)'
    ),
    (
        72,
        'Ben-Hur und seine etwas schnellere Schwester',
        'ben-hur-und-seine-etwas-schnellere-schwester',
        6,
        13.99,
        'img/movieImg/ben-hur-und-seine-etwas-schnellere-schwester-1749843301.jpeg',
        'Ben-Hur kämpft im Wagenrennen um sein Leben, muss aber feststellen, dass seine jüngere Schwester heimlich trainiert hat und viel schneller ist als er.',
        2024,
        175,
        'Historisches Drama-Komödie',
        'William Wyler (Familienrennen)'
    ),
    (
        73,
        'Die Zehn Gebote und die Elf Vorschläge',
        'die-zehn-gebote-und-die-elf-vorschlaege',
        0,
        10.99,
        'img/movieImg/die-zehn-gebote-und-die-elf-vorschlaege-1750009754.jpeg',
        'Moses erhält die Zehn Gebote, aber seine Anhänger haben noch ein paar "Verbesserungsvorschläge", die zu lustigen Diskussionen führen.',
        2023,
        120,
        'Historische Komödie',
        'Cecil B. DeMille (Brainstorming)'
    ),
    (
        74,
        'Alles über Eva und ihren nervigen Freund Adam',
        'alles-ueber-eva-und-ihren-nervigen-freund-adam',
        6,
        9.99,
        'img/movieImg/alles-ueber-eva-und-ihren-nervigen-freund-adam-1749843002.jpeg',
        'Eine aufstrebende Schauspielerin versucht, in der Theaterwelt Fuß zu fassen, während ihr überfürsorglicher und ständig nörgelnder Freund ihr das Leben schwer macht.',
        2022,
        115,
        'Drama-Komödie',
        'Joseph L. Mankiewicz (Beziehungsdrama)'
    ),
    (
        75,
        'Von hier bis in die Ewigkeit und zurück zum Abendessen',
        'von-hier-bis-in-die-ewigkeit-und-zurueck-zum-abendessen',
        6,
        11.99,
        'img/movieImg/von-hier-bis-in-die-ewigkeit-und-zurueck-zum-abendessen-1750064177.jpg',
        'Soldaten auf Hawaii vor Pearl Harbor erleben Liebe und Leid, müssen aber sicherstellen, dass sie rechtzeitig zum Abendessen zurück sind.',
        2023,
        108,
        'Kriegs-Romantik-Komödie',
        'Fred Zinnemann (Mahlzeit-Edition)'
    ),
    (
        76,
        'African Queen und der etwas weniger beeindruckende asiatische König',
        'african-queen-und-der-etwas-weniger-beeindruckende-asiatische-koenig',
        6,
        12.99,
        'img/movieImg/african-queen-und-der-etwas-weniger-beeindruckende-asiatische-koenig-1749842908.jpeg',
        'Ein Kapitän und eine Missionarin versuchen, im Dschungel zu überleben, treffen aber auf einen rivalisierenden, weniger beeindruckenden asiatischen Monarchen.',
        2024,
        98,
        'Abenteuer-Komödie',
        'John Huston (Exotische Begegnung)'
    ),
    (
        77,
        'Der Malteser Falke und der Kanarienvogel, der lispelt',
        'der-malteser-falke-und-der-kanarienvogel-der-lispelt',
        12,
        13.99,
        'img/movieImg/der-malteser-falke-und-der-kanarienvogel-der-lispelt-1750007617.jpeg',
        'Sam Spade sucht nach einer kostbaren Statue und stößt dabei auf einen lispelnden Kanarienvogel, der unwissentlich wichtige Hinweise preisgibt.',
        2023,
        100,
        'Krimi-Komödie',
        'John Huston (Tierische Hinweise)'
    ),
    (
        78,
        'Der Schatz der Sierra Madre ist eine gefälschte Plastikmünze',
        'der-schatz-der-sierra-madre-ist-eine-gefaelschte-plastikmuenze',
        12,
        10.99,
        'img/movieImg/der-schatz-der-sierra-madre-ist-eine-gefaelschte-plastikmuenze-1750007907.jpeg',
        'Drei Goldsucher finden in Mexiko einen Schatz, der sich als wertlose Plastikmünze entpuppt, was zu einer Reihe von enttäuschenden und amüsanten Ereignissen führt.',
        2022,
        115,
        'Abenteuer-Komödie',
        'John Huston (Fälschungsalarm)'
    ),
    (
        79,
        'Die Früchte des Zorns sind eigentlich ziemlich sauer',
        'die-fruechte-des-zorns-sind-eigentlich-ziemlich-sauer',
        12,
        9.99,
        'img/movieImg/die-fruechte-des-zorns-sind-eigentlich-ziemlich-sauer-1750008728.jpeg',
        'Eine Familie kämpft während der Großen Depression ums Überleben, aber der eigentliche Kampf ist das Überleben ihrer eigenen, extrem sauren Ernte.',
        2024,
        120,
        'Drama-Komödie',
        'John Ford (Saure Trauben)'
    ),
    (
        80,
        'Mr. Smith geht nach Washington und verläuft sich hoffnungslos in der U-Bahn',
        'mr-smith-geht-nach-washington-und-verlaeuft-sich-hoffnungslos-in-der-u-bahn',
        0,
        8.99,
        'img/movieImg/mr-smith-geht-nach-washington-und-verlaeuft-sich-hoffnungslos-in-der-u-bahn-1750012192.jpeg',
        'Ein idealistischer Senator versucht, Korruption zu bekämpfen, aber seine größte Herausforderung ist es, sich im U-Bahn-System von Washington D.C. zurechtzufinden.',
        2023,
        110,
        'Drama-Komödie',
        'Frank Capra (U-Bahn-Chaos)'
    ),
    (
        81,
        'Es geschah in einer Nacht und am nächsten Tag hatten alle Kopfschmerzen',
        'es-geschah-in-einer-nacht-und-am-naechsten-tag-hatten-alle-kopfschmerzen',
        6,
        10.99,
        'img/movieImg/es-geschah-in-einer-nacht-und-am-naechsten-tag-hatten-alle-kopfschmerzen-1750010046.jpeg',
        'Ein verwöhntes Erbin und ein Reporter erleben eine verrückte Nacht voller Abenteuer, die am nächsten Morgen zu einem riesigen Kater führt.',
        2022,
        95,
        'Romantik-Komödie',
        'Frank Capra (Katerstimmung)'
    ),
    (
        82,
        'Die Nacht vor der Hochzeit aus Berliner Perspektive erzählt',
        'die-nacht-vor-der-hochzeit-aus-berliner-perspektive-erzaehlt',
        12,
        11.99,
        'img/movieImg/die-nacht-vor-der-hochzeit-aus-berliner-perspektive-erzaehlt-1750009196.jpeg',
        'Die Ereignisse vor einer Hochzeit, gesehen durch die Augen der exzentrischen und feierfreudigen Berliner Gäste.',
        2024,
        92,
        'Komödie',
        'George Cukor (Berliner Schnauze)'
    ),
    (
        83,
        'Haben und Nichthaben und keine Ahnung, was los ist',
        'haben-und-nichthaben-und-keine-ahnung-was-los-ist',
        12,
        12.99,
        'img/movieImg/haben-und-nichthaben-und-keine-ahnung-was-los-ist-1750010376.jpeg',
        'Ein Bootskapitän in der Karibik wird in ein gefährliches Spiel aus Spionage und Liebe verwickelt, versteht aber die Hälfte der Zeit nicht, was vor sich geht.',
        2023,
        98,
        'Abenteuer-Komödie',
        'Howard Hawks (Verwirrt in der Karibik)'
    ),
    (
        84,
        'Die besten Jahre unseres Lebens sind definitiv vorbei',
        'die-besten-jahre-unseres-lebens-sind-definitiv-vorbei',
        6,
        9.99,
        'img/movieImg/die-besten-jahre-unseres-lebens-sind-definitiv-vorbei-1750008642.jpeg',
        'Drei Soldaten kehren aus dem Krieg zurück und müssen sich an das zivile Leben gewöhnen, stellen aber fest, dass die besten Jahre wohl hinter ihnen liegen.',
        2025,
        150,
        'Drama-Komödie',
        'William Wyler (Midlife Crisis)'
    ),
    (
        85,
        'Das Apartment ist viel zu klein für diese Familie',
        'das-apartment-ist-viel-zu-klein-fuer-diese-familie',
        0,
        8.99,
        'img/movieImg/das-apartment-ist-viel-zu-klein-fuer-diese-familie-1749843731.jpeg',
        'Ein Angestellter vermietet seine Wohnung an seine Chefs für deren Affären, aber die Wohnung wird zu klein, als seine riesige Familie unerwartet einzieht.',
        2023,
        100,
        'Komödie',
        'Billy Wilder (Wohnungsnot)'
    ),
    (
        86,
        'Manche mögen\'s heiß, manche mögen\'s kalt und manche wollen es nur lauwarm',
        'manche-moegens-heiss-manche-moegens-kalt-und-manche-wollen-es-nur-lauwarm',
        6,
        11.99,
        'img/movieImg/manche-moegens-heiss-manche-moegens-kalt-und-manche-wollen-es-nur-lauwarm-1750011826.jpeg',
        'Zwei Musiker fliehen nach einem Gangstermord als Frauen verkleidet und treffen auf eine Band, deren Vorlieben von extrem heiß bis lauwarm reichen.',
        2022,
        118,
        'Komödie',
        'Billy Wilder (Temperatur-Edition)'
    ),
    (
        87,
        'Zwölf Uhr mittags ist viel zu früh für eine Schießerei',
        'zwoelf-uhr-mittags-ist-viel-zu-frueh-fuer-eine-schießerei',
        12,
        13.99,
        'img/movieImg/zwoelf-uhr-mittags-ist-viel-zu-frueh-fuer-eine-schiesserei-1750064691.jpg',
        'Ein Sheriff muss sich einer gefährlichen Bande stellen, aber er ist ein Morgenmuffel und findet, dass 12 Uhr mittags viel zu früh für so etwas ist.',
        2024,
        85,
        'Western-Komödie',
        'Fred Zinnemann (Spätaufsteher)'
    ),
    (
        88,
        'Mein großer Freund Shane, komm zurück, du hast deinen Hut vergessen!',
        'mein-grosser-freund-shane-komm-zurueck-du-hast-deinen-hut-vergessen',
        6,
        10.99,
        'img/movieImg/mein-grosser-freund-shane-komm-zurueck-du-hast-deinen-hut-vergessen-1750012165.jpeg',
        'Ein kleiner Junge verehrt einen Revolverhelden, der die Stadt verlässt, aber der Junge merkt, dass Shane seinen Lieblingshut vergessen hat und macht sich auf die Suche nach ihm.',
        2023,
        90,
        'Western-Komödie',
        'George Stevens (Hutjagd)'
    ),
    (
        89,
        'Der Schwarze Falke ist hoffnungslos verirrt und fragt nicht nach dem Weg',
        'der-schwarze-falke-ist-hoffnungslos-verirrt-und-fragt-nicht-nach-dem-weg',
        12,
        12.99,
        'img/movieImg/der-schwarze-falke-ist-hoffnungslos-verirrt-und-fragt-nicht-nach-dem-weg-1750015465.jpeg',
        'Ein Bürgerkriegsveteran sucht nach seiner entführten Nichte, verläuft sich aber ständig in der Prärie, weil er zu stolz ist, nach dem Weg zu fragen.',
        2025,
        119,
        'Western-Komödie',
        'John Ford (Navigationsprobleme)'
    ),
    (
        90,
        'Der Mann, der Liberty Valance erschoss und komplett daneben zielte',
        'der-mann-der-liberty-valance-erschoss-und-komplett-daneben-zielte',
        12,
        11.99,
        'img/movieImg/der-mann-der-liberty-valance-erschoss-und-komplett-daneben-zielte-1750007639.jpeg',
        'Die wahre Geschichte hinter dem Mythos des Mannes, der Liberty Valance erschoss, aber in Wahrheit war er so betrunken, dass er ihn gar nicht traf.',
        2024,
        108,
        'Western-Komödie',
        'John Ford (Schießausbildung)'
    ),
    (
        91,
        'Der Gute, der Böse, der Hässliche und der unglaublich Mittelmäßige',
        'der-gute-der-boese-der-haessliche-und-der-unglaublich-mittelmaessige',
        16,
        14.99,
        'img/movieImg/der-gute-der-boese-der-haessliche-und-der-unglaublich-mittelmaessige-1749844771.jpeg',
        'Drei Kopfgeldjäger suchen nach einem Schatz im Bürgerkrieg, aber ein vierter, unglaublich mittelmäßiger Schütze mischt sich ein und sorgt für Chaos.',
        2023,
        170,
        'Western-Komödie',
        'Sergio Leone (Casting-Panne)'
    ),
    (
        92,
        'Spiel mir das Lied vom Tod und dann war alles vorbei',
        'spiel-mir-das-lied-vom-tod-und-dann-war-alles-vorbei',
        16,
        15.99,
        'img/movieImg/spiel-mir-das-lied-vom-tod-und-dann-war-alles-vorbei-1750012755.jpeg',
        'Ein mysteriöser Fremder mit einer Mundharmonika rächt sich an Banditen, aber nach dem letzten Ton ist auch der Film abrupt vorbei.',
        2025,
        165,
        'Western-Komödie',
        'Sergio Leone (Kurzschluss)'
    ),
    (
        93,
        'Für eine Handvoll Dollar kriegt man nicht mal ein Sandwich',
        'fuer-eine-handvoll-dollar-kriegt-man-nicht-mal-ein-sandwich',
        12,
        10.99,
        'img/movieImg/fuer-eine-handvoll-dollar-kriegt-man-nicht-mal-ein-sandwich-1750010214.jpeg',
        'Ein namenloser Revolverheld gerät in einen Krieg zwischen zwei Banden, aber sein Hauptproblem ist, dass er mit einer Handvoll Dollar nicht genug für ein anständiges Essen bekommt.',
        2022,
        99,
        'Western-Komödie',
        'Sergio Leone (Hunger Games)'
    ),
    (
        94,
        'Für ein paar Dollar mehr gibt es immer noch keine anständige Mahlzeit',
        'fuer-ein-paar-dollar-mehr-gibt-es-immer-noch-keine-anstaendige-mahlzeit',
        12,
        11.99,
        'img/movieImg/fuer-ein-paar-dollar-mehr-gibt-es-immer-noch-keine-anstaendige-mahlzeit-1750010178.jpeg',
        'Die Fortsetzung, in der die Kopfgeldjäger noch mehr Geld verdienen, aber immer noch Schwierigkeiten haben, ein zufriedenstellendes Essen zu finden.',
        2023,
        105,
        'Western-Komödie',
        'Sergio Leone (Diät-Edition)'
    ),
    (
        95,
        'Kopf runter, du Trottel! – Nein, ich bin kein Trottel!',
        'kopf-runter-du-trottel-nein-ich-bin-kein-trottel',
        12,
        12.99,
        'img/movieImg/kopf-runter-du-trottel-nein-ich-bin-kein-trottel-1750011722.jpeg',
        'Zwei Männer werden in die mexikanische Revolution verwickelt, aber einer von ihnen weigert sich beharrlich, die Befehle des anderen zu befolgen.',
        2024,
        140,
        'Western-Komödie',
        'Sergio Leone (Sturköpfe)'
    ),
    (
        96,
        'Mein Name ist Nobody, aber Sie können mich kurz Bob nennen.',
        'mein-name-ist-nobody-aber-sie-koennen-mich-kurz-bob-nennen',
        6,
        9.99,
        'img/movieImg/mein-name-ist-nobody-aber-sie-koennen-mich-kurz-bob-nennen-1750012144.jpeg',
        'Ein junger Revolverheld versucht, einem alternden Outlaw zu Ruhm zu verhelfen, während er seine eigene Identität geheim hält und nur als "Bob" angesprochen werden möchte.',
        2023,
        117,
        'Western-Komödie',
        'Tonino Valerii (Nicknames)'
    ),
    (
        97,
        'The Fast & The Bobr',
        'the-fast-the-bobr',
        12,
        14.99,
        'img/movieImg/the-fast--the-bobr-686b723b4c546.png',
        'Dom "Der Damm" Boretto und seine biberstarke Crew dachten, sie hätten das schnelle Leben hinter sich gelassen. Doch als ein mysteriöser Hacker namens Cipher-Zahn sie aus dem Bau lockt, müssen sie ihre aufgemotzten Baumstamm-Flitzer wieder aufheulen lassen. Von den Kanälen Venedigs bis zu den Stromschnellen des Amazonas rasen sie in einem neuen Abenteuer, bei dem es nicht nur um Geschwindigkeit geht, sondern um Familie... und die besten Hölzer.',
        2002,
        122,
        'Action, Komödie',
        'Justin Lin-Biber'
    ),
    (
        98,
        'Mad Bobr: Fury Road',
        'mad-bobr-fury-road',
        16,
        12.99,
        'img/movieImg/mad-bobr-fury-road-686b7adc7c416.png',
        'In den ausgetrockneten Flussbetten der Post-Apokalypse, wo Wasser wertvoller ist als jedes Holz, wird der einsame Nager Max Biberdamski von den Schergen des tyrannischen Immortan Biberius gefangen genommen. Er wird in eine waghalsige Flucht verwickelt, als die elitäre Kaiserin Furiosa mit einem schwer bewachten War-Rig entkommt, um eine Gruppe von Zucht-Bibern in den legendären, grünen Ur-Damm zu führen. Eine gnadenlose Verfolgungsjagd über die staubige Fury Road beginnt.',
        NULL,
        NULL,
        '',
        ''
    );

-- Fügt Beispieldaten in die Series-Tabelle ein, inklusive 'slug' und mit 'Creator' statt 'Regisseur'.
INSERT INTO
    `Series` (
        `SeriesId`,
        `Title`,
        `slug`,
        `Episodes`,
        `Price`,
        `PosterPath`,
        `Beschreibung`,
        `Erscheinungsjahr`,
        `Endjahr`,
        `Staffeln`,
        `Genre`,
        `Creator`
    )
VALUES
    (
        1,
        'Büro für ungeklärte Phänomene und verlorene Tupperdosen',
        'buero-fuer-ungeklaerte-phaenomene-und-verlorene-tupperdosen',
        12,
        19.99,
        'img/seriesImg/buero-fuer-ungeklaerte-phaenomene-und-verlorene-tupperdosen-1750754591.png',
        'Zwei ungleiche Agenten untersuchen übernatürliche Vorkommnisse und das größte Mysterium der Menschheit: Wohin verschwinden die Tupperdosen-Deckel?',
        2022,
        NULL,
        3,
        'Sci-Fi-Komödie',
        'Dana Skully-Deckel'
    ),
    (
        2,
        'Die Chroniken von Nimmerland: Wo die Socken immer verschwinden',
        'die-chroniken-von-nimmerland-wo-die-socken-immer-verschwinden',
        6,
        14.99,
        'img/seriesImg/die-chroniken-von-nimmerland-wo-die-socken-immer-verschwinden-1750757271.png',
        'Eine Gruppe von Kindern entdeckt ein Portal in ihrer Waschmaschine, das sie in ein Land führt, in dem alle verlorenen Socken leben und Abenteuer erleben.',
        2021,
        NULL,
        2,
        'Fantasy-Abenteuer',
        'C.S. Lewis (Remix)'
    ),
    (
        3,
        'Game of Stühle: Ein Lied von Sitzgelegenheiten und Verrat',
        'game-of-stuehle-ein-lied-von-sitzgelegenheiten-und-verrat',
        16,
        24.99,
        'img/seriesImg/game-of-stuehle-ein-lied-von-sitzgelegenheiten-und-verrat-1750758777.png',
        'In einer Welt, in der die sieben Königreiche um den bequemsten Bürostuhl kämpfen, sind Intrigen, Verrat und Rückenschmerzen an der Tagesordnung.',
        2019,
        NULL,
        5,
        'Fantasy-Satire',
        'George R.R. Sessel'
    ),
    (
        4,
        'Breaking Bad: Walter White kocht jetzt nur noch Griesbrei',
        'breaking-bad-walter-white-kocht-jetzt-nur-noch-griesbrei',
        12,
        17.99,
        'img/seriesImg/breaking-bad-walter-white-kocht-jetzt-nur-noch-griesbrei-1750753177.png',
        'Nach seiner Karriere als Drogenbaron entdeckt Walter White seine Leidenschaft für die Herstellung von perfektem Griesbrei und kämpft um die Vorherrschaft auf dem lokalen Wochenmarkt.',
        2024,
        NULL,
        1,
        'Dokumentation',
        'Vince Gilligan (Dessert-Edition)'
    ),
    (
        5,
        'The Walking Dead: Die Jagd nach dem letzten Klopapier',
        'the-walking-dead-die-jagd-nach-dem-letzten-klopapier',
        18,
        22.99,
        'img/seriesImg/placeholder.png',
        'In einer von Zombies überrannten Welt ist nicht die Untoten die größte Bedrohung, sondern der Mangel an Klopapier. Eine Gruppe Überlebender riskiert alles für eine Rolle.',
        2020,
        2022,
        3,
        'Horror-Satire',
        'Frank Darabont (Pandemie-Spezial)'
    ),
    (
        6,
        'Stranger Things: Das unheimliche W-LAN-Passwort',
        'stranger-things-das-unheimliche-w-lan-passwort',
        12,
        19.99,
        'img/seriesImg/placeholder.png',
        'Eine Gruppe von Kindern muss ein mysteriöses, übernatürliches W-LAN-Passwort knacken, um ihren Freund aus einer digitalen Dimension zu retten.',
        2023,
        NULL,
        4,
        'Mystery-Sci-Fi',
        'Duffer Brothers (Digital)'
    ),
    (
        7,
        'Das Büro: Home-Office-Wahnsinn',
        'das-buero-home-office-wahnsinn',
        6,
        15.99,
        'img/seriesImg/das-buero-home-office-wahnsinn-1750757739.png',
        'Die Mitarbeiter von Dunder Mifflin müssen sich an die Herausforderungen des Home-Office gewöhnen, was zu noch absurderen Situationen per Videoanruf führt.',
        2021,
        NULL,
        2,
        'Sitcom',
        'Greg Daniels (Remote)'
    ),
    (
        8,
        'Die Sopranos singen jetzt im Kirchenchor',
        'die-sopranos-singen-jetzt-im-kirchenchor',
        16,
        21.99,
        'img/seriesImg/die-sopranos-singen-jetzt-im-kirchenchor-1750758694.png',
        'Tony Soprano versucht, seine kriminellen Aktivitäten zu verbergen, indem er dem örtlichen Kirchenchor beitritt, was zu unerwarteten Konflikten mit der Mafia und dem Pfarrer führt.',
        2024,
        NULL,
        1,
        'Krimi-Komödie',
        'David Chase (Halleluja)'
    ),
    (
        9,
        'Sherlock und das Rätsel des fehlenden Milchschaums',
        'sherlock-und-das-raetsel-des-fehlenden-milchschaums',
        6,
        18.99,
        'img/seriesImg/placeholder.png',
        'Der brillante Detektiv Sherlock Holmes wird mit seinem bisher kniffligsten Fall konfrontiert: Jemand stiehlt den Milchschaum von seinem Cappuccino.',
        2022,
        2023,
        2,
        'Krimi-Komödie',
        'Steven Moffat (Koffein-Krimi)'
    ),
    (
        10,
        'Friends: Die Wiedervereinigung in der Warteschlange beim Amt',
        'friends-die-wiedervereinigung-in-der-warteschlange-beim-amt',
        12,
        16.99,
        'img/seriesImg/friends-die-wiedervereinigung-in-der-warteschlange-beim-amt-1750762985.png',
        'Die sechs Freunde treffen sich Jahre später zufällig in einer endlosen Warteschlange beim Bürgeramt wieder und schwelgen in Erinnerungen, während sie versuchen, eine Nummer zu ziehen.',
        2023,
        NULL,
        1,
        'Sitcom',
        'Marta Kauffman (Bürokratie)'
    ),
    (
        11,
        'Lost: Sie waren die ganze Zeit im falschen Flugzeug',
        'lost-sie-waren-die-ganze-zeit-im-falschen-flugzeug',
        16,
        23.99,
        'img/seriesImg/lost-sie-waren-die-ganze-zeit-im-falschen-flugzeug-1750763218.png',
        'Die Überlebenden von Flug 815 stellen fest, dass sie versehentlich in das falsche Flugzeug gestiegen sind und eigentlich auf dem Weg zu einem All-inclusive-Urlaub auf Mallorca waren.',
        2025,
        NULL,
        1,
        'Mystery-Parodie',
        'J.J. Abrams (Urlaubs-Edition)'
    ),
    (
        12,
        'How I Met Your Mother: Ted erzählt die Geschichte jetzt in 5 Minuten',
        'how-i-met-your-mother-ted-erzaehlt-die-geschichte-jetzt-in-5-minuten',
        6,
        14.99,
        'img/seriesImg/how-i-met-your-mother-ted-erzaehlt-die-geschichte-jetzt-in-5-minuten-1750763528.png',
        'Eine neue Version der Serie, in der Ted die gesamte Geschichte, wie er die Mutter seiner Kinder kennengelernt hat, in einer einzigen, atemlosen fünfminütigen Episode zusammenfasst.',
        2023,
        NULL,
        1,
        'Sitcom-Kurzfassung',
        'Carter Bays (Effizienz)'
    ),
    (
        13,
        'Dexter: Der Serienmörder, der jetzt nur noch Unkraut jätet',
        'dexter-der-serienmoerder-der-jetzt-nur-noch-unkraut-jaetet',
        18,
        22.99,
        'img/seriesImg/dexter-der-serienmoerder-der-jetzt-nur-noch-unkraut-jaetet-1750756753.png',
        'Dexter Morgan hat seine mörderischen Triebe hinter sich gelassen und kanalisiert seine dunkle Seite nun in die akribische und rücksichtslose Beseitigung von Unkraut in seinem Garten.',
        2024,
        NULL,
        2,
        'Thriller-Gartenarbeit',
        'James Manos Jr. (Grüner Daumen)'
    ),
    (
        14,
        'Die Simpsons: Jetzt in 3D und mit noch mehr Problemen',
        'die-simpsons-jetzt-in-3d-und-mit-noch-mehr-problemen',
        6,
        17.99,
        'img/seriesImg/die-simpsons-jetzt-in-3d-und-mit-noch-mehr-problemen-1750758074.png',
        'Die berühmte Zeichentrickfamilie wird in eine 3D-Welt versetzt, was zu neuen, multidimensionalen Problemen und noch bizarreren Abenteuern führt.',
        2025,
        NULL,
        35,
        'Animation-3D',
        'Matt Groening (Z-Achse)'
    ),
    (
        15,
        'Akte X: Die Wahrheit ist irgendwo im Keller, hinter den alten Kartons',
        'akte-x-die-wahrheit-ist-irgendwo-im-keller-hinter-den-alten-kartons',
        12,
        20.00,
        'img/seriesImg/akte-x-die-wahrheit-ist-irgendwo-im-keller-hinter-den-alten-kartons-1750752507.png',
        'Mulder und Scully setzen ihre Suche nach der Wahrheit fort, nur um festzustellen, dass alle Beweise für außerirdisches Leben die ganze Zeit über im Keller des FBI-Gebäudes lagerten.',
        2023,
        NULL,
        3,
        'Sci-Fi-Krimi',
        'Chris Carter (Archiv)'
    ),
    (
        16,
        'Seinfeld: Eine Serie über wirklich alles',
        'seinfeld-eine-serie-ueber-wirklich-alles',
        6,
        16.99,
        'img/seriesImg/seinfeld-eine-serie-ueber-wirklich-alles-1750764127.png',
        'Jerry, George, Elaine und Kramer setzen ihre alltäglichen Beobachtungen fort, aber diesmal geht es nicht um nichts, sondern um buchstäblich alles, was ihnen in den Sinn kommt.',
        2024,
        NULL,
        10,
        'Sitcom',
        'Larry David (Maximalismus)'
    ),
    (
        17,
        'The Crown: Die königliche Familie eröffnet einen Dönerladen',
        'the-crown-die-koenigliche-familie-eroeffnet-einen-doenerladen',
        6,
        18.99,
        'img/seriesImg/placeholder.png',
        'Um die Staatskasse aufzubessern, beschließt die britische Königsfamilie, einen Dönerladen direkt vor dem Buckingham Palace zu eröffnen, was zu diplomatischen und kulinarischen Verwicklungen führt.',
        2023,
        NULL,
        2,
        'Historien-Satire',
        'Peter Morgan (Mit alles und scharf)'
    ),
    (
        18,
        'House of Cards: Frank Underwood wird zum Hausmeister',
        'house-of-cards-frank-underwood-wird-zum-hausmeister',
        16,
        21.99,
        'img/seriesImg/house-of-cards-frank-underwood-wird-zum-hausmeister-1750763360.png',
        'Nach seinem politischen Fall muss Frank Underwood als Hausmeister im Kapitol arbeiten und versucht, durch Intrigen und Manipulationen die Kontrolle über den Wischmopp zu erlangen.',
        2025,
        NULL,
        1,
        'Politsatire',
        'Beau Willimon (Saubere Arbeit)'
    ),
    (
        19,
        'Breaking Badgers: Die Dachs-Mafia von Berlin',
        'breaking-badgers-die-dachs-mafia-von-berlin',
        16,
        20.99,
        'img/seriesImg/breaking-badgers-die-dachs-mafia-von-berlin-1750753413.png',
        'Eine Gruppe von Dachsen baut ein unterirdisches Imperium auf, das den Müllhandel in Berlin kontrolliert. Ein rivalisierender Waschbär-Clan will ihnen das Geschäft streitig machen.',
        2022,
        NULL,
        3,
        'Tier-Krimi',
        'Vince Grimbart'
    ),
    (
        20,
        'The Witcher: Geralt auf der Suche nach einer Steckdose',
        'the-witcher-geralt-auf-der-suche-nach-einer-steckdose',
        16,
        22.99,
        'img/seriesImg/placeholder.png',
        'Der Hexer Geralt von Riva muss Monster jagen, aber seine größte Herausforderung ist es, in der mittelalterlichen Welt eine funktionierende Steckdose für sein Smartphone zu finden.',
        2023,
        NULL,
        4,
        'Fantasy-Komödie',
        'Andrzej Sapkowski (Digital)'
    ),
    (
        21,
        'Peaky Blinders: Die Gang mit den zu großen Mützen',
        'peaky-blinders-die-gang-mit-den-zu-grossen-muetzen',
        16,
        21.99,
        'img/seriesImg/peaky-blinders-die-gang-mit-den-zu-grossen-muetzen-1750763914.png',
        'Die Shelby-Familie terrorisiert Birmingham, aber ihr Markenzeichen, die Schiebermützen, sind so überdimensioniert, dass sie ständig über ihre Augen rutschen.',
        2024,
        NULL,
        2,
        'Gangster-Parodie',
        'Steven Knight (Hutgröße XXL)'
    ),
    (
        22,
        'Black Mirror: Die App, die Toast bewertet',
        'black-mirror-die-app-die-toast-bewertet',
        12,
        19.99,
        'img/seriesImg/black-mirror-die-app-die-toast-bewertet-1750752854.png',
        'In naher Zukunft bestimmt eine App, die den Bräunungsgrad von Toast bewertet, den sozialen Status der Menschen. Eine Frau, deren Toaster unzuverlässig ist, gerät ins Abseits.',
        2023,
        NULL,
        1,
        'Sci-Fi-Dystopie',
        'Charlie Brooker (Frühstücks-Terror)'
    ),
    (
        23,
        'Modern Family: Jetzt mit noch mehr peinlichen Verwandten',
        'modern-family-jetzt-mit-noch-mehr-peinlichen-verwandten',
        6,
        17.99,
        'img/seriesImg/placeholder.png',
        'Die Pritchett-Dunphy-Tucker-Familie wächst weiter, als plötzlich längst vergessene, extrem peinliche Verwandte auftauchen und für neues Chaos sorgen.',
        2024,
        NULL,
        12,
        'Sitcom',
        'Christopher Lloyd (Familientreffen)'
    ),
    (
        24,
        'The Office (US): Michael Scott gründet eine Rockband',
        'the-office-us-michael-scott-gruendet-eine-rockband',
        6,
        16.99,
        'img/seriesImg/placeholder.png',
        'Michael Scott verlässt Dunder Mifflin, um seinen wahren Traum zu verwirklichen: Er gründet eine Rockband namens "Threat Level Midnight" mit ehemaligen Bürokollegen.',
        2023,
        NULL,
        1,
        'Spin-Off-Komödie',
        'Ricky Gervais (US-Tour)'
    ),
    (
        25,
        'Arrested Development: Die Bluths bauen ein Baumhaus',
        'arrested-development-die-bluths-bauen-ein-baumhaus',
        12,
        18.99,
        'img/seriesImg/arrested-development-die-bluths-bauen-ein-baumhaus-1750752700.png',
        'Die dysfunktionale Familie Bluth versucht, durch den Bau eines übertrieben komplizierten Baumhauses wieder zueinander zu finden, was natürlich in einem finanziellen und familiären Desaster endet.',
        2025,
        NULL,
        6,
        'Sitcom',
        'Mitchell Hurwitz (Holzweg)'
    ),
    (
        26,
        'Parks and Recreation: Leslie Knope kandidiert für das Amt der Galaktischen Präsidentin',
        'parks-and-recreation-leslie-knope-kandidiert-fuer-das-amt-der-galaktischen-praesidentin',
        6,
        17.99,
        'img/seriesImg/parks-and-recreation-leslie-knope-kandidiert-fuer-das-amt-der-galaktischen-praesidentin-1750765190.png',
        'Nach ihrer Karriere in Pawnee beschließt Leslie Knope, dass ihre wahre Bestimmung darin liegt, für das Amt der Präsidentin der Vereinten Planeten zu kandidieren. Ron Swanson ist nicht beeindruckt.',
        2024,
        NULL,
        8,
        'Sci-Fi-Sitcom',
        'Greg Daniels (Weltall)'
    ),
    (
        27,
        'Brooklyn Nine-Nine: Das Geheimnis des gestohlenen Donuts',
        'brooklyn-nine-nine-das-geheimnis-des-gestohlenen-donuts',
        6,
        16.99,
        'img/seriesImg/brooklyn-nine-nine-das-geheimnis-des-gestohlenen-donuts-1750754248.png',
        'Das 99. Revier steht kopf, als Captain Holts Lieblings-Donut unter mysteriösen Umständen verschwindet. Jake Peralta übernimmt die Ermittlungen in diesem hochbrisanten Fall.',
        2023,
        NULL,
        9,
        'Krimi-Sitcom',
        'Dan Goor (Zuckerschock)'
    ),
    (
        28,
        'Fleabag und die sprechende Zimmerpflanze',
        'fleabag-und-die-sprechende-zimmerpflanze',
        16,
        20.99,
        'img/seriesImg/fleabag-und-die-sprechende-zimmerpflanze-1750763076.png',
        'Fleabag versucht, ihr Leben in den Griff zu bekommen, aber ihre einzige Vertraute ist eine zynische Zimmerpflanze, die ihre Gedanken kommentiert und die vierte Wand durchbricht.',
        2024,
        NULL,
        3,
        'Dramedy',
        'Phoebe Waller-Bridge (Botanik)'
    ),
    (
        29,
        'Mad Men: Don Draper verkauft jetzt Tupperware',
        'mad-men-don-draper-verkauft-jetzt-tupperware',
        12,
        19.99,
        'img/seriesImg/mad-men-don-draper-verkauft-jetzt-tupperware-1750763509.png',
        'Nach seinem Werbeerfolg in den 60ern findet sich Don Draper in den 70ern wieder und versucht, seine kreativen Fähigkeiten auf den Verkauf von Tupperware auf Hauspartys anzuwenden.',
        2025,
        NULL,
        1,
        'Retro-Drama',
        'Matthew Weiner (Plastik-Ära)'
    ),
    (
        30,
        'Chernobyl: Der Reaktor hatte nur Schluckauf',
        'chernobyl-der-reaktor-hatte-nur-schluckauf',
        16,
        22.99,
        'img/seriesImg/chernobyl-der-reaktor-hatte-nur-schluckauf-1750755301.png',
        'Eine alternative Geschichtsschreibung, in der die Katastrophe von Tschernobyl nur ein kleiner, technischer Schluckauf war, der von übereifrigen Bürokraten dramatisiert wurde.',
        2023,
        NULL,
        1,
        'Satire-Miniserie',
        'Craig Mazin (Alles halb so wild)'
    );

-- Fügt Beispieldaten in die User- und Kunde-Tabelle ein.
INSERT INTO
    `User` (
        `UserId`,
        `Username`,
        `EMail`,
        `Password`,
        `Rolle`,
        `Birthday`
    )
VALUES
    (
        1,
        'admin',
        'admin@filmverleih.net',
        '$2y$10$9.2cO/9a.iE.1fG/jF.dJ.Y6kNbqmhbH2m2d0aLJG12u2b8j9sL0G',
        'admin',
        '1990-05-23'
    ),
    (
        2,
        'peter.pan',
        'peter.pan@example.com',
        '$2y$10$9.2cO/9a.iE.1fG/jF.dJ.Y6kNbqmhbH2m2d0aLJG12u2b8j9sL0G',
        'user',
        '1992-08-15'
    ),
    (
        3,
        'sandra.service',
        'sandra.service@filmverleih.net',
        '$2y$10$9.2cO/9a.iE.1fG/jF.dJ.Y6kNbqmhbH2m2d0aLJG12u2b8j9sL0G',
        'kundendienst',
        '1995-02-20'
    ),
    (
        4,
        'chris.coadmin',
        'chris.coadmin@filmverleih.net',
        '$2y$10$9.2cO/9a.iE.1fG/jF.dJ.Y6kNbqmhbH2m2d0aLJG12u2b8j9sL0G',
        'co-admin',
        '1988-11-30'
    ),
    (
        5,
        'julia.kunde',
        'julia.kunde@example.com',
        '$2y$10$9.2cO/9a.iE.1fG/jF.dJ.Y6kNbqmhbH2m2d0aLJG12u2b8j9sL0G',
        'user',
        '1998-04-12'
    ),
    (
        6,
        'tom.tester',
        'tom.tester@example.com',
        '$2y$10$9.2cO/9a.iE.1fG/jF.dJ.Y6kNbqmhbH2m2d0aLJG12u2b8j9sL0G',
        'user',
        '1991-09-01'
    ),
    (
        7,
        'leonie.leiht',
        'leonie.leiht@example.com',
        '$2y$10$9.2cO/9a.iE.1fG/jF.dJ.Y6kNbqmhbH2m2d0aLJG12u2b8j9sL0G',
        'user',
        '2000-12-24'
    ),
    (
        8,
        'markus.meier',
        'markus.meier@example.com',
        '$2y$10$9.2cO/9a.iE.1fG/jF.dJ.Y6kNbqmhbH2m2d0aLJG12u2b8j9sL0G',
        'user',
        '1985-06-07'
    ),
    (
        9,
        'user.ohne.bestellung',
        'ohne@bestellung.de',
        '$2y$10$9.2cO/9a.iE.1fG/jF.dJ.Y6kNbqmhbH2m2d0aLJG12u2b8j9sL0G',
        'user',
        '1999-10-10'
    ),
    (
        10,
        'inaktiv.kunde',
        'inaktiv@mail.com',
        '$2y$10$9.2cO/9a.iE.1fG/jF.dJ.Y6kNbqmhbH2m2d0aLJG12u2b8j9sL0G',
        'user',
        '1976-03-18'
    ),
    (
        11,
        'support.helga',
        'helga.support@filmverleih.net',
        '$2y$10$9.2cO/9a.iE.1fG/jF.dJ.Y6kNbqmhbH2m2d0aLJG12u2b8j9sL0G',
        'kundendienst',
        '1993-01-25'
    ),
    (
        12,
        'anna.mustermann',
        'anna.mustermann@example.com',
        '$2y$10$9.2cO/9a.iE.1fG/jF.dJ.Y6kNbqmhbH2m2d0aLJG12u2b8j9sL0G',
        'user',
        '1987-07-22'
    ),
    (
        13,
        'ben.beispiel',
        'ben.beispiel@example.com',
        '$2y$10$9.2cO/9a.iE.1fG/jF.dJ.Y6kNbqmhbH2m2d0aLJG12u2b8j9sL0G',
        'user',
        '1990-01-05'
    ),
    (
        14,
        'clara.musterfrau',
        'clara.musterfrau@example.com',
        '$2y$10$9.2cO/9a.iE.1fG/jF.dJ.Y6kNbqmhbH2m2d0aLJG12u2b8j9sL0G',
        'user',
        '1983-09-10'
    ),
    (
        15,
        'david.dummy',
        'david.dummy@example.com',
        '$2y$10$9.2cO/9a.iE.1fG/jF.dJ.Y6kNbqmhbH2m2d0aLJG12u2b8j9sL0G',
        'user',
        '1994-11-28'
    ),
    (
        16,
        'eva.einfach',
        'eva.einfach@example.com',
        '$2y$10$9.2cO/9a.iE.1fG/jF.dJ.Y6kNbqmhbH2m2d0aLJG12u2b8j9sL0G',
        'user',
        '1980-03-03'
    );

INSERT INTO
    `Kunde` (
        `KundeId`,
        `Vorname`,
        `Nachname`,
        `Strasse`,
        `Hausnummer`,
        `Telefon`,
        `PLZ`,
        `Ort`,
        `Land`,
        `UserId`
    )
VALUES
    (
        1,
        'Admin',
        'Admin',
        'Hauptstr.',
        '1',
        '123456789',
        '10115',
        'Berlin',
        'Deutschland',
        1
    ),
    (
        2,
        'Peter',
        'Pan',
        'Nimmerland-Weg',
        '1',
        '01122334455',
        '12345',
        'Fantasieburg',
        'Deutschland',
        2
    ),
    (
        3,
        'Sandra',
        'Service',
        'Hilfestraße',
        '101',
        '02233445566',
        '20095',
        'Hamburg',
        'Deutschland',
        3
    ),
    (
        4,
        'Chris',
        'Co-Admin',
        'Verwaltungs-Gasse',
        '2B',
        '03344556677',
        '10115',
        'Berlin',
        'Deutschland',
        4
    ),
    (
        5,
        'Julia',
        'Kunde',
        'Beispiel-Allee',
        '33',
        NULL,
        '50667',
        'Köln',
        'Deutschland',
        5
    ),
    (
        6,
        'Tom',
        'Tester',
        'Testweg',
        '42a',
        '04455667788',
        '60311',
        'Frankfurt am Main',
        'Deutschland',
        6
    ),
    (
        7,
        'Leonie',
        'Leiht',
        'Bibliotheksplatz',
        '5',
        '05566778899',
        '80331',
        'München',
        'Deutschland',
        7
    ),
    (
        8,
        'Markus',
        'Meier',
        'Normalo-Ring',
        '17',
        NULL,
        '70173',
        'Stuttgart',
        'Deutschland',
        8
    ),
    (
        9,
        'Ina',
        'Interessiert',
        'Warteschleife',
        '99',
        '06677889900',
        '40213',
        'Düsseldorf',
        'Deutschland',
        9
    ),
    (
        10,
        'Paul',
        'Pause',
        'Ruhe-Stieg',
        '8',
        NULL,
        '04109',
        'Leipzig',
        'Deutschland',
        10
    ),
    (
        11,
        'Helga',
        'Hilfreich',
        'Support-Gasse',
        '12',
        '07788990011',
        '01067',
        'Dresden',
        'Deutschland',
        11
    ),
    (
        12,
        'Anna',
        'Mustermann',
        'Musterweg',
        '12',
        '08912345678',
        '80333',
        'München',
        'Deutschland',
        12
    ),
    (
        13,
        'Ben',
        'Beispiel',
        'Beispielpfad',
        '7b',
        '03098765432',
        '10117',
        'Berlin',
        'Deutschland',
        13
    ),
    (
        14,
        'Clara',
        'Musterfrau',
        'Alte Gasse',
        '55',
        '04023456789',
        '20097',
        'Hamburg',
        'Deutschland',
        14
    ),
    (
        15,
        'David',
        'Dummy',
        'Dummy-Str.',
        '21',
        '02218765432',
        '50676',
        'Köln',
        'Deutschland',
        15
    ),
    (
        16,
        'Eva',
        'Einfach',
        'Einfacher Weg',
        '1',
        '07111234567',
        '70174',
        'Stuttgart',
        'Deutschland',
        16
    );

-- Fügt Beispieldaten in die Ticket-Tabelle ein, jetzt mit ProduktId und ProduktTyp.
INSERT INTO
    `Ticket` (
        `TicketId`,
        `Zeitstempel`,
        `BeginnDatum`,
        `EndDatum`,
        `Zahlungsstatus`,
        `KundeId`,
        `ProduktId`,
        `ProduktTyp`
    )
VALUES
    (
        1,
        '2025-06-18 10:00:00',
        '2025-06-18 10:00:00',
        '2025-07-18 10:00:00',
        'Offen',
        2,
        1,
        'movie'
    ),
    (
        2,
        '2025-05-15 11:00:00',
        '2025-05-15 11:00:00',
        '2025-06-14 11:00:00',
        'Beglichen',
        2,
        5,
        'movie'
    ),
    (
        3,
        '2025-06-17 14:00:00',
        '2025-06-17 14:00:00',
        '2025-07-17 14:00:00',
        'Beglichen',
        3,
        2,
        'movie'
    ),
    (
        4,
        '2025-04-01 18:00:00',
        '2025-04-01 18:00:00',
        '2025-05-01 18:00:00',
        'Beglichen',
        3,
        7,
        'movie'
    ),
    (
        5,
        '2025-06-10 16:00:00',
        '2025-06-10 16:00:00',
        '2025-07-10 16:00:00',
        'Beglichen',
        5,
        8,
        'movie'
    ),
    (
        6,
        '2025-03-15 12:30:00',
        '2025-03-15 12:30:00',
        '2025-04-14 12:30:00',
        'Beglichen',
        5,
        9,
        'movie'
    ),
    (
        7,
        '2025-05-05 08:00:00',
        '2025-05-05 08:00:00',
        '2025-06-04 08:00:00',
        'Beglichen',
        6,
        10,
        'movie'
    ),
    (
        8,
        '2025-06-16 20:00:00',
        '2025-06-16 20:00:00',
        '2025-07-16 20:00:00',
        'Offen',
        7,
        4,
        'movie'
    ),
    (
        9,
        '2025-02-14 19:00:00',
        '2025-02-14 19:00:00',
        '2025-03-16 19:00:00',
        'Beglichen',
        7,
        6,
        'movie'
    ),
    (
        10,
        '2025-06-18 11:45:00',
        '2025-06-18 11:45:00',
        '2025-07-18 11:45:00',
        'Beglichen',
        8,
        1,
        'movie'
    ),
    (
        11,
        '2025-06-20 09:30:00',
        '2025-06-20 09:30:00',
        '2025-07-20 09:30:00',
        'Beglichen',
        2,
        1,
        'series'
    ),
    (
        12,
        '2025-05-25 14:00:00',
        '2025-05-25 14:00:00',
        '2025-06-24 14:00:00',
        'Beglichen',
        3,
        2,
        'series'
    ),
    (
        13,
        '2025-06-19 10:15:00',
        '2025-06-19 10:15:00',
        '2025-07-19 10:15:00',
        'Offen',
        5,
        3,
        'series'
    ),
    (
        14,
        '2025-04-10 16:00:00',
        '2025-04-10 16:00:00',
        '2025-05-10 16:00:00',
        'Beglichen',
        6,
        4,
        'series'
    ),
    (
        15,
        '2025-06-15 11:00:00',
        '2025-06-15 11:00:00',
        '2025-07-15 11:00:00',
        'Beglichen',
        7,
        5,
        'series'
    ),
    (
        16,
        '2025-06-22 10:00:00',
        '2025-06-22 10:00:00',
        '2025-07-22 10:00:00',
        'Beglichen',
        12,
        16,
        'movie'
    ),
    (
        17,
        '2025-06-23 11:00:00',
        '2025-06-23 11:00:00',
        '2025-07-23 11:00:00',
        'Offen',
        13,
        17,
        'movie'
    ),
    (
        18,
        '2025-06-24 12:00:00',
        '2025-06-24 12:00:00',
        '2025-07-24 12:00:00',
        'Beglichen',
        14,
        18,
        'movie'
    ),
    (
        19,
        '2025-06-25 13:00:00',
        '2025-06-25 13:00:00',
        '2025-07-25 13:00:00',
        'Beglichen',
        15,
        19,
        'movie'
    ),
    (
        20,
        '2025-06-26 14:00:00',
        '2025-06-26 14:00:00',
        '2025-07-26 14:00:00',
        'Offen',
        16,
        20,
        'movie'
    ),
    (
        21,
        '2025-06-27 15:00:00',
        '2025-06-27 15:00:00',
        '2025-07-27 15:00:00',
        'Beglichen',
        1,
        21,
        'movie'
    ),
    (
        22,
        '2025-06-28 16:00:00',
        '2025-06-28 16:00:00',
        '2025-07-28 16:00:00',
        'Beglichen',
        2,
        22,
        'movie'
    ),
    (
        23,
        '2025-06-29 17:00:00',
        '2025-06-29 17:00:00',
        '2025-07-29 17:00:00',
        'Offen',
        3,
        23,
        'movie'
    ),
    (
        24,
        '2025-06-30 18:00:00',
        '2025-06-30 18:00:00',
        '2025-07-30 18:00:00',
        'Beglichen',
        4,
        24,
        'movie'
    ),
    (
        25,
        '2025-07-01 19:00:00',
        '2025-07-01 19:00:00',
        '2025-07-31 19:00:00',
        'Beglichen',
        5,
        25,
        'movie'
    ),
    (
        26,
        '2025-07-02 20:00:00',
        '2025-07-02 20:00:00',
        '2025-08-01 20:00:00',
        'Offen',
        6,
        6,
        'series'
    ),
    (
        27,
        '2025-07-03 21:00:00',
        '2025-07-03 21:00:00',
        '2025-08-02 21:00:00',
        'Beglichen',
        7,
        7,
        'series'
    ),
    (
        28,
        '2025-07-04 22:00:00',
        '2025-07-04 22:00:00',
        '2025-08-03 22:00:00',
        'Beglichen',
        8,
        8,
        'series'
    ),
    (
        29,
        '2025-07-05 23:00:00',
        '2025-07-05 23:00:00',
        '2025-08-04 23:00:00',
        'Offen',
        12,
        9,
        'series'
    ),
    (
        30,
        '2025-07-06 09:00:00',
        '2025-07-06 09:00:00',
        '2025-08-05 09:00:00',
        'Beglichen',
        13,
        10,
        'series'
    );

-- INSERT INTO Ticket  
-- VALUES (31,'2025-06-18 17:00:00', '2025-06-18 17:00:00', '2025-06-25 17:00:00', 'Offen', 1, 10);
-- INSERT INTO Ticket 
-- VALUES (32,'2025-05-01 12:00:00', '2025-05-01 12:00:00', '2025-05-08 12:00:00', 'Beglichen', 1, 15);
-- Löscht vorhandene Beispieldaten, um Duplikate zu vermeiden
-- TRUNCATE TABLE `Episode`;
-- Fügt 5 Beispiel-Episoden für jede der 30 Serien hinzu
INSERT INTO
    `Episode` (
        `SeriesId`,
        `SeasonNumber`,
        `EpisodeNumber`,
        `Title`,
        `Description`,
        `Laufzeit`
    )
VALUES
    -- Serie 1: Büro für ungeklärte Phänomene...
    (
        1,
        1,
        1,
        'Der Fall des verschwundenen Deckels',
        'Agenten Mulder und Scully untersuchen das mysteriöse Verschwinden eines perfekt passenden Tupperdosen-Deckels.',
        45
    ),
    (
        1,
        1,
        2,
        'Die Socke aus der vierten Dimension',
        'Eine einzelne Socke taucht nach Wochen wieder auf, aber sie scheint... verändert. Und sie ist nicht allein.',
        47
    ),
    (
        1,
        1,
        3,
        'Das Ketchup-Flaschen-Komplott',
        'Warum kommt aus einer vollen Ketchup-Flasche zuerst nur wässrige Flüssigkeit? Eine Verschwörung wird aufgedeckt.',
        46
    ),
    (
        1,
        2,
        1,
        'Die Fernbedienung im Bermuda-Sofa',
        'Ein verzweifelter Familienvater verliert die Fernbedienung in den Tiefen des neuen Sofas. Ein Rettungsteam wird entsandt.',
        48
    ),
    (
        1,
        2,
        2,
        'Akte USB: Der Stick, der sich nicht drehen lässt',
        'Ein USB-Stick widersetzt sich den Gesetzen der Physik und passt erst beim dritten Versuch. Scully sucht nach einer rationalen Erklärung.',
        44
    ),
    -- Serie 2: Die Chroniken von Nimmerland...
    (
        2,
        1,
        1,
        'Der erste Socken-Sprung',
        'Vier Geschwister finden ein Portal in ihrer Waschmaschine und betreten eine Welt, die von einsamen Socken regiert wird.',
        24
    ),
    (
        2,
        1,
        2,
        'Der Rat der wollenen Ältesten',
        'Die Kinder müssen den Ältestenrat der Socken überzeugen, ihnen bei der Suche nach einem passenden Paar zu helfen.',
        22
    ),
    (
        2,
        1,
        3,
        'Die Armee der bunten Kindersocken',
        'Eine Armee von bunten Kindersocken plant eine Rebellion gegen die grauen Anzugsocken.',
        23
    ),
    (
        2,
        2,
        1,
        'Der Fussel-König',
        'Ein furchterregender Fussel-König entführt eine der Socken. Eine Rettungsmission beginnt.',
        25
    ),
    (
        2,
        2,
        2,
        'Die Schlacht am Wäscheständer',
        'Die finale Konfrontation zwischen den Socken und den dunklen Mächten des Weichspülers.',
        26
    ),
    -- Serie 3: Game of Stühle...
    (
        3,
        1,
        1,
        'Der Winter naht... und der Bürostuhl ist kalt',
        'Lord Stuhl von Winterfell beschwert sich über die mangelnde Sitzheizung und schmiedet eine Allianz.',
        55
    ),
    (
        3,
        1,
        2,
        'Die Schlacht am Kopierer',
        'Die Häuser Lehnhardt und Drehstuhl kämpfen um die Kontrolle über den einzigen funktionierenden Kopierer des Reiches.',
        58
    ),
    (
        3,
        1,
        3,
        'Eine Hochzeit und ein ergonomischer Todesfall',
        'Eine unerwartete Allianz wird durch eine Hochzeit besiegelt, doch der neue ergonomische Stuhl des Königs hat tödliche Mängel.',
        62
    ),
    (
        3,
        1,
        4,
        'Der Tanz mit den Rollen',
        'Eine rivalisierende Fraktion versucht, durch den Austausch der Stuhlrollen die Macht an sich zu reißen.',
        59
    ),
    (
        3,
        1,
        5,
        'Die lange Nacht der quietschenden Scharniere',
        'Ein Attentat auf den König wird durch das laute Quietschen eines alten Bürostuhls vereitelt.',
        61
    ),
    -- Serie 4: Breaking Bad: Walter White kocht...
    (
        4,
        1,
        1,
        'Pilot: Die perfekte Temperatur',
        'Walter entdeckt, dass die Herstellung von Griesbrei eine exakte Wissenschaft ist.',
        28
    ),
    (
        4,
        1,
        2,
        'Der Zimt-Konflikt',
        'Ein rivalisierender Koch versucht, Walters Zimt-Lieferung zu sabotieren.',
        30
    ),
    (
        4,
        1,
        3,
        'Das Rosinen-Kartell',
        'Walter muss sich mit dem mächtigen Rosinen-Kartell auseinandersetzen, um seine Griesbrei-Qualität zu sichern.',
        29
    ),
    (
        4,
        1,
        4,
        'Sag meinen Namen: "Grieskoch"',
        'Walter erlangt auf dem Wochenmarkt einen legendären Ruf.',
        31
    ),
    (
        4,
        1,
        5,
        'Ein Löffel voll Rache',
        'Nachdem sein Rezept gestohlen wurde, plant Walter einen süßen, aber klumpigen Rachefeldzug.',
        33
    ),
    -- Serie 5: The Walking Dead: Die Jagd nach dem letzten Klopapier
    (
        5,
        1,
        1,
        'Zwei Blätter für die Ewigkeit',
        'Die Gruppe findet eine einzelne, unversehrte Rolle Klopapier, was zu internen Spannungen führt.',
        44
    ),
    (
        5,
        1,
        2,
        'Die Flüsterer und das feuchte Tuch',
        'Eine feindliche Gruppe versucht, die Klopapier-Rolle gegen feuchte Tücher einzutauschen.',
        46
    ),
    (
        5,
        1,
        3,
        'Das Heiligtum der dreilagigen Blätter',
        'Gerüchte über ein unberührtes Lagerhaus voller Toilettenpapier führen die Gruppe auf eine gefährliche Mission.',
        45
    ),
    (
        5,
        1,
        4,
        'Negan und der letzte Wischer',
        'Negan taucht auf und fordert die halbe Rolle als Tribut.',
        48
    ),
    (
        5,
        2,
        1,
        'Die Zombie-Horde am Supermarkt',
        'Die Gruppe muss eine riesige Zombie-Horde ablenken, um an eine neue Lieferung zu gelangen.',
        47
    ),
    -- Serie 6: Stranger Things: Das unheimliche W-LAN-Passwort
    (
        6,
        1,
        1,
        'Das verschwundene Signal',
        'Wills W-LAN-Verbindung bricht plötzlich ab, und er ist verschwunden. Seine Freunde vermuten einen übernatürlichen Router.',
        51
    ),
    (
        6,
        1,
        2,
        'Der Repeater aus der anderen Dimension',
        'Ein mysteriöses Mädchen mit telekinetischen Fähigkeiten kennt möglicherweise das Passwort.',
        53
    ),
    (
        6,
        1,
        3,
        'Die Lichterkette als Hotspot',
        'Joyce versucht, über eine Lichterkette mit dem verlorenen W-LAN zu kommunizieren.',
        52
    ),
    (
        6,
        2,
        1,
        'Der Demogorgon im Serverraum',
        'Das Monster aus der anderen Dimension scheint von der Abwärme des Schul-Serverraums angezogen zu werden.',
        55
    ),
    (
        6,
        2,
        2,
        'Das Upside-Down-Netzwerk',
        'Die Kinder müssen sich in ein alternatives, dunkles Netzwerk einloggen, um Will zu retten.',
        56
    ),
    -- Serie 7: Das Büro: Home-Office-Wahnsinn
    (
        7,
        1,
        1,
        'Der stumme Michael',
        'Michael vergisst ständig, sein Mikrofon bei Videoanrufen einzuschalten.',
        22
    ),
    (
        7,
        1,
        2,
        'Dwight Schrute, ehrenamtlicher IT-Support',
        'Dwight versucht, die Computerprobleme seiner Kollegen per Fernzugriff zu lösen, was im Chaos endet.',
        23
    ),
    (
        7,
        1,
        3,
        'Die virtuelle Kaffeepause',
        'Das Team versucht, die soziale Interaktion durch gezwungene virtuelle Kaffeepausen aufrechtzuerhalten.',
        21
    ),
    (
        7,
        2,
        1,
        'Jim und der virtuelle Hintergrund',
        'Jim treibt Dwight in den Wahnsinn, indem er ständig seinen virtuellen Hintergrund ändert.',
        24
    ),
    (
        7,
        2,
        2,
        'Die Online-Dundies',
        'Michael veranstaltet eine Preisverleihung über Zoom, aber die Internetverbindung ist instabil.',
        25
    ),
    -- Serie 8: Die Sopranos singen jetzt im Kirchenchor
    (
        8,
        1,
        1,
        'Ave Maria und ein gebrochenes Bein',
        'Tony tritt dem Kirchenchor bei, um unauffällig zu bleiben, aber seine Methoden zur "Motivation" der anderen Sänger sind unorthodox.',
        49
    ),
    (
        8,
        1,
        2,
        'Halleluja mit einem Hauch von Einschüchterung',
        'Ein rivalisierender Tenor wird "überzeugt", seinen Soloplatz an Tony abzutreten.',
        51
    ),
    (
        8,
        1,
        3,
        'Die Beichte und das schmutzige Geld',
        'Tony hat Schwierigkeiten, dem Pfarrer seine Sünden zu beichten, ohne sein Geschäft zu verraten.',
        50
    ),
    (
        8,
        1,
        4,
        'Ein Requiem für einen Spitzel',
        'Ein Chormitglied entpuppt sich als FBI-Informant, was Tony vor eine moralische Zerreißprobe stellt.',
        53
    ),
    (
        8,
        1,
        5,
        'Stille Nacht, unheilige Nacht',
        'Das alljährliche Weihnachtskonzert wird durch einen unerwarteten Besuch von Tonys "Geschäftspartnern" unterbrochen.',
        54
    ),
    -- Serie 9: Sherlock und das Rätsel des fehlenden Milchschaums
    (
        9,
        1,
        1,
        'Eine Studie in Latte Macchiato',
        'Sherlock bemerkt eine Unregelmäßigkeit bei seinem Morgenkaffee und eröffnet seinen bisher persönlichsten Fall.',
        47
    ),
    (
        9,
        1,
        2,
        'Der Hund der Baristas',
        'Ein mysteriöser Hund scheint immer dann aufzutauchen, wenn der Milchschaum verschwindet. Ein Zufall? Sherlock glaubt nicht daran.',
        49
    ),
    (
        9,
        1,
        3,
        'Das Zeichen der Vier Kaffeebohnen',
        'Ein kryptisches Zeichen, hinterlassen am Tatort, führt Sherlock in die Welt der geheimen Kaffee-Syndikate.',
        48
    ),
    (
        9,
        2,
        1,
        'Ein Skandal in der Milchabteilung',
        'Sherlock verdächtigt eine hochrangige Person aus der Molkereiindustrie, hinter dem Diebstahl zu stecken.',
        50
    ),
    (
        9,
        2,
        2,
        'Sein letzter Schwur... auf Koffein',
        'Im finalen Duell stellt Sherlock seinen Erzfeind Moriarty, der eine Vorliebe für schwarzen Kaffee hat.',
        52
    ),
    -- Serie 10: Friends: Die Wiedervereinigung in der Warteschlange beim Amt
    (
        10,
        1,
        1,
        'Der mit der Wartenummer',
        'Joey, Chandler, Ross, Monica, Phoebe und Rachel treffen sich zufällig wieder, als sie alle versuchen, ihren Personalausweis zu erneuern.',
        25
    ),
    (
        10,
        1,
        2,
        'Der mit dem falschen Formular',
        'Ross hat das falsche Formular ausgefüllt, was zu einer hitzigen Debatte über Bürokratie führt.',
        24
    ),
    (
        10,
        1,
        3,
        'Die mit dem "Smelly Cat" im Wartezimmer',
        'Phoebe versucht, die Wartenden mit einer Akustik-Version von "Smelly Cat" aufzuheitern.',
        23
    ),
    (
        10,
        1,
        4,
        'Der mit dem gestohlenen Sandwich',
        'Jemand stiehlt Ross\' Sandwich aus der Tasche, was eine intensive Untersuchung innerhalb der Warteschlange auslöst.',
        26
    ),
    (
        10,
        1,
        5,
        'Der Letzte, der aufgerufen wird',
        'Die Freunde wetten, wer als Letztes aufgerufen wird. Der Verlierer muss alle zum Kaffee einladen.',
        27
    ),
    -- Serie 11: Lost: Sie waren die ganze Zeit im falschen Flugzeug
    (
        11,
        1,
        1,
        'Flug 815 nach Mallorca',
        'Die Überlebenden erkunden die Insel und finden statt eines Rauchmonsters einen Sangria-Stand.',
        48
    ),
    (
        11,
        1,
        2,
        'Der Andere ist der Animateur',
        'Die "Anderen" entpuppen sich als übermotiviertes Animationsteam eines All-inclusive-Clubs.',
        47
    ),
    (
        11,
        1,
        3,
        'Die Zahlen sind die Zimmernummern',
        'Die mysteriösen Zahlen sind die Zimmernummern für das Hotel, das sie gebucht hatten.',
        49
    ),
    (
        11,
        1,
        4,
        'Lebe zusammen, feiere allein',
        'Locke versucht, die Gruppe zu überzeugen, dass die Insel sie aus einem bestimmten Grund hierher gebracht hat: zur Happy Hour.',
        50
    ),
    (
        11,
        1,
        5,
        'Wir müssen zurück... zum Buffet',
        'Jack erkennt, dass sie die Insel verlassen müssen, bevor das All-you-can-eat-Buffet schließt.',
        52
    ),
    -- Serie 12: How I Met Your Mother: Ted erzählt die Geschichte jetzt in 5 Minuten
    (
        12,
        1,
        1,
        'Die Kurzfassung',
        'Ted fasst die ersten drei Staffeln in 60 Sekunden zusammen, sehr zur Freude seiner Kinder.',
        5
    ),
    (
        12,
        1,
        2,
        'Der Blaue-Horn-Schnelldurchlauf',
        'Die Geschichte des blauen Horns wird in einer schnellen Montage erzählt.',
        5
    ),
    (
        12,
        1,
        3,
        'Die mit dem gelben Regenschirm... sie war da!',
        'Ted überspringt alle unwichtigen Details und kommt direkt zum Punkt.',
        5
    ),
    (
        12,
        1,
        4,
        'Barney Stinsons legendäre 30 Sekunden',
        'Alle legendären Momente von Barney werden in einem schnellen Zusammenschnitt gezeigt.',
        5
    ),
    (
        12,
        1,
        5,
        'Und das, Kinder, war... ach, egal.',
        'Ted beendet die Geschichte abrupt, weil die Pizza geliefert wird.',
        5
    ),
    -- Serie 13: Dexter: Der Serienmörder, der jetzt nur noch Unkraut jätet
    (
        13,
        1,
        1,
        'Der dunkle Begleiter im Blumenbeet',
        'Dexter spürt eine unkontrollierbare Lust, Löwenzahn zu vernichten.',
        48
    ),
    (
        13,
        1,
        2,
        'Ein perfekter Rasenschnitt',
        'Dexter plant den perfekten Mord... an einem widerspenstigen Gänseblümchen.',
        47
    ),
    (
        13,
        1,
        3,
        'Der Giersch muss sterben',
        'Ein besonders hartnäckiges Unkraut treibt Dexter an den Rand des Wahnsinns.',
        49
    ),
    (
        13,
        2,
        1,
        'Die Komposthaufen-Leiche',
        'Dexter entsorgt die Überreste seiner Gartenarbeit auf einem geheimen Komposthaufen.',
        50
    ),
    (
        13,
        2,
        2,
        'Blutdünger',
        'Dexter entwickelt einen neuen, sehr effektiven Dünger, dessen Zutaten fragwürdig sind.',
        51
    ),
    -- Serie 14: Die Simpsons: Jetzt in 3D
    (
        14,
        1,
        1,
        'Die dritte Dimension des Donuts',
        'Homer versucht, einen 3D-gedruckten Donut zu essen, was zu unerwarteten Problemen führt.',
        22
    ),
    (
        14,
        1,
        2,
        'Lisa und die Z-Achse',
        'Lisa entdeckt die philosophischen Implikationen der dritten Dimension.',
        21
    ),
    (
        14,
        1,
        3,
        'Barts multidimensionaler Streich',
        'Bart nutzt die neue Dimension, um noch ausgefeiltere Streiche zu spielen.',
        23
    ),
    (
        14,
        1,
        4,
        'Marge gegen die Polygon-Kanten',
        'Marge hat Schwierigkeiten, das Haus sauber zu halten, da sich Staub in den Polygon-Kanten sammelt.',
        24
    ),
    (
        14,
        1,
        5,
        'Mr. Burns in Hochglanz-Render',
        'Mr. Burns genießt seine neue, hochauflösende Boshaftigkeit.',
        25
    ),
    -- Serie 15: Akte X: Die Wahrheit ist irgendwo im Keller
    (
        15,
        1,
        1,
        'Der Aktenschrank des Grauens',
        'Mulder findet einen alten Aktenschrank, der mit "X-Files" beschriftet ist, aber der Schlüssel fehlt.',
        46
    ),
    (
        15,
        1,
        2,
        'Der rauchende Hausmeister',
        'Der geheimnisvolle Raucher entpuppt sich als der Hausmeister, der nur eine ruhige Ecke zum Rauchen sucht.',
        47
    ),
    (
        15,
        1,
        3,
        'Die Verschwörung der vergessenen Notizen',
        'Scully versucht, Mulders unleserliche Notizen zu entziffern, die den Weg zur Wahrheit weisen könnten.',
        48
    ),
    (
        15,
        2,
        1,
        'Ich will den Keller glauben',
        'Mulder ist überzeugt, dass die Wahrheit hinter einer verschlossenen Kellertür liegt.',
        49
    ),
    (
        15,
        2,
        2,
        'Die Wahrheit ist da draußen... und sie braucht neue Glühbirnen',
        'Das Licht im Keller geht aus, was die Suche nach der Wahrheit erheblich erschwert.',
        50
    ),
    -- Serie 16: Seinfeld: Eine Serie über wirklich alles
    (
        16,
        1,
        1,
        'Das Dilemma mit der Quantenphysik',
        'Jerry philosophiert über die Beobachtung von subatomaren Teilchen, während er auf seinen Kaffee wartet.',
        22
    ),
    (
        16,
        1,
        2,
        'George und die unendliche Suppe',
        'George bestellt eine "Suppe des Tages", die sich als unendlich herausstellt.',
        23
    ),
    (
        16,
        1,
        3,
        'Elaine und das Konzept der Zeit',
        'Elaine versucht, einen Termin zu vereinbaren, verstrickt sich aber in eine Debatte über die Linearität der Zeit.',
        24
    ),
    (
        16,
        1,
        4,
        'Kramer gründet ein Universum',
        'Kramer versucht, in seiner Wohnung ein eigenes Universum zu erschaffen.',
        25
    ),
    (
        16,
        1,
        5,
        'Das Nichts und das Alles',
        'Die Freunde stellen fest, dass eine Serie über alles am Ende doch eine Serie über nichts ist.',
        26
    ),
    -- Serie 17: The Crown: Die königliche Familie eröffnet einen Dönerladen
    (
        17,
        1,
        1,
        'Mit alles und scharf, Eure Majestät?',
        'Die Queen lernt, wie man einen perfekten Döner zubereitet.',
        45
    ),
    (
        17,
        1,
        2,
        'Prinz Philip und die Knoblauchsoße',
        'Prinz Philip weigert sich, Knoblauchsoße zu verwenden, was zu einer diplomatischen Krise mit dem türkischen Botschafter führt.',
        47
    ),
    (
        17,
        1,
        3,
        'Der Thronfolger am Spieß',
        'Prinz Charles ist für den Dönerspieß verantwortlich, was seine Eignung für den Thron in Frage stellt.',
        46
    ),
    (
        17,
        2,
        1,
        'Eine königliche Lieferung',
        'Die Familie versucht, einen Lieferservice aufzubauen, aber die königlichen Kutschen sind zu langsam.',
        48
    ),
    (
        17,
        2,
        2,
        'Der Döner des Volkes',
        'Der Dönerladen wird zum Symbol der Volksnähe und steigert die Beliebtheit der Monarchie.',
        50
    ),
    -- Serie 18: House of Cards: Frank Underwood wird zum Hausmeister
    (
        18,
        1,
        1,
        'Ein neuer Wischmopp, eine neue Macht',
        'Frank beginnt seine neue Karriere und erkennt das Machtpotenzial eines sauberen Flurs.',
        55
    ),
    (
        18,
        1,
        2,
        'Das Komplott im Putzmittelschrank',
        'Frank schmiedet eine Intrige, um die Kontrolle über die besten Reinigungsmittel zu erlangen.',
        57
    ),
    (
        18,
        1,
        3,
        'Die vierte Wand... muss geputzt werden',
        'Frank durchbricht die vierte Wand, um dem Publikum seine Reinigungsstrategie zu erklären.',
        56
    ),
    (
        18,
        1,
        4,
        'Ein Kartenhaus aus Staub',
        'Franks Pläne drohen zu scheitern, als ein rivalisierender Hausmeister auftaucht.',
        58
    ),
    (
        18,
        1,
        5,
        'Der Präsident des sauberen Bodens',
        'Frank erreicht sein Ziel und wird zum obersten Hausmeister des Kapitols ernannt.',
        60
    ),
    -- Serie 19: Breaking Badgers: Die Dachs-Mafia von Berlin
    (
        19,
        1,
        1,
        'Das Revier',
        'Dachs-Pate Heisenbadger markiert sein Territorium im Tiergarten und legt sich mit den Waschbären an.',
        50
    ),
    (
        19,
        1,
        2,
        'Der Müll-Deal',
        'Ein lukrativer Deal über die Kontrolle der Abfallcontainer am Brandenburger Tor geht schief.',
        52
    ),
    (
        19,
        1,
        3,
        'Der Verrat im Bau',
        'Heisenbadger muss einen Verräter in den eigenen Reihen finden, der mit den Füchsen aus dem Grunewald zusammenarbeitet.',
        55
    ),
    (
        19,
        2,
        1,
        'Ein neuer Bau',
        'Die Dachs-Mafia expandiert und baut ein neues Tunnelsystem unter dem Reichstag.',
        53
    ),
    (
        19,
        2,
        2,
        'Die Rache der Waschbären',
        'Der rivalisierende Waschbär-Clan startet einen Gegenangriff.',
        54
    ),
    -- Serie 20: The Witcher: Geralt auf der Suche nach einer Steckdose
    (
        20,
        1,
        1,
        'Ein Hexer, ein Pferd und kein WLAN',
        'Geralt versucht, eine Karte auf seinem Smartphone zu laden, aber das mittelalterliche Netz ist schwach.',
        48
    ),
    (
        20,
        1,
        2,
        'Das Monster von Kabelsalat',
        'Ein furchterregendes Monster entpuppt sich als ein riesiger Knoten aus alten Ladekabeln.',
        47
    ),
    (
        20,
        1,
        3,
        'Der Fluch des leeren Akkus',
        'Geralts Smartphone-Akku ist leer, und er muss einen Weg finden, es aufzuladen, bevor die nächste Bestie angreift.',
        49
    ),
    (
        20,
        2,
        1,
        'Die Magie der Powerbank',
        'Ein Magier bietet Geralt eine verzauberte Powerbank an, aber sie hat ihren Preis.',
        50
    ),
    (
        20,
        2,
        2,
        'Das Schicksal und das Ladekabel',
        'Geralt erkennt, dass sein Schicksal untrennbar mit dem Finden des richtigen Ladekabels verbunden ist.',
        52
    ),
    -- Serie 21: Peaky Blinders: Die Gang mit den zu großen Mützen
    (
        21,
        1,
        1,
        'Ein Schatten über Birmingham',
        'Tommy Shelby plant einen Überfall, kann aber kaum etwas sehen, weil seine Mütze zu tief sitzt.',
        55
    ),
    (
        21,
        1,
        2,
        'Die Rennbahn und das eingeschränkte Sichtfeld',
        'Die Peaky Blinders versuchen, ein Pferderennen zu manipulieren, was durch ihre Mützen erschwert wird.',
        57
    ),
    (
        21,
        1,
        3,
        'Ein Treffen mit dem Feind... wenn man ihn denn findet',
        'Tommy trifft sich mit einem Rivalen, erkennt ihn aber erst, als er seine Mütze abnimmt.',
        56
    ),
    (
        21,
        2,
        1,
        'Der Hutmacher von Birmingham',
        'Die Gang sucht den Hutmacher auf, der für ihre überdimensionierten Mützen verantwortlich ist.',
        58
    ),
    (
        21,
        2,
        2,
        'Die Schlacht der Mützen',
        'Im finalen Kampf nutzen die Peaky Blinders ihre Mützen als Wurfgeschosse.',
        60
    ),
    -- Serie 22: Black Mirror: Die App, die Toast bewertet
    (
        22,
        1,
        1,
        'Das perfekte Goldbraun',
        'Eine Frau versucht verzweifelt, den perfekten Toast für eine hohe Bewertung zu machen.',
        49
    ),
    (
        22,
        1,
        2,
        'Die Verbrannten',
        'Eine Gruppe von Ausgestoßenen, deren Toaster defekt sind, lebt am Rande der Gesellschaft.',
        51
    ),
    (
        22,
        1,
        3,
        'Der Algorithmus des Frühstücks',
        'Ein Programmierer entdeckt einen Weg, den Toast-Bewertungsalgorithmus zu manipulieren.',
        50
    ),
    (
        22,
        1,
        4,
        'Upgrade auf den Toaster 2.0',
        'Die Frau investiert ihr gesamtes Erspartes in einen neuen, hochmodernen Toaster, um ihren sozialen Status zu verbessern.',
        53
    ),
    (
        22,
        1,
        5,
        'Die Rebellion der Krümel',
        'Die "Verbrannten" starten eine Rebellion, indem sie das System mit Krümeln lahmlegen.',
        54
    ),
    -- Serie 23: Modern Family: Jetzt mit noch mehr peinlichen Verwandten
    (
        23,
        1,
        1,
        'Onkel Rüdiger und sein Akkordeon',
        'Ein vergessener Onkel taucht auf und besteht darauf, bei jeder Gelegenheit Akkordeon zu spielen.',
        22
    ),
    (
        23,
        1,
        2,
        'Tante Gerda und ihre 17 Katzen',
        'Eine exzentrische Tante zieht vorübergehend bei den Dunphys ein, zusammen mit ihren 17 Katzen.',
        23
    ),
    (
        23,
        1,
        3,
        'Der Cousin, der alles besser weiß',
        'Ein neunmalkluger Cousin besucht die Familie und kritisiert jede ihrer Entscheidungen.',
        24
    ),
    (
        23,
        1,
        4,
        'Das Familientreffen des Schreckens',
        'Alle peinlichen Verwandten kommen zu einem großen Familientreffen zusammen, was im totalen Chaos endet.',
        25
    ),
    (
        23,
        1,
        5,
        'Wir sind doch alle ein bisschen seltsam',
        'Die Familie erkennt, dass ihre seltsamen Verwandten sie nur noch mehr zusammenschweißen.',
        26
    ),
    -- Serie 24: The Office (US): Michael Scott gründet eine Rockband
    (
        24,
        1,
        1,
        'Threat Level Midnight: The Musical',
        'Michael gründet eine Band und schreibt ein Musical über seine fiktive Spionagekarriere.',
        25
    ),
    (
        24,
        1,
        2,
        'Dwight am Schlagzeug',
        'Dwight erweist sich als überraschend guter, aber extrem lauter Schlagzeuger.',
        24
    ),
    (
        24,
        1,
        3,
        'Der erste Auftritt im Pub',
        'Die Band hat ihren ersten Auftritt in einem örtlichen Pub, aber das Publikum ist nicht beeindruckt.',
        23
    ),
    (
        24,
        1,
        4,
        'Der Band-Krieg',
        'Kreative Differenzen führen zu einem internen Machtkampf in der Band.',
        26
    ),
    (
        24,
        1,
        5,
        'Die Auflösung',
        'Michael löst die Band auf, um eine Solokarriere als Comedian zu starten.',
        27
    ),
    -- Serie 25: Arrested Development: Die Bluths bauen ein Baumhaus
    (
        25,
        1,
        1,
        'Ein Fundament aus Lügen',
        'George Michael versucht, ein Baumhaus zu bauen, aber seine Familie hat sehr unterschiedliche Vorstellungen davon.',
        28
    ),
    (
        25,
        1,
        2,
        'Die Genehmigung und der Zaubertrick',
        'Gob versucht, die Baugenehmigung mit einem missglückten Zaubertrick zu umgehen.',
        27
    ),
    (
        25,
        1,
        3,
        'Es gibt immer Geld in der Bananen-Hütte... oder im Baumhaus',
        'Michael entdeckt, dass sein Vater Geld in den Wänden des unfertigen Baumhauses versteckt hat.',
        29
    ),
    (
        25,
        1,
        4,
        'Der Hühnertanz auf dem Dach',
        'Lindsay weigert sich, das Baumhaus zu betreten, und führt stattdessen einen Protesttanz auf dem Dach auf.',
        30
    ),
    (
        25,
        1,
        5,
        'Der große Einsturz',
        'Das überladene und schlecht geplante Baumhaus stürzt am Ende der Episode spektakulär in sich zusammen.',
        31
    ),
    -- Serie 26: Parks and Recreation: Leslie Knope kandidiert für das Amt der Galaktischen Präsidentin
    (
        26,
        1,
        1,
        'Ein neuer Wahlkampf, eine neue Galaxie',
        'Leslie beschließt, dass Pawnee nicht genug ist, und kandidiert für das höchste Amt der Galaxie.',
        22
    ),
    (
        26,
        1,
        2,
        'Ron und die Aliens',
        'Ron Swanson muss sich mit außerirdischen Bürokraten auseinandersetzen, was er zutiefst verabscheut.',
        23
    ),
    (
        26,
        1,
        3,
        'Ein Wahlkampf-Plakat für den Mars',
        'Tom Haverford designt eine schrille Wahlkampagne für die Mars-Kolonie.',
        24
    ),
    (
        26,
        1,
        4,
        'Die Debatte auf dem Mond',
        'Leslie nimmt an einer hitzigen Debatte mit ihrem politischen Rivalen vom Planeten Zorp teil.',
        25
    ),
    (
        26,
        1,
        5,
        'Lil Sebastian im Weltraum',
        'Ein Klon von Lil Sebastian wird zum Maskottchen der intergalaktischen Kampagne.',
        26
    ),
    -- Serie 27: Brooklyn Nine-Nine: Das Geheimnis des gestohlenen Donuts
    (
        27,
        1,
        1,
        'Ein Verbrechen von zuckersüßer Bedeutung',
        'Captain Holt meldet den Diebstahl seines Lieblings-Donuts, und Jake übernimmt den Fall mit übertriebenem Ernst.',
        21
    ),
    (
        27,
        1,
        2,
        'Die Spur der Streusel',
        'Jake und Charles folgen einer Spur von bunten Streuseln, die sie durch das gesamte Revier führt.',
        22
    ),
    (
        27,
        1,
        3,
        'Das Verhör des Scully',
        'Scully und Hitchcock sind die Hauptverdächtigen, aber ihre Alibis sind wasserdicht (sie waren beim Mittagessen).',
        23
    ),
    (
        27,
        1,
        4,
        'Rosa und der geheime Zuckerguss',
        'Rosa Diaz hat eine geheime Leidenschaft für Backen und wird zur Hauptverdächtigen.',
        24
    ),
    (
        27,
        1,
        5,
        'Der wahre Täter ist... Cheddar!',
        'Am Ende stellt sich heraus, dass Captain Holts Hund Cheddar den Donut gestohlen hat.',
        25
    ),
    -- Serie 28: Fleabag und die sprechende Zimmerpflanze
    (
        28,
        1,
        1,
        'Ein neues Blatt',
        'Fleabag kauft eine Zimmerpflanze, die unerwartet beginnt, ihre innersten Gedanken zynisch zu kommentieren.',
        28
    ),
    (
        28,
        1,
        2,
        'Die Pflanze und der Priester',
        'Die Pflanze ist eifersüchtig auf den Priester und versucht, Fleabags aufkeimende Beziehung zu sabotieren.',
        27
    ),
    (
        28,
        1,
        3,
        'Gießen oder nicht gießen',
        'Fleabag vergisst, die Pflanze zu gießen, was zu einer passiv-aggressiven Auseinandersetzung führt.',
        29
    ),
    (
        28,
        1,
        4,
        'Photosynthese und andere Lügen',
        'Die Pflanze behauptet, sie könne durch Photosynthese die Zukunft vorhersagen.',
        30
    ),
    (
        28,
        1,
        5,
        'Umtopfen',
        'Fleabag beschließt, dass es Zeit für eine Veränderung ist, und topft die Pflanze in einen größeren Topf um, was einer Paartherapie gleicht.',
        31
    ),
    -- Serie 29: Mad Men: Don Draper verkauft jetzt Tupperware
    (
        29,
        1,
        1,
        'Der Pitch für die Frischhaltedose',
        'Don versucht, eine Gruppe von Hausfrauen mit einer existenziellen Präsentation von den Vorzügen der Tupperware zu überzeugen.',
        48
    ),
    (
        29,
        1,
        2,
        'Das Karussell der Salatschüsseln',
        'Don nutzt alte Familienfotos, um eine emotionale Verbindung zu Salatschüsseln herzustellen.',
        47
    ),
    (
        29,
        1,
        3,
        'Ein neuer Anfang... mit einem Deckel drauf',
        'Don hat eine Affäre mit einer Kundin, die von seiner Fähigkeit, Deckel perfekt zu verschließen, fasziniert ist.',
        49
    ),
    (
        29,
        1,
        4,
        'Rauch und Spiegel und Plastik',
        'Don kämpft mit seiner Identität, während er versucht, die Authentizität von Plastikbehältern zu verkaufen.',
        50
    ),
    (
        29,
        1,
        5,
        'Die Party ist vorbei',
        'Nach einer erfolgreichen Tupper-Party stellt Don fest, dass er sich genauso leer fühlt wie zuvor.',
        52
    ),
    -- Serie 30: Chernobyl: Der Reaktor hatte nur Schluckauf
    (
        30,
        1,
        1,
        '3.6 Röntgen? Nicht toll, aber auch nicht schrecklich.',
        'Die Ingenieure diskutieren, ob der kleine Zwischenfall wirklich so schlimm ist, oder ob man einfach weiterarbeiten kann.',
        58
    ),
    (
        30,
        1,
        2,
        'Die Graphit-Spitzen auf dem Dach sind nur Dekoration',
        'Ein Komitee beschließt, dass die seltsamen schwarzen Steine auf dem Dach eine künstlerische Entscheidung des Architekten waren.',
        59
    ),
    (
        30,
        1,
        3,
        'Der metallische Geschmack ist wahrscheinlich das neue Mundwasser',
        'Die Bewohner von Pripyat werden angewiesen, sich keine Sorgen über den seltsamen Geschmack in der Luft zu machen.',
        60
    ),
    (
        30,
        1,
        4,
        'Ein Anruf in Moskau',
        'Ein besorgter Wissenschaftler versucht, Moskau zu erreichen, landet aber in der Warteschleife.',
        62
    ),
    (
        30,
        1,
        5,
        'Alles unter Kontrolle',
        'Die Regierung gibt eine Pressekonferenz und versichert, dass der Reaktor nur einen kleinen Schluckauf hatte und alles unter Kontrolle ist.',
        61
    );
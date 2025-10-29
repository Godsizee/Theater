# **Filmverleih-Plattform **

## Einleitung für den Dozenten / Tester

Hallo! Willkommen zu diesem Webprojekt. Es wurde entwickelt, um einfach und schnell auf einem Standard-Entwicklungssystem wie **XAMPP** lauffähig zu sein. Du musst keine komplizierten Server-Einstellungen vornehmen oder Kommandozeilen-Tools installieren.

Die folgende Anleitung führt dich Schritt für Schritt durch die Einrichtung. Wenn du den Anweisungen folgst, solltest du die Plattform in wenigen Minuten einsatzbereit haben. Für die Datenbank-Verwaltung kannst du das mit XAMPP gelieferte **phpMyAdmin** oder ein externes Tool wie **DBeaver** verwenden – die Anleitung deckt beides ab.

Viel Spaß beim Testen!

---

## Kurzbeschreibung

Dieses Projekt ist eine moderne, hybride Webanwendung für einen fiktiven Film- und Serienverleih. Es kombiniert ein stabiles, objektorientiertes **PHP-Backend** für die Daten- und Geschäftslogik mit einem dynamischen **JavaScript-Frontend**, das für ein schnelles, App-ähnliches Nutzererlebnis (Single-Page-Application) sorgt.

Die Architektur setzt auf eine klare Trennung von Anliegen (Separation of Concerns) durch den Einsatz des **Repository- und Front-Controller-Patterns**. Das Frontend wird mit dem modernen Build-Tool **Vite** verwaltet und zu optimierten, performanten Dateien für den Live-Betrieb zusammengebaut.

## Features

Die Plattform ist in drei Hauptbereiche gegliedert:

* **Für Nutzer (Öffentlicher Bereich):**
    * Dynamisches Stöbern, Sortieren und Filtern von Filmen & Serien ohne Neuladen der Seite.
    * Moderne SPA-Navigation mit animierten Seitenübergängen.
    * Interaktiver Warenkorb und Checkout-Prozess.
    * Persönlicher Profilbereich zur Verwaltung von Daten, Bestellungen und Rechnungen.
    * Visuelle Effekte wie ein Dark Mode, "Film-Look"-Filter und dynamische Akzentfarben.
    * Herunterladen von Rechnungen als dynamisch generierte PDF-Dateien.

* **Für Administratoren (`/admin`):**
    * Vollständige CRUD-Verwaltung für Filme, Serien und Benutzer.
    * Dashboard zur Datenqualität, das auf Einträge mit fehlenden Informationen hinweist.
    * Detailliertes und filterbares Aktivitätsprotokoll (Audit Log) aller Admin-Aktionen.
    * Zentrale Verwaltung von globalen Seiteneinstellungen.

* **Für den Kundendienst (`/support`):**
    * Eigene, eingeschränkte Oberfläche für Support-Aufgaben.
    * Effiziente Kundensuche (nach Name, E-Mail, ID).
    * Einsicht in Kundendetails und deren Bestellhistorie.
    * Möglichkeit, offene Bestellungen im Namen des Kunden zu stornieren.

## Technologie-Stack

* **Backend:** PHP 8.x, PDO für Datenbankzugriffe
* **Frontend:** Vanilla JavaScript (ES6 Module), HTML5, CSS3
* **Datenbank:** MySQL / MariaDB
* **Webserver:** Apache (über XAMPP)
* **Build-Tool:** Vite.js
* **PHP-Bibliotheken:** tFPDF (manuell eingebunden)

## Ordnerstruktur

* `/app`: Das Herz der Backend-Logik (Core-Klassen, Controller, Repositories, Services).
* `/api`: Die Schnittstelle, die JSON-Daten für das Frontend bereitstellt.
* `/public`: Der Web-Root mit allen öffentlichen Assets (`assets`, `dist`) und der `index.php`.
* `/config`: Zentrale Konfigurationsdateien für Datenbank, Routen und Seiteneinstellungen.
* `/pages`: Die "View"-Dateien, die das HTML-Markup enthalten.
* `/templates`: Wiederverwendbare HTML-Bausteine (Header, Footer, Partials).

---

## 🚀 Installation & Inbetriebnahme (Für Bewerter & Dozenten)

### Schritt 1: Voraussetzungen

Alles, was du benötigst, ist eine laufende **XAMPP**-Installation.
* [Download XAMPP](https://www.apachefriends.org/de/index.html)

**Node.js oder npm werden nicht benötigt**, da alle Frontend-Dateien bereits fertig kompiliert im Projekt enthalten sind.

### Schritt 2: Projektdateien einrichten

1.  Kopiere den gesamten Projektordner in das `htdocs`-Verzeichnis deiner XAMPP-Installation.
    * Der Pfad sollte so aussehen: `C:\xampp\htdocs\Theater\` (oder ein Name deiner Wahl).

### Schritt 3: Datenbank erstellen & importieren

1.  Starte den **Apache**- und **MySQL**-Dienst über das XAMPP Control Panel.
2.  Öffne dein bevorzugtes Datenbank-Tool.

#### **Option A: Mit phpMyAdmin**
1.  Öffne im Browser die Adresse `http://localhost/phpmyadmin/`.
2.  Klicke links auf **Neu**, gib als Datenbanknamen `filmverleih` ein und klicke auf **Anlegen**.
3.  Wähle die neue Datenbank `filmverleih` in der linken Seitenleiste aus.
4.  Klicke oben auf den Reiter **"Importieren"**.
5.  Klicke auf "Datei auswählen" und wähle die `filmverleih.sql`-Datei aus dem Projektverzeichnis.
6.  Scrolle nach unten und klicke auf **"Importieren"**.

#### **Option B: Mit DBeaver**
1.  Erstelle eine neue Verbindung zu deiner lokalen MySQL-Instanz (Host: `localhost`, Port: `3306`, Benutzer: `root`, Passwort: *leer lassen*).
2.  Rechtsklicke im Datenbank-Navigator auf deine Verbindung und wähle **"SQL-Editor" -> "SQL-Skript ausführen"**.
3.  Wähle die `filmverleih.sql`-Datei aus dem Projektverzeichnis und starte den Import.

### Schritt 4: Konfiguration anpassen

1.  Öffne die Datei `config/database_access.php`.
2.  Stelle sicher, dass der `base_url` korrekt auf deinen Projektordner verweist (z.B. `/Theater`). Die Standard-Datenbankeinstellungen (`root` ohne Passwort) sollten für XAMPP bereits passen.

### Schritt 5: Anwendung starten

**Das Projekt ist nun vollständig eingerichtet!** Du kannst es im Browser unter der folgenden URL aufrufen:

`http://localhost/Theater/`

---

## 👨‍💻 Für Entwickler (Optional)

Wenn du am Frontend (JavaScript/CSS) arbeiten möchtest, benötigst du zusätzlich **Node.js** und **npm**.

### Frontend-Abhängigkeiten installieren

1.  Öffne ein Terminal (Eingabeaufforderung, PowerShell, etc.).
2.  Navigiere in das Hauptverzeichnis deines Projekts:
    ```bash
    cd C:\xampp\htdocs\Theater
    ```
3.  Führe den folgenden Befehl aus, um Vite zu installieren:
    ```bash
    npm install
    ```

### Entwicklungs-Workflow

1.  **Starte XAMPP** (Apache & MySQL).
2.  **Starte den Vite-Entwicklungsserver:** Öffne ein Terminal im Projektverzeichnis und führe aus:
    ```bash
    npm run dev
    ```
3.  Greife auf die Seite ganz normal über XAMPP zu (`http://localhost/Theater/`). Vite erkennt Änderungen an den Quelldateien in `/public/assets/` und aktualisiert den Browser automatisch (Hot Module Replacement).

### Produktions-Build

Um die Frontend-Dateien zu bündeln und zu optimieren, führe folgenden Befehl aus:
```bash
npm run build
    ```

Dieser Befehl bündelt und komprimiert alle Frontend-Assets in den `/public/dist`-Ordner. Die Anwendung nutzt diese optimierten Dateien automatisch.
# --- Konfiguration ---
# Setzt den Quellordner auf das Verzeichnis, in dem dieses Skript liegt
$SourceDirectory = Split-Path -Parent $MyInvocation.MyCommand.Definition
# $OutputDirectory wird NICHT mehr benötigt, da die Originaldateien überschrieben werden
$ExcludeDirectoryPattern = "tfpdf" 
$FileTypesToProcess = "*.php", "*.html", "*.css", "*.js"

# --- Vorbereitung ---
Write-Host "Starte verbesserte Bereinigung von Kommentaren und überschreibe Originaldateien..." -ForegroundColor Green

# --- WICHTIGER HINWEIS ---
Write-Host "`n"
Write-Host "¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦" -ForegroundColor Red
Write-Host "¦¦¦ ACHTUNG: Das Überschreiben von Originaldateien ist ein irreversibler Vorgang! ¦¦¦" -ForegroundColor Red
Write-Host "¦¦¦ Es werden KEINE Sicherungskopien erstellt. Stellen Sie sicher, dass Sie ¦¦¦" -ForegroundColor Red
Write-Host "¦¦¦ vor der Fortsetzung eine Sicherung Ihrer Dateien erstellt haben.         ¦¦¦" -ForegroundColor Red
Write-Host "¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦¦" -ForegroundColor Red
Write-Host "`n"
Read-Host "Drücken Sie die Enter-Taste, um zu bestätigen, dass Sie diesen Hinweis verstanden haben und fortfahren möchten..." | Out-Null

# --- Hauptlogik ---
Get-ChildItem -Path $SourceDirectory -Recurse -Include $FileTypesToProcess -File | ForEach-Object {
    $file = $_
    $filePath = $file.FullName

    # Überspringe ausgeschlossene Verzeichnisse
    If ($filePath -like "*\$ExcludeDirectoryPattern\*") {
        Write-Host "Überspringe (ausgeschlossener Pfad): $($filePath)" -ForegroundColor DarkYellow
        Return
    }

    Write-Host "Verarbeite und überschreibe: $($filePath)"

    # Schritt 1: Gesamten Dateiinhalt einlesen, um Block-Kommentare (/* ... */) zu entfernen
    $content = Get-Content -Path $filePath -Raw -Encoding UTF8
    $cleanedContent = $content -replace '(?s)/\*.*?\*/', ''

    # Schritt 2: Zeilen, die NUR aus einem "// "-Kommentar bestehen, entfernen
    $lines = $cleanedContent -split '(\r\n|\n|\r)'
    $finalLines = foreach ($line in $lines) {
        if ($line -notmatch "^\s*//\s") {
            $line
        }
    }

    # Die komplett bereinigten Zeilen in die Zieldatei schreiben
    Set-Content -Path $filePath -Value $finalLines -Encoding UTF8
}

Write-Host "`nFertig! Alle bereinigten Dateien wurden direkt überschrieben." -ForegroundColor Green
Write-Host "Es wurden KEINE Sicherungskopien erstellt." -ForegroundColor Red
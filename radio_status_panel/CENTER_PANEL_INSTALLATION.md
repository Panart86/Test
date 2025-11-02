# Center Panel Installation - Radio Status Panel

Das Radio Status Panel bietet zwei Anzeigemodi:

## 1. Side Panel (Seitenleiste)
- Datei: `radio_status_panel.php`
- Für Side-Panels (Links/Rechts)
- Kompakte Ansicht

## 2. Center Panel (Mittelpanel)
- Datei: `radio_status_center.php`
- Für das Hauptinhaltspanel (Mitte der Seite)
- Große, detaillierte Ansicht mit schönem Design
- AJAX Live-Updates ohne Seiten-Reload

## Center Panel Installation

### Methode 1: Als benutzerdefinierte Seite

1. **Erstellen Sie eine neue Custom Page**:
   - Gehen Sie zu **Content Admin** → **Custom Pages**
   - Klicken Sie auf **Add Page**

2. **Konfigurieren Sie die Seite**:
   - **Page Title**: Radio Status
   - **Page URL**: radio-status
   - **Page Content**: Lassen Sie leer oder fügen Sie eine Beschreibung hinzu
   - **PHP Include**: `/infusions/radio_status_panel/radio_status_center.php`
   - Oder verwenden Sie:
   ```php
   <?php
   require_once INFUSIONS."radio_status_panel/radio_status_center.php";
   ?>
   ```

3. **Speichern und aktivieren**

4. **Seite aufrufen**:
   - Die Seite ist nun unter `https://ihre-domain.de/radio-status.php` erreichbar

### Methode 2: Als Startseite einbinden

1. **Bearbeiten Sie Ihre theme.php oder eine Startseite**:
   ```php
   <?php
   require_once INFUSIONS."radio_status_panel/radio_status_center.php";
   ?>
   ```

2. **Oder in einem benutzerdefinierten PHP-Block**

### Methode 3: In der Homepage integrieren

1. **Bearbeiten Sie die Datei `home.php` in Ihrem Theme-Ordner**

2. **Fügen Sie hinzu**:
   ```php
   <?php
   if (file_exists(INFUSIONS."radio_status_panel/radio_status_center.php")) {
       require_once INFUSIONS."radio_status_panel/radio_status_center.php";
   }
   ?>
   ```

3. **Speichern**

## Features des Center Panels

### Visuelles Design
- ✅ Große, ansprechende Karten für jeden Stream
- ✅ Gradient-Header mit Stream-Namen
- ✅ Live-Status mit animiertem Badge (Online/Offline)
- ✅ Pulsierendes Radio-Icon
- ✅ Responsive Design für alle Bildschirmgrößen

### Metadaten-Anzeige
- ✅ **Aktueller Song**: Groß und prominent mit animiertem Musik-Icon
- ✅ **Höreranzahl**: Mit visuellem Balken (Füllstand)
- ✅ **Max. Hörer**: Maximale Kapazität
- ✅ **Peak Hörer**: Höchste Hörerzahl
- ✅ **Bitrate**: Stream-Qualität in kbps
- ✅ **Server-Info**: Server und Port

### Animationen
- ✅ Rotierendes Musik-Icon beim aktuellen Song
- ✅ Pulsierender Online/Offline-Status
- ✅ Hover-Effekte auf Statistik-Karten
- ✅ Sanfte Übergänge und Transformationen

### Live-Updates
- ✅ AJAX-basierte Aktualisierung
- ✅ Kein Seiten-Reload erforderlich
- ✅ Konfigurierbares Intervall (über Admin-Panel)
- ✅ Automatische Daten-Synchronisation

### Responsives Layout
- ✅ Optimiert für Desktop, Tablet und Mobile
- ✅ Flexible Grid-Ansicht
- ✅ Automatische Anpassung der Kartengröße

## Konfiguration

Alle Einstellungen werden über das Admin-Panel verwaltet:
- **System Admin** → **Radio Status Panel** → **Einstellungen**

Konfigurierbare Optionen:
- Aktualisierungsintervall
- Anzeige von Höreranzahl
- Anzeige von Metadaten
- Anzeige von Bitrate
- Anzeige von Genre

## Unterschiede: Side Panel vs Center Panel

| Feature | Side Panel | Center Panel |
|---------|-----------|--------------|
| Design | Kompakt | Groß & detailliert |
| Position | Seitenleiste | Hauptinhalt |
| Animationen | Basis | Erweitert |
| Live-Updates | Page Reload | AJAX |
| Statistiken | Einfach | Detailliert mit Visualisierung |
| Responsive | Ja | Ja, optimiert |
| Song-Anzeige | Klein | Groß & prominent |
| Hörer-Visualisierung | Text | Balken + Text |

## Beispiel-URLs

Nach der Installation können Sie das Center Panel über verschiedene Wege aufrufen:

1. **Custom Page**: `https://ihre-domain.de/radio-status.php`
2. **Direkt (wenn erlaubt)**: `https://ihre-domain.de/infusions/radio_status_panel/radio_status_center.php`
3. **Als Startseite**: `https://ihre-domain.de/` (wenn als Home eingebunden)

## Tipps

1. **Performance**:
   - Verwenden Sie ein Aktualisierungsintervall von mindestens 10 Sekunden
   - Bei vielen gleichzeitigen Besuchern erhöhen Sie das Intervall

2. **Design-Anpassungen**:
   - CSS kann direkt in `radio_status_center.php` angepasst werden
   - Farben der Gradients können im Style-Block geändert werden

3. **Multiple Streams**:
   - Das Center Panel zeigt alle aktiven Streams als einzelne Karten an
   - Perfekt für Multi-Stream-Setups

4. **Mobile Optimierung**:
   - Das Panel passt sich automatisch an mobile Geräte an
   - Karten werden gestapelt für bessere Lesbarkeit

## Fehlersuche

### Panel wird nicht angezeigt
- Überprüfen Sie den Pfad zur Datei
- Stellen Sie sicher, dass die Infusion installiert ist
- Prüfen Sie die PHP-Fehlerprotokolle

### AJAX-Updates funktionieren nicht
- Überprüfen Sie die Browser-Konsole auf JavaScript-Fehler
- Stellen Sie sicher, dass `ajax_refresh.php` erreichbar ist
- Testen Sie: `https://ihre-domain.de/infusions/radio_status_panel/ajax_refresh.php`

### Stream zeigt "Offline" obwohl er läuft
- Überprüfen Sie Server und Port im Admin-Panel
- Testen Sie die Erreichbarkeit: `http://server:port/7.html`
- Prüfen Sie Firewall-Einstellungen

## Support

Bei Fragen oder Problemen:
1. Überprüfen Sie die Hauptdokumentation in `README.md`
2. Aktivieren Sie den Debug-Modus in PHP-Fusion
3. Überprüfen Sie die PHP-Fehlerprotokolle

## Screenshots

Das Center Panel bietet:
- 📊 Große Dashboard-Ansicht
- 🎵 Prominent angezeigter aktueller Song
- 👥 Visuelle Hörer-Statistiken
- 📡 Alle Stream-Metadaten auf einen Blick
- 🎨 Modernes, ansprechendes Design

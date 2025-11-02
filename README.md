# Radio Status Panel für PHP-Fusion v9

Ein professionelles Radio-Status-Panel für PHP-Fusion v9, das Stream-Daten von Shoutcast v1 Servern abruft und auf der Homepage anzeigt.

## Features

- **Shoutcast v1 Unterstützung**: Vollständige Integration mit Shoutcast v1 Servern
- **Live-Stream-Status**: Zeigt den aktuellen Status des Radio-Streams an (Online/Offline)
- **Metadaten-Anzeige**:
  - Aktueller Song/Track
  - Anzahl der Hörer
  - Maximale Hörerzahl
  - Bitrate
  - Genre (falls verfügbar)
- **Admin-Panel**: Vollständiges Verwaltungsinterface für:
  - Hinzufügen/Bearbeiten/Löschen von Streams
  - Konfigurierbare Anzeigeoptionen
  - Anpassbares Aktualisierungsintervall
- **Multi-Stream-Support**: Unterstützung für mehrere Radio-Streams
- **Automatische Aktualisierung**: Konfigurierbare Auto-Refresh-Funktion
- **Responsive Design**: Optimiert für alle Bildschirmgrößen

## Systemanforderungen

- PHP-Fusion v9.x
- PHP 7.0 oder höher
- MySQL 5.6 oder höher
- allow_url_fopen aktiviert (für Stream-Abfragen)

## Installation

### 1. Dateien hochladen

Laden Sie den kompletten `radio_status_panel` Ordner in das `infusions` Verzeichnis Ihrer PHP-Fusion Installation hoch:

```
/infusions/radio_status_panel/
```

### 2. Infusion installieren

1. Melden Sie sich im PHP-Fusion Admin-Panel an
2. Navigieren Sie zu **System Admin** → **Infusions**
3. Suchen Sie "Radio Status Panel" in der Liste
4. Klicken Sie auf **Installieren**

### 3. Admin-Rechte vergeben

1. Gehen Sie zu **Benutzer-Verwaltung** → **Benutzergruppen**
2. Wählen Sie die gewünschte Benutzergruppe aus (z.B. Administratoren)
3. Aktivieren Sie die Berechtigung **RSP** (Radio Status Panel)
4. Speichern Sie die Änderungen

## Konfiguration

### Radio-Stream hinzufügen

1. Gehen Sie zu **System Admin** → **Radio Status Panel**
2. Klicken Sie auf **Stream hinzufügen**
3. Füllen Sie die erforderlichen Felder aus:
   - **Stream-Name**: Name des Radio-Streams (z.B. "Mein Radio")
   - **Server**: Server-Adresse (z.B. "radio.example.com")
   - **Port**: Server-Port (Standard: 8000)
   - **Mountpoint**: Mount-Pfad (Standard: "/")
   - **Server-Typ**: Wählen Sie "Shoutcast v1"
   - **Admin-Passwort**: (Optional) Admin-Passwort für erweiterte Stats
   - **Reihenfolge**: Anzeigereihenfolge (0 = erste Position)
   - **Status**: Aktivieren Sie den Haken für "Aktiv"
4. Klicken Sie auf **Speichern**

### Einstellungen anpassen

Im **Einstellungen**-Tab können Sie folgende Optionen konfigurieren:

- **Aktualisierungsintervall**: Zeit in Sekunden zwischen den Aktualisierungen (Standard: 10)
- **Höreranzahl anzeigen**: Zeigt die aktuelle Hörerzahl an
- **Aktuellen Song anzeigen**: Zeigt den aktuell gespielten Song an
- **Maximale Höreranzahl anzeigen**: Zeigt die maximale Hörerzahl an
- **Bitrate anzeigen**: Zeigt die Stream-Bitrate an
- **Genre anzeigen**: Zeigt das Genre des Streams an

### Panel aktivieren

1. Gehen Sie zu **Content Admin** → **Panels**
2. Suchen Sie "Radio Status" in der Liste
3. Stellen Sie sicher, dass das Panel aktiviert ist
4. Wählen Sie die gewünschte Seiten-Position (z.B. Links, Rechts, Oben, Unten)
5. Konfigurieren Sie die Zugriffsrechte und Sichtbarkeit
6. Speichern Sie die Änderungen

## Verwendung

### Frontend-Anzeige

Nach der Installation und Konfiguration erscheint das Radio-Status-Panel automatisch auf Ihrer Homepage (je nach Panel-Position).

Das Panel zeigt:
- **Stream-Name** mit Status-Badge (Online/Offline)
- **Aktueller Song** mit Musik-Icon
- **Hörerzahl** mit Benutzer-Icon
- **Bitrate** (falls konfiguriert)
- **Genre** (falls verfügbar)

### Shoutcast v1 Server konfigurieren

Für optimale Ergebnisse sollte Ihr Shoutcast v1 Server korrekt konfiguriert sein:

1. Stellen Sie sicher, dass der Server auf dem angegebenen Port erreichbar ist
2. Die Stats-Seite sollte unter `http://server:port/7.html` verfügbar sein
3. Optional: Konfigurieren Sie ein Admin-Passwort für erweiterte Statistiken

### Tipps

- **Performance**: Bei vielen Hörern empfiehlt sich ein höheres Aktualisierungsintervall (z.B. 30 Sekunden)
- **Fehlersuche**: Wenn der Stream als "Offline" angezeigt wird:
  - Überprüfen Sie Server und Port
  - Stellen Sie sicher, dass `allow_url_fopen` in PHP aktiviert ist
  - Prüfen Sie die Firewall-Einstellungen
  - Testen Sie die Erreichbarkeit: `http://ihr-server:port/7.html`

## Technische Details

### Dateistruktur

```
radio_status_panel/
├── infusion.php                    # Infusion-Definition
├── infusion_db.php                 # Datenbank-Konstanten
├── radio_status_panel.php          # Panel-Display-Datei
├── admin/
│   └── radio_admin.php             # Admin-Interface
├── classes/
│   └── ShoutcastReader.php         # Shoutcast API-Klasse
├── locale/
│   └── German.php                  # Deutsche Sprachdatei
└── templates/                       # (Reserviert für Templates)
```

### Datenbank-Tabellen

- **fusion_radio_status**: Speichert Stream-Konfigurationen
- **fusion_radio_settings**: Speichert globale Einstellungen

### API-Integration

Die `ShoutcastReader`-Klasse kommuniziert mit dem Shoutcast v1 Server über:
- Stats-Endpoint: `/7.html` - Liefert Live-Statistiken
- Format: `currentlisteners,peaklisteners,maxlisteners,uniquelisteners,bitrate,songtitle`

## Erweiterungen

### Zukünftige Features (geplant)

- Shoutcast v2 Unterstützung
- Icecast Server Unterstützung
- AJAX-basierte Aktualisierung (ohne Seiten-Reload)
- Historische Statistiken
- Hörerzahl-Diagramme
- Player-Integration
- Mobile App Integration

## Lizenz

Diese Infusion wird unter der GNU Affero General Public License v3.0 veröffentlicht.

## Support

Bei Problemen oder Fragen:
1. Überprüfen Sie die PHP-Fusion Logs
2. Aktivieren Sie Debug-Modus in PHP-Fusion
3. Überprüfen Sie die Server-Erreichbarkeit
4. Kontaktieren Sie den PHP-Fusion Support

## Credits

Entwickelt für die PHP-Fusion Community
- PHP-Fusion: https://www.php-fusion.co.uk/
- Shoutcast: https://www.shoutcast.com/

## Changelog

### Version 1.0.0 (2025-11-02)
- Erste Veröffentlichung
- Shoutcast v1 Unterstützung
- Admin-Panel
- Multi-Stream-Support
- Auto-Refresh-Funktion
- Deutsche Lokalisierung

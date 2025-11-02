# Radio Status Panel - Installation & Fehlerbehebung

## Problem: Admin-Panel erscheint nicht

Wenn das Admin-Panel nicht im Backend erscheint, liegt das wahrscheinlich daran, dass die Infusion vor der Korrektur installiert wurde.

## Lösung 1: Neuinstallation (Empfohlen)

### Schritt 1: Infusion deinstallieren
1. Melden Sie sich im PHP-Fusion Admin-Panel an
2. Gehen Sie zu **System Admin** → **Infusions**
3. Suchen Sie "Radio Status Panel"
4. Klicken Sie auf **Deinstallieren**
5. Bestätigen Sie die Deinstallation

**ACHTUNG**: Dies löscht alle Ihre konfigurierten Streams und Einstellungen!

### Schritt 2: Infusion neu installieren
1. Gehen Sie zu **System Admin** → **Infusions**
2. Suchen Sie "Radio Status Panel"
3. Klicken Sie auf **Installieren**
4. Das Admin-Panel sollte nun erscheinen

### Schritt 3: Admin-Panel aufrufen
1. Gehen Sie zu **System Admin**
2. In der linken Navigation sollte nun **"Radio Status Panel"** erscheinen
3. Klicken Sie darauf, um Streams zu konfigurieren

## Lösung 2: Manuelle Datenbank-Korrektur (Behält Daten)

Falls Sie bereits Streams konfiguriert haben und diese nicht verlieren möchten:

### SQL-Befehl ausführen

Führen Sie folgenden SQL-Befehl in phpMyAdmin aus (ersetzen Sie `fusion_` durch Ihr Datenbank-Präfix):

```sql
INSERT INTO fusion_admin (admin_rights, admin_image, admin_title, admin_link, admin_page)
VALUES ('RSP', 'radio.svg', 'Radio Status Panel', '../infusions/radio_status_panel/admin/radio_admin.php', '5');
```

### Admin-Rechte vergeben

Danach müssen Sie die Admin-Rechte noch vergeben:

1. Gehen Sie zu **Benutzer-Verwaltung** → **Benutzergruppen**
2. Wählen Sie **Super Administrators** oder **Administrators**
3. Scrollen Sie nach unten zu den Rechten
4. Aktivieren Sie die Checkbox bei **RSP** (Radio Status Panel)
5. Klicken Sie auf **Speichern**

## Wo finde ich das Admin-Panel?

Nach erfolgreicher Installation finden Sie das Admin-Panel hier:

1. **In der Admin-Navigation**:
   - System Admin → Radio Status Panel

2. **Oder direkt über URL**:
   - `https://ihre-domain.de/infusions/radio_status_panel/admin/radio_admin.php`

3. **Im Admin-Dashboard**:
   - Unter "Content & Infusions" oder "System"

## Admin-Panel Funktionen

Im Admin-Panel können Sie:

- ✅ **Streams verwalten** (Hinzufügen/Bearbeiten/Löschen)
- ✅ **Einstellungen konfigurieren** (Refresh-Intervall, Anzeigeoptionen)
- ✅ **Mehrere Streams** anlegen
- ✅ **Stream-Reihenfolge** festlegen
- ✅ **Streams aktivieren/deaktivieren**

## Stream hinzufügen - Schnellanleitung

1. Gehen Sie zu **System Admin** → **Radio Status Panel**
2. Klicken Sie auf **Stream hinzufügen**
3. Füllen Sie die Felder aus:

### Erforderliche Felder:

| Feld | Beispiel | Beschreibung |
|------|----------|--------------|
| **Stream-Name** | "Mein Radio" | Name, der angezeigt wird |
| **Server** | radio.example.com | Server-Adresse (ohne http://) |
| **Port** | 8000 | Server-Port |
| **Mountpoint** | / | Mount-Pfad (meist "/") |
| **Server-Typ** | Shoutcast v1 | Wählen Sie Ihren Server-Typ |

### Optionale Felder:

| Feld | Beschreibung |
|------|--------------|
| **Admin-Passwort** | Für erweiterte Stats (optional) |
| **Reihenfolge** | Anzeigereihenfolge (0 = erste Position) |
| **Status** | Aktiviert/Deaktiviert |

4. Klicken Sie auf **Speichern**

## Shoutcast v1 Server testen

Bevor Sie den Stream in PHP-Fusion hinzufügen, testen Sie ob er erreichbar ist:

1. **Im Browser öffnen**:
   ```
   http://ihr-server:port/7.html
   ```

2. **Erwartete Ausgabe** (Beispiel):
   ```
   45,89,100,23,128,Artist - Song Title
   ```

3. **Format erklärt**:
   - 45 = Aktuelle Hörer
   - 89 = Peak Hörer
   - 100 = Max. Hörer
   - 23 = Unique Hörer
   - 128 = Bitrate
   - "Artist - Song Title" = Aktueller Song

Falls die Seite nicht lädt:
- ❌ Server ist offline oder nicht erreichbar
- ❌ Firewall blockiert den Port
- ❌ Server-Adresse oder Port falsch

## Häufige Probleme

### Problem: Admin-Panel zeigt "Access Denied"

**Lösung**: Sie haben keine Admin-Rechte für das Panel
1. Gehen Sie zu **Benutzer-Verwaltung** → **Benutzergruppen**
2. Bearbeiten Sie Ihre Benutzergruppe
3. Aktivieren Sie **RSP** Rechte
4. Speichern

### Problem: Stream zeigt immer "Offline"

**Ursachen**:
- Server ist wirklich offline
- `allow_url_fopen` ist in PHP deaktiviert
- Firewall blockiert ausgehende Verbindungen
- Server-Adresse oder Port falsch

**Lösung**:
1. Testen Sie: `http://ihr-server:port/7.html` im Browser
2. Prüfen Sie `allow_url_fopen` in php.ini
3. Kontaktieren Sie Ihren Hosting-Provider

### Problem: Panel erscheint nicht auf der Website

**Lösung**:
1. Gehen Sie zu **Content Admin** → **Panels**
2. Suchen Sie "Radio Status"
3. Stellen Sie sicher, dass:
   - ✅ Status = Aktiviert
   - ✅ Seiten-Position gewählt (z.B. Links)
   - ✅ Sichtbarkeit = Alle Seiten (oder gewünschte Seiten)
4. Speichern

## Center Panel Installation

Für das große Center Panel siehe:
- [CENTER_PANEL_INSTALLATION.md](CENTER_PANEL_INSTALLATION.md)

## Support

Bei weiteren Problemen:
1. Überprüfen Sie die PHP-Fehlerprotokolle
2. Aktivieren Sie Debug-Modus in PHP-Fusion
3. Prüfen Sie die Datenbank-Tabellen:
   - `fusion_radio_status` (Streams)
   - `fusion_radio_settings` (Einstellungen)
   - `fusion_admin` (Admin-Panel-Link)

## Version

Diese Anleitung gilt für Radio Status Panel v1.1.0 und höher.

<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: radio_admin.php
| Author: Radio Status Panel Infusion
+--------------------------------------------------------*/
require_once "../../maincore.php";

pageAccess('RSP');

require_once THEMES."templates/admin_header.php";

// Simple debug test
echo "<h2>Radio Status Panel - Admin</h2>";
echo "<p>PHP-Fusion Version: ".fusion_get_settings('version')."</p>";
echo "<p>Test erfolgreich!</p>";

// Try to load infusion_db
if (file_exists(INFUSIONS."radio_status_panel/infusion_db.php")) {
    require_once INFUSIONS."radio_status_panel/infusion_db.php";
    echo "<p>✓ infusion_db.php geladen</p>";
    echo "<p>DB_RADIO_STATUS: ".DB_RADIO_STATUS."</p>";
    echo "<p>DB_RADIO_SETTINGS: ".DB_RADIO_SETTINGS."</p>";
} else {
    echo "<p>✗ infusion_db.php nicht gefunden</p>";
}

// Try to load locale
if (file_exists(INFUSIONS."radio_status_panel/locale/German.php")) {
    require_once INFUSIONS."radio_status_panel/locale/German.php";
    echo "<p>✓ German.php geladen</p>";

    if (isset($locale['RSP_title'])) {
        echo "<p>Locale RSP_title: ".$locale['RSP_title']."</p>";
    }
} else {
    echo "<p>✗ German.php nicht gefunden</p>";
}

// Check if tables exist
$tables_exist = true;
$result = dbquery("SHOW TABLES LIKE '".DB_RADIO_STATUS."'");
if (dbrows($result) > 0) {
    echo "<p>✓ Tabelle ".DB_RADIO_STATUS." existiert</p>";

    // Count streams
    $count_result = dbquery("SELECT COUNT(*) as count FROM ".DB_RADIO_STATUS);
    $count_data = dbarray($count_result);
    echo "<p>Anzahl Streams: ".$count_data['count']."</p>";
} else {
    echo "<p>✗ Tabelle ".DB_RADIO_STATUS." existiert NICHT</p>";
    $tables_exist = false;
}

$result = dbquery("SHOW TABLES LIKE '".DB_RADIO_SETTINGS."'");
if (dbrows($result) > 0) {
    echo "<p>✓ Tabelle ".DB_RADIO_SETTINGS." existiert</p>";
} else {
    echo "<p>✗ Tabelle ".DB_RADIO_SETTINGS." existiert NICHT</p>";
    $tables_exist = false;
}

if (!$tables_exist) {
    echo "<div style='background:#f44336;color:white;padding:15px;margin:20px 0;'>";
    echo "<strong>FEHLER: Datenbank-Tabellen fehlen!</strong><br>";
    echo "Bitte gehen Sie zu System Admin → Infusions und installieren Sie die 'Radio Status Panel' Infusion.";
    echo "</div>";
}

require_once THEMES."templates/admin_footer.php";

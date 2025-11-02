<?php
// Step 1: Absolute minimal test
echo "Step 1: PHP funktioniert<br>";

// Step 2: Try to load maincore
echo "Step 2: Versuche maincore.php zu laden...<br>";

if (file_exists("../../maincore.php")) {
    echo "Step 2a: maincore.php existiert<br>";
    require_once "../../maincore.php";
    echo "Step 2b: maincore.php erfolgreich geladen<br>";
} else {
    die("FEHLER: maincore.php nicht gefunden!");
}

// Step 3: Check if we're in Fusion
echo "Step 3: IN_FUSION defined? " . (defined('IN_FUSION') ? 'JA' : 'NEIN') . "<br>";

// Step 4: Try pageAccess
echo "Step 4: Versuche pageAccess('RSP')...<br>";
try {
    pageAccess('RSP');
    echo "Step 4a: pageAccess erfolgreich<br>";
} catch (Exception $e) {
    echo "Step 4b: pageAccess FEHLER: " . $e->getMessage() . "<br>";
}

// Step 5: Load admin header
echo "Step 5: Versuche admin_header.php zu laden...<br>";
if (defined('THEMES') && file_exists(THEMES."templates/admin_header.php")) {
    echo "Step 5a: admin_header.php existiert<br>";
    require_once THEMES."templates/admin_header.php";
    echo "Step 5b: admin_header.php erfolgreich geladen<br>";
} else {
    echo "Step 5c: THEMES nicht definiert oder Datei nicht gefunden<br>";
}

echo "<h2>ERFOLG: Alle Schritte abgeschlossen!</h2>";

require_once THEMES."templates/admin_footer.php";

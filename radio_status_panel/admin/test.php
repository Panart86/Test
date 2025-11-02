<?php
// Absolute minimal test - NO pageAccess
echo "TEST 1: PHP works<br>";

// Check maincore path
$maincore_path = "../../maincore.php";
echo "TEST 2: Checking maincore path: " . $maincore_path . "<br>";
echo "TEST 3: File exists? " . (file_exists($maincore_path) ? "YES" : "NO") . "<br>";

if (file_exists($maincore_path)) {
    echo "TEST 4: Loading maincore...<br>";
    require_once $maincore_path;
    echo "TEST 5: Maincore loaded successfully<br>";
    echo "TEST 6: IN_FUSION defined? " . (defined('IN_FUSION') ? 'YES' : 'NO') . "<br>";

    if (defined('INFUSIONS')) {
        echo "TEST 7: INFUSIONS path: " . INFUSIONS . "<br>";
    }

    // Check if RSP rights exist
    echo "TEST 8: Checking RSP admin rights...<br>";
    $check_rights = dbquery("SELECT * FROM ".DB_PREFIX."admin WHERE admin_rights='RSP'");
    if (dbrows($check_rights) > 0) {
        echo "TEST 9: RSP rights exist in database<br>";
        $rights_data = dbarray($check_rights);
        echo "TEST 10: Admin link: " . $rights_data['admin_link'] . "<br>";
    } else {
        echo "TEST 9: <strong style='color:red;'>RSP rights NOT found in database!</strong><br>";
        echo "<div style='background:yellow;padding:10px;margin:10px 0;'>";
        echo "<strong>PROBLEM FOUND:</strong> Die Admin-Rechte wurden nicht in die Datenbank eingetragen!<br>";
        echo "LÖSUNG: Gehen Sie zu System Admin → Infusions und deinstallieren/installieren Sie die Radio Status Panel Infusion neu.";
        echo "</div>";
    }

    // Check if tables exist
    echo "TEST 11: Checking database tables...<br>";
    $check_table = dbquery("SHOW TABLES LIKE '".DB_PREFIX."radio_status'");
    if (dbrows($check_table) > 0) {
        echo "TEST 12: Table ".DB_PREFIX."radio_status exists<br>";
    } else {
        echo "TEST 12: <strong style='color:red;'>Table ".DB_PREFIX."radio_status NOT found!</strong><br>";
    }

} else {
    echo "TEST 4: <strong style='color:red;'>MAINCORE NOT FOUND!</strong><br>";
    echo "Current directory: " . getcwd() . "<br>";
    echo "Looking for: " . realpath($maincore_path) . "<br>";
}

echo "<hr>";
echo "<h2>If you see this, the basic test worked!</h2>";

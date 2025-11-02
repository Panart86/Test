<?php
echo "<h2>Searching for maincore.php</h2>";
echo "Current directory: " . getcwd() . "<br><br>";

$possible_paths = [
    "../../maincore.php",
    "../../../maincore.php",
    "../../../../maincore.php",
    "../../../web10/maincore.php",
    "/var/www/clients/client6/web10/testseite/maincore.php",
    "/var/www/clients/client6/web10/maincore.php",
    $_SERVER['DOCUMENT_ROOT'] . "/maincore.php"
];

echo "<h3>Testing paths:</h3>";
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Path</th><th>Exists?</th><th>Realpath</th></tr>";

foreach ($possible_paths as $path) {
    $exists = file_exists($path);
    $realpath = $exists ? realpath($path) : 'N/A';

    echo "<tr>";
    echo "<td>" . htmlspecialchars($path) . "</td>";
    echo "<td style='color:" . ($exists ? 'green' : 'red') . ";'><strong>" . ($exists ? 'YES ✓' : 'NO ✗') . "</strong></td>";
    echo "<td>" . htmlspecialchars($realpath) . "</td>";
    echo "</tr>";

    if ($exists) {
        echo "<tr><td colspan='3' style='background:#90EE90;'>";
        echo "<strong>FOUND!</strong> Use this path: <code>" . $path . "</code>";
        echo "</td></tr>";
    }
}

echo "</table>";

echo "<br><h3>Server Info:</h3>";
echo "DOCUMENT_ROOT: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
echo "SCRIPT_FILENAME: " . $_SERVER['SCRIPT_FILENAME'] . "<br>";
echo "PHP_SELF: " . $_SERVER['PHP_SELF'] . "<br>";

// Try to find it by going up directories
echo "<br><h3>Searching parent directories:</h3>";
$current = __DIR__;
for ($i = 0; $i < 5; $i++) {
    $current = dirname($current);
    $test_path = $current . "/maincore.php";
    echo "Level $i: " . $test_path . " - " . (file_exists($test_path) ? "<strong style='color:green;'>FOUND! ✓</strong>" : "not found") . "<br>";

    if (file_exists($test_path)) {
        echo "<div style='background:yellow;padding:10px;margin:10px 0;'>";
        echo "<strong>SOLUTION:</strong> Use this path in radio_admin.php:<br>";
        echo "<code>require_once \"" . str_repeat("../", $i+1) . "maincore.php\";</code>";
        echo "</div>";
        break;
    }
}

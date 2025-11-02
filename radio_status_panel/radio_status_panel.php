<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: radio_status_panel.php
| Author: Radio Status Panel
+--------------------------------------------------------*/
defined('IN_FUSION') || exit;

require_once INFUSIONS."radio_status_panel/infusion_db.php";
require_once RADIO_STATUS_LOCALE;
require_once INFUSIONS."radio_status_panel/classes/ShoutcastReader.php";

// Load settings
$settings = [];
$result = dbquery("SELECT * FROM ".DB_RADIO_SETTINGS);
while ($data = dbarray($result)) {
    $settings[$data['settings_name']] = $data['settings_value'];
}

// Default settings
$refresh_interval = isset($settings['refresh_interval']) ? $settings['refresh_interval'] : 10;
$show_listeners = isset($settings['show_listeners']) ? $settings['show_listeners'] : 1;
$show_current_song = isset($settings['show_current_song']) ? $settings['show_current_song'] : 1;
$show_max_listeners = isset($settings['show_max_listeners']) ? $settings['show_max_listeners'] : 1;
$show_bitrate = isset($settings['show_bitrate']) ? $settings['show_bitrate'] : 1;
$show_genre = isset($settings['show_genre']) ? $settings['show_genre'] : 1;

// Add CSS
add_to_head("<style>
    .radio-status-container {
        margin: 0;
    }
    .radio-stream {
        margin-bottom: 10px;
    }
    .radio-stream:last-child {
        margin-bottom: 0;
    }
    .radio-info-item {
        margin: 8px 0;
        padding: 5px 0;
    }
    .radio-info-item i {
        width: 20px;
        text-align: center;
        margin-right: 5px;
    }
    .radio-current-song {
        font-style: italic;
    }
    .label-success {
        background-color: #5cb85c;
    }
    .label-danger {
        background-color: #d9534f;
    }
</style>");

// Add auto-refresh JavaScript
if ($refresh_interval > 0) {
    add_to_footer("<script>
    (function() {
        var refreshInterval = ".$refresh_interval." * 1000;

        function refreshRadioStatus() {
            // Reload panel content via AJAX
            var container = document.querySelector('.radio-status-container');
            if (container) {
                // Simple reload - in production, use AJAX for better UX
                setTimeout(function() {
                    location.reload();
                }, refreshInterval);
            }
        }

        if (refreshInterval > 0) {
            refreshRadioStatus();
        }
    })();
    </script>");
}

// Load active streams
$streams_result = dbquery("SELECT * FROM ".DB_RADIO_STATUS." WHERE radio_status='1' ORDER BY radio_order ASC, radio_name ASC");

echo "<div class='radio-status-container'>";

if (dbrows($streams_result)) {
    while ($stream = dbarray($streams_result)) {
        // Fetch stream data
        $reader = new ShoutcastReader($stream['radio_server'], $stream['radio_port'], $stream['radio_mount'], $stream['radio_password']);
        $stream_data = $reader->getStreamData();

        echo "<div class='radio-stream' data-refresh='".$refresh_interval."' data-stream-id='".$stream['radio_id']."'>";
        echo "<div class='panel panel-default'>";
        echo "<div class='panel-heading'>";
        echo "<h4 class='panel-title' style='font-size: 14px;'>";
        echo "<i class='fa fa-radio'></i> ".htmlspecialchars($stream['radio_name']);

        if ($stream_data !== false && isset($stream_data['online']) && $stream_data['online']) {
            echo " <span class='label label-success pull-right'><i class='fa fa-circle'></i> ".$locale['RSP_online']."</span>";
        } else {
            echo " <span class='label label-danger pull-right'><i class='fa fa-circle'></i> ".$locale['RSP_offline']."</span>";
        }

        echo "</h4>";
        echo "</div>";

        echo "<div class='panel-body'>";

        if ($stream_data === false) {
            // Fehler beim Abrufen der Daten
            echo "<div class='alert alert-warning m-b-0'>";
            echo "<i class='fa fa-exclamation-triangle'></i> ";
            echo "<strong>Verbindungsfehler:</strong> ".$reader->getError();
            echo "</div>";
        } elseif ($stream_data !== false && isset($stream_data['online']) && $stream_data['online']) {
            echo "<div class='radio-info'>";

            // Current song
            if ($show_current_song) {
                echo "<div class='radio-info-item'>";
                echo "<i class='fa fa-music'></i> ";
                echo "<strong>".$locale['RSP_current_song'].":</strong> ";
                echo "<span class='radio-current-song'>".htmlspecialchars($stream_data['current_song'])."</span>";
                echo "</div>";
            }

            // Listeners
            if ($show_listeners) {
                echo "<div class='radio-info-item'>";
                echo "<i class='fa fa-users'></i> ";
                echo "<strong>".$locale['RSP_listeners'].":</strong> ";
                echo "<span class='radio-listeners'>".(int)$stream_data['listeners']."</span>";
                if ($show_max_listeners && isset($stream_data['max_listeners'])) {
                    echo " / ".(int)$stream_data['max_listeners'];
                }
                echo "</div>";
            }

            // Bitrate
            if ($show_bitrate && isset($stream_data['bitrate']) && $stream_data['bitrate'] > 0) {
                echo "<div class='radio-info-item'>";
                echo "<i class='fa fa-signal'></i> ";
                echo "<strong>".$locale['RSP_bitrate'].":</strong> ";
                echo "<span class='radio-bitrate'>".(int)$stream_data['bitrate']." kbps</span>";
                echo "</div>";
            }

            echo "</div>";
        } else {
            echo "<div class='alert alert-warning' style='margin: 0;'>";
            echo "<i class='fa fa-exclamation-triangle'></i> ";
            echo $locale['RSP_offline'];
            echo "</div>";
        }

        echo "</div>"; // panel-body
        echo "</div>"; // panel
        echo "</div>"; // radio-stream
    }
} else {
    // No streams configured
    echo "<div class='well text-center'>";
    echo $locale['RSP_no_streams'];
    echo "</div>";
}

echo "</div>"; // radio-status-container

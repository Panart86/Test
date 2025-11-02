<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: radio_status_center.php
| Author: Radio Status Panel - Center Display
+--------------------------------------------------------*/
require_once "../../maincore.php";
require_once INFUSIONS."radio_status_panel/infusion_db.php";
require_once RADIO_STATUS_LOCALE;
require_once THEMES."templates/header.php";

// Load settings
$settings = [];
$result = dbquery("SELECT * FROM ".DB_RADIO_SETTINGS);
while ($data = dbarray($result)) {
    $settings[$data['settings_name']] = $data['settings_value'];
}

// Default settings
$show_listeners = isset($settings['show_listeners']) ? $settings['show_listeners'] : 1;
$show_current_song = isset($settings['show_current_song']) ? $settings['show_current_song'] : 1;
$show_max_listeners = isset($settings['show_max_listeners']) ? $settings['show_max_listeners'] : 1;
$show_bitrate = isset($settings['show_bitrate']) ? $settings['show_bitrate'] : 1;
$show_genre = isset($settings['show_genre']) ? $settings['show_genre'] : 1;
$refresh_interval = isset($settings['refresh_interval']) ? $settings['refresh_interval'] : 10;

add_to_title($locale['global_200'].$locale['RSP_title']);

// Add custom CSS
add_to_head("<style>
.radio-center-container {
    margin: 0 auto;
    max-width: 1200px;
    padding: 20px 0;
}

.radio-center-header {
    text-align: center;
    margin-bottom: 30px;
    padding: 20px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.radio-center-header h2 {
    margin: 0;
    font-size: 32px;
    font-weight: bold;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
}

.radio-center-header p {
    margin: 10px 0 0 0;
    opacity: 0.9;
    font-size: 16px;
}

.radio-stream-card {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 8px 16px rgba(0,0,0,0.1);
    margin-bottom: 30px;
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.radio-stream-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 24px rgba(0,0,0,0.15);
}

.radio-card-header {
    background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
    color: white;
    padding: 20px 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.radio-card-header.offline {
    background: linear-gradient(135deg, #95a5a6 0%, #7f8c8d 100%);
}

.radio-card-title {
    margin: 0;
    font-size: 24px;
    font-weight: bold;
    display: flex;
    align-items: center;
}

.radio-card-title i {
    margin-right: 15px;
    font-size: 28px;
    animation: pulse 2s ease-in-out infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.6; }
}

.radio-status-badge {
    padding: 8px 20px;
    border-radius: 20px;
    font-size: 14px;
    font-weight: bold;
    display: flex;
    align-items: center;
    gap: 8px;
}

.radio-status-badge.online {
    background: #27ae60;
    box-shadow: 0 0 20px rgba(39, 174, 96, 0.5);
}

.radio-status-badge.offline {
    background: #e74c3c;
}

.radio-status-badge i {
    animation: blink 1.5s ease-in-out infinite;
}

@keyframes blink {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.3; }
}

.radio-card-body {
    padding: 30px;
}

.radio-current-song-section {
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    padding: 25px;
    border-radius: 10px;
    margin-bottom: 25px;
    text-align: center;
}

.radio-current-song-label {
    font-size: 14px;
    color: #7f8c8d;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 10px;
    font-weight: 600;
}

.radio-current-song-title {
    font-size: 26px;
    color: #2c3e50;
    font-weight: bold;
    margin: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 15px;
}

.radio-current-song-title i {
    color: #e74c3c;
    font-size: 30px;
    animation: rotate 3s linear infinite;
}

@keyframes rotate {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.radio-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.radio-stat-card {
    background: white;
    border: 2px solid #ecf0f1;
    border-radius: 10px;
    padding: 20px;
    text-align: center;
    transition: all 0.3s ease;
}

.radio-stat-card:hover {
    border-color: #3498db;
    transform: scale(1.05);
    box-shadow: 0 4px 8px rgba(52, 152, 219, 0.2);
}

.radio-stat-icon {
    font-size: 36px;
    margin-bottom: 10px;
    color: #3498db;
}

.radio-stat-value {
    font-size: 32px;
    font-weight: bold;
    color: #2c3e50;
    margin: 10px 0;
}

.radio-stat-label {
    font-size: 14px;
    color: #7f8c8d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 600;
}

.radio-offline-message {
    text-align: center;
    padding: 40px;
    color: #7f8c8d;
}

.radio-offline-message i {
    font-size: 64px;
    margin-bottom: 20px;
    color: #e74c3c;
}

.radio-offline-message h3 {
    margin: 0;
    color: #2c3e50;
}

.radio-no-streams {
    text-align: center;
    padding: 60px 20px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.radio-no-streams i {
    font-size: 72px;
    color: #bdc3c7;
    margin-bottom: 20px;
}

.radio-no-streams h3 {
    color: #7f8c8d;
    margin: 0;
}

.radio-loading {
    text-align: center;
    padding: 20px;
}

.radio-loading i {
    font-size: 48px;
    color: #3498db;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.radio-listeners-bar {
    margin-top: 15px;
    background: #ecf0f1;
    border-radius: 10px;
    height: 8px;
    overflow: hidden;
}

.radio-listeners-fill {
    height: 100%;
    background: linear-gradient(90deg, #3498db 0%, #2ecc71 100%);
    border-radius: 10px;
    transition: width 0.5s ease;
}

@media (max-width: 768px) {
    .radio-card-header {
        flex-direction: column;
        gap: 15px;
        text-align: center;
    }

    .radio-stats-grid {
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    }

    .radio-current-song-title {
        font-size: 20px;
    }

    .radio-center-header h2 {
        font-size: 24px;
    }
}
</style>");

opentable($locale['RSP_title']);

echo "<div class='radio-center-container' id='radio-center-container'>";

// Header
echo "<div class='radio-center-header'>";
echo "<h2><i class='fa fa-broadcast-tower'></i> ".$locale['RSP_title']."</h2>";
echo "<p>Live Radio Stream Status</p>";
echo "</div>";

// Load active streams
$streams_result = dbquery("SELECT * FROM ".DB_RADIO_STATUS." WHERE radio_status='1' ORDER BY radio_order ASC, radio_name ASC");

if (dbrows($streams_result)) {
    require_once INFUSIONS."radio_status_panel/classes/ShoutcastReader.php";

    while ($stream = dbarray($streams_result)) {
        // Fetch stream data
        $reader = new ShoutcastReader($stream['radio_server'], $stream['radio_port'], $stream['radio_password']);
        $stream_data = $reader->getStreamData();

        $is_online = ($stream_data !== false && isset($stream_data['online']) && $stream_data['online']);

        echo "<div class='radio-stream-card' data-stream-id='".$stream['radio_id']."'>";

        // Card Header
        echo "<div class='radio-card-header".($is_online ? '' : ' offline')."'>";
        echo "<h3 class='radio-card-title'>";
        echo "<i class='fa fa-radio'></i>";
        echo htmlspecialchars($stream['radio_name']);
        echo "</h3>";
        echo "<div class='radio-status-badge ".($is_online ? 'online' : 'offline')."'>";
        echo "<i class='fa fa-circle'></i>";
        echo $is_online ? $locale['RSP_online'] : $locale['RSP_offline'];
        echo "</div>";
        echo "</div>";

        // Card Body
        echo "<div class='radio-card-body'>";

        if ($is_online) {
            // Current Song Section
            if ($show_current_song) {
                echo "<div class='radio-current-song-section'>";
                echo "<div class='radio-current-song-label'>".$locale['RSP_current_song']."</div>";
                echo "<h4 class='radio-current-song-title'>";
                echo "<i class='fa fa-music'></i>";
                echo "<span>".htmlspecialchars($stream_data['current_song'])."</span>";
                echo "</h4>";
                echo "</div>";
            }

            // Stats Grid
            echo "<div class='radio-stats-grid'>";

            // Listeners
            if ($show_listeners) {
                echo "<div class='radio-stat-card'>";
                echo "<div class='radio-stat-icon'><i class='fa fa-users'></i></div>";
                echo "<div class='radio-stat-value'>".$stream_data['listeners']."</div>";
                echo "<div class='radio-stat-label'>".$locale['RSP_listeners']."</div>";

                // Listeners bar
                if ($show_max_listeners && $stream_data['max_listeners'] > 0) {
                    $percentage = ($stream_data['listeners'] / $stream_data['max_listeners']) * 100;
                    echo "<div class='radio-listeners-bar'>";
                    echo "<div class='radio-listeners-fill' style='width: ".$percentage."%'></div>";
                    echo "</div>";
                }
                echo "</div>";
            }

            // Max Listeners
            if ($show_max_listeners) {
                echo "<div class='radio-stat-card'>";
                echo "<div class='radio-stat-icon'><i class='fa fa-user-plus'></i></div>";
                echo "<div class='radio-stat-value'>".$stream_data['max_listeners']."</div>";
                echo "<div class='radio-stat-label'>".$locale['RSP_max_listeners']."</div>";
                echo "</div>";
            }

            // Peak Listeners
            if (isset($stream_data['peak_listeners']) && $stream_data['peak_listeners'] > 0) {
                echo "<div class='radio-stat-card'>";
                echo "<div class='radio-stat-icon'><i class='fa fa-chart-line'></i></div>";
                echo "<div class='radio-stat-value'>".$stream_data['peak_listeners']."</div>";
                echo "<div class='radio-stat-label'>Peak Hörer</div>";
                echo "</div>";
            }

            // Bitrate
            if ($show_bitrate && isset($stream_data['bitrate']) && $stream_data['bitrate'] > 0) {
                echo "<div class='radio-stat-card'>";
                echo "<div class='radio-stat-icon'><i class='fa fa-signal'></i></div>";
                echo "<div class='radio-stat-value'>".$stream_data['bitrate']."</div>";
                echo "<div class='radio-stat-label'>".$locale['RSP_bitrate']." (kbps)</div>";
                echo "</div>";
            }

            // Server Info
            echo "<div class='radio-stat-card'>";
            echo "<div class='radio-stat-icon'><i class='fa fa-server'></i></div>";
            echo "<div class='radio-stat-value' style='font-size: 16px;'>".$stream['radio_server'].":".$stream['radio_port']."</div>";
            echo "<div class='radio-stat-label'>Server</div>";
            echo "</div>";

            echo "</div>"; // stats-grid

        } else {
            // Offline message
            echo "<div class='radio-offline-message'>";
            echo "<i class='fa fa-exclamation-triangle'></i>";
            echo "<h3>Stream ist momentan offline</h3>";
            echo "<p>Der Radio-Stream ist derzeit nicht verfügbar. Bitte versuchen Sie es später erneut.</p>";
            echo "</div>";
        }

        echo "</div>"; // card-body
        echo "</div>"; // stream-card
    }
} else {
    // No streams configured
    echo "<div class='radio-no-streams'>";
    echo "<i class='fa fa-broadcast-tower'></i>";
    echo "<h3>".$locale['RSP_no_streams']."</h3>";
    echo "<p>Es sind derzeit keine Radio-Streams konfiguriert.</p>";
    echo "</div>";
}

echo "</div>"; // radio-center-container

// Add JavaScript for auto-refresh
if ($refresh_interval > 0) {
    add_to_footer("<script>
    (function() {
        var refreshInterval = ".($refresh_interval * 1000).";
        var lastUpdate = Date.now();

        function refreshRadioData() {
            // Use AJAX to refresh data without page reload
            var xhr = new XMLHttpRequest();
            xhr.open('GET', '".INFUSIONS."radio_status_panel/ajax_refresh.php', true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    try {
                        var data = JSON.parse(xhr.responseText);
                        updateStreamData(data);
                        lastUpdate = Date.now();
                    } catch(e) {
                        console.error('Error parsing radio data:', e);
                    }
                }
            };
            xhr.send();
        }

        function updateStreamData(streams) {
            streams.forEach(function(stream) {
                var card = document.querySelector('[data-stream-id=\"' + stream.id + '\"]');
                if (!card) return;

                // Update current song
                var songElement = card.querySelector('.radio-current-song-title span');
                if (songElement && stream.current_song) {
                    songElement.textContent = stream.current_song;
                }

                // Update listener count
                var listenerElement = card.querySelector('.radio-stat-card .radio-stat-value');
                if (listenerElement && stream.listeners !== undefined) {
                    listenerElement.textContent = stream.listeners;
                }

                // Update listeners bar
                var listenerBar = card.querySelector('.radio-listeners-fill');
                if (listenerBar && stream.listeners !== undefined && stream.max_listeners > 0) {
                    var percentage = (stream.listeners / stream.max_listeners) * 100;
                    listenerBar.style.width = percentage + '%';
                }

                // Update status badge
                var statusBadge = card.querySelector('.radio-status-badge');
                var cardHeader = card.querySelector('.radio-card-header');
                if (stream.online) {
                    statusBadge.classList.remove('offline');
                    statusBadge.classList.add('online');
                    cardHeader.classList.remove('offline');
                } else {
                    statusBadge.classList.remove('online');
                    statusBadge.classList.add('offline');
                    cardHeader.classList.add('offline');
                }
            });
        }

        // Start auto-refresh
        if (refreshInterval > 0) {
            setInterval(refreshRadioData, refreshInterval);
        }

        // Add visual indicator of last update
        var container = document.getElementById('radio-center-container');
        if (container) {
            var updateIndicator = document.createElement('div');
            updateIndicator.style.cssText = 'text-align: center; padding: 10px; color: #7f8c8d; font-size: 12px;';
            updateIndicator.innerHTML = '<i class=\"fa fa-sync\"></i> Automatische Aktualisierung alle ' + ".$refresh_interval." + ' Sekunden';
            container.appendChild(updateIndicator);
        }
    })();
    </script>");
}

closetable();

require_once THEMES."templates/footer.php";

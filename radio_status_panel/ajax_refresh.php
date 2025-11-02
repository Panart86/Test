<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: ajax_refresh.php
| Author: Radio Status Panel - AJAX Refresh Endpoint
+--------------------------------------------------------*/
require_once "../../maincore.php";

// Check if this is an AJAX request
if (!defined('IN_FUSION')) {
    die('Access Denied');
}

header('Content-Type: application/json');

require_once INFUSIONS."radio_status_panel/infusion_db.php";
require_once INFUSIONS."radio_status_panel/classes/ShoutcastReader.php";

$response = [];

// Load active streams
$streams_result = dbquery("SELECT * FROM ".DB_RADIO_STATUS." WHERE radio_status='1' ORDER BY radio_order ASC, radio_name ASC");

if (dbrows($streams_result)) {
    while ($stream = dbarray($streams_result)) {
        // Fetch stream data
        $reader = new ShoutcastReader($stream['radio_server'], $stream['radio_port'], $stream['radio_password']);
        $stream_data = $reader->getStreamData();

        $stream_info = [
            'id' => $stream['radio_id'],
            'name' => $stream['radio_name'],
            'online' => false,
            'current_song' => 'Unbekannt',
            'listeners' => 0,
            'max_listeners' => 0,
            'peak_listeners' => 0,
            'bitrate' => 0,
            'genre' => ''
        ];

        if ($stream_data !== false && isset($stream_data['online']) && $stream_data['online']) {
            $stream_info['online'] = true;
            $stream_info['current_song'] = isset($stream_data['current_song']) ? $stream_data['current_song'] : 'Unbekannt';
            $stream_info['listeners'] = isset($stream_data['listeners']) ? (int)$stream_data['listeners'] : 0;
            $stream_info['max_listeners'] = isset($stream_data['max_listeners']) ? (int)$stream_data['max_listeners'] : 0;
            $stream_info['peak_listeners'] = isset($stream_data['peak_listeners']) ? (int)$stream_data['peak_listeners'] : 0;
            $stream_info['bitrate'] = isset($stream_data['bitrate']) ? (int)$stream_data['bitrate'] : 0;
            $stream_info['genre'] = isset($stream_data['genre']) ? $stream_data['genre'] : '';
        }

        $response[] = $stream_info;
    }
}

echo json_encode($response);
exit;

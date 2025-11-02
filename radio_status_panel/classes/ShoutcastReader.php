<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: ShoutcastReader.php
| Author: Radio Status Panel Infusion
+--------------------------------------------------------*/
defined('IN_FUSION') || exit;

/**
 * Class ShoutcastReader
 * Liest Stream-Informationen von Shoutcast v1 Servern aus
 */
class ShoutcastReader {

    private $server;
    private $port;
    private $password;
    private $timeout = 5;
    private $error = '';

    /**
     * Constructor
     *
     * @param string $server Server-Adresse
     * @param int $port Server-Port
     * @param string $password Admin-Passwort (optional)
     */
    public function __construct($server, $port, $password = '') {
        $this->server = $server;
        $this->port = $port;
        $this->password = $password;
    }

    /**
     * Holt die Stream-Status-Daten vom Shoutcast v1 Server
     *
     * @return array|false Array mit Stream-Daten oder false bei Fehler
     */
    public function getStreamData() {
        $data = $this->fetchStats();

        if ($data === false) {
            return false;
        }

        return $this->parseStats($data);
    }

    /**
     * Holt die rohen Stats vom Server
     *
     * @return string|false Rohe Stats oder false bei Fehler
     */
    private function fetchStats() {
        $url = "http://{$this->server}:{$this->port}/7.html";

        // Context für HTTP-Request erstellen
        $opts = [
            'http' => [
                'method' => 'GET',
                'timeout' => $this->timeout,
                'user_agent' => 'PHP-Fusion Radio Status Panel'
            ]
        ];

        // Wenn Passwort vorhanden, Basic Auth hinzufügen
        if (!empty($this->password)) {
            $opts['http']['header'] = "Authorization: Basic " . base64_encode("admin:{$this->password}");
        }

        $context = stream_context_create($opts);

        // Fehlerbehandlung
        $data = @file_get_contents($url, false, $context);

        if ($data === false) {
            $this->error = "Konnte keine Verbindung zum Server herstellen";
            return false;
        }

        return $data;
    }

    /**
     * Parst die Stats vom Shoutcast v1 Server
     * Format: currentlisteners,peaklisteners,maxlisteners,uniquelisteners,bitrate,songtitle
     *
     * @param string $data Rohe Stats
     * @return array Geparste Stats
     */
    private function parseStats($data) {
        $stats = explode(',', trim($data), 7);

        // Standard-Werte, falls Parsing fehlschlägt
        $result = [
            'online' => false,
            'listeners' => 0,
            'peak_listeners' => 0,
            'max_listeners' => 0,
            'unique_listeners' => 0,
            'bitrate' => 0,
            'current_song' => 'Unbekannt',
            'server_name' => '',
            'server_url' => '',
            'genre' => '',
            'song_title' => 'Unbekannt'
        ];

        if (count($stats) >= 6) {
            $result['online'] = true;
            $result['listeners'] = isset($stats[0]) ? (int)$stats[0] : 0;
            $result['peak_listeners'] = isset($stats[1]) ? (int)$stats[1] : 0;
            $result['max_listeners'] = isset($stats[2]) ? (int)$stats[2] : 0;
            $result['unique_listeners'] = isset($stats[3]) ? (int)$stats[3] : 0;
            $result['bitrate'] = isset($stats[4]) ? (int)$stats[4] : 0;
            $result['current_song'] = isset($stats[5]) ? $this->cleanSongTitle($stats[5]) : 'Unbekannt';
            $result['song_title'] = $result['current_song'];

            // Zusätzliche Infos holen, wenn verfügbar
            $extraData = $this->fetchExtraData();
            if ($extraData !== false) {
                $result = array_merge($result, $extraData);
            }
        }

        return $result;
    }

    /**
     * Holt zusätzliche Informationen vom Server (Genre, Server-Name, etc.)
     *
     * @return array|false Array mit zusätzlichen Daten oder false
     */
    private function fetchExtraData() {
        $url = "http://{$this->server}:{$this->port}/index.html";

        $opts = [
            'http' => [
                'method' => 'GET',
                'timeout' => $this->timeout,
                'user_agent' => 'PHP-Fusion Radio Status Panel'
            ]
        ];

        $context = stream_context_create($opts);
        $data = @file_get_contents($url, false, $context);

        if ($data === false) {
            return false;
        }

        $result = [];

        // Server-Name extrahieren
        if (preg_match('/<body.*?>(.*?)<\/body>/is', $data, $matches)) {
            if (preg_match('/SHOUTcast Server/i', $matches[1])) {
                $result['server_name'] = 'SHOUTcast Radio';
            }
        }

        return $result;
    }

    /**
     * Bereinigt den Song-Titel
     *
     * @param string $title Roher Song-Titel
     * @return string Bereinigter Song-Titel
     */
    private function cleanSongTitle($title) {
        // HTML-Entities dekodieren
        $title = html_entity_decode($title, ENT_QUOTES, 'UTF-8');

        // Leerzeichen bereinigen
        $title = trim($title);

        // Wenn leer, Standard zurückgeben
        if (empty($title)) {
            return 'Unbekannt';
        }

        return $title;
    }

    /**
     * Gibt den letzten Fehler zurück
     *
     * @return string Fehlermeldung
     */
    public function getError() {
        return $this->error;
    }

    /**
     * Setzt das Timeout für die Verbindung
     *
     * @param int $timeout Timeout in Sekunden
     */
    public function setTimeout($timeout) {
        $this->timeout = (int)$timeout;
    }
}

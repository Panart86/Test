<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: infusion_db.php
| Author: Radio Status Panel Infusion
+--------------------------------------------------------+
| This program is released as free software under the
| Affero GPL license. You can redistribute it and/or
| modify it under the terms of this license which you
| can read by viewing the included agpl.txt or online
| at www.gnu.org/licenses/agpl.html. Removal of this
| copyright header is strictly prohibited without
| written permission from the original author(s).
+--------------------------------------------------------*/
defined('IN_FUSION') || exit;

// Database table names
if (!defined('DB_RADIO_STATUS')) {
    define('DB_RADIO_STATUS', DB_PREFIX.'radio_status');
}

if (!defined('DB_RADIO_SETTINGS')) {
    define('DB_RADIO_SETTINGS', DB_PREFIX.'radio_settings');
}

// Admin rights
if (!defined('RADIO_STATUS_LOCALE')) {
    $locale_path = INFUSIONS.'radio_status_panel/locale/';

    if (file_exists($locale_path.LOCALESET.'radio_status.php')) {
        define('RADIO_STATUS_LOCALE', $locale_path.LOCALESET.'radio_status.php');
    } else {
        define('RADIO_STATUS_LOCALE', $locale_path.'German.php');
    }
}

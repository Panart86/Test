<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: infusion.php
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

// Infusion general information
$inf_title = "Radio Status Panel";
$inf_description = "Zeigt den aktuellen Radio-Stream-Status mit Shoutcast v1 Unterstützung an";
$inf_version = "1.1.0";
$inf_developer = "PHP-Fusion Community";
$inf_email = "";
$inf_weburl = "";
$inf_folder = "radio_status_panel";
$inf_image = "radio.svg";

// Admin panel settings
$inf_adminpanel[1] = [
    "rights" => "RSP",
    "image" => $inf_image,
    "title" => $inf_title,
    "panel" => "radio_admin.php",
    "page" => 5
];

// Insert admin link into database
$inf_insertdbrow[1] = DB_ADMIN." (admin_rights, admin_image, admin_title, admin_link, admin_page) VALUES ('RSP', '".$inf_image."', '".$inf_title."', '".INFUSIONS."radio_status_panel/admin/radio_admin.php', '5')";

// Insert panel link
$inf_insertdbrow[2] = DB_PANELS." (panel_name, panel_filename, panel_content, panel_side, panel_order, panel_type, panel_access, panel_display, panel_status, panel_url_list, panel_restriction, panel_languages) VALUES ('Radio Status', 'radio_status_panel', '', '1', '5', 'file', '0', '1', '1', '', '3', '".fusion_get_settings('enabled_languages')."')";

// Database tables
$inf_newtable[1] = DB_RADIO_STATUS." (
    radio_id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
    radio_name VARCHAR(200) NOT NULL DEFAULT '',
    radio_server VARCHAR(255) NOT NULL DEFAULT '',
    radio_port SMALLINT(5) UNSIGNED NOT NULL DEFAULT '8000',
    radio_mount VARCHAR(100) NOT NULL DEFAULT '/',
    radio_type ENUM('shoutcast1', 'shoutcast2', 'icecast') NOT NULL DEFAULT 'shoutcast1',
    radio_password VARCHAR(100) NOT NULL DEFAULT '',
    radio_status TINYINT(1) UNSIGNED NOT NULL DEFAULT '1',
    radio_order SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0',
    PRIMARY KEY (radio_id)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_unicode_ci";

$inf_newtable[2] = DB_RADIO_SETTINGS." (
    settings_name VARCHAR(100) NOT NULL DEFAULT '',
    settings_value TEXT NOT NULL,
    PRIMARY KEY (settings_name)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_unicode_ci";

// Insert default settings
$inf_insertdbrow[3] = DB_RADIO_SETTINGS." (settings_name, settings_value) VALUES ('refresh_interval', '10')";
$inf_insertdbrow[4] = DB_RADIO_SETTINGS." (settings_name, settings_value) VALUES ('show_listeners', '1')";
$inf_insertdbrow[5] = DB_RADIO_SETTINGS." (settings_name, settings_value) VALUES ('show_current_song', '1')";
$inf_insertdbrow[6] = DB_RADIO_SETTINGS." (settings_name, settings_value) VALUES ('show_max_listeners', '1')";
$inf_insertdbrow[7] = DB_RADIO_SETTINGS." (settings_name, settings_value) VALUES ('show_bitrate', '1')";
$inf_insertdbrow[8] = DB_RADIO_SETTINGS." (settings_name, settings_value) VALUES ('show_genre', '1')";

// Delete panel on uninstall
$inf_deldbrow[1] = DB_PANELS." WHERE panel_filename='radio_status_panel'";

// Delete tables on uninstall
$inf_droptable[1] = DB_RADIO_STATUS;
$inf_droptable[2] = DB_RADIO_SETTINGS;

// Delete admin panel on uninstall
$inf_deldbrow[2] = DB_ADMIN." WHERE admin_rights='RSP'";

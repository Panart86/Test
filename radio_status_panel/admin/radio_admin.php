<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: radio_admin.php
| Author: Radio Status Panel Infusion
+--------------------------------------------------------*/
require_once "../../../maincore.php";

pageAccess('RSP');

require_once THEMES."templates/admin_header.php";
require_once INFUSIONS."radio_status_panel/infusion_db.php";
require_once RADIO_STATUS_LOCALE;

add_to_title($locale['global_200'].$locale['RSP_title']);

// Actions
$action = isset($_GET['action']) ? $_GET['action'] : '';
$radio_id = isset($_GET['radio_id']) && isnum($_GET['radio_id']) ? intval($_GET['radio_id']) : 0;

// Delete stream
if ($action == 'delete' && $radio_id) {
    if (isset($_POST['confirm'])) {
        dbquery("DELETE FROM ".DB_RADIO_STATUS." WHERE radio_id='".$radio_id."'");
        addNotice('success', $locale['RSP_stream_deleted']);
        redirect(FUSION_SELF.fusion_get_aidlink());
    } else {
        opentable($locale['RSP_delete_stream']);
        echo "<div class='panel panel-danger'>";
        echo "<div class='panel-heading'>";
        echo "<h3 class='panel-title'><i class='fa fa-warning'></i> Warnung</h3>";
        echo "</div>";
        echo "<div class='panel-body text-center' style='padding:40px;'>";
        echo "<i class='fa fa-exclamation-triangle fa-5x text-danger m-b-20' style='display:block;'></i>";
        echo "<h3>Stream wirklich löschen?</h3>";
        echo "<p class='text-muted'>Diese Aktion kann nicht rückgängig gemacht werden.</p>";
        echo openform('delete_form', 'post', FUSION_SELF.fusion_get_aidlink().'&amp;action=delete&amp;radio_id='.$radio_id);
        echo "<div class='m-t-30'>";
        echo form_button('confirm', $locale['RSP_delete'], 'confirm', ['class' => 'btn-danger btn-lg m-r-10', 'icon' => 'fa fa-trash']);
        echo "<a href='".FUSION_SELF.fusion_get_aidlink()."' class='btn btn-default btn-lg'><i class='fa fa-arrow-left'></i> ".$locale['RSP_cancel']."</a>";
        echo "</div>";
        echo closeform();
        echo "</div>";
        echo "</div>";
        closetable();
        require_once THEMES."templates/admin_footer.php";
        exit;
    }
}

// Edit/Add stream
if ($action == 'edit' || $action == 'add') {
    $data = [
        'radio_id' => 0,
        'radio_name' => '',
        'radio_server' => '',
        'radio_port' => 8000,
        'radio_mount' => '/',
        'radio_type' => 'shoutcast1',
        'radio_password' => '',
        'radio_status' => 1,
        'radio_order' => 0
    ];

    if ($action == 'edit' && $radio_id) {
        $result = dbquery("SELECT * FROM ".DB_RADIO_STATUS." WHERE radio_id='".$radio_id."'");
        if (dbrows($result)) {
            $data = dbarray($result);
        } else {
            redirect(FUSION_SELF.fusion_get_aidlink());
        }
    }

    if (isset($_POST['save_stream'])) {
        $radio_name = stripinput($_POST['radio_name']);
        $radio_server = stripinput($_POST['radio_server']);
        $radio_port = isnum($_POST['radio_port']) ? intval($_POST['radio_port']) : 8000;
        $radio_mount = stripinput($_POST['radio_mount']);
        $radio_type = stripinput($_POST['radio_type']);
        $radio_password = stripinput($_POST['radio_password']);
        $radio_status = isset($_POST['radio_status']) ? 1 : 0;
        $radio_order = isnum($_POST['radio_order']) ? intval($_POST['radio_order']) : 0;

        if ($radio_name && $radio_server) {
            if ($action == 'edit' && $radio_id) {
                dbquery("UPDATE ".DB_RADIO_STATUS." SET
                    radio_name='".$radio_name."',
                    radio_server='".$radio_server."',
                    radio_port='".$radio_port."',
                    radio_mount='".$radio_mount."',
                    radio_type='".$radio_type."',
                    radio_password='".$radio_password."',
                    radio_status='".$radio_status."',
                    radio_order='".$radio_order."'
                    WHERE radio_id='".$radio_id."'");
                addNotice('success', $locale['RSP_stream_updated']);
            } else {
                dbquery("INSERT INTO ".DB_RADIO_STATUS." (radio_name, radio_server, radio_port, radio_mount, radio_type, radio_password, radio_status, radio_order)
                    VALUES ('".$radio_name."', '".$radio_server."', '".$radio_port."', '".$radio_mount."', '".$radio_type."', '".$radio_password."', '".$radio_status."', '".$radio_order."')");
                addNotice('success', $locale['RSP_stream_added']);
            }
            redirect(FUSION_SELF.fusion_get_aidlink());
        } else {
            addNotice('danger', $locale['RSP_error_name']);
        }
    }

    opentable($action == 'edit' ? $locale['RSP_edit_stream'] : $locale['RSP_add_stream']);

    echo openform('stream_form', 'post', FUSION_SELF.fusion_get_aidlink().'&amp;action='.$action.($radio_id ? '&amp;radio_id='.$radio_id : ''));

    // Basic Information - Bootstrap 3 Panel
    echo "<div class='panel panel-primary'>";
    echo "<div class='panel-heading'>";
    echo "<h3 class='panel-title'><i class='fa fa-info-circle'></i> Basis-Informationen</h3>";
    echo "</div>";
    echo "<div class='panel-body'>";

    echo "<div class='row'>";
    echo "<div class='col-xs-12 col-sm-8'>";
    echo "<div class='form-group'>";
    echo "<label>".$locale['RSP_stream_name']." <span class='required'>*</span></label>";
    echo "<input type='text' name='radio_name' value='".htmlspecialchars($data['radio_name'])."' class='form-control' placeholder='Mein Radio Stream' required />";
    echo "</div>";
    echo "</div>";

    echo "<div class='col-xs-12 col-sm-4'>";
    echo "<div class='form-group'>";
    echo "<label>".$locale['RSP_order']."</label>";
    echo "<input type='number' name='radio_order' value='".$data['radio_order']."' class='form-control' />";
    echo "</div>";
    echo "</div>";
    echo "</div>";

    echo "<div class='form-group'>";
    echo "<div class='checkbox'>";
    echo "<label>";
    echo "<input type='checkbox' name='radio_status' value='1'".($data['radio_status'] ? ' checked' : '')." />";
    echo " ".$locale['RSP_active'];
    echo "</label>";
    echo "</div>";
    echo "</div>";

    echo "</div>";
    echo "</div>";

    // Server Configuration - Bootstrap 3 Panel
    echo "<div class='panel panel-info'>";
    echo "<div class='panel-heading'>";
    echo "<h3 class='panel-title'><i class='fa fa-server'></i> Server-Konfiguration</h3>";
    echo "</div>";
    echo "<div class='panel-body'>";

    echo "<div class='row'>";
    echo "<div class='col-xs-12 col-sm-6'>";
    echo "<div class='form-group'>";
    echo "<label>".$locale['RSP_server']." <span class='required'>*</span></label>";
    echo "<input type='text' name='radio_server' value='".htmlspecialchars($data['radio_server'])."' class='form-control' placeholder='stream.example.com' required />";
    echo "</div>";
    echo "</div>";

    echo "<div class='col-xs-12 col-sm-3'>";
    echo "<div class='form-group'>";
    echo "<label>".$locale['RSP_port']." <span class='required'>*</span></label>";
    echo "<input type='number' name='radio_port' value='".$data['radio_port']."' class='form-control' required />";
    echo "</div>";
    echo "</div>";

    echo "<div class='col-xs-12 col-sm-3'>";
    echo "<div class='form-group'>";
    echo "<label>".$locale['RSP_mount']."</label>";
    echo "<input type='text' name='radio_mount' value='".htmlspecialchars($data['radio_mount'])."' class='form-control' placeholder='/' />";
    echo "</div>";
    echo "</div>";
    echo "</div>";

    echo "<div class='row'>";
    echo "<div class='col-xs-12 col-sm-6'>";
    echo "<div class='form-group'>";
    echo "<label>".$locale['RSP_type']."</label>";
    echo "<select name='radio_type' class='form-control'>";
    echo "<option value='shoutcast1'".($data['radio_type'] == 'shoutcast1' ? ' selected' : '').">Shoutcast v1</option>";
    echo "<option value='shoutcast2'".($data['radio_type'] == 'shoutcast2' ? ' selected' : '').">Shoutcast v2</option>";
    echo "<option value='icecast'".($data['radio_type'] == 'icecast' ? ' selected' : '').">Icecast</option>";
    echo "</select>";
    echo "</div>";
    echo "</div>";

    echo "<div class='col-xs-12 col-sm-6'>";
    echo "<div class='form-group'>";
    echo "<label>".$locale['RSP_password']." <small class='text-muted'>(Optional)</small></label>";
    echo "<input type='password' name='radio_password' value='".htmlspecialchars($data['radio_password'])."' class='form-control' autocomplete='off' placeholder='Admin-Passwort' />";
    echo "</div>";
    echo "</div>";
    echo "</div>";

    echo "</div>";
    echo "</div>";

    echo "<div class='m-t-20'>";
    echo form_button('save_stream', $locale['RSP_save'], $locale['RSP_save'], ['class' => 'btn-success btn-lg m-r-10', 'icon' => 'fa fa-save']);
    echo "<a href='".FUSION_SELF.fusion_get_aidlink()."' class='btn btn-default btn-lg'><i class='fa fa-arrow-left'></i> ".$locale['RSP_cancel']."</a>";
    echo "</div>";

    echo closeform();
    closetable();
    require_once THEMES."templates/admin_footer.php";
    exit;
}

// Settings
if ($action == 'settings') {
    if (isset($_POST['save_settings'])) {
        $settings = [
            'refresh_interval' => isnum($_POST['refresh_interval']) ? intval($_POST['refresh_interval']) : 10,
            'show_listeners' => isset($_POST['show_listeners']) ? 1 : 0,
            'show_current_song' => isset($_POST['show_current_song']) ? 1 : 0,
            'show_max_listeners' => isset($_POST['show_max_listeners']) ? 1 : 0,
            'show_bitrate' => isset($_POST['show_bitrate']) ? 1 : 0,
            'show_genre' => isset($_POST['show_genre']) ? 1 : 0
        ];

        foreach ($settings as $key => $value) {
            $check = dbquery("SELECT settings_name FROM ".DB_RADIO_SETTINGS." WHERE settings_name='".$key."'");
            if (dbrows($check)) {
                dbquery("UPDATE ".DB_RADIO_SETTINGS." SET settings_value='".$value."' WHERE settings_name='".$key."'");
            } else {
                dbquery("INSERT INTO ".DB_RADIO_SETTINGS." (settings_name, settings_value) VALUES ('".$key."', '".$value."')");
            }
        }

        addNotice('success', $locale['RSP_settings_updated']);
        redirect(FUSION_SELF.fusion_get_aidlink());
    }

    $settings = [];
    $result = dbquery("SELECT * FROM ".DB_RADIO_SETTINGS);
    while ($data = dbarray($result)) {
        $settings[$data['settings_name']] = $data['settings_value'];
    }

    opentable($locale['RSP_settings']);

    echo openform('settings_form', 'post', FUSION_SELF.fusion_get_aidlink().'&amp;action=settings');

    echo "<div class='panel panel-success'>";
    echo "<div class='panel-heading'>";
    echo "<h3 class='panel-title'><i class='fa fa-refresh'></i> Aktualisierung</h3>";
    echo "</div>";
    echo "<div class='panel-body'>";
    echo "<div class='form-group'>";
    echo "<label>".$locale['RSP_refresh_interval']."</label>";
    echo "<div class='input-group' style='max-width:250px;'>";
    echo "<input type='number' name='refresh_interval' value='".(isset($settings['refresh_interval']) ? $settings['refresh_interval'] : 10)."' class='form-control' min='5' max='300' />";
    echo "<span class='input-group-addon'>Sekunden</span>";
    echo "</div>";
    echo "<span class='help-block'>Wie oft sollen die Stream-Daten aktualisiert werden? (5-300 Sekunden)</span>";
    echo "</div>";
    echo "</div>";
    echo "</div>";

    echo "<div class='panel panel-info'>";
    echo "<div class='panel-heading'>";
    echo "<h3 class='panel-title'><i class='fa fa-eye'></i> Anzeigeoptionen</h3>";
    echo "</div>";
    echo "<div class='panel-body'>";

    $options = [
        ['name' => 'show_listeners', 'icon' => 'fa-users', 'label' => $locale['RSP_show_listeners']],
        ['name' => 'show_current_song', 'icon' => 'fa-music', 'label' => $locale['RSP_show_current_song']],
        ['name' => 'show_max_listeners', 'icon' => 'fa-user-plus', 'label' => $locale['RSP_show_max_listeners']],
        ['name' => 'show_bitrate', 'icon' => 'fa-signal', 'label' => $locale['RSP_show_bitrate']],
        ['name' => 'show_genre', 'icon' => 'fa-tag', 'label' => $locale['RSP_show_genre']]
    ];

    foreach ($options as $opt) {
        echo "<div class='checkbox'>";
        echo "<label>";
        echo "<input type='checkbox' name='".$opt['name']."' value='1'".(isset($settings[$opt['name']]) && $settings[$opt['name']] ? ' checked' : '')." />";
        echo " <i class='fa ".$opt['icon']."'></i> ".$opt['label'];
        echo "</label>";
        echo "</div>";
    }

    echo "</div>";
    echo "</div>";

    echo "<div class='m-t-20'>";
    echo form_button('save_settings', $locale['RSP_save'], $locale['RSP_save'], ['class' => 'btn-success btn-lg m-r-10', 'icon' => 'fa fa-save']);
    echo "<a href='".FUSION_SELF.fusion_get_aidlink()."' class='btn btn-default btn-lg'><i class='fa fa-arrow-left'></i> ".$locale['RSP_cancel']."</a>";
    echo "</div>";

    echo closeform();
    closetable();
    require_once THEMES."templates/admin_footer.php";
    exit;
}

// Main Dashboard
opentable($locale['RSP_admin_title']);

// Action Buttons - Bootstrap 3
echo "<div class='m-b-20'>";
echo "<a class='btn btn-success btn-lg m-r-10' href='".FUSION_SELF.fusion_get_aidlink()."&amp;action=add'>";
echo "<i class='fa fa-plus-circle'></i> ".$locale['RSP_add_stream'];
echo "</a>";
echo "<a class='btn btn-primary btn-lg' href='".FUSION_SELF.fusion_get_aidlink()."&amp;action=settings'>";
echo "<i class='fa fa-cog'></i> ".$locale['RSP_settings'];
echo "</a>";
echo "</div>";

// Statistics
$total_streams = dbcount("(radio_id)", DB_RADIO_STATUS);
$active_streams = dbcount("(radio_id)", DB_RADIO_STATUS, "radio_status='1'");
$inactive_streams = $total_streams - $active_streams;

$settings = [];
$result_settings = dbquery("SELECT * FROM ".DB_RADIO_SETTINGS);
while ($data = dbarray($result_settings)) {
    $settings[$data['settings_name']] = $data['settings_value'];
}
$refresh = isset($settings['refresh_interval']) ? $settings['refresh_interval'] : 10;

// Bootstrap 3 Info Boxes
echo "<div class='row m-b-20'>";

echo "<div class='col-xs-12 col-sm-6 col-md-3'>";
echo "<div class='panel panel-info'>";
echo "<div class='panel-body text-center' style='padding:30px 15px;'>";
echo "<i class='fa fa-radio fa-4x' style='color:#31b0d5;'></i>";
echo "<h2 class='m-t-10 m-b-5' style='font-weight:bold;'>".$total_streams."</h2>";
echo "<p class='text-uppercase' style='font-size:11px;letter-spacing:1px;'><strong>Gesamt Streams</strong></p>";
echo "</div></div></div>";

echo "<div class='col-xs-12 col-sm-6 col-md-3'>";
echo "<div class='panel panel-success'>";
echo "<div class='panel-body text-center' style='padding:30px 15px;'>";
echo "<i class='fa fa-check-circle fa-4x' style='color:#449d44;'></i>";
echo "<h2 class='m-t-10 m-b-5' style='font-weight:bold;'>".$active_streams."</h2>";
echo "<p class='text-uppercase' style='font-size:11px;letter-spacing:1px;'><strong>Aktive Streams</strong></p>";
echo "</div></div></div>";

echo "<div class='col-xs-12 col-sm-6 col-md-3'>";
echo "<div class='panel panel-warning'>";
echo "<div class='panel-body text-center' style='padding:30px 15px;'>";
echo "<i class='fa fa-pause-circle fa-4x' style='color:#ec971f;'></i>";
echo "<h2 class='m-t-10 m-b-5' style='font-weight:bold;'>".$inactive_streams."</h2>";
echo "<p class='text-uppercase' style='font-size:11px;letter-spacing:1px;'><strong>Inaktive Streams</strong></p>";
echo "</div></div></div>";

echo "<div class='col-xs-12 col-sm-6 col-md-3'>";
echo "<div class='panel panel-primary'>";
echo "<div class='panel-body text-center' style='padding:30px 15px;'>";
echo "<i class='fa fa-refresh fa-4x' style='color:#337ab7;'></i>";
echo "<h2 class='m-t-10 m-b-5' style='font-weight:bold;'>".$refresh."s</h2>";
echo "<p class='text-uppercase' style='font-size:11px;letter-spacing:1px;'><strong>Refresh Intervall</strong></p>";
echo "</div></div></div>";

echo "</div>";

// List streams
$result = dbquery("SELECT * FROM ".DB_RADIO_STATUS." ORDER BY radio_order ASC, radio_name ASC");

if (dbrows($result)) {
    echo "<div class='panel panel-default'>";
    echo "<div class='panel-heading'>";
    echo "<h3 class='panel-title'><i class='fa fa-list'></i> Radio Streams</h3>";
    echo "</div>";
    echo "<div class='table-responsive'>";
    echo "<table class='table table-striped table-bordered table-hover m-b-0'>";
    echo "<thead>";
    echo "<tr class='active'>";
    echo "<th style='width:50px;'>#</th>";
    echo "<th>".$locale['RSP_stream_name']."</th>";
    echo "<th>".$locale['RSP_server']."</th>";
    echo "<th style='width:120px;'>".$locale['RSP_type']."</th>";
    echo "<th class='text-center' style='width:100px;'>".$locale['RSP_status']."</th>";
    echo "<th class='text-center' style='width:140px;'>".$locale['RSP_actions']."</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";

    while ($data = dbarray($result)) {
        $type_label = $data['radio_type'];
        if ($data['radio_type'] == 'shoutcast1') $type_label = 'Shoutcast v1';
        if ($data['radio_type'] == 'shoutcast2') $type_label = 'Shoutcast v2';
        if ($data['radio_type'] == 'icecast') $type_label = 'Icecast';

        echo "<tr>";
        echo "<td><strong>#".$data['radio_id']."</strong></td>";
        echo "<td>";
        echo "<strong>".htmlspecialchars($data['radio_name'])."</strong><br>";
        echo "<small class='text-muted'><i class='fa fa-folder-open'></i> Mount: ".htmlspecialchars($data['radio_mount'])." | <i class='fa fa-sort'></i> Reihenfolge: ".$data['radio_order']."</small>";
        echo "</td>";
        echo "<td><code>".htmlspecialchars($data['radio_server']).":".htmlspecialchars($data['radio_port'])."</code></td>";
        echo "<td><span class='label label-info'>".$type_label."</span></td>";
        echo "<td class='text-center'>";
        if ($data['radio_status']) {
            echo "<span class='label label-success'><i class='fa fa-check'></i> Aktiv</span>";
        } else {
            echo "<span class='label label-default'><i class='fa fa-pause'></i> Inaktiv</span>";
        }
        echo "</td>";
        echo "<td class='text-center'>";
        echo "<div class='btn-group btn-group-sm'>";
        echo "<a class='btn btn-primary' href='".FUSION_SELF.fusion_get_aidlink()."&amp;action=edit&amp;radio_id=".$data['radio_id']."' title='Bearbeiten'>";
        echo "<i class='fa fa-edit'></i>";
        echo "</a>";
        echo "<a class='btn btn-danger' href='".FUSION_SELF.fusion_get_aidlink()."&amp;action=delete&amp;radio_id=".$data['radio_id']."' title='Löschen'>";
        echo "<i class='fa fa-trash-o'></i>";
        echo "</a>";
        echo "</div>";
        echo "</td>";
        echo "</tr>";
    }

    echo "</tbody>";
    echo "</table>";
    echo "</div>";
    echo "</div>";
} else {
    echo "<div class='alert alert-info text-center' style='padding:40px;'>";
    echo "<i class='fa fa-info-circle fa-4x m-b-20' style='display:block;'></i>";
    echo "<h4 class='m-t-0'>".$locale['RSP_no_streams']."</h4>";
    echo "<p>Klicken Sie auf <strong>'Stream hinzufügen'</strong> um Ihren ersten Radio-Stream zu konfigurieren.</p>";
    echo "</div>";
}

closetable();

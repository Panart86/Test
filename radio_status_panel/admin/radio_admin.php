<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: radio_admin.php
| Author: Radio Status Panel Infusion
+--------------------------------------------------------*/
require_once "../../maincore.php";

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
        echo "<div class='well text-center'>";
        echo "<p>".$locale['RSP_delete_stream']."?</p>";
        echo openform('delete_form', 'post', FUSION_SELF.fusion_get_aidlink().'&amp;action=delete&amp;radio_id='.$radio_id);
        echo form_button('confirm', $locale['RSP_delete'], 'confirm', ['class' => 'btn-danger m-r-10']);
        echo "<a href='".FUSION_SELF.fusion_get_aidlink()."' class='btn btn-default'>".$locale['RSP_cancel']."</a>";
        echo closeform();
        echo "</div>";
        closetable();
    }
}

// Edit/Add stream
else if ($action == 'edit' || $action == 'add') {
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
                // Update
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
                // Insert
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

    echo "<div class='form-group'>";
    echo "<label class='control-label col-xs-12 col-sm-3'>".$locale['RSP_stream_name']." <span class='required'>*</span></label>";
    echo "<div class='col-xs-12 col-sm-9'>";
    echo "<input type='text' name='radio_name' value='".htmlspecialchars($data['radio_name'])."' class='form-control' required='required' />";
    echo "</div>";
    echo "</div>";

    echo "<div class='form-group'>";
    echo "<label class='control-label col-xs-12 col-sm-3'>".$locale['RSP_server']." <span class='required'>*</span></label>";
    echo "<div class='col-xs-12 col-sm-9'>";
    echo "<input type='text' name='radio_server' value='".htmlspecialchars($data['radio_server'])."' class='form-control' placeholder='example.com' required='required' />";
    echo "</div>";
    echo "</div>";

    echo "<div class='form-group'>";
    echo "<label class='control-label col-xs-12 col-sm-3'>".$locale['RSP_port']." <span class='required'>*</span></label>";
    echo "<div class='col-xs-12 col-sm-9'>";
    echo "<input type='number' name='radio_port' value='".$data['radio_port']."' class='form-control' style='width:150px;' required='required' />";
    echo "</div>";
    echo "</div>";

    echo "<div class='form-group'>";
    echo "<label class='control-label col-xs-12 col-sm-3'>".$locale['RSP_mount']."</label>";
    echo "<div class='col-xs-12 col-sm-9'>";
    echo "<input type='text' name='radio_mount' value='".htmlspecialchars($data['radio_mount'])."' class='form-control' style='width:200px;' placeholder='/' />";
    echo "</div>";
    echo "</div>";

    echo "<div class='form-group'>";
    echo "<label class='control-label col-xs-12 col-sm-3'>".$locale['RSP_type']."</label>";
    echo "<div class='col-xs-12 col-sm-9'>";
    echo "<select name='radio_type' class='form-control' style='width:200px;'>";
    echo "<option value='shoutcast1'".($data['radio_type'] == 'shoutcast1' ? ' selected' : '').">Shoutcast v1</option>";
    echo "<option value='shoutcast2'".($data['radio_type'] == 'shoutcast2' ? ' selected' : '').">Shoutcast v2</option>";
    echo "<option value='icecast'".($data['radio_type'] == 'icecast' ? ' selected' : '').">Icecast</option>";
    echo "</select>";
    echo "</div>";
    echo "</div>";

    echo "<div class='form-group'>";
    echo "<label class='control-label col-xs-12 col-sm-3'>".$locale['RSP_password']."</label>";
    echo "<div class='col-xs-12 col-sm-9'>";
    echo "<input type='password' name='radio_password' value='".htmlspecialchars($data['radio_password'])."' class='form-control' autocomplete='off' />";
    echo "</div>";
    echo "</div>";

    echo "<div class='form-group'>";
    echo "<label class='control-label col-xs-12 col-sm-3'>".$locale['RSP_order']."</label>";
    echo "<div class='col-xs-12 col-sm-9'>";
    echo "<input type='number' name='radio_order' value='".$data['radio_order']."' class='form-control' style='width:100px;' />";
    echo "</div>";
    echo "</div>";

    echo "<div class='form-group'>";
    echo "<label class='control-label col-xs-12 col-sm-3'>".$locale['RSP_status']."</label>";
    echo "<div class='col-xs-12 col-sm-9'>";
    echo "<label class='checkbox-inline'><input type='checkbox' name='radio_status' value='1'".($data['radio_status'] ? ' checked' : '')." /> ".$locale['RSP_active']."</label>";
    echo "</div>";
    echo "</div>";

    echo "<div class='form-group'>";
    echo "<div class='col-xs-12 col-sm-offset-3 col-sm-9'>";
    echo form_button('save_stream', $locale['RSP_save'], $locale['RSP_save'], ['class' => 'btn-primary m-r-10']);
    echo "<a href='".FUSION_SELF.fusion_get_aidlink()."' class='btn btn-default'>".$locale['RSP_cancel']."</a>";
    echo "</div>";
    echo "</div>";

    echo closeform();
    closetable();
}

// Settings
else if ($action == 'settings') {
    if (isset($_POST['save_settings'])) {
        $refresh_interval = isnum($_POST['refresh_interval']) ? intval($_POST['refresh_interval']) : 10;
        $show_listeners = isset($_POST['show_listeners']) ? 1 : 0;
        $show_current_song = isset($_POST['show_current_song']) ? 1 : 0;
        $show_max_listeners = isset($_POST['show_max_listeners']) ? 1 : 0;
        $show_bitrate = isset($_POST['show_bitrate']) ? 1 : 0;
        $show_genre = isset($_POST['show_genre']) ? 1 : 0;

        $settings = [
            'refresh_interval' => $refresh_interval,
            'show_listeners' => $show_listeners,
            'show_current_song' => $show_current_song,
            'show_max_listeners' => $show_max_listeners,
            'show_bitrate' => $show_bitrate,
            'show_genre' => $show_genre
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

    // Load current settings
    $settings = [];
    $result = dbquery("SELECT * FROM ".DB_RADIO_SETTINGS);
    while ($data = dbarray($result)) {
        $settings[$data['settings_name']] = $data['settings_value'];
    }

    opentable($locale['RSP_settings']);

    echo openform('settings_form', 'post', FUSION_SELF.fusion_get_aidlink().'&amp;action=settings');

    echo "<div class='form-group'>";
    echo "<label class='control-label col-xs-12 col-sm-3'>".$locale['RSP_refresh_interval']."</label>";
    echo "<div class='col-xs-12 col-sm-9'>";
    echo "<input type='number' name='refresh_interval' value='".(isset($settings['refresh_interval']) ? $settings['refresh_interval'] : 10)."' class='form-control' style='width:150px;' />";
    echo "</div>";
    echo "</div>";

    echo "<div class='form-group'>";
    echo "<label class='control-label col-xs-12 col-sm-3'>".$locale['RSP_show_listeners']."</label>";
    echo "<div class='col-xs-12 col-sm-9'>";
    echo "<label class='checkbox-inline'><input type='checkbox' name='show_listeners' value='1'".(isset($settings['show_listeners']) && $settings['show_listeners'] ? ' checked' : '')." /> Aktiviert</label>";
    echo "</div>";
    echo "</div>";

    echo "<div class='form-group'>";
    echo "<label class='control-label col-xs-12 col-sm-3'>".$locale['RSP_show_current_song']."</label>";
    echo "<div class='col-xs-12 col-sm-9'>";
    echo "<label class='checkbox-inline'><input type='checkbox' name='show_current_song' value='1'".(isset($settings['show_current_song']) && $settings['show_current_song'] ? ' checked' : '')." /> Aktiviert</label>";
    echo "</div>";
    echo "</div>";

    echo "<div class='form-group'>";
    echo "<label class='control-label col-xs-12 col-sm-3'>".$locale['RSP_show_max_listeners']."</label>";
    echo "<div class='col-xs-12 col-sm-9'>";
    echo "<label class='checkbox-inline'><input type='checkbox' name='show_max_listeners' value='1'".(isset($settings['show_max_listeners']) && $settings['show_max_listeners'] ? ' checked' : '')." /> Aktiviert</label>";
    echo "</div>";
    echo "</div>";

    echo "<div class='form-group'>";
    echo "<label class='control-label col-xs-12 col-sm-3'>".$locale['RSP_show_bitrate']."</label>";
    echo "<div class='col-xs-12 col-sm-9'>";
    echo "<label class='checkbox-inline'><input type='checkbox' name='show_bitrate' value='1'".(isset($settings['show_bitrate']) && $settings['show_bitrate'] ? ' checked' : '')." /> Aktiviert</label>";
    echo "</div>";
    echo "</div>";

    echo "<div class='form-group'>";
    echo "<label class='control-label col-xs-12 col-sm-3'>".$locale['RSP_show_genre']."</label>";
    echo "<div class='col-xs-12 col-sm-9'>";
    echo "<label class='checkbox-inline'><input type='checkbox' name='show_genre' value='1'".(isset($settings['show_genre']) && $settings['show_genre'] ? ' checked' : '')." /> Aktiviert</label>";
    echo "</div>";
    echo "</div>";

    echo "<div class='form-group'>";
    echo "<div class='col-xs-12 col-sm-offset-3 col-sm-9'>";
    echo form_button('save_settings', $locale['RSP_save'], $locale['RSP_save'], ['class' => 'btn-primary m-r-10']);
    echo "<a href='".FUSION_SELF.fusion_get_aidlink()."' class='btn btn-default'>".$locale['RSP_cancel']."</a>";
    echo "</div>";
    echo "</div>";

    echo closeform();
    closetable();
}

// List streams (default view)
else {
    opentable($locale['RSP_admin_title']);

    // Add buttons
    echo "<div class='m-b-20'>";
    echo "<a class='btn btn-success' href='".FUSION_SELF.fusion_get_aidlink()."&amp;action=add'>";
    echo "<i class='fa fa-plus'></i> ".$locale['RSP_add_stream'];
    echo "</a> ";
    echo "<a class='btn btn-default' href='".FUSION_SELF.fusion_get_aidlink()."&amp;action=settings'>";
    echo "<i class='fa fa-cog'></i> ".$locale['RSP_settings'];
    echo "</a>";
    echo "</div>";

    // List streams
    $result = dbquery("SELECT * FROM ".DB_RADIO_STATUS." ORDER BY radio_order ASC, radio_name ASC");

    if (dbrows($result)) {
        echo "<table class='table table-responsive table-striped'>";
        echo "<thead>";
        echo "<tr>";
        echo "<th>".$locale['RSP_stream_name']."</th>";
        echo "<th>".$locale['RSP_server']."</th>";
        echo "<th>".$locale['RSP_type']."</th>";
        echo "<th>".$locale['RSP_status']."</th>";
        echo "<th class='text-right'>".$locale['RSP_order']."</th>";
        echo "<th class='text-center'>Actions</th>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";

        while ($data = dbarray($result)) {
            $status_label = $data['radio_status'] ? "<span class='label label-success'>".$locale['RSP_active']."</span>" :
                                                    "<span class='label label-default'>".$locale['RSP_inactive']."</span>";

            $type_label = $data['radio_type'];
            if ($data['radio_type'] == 'shoutcast1') $type_label = 'Shoutcast v1';
            if ($data['radio_type'] == 'shoutcast2') $type_label = 'Shoutcast v2';
            if ($data['radio_type'] == 'icecast') $type_label = 'Icecast';

            echo "<tr>";
            echo "<td><strong>".htmlspecialchars($data['radio_name'])."</strong></td>";
            echo "<td>".htmlspecialchars($data['radio_server']).":".htmlspecialchars($data['radio_port'])."</td>";
            echo "<td>".$type_label."</td>";
            echo "<td>".$status_label."</td>";
            echo "<td class='text-right'>".$data['radio_order']."</td>";
            echo "<td class='text-center'>";
            echo "<a class='btn btn-sm btn-default' href='".FUSION_SELF.fusion_get_aidlink()."&amp;action=edit&amp;radio_id=".$data['radio_id']."'>";
            echo "<i class='fa fa-pencil'></i> ".$locale['RSP_edit'];
            echo "</a> ";
            echo "<a class='btn btn-sm btn-danger' href='".FUSION_SELF.fusion_get_aidlink()."&amp;action=delete&amp;radio_id=".$data['radio_id']."'>";
            echo "<i class='fa fa-trash'></i> ".$locale['RSP_delete'];
            echo "</a>";
            echo "</td>";
            echo "</tr>";
        }

        echo "</tbody>";
        echo "</table>";
    } else {
        echo "<div class='well text-center'>";
        echo $locale['RSP_no_streams'];
        echo "</div>";
    }

    closetable();
}

require_once THEMES."templates/admin_footer.php";
